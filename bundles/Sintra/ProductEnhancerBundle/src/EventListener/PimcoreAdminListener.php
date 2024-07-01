<?php

namespace Sintra\ProductEnhancerBundle\EventListener;

use Pimcore\Event\BundleManager\PathsEvent;
use Pimcore\Event\BundleManagerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PimcoreAdminListener
{
    public function addJSFiles(PathsEvent $event): void
    {
        $event->setPaths(
            array_merge(
                $event->getPaths(),
                [
                    '/bundles/sintraproductenhancer/js/pimcore/startup.js'
                ]
            )
        );
    }
}
