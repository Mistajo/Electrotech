<?php

namespace App\Controller\User\Order;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class OrderController extends AbstractController
{
    #[Route('/order', name: 'user.order.index')]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('pages/user/order/index.html.twig');
    }
}
