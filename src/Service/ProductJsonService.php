<?php
namespace App\Service;

use Liip\ImagineBundle\Templating\Helper\FilterHelper;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ProductJsonService
{
    public function __construct(UploaderHelper $uploadHelper, FilterHelper $filterHelper)
    {
        $this->uploadHelper = $uploadHelper;
        $this->filterHelper = $filterHelper;
    }

    public function createJson($products): Response
    {
        $data = [];

        foreach ($products as $product) {
            if ($product->getPicture()) {
                $image = $this->filterHelper->filter($this->uploadHelper->asset($product, 'pictureFile'), 'square');
            } else {
                $image = null;
            }

            $data[] = [
                'product_id' => $product->getId(),
                'title' => $product->getTitle(),
                'description' => $product->getDescription(),
                'image' => $image,
                'deadline' => $product->timeLeft,
                'latitude' => $product->getLocation()->getLatitude(),
                'longitude' => $product->getLocation()->getLongitude(),
                'owner_id' => $product->getUser()->getId()
            ];
        }
        $dataJson = json_encode($data);

        $response = new Response($dataJson);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
