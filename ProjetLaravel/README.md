# Système de Gestion des Émargements

Application web Laravel pour la gestion des émargements dans un établissement d'enseignement. Cette application permet de gérer les présences des étudiants aux cours, avec des fonctionnalités avancées pour les professeurs et les administrateurs.

## Fonctionnalités

### Gestion des Utilisateurs
- Trois rôles : Admin, Professeur, Étudiant
- Gestion complète des profils utilisateurs
- Interface d'administration des utilisateurs

### Gestion des Cours
- Création et planification des cours
- Attribution des salles et des professeurs
- Gestion des emplois du temps

### Gestion des Émargements
- Enregistrement des présences/absences/retards
- Interface intuitive pour les professeurs
- Système de signature pour les étudiants
- Commentaires et justifications

### Statistiques et Rapports
- Tableaux de bord détaillés
- Statistiques par cours, professeur et étudiant
- Graphiques de tendances
- Export des données en PDF et Excel

## Prérequis

- PHP >= 8.1
- Composer
- MySQL ou MariaDB
- Node.js et NPM (pour les assets)
- Extensions PHP requises :
  - BCMath
  - Ctype
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML

## Installation

1. Cloner le dépôt :
```bash
git clone [url-du-depot]
cd [nom-du-projet]
```

2. Installer les dépendances PHP :
```bash
composer install
```

3. Installer les dépendances JavaScript :
```bash
npm install
```

4. Configurer l'environnement :
```bash
cp .env.example .env
php artisan key:generate
```

5. Configurer la base de données dans le fichier `.env`

6. Migrer la base de données :
```bash
php artisan migrate --seed
```

7. Compiler les assets :
```bash
npm run dev
```

## Structure du Projet

- `app/Http/Controllers/` - Contrôleurs de l'application
- `app/Models/` - Modèles Eloquent
- `app/Policies/` - Politiques d'autorisation
- `database/migrations/` - Migrations de base de données
- `database/seeders/` - Données de test
- `resources/views/` - Vues Blade
- `routes/web.php` - Routes de l'application
- `tests/` - Tests unitaires et fonctionnels

## Tests

L'application inclut une suite complète de tests :

```bash
php artisan test
```

## Sécurité

- Authentification complète avec Laravel Breeze
- Autorisations basées sur les rôles
- Protection CSRF
- Validation des données
- Sessions sécurisées

## Dépendances Principales

- Laravel 10.x - Framework PHP
- Laravel Breeze - Authentication
- DomPDF - Génération de PDF
- Maatwebsite Excel - Export Excel
- Chart.js - Visualisation des données

## Contribution

1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commiter vos changements
4. Pousser vers la branche
5. Créer une Pull Request

## License

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.

## Support

Pour toute question ou problème, veuillez ouvrir un ticket dans la section Issues du dépôt.
