<?php
namespace App\Service;

use Carbon\Carbon;
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
        Carbon::setLocale('lt');
        foreach ($products as $product) {
            $product->timeLeft =  Carbon::parse($product->getDeadline())->diffForHumans();
        }

        $data = [];

        foreach ($products as $product) {
            if ($product->getPicture()) {
                $image = $this->filterHelper->filter($this->uploadHelper->asset($product, 'pictureFile'), 'square');
            } else {
                $image = null;
            }

            $data[] = [
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
