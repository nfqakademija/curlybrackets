<?php

namespace App\Controller;

use App\DTO\Coordinate;
use App\Entity\Product;
use App\Form\ContactType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\CheckOwnershipService;
use App\Service\MailingService;
use App\Service\ProductJsonService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{

    /**
     * @var ProductJsonService
     */
    private $productJsonService;

    /**
     * @var CheckOwnershipService
     */
    private $checkOwnershipService;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var MailingService
     */
    private $mailingService;

    /**
     * ProductController constructor.
     *
     * @param ProductJsonService $productJsonService
     * @param CheckOwnershipService $checkOwnershipService
     * @param ProductRepository $productRepository
     * @param MailingService $mailingService
     */
    public function __construct(
        ProductJsonService $productJsonService,
        CheckOwnershipService $checkOwnershipService,
        ProductRepository $productRepository,
        MailingService $mailingService
    ) {
        $this->productJsonService = $productJsonService;
        $this->checkOwnershipService = $checkOwnershipService;
        $this->productRepository = $productRepository;
        $this->mailingService = $mailingService;
    }

    /**
     * @Route("/index", name="product_index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        $products = $this->productRepository->findByActiveProducts();

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/jsonIndex", name="product_json", methods={"GET"})
     * @return Response
     */
    public function jsonIndex(): Response
    {
        $products = $this->productRepository->findByActiveProducts();

        return $this->productJsonService->createJson($products);
    }

    /**
     * @Route("/jsonMap", name="product_map", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function activeMap(Request $request): Response
    {
        $leftTopCorner = new Coordinate($request->get('latitudeSE'), $request->get('longitudeSE'));
        $bottomRightCorner = new Coordinate($request->get('latitudeNW'), $request->get('longitudeNW'));

        $products = $this->productRepository->findProductsByLocation($leftTopCorner, $bottomRightCorner);

        return $this->productJsonService->createJson($products);
    }

    /**
     * @Route("/{id}/give", name="product_give", methods={"GET"})
     * @param Product $product
     * @return Response
     */
    public function giveAway(Product $product): Response
    {
        $product->setGivenAway(!$product->getGivenAway());

        $this->productRepository->save($product);
        $this->addFlash('success', 'Produktas atidavimo būsena pakeista!');

        return $this->redirectToRoute('user_show', [
            'id' => $product->getUser()->getId(),
        ]);
    }

    /**
     * @Route("/{id}/visibility", name="product_visibility", methods={"GET"})
     * @param Product $product
     * @return Response
     */
    public function changeVisibility(Product $product): Response
    {
        $product->setStatus(!$product->getstatus());

        $this->productRepository->save($product);
        $this->addFlash('success', 'Produktas matomumas pakeistas!');

        return $this->redirectToRoute('user_show', [
            'id' => $product->getUser()->getId(),
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     * @param Request $request
     * @param UserInterface|null $user
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, UserInterface $user = null): Response
    {
        if (!$user->getLocation()) {
            return $this->redirectToRoute('location_redirect');
        }

        $product = new Product();

        $form = $this->makeForm($product, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUser($user);
            $product->setLocation($user->getLocation());

            $this->productRepository->save($product);
            $this->addFlash('success', 'Produktas sėkmingai pridėtas!');

            return $this->redirectToRoute('user_show', [
                'id' => $product->getUser()->getId(),
            ]);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Product $product
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, Product $product): Response
    {
        if (!$this->checkOwnershipService->isProductOwner($product)) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->makeForm($product, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->productRepository->save($product);
            $this->addFlash('success', 'Produktas sėkmingai pakeistas!');

            return $this->redirectToRoute('user_show', [
                'id' => $product->getUser()->getId(),
            ]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/contact/{id}", name="contact", methods={"GET","POST"})
     * @param Request $request
     * @param Product $product
     * @return RedirectResponse|Response
     */
    public function contact(Request $request, Product $product)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        $products = $this->productRepository->findByActiveProducts();
        if (!in_array($product, $products)) {
            $this->addFlash('danger', 'Produktas nepasiekiamas!');
            return $this->redirectToRoute('product_index');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data['productTitle'] = $product->getTitle();

            $message = 'Foodsharing puslapio lankytojas ' . $form['name']->getData().' nori susisiekti su Jumis';
            $recipient = $product->getUser()->getEmail();
            $twig = 'emails/contact.html.twig';

            $this->mailingService->sendMail($data, $recipient, $message, $twig);

            $this->addFlash('success', 'Jūsų žinutė išsiųsta!');

            return $this->redirectToRoute('product_index');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->checkOwnershipService->isProductOwner($product)) {
            if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
                $this->productRepository->remove($product);
                $this->addFlash('danger', 'Produktas ištrintas!');
            }
            return $this->redirectToRoute('user_show', [
                'id' => $product->getUser()->getId(),
            ]);
        }
        throw $this->createAccessDeniedException();
    }

    /**
     * @param $product
     * @param $request
     * @return FormInterface
     */
    private function makeForm($product, $request): FormInterface
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        return $form;
    }
}
