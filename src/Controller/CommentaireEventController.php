<?php
namespace App\Controller;

use App\Entity\CommentaireEvent;
use App\Form\CommentaireEventType;
use App\Repository\CommentaireEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Evenement;


class CommentaireEventController extends AbstractController
{
    #[Route('/evenement/{id}/commenter', name: 'app_commentaire_event_new', methods: ['GET', 'POST'])]
    public function ajouterCommentaire(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour ajouter un commentaire.');
            return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion
        }

        // Créer un nouveau commentaire
        $commentaire = new CommentaireEvent();
        $form = $this->createForm(CommentaireEventType::class, $commentaire);
        $form->handleRequest($request);

        // Traiter le formulaire lorsque celui-ci est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Associer le commentaire à l'événement et à l'utilisateur
            $commentaire->setEvenement($evenement);
            $commentaire->setUser($this->getUser()); // Associer l'utilisateur connecté au commentaire
            $commentaire->setDateCreation(new \DateTime()); // Date de création du commentaire

            // Sauvegarder le commentaire en base de données
            $entityManager->persist($commentaire);
            $entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Commentaire ajouté avec succès !');

            // Rediriger vers la page de détail de l'événement
            return $this->redirectToRoute('detail_evenement', ['id' => $evenement->getId()]);
        }

        // Afficher le formulaire dans la vue
        return $this->render('evenement/detail.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

#[Route('/evenement/{id}', name: 'detail_evenement', methods: ['GET'])]
public function detail(Evenement $evenement): Response
{
    return $this->render('evenement/detail.html.twig', [
        'evenement' => $evenement,
    ]);
}
}