<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConversionController extends AbstractController
{
    #[Route('/api/conversion', name: 'conversion_api', methods: ['GET'])]
    public function convertirDevise(Request $request): JsonResponse
    {
        $montant = (float)$request->query->get('montant', 0);

       
        if ($montant <= 0 || !is_numeric($montant)) {
            return new JsonResponse(['success' => false, 'message' => '⚠️ Montant invalide.'], 400);
        }

       
        $apiKey = $_ENV['EXCHANGE_RATE_API_KEY'];

        $url = "https://v6.exchangerate-api.com/v6/$apiKey/latest/TND"; 

        try {
            
            $response = @file_get_contents($url);

            if (!$response) {
                throw new \Exception('❌ Impossible d’accéder à l’API.');
            }

            $data = json_decode($response, true);

            
            if (!isset($data['conversion_rates'])) {
                throw new \Exception('⚠️ Erreur lors de la récupération des taux de conversion.');
            }

           
            $conversion = [
                'USD' => round($montant * $data['conversion_rates']['USD'], 2),
                'EUR' => round($montant * $data['conversion_rates']['EUR'], 2),
                'GBP' => round($montant * $data['conversion_rates']['GBP'], 2),
            ];

            return new JsonResponse(['success' => true, 'conversion' => $conversion]);

        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
