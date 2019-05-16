<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Controller;

use App\Entity\Location;
use App\Entity\Product;
use App\Entity\User;
use App\Form\LocationType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/", name="product_index", methods={"GET"})
     * @param ProductRepository $productRepository
     * @return Response
     * @throws Exception
     */
    public function index(EntityManagerInterface $em): Response
    {
        $repository = $em->getRePository(Product::class);
        $products = $repository->findByActiveProducts();

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }


    /**
     * @Route("/{id}/give", name="product_give", methods={"GET"})
     * @param Product $product
     * @param User $user
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

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getDoctrine()->getRepository(User::class)
                ->findOneById($this->getUser()->getId());
            $product->setUser($user);
            $product->setCreatedAt(new DateTime('now'));
            $product->setUpdatedAt(new DateTime('now'));
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

        if ($product->getUser()->getId() === $this->get('security.token_storage')->getToken()->getUser()->getId()) {
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
        throw $this->createNotFoundException('You are not allowed to reach this site.');
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($product->getUser()->getId() === $this->get('security.token_storage')->getToken()->getUser()->getId()) {
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
