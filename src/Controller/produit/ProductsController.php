<?php

namespace App\Controller\produit;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\Commentaire;
use App\Form\ProductType;
use App\Repository\CommentaireRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductsController extends AbstractController
{
    private $entityManager;
    private $userRepository;
    private $httpClient;
    private $commentaireRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, HttpClientInterface $httpClient, CommentaireRepository $commentaireRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->httpClient = $httpClient;
        $this->commentaireRepository = $commentaireRepository;
    }

    #[Route('/produit/ajout', name: 'ajout_produit')]
    public function addProduct(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProductType::class, $produit, ['attr' => ['novalidate' => 'novalidate']]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->VhImageUpload($form, $produit);
            $produit->setUser($this->getUser());
            $this->entityManager->persist($produit);
            $this->entityManager->flush();
            $this->addFlash('success', 'Product added successfully!');
            return $this->redirectToRoute('liste_produits');
        }
        return $this->render('produit/productcreate.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }

    private function VhImageUpload($form, Produit $product): void
    {
        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('error', 'Form is not submitted or invalid.');
            return;
        }

        if (!$form->has('imageFile')) {
            throw new \RuntimeException('The form does not have an "imageFile" field.');
        }
        $imageFile = $form->get('imageFile')->getData();
        if ($imageFile) {
            if (!$imageFile instanceof UploadedFile) {
                throw new \RuntimeException(sprintf(
                    'Expected UploadedFile, got %s instead.',
                    is_object($imageFile) ? get_class($imageFile) : gettype($imageFile)
                ));
            }

            if ($imageFile->getError() !== UPLOAD_ERR_OK) {
                $this->addFlash('error', 'File upload error: ' . $imageFile->getErrorMessage());
                return;
            }

            $extension = strtolower($imageFile->getClientOriginalExtension());
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($extension, $allowed)) {
                $product->setImageFile($imageFile);
                $newFilename = uniqid() . '.' . $extension;
                $product->setImageName($newFilename);
            } else {
                $this->addFlash('error', 'Invalid image format. Allowed formats: jpg, jpeg, png, gif.');
            }
        }
    }

    #[Route('/produit', name: 'liste_produits')]
    public function listProducts(): Response
    {
        $products = $this->entityManager->getRepository(Produit::class)->findBy(['user' => $this->getUser()]);
        return $this->render('produit/listeproduitdashboard.html.twig', ['produits' => $products]);
    }

    #[Route('/produit/edit/{id}', name: 'edit_produit')]
    public function editProduct(Request $request, Produit $product): Response
    {
        $form = $this->createForm(ProductType::class, $product, [
            'is_edit' => true,
            'attr' => ['novalidate' => 'novalidate']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->vhImageUpload($form, $product); // Fixed variable name from $produit to $product
            $this->entityManager->flush();
            $this->addFlash('success', 'Product updated successfully!');
            return $this->redirectToRoute('liste_produits');
        }

        return $this->render('produit/productcreate.html.twig', [
            'form' => $form->createView(),
            'produit' => $product
        ]);
    }

    #[Route('/produit/delete/{id}', name: 'delete_produit')]
    public function deleteProduct(Produit $product): Response
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
        return $this->redirectToRoute('liste_produits');
    }

    #[Route('/produit/shop', name: 'shop_produits', methods: ['GET'])]
    public function shopProducts(Request $request): Response
    {
        $entityManager = $this->entityManager;
        $categories = $entityManager->getRepository(Categorie::class)->findAll();

        $queryBuilder = $entityManager->createQueryBuilder()
            ->select('p')
            ->from(Produit::class, 'p')
            ->orderBy('p.id', 'DESC');

        $searchTerm = $request->query->get('search');
        if ($searchTerm) {
            $queryBuilder->andWhere('p.nom LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(12);

        try {
            $currentPage = $request->query->getInt('page', 1);
            $pagerfanta->setCurrentPage($currentPage);
        } catch (OutOfRangeCurrentPageException $e) {
            return $this->redirectToRoute('shop_produits', ['page' => 1]);
        }


        return $this->render('homepage/productPage.html.twig', [
            'pager' => $pagerfanta,
            'categories' => $categories,
        ]);
    }

    private function handleImageUpload($form, Produit $product): void
    {
        $imageFile = $form->get('urlImageProduit')->getData();
        if ($imageFile) {
            $extension = strtolower($imageFile->getClientOriginalExtension());
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($extension, $allowed)) {
                $newFilename = uniqid() . '.' . $extension;
                try {
                    $imageFile->move($this->getParameter('images_directory'), $newFilename);
                    $product->setUrlImageProduit('uploads/images/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Image upload failed.');
                }
            } else {
                $this->addFlash('error', 'Invalid image format. Allowed: jpg, jpeg, png, gif.');
            }
        }
    }

    #[Route('/produit/aiml', name: 'aiml_api', methods: ['POST'])]
    public function callAIMLApi(Request $request, HttpClientInterface $httpClient): JsonResponse
    {
        $apiKey = '0fd268ea35df4e5f97efa439f90c761d';
        $baseUrl = "https://api.aimlapi.com/v1";
        $data = json_decode($request->getContent(), true);

        $produits = $this->entityManager->getRepository(Produit::class)->findAll();

        $StringProduits = '';
        for ($i = 0; $i < count($produits); $i++) {
            $StringProduits .= $produits[$i]->__toString() . ', ';
        }

        $prompt = $StringProduits . "je veux retourner les produits que semblent les meilleurs phares";

        try {
            $response = $httpClient->request('POST', "$baseUrl/chat/completions", [
                'headers' => [
                    'Authorization' => "Bearer $apiKey",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an AI assistant who knows everything.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ]
            ]);

            $result = $response->toArray();

            $generatedText = $result['choices'][0]['message']['content'] ?? 'Aucune réponse générée.';

            return new JsonResponse(['response' => $generatedText]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/produit/featured', name: 'produits_featured', methods: ['GET'])]
    public function getFeaturedProducts(): JsonResponse
    {
        $apiKey = '0fd268ea35df4e5f97efa439f90c761d';
        $baseUrl = "https://api.aimlapi.com/v1";

        $produits = $this->entityManager->getRepository(Produit::class)->findAll();
        $donneesProduits = array_map(fn($produit) => [
            'nom' => $produit->getNom(),
            'description' => $produit->getDescription(),
            'prixUnitaire' => $produit->getPrixUnitaire(),
            'urlImageProduit' => $produit->getUrlImageProduit(),
            'categorie' => $produit->getCategorie()->getNom(),
        ], $produits);

        $prompt = "À partir des données suivantes sur les produits, sélectionnez les 3 produits phares en fonction de leur unicité, prix et disponibilité en stock. Retournez leurs noms dans une liste :\n" . json_encode($donneesProduits, JSON_PRETTY_PRINT);

        try {
            $reponse = $this->httpClient->request('POST', "$baseUrl/chat/completions", [
                'headers' => [
                    'Authorization' => "Bearer $apiKey",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Vous êtes un assistant IA expert en analyse de produits.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ],
            ]);

            $resultat = $reponse->toArray();
            $nomsPhares = json_decode($resultat['choices'][0]['message']['content'], true);

            $produitsPhares = array_filter($produits, fn($produit) => in_array($produit->getNom(), $nomsPhares));
            $donneesPhares = array_map(fn($produit) => [
                'nom' => $produit->getNom(),
                'description' => $produit->getDescription(),
                'prixUnitaire' => $produit->getPrixUnitaire(),
                'urlImageProduit' => $produit->getUrlImageProduit(),
                'categorie' => $produit->getCategorie()->getNom(),
            ], $produitsPhares);

            return new JsonResponse(['produits_phares' => array_values($donneesPhares)]);
        } catch (\Exception $e) {
            return new JsonResponse(['erreur' => $e->getMessage()], 500);
        }
    }

    #[Route('/produit/description/generer/{nomProduit}', name: 'generer_description', methods: ['POST'])]
    public function genererDescription(Request $request, string $nomProduit): JsonResponse
    {
        if (empty($nomProduit)) {
            return new JsonResponse(['erreur' => 'Le nom du produit est requis'], 400);
        }

        $openFoodFactsUrl = "https://world.openfoodfacts.org/cgi/search.pl?search_terms=" . urlencode($nomProduit) . "&search_simple=1&json=1";

        try {
            $response = $this->httpClient->request('GET', $openFoodFactsUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Symfony-App - Test',
                ],
            ]);

            $data = $response->toArray();

            if (empty($data['products'])) {
                return new JsonResponse([
                    'erreur' => "Aucun produit trouvé pour '$nomProduit' dans Open Food Facts."
                ], 404);
            }

            $product = $data['products'][0];
            $description = $product['product_name'] ?? $nomProduit;

            if (!empty($product['generic_name'])) {
                $description = $product['generic_name'];
            } elseif (!empty($product['ingredients_text'])) {
                $description = "Un produit à base de " . strtolower($product['ingredients_text']) . ".";
            } else {
                $description = "Découvrez {$nomProduit}, un produit frais et naturel de la catégorie des fruits et légumes.";
            }

            return new JsonResponse(['description' => $description]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'erreur' => 'Échec de la récupération de la description',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/api/product/{id}/rate', name: 'api_product_rate', methods: ['POST'])]
    public function rateProduct(Request $request, string $id): JsonResponse
    {
        try {
            $produit = $this->entityManager->getRepository(Produit::class)->find($id);
            if (!$produit) {
                return new JsonResponse(['error' => 'Product not found'], 404);
            }

            $user = $this->getUser();
            if (!$user) {
                return new JsonResponse(['error' => 'Utilisateur non connecté'], 401);
            }

            $data = json_decode($request->getContent(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse(['error' => 'Invalid JSON payload'], 400);
            }

            $rating = $data['rating'] ?? null;
            if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
                return new JsonResponse(['error' => 'Invalid rating'], 400);
            }

            $existingRating = $this->entityManager->getRepository(Commentaire::class)->findOneBy([
                'produit' => $produit,
                'user' => $user,
            ]);

            if ($existingRating) {
                return new JsonResponse(['error' => 'You have already rated this product'], 403);
            }

            $this->entityManager->flush();
            $ratings = $this->entityManager->getRepository(Commentaire::class)->findBy(['produit' => $produit]);
            $totalRating = array_sum(array_map(fn($r) => $r->getNote(), $ratings));
            $uniqueUsers = count(array_unique(array_map(fn($r) => $r->getUser()->getId(), $ratings)));
            $average = $uniqueUsers > 0 ? $totalRating / $uniqueUsers : 0;

            $produit->setRate($average);
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/product/{id}/comments', name: 'api_product_comments', methods: ['GET'])]
    public function getProductComments(string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        if (!$produit) {
            return new JsonResponse(['error' => 'Produit non trouvé'], 404);
        }

        $comments = $produit->getCommentaires()->map(function (Commentaire $comment) {
            return [
                'auteur' => $this->getUser()->getNom(),
                'contenu' => $comment->getContenu(),
                'note' => $comment->getNote(),
                'dateCreation' => $comment->getDateCreation()->format('Y-m-d H:i:s')
            ];
        });

        return new JsonResponse(['comments' => $comments]);
    }

    #[Route('/api/product/{id}/comment', name: 'api_product_add_comment', methods: ['POST'])]
    public function addComment(Request $request, string $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non connecté'], 401);
        }
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        if (!$produit) {
            return new JsonResponse(['error' => 'Produit non trouvé'], 404);
        }
        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['content']) || !isset($data['note'])) {
            return new JsonResponse(['error' => 'Données invalides'], 400);
        }
        $commentaire = new Commentaire();
        $commentaire->setContenu($data['content'])
            ->setNote((float)$data['note'])
            ->setAuteur($this->getUser()->getNom())
            ->setProduit($produit)
            ->setUser($user);
        $entityManager->persist($commentaire);
        $entityManager->flush();

        $averageRate = $entityManager->getRepository(Commentaire::class)
            ->getAverageRatingByProduit($produit);
        $produit->setRate($averageRate);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'updatedRate' => $averageRate
        ]);
    }

    #[Route('/api/products', name: 'api_products_all', methods: ['GET'])]
    public function getAllProducts(Request $request): JsonResponse
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Produit::class, 'p')
            ->orderBy('p.id', 'DESC');

        // Apply filters
        $searchTerm = $request->query->get('search');
        $priceMax = $request->query->get('price_max');
        $categories = $request->query->get('categories') ? explode(',', $request->query->get('categories')) : [];

        if ($searchTerm) {
            $queryBuilder->andWhere('p.nom LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($priceMax !== null && is_numeric($priceMax)) {
            $queryBuilder->andWhere('p.prixUnitaire <= :priceMax')
                ->setParameter('priceMax', (float)$priceMax);
        }

        if (!empty($categories)) {
            $queryBuilder->andWhere('p.categorie IN (:categories)')
                ->setParameter('categories', $categories);
        }

        $products = $queryBuilder->getQuery()->getResult();

        $productData = array_map(fn($produit) => [
            'id' => $produit->getId(),
            'nom' => $produit->getNom(),
            'description' => $produit->getDescription(),
            'prixUnitaire' => $produit->getPrixUnitaire(),
            'urlImageProduit' => $this->vichUploaderAsset($produit, 'imageFile'), // Adjust based on your setup
            'categorie' => $produit->getCategorie()->getNom(),
            'quantite' => $produit->getQuantite(),
        ], $products);

        return new JsonResponse(['products' => $productData]);
    }

    private function vichUploaderAsset($entity, $fieldName): string
    {
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        return $helper->asset($entity, $fieldName) ?? '';
    }

    #[Route('/api/generate-description/{query}', name: 'api_generate_description', methods: ['POST'])]
    public function generateDescription(Request $request,string $query): JsonResponse
    {
        $defaultPayload = [
            "context" => "Description concise (maximum 3 lignes) d'un produit agricole pour un site e-commerce. Pas d'introduction ni de texte générique, seulement la description du produit.",
            "formality" => "default",
            "keywords" => ["fruits", "légumes", "agriculture", "produit frais"],
            "max_tokens" => 150,
            "model" => "gemini-2-0-flash",
            "n" => 1,
            "source_lang" => "fr",
            "target_lang" => "fr",
            "temperature" => 0.7,
            "title" => $query,
        ];

        $data = json_decode($request->getContent(), true) ?? [];

        $payload = array_merge($defaultPayload, $data);

        try {
            $response = $this->httpClient->request('POST', 'https://api.textcortex.com/v1/texts/blogs', [
                'headers' => [
                    'Authorization' => 'Bearer gAAAAABnyPuFUnwvy1ifyRzMuFu6IIoz2l6LKSwbC-Ywkc6cibh-hM3iygOBDYK1f8zZIogeEevTZWy-kCw2VXCWFjij1Pwx1Wzyu2u-1T2vzap1KSM8HiRN2nOwvFBbrBNJiSMlumT4JNakMvj0Yu3JSwWlMytYdTcyR1XJrZFIuPw_B3DeztA=',
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $statusCode = $response->getStatusCode();
            $content = json_decode($response->getContent(false), true);

            if ($statusCode !== 200) {
                return new JsonResponse([
                    'error' => 'Erreur API TextCortex',
                    'details' => $content,
                ], $statusCode);
            }

            $cleanDescription = $this->extractDescription($content);

            return new JsonResponse(["description" => $cleanDescription]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la requête API', 'message' => $e->getMessage()], 500);
        }
    }


    private function extractDescription(array $apiResponse): string
    {
        if (
            isset($apiResponse['status']) && $apiResponse['status'] === 'success' &&
            isset($apiResponse['data']['outputs'][0]['text'])
        ) {
            $description = $apiResponse['data']['outputs'][0]['text'];

            $cleanDescription = trim(preg_replace('/\s+/', ' ', $description));

            return $cleanDescription;
        }

        return "Aucune description valide trouvée.";




}

}