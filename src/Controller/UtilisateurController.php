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

class UtilisateurController extends AbstractController
{
    private $clientService;

    public function __construct(ClientService $clientService) {
        $this->clientService = $clientService;
    }

    #[Route('/client/{id}', name: 'clientUtilisateurs', methods: ['GET'])]
    public function getClientUtilisateurs(int $id, UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        $utilisateurs = $utilisateurRepository->findUtilisateursByClientId($id);

        if (!$this->clientService->isClient($id)) {
            return $this->json("Le client n'a pas été trouvé.", 404);
        } elseif (!$utilisateurs) {
            return $this->json("Le client n'a pas d'utilisateur lié à lui.", 404);
        } else {
            return $this->json($utilisateurs, 200, [], ['groups' => 'client']);
        }
    }

    #[Route('/client/{idClient}/utilisateur/{idUtilisateur}', name: 'detailUtilisateur', methods: ['GET'])]
    public function detailUtilisateur(int $idClient, int $idUtilisateur, UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        if (!$this->clientService->isClient($idClient)) {
            return $this->json("Le client n'a pas été trouvé.", 404);
        } elseif (!$this->clientService->isUtilisateur($idUtilisateur)) {
            return $this->json("L'utilisateur n'a pas été trouvé.", 404);
        } elseif (!$this->clientService->isClientByUser($idClient, $idUtilisateur)) {
            return $this->json("Cet utilisateur n'est pas associé à ce client.", 404);
        } else {
            return $this->json($utilisateurRepository->findUtilisateursByClientIdAndUserId($idClient, $idUtilisateur), 200, [], ['groups' => 'client']);
        }
    }
}