<?php
namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;
    
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $data = [
            'clients' => [
                ['raison_sociale' => 'Client A'],
                ['raison_sociale' => 'Client B'],
                ['raison_sociale' => 'Client C'],
            ],
            'produits' => [
                ['nom' => 'Produit 1', 'dimension' => '10x10', 'prix' => '100.00', 'marque' => 'Marque A', 'description' => 'Description du Produit 1'],
                ['nom' => 'Produit 2', 'dimension' => '15x20', 'prix' => '150.00', 'marque' => 'Marque B', 'description' => 'Description du Produit 2'],
                ['nom' => 'Produit 3', 'dimension' => '8x12', 'prix' => '80.00', 'marque' => 'Marque C', 'description' => 'Description du Produit 3'],
            ],
            'utilisateurs' => [
                ['nom' => 'John', 'prenom' => 'Doe', 'email' => 'john.doe@email.com', 'mot_de_passe' => 'MotDePasse.1', 'client_id' => 1],
                ['nom' => 'Jane', 'prenom' => 'Smith', 'email' => 'jane.smith@email.com', 'mot_de_passe' => 'motdepasse2', 'client_id' => 2],
                ['nom' => 'Alice', 'prenom' => 'Johnson', 'email' => 'alice.johnson@email.com', 'mot_de_passe' => 'motdepasse3', 'role' => 'Utilisateur', 'client_id' => 3],
            ],
        ];

        foreach ($data['clients'] as $clientData) {
            $client = new Client();
            $client->setRaisonSociale($clientData['raison_sociale']);
            $manager->persist($client);
        }

        foreach ($data['produits'] as $produitData) {
            $produit = new Produit();
            $produit->setNom($produitData['nom']);
            $produit->setDimension($produitData['dimension']);
            $produit->setPrix($produitData['prix']);
            $produit->setMarque($produitData['marque']);
            $produit->setDescription($produitData['description']);
            $manager->persist($produit);
        }

        foreach ($data['utilisateurs'] as $utilisateurData) {
            $utilisateur = new Utilisateur();
            $utilisateur->setNom($utilisateurData['nom']);
            $utilisateur->setPrenom($utilisateurData['prenom']);
            $utilisateur->setEmail($utilisateurData['email']);
            $utilisateur->setPassword($this->userPasswordHasher->hashPassword($utilisateur, $utilisateurData['mot_de_passe']));
            $utilisateur->setRoles(["ROLE_USER"]);

            $client = $manager->getRepository(Client::class)->find($utilisateurData['client_id']);
            $utilisateur->setClient($client);

            $manager->persist($utilisateur);
        }
        $manager->flush();
    }
}
