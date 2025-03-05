<?php

namespace App\Controller;

use App\Entity\CommentaireEvent;
use App\Form\CommentaireEventType;
use App\Entity\Evenement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireEventController extends AbstractController
{
    #[Route('/evenement/{id}/commenter', name: 'app_commentaire_event_new', methods: ['GET', 'POST'])]
    public function ajouterCommentaire(
        Request $request, 
        Evenement $evenement, 
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour ajouter un commentaire.');
            return $this->redirectToRoute('app_login');
        }

        // Créer et gérer le formulaire de commentaire
        $commentaire = new CommentaireEvent(); // This now works because of the use statement
        $form = $this->createForm(CommentaireEventType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setEvenement($evenement);
            $commentaire->setUser($this->getUser());
            $commentaire->setDateCreation(new \DateTime());

            $entityManager->persist($commentaire);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire ajouté avec succès !');
            return $this->redirectToRoute('detail_evenement', ['id' => $evenement->getId()]);
        }

        return $this->render('evenement/detail.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
            'commentaires' => $evenement->getCommentaireEvents(), // Assurer l'affichage des commentaires
        ]);
    }

    #[Route('/evenement/{id}', name: 'detail_evenement', methods: ['GET'])]
    public function detail(Evenement $evenement): Response
    {
        return $this->render('evenement/detail.html.twig', [
            'evenement' => $evenement,
            'commentaires' => $evenement->getCommentaireEvents(),
        ]);
    }
}