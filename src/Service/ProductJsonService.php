<?php
namespace App\Service;

use App\Entity\Product;
use Carbon\Carbon;
use Liip\ImagineBundle\Templating\Helper\FilterHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class ProductJsonService
 *
 * @package App\Service
 */
class ProductJsonService
{
    /**
     * @var UploaderHelper
     */
    private $uploadHelper;

    /**
     * @var FilterHelper
     */
    private $filterHelper;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * ProductJsonService constructor.
     *
     * @param UploaderHelper $uploadHelper
     * @param FilterHelper $filterHelper
     * @param RouterInterface $router
     */
    public function __construct(UploaderHelper $uploadHelper, FilterHelper $filterHelper, RouterInterface $router, KernelInterface $kernel)
    {
        $this->uploadHelper = $uploadHelper;
        $this->filterHelper = $filterHelper;
        $this->router = $router;
        $this->kernel = $kernel;
    }

    /**
     * @param $products
     * @return Response
     */
    public function createJson($products): Response
    {
        $data = [];

        /** @var Product $product */
        foreach ($products as $product) {
            if ($product->getPicture()) {
                $image = $this->filterHelper->filter($this->uploadHelper->asset($product, 'pictureFile'), 'mini');
            } else {
                $image = null;
            }

            if ($product->getUser()->getAvatar()) {
                $avatar = $this->kernel->getProjectDir().'/../images/avatars/'. $product->getUser()->getAvatar() ;
            } else {
                $avatar = null;
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
                'contact_url' => $this->router->generate('contact', ['id' => $product->getId()]),
                'username' => $product->getUser()->getUsername(),
                'avatar' => $avatar
            ];
        }
        $dataJson = json_encode($data);

        $response = new Response($dataJson);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
