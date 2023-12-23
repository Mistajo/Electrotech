<?php

namespace App\Controller\Admin\Product;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class ProductController extends AbstractController
{
    #[Route('/product', name: 'admin.product.index')]
    public function index(): Response
    {
        return $this->render('pages/admin/product/index.html.twig');
    }
}