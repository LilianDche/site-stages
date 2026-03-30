# Web4All v5 – Guide d'installation

## 1. Copier dans htdocs
Copiez le dossier `ProjetWEB_v5` dans `C:\xampp\htdocs\`

## 2. Importer la BDD
phpMyAdmin → Importer → `database.sql`

## 3. Comptes de test (mot de passe : password)
| Email | Rôle |
|---|---|
| admin@web4all.fr | Admin |
| pilote@web4all.fr | Pilote |
| etudiant@web4all.fr | Étudiant |

## 4. URLs
- Site : http://localhost/ProjetWEB_v5/
- Espace pilote : http://localhost/ProjetWEB_v5/pilote/
- Admin : http://localhost/ProjetWEB_v5/admin/

## Structure
```
ProjetWEB_v5/
├── index.php              ← Accueil
├── formulaire.php         ← Candidature
├── profil.php             ← Profil étudiant
├── mention_legales.php    ← Mentions légales
├── database.sql           ← À importer dans phpMyAdmin
├── css/style.css
├── js/script.js
├── php/
│   ├── config.php         ← Config BDD + helpers rôles
│   ├── auth.php           ← API auth (login/register/logout)
│   ├── offres.php         ← API offres
│   ├── entreprises.php    ← API entreprises
│   ├── competences.php    ← API compétences
│   ├── promotions.php     ← API promotions
│   ├── candidatures.php   ← API candidatures
│   └── modal_auth.php     ← Composant modal
├── pilote/
│   └── index.php          ← Espace pilote (offres, entreprises, promos)
└── admin/
    ├── index.php          ← Panel admin complet
    └── api.php            ← API admin
```

## Rôles et accès
| Fonctionnalité | Étudiant | Pilote | Admin |
|---|---|---|---|
| Voir les offres | ✅ | ✅ | ✅ |
| Filtrer par compétence | ✅ | ✅ | ✅ |
| Postuler | ✅ | ❌ | ❌ |
| Espace pilote | ❌ | ✅ | ✅ |
| Créer/modifier offres | ❌ | ✅ | ✅ |
| Gérer entreprises | ❌ | ✅ | ✅ |
| Voir promotions | ❌ | ✅ | ✅ |
| Panel admin complet | ❌ | ❌ | ✅ |
| Gérer rôles | ❌ | ❌ | ✅ |
| Gérer promotions | ❌ | ❌ | ✅ |
