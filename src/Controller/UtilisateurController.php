<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Utilisateur;
use App\Repository\ClientRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UtilisateurController extends AbstractController
{
    #[Route('/client/{id}', name: 'app_utilisateurs', methods:['GET'])]
    public function getClientUtilisateurs(int $id, UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        return $this->json($utilisateurRepository->findUtilisateursByClientId($id), 200, [], ['groups' => 'client'] );
    }
}
