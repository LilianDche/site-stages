# Web4All – Guide d'installation

## 1. Copier dans htdocs
Copiez le dossier `ProjetWEB` dans `C:\xampp\htdocs\`

## 2. Importer la BDD
phpMyAdmin → Importer → `database.sql`

> **Si votre base existe déjà**, exécutez ces migrations manuellement :
> ```sql
> ALTER TABLE utilisateurs ADD COLUMN IF NOT EXISTS reset_token VARCHAR(64) DEFAULT NULL;
> ALTER TABLE utilisateurs ADD COLUMN IF NOT EXISTS reset_token_expires DATETIME DEFAULT NULL;
> ```

## 3. Configuration
La configuration se trouve dans `php/config.php`.  
- **BDD** : modifiez `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` si besoin.
- **SITE_URL** : détectée automatiquement — aucune modification nécessaire.
- **Google Maps** : remplacez `YOUR_API_KEY` dans `index.php` par votre clé API Maps JavaScript.

## 4. Comptes de test (mot de passe : `password`)
| Email | Rôle |
|---|---|
| admin@web4all.fr | Admin |
| pilote@web4all.fr | Pilote |
| etudiant@web4all.fr | Étudiant |

## 5. URLs
- Site : http://localhost/ProjetWEB/
- Espace pilote : http://localhost/ProjetWEB/pilote/
- Admin : http://localhost/ProjetWEB/admin/

## Structure
```
ProjetWEB/
├── index.php              ← Accueil + liste des offres
├── offre.php              ← Détail d'une offre
├── entreprises.php        ← Liste des entreprises
├── entreprise.php         ← Détail d'une entreprise
├── formulaire.php         ← Candidature
├── profil.php             ← Profil étudiant
├── messages.php           ← Messagerie interne
├── pilote_profil.php      ← Profil public d'un pilote
├── reset-password.php     ← Réinitialisation mot de passe
├── mention_legales.php    ← Mentions légales
├── database.sql           ← Schéma + données de test
├── css/style.css
├── js/script.js
├── uploads/               ← CV et LM (écriture requise)
│   └── .htaccess          ← Bloque l'exécution PHP dans uploads/
├── php/
│   ├── config.php         ← Config BDD, SITE_URL auto, helpers
│   ├── auth.php           ← Login / register / logout / reset password
│   ├── offres.php         ← API offres (lecture publique)
│   ├── entreprises.php    ← API entreprises
│   ├── competences.php    ← API compétences
│   ├── promotions.php     ← API promotions
│   ├── candidatures.php   ← API candidatures étudiant
│   ├── messages.php       ← API messagerie interne
│   ├── upload.php         ← Upload CV/LM (PDF, max 2 Mo)
│   ├── demande_offre.php  ← Soumission offre pilote
│   ├── pilotes.php        ← API pilotes
│   └── modal_auth.php     ← Composant modal connexion/inscription
├── pilote/
│   ├── index.php          ← Espace pilote
│   └── demande.php        ← Soumission nouvelle offre
└── admin/
    ├── index.php          ← Panel admin complet
    └── api.php            ← API admin (tous rôles protégés)
```

## Rôles et accès
| Fonctionnalité | Étudiant | Pilote | Admin |
|---|---|---|---|
| Voir les offres | ✅ | ✅ | ✅ |
| Postuler | ✅ | ❌ | ❌ |
| Messagerie | ✅ | ✅ | ✅ |
| Espace pilote | ❌ | ✅ | ✅ |
| Créer/modifier offres | ❌ | ✅ | ✅ |
| Valider offres | ❌ | ❌ | ✅ |
| Gérer utilisateurs | ❌ | ❌ | ✅ |
| Panel admin complet | ❌ | ❌ | ✅ |

## Sécurité
- Toutes les requêtes SQL utilisent des requêtes préparées PDO (pas d'injection SQL).
- Les fichiers uploadés sont vérifiés par extension ET type MIME réel (`finfo`).
- Le dossier `uploads/` bloque l'exécution PHP via `.htaccess`.
- Les erreurs PHP sont masquées en production, visibles en local.
- Les mots de passe sont hashés avec `PASSWORD_BCRYPT`.
- Les tokens de reset expirent après 1 heure.
- Un email suspendu ne peut pas créer un nouveau compte.
