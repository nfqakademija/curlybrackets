<?php
namespace App\Service;

use App\Entity\Product;
use Carbon\Carbon;
use Liip\ImagineBundle\Templating\Helper\FilterHelper;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ProductJsonService
{
    public function __construct(UploaderHelper $uploadHelper, FilterHelper $filterHelper, RouterInterface $router)
    {
        $this->uploadHelper = $uploadHelper;
        $this->filterHelper = $filterHelper;
        $this->router = $router;
    }

    public function createJson($products): Response
    {
        $data = [];

        /** @var Product $product */
        foreach ($products as $product) {
            if ($product->getPicture()) {
                //todo change to small filter (100x100)
                $image = $this->filterHelper->filter($this->uploadHelper->asset($product, 'pictureFile'), 'mini');
            } else {
                $image = null;
            }

            Carbon::setLocale('lt');
            $timeLeft =  Carbon::parse($product->getDeadline())->diffForHumans();

            $data[] = [
                'product_id' => $product->getId(),
                'title' => $product->getTitle(),
                'description' => $product->getDescription(),
                'image' => $image,
                'deadline' => $timeLeft,
                'latitude' => $product->getLocation()->getLatitude(),
                'longitude' => $product->getLocation()->getLongitude(),
                'owner_id' => $product->getUser()->getId(),
                'contact_url' => $this->router->generate('contact', ['id' => $product->getId()])
            ];
        }
        $dataJson = json_encode($data);

        $response = new Response($dataJson);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
