# Documentation du Projet BileMo API

Ce projet est une API Symfony pour la vitrine de téléphones mobiles de l'entreprise BileMo. L'API permet d'accéder à un catalogue de téléphones mobiles haut de gamme en utilisant des méthodes d'authentification JWT. L'API est conçue pour des clients B2B (business to business) qui souhaitent intégrer le catalogue de BileMo dans leurs plateformes.

## Table des matières

- [Installation](#installation)
- [Configuration](#configuration)
- [Authentification](#authentification)
- [URI](#URI)
- [Références](#références)

## Installation

1. Cloner le référentiel depuis GitHub :

   ```bash
   git clone https://github.com/BenDejardin/BileMo-API.git
   ```

2. Installer les dépendances avec Composer :

   ```bash
   composer install
   ```

3. Créer la base de données et effectuer les migrations :

   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

4. Charger les fixtures pour créer des utilisateurs :

   ```bash
   php bin/console doctrine:fixtures:load
   ```

   Les fixtures créeront des utilisateurs de test avec des rôles spécifiques.

5. Lancer le serveur de développement :

   ```bash
   php bin/console server:run
   ```

Le serveur de développement sera accessible à l'adresse http://localhost:8000.

## Configuration

Le projet utilise un fichier de configuration `config/packages/security.yaml` pour définir la stratégie d'authentification JWT.

## Authentification

L'API utilise JWT (JSON Web Token) pour l'authentification. Les utilisateurs peuvent accéder librement à l'endpoint `/login` pour obtenir un token JWT.

Pour obtenir un token JWT :

1. Accédez à l'URL `/login` dans votre navigateur ou via un client HTTP, tel que Postman.

2. Utilisez l'un des comptes de test suivants (créés par les fixtures) :
   ```json
   {
    "username": "john.doe@email.com",
    "password": "MotDePasse.1"
   }
   ```

3. Récupérez votre token JWT, puis il vous faudra l'inclure dans l'en-tête HTTP avec Authorization : bearer (votreToken).

## URI

L'API expose les URI suivants :

- `GET produits` : Récupérer la liste des produits BileMo.
- `GET produit/{id}` : Récupérer les détails d'un produit BileMo.
- `GET client/{id}` : Récupérer la liste des utilisateurs inscrits liés à un client.
- `GET client/{idClient}/utilisateur/{idUtilisateur}` : Récupérer les détails d'un utilisateur inscrit lié à un client.
- `POST client/{idClient}` : Ajouter un nouvel utilisateur lié à un client.
- `DELETE client/{idClient}/utilisateur/{idUtilisateur}` : Supprimer un utilisateur ajouté par un client.

Toutes les réponses de ces endpoints sont mises en cache pour améliorer les performances.
