<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\EvenementRepository;
use App\Entity\Inscription;
use App\Form\InscriptionType; 

final class EvenementController extends AbstractController
{



    #[Route('/evenement/ajout', name: 'app_evenement_ajout')]
    public function ajout(Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $params): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de la photo
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                $photoFilename = uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move(
                    $params->get('photos_directory'),
                    $photoFilename
                );
                $evenement->setPhoto($photoFilename);
            }

            // Gestion des régions
            $regions = $form->get('regions')->getData();
            foreach ($regions as $region) {
                $evenement->addRegion($region); // Utilise la méthode addRegion de l'entité Evenement
            }

            $entityManager->persist($evenement);
            $entityManager->flush();

            $this->addFlash('success', 'Événement ajouté avec succès !');
            return $this->redirectToRoute('app_evenement');
        }

        return $this->render('evenement/ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    // Afficher la liste des événements
    #[Route('/evenement', name: 'app_evenement')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $evenements = $entityManager->getRepository(Evenement::class)->findAll();

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,  
        ]);
    }

    // Voir un événement
    #[Route('/evenement/{id}', name: 'app_evenement_voir')]
    public function voir(Evenement $evenement): Response
    {
        return $this->render('evenement/voir.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    // Ajouter un événement avec stockage de la photo dans le répertoire
   

    // Modifier un événement avec gestion de la photo
    #[Route('/evenement/modifier/{id}', name: 'app_evenement_modifier')]
    public function modifier(Request $request, Evenement $evenement, EntityManagerInterface $entityManager, ParameterBagInterface $params): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photoFile')->getData();

            if ($photoFile) {
                $photoFilename = uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move(
                    $params->get('photos_directory'),
                    $photoFilename
                );
                $evenement->setPhoto($photoFilename);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Événement modifié avec succès.');

            return $this->redirectToRoute('app_evenement');
        }

        return $this->render('evenement/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Supprimer un événement et la photo associée
    #[Route('/evenement/supprimer/{id}', name: 'app_evenement_supprimer')]
    public function supprimer(Evenement $evenement, EntityManagerInterface $entityManager, ParameterBagInterface $params): Response
    {
        // Supprimer le fichier de la photo si elle existe
        $filesystem = new Filesystem();
        $photoPath = $params->get('photos_directory') . '/' . $evenement->getPhoto();
        if ($filesystem->exists($photoPath)) {
            $filesystem->remove($photoPath);
        }

        $entityManager->remove($evenement);
        $entityManager->flush();
        $this->addFlash('success', 'Événement supprimé avec succès.');

        return $this->redirectToRoute('app_evenement');
    }
//liste 
#[Route('/evenements/liste', name: 'app_evenements_liste')]
public function listEvenements(Request $request, EvenementRepository $evenementRepository): Response
{
    // Récupérer le terme de recherche depuis la requête
    $searchTerm = $request->query->get('search', '');

    // Récupérer le paramètre de tri depuis la requête
    $sortBy = $request->query->get('sort_by', 'default');

    // Filtrer les événements en fonction du terme de recherche et du tri
    $evenements = $evenementRepository->findBySearchAndSort($searchTerm, $sortBy);

    return $this->render('evenement/liste.html.twig', [
        'evenements' => $evenements,
        'searchTerm' => $searchTerm, // Passer le terme de recherche au template
        'sortBy' => $sortBy, // Passer le paramètre de tri au template
    ]);
}

    // Détail d'un événement
    #[Route('/evenements/{id}', name: 'app_evenement_detail')]
    public function detail(Evenement $evenement): Response
    {
        return $this->render('evenement/detail.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/api/evenements', name: 'app_evenements_api', methods: ['GET'])]
    public function apiEvents(EvenementRepository $evenementRepository): Response
    {
        // Récupérer tous les événements
        $evenements = $evenementRepository->findAll();
    
        // Préparer les données pour FullCalendar
        $data = [];
        foreach ($evenements as $evenement) {
            $data[] = [
                'id' => $evenement->getId(),
                'title' => $evenement->getTitre(),
                'start' => $evenement->getDateDebut() ? $evenement->getDateDebut()->format('Y-m-d H:i:s') : null,
                'end' => $evenement->getDateFin() ? $evenement->getDateFin()->format('Y-m-d H:i:s') : null,
                'description' => $evenement->getDescription(),
                'color' => '#378006', // Couleur spécifique pour chaque événement
            ];
        }
    
        // Retourner les données au format JSON
        return $this->json($data);
    }

    //inscription
    #[Route('/evenement/inscription/{id}', name: 'app_evenement_inscription_form')]
    public function inscriptionForm(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
    
        // Créez une nouvelle inscription
        $inscription = new Inscription();
        $inscription->setEvenement($evenement);
    
        // Si l'utilisateur est connecté, pré-remplissez les champs
        if ($user) {
            $inscription->setNom($user->getNom());
            $inscription->setPrenom($user->getPrenom());
            $inscription->setEmail($user->getEmail());
            $inscription->setNumTel($user->getNumTel());
        }
    
        // Créez le formulaire
        $form = $this->createForm(InscriptionType::class, $inscription);
    
        // Traitez la soumission du formulaire
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Associez l'utilisateur connecté à l'inscription
            if ($user) {
                $inscription->setUser($user);
            }
    
            // Enregistrez l'inscription en base de données
            $entityManager->persist($inscription);
            $entityManager->flush();
    
            $this->addFlash('success', 'Inscription réussie !');
            return $this->redirectToRoute('app_evenement'); // Redirection vers la liste des événements
        }
    
        // Affichez le formulaire
        return $this->render('evenement/inscription_form.html.twig', [
            'form' => $form->createView(),
            'evenement' => $evenement,
            'user' => $user, // Passez l'utilisateur connecté au template
        ]);
    }
}
