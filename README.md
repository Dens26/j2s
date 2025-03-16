# Projet J2S – Le site de Sorralli

## 🎲 Présentation
Le site J2S est conçu pour promouvoir les plateformes de la streameuse **Sorralli** tout en offrant une expérience interactive autour des jeux de société. 

🖥️ [Découvrir le site](#) *(ajouter le lien si disponible)*

## 🔎 Fonctionnalités principales
- **Jeu mystère interactif** : Pendant le stream, un administrateur peut afficher un jeu mystère que les spectateurs doivent tenter de découvrir.
- **Mode autonome** : Après le live, le jeu reste accessible aux membres inscrits.
- **Accès aux jeux mystères** :
  - Participer au jeu mystère du moment.
  - Explorer et rejouer les anciens jeux dans la section *Défis de Sorralli*.
- **Visiteurs non inscrits** : accès aux fiches détaillées des jeux de société.

## 📸 Aperçu du site *(ajouter des captures d'écran)*
- Accueil du site
- Fiche jeu
- Jeu mystère en cours
- Tableau de bord admin

## 🚀 Améliorations futures *(en discussion)*
- **Système de score** : accumulation de points selon les performances des joueurs.
- **Classement (Leaderboard)** : mise en avant des meilleurs détectives de la communauté.

## 🛠 Technologies utilisées
- **Symfony** *(version XX.X, préciser)* pour une architecture robuste et sécurisée.
- **Base de données** : *MySQL / SQLite (préciser)*.
- **Twig** pour le rendu des templates.
- **Doctrine ORM** pour la gestion des entités.
- **Sécurité Symfony** : système d'authentification et de gestion des utilisateurs.
- **Bootstrap / TailwindCSS (préciser si utilisé)** pour le design.

## 📦 Installation et configuration
### Prérequis
- PHP `>=8.X`
- Composer `>=2.X`
- Symfony CLI (optionnel mais recommandé)
- Serveur MySQL / SQLite

### Installation
```sh
# Cloner le projet
git clone https://github.com/ton-repo.git
cd ton-repo

# Installer les dépendances
composer install

# Configurer l'environnement
cp .env.example .env
# Modifier le fichier .env avec les paramètres de la base de données

# Création de la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Démarrer le serveur Symfony
symfony server:start
```

## 🔐 Gestion des utilisateurs
- **Rôles disponibles** : `ROLE_USER`, `ROLE_ADMIN`
- Les administrateurs peuvent gérer les jeux et les utilisateurs via un tableau de bord sécurisé.

## 🤝 Contribution
Le projet est ouvert aux contributions ! Pour contribuer :
1. Forker le projet 📌
2. Créer une branche `feature/ma-fonctionnalite`
3. Soumettre une *Pull Request*

## 📜 Licence
Ce projet est sous licence [MIT](LICENSE).

---

🚀 N'hésitez pas à partager vos retours et suggestions !