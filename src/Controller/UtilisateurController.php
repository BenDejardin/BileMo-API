<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Utilisateur;
use App\Repository\ClientRepository;
use App\Repository\UtilisateurRepository;
use App\Service\ClientService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Contracts\Cache\CacheInterface;

class UtilisateurController extends AbstractController
{
    private $clientService;
    private $cache;

    public function __construct(ClientService $clientService, CacheInterface $cache) {
        $this->clientService = $clientService;
        $this->cache = $cache;
    }

    #[Route('/client/{id}', name: 'clientUtilisateurs', methods: ['GET'])]
    public function getClientUtilisateurs(int $id, UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        // Mise en cache de cette requete 
        $reponse = $this->cache->get("getUserByClient-".$id, function() use ($utilisateurRepository, $id){
            $utilisateurs = $utilisateurRepository->findUtilisateursByClientId($id);
            if (!$this->clientService->isClient($id)) {
                return $this->json(['status' => 404, 'message' => "Le client n'a pas été trouvé."], 404);
            } elseif (!$utilisateurs) {
                return $this->json(['status' => 400, 'message' => "Le client n'a pas d'utilisateur lié à lui."], 400);
            } else {
                return $this->json($utilisateurs, 200, [], ['groups' => 'client']);
            }
        });

        return $reponse;
    }

    #[Route('/client/{idClient}/utilisateur/{idUtilisateur}', name: 'detailUtilisateur', methods: ['GET'])]
    public function detailUtilisateur(int $idClient, int $idUtilisateur, UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        $reponse = $this->cache->get("detailUtilisateur-id".$idUtilisateur, function () use ($idClient, $idUtilisateur, $utilisateurRepository) {
            if (!$this->clientService->isClient($idClient)) {
                return $this->json(['status' => 404, 'message' => "Le client n'a pas été trouvé."], 404);
            } elseif (!$this->clientService->isUtilisateur($idUtilisateur)) {
                return $this->json(['status' => 404, 'message' => "L'utilisateur n'a pas été trouvé."], 404);
            } elseif (!$this->clientService->isClientByUser($idClient, $idUtilisateur)) {
                return $this->json(['status' => 400, 'message' => "Cet utilisateur n'est pas associé à ce client."], 400);
            } else {
                return $this->json($utilisateurRepository->findUtilisateursByClientIdAndUserId($idClient, $idUtilisateur), 200, [], ['groups' => 'client']);
            }
        });

        return $reponse;
    }

    #[Route('/client/{idClient}', name: 'ajoutUtilisateur', methods:['POST'])]
    public function ajoutUtilisateur(int $idClient, Request $request , EntityManagerInterface $entityManager, SerializerInterface $serializer, UserPasswordHasherInterface $passwordHasher, ClientRepository $clientRepository): JsonResponse{
         if($this->clientService->isClient($idClient) == false) return $this->json(['status' => 404, 'message' => "Le client n'a pas été trouvé."], 404);

        try{
            $jsonRecu = $request->getContent();
            $data = json_decode($jsonRecu, true);
    
            $utilisateur = $serializer->deserialize($jsonRecu, Utilisateur::class, 'json');
    
            $utilisateur->setRoles(['ROLE_USER']);
            $utilisateur->setClient($clientRepository->find($idClient));
            $utilisateur->setPassword($passwordHasher->hashPassword($utilisateur, $data['password']));
    
            $entityManager->persist($utilisateur);
            $entityManager->flush();
    
            return $this->json($utilisateur, 200, [], ['groups' => 'client'] );
        }
        catch(NotEncodableValueException $e){
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/client/{idClient}/utilisateur/{idUtilisateur}', name: 'deleteUtilisateur', methods:['DELETE'])]
    public function suppressionUtilisateur(int $idClient, int $idUtilisateur, EntityManagerInterface $entityManager, UtilisateurRepository $utilisateurRepository): JsonResponse {
        try {
            $utilisateur = $utilisateurRepository->find($idUtilisateur);

            if (!$utilisateur) {
                return $this->json(['status' => 404, 'message' => "L'utilisateur n'a pas été trouvé."], 404);
            }

            if ($utilisateur->getClient()->getId() != $idClient) {
                return $this->json(['status' => 400, 'message' => "L'utilisateur n'appartient pas à ce client."], 400);
            }

            // Vider le cache concernant l'utilisateur
            $this->cache->delete("detailUtilisateur-id".$idUtilisateur);

            $entityManager->remove($utilisateur);
            $entityManager->flush();

            return $this->json(['status' => 200, 'message' => "L'utilisateur a bien été supprimé."], 200);

        } catch (\Exception $e) {
            return $this->json(['status' => 500, 'message' => "Une erreur s'est produite lors de la suppression de l'utilisateur.", 500]);
        }
    }
}