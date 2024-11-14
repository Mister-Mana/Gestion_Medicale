# Gestion_Medicale
API REST en PHP pour la gestion des rendez-vous médicales

# API de Gestion des Rendez-vous Médicaux

Cette API permet aux utilisateurs de gérer des rendez-vous médicaux, de consulter les services disponibles, de prendre des rendez-vous et d'annuler des rendez-vous. L'architecture est basée sur le modèle MVC et utilise MySQLi pour la gestion de la base de données.

## Fonctionnalités

### 1. Authentification des Utilisateurs

- **POST /login**
  - Authentifie un utilisateur (médecin ou patient) avec son numéro de téléphone et son mot de passe.
  - **Paramètres :**
    - `telephone` : Numéro de téléphone de l'utilisateur.
    - `mot_de_passe` : Mot de passe de l'utilisateur.
    - `role` : Rôle de l'utilisateur (`patient` ou `medecin`).
  - **Réponse :**
    - `200 OK` : Authentification réussie, renvoie l'ID de l'utilisateur et son rôle.
    - `401 Unauthorized` : Numéro de téléphone ou mot de passe incorrect.

- **POST /logout**
  - Déconnecte l'utilisateur actuel.
  - **Réponse :**
    - `200 OK` : Déconnexion réussie.

### 2. Gestion des Services Médicaux

- **GET /services**
  - Récupère tous les services médicaux disponibles.
  - **Réponse :**
    - `200 OK` : Liste des services.

- **GET /services/{id}**
  - Récupère un service médical spécifique par ID.
  - **Réponse :**
    - `200 OK` : Détails du service.
    - `404 Not Found` : Service non trouvé.

- **POST /services**
  - Crée un nouveau service médical.
  - **Paramètres :**
    - `nom_service` : Nom du service.
    - `description` : Description du service.
    - `prix` : Prix du service.
  - **Réponse :**
    - `201 Created` : ID du service créé.

- **PUT /services/{id}**
  - Met à jour un service médical existant.
  - **Paramètres :**
    - `nom_service`, `description`, `prix`.
  - **Réponse :**
    - `200 OK` : Mise à jour réussie.
    - `404 Not Found` : Service non trouvé.

- **DELETE /services/{id}**
  - Supprime un service médical.
  - **Réponse :**
    - `200 OK` : Service supprimé avec succès.
    - `404 Not Found` : Service non trouvé.

### 3. Gestion des Rendez-vous

- **POST /rendezvous**
  - Crée un nouveau rendez-vous.
  - **Paramètres :**
    - `medecin_id` : ID du médecin.
    - `service_id` : ID du service.
    - `date_rendezvous` : Date et heure du rendez-vous.
  - **Réponse :**
    - `201 Created` : ID du rendez-vous créé.
    - `400 Bad Request` : Médecin indisponible.

- **GET /rendezvous/{id}**
  - Récupère un rendez-vous spécifique par ID.
  - **Réponse :**
    - `200 OK` : Détails du rendez-vous.
    - `404 Not Found` : Rendez-vous non trouvé.

- **DELETE /rendezvous/{id}**
  - Supprime un rendez-vous.
  - **Réponse :**
    - `200 OK` : Rendez-vous supprimé avec succès.
    - `404 Not Found` : Rendez-vous non trouvé.

- **POST /rendezvous/{id}/cancel**
  - Annule un rendez-vous existant.
  - **Réponse :**
    - `200 OK` : Rendez-vous annulé avec succès.
    - `404 Not Found` : Rendez-vous non trouvé.

### 4. Utilisation de l'API USSD

L'API inclut également une interface USSD pour permettre aux utilisateurs d'interagir avec le système via leur téléphone mobile. Les utilisateurs peuvent se connecter, consulter les services, prendre des rendez-vous et annuler des rendez-vous via un menu USSD convivial.

### 5. Configuration et Déploiement

- **Base de données** : MySQL (ou compatible) pour stocker les utilisateurs, services et rendez-vous.
- **Serveur Web** : PHP pour exécuter l'application serveur.
- **Dépendances** : Assurez-vous d'avoir les extensions PHP requises pour MySQLi.

### 6. Exemples de Requêtes

#### Authentification

```bash
curl -X POST http://votre-api/login -d "telephone=1234567890&mot_de_passe=secret&role=patient"

Cette API fournit une solution complète pour la gestion des rendez-vous médicaux, intégrant l'authentification, la gestion des services, et la possibilité de prendre et d'annuler des rendez-vous. Pour toute question ou contribution, n'hésitez pas à contacter les mainteneurs du projet.
