<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractApiController
{
    /**
     * @Route("/products", name="create_product", methods={"POST"})
     */
    public function crateProduct(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->buildForm(ProductType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Product $product */
            $product = $form->getData();

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->respond($product, Response::HTTP_OK, ['product']);
        }

        return $this->respond($form, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/products/{id}", name="edit_product", methods={"PATCH", "PUT"})
     */
    public function editProduct($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->respond(['title' => 'Product not exist'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->respond($product, Response::HTTP_OK, ['product']);
        }

        return $this->respond($form, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/products/{id}", name="get_product", methods={"GET"})
     */
    public function getProduct($id, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if ($product) {
            return $this->respond($product, Response::HTTP_OK, ['product']);
        }

        return $this->respond(['title' => 'Product not exist'], Response::HTTP_NOT_FOUND);
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
            return $this->respond(null,Response::HTTP_NO_CONTENT);
        }

        return $this->respond(['title' => 'Product not exist'], Response::HTTP_NOT_FOUND);
    }
}
