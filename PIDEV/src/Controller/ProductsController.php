<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ProductType;
use App\Entity\Produit;
use App\Entity\Categorie;

class ProductsController extends AbstractController
{
    #[Route('/produit/ajout', name: 'ajout-produit')]
    public function ajouterProduit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProductType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('urlImageProduit')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    $produit->setUrlImageProduit('uploads/images/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('liste-produits');
        }

        return $this->render('produit/productcreate.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produit', name: 'liste-produits')]
    public function listeProduits(EntityManagerInterface $entityManager): Response
    {
        $produits = $entityManager->getRepository(Produit::class)->findAll();

        return $this->render('produit/produit.html.twig', [
            'produits' => $produits
        ]);
    }

    #[Route('/produit/edit/{id}', name: 'edit-produit')]
    public function editProduit(Request $request, EntityManagerInterface $entityManager, Produit $produit): Response
    {
        $form = $this->createForm(ProductType::class, $produit, [
            'is_edit' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('urlImageProduit')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    $produit->setUrlImageProduit('uploads/images/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('liste-produits');
        }

        return $this->render('produit/productcreate.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit
        ]);
    }

    #[Route('/produit/delete/{id}', name: 'delete-produit')]
    public function deleteProduit(EntityManagerInterface $entityManager, Produit $produit): Response
    {
        $entityManager->remove($produit);
        $entityManager->flush();

        return $this->redirectToRoute('liste-produits');
    }
}
