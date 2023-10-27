<?php
namespace App\Service;

use App\Repository\ClientRepository;
use App\Repository\UtilisateurRepository;

class ClientService
{
    private $clientRepository;
    private $utilisateurRepository;

    public function __construct(ClientRepository $clientRepository, UtilisateurRepository $utilisateurRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->clientRepository = $clientRepository;
    }

    public function findClientById(int $idClient)
    {
        return $this->clientRepository->find($idClient);
    }

    public function isClientByUser($idClient, $idUtilisateur) : bool
    {
        $client = $this->clientRepository->find($idClient);
        $utilisateur = $this->utilisateurRepository->find($idUtilisateur);

        return $client->getUtilisateurs()->contains($utilisateur);
    
    }

    public function isClient($idClient) : bool
    {
        return $this->clientRepository->find($idClient) != null ? true : false; 
    }

    public function isUtilisateur($idUtilisateur) : bool
    {
        return $this->utilisateurRepository->find($idUtilisateur) != null ? true : false; 
    }
}
