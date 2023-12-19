<?php

namespace App\Controller\User\Address;

use App\Entity\Address;
use App\Form\AddressFormType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user')]
class AddressController extends AbstractController
{
    #[Route('/address', name: 'user.address.index')]
    public function index(AddressRepository $addressRepository): Response
    {
        $addresses = $addressRepository->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']);

        $addresses = $addressRepository->findAll();

        return $this->render('pages/user/address/index.html.twig', [
            'addresses' => $addresses
        ]);
    }

    #[Route('/address/create', name: 'user.address.create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressFormType::class, $address);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $em->persist($address);
            $em->flush();
            $this->addFlash("success", "L'adresse a bien été crée.");
            return $this->redirectToRoute('user.address.index');
        }
        return $this->render('pages/user/address/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/address/edit/{id}', name: 'user.address.edit', methods: ['GET', 'PUT'])]
    public function edit(Address $address, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AddressFormType::class, $address, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($address);
            $em->flush();
            $this->addFlash("success", "L'adresse a bien été modifiée.");
            return $this->redirectToRoute('user.address.index');
        }
        return $this->render('pages/user/address/edit.html.twig', [
            'form' => $form->createView(),
            'address' => $address
        ]);
    }

    #[Route('/address/delete/{id}', name: 'user.address.delete', methods: ['DELETE'])]
    public function delete(Address $address, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid("delete_address_" . $address->getId(), $request->request->get("csrf_token"))) {
            // On prepare la requete de suppression de l'agence
            $em->remove($address);
            // on envois la requete
            $em->flush();
            // on affiche un message de succès
            $this->addFlash("success", "L'adresse a bien été supprimée.");
            // on redirige vers la page d'accueil des agences
            return $this->redirectToRoute("user.address.index");
        }
        return $this->render('pages/user/address/index.html.twig');
    }
}
