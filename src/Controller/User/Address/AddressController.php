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
        $addresses = $addressRepository->findBy(['user' => $this->getUser()], ['updatedAt' => 'DESC']);
        $addresses = $addressRepository->findAll();
        return $this->render('pages/user/address/index.html.twig', [
            'addresses' => $addresses
        ]);
    }

    #[Route('/address/create', name: 'user.address.create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em,): Response
    {
        $user = $this->getUser();
        $address = new Address();
        $form = $this->createForm(AddressFormType::class, $address);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($user);
            $em->persist($address);
            $em->flush();
            // Désélectionner l'ancienne adresse par défaut et définir la nouvelle comme adresse par défaut si le formulaire a été soumis avec la case cochée
            if ($form->get('defaultAddress')->getData() === true) {
                $user = $address->getUser();
                // Parcourir toutes les adresses de l'utilisateur pour trouver l'adresse par défaut et la désélectionner
                foreach ($user->getAddresses() as $address) {
                    if ($address->isDefaultAddress() === true) {
                        $address->setDefaultAddress(false);
                    }
                }
                // Définir la nouvelle adresse comme adresse par défaut
                $address->setDefaultAddress(true);
            }
            // Enregistrer les modifications
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
        $user = $this->getUser();
        $form = $this->createForm(AddressFormType::class, $address, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($user);
            $em->persist($address);
            $em->flush();
            // Désélectionner l'ancienne adresse par défaut et définir la nouvelle comme adresse par défaut si le formulaire a été soumis avec la case cochée
            if ($form->get('defaultAddress')->getData() === true) {
                $user = $address->getUser();
                $ancienneAdresseParDefaut = null;
                // Parcourir toutes les adresses de l'utilisateur pour trouver l'adresse par défaut et la désélectionner
                foreach ($user->getAddresses() as $a) {
                    if ($a->getId() === $address->getId()) {
                        // Si cette adresse est l'adresse que nous venons de modifier, le statut de "Adresse par défaut" doit être définit à true
                        $a->setDefaultAddress(true);
                    } else if ($a->isDefaultAddress() === true) {
                        // Si cette adresse est une ancienne adresse définie comme adresse par défaut, elle doit être désélectionnée
                        $a->setDefaultAddress(false);
                        $ancienneAdresseParDefaut = $a;
                    }
                }
                // Si la nouvelle adresse modifiée vient d'être créée, elle sera la dernière adresse de l'utilisateur
                if (!$ancienneAdresseParDefaut) {
                    $adresses = $user->getAddresses();
                    $ancienneAdresseParDefaut = $adresses[count($adresses) - 1];
                }
                // Enregistrer les modifications sur l'ancienne adresse par défaut si une nouvelle adresse par défaut est définie
                if ($ancienneAdresseParDefaut) {
                    $em->persist($ancienneAdresseParDefaut);
                }
                $em->persist($user);
            }
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

    #[Route('/address/default-Address/{id}', name: 'user.defaultaddress.edit', methods: ['GET', 'PUT'])]
    public function defaultAddress(Address $address, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        // Parcourir toutes les adresses de l'utilisateur pour désélectionner l'ancienne adresse par défaut
        foreach ($user->getAddresses() as $a) {
            if ($a->isDefaultAddress() === true) {
                $a->setDefaultAddress(false);
            }
        }

        // Définir la nouvelle adresse comme adresse par défaut
        $address->setDefaultAddress(true);

        // Enregistrer les modifications
        $em->persist($address);
        $em->flush();

        $this->addFlash("success", "L'adresse a bien été définie comme adresse par défaut.");
        return $this->redirectToRoute('user.address.index');
    }
}
