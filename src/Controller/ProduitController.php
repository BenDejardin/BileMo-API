<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    #[Route('/produits', name: 'app_produits', methods:'GET')]
    public function produits(ProduitRepository $produitRepository): JsonResponse
    {
        // Methode JSon vas faire un json_encode puis un $normalizer->normalize
        // Argument($data, codeStatut, en tete, parametre (exemple : les groups qu'on souhaite))
        return !$produitRepository->findAll() 
        ? $this->json(['status' => 404,'message' => "Aucun produit existant"], 404)
        : $this->json($produitRepository->findAll(), 200, []);
    }

    #[Route('/produit/{id}', name: 'app_produit', methods:'GET')]
    public function produit(int $id, ProduitRepository $produitRepository): JsonResponse
    {
        return !$produitRepository->find($id) 
        ? $this->json(['status' => 404, 'message' => "Ce produit n'existe pas"])
        :  $this->json($produitRepository->find($id), 200, []);
    }
}
