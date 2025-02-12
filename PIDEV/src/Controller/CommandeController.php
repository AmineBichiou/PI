<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Produit;
use App\Entity\Commande;

class CommandeController extends AbstractController
{
    #[Route('/mon-panier', name: 'mon_panier')]
    public function voirPanier(EntityManagerInterface $entityManager): Response
    {
        $commandes = $entityManager->getRepository(Commande::class)->findAll();

        return $this->render('commande/mon_panier.html.twig', [
            'commandes' => $commandes
        ]);
    }

    #[Route('/produit/ajouter-au-panier/{id}/{quantite}', name: 'ajouter_commande')]
    public function ajouterAuPanier(Produit $produit, int $quantite, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si une commande existe déjà pour ce produit
        $commandeExistante = $entityManager->getRepository(Commande::class)->findOneBy(['produit' => $produit]);
    
        if ($commandeExistante) {
            $commandeExistante->setQuantite($commandeExistante->getQuantite() + $quantite);
        } else {
            $commande = new Commande();
            $commande->setProduit($produit);
            $commande->setQuantite($quantite);
            $entityManager->persist($commande);
        }
    
        $entityManager->flush();
    
        $this->addFlash('success', 'Produit ajouté au panier avec succès ✅');
        return $this->redirectToRoute('mon_panier');
    }
    

    #[Route('/commande/supprimer/{id}', name: 'supprimer_commande')]
    public function supprimerCommande(EntityManagerInterface $entityManager, Commande $commande): Response
    {
        $entityManager->remove($commande);
        $entityManager->flush();

        return $this->redirectToRoute('mon_panier');
    }

    #[Route('/commande/modifier/{id}', name: 'modifier_quantite', methods: ['POST'])]
    public function modifierQuantite(Request $request, EntityManagerInterface $entityManager, Commande $commande): Response
    {
        $nouvelleQuantite = $request->request->get('quantite');

        if (!is_numeric($nouvelleQuantite) || $nouvelleQuantite < 1) {
            $this->addFlash('error', 'Quantité invalide.');
            return $this->redirectToRoute('mon_panier');
        }

        $commande->setQuantite((int) $nouvelleQuantite);
        $entityManager->flush();

        $this->addFlash('success', 'Quantité mise à jour avec succès ✅');

        return $this->redirectToRoute('mon_panier');
    }
}
