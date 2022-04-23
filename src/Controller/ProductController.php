<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Service\FormErrorProcess;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/products", name="create_product", methods={"POST"})
     */
    public function crateProduct(Request $request, EntityManagerInterface $entityManager, FormErrorProcess $formErrorProcess): Response
    {
        $formData = json_decode($request->getContent(), true);

        $form = $this->createForm(ProductType::class);
        $form->handleRequest($request);
        $form->submit($formData);

        if ($form->isValid()) {
            /** @var Product $product */
            $product = $form->getData();

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->json($product, Response::HTTP_CREATED, [], ['groups' => ['product']]);
        }

        return $this->json([
            'message' => 'There was a validation error',
            'errors' => $formErrorProcess->getErrorsFromForm($form)
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/products/{id}", name="edit_product", methods={"PATCH", "PUT"})
     */
    public function editProduct($id, Request $request, EntityManagerInterface $entityManager, FormErrorProcess $formErrorProcess): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json([
                'message' => 'Product not exist',
            ], Response::HTTP_BAD_REQUEST);
        }

        $formData = json_decode($request->getContent(), true);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $form->submit($formData, false);

        if ($form->isValid()) {
            $entityManager->flush();

            return $this->json($product, Response::HTTP_CREATED, [], ['groups' => ['product']]);
        }

        return $this->json([
            'message' => 'There was a validation error',
            'errors' => $formErrorProcess->getErrorsFromForm($form)
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/products/{id}", name="get_product", methods={"GET"})
     */
    public function getProduct($id, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if ($product) {
            return $this->json($product, Response::HTTP_OK, [], ['groups' => ['product']]);
        }

        return $this->json([
            'message' => 'Product not exist',
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/products/{id}", name="delete_product", methods={"DELETE"})
     */
    public function deleteProduct($id, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if ($product) {
            $entityManager->remove($product);
            $entityManager->flush();

            return $this->json([], Response::HTTP_NO_CONTENT);
        }

        return $this->json([
            'message' => 'Product not exist',
        ], Response::HTTP_NOT_FOUND);
    }
}
