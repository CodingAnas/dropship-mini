<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Document\Product;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;

use App\Service\PriceCalculator;

class ProductController extends AbstractController
{

    #[Route(
        '/api/products/seed',
        name: 'seed_products',
        methods: ['GET']
    )]
    public function seedProducts(DocumentManager $dm)
    {
        $data = [
            ['Ceramic Mug', 9.99, 'local', 'MUG-001'],
            ['Phone Stand', 14.50, 'ali', 'ALI-STAND-22'],
            ['Desk Lamp', 29.00, 'ali', 'ALI-LAMP-07'],
        ];

        foreach ($data as [$title, $price, $source, $supplierSku]) {
            $product = new Product();
            $product->setTitle($title);
            $product->setPrice($price);
            $product->setSource($source);
            $product->setSupplierSku($supplierSku);

            $dm->persist($product);
        }

        $dm->flush();

        return $this->json(['seeded' => count($data)]);
    }

    //We will send the above seed as payload in JSON 

    // #[Route('/api/products/bulk', name: 'api_product_bulk', methods: ['POST'])]
    // public function bulkCreate(
    //     #[MapRequestPayload(type: Product::class)] array $products,
    //     DocumentManager $dm,
    // ): JsonResponse {
    //     foreach ($products as $product) {
    //         $dm->persist($product);
    //     }

    //     $dm->flush();

    //     return $this->json(['created' => count($products)], 201);
    // }

    #[Route(
        '/api/products',
        name: 'all_products',
        methods: ['GET']
    )]
    public function getProducts(DocumentManager $dm, PriceCalculator $pr): Response
    {

        $table = $dm->getRepository(Product::class);
        $products = $table->findAll();
        $data = array_map(fn($product) => [
            'title' => $product->getTitle(),
            'price' => $product->getPrice(),
            'source' => $product->getSource(),
            'supplierSku' => $product->getSupplierSku(),
            'sellingPrice' => $pr->withMarkup($product->getPrice())
        ], $products);

        return $this->json($data);
    }

    #[Route(
        '/api/products/{id}',
        name: "specific_product",
        methods: ['GET']
    )]
    public function getProduct(DocumentManager $dm, PriceCalculator $pr, string $id): Response
    {
        $table = $dm->getRepository(Product::class);
        $product = $table->find($id);
        return $this->json([
            'title' => $product->getTitle(),
            'price' => $product->getPrice(),
            'source' => $product->getSource(),
            'supplierSku' => $product->getSupplierSku(),
            'sellingPrice' => $pr->withMarkup($product->getPrice())
        ]);
    }

    #[Route(
        '/api/products/{id}/edit',
        name: 'edit_product',
        methods: ['GET', 'POST']
    )]
    public function editProduct(DocumentManager $dm, string $id, Request $request): Response
    {

        $table = $dm->getRepository(Product::class);
        $product = $table->find($id);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dm->flush();
            return $this->redirectToRoute("all_products");
        }

        return $this->render('product/edit.html.twig', ['form' => $form]);
    }
}
