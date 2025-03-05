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
use Knp\Component\Pager\PaginatorInterface;


final class EvenementController extends AbstractController
{
    #[Route('/evenement/ajout', name: 'app_evenement_ajout')]
public function ajout(Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $params): Response
{
    // Crée une nouvelle instance de l'entité Evenement
    $evenement = new Evenement();

    // Crée le formulaire
    $form = $this->createForm(EvenementType::class, $evenement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gestion de la photo
        $photoFile = $form->get('photoFile')->getData();
        if ($photoFile) {
            try {
                // Génère un nom de fichier unique
                $photoFilename = uniqid() . '.' . $photoFile->guessExtension();

                // Déplace le fichier téléchargé vers le répertoire de stockage
                $photoFile->move(
                    $params->get('photos_directory'),
                    $photoFilename
                );

                // Associe le nom du fichier à l'entité Evenement
                $evenement->setPhoto($photoFilename);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors du téléchargement de la photo.');
                return $this->redirectToRoute('app_evenement_ajout');
            }
        }

        // Gestion des régions
        $regions = $form->get('regions')->getData();
        foreach ($regions as $region) {
            $evenement->addRegion($region);
        }

        // Enregistre l'événement en base de données
        $entityManager->persist($evenement);
        $entityManager->flush();

        // Ajoute un message de succès
        $this->addFlash('success', 'Événement ajouté avec succès !');

        // Redirige l'utilisateur vers la liste des événements
        return $this->redirectToRoute('app_evenement');
    }

    // Affiche le formulaire
    return $this->render('evenement/ajout.html.twig', [
        'form' => $form->createView(),
    ]);
}






    #[Route('/evenement/{id}', name: 'app_evenement_voir')]
    public function voir(int $id, EvenementRepository $evenementRepository): Response
    {
        $evenement = $evenementRepository->find($id);
        if (!$evenement) {
            throw $this->createNotFoundException('L\'événement demandé n\'existe pas.');
        }
        return $this->render('evenement/voir.html.twig', [
            'evenement' => $evenement,
        ]);
    }



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











//liste (front) 
#[Route('/evenements/liste', name: 'app_evenements_liste')]
public function listEvenements(Request $request, EvenementRepository $evenementRepository, PaginatorInterface $paginator): Response
{
    // Récupérer le terme de recherche depuis la requête
    $searchTerm = $request->query->get('search', '');

    // Récupérer le paramètre de tri depuis la requête
    $sortBy = $request->query->get('sort_by', 'default');

    // Filtrer les événements en fonction du terme de recherche et du tri
    $query = $evenementRepository->findBySearchAndSortQuery($searchTerm, $sortBy);

    // Paginer les résultats
    $evenements = $paginator->paginate(
        $query, // Requête à paginer
        $request->query->getInt('page', 1), // Numéro de la page, 1 par défaut
        10 // Nombre d'éléments par page
    );

    return $this->render('evenement/liste.html.twig', [
        'evenements' => $evenements,
        'searchTerm' => $searchTerm, // Passer le terme de recherche au template
        'sortBy' => $sortBy, // Passer le paramètre de tri au template
    ]);
}




    // Détail d'un événement(front)
    #[Route('/evenement/{id}', name: 'detail_evenement', methods: ['GET'])]
public function detail(int $id, EvenementRepository $evenementRepository): Response
{
    // Récupérer l'événement par son ID
    $evenement = $evenementRepository->find($id);

    // Vérifier si l'événement existe
    if (!$evenement) {
        throw $this->createNotFoundException('L\'événement demandé n\'existe pas.');
    }

    // Afficher la vue
    return $this->render('evenement/detail.html.twig', [
        'evenement' => $evenement,
    ]);
}
//calendrier
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
    #[Route('/evenement', name: 'app_evenement')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $evenements = $entityManager->getRepository(Evenement::class)->findAll();

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,  
        ]);
    }
}
