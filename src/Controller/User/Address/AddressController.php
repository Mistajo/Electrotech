<?php

namespace App\Controller\User\Address;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class AddressController extends AbstractController
{
    #[Route('/address', name: 'user.address.index')]
    public function index(): Response
    {
        return $this->render('pages/user/address/index.html.twig');
    }
}
