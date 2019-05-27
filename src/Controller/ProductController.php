<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Controller;

use App\DTO\Coordinate;
use App\Entity\Product;
use App\Form\ContactType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\CheckOwnershipService;
use App\Service\MailingService;
use App\Service\ProductJsonService;
use App\Service\DoctrineActionsService;
use Exception;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Carbon\Carbon;

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
     * ProductController constructor.
     *
     * @param ProductJsonService $productJsonService
     * @param CheckOwnershipService $checkOwnershipService
     */
    public function __construct(
        ProductJsonService $productJsonService,
        CheckOwnershipService $checkOwnershipService,
        ProductRepository $productRepository,
        DoctrineActionsService $doctrineActions,
        MailingService $mailingService
    ) {
        $this->productJsonService = $productJsonService;
        $this->checkOwnerShip = $checkOwnershipService;
        $this->productRepository = $productRepository;
        $this->doctrineActions = $doctrineActions;
        $this->mailingService = $mailingService;
    }

    /**
     * @Route("/index", name="product_index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        $products = $this->productRepository->findByActiveProducts();
        $this->timeLeftForEach($products);

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
        $this->timeLeftForEach($products);

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
        $this->timeLeftForEach($products);

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

        $this->doctrineActions->save($product);
        $this->addFlash('success', 'Produktas atidavimo būsena pakeista!');

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

            $this->doctrineActions->save($product);
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
        if ($this->checkOwnerShip->isProductOwner($product)) {
            $form = $this->makeForm($product, $request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->doctrineActions->save($product);
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
        //todo 404 instead
        throw $this->createNotFoundException('You are not allowed to reach this site.');
    }

    /**
     * @Route("/contact/{id}", name="contact", methods={"GET","POST"})
     * @param Request $request
     * @param Product $product
     * @param Swift_Mailer $mailer
     * @return RedirectResponse|Response
     */
    public function contact(Request $request, Product $product, Swift_Mailer $mailer)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

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

        Carbon::setLocale('lt');
        $this->timeLeft($product);

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
        if ($this->checkOwnerShip->isProductOwner($product)) {
            if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
                $this->doctrineActions->remove($product);
                $this->addFlash('danger', 'Produktas ištrintas!');
            }
            return $this->redirectToRoute('user_show', [
                'id' => $product->getUser()->getId(),
            ]);
        }
        throw $this->createNotFoundException('You are not allowed to reach this site.');
    }

    private function timeLeft(Product $product): string
    {
        return $product->timeLeft = Carbon::parse($product->getDeadline())->diffForHumans();
    }

    public function timeLeftForEach($products)
    {
        Carbon::setLocale('lt');
        foreach ($products as $product) {
            $this->timeleft($product);
        }
        return $products;
    }

    private function makeForm($product, $request)
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        return $form;
    }

}
