<?php

namespace App\Controller;

use App\Entity\CommandeFinalisee;
use App\Entity\Produit;
use App\Repository\CommandeFinaliseeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class AdminCommandeController extends AbstractController
{
    #[Route('/commandes', name: 'commandes')]
    public function index(
        CommandeFinaliseeRepository $commandeFinaliseeRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // 1. Récupérer toutes les commandes finalisées
        $commandes = $commandeFinaliseeRepository->findBy([], ['dateCommande' => 'DESC']);

        // 2. Récupérer la liste des produits (pour le <select> dans le template)
        $listeProduits = $entityManager->getRepository(Produit::class)->findAll();

        return $this->render('admin_commande/index.html.twig', [
            'commandes' => $commandes,
            'listeProduits' => $listeProduits,  // <-- Passé au template pour lister les noms
        ]);
    }

    #[Route('/commandes/modifier/{id}', name: 'modifier_commande', methods: ['POST'])]
    public function modifierCommande(
        Request $request,
        CommandeFinalisee $commandeFinalisee,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['message' => '❌ Données invalides'], Response::HTTP_BAD_REQUEST);
        }

        // 1. Mettre à jour les champs passés en JSON
        //    Ex: nomProduit, quantite, prixTotal...
        foreach ($data as $key => $value) {
            // On utilise un setter si disponible, sinon on ignore
            $setter = 'set' . ucfirst($key);
            if (method_exists($commandeFinalisee, $setter)) {
                $commandeFinalisee->$setter($value);
            }
        }

        // 2. Enregistrer en base
        $entityManager->flush();

        return new JsonResponse(['message' => '✅ Commande mise à jour avec succès !'], Response::HTTP_OK);
    }

    #[Route('/commandes/supprimer/{id}', name: 'supprimer_commande', methods: ['DELETE'])]
    public function supprimerCommande(
        CommandeFinalisee $commandeFinalisee,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $entityManager->remove($commandeFinalisee);
        $entityManager->flush();

        return new JsonResponse(['message' => '✅ Commande supprimée avec succès !'], Response::HTTP_OK);
    }
}
