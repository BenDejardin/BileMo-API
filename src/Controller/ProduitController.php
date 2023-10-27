<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    private $cache;
    public function __construct(CacheInterface $cache) {
        $this->cache = $cache;
    }

    #[Route('/produits', name: 'app_produits', methods:'GET')]
    public function produits(ProduitRepository $produitRepository): JsonResponse
    {

        // Mise en cache de cette requete 
        $reponse = $this->cache->get("getAllProduct", function () use ($produitRepository) {
            $produits = $produitRepository->findAll();

            // Si les produits ne sont pas trouvés en base de données, retournez une réponse JSON appropriée.
            if (empty($produits)) {
                return $this->json(['status' => 404, 'message' => "Aucun produit existant"], 404);
            }

            // Si des produits sont trouvés, transformez-les en JSON et retournez la réponse JSON.
            return $this->json($produits, 200, []);
        });

        return $reponse;
    }

    #[Route('/produit/{id}', name: 'app_produit', methods: 'GET')]
    public function produit(int $id, ProduitRepository $produitRepository): JsonResponse
    {
        $reponse = $this->cache->get("getProduct-".$id, function () use ($produitRepository, $id) {
            $produit = $produitRepository->find($id);

            if (!$produit) {
                return $this->json(['status' => 404, 'message' => "Ce produit n'existe pas"], 404);
            }

            return $this->json($produit, 200, []);
        });

        return $reponse;
    }

}
