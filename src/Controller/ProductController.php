<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        dump($form);
//        exit;

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('picture')->getData()) {
                /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */

                $file = $form->get('picture')->getData();

                $fileName = md5($product->getId())  . '-' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('pictures_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $product->setPicture($fileName);

            } else {
                $fileName = 'placeholderProduct.jpg';
                $product->setPicture($fileName);
            }

            $product->setSupplierId($this->get('security.token_storage')->getToken()->getUser()->getId());
            $product->setCreatedAt(new \DateTime("now"));
            $product->setUpdatedAt(new \DateTime("now"));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();



            if ($form->get('picture')->getData()) {
                /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */

                $file = $form->get('picture')->getData();
                $fileName = md5($product->getId()) . '-' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $file->guessExtension();

                try {

                    $file->move(
                        $this->getParameter('pictures_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }


            } else {
                $fileName = $product->getPicture();
                $product->setPicture($fileName);

            }
            $product->setPicture($fileName);
            $product->setUpdatedAt(new \DateTime("now"));
            $this->getDoctrine()->getManager()->flush();


            return $this->redirectToRoute('product_index', [
                'id' => $product->getId(),
            ]);

        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }
}
