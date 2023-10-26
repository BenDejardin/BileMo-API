<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    #[Route('/produits', name: 'app_produits', methods:'GET')]
    public function index(ProduitRepository $produitRepository): JsonResponse
    {
        // Methode JSon vas faire un json_encode puis un $normalizer->normalize
        // Argument($data, codeStatut, en tete, parametre (exemple : les groups qu'on souhaite))
        return $this->json($produitRepository->findAll(), 200, []);
    }
}
