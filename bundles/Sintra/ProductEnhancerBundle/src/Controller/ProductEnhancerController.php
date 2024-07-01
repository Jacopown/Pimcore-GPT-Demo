<?php

namespace Sintra\ProductEnhancerBundle\Controller;

use OpenAI;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Asset\Image;
use Pimcore\Config;

class ProductEnhancerController extends FrontendController
{
    /**
     * @Route("/enhance-product", name="enhance_product", methods={"POST"})
     */
  public function enhanceProduct(Request $request): Response
  {
    $client = OpenAI::client(Config::getWebsiteConfig()['openaiapikey']);
    $enhancerLanguages = Config::getWebsiteConfig()['enhancerlanguages'];
    $context = Config::getWebsiteConfig()['context'];
    $toneOfVoice = Config::getWebsiteConfig()['toneofvoice'];

    $requestData = json_decode($request->getContent(), true);
    $selectedProductIds = $requestData['productIds'];

    $products = [];
    foreach ($selectedProductIds as $id) {
      $product = Product::getById($id);

      if ($product instanceof Product) {
        $image = $product->getImage(); // Assuming 'images' is the field name holding images
        $imagePath = $image->getLocalFile();
        $imageContent = file_get_contents($imagePath);
        $base64Image = base64_encode($imageContent);
        try{
          $data = $client->chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
              [
                'role' => 'user',
                'content'=> [
                  [
                    'type' => 'text',
                    'text'=> "Write a plain text paragraph describing the product in the image. This paragraph will be the description for the product in my web store. This is the context of the store where i'm selling the product: $context. This is the tone of voice you should use: $toneOfVoice. Translate it also in the following languages: $enhancerLanguages"
                  ],
                  [
                    'type'=>'image_url',
                    'image_url' => [
                      'url' => "data:image/jpeg;base64,$base64Image"
                    ]
                  ]
                ]
              ]
            ],
          ]);
          $description = $data['choices'][0]['message']['content'];
          $product->setDescription($description);
          $response = 'Description correctly generated';
          try {
            $product->save();
          } catch (Exception $e) {
            $response = $e->getMessage();
            continue;
          }
        } catch (Exception $e) {
          $response = 'Error: ' . $e->getMessage();
          continue;
        }
        $products[] = [
            'id' => $id,
            'response' => $response,
        ];
      }
    }
    return new Response(json_encode(['status' => 'success', 'message' => 'Enhanced', 'products' => $products]));
  }
}
