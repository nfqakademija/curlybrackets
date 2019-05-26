<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Controller;

use App\DTO\Coordinate;
use App\Entity\Product;
use App\Entity\User;
use App\Form\ContactType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\CheckOwnershipService;
use App\Service\ProductJsonService;
use App\Service\TokenGeneratorService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Liip\ImagineBundle\Templating\Helper\FilterHelper;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Carbon\Carbon;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

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
     * @var UploaderHelper
     */
    private $uploadHelper;

    /**
     * @var FilterHelper
     */
    private $filterHelper;

    /**
     * ProductController constructor.
     *
     * @param UploaderHelper $uploadHelper
     * @param FilterHelper $filterHelper
     * @param ProductJsonService $productJsonService
     */
    public function __construct(
        UploaderHelper $uploadHelper,
        FilterHelper $filterHelper,
        ProductJsonService $productJsonService,
        CheckOwnershipService $checkOwnershipService
    ) {
        $this->uploadHelper = $uploadHelper;
        $this->filterHelper = $filterHelper;
        $this->productJsonService = $productJsonService;
        $this->checkOwnerShip = $checkOwnershipService;
    }


    /**
     * @Route("/index", name="product_index", methods={"GET"})
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function index(EntityManagerInterface $em): Response
    {
        $repository = $em->getRePository(Product::class);
        $products = $repository->findByActiveProducts();
        Carbon::setLocale('lt');
        foreach ($products as $product) {
            $product->timeLeft =  Carbon::parse($product->getDeadline())->diffForHumans();
        }
        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/jsonIndex", name="product_json", methods={"GET"})
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function jsonIndex(EntityManagerInterface $em): Response
    {
        $repository = $em->getRePository(Product::class);
        $products = $repository->findByActiveProducts();

        return $this->productJsonService->createJson($products);
    }

    /**
     * @Route("/jsonMap", name="product_map", methods={"GET"})
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     */
    public function activeMap(
        EntityManagerInterface $em,
        Request $request
    ): Response {

        $leftTopCorner = new Coordinate($request->get('latitudeSE'), $request->get('longitudeSE'));
        $bottomRightCorner = new Coordinate($request->get('latitudeNW'), $request->get('longitudeNW'));

        /** @var ProductRepository $repository */
        $repository = $em->getRePository(Product::class);
        $products = $repository->findProductsByLocation($leftTopCorner, $bottomRightCorner);

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
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);
        $entityManager->flush();
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

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);


        //todo $user pasiimamaas antra karta
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getDoctrine()->getRepository(User::class)
                ->findOneById($this->getUser()->getId());
            $product->setUser($user);
            $product->setCreatedAt(new DateTime('now'));
            $product->setUpdatedAt(new DateTime('now'));
            $product->setLocation($user->getLocation());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
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
            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $product->setUpdatedAt(new DateTime('now'));
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Produktas sėkmingai pakeistas!');
                return $this->redirectToRoute('user_show', [
                    'id' => $product->getUser()->getId(),
                ]);
            }

            return $this->render('product/edit.html.twig', [
                'product' => $product,
                'form' => $form->createView(),
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
            $message = (new Swift_Message('Foodsharing puslapio lankytojas ' . $form['name']->getData().
                ' nori susisiekti su Jumis'))
                ->setFrom('foodsharinglithuania@gmail.com')
                ->setTo($product->getUser()->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig',
                        ['data' => $data,
                         'product' => $product]
                    ),
                    'text/html'
                );

                $mailer->send($message);

            $this->addFlash('success', 'Jūsų žinutė išsiųsta!');
            return $this->redirectToRoute('product_index');
        }

        Carbon::setLocale('lt');
        $product->timeLeft =  Carbon::parse($product->getDeadline())->diffForHumans();

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
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($product);
                $entityManager->flush();
                $this->addFlash('danger', 'Produktas ištrintas!');
            }
            return $this->redirectToRoute('user_show', [
                'id' => $product->getUser()->getId(),
            ]);
        }
        throw $this->createNotFoundException('You are not allowed to reach this site.');
    }

}
