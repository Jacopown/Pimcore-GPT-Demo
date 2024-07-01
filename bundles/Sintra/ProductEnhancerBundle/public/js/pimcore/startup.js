pimcore.registerNS("pimcore.plugin.SintraProductEnhancerBundle");

pimcore.plugin.SintraProductEnhancerBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.postOpenObject, (e) => {
            if (e.detail.object.type === 'folder' && e.detail.object.data.general.fullpath.match(/^\/(Product|Products|Prodotti)$/))  {
                e.detail.object.toolbar.add({
                    text: t('Enhance'),
                    iconCls: 'pimcore_icon_robots',
                    scale: 'small',
                    handler: function (obj) {
                        let grid = obj.search.grid;
                        if (grid.selection){
                            var selModel = grid.getSelectionModel();
                            var selectedRows = selModel.getSelection();
        
                            if (selectedRows.length > 0 && selectedRows.length <= 100) {
                                var selectedProductIds = selectedRows.map(row => row.data.id);
                                console.log(selectedProductIds);
                                Ext.Ajax.request({
                                    url: '/enhance-product',
                                    method: 'POST',
                                    jsonData: {
                                        productIds: selectedProductIds
                                    },
                                    success: function (response) {
                                        var res = Ext.decode(response.responseText);
                                        if (res.status === 'success') {
                                            var message = '<ul>';
                                            res.products.forEach(function(product) {
                                                message += '<li>Product ID: ' + product.id + ', response: '+ product.response+'</li>';
                                            });
                                            message += '</ul>';
                                
                                            Ext.Msg.alert(t("Success"), res.message + '<br><br>' + message);
                                        } else {
                                            Ext.Msg.alert(t("error"), res.message);
                                        }
                                    },
                                    failure: function () {
                                        Ext.Msg.alert(t("error"), t("An error occurred while enhancing products"));
                                    }
                                });
                            }
                            if (selectedRows.length > 100) {
                                Ext.Msg.alert(t("warning"), t("Select a maximum of 100 products"));
                                return;
                            }
                        } else {
                            Ext.Msg.alert(t("warning"), t("Select at least one Product"));
                            return;
                        }
                    }.bind(this, e.detail.object)
                });
            pimcore.layout.refresh();
            }
        });
    }
});

var SintraProductEnhancerBundlePlugin = new pimcore.plugin.SintraProductEnhancerBundle();

