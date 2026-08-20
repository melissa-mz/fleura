# FLEURA — Site E-Commerce

Site e-commerce complet pour Boutique Fleura, magasin de mode féminine à Koléa, Algérie.

## Installation (WAMP)

1. Copiez le dossier `fleura/` dans `C:\wamp64\www\`
2. Démarrez WAMP (Apache + MySQL)
3. Ouvrez phpMyAdmin : `http://localhost/phpmyadmin`
4. Cliquez sur "Importer", sélectionnez le fichier `fleura/database/fleura.sql` et validez
5. Le site est accessible sur : `http://localhost/fleura/`
6. L'administration est sur : `http://localhost/fleura/admin/`

## Compte administrateur

- Email : `admin@fleura.dz`
- Mot de passe : `admin123`

## Structure

```
fleura/
├── index.php              Page d'accueil
├── shop.php               Catalogue / Collection
├── product.php            Page produit
├── cart.php               Panier
├── checkout.php           Commande
├── order-success.php      Confirmation commande
├── about.php              À propos
├── contact.php            Contact
├── admin/                 Espace administration
│   ├── login.php          Connexion
│   ├── dashboard.php      Tableau de bord
│   ├── products.php       Gestion produits
│   ├── add-product.php     Ajouter produit
│   ├── edit-product.php    Modifier produit
│   ├── orders.php         Gestion commandes
│   ├── order-details.php   Détail commande
│   ├── categories.php     Gestion catégories
│   └── logout.php         Déconnexion
├── config/
│   └── database.php       Configuration MySQL
├── includes/
│   ├── header.php          En-tête du site
│   ├── footer.php          Pied de page
│   └── functions.php       Fonctions utilitaires
├── assets/
│   ├── css/style.css       Styles premium
│   └── js/main.js          Animations + effet rideau
└── database/
    └── fleura.sql          Base de données MySQL
```

## Technologies

- PHP 7.4+ (PDO / requêtes préparées)
- MySQL 5.7+ / MariaDB
- HTML5, CSS3, JavaScript
- Compatible WAMP / XAMPP / LAMP

## Fonctionnalités

- Design premium fashion editorial + luxury minimalism
- Effet rideau sur les cartes de catégories (hover)
- Page d'accueil avec hero, collection, nouveautés, éditorial, témoignages, Instagram
- Catalogue avec filtres (catégorie, prix, taille, couleur), tri et pagination
- Page produit avec galerie, options, quantité, onglets
- Panier et commande (livraison domicile/bureau, paiement à la livraison)
- Administration sécurisée (mots de passe hashés, sessions protégées)
- Gestion produits, catégories, commandes avec statuts
- Responsive (mobile, tablette, desktop)

## Informations boutique

- Boutique Fleura — Koléa, Algérie
- Téléphone : 0670 06 37 31
- Note : 4,8/5 (8 avis)
- Paiement : Espèces uniquement
