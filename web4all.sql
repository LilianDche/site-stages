-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 01 avr. 2026 à 16:59
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `web4all`
--

-- --------------------------------------------------------

--
-- Structure de la table `candidatures`
--

CREATE TABLE `candidatures` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `offre_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `statut` enum('envoyee','vue','acceptee','refusee') NOT NULL DEFAULT 'envoyee',
  `cv_path` varchar(255) DEFAULT NULL,
  `lm_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidatures`
--

INSERT INTO `candidatures` (`id`, `utilisateur_id`, `offre_id`, `message`, `statut`, `cv_path`, `lm_path`, `created_at`) VALUES
(1, 43, 1, 'Je trouve Node.js très intéressant et très utile.', 'acceptee', '43_1_cv_1773830037.pdf', '43_1_lm_1773830037.pdf', '2026-03-18 11:33:57'),
(4, 43, 2, 'RTGNBR', 'refusee', '43_2_cv_1775043069.pdf', '43_2_lm_1775043069.pdf', '2026-04-01 13:31:09'),
(5, 59, 13, 'rthfgn', 'envoyee', '59_13_cv_1775050077.pdf', '59_13_lm_1775050077.pdf', '2026-04-01 15:27:57'),
(7, 43, 17, 'rlskgndriphj', 'envoyee', '43_17_cv_1775052256.pdf', '43_17_lm_1775052256.pdf', '2026-04-01 16:04:16');

-- --------------------------------------------------------

--
-- Structure de la table `competences`
--

CREATE TABLE `competences` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `categorie` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `competences`
--

INSERT INTO `competences` (`id`, `nom`, `categorie`) VALUES
(1, 'PHP', 'Développement'),
(2, 'JavaScript', 'Développement'),
(3, 'React', 'Développement'),
(4, 'Node.js', 'Développement'),
(5, 'Python', 'Développement'),
(6, 'TypeScript', 'Développement'),
(7, 'SQL', 'Base de données'),
(8, 'MySQL', 'Base de données'),
(9, 'PostgreSQL', 'Base de données'),
(10, 'MongoDB', 'Base de données'),
(11, 'Linux', 'Système'),
(12, 'Windows Server', 'Système'),
(13, 'Docker', 'DevOps'),
(14, 'Kubernetes', 'DevOps'),
(15, 'Git', 'DevOps'),
(16, 'CI/CD', 'DevOps'),
(17, 'Ansible', 'DevOps'),
(18, 'Réseau', 'Réseau'),
(19, 'VLAN', 'Réseau'),
(20, 'Firewall', 'Réseau'),
(21, 'VPN', 'Réseau'),
(22, 'Figma', 'Design'),
(23, 'UX/UI', 'Design'),
(24, 'Photoshop', 'Design'),
(25, 'Power BI', 'Data'),
(26, 'Python Data', 'Data'),
(27, 'Machine Learning', 'Data');

-- --------------------------------------------------------

--
-- Structure de la table `entreprises`
--

CREATE TABLE `entreprises` (
  `id` int(11) NOT NULL,
  `nom` varchar(200) NOT NULL,
  `secteur` varchar(150) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `pays` varchar(80) DEFAULT 'France',
  `email_contact` varchar(180) DEFAULT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `nb_stagiaires` int(11) DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `entreprises`
--

INSERT INTO `entreprises` (`id`, `nom`, `secteur`, `adresse`, `ville`, `code_postal`, `pays`, `email_contact`, `telephone`, `site_web`, `description`, `nb_stagiaires`, `created_at`, `updated_at`) VALUES
(1, 'TechNormandie', 'Développement web', NULL, 'Rouen', '76000', 'France', 'contact@technormandie.fr', '02 35 00 11 22', 'https://technormandie.fr', 'Agence web spécialisée React/Node.js. Équipe de 25 développeurs passionnés.', 3, '2026-03-17 23:09:25', '2026-03-17 23:09:25'),
(2, 'InfoServices SAS', 'Infrastructure IT', NULL, 'Le Havre', '76600', 'France', 'rh@infoservices.fr', '02 35 00 33 44', 'https://infoservices.fr', 'Administration systèmes Linux/VMware. Certifiée ISO 27001.', 5, '2026-03-17 23:09:25', '2026-03-17 23:09:25'),
(3, 'CyberNord', 'Cybersécurité', NULL, 'Caen', '14000', 'France', 'stages@cybernord.fr', '02 31 00 55 66', 'https://cybernord.fr', 'Audit, pentest et conseil en sécurité pour les PME du Grand Ouest.', 2, '2026-03-17 23:09:25', '2026-03-17 23:09:25'),
(4, 'CloudFactory', 'DevOps / Cloud', NULL, 'Rouen', '76000', 'France', 'recrutement@cloudfactory.io', '02 35 00 77 88', 'https://cloudfactory.io', 'Plateforme cloud AWS/Azure et automatisation CI/CD pour clients grands comptes.', 4, '2026-03-17 23:09:25', '2026-03-17 23:09:25'),
(5, 'PixelStudio', 'Design UX/UI', NULL, 'Paris', '75009', 'France', 'jobs@pixelstudio.fr', '01 42 00 11 22', 'https://pixelstudio.fr', 'Studio de design produit reconnu, partenaire de startups du CAC 40.', 6, '2026-03-17 23:09:25', '2026-03-17 23:09:25'),
(6, 'DataWave', 'Data / BI', NULL, 'Paris', '75002', 'France', 'hr@datawave.fr', '01 42 00 33 44', 'https://datawave.fr', 'Analyse de données et dashboards BI pour le secteur bancaire et retail.', 3, '2026-03-17 23:09:25', '2026-03-17 23:09:25'),
(7, 'HelpDesk76', 'Support IT', NULL, 'Le Havre', '76600', 'France', 'contact@helpdesk76.fr', '02 35 00 99 00', 'https://helpdesk76.fr', 'Support utilisateurs N1/N2 et gestion de parc informatique multi-sites.', 8, '2026-03-17 23:09:25', '2026-03-17 23:09:25'),
(9, 'NordTech', 'Développement web', NULL, 'Lille', '59000', 'France', 'contact@nordtech.fr', '03 20 00 11 22', 'https://nordtech.fr', 'ESN lilloise spécialisée en développement web et mobile, 80 collaborateurs.', 4, '2026-03-17 23:09:25', '2026-03-17 23:09:25'),
(10, 'InfraLille', 'Infrastructure IT', NULL, 'Lille', '59000', 'France', 'rh@infralille.fr', '03 20 00 33 44', 'https://infralille.fr', 'Hébergement et infogérance pour TPE/PME du Nord-Pas-de-Calais.', 3, '2026-03-17 23:09:25', '2026-03-17 23:09:25'),
(11, 'DataNord', 'Data / BI', NULL, 'Lille', '59000', 'France', 'jobs@datanord.fr', '03 20 00 55 66', 'https://datanord.fr', 'Cabinet de conseil en data science et intelligence artificielle.', 2, '2026-03-17 23:09:25', '2026-03-17 23:09:25'),
(12, 'SecureLille', 'Cybersécurité', NULL, 'Lille', '59100', 'France', 'stages@securelille.fr', '03 20 00 77 88', 'https://securelille.fr', 'SOC et réponse à incident pour le secteur industriel du Nord.', 1, '2026-03-17 23:09:25', '2026-03-17 23:09:25'),
(13, 'StartupNord – Lille', NULL, NULL, NULL, NULL, 'France', NULL, NULL, NULL, NULL, 0, '2026-03-18 02:44:06', '2026-03-18 02:44:06');

-- --------------------------------------------------------

--
-- Structure de la table `etudiant_competences`
--

CREATE TABLE `etudiant_competences` (
  `utilisateur_id` int(11) NOT NULL,
  `competence_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `etudiant_competences`
--

INSERT INTO `etudiant_competences` (`utilisateur_id`, `competence_id`) VALUES
(8, 1),
(8, 2),
(8, 8),
(9, 2),
(9, 3),
(9, 6),
(10, 11),
(10, 15),
(10, 18);

-- --------------------------------------------------------

--
-- Structure de la table `offres`
--

CREATE TABLE `offres` (
  `id` int(11) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `entreprise_id` int(11) NOT NULL,
  `lieu` varchar(100) NOT NULL,
  `type` enum('stage','alternance') NOT NULL,
  `description` text NOT NULL,
  `duree` varchar(80) DEFAULT NULL,
  `remuneration` varchar(80) DEFAULT NULL,
  `lat` decimal(9,6) DEFAULT NULL,
  `lng` decimal(9,6) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `statut_validation` enum('validee','en_attente','refusee') NOT NULL DEFAULT 'validee',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `offres`
--

INSERT INTO `offres` (`id`, `titre`, `entreprise_id`, `lieu`, `type`, `description`, `duree`, `remuneration`, `lat`, `lng`, `actif`, `statut_validation`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Stage Développeur Web Full-Stack', 1, 'Rouen', 'stage', 'Rejoignez notre équipe Agile pour développer des applications web modernes en React et Node.js. Participation aux daily stand-ups, code reviews et démos clients.', '6 mois', '11560 €/mois', 49.443100, 1.099300, 1, 'validee', 2, '2026-03-17 23:09:26', '2026-04-01 16:11:22'),
(2, 'Stage Développeur Front-End React', 1, 'Rouen', 'stage', 'Développez des composants React pour nos clients e-commerce avec TypeScript, Redux et des outils de test (Jest, Cypress). Équipe de 8 développeurs en méthode Scrum.', '3 mois', '570 €/mois', 49.443100, 1.099300, 1, 'validee', 2, '2026-03-17 23:09:26', '2026-03-17 23:09:26'),
(3, 'Stage QA / Testeur Logiciel', 1, 'Rouen', 'stage', 'Rédigez et exécutez des plans de tests (fonctionnels, non-régressifs, de performance). Automatisez les tests avec Cypress et Playwright. Collaboration quotidienne avec les développeurs.', '6 mois', '560 €/mois', 49.443100, 1.099300, 1, 'validee', 2, '2026-03-17 23:09:26', '2026-03-17 23:09:26'),
(4, 'Stage Intégrateur Web HTML/CSS/JS', 1, 'Rouen', 'stage', 'Intégrez des maquettes Figma en pages web responsive pour des projets e-commerce, en binôme avec un développeur senior. Maîtrise du CSS et de JavaScript attendue.', '3 mois', '560 €/mois', 49.443100, 1.099300, 1, 'validee', 52, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(5, 'Alternance Développeur Full-Stack', 1, 'Rouen', 'alternance', 'Développez des applications web complètes en React/Node.js de la conception au déploiement. Formation aux bonnes pratiques CI/CD incluse.', '2 ans', '950 €/mois', 49.443100, 1.099300, 1, 'validee', 52, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(6, 'Alternance Développeur Back-End PHP', 1, 'Rouen', 'alternance', 'Développez et maintenez nos APIs REST en PHP/Symfony. Revues de code et mises en production hebdomadaires sur AWS, participation aux choix d\'architecture.', '2 ans', '920 €/mois', 49.443100, 1.099300, 1, 'validee', 52, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(7, 'Alternance Administrateur Systèmes Linux', 2, 'Le Havre', 'alternance', 'Gérez notre infrastructure Linux (70 serveurs) : supervision Zabbix, automatisation Ansible, mise à jour des procédures ITIL. Formation certifiante Red Hat prévue.', '2 ans', '900 €/mois', 49.494400, 0.107900, 1, 'validee', 3, '2026-03-17 23:09:26', '2026-03-17 23:09:26'),
(8, 'Stage Technicien Systèmes & Réseaux', 2, 'Le Havre', 'stage', 'Administrez notre parc de serveurs Linux et Windows Server : Active Directory, supervision Nagios, déploiement de patchs, rédaction de procédures. Environnement multi-sites.', '4 mois', '560 €/mois', 49.494400, 0.107900, 1, 'validee', 3, '2026-03-17 23:09:26', '2026-03-17 23:09:26'),
(9, 'Stage Administrateur Virtualisation', 2, 'Le Havre', 'stage', 'Gérez notre environnement VMware vSphere (200 VMs) : création de VMs, snapshots, migrations vMotion, monitoring des performances. Formation VMware VCP proposée en fin de stage.', '6 mois', '570 €/mois', 49.494400, 0.107900, 1, 'validee', 3, '2026-03-17 23:09:26', '2026-03-17 23:09:26'),
(10, 'Stage Administrateur Réseaux Cisco', 2, 'Le Havre', 'stage', 'Configurez switches Cisco, mettez en place des VLANs et rédigez la documentation réseau pour 3 sites clients. Encadré par un ingénieur CCNP.', '4 mois', '560 €/mois', 49.494400, 0.107900, 1, 'validee', 54, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(11, 'Alternance Ingénieur Systèmes Linux', 2, 'Le Havre', 'alternance', 'Gérez l\'infrastructure Linux de nos 120 clients PME. Automatisez les tâches récurrentes avec Ansible et Python. Participez à la migration cloud hybride (Azure + on-premise).', '2 ans', '980 €/mois', 49.494400, 0.107900, 1, 'validee', 54, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(12, 'Stage Réseau & Sécurité', 3, 'Caen', 'stage', 'Participez à des missions d\'audit réseau et de configuration de firewalls pfSense. Rédigez des rapports techniques et proposez des plans de remédiation à nos clients PME.', '4 mois', '560 €/mois', 49.182900, -0.370700, 1, 'validee', 4, '2026-03-17 23:09:26', '2026-03-17 23:09:26'),
(13, 'Stage Pentester Junior', 3, 'Caen', 'stage', 'Réalisez des tests d\'intrusion sur des applications web et des infrastructures réseau. Rédigez des rapports détaillés et présentez vos résultats. Maîtrise de Kali Linux souhaitée.', '6 mois', '600 €/mois', 49.182900, -0.370700, 1, 'validee', 4, '2026-03-17 23:09:26', '2026-03-17 23:09:26'),
(14, 'Stage Analyste SOC – Splunk', 3, 'Caen', 'stage', 'Analysez les alertes remontées par notre SIEM Splunk. Qualifiez les incidents, rédigez les rapports et proposez des mesures de remédiation pour nos clients PME.', '6 mois', '580 €/mois', 49.182900, -0.370700, 1, 'validee', 4, '2026-03-17 23:09:26', '2026-03-17 23:09:26'),
(15, 'Stage Analyste Malware', 3, 'Caen', 'stage', 'Analysez des échantillons de malware dans un environnement sandboxé. Rédigez des rapports d\'analyse et proposez des règles de détection YARA/Sigma. Encadré par un expert OSCP.', '4 mois', '580 €/mois', 49.182900, -0.370700, 1, 'validee', 55, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(16, 'Alternance Consultant Cybersécurité', 3, 'Caen', 'alternance', 'Accompagnez nos clients PME : audits ISO 27001, analyse de risques, rédaction de PSSI. Certification CISSP ou CISM financée par l\'entreprise.', '2 ans', '900 €/mois', 49.182900, -0.370700, 1, 'validee', 55, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(17, 'Alternance DevOps Cloud', 4, 'Rouen', 'alternance', 'Intégrez notre squad DevOps : pipelines GitLab CI/CD, orchestration Docker/Kubernetes sur AWS, scripts Terraform. Astreinte et post-mortems d\'incidents inclus.', '2 ans', '1000 €/mois', 49.443100, 1.099300, 1, 'validee', 44, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(18, 'Stage Ingénieur DevOps', 4, 'Rouen', 'stage', 'Mettez en place des pipelines CI/CD sur GitLab, gérez des conteneurs Docker et des clusters Kubernetes. Rédigez des modules Terraform pour le provisionnement AWS.', '6 mois', '600 €/mois', 49.443100, 1.099300, 1, 'validee', 44, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(19, 'Stage Développeur Infrastructure as Code', 4, 'Rouen', 'stage', 'Développez et maintenez nos modules Terraform et Ansible. Documentez les architectures cloud et participez aux revues de code infrastructure. Environnement AWS multi-comptes.', '4 mois', '570 €/mois', 49.443100, 1.099300, 1, 'validee', 56, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(20, 'Alternance SRE – Site Reliability Engineer', 4, 'Rouen', 'alternance', 'Assurez la fiabilité de nos plateformes cloud (SLA 99,9%). SLO/SLI, runbooks automatisés, astreintes et post-mortems. Stack : Prometheus, Grafana, PagerDuty.', '2 ans', '1050 €/mois', 49.443100, 1.099300, 1, 'validee', 56, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(21, 'Stage UX/UI Designer', 5, 'Paris', 'stage', 'Créez des interfaces mémorables pour nos clients startups : wireframes, prototypes Figma, tests utilisateurs. Participation aux ateliers de co-design et aux présentations clients.', '6 mois', '600 €/mois', 48.856600, 2.352200, 1, 'validee', 5, '2026-03-17 23:09:26', '2026-03-17 23:09:26'),
(22, 'Stage Designer UI – Applications Mobiles', 5, 'Paris', 'stage', 'Concevez les interfaces de nos applications mobiles iOS/Android. Créez des design systems cohérents, réalisez des tests A/B et itérez avec les développeurs React Native. Portfolio obligatoire.', '3 mois', '610 €/mois', 48.856600, 2.352200, 1, 'validee', 5, '2026-03-17 23:09:26', '2026-03-17 23:09:26'),
(23, 'Stage Designer Graphique & Brand', 5, 'Paris', 'stage', 'Créez des supports de communication (identité visuelle, charte graphique, print et digital). Maîtrise d\'Illustrator et Photoshop requise. Figma est un plus.', '6 mois', '590 €/mois', 48.856600, 2.352200, 1, 'validee', 5, '2026-03-17 23:09:26', '2026-03-17 23:09:26'),
(24, 'Stage Product Designer – SaaS', 5, 'Paris', 'stage', 'Concevez l\'UX de nouvelles fonctionnalités pour nos clients SaaS. Entretiens utilisateurs, prototypes Figma et itérations avec les équipes dev. Portfolio requis.', '6 mois', '620 €/mois', 48.856600, 2.352200, 1, 'validee', 53, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(25, 'Alternance UX Researcher', 5, 'Paris', 'alternance', 'Menez des recherches utilisateurs (entretiens, tests d\'usabilité, analytics). Transformez les insights en recommandations design sur des produits SaaS avec 50 000+ utilisateurs actifs.', '2 ans', '920 €/mois', 48.856600, 2.352200, 1, 'validee', 53, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(26, 'Alternance Motion Designer', 5, 'Paris', 'alternance', 'Créez des animations pour les interfaces produit de nos clients. Stack : After Effects, Lottie, Figma. Collaboration directe avec les équipes produit et marketing.', '2 ans', '900 €/mois', 48.856600, 2.352200, 1, 'validee', 53, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(27, 'Alternance Data Analyst', 6, 'Paris', 'alternance', 'Analysez les données de nos clients retail et bancaires. Construisez des dashboards Power BI, automatisez des ETL Python et présentez vos résultats au COMEX. Accès aux datasets réels dès J+1.', '2 ans', '950 €/mois', 48.856600, 2.352200, 1, 'validee', 45, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(28, 'Stage Data Engineer', 6, 'Paris', 'stage', 'Construisez et maintenez nos pipelines ETL avec Python et Apache Airflow. Alimentez notre data warehouse Snowflake et optimisez les requêtes SQL pour nos clients retail.', '6 mois', '620 €/mois', 48.856600, 2.352200, 1, 'validee', 45, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(29, 'Stage Business Analyst – BI & Reporting', 6, 'Paris', 'stage', 'Analysez les données clients et construisez des tableaux de bord Power BI et Tableau. Rédigez les spécifications fonctionnelles et animez les comités de pilotage.', '4 mois', '600 €/mois', 48.856600, 2.352200, 1, 'validee', 57, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(30, 'Alternance Machine Learning Engineer', 6, 'Paris', 'alternance', 'Développez et déployez des modèles ML en production. Stack : Python, scikit-learn, TensorFlow, MLflow. Cas d\'usage réels : recommandation, prédiction de churn, détection de fraude.', '2 ans', '1000 €/mois', 48.856600, 2.352200, 1, 'validee', 57, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(31, 'Stage Technicien Support N2', 7, 'Le Havre', 'stage', 'Traitez les tickets N2 (réseau, OS, applicatif) sur notre plateforme Jira. Astreinte hebdomadaire et rédaction de procédures pour notre base de connaissance.', '3 mois', '560 €/mois', 49.494400, 0.107900, 1, 'validee', 46, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(32, 'Stage Technicien Support N1/N2', 7, 'Le Havre', 'stage', 'Traitez les tickets utilisateurs via ServiceNow. Diagnostiquez et résolvez les incidents matériels, logiciels et réseau. Rédigez les procédures pour la base de connaissance.', '3 mois', '560 €/mois', 49.494400, 0.107900, 1, 'validee', 46, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(33, 'Alternance Responsable Parc Informatique', 7, 'Le Havre', 'alternance', 'Gérez un parc de 500 postes (déploiement SCCM, imaging, inventaire). Migration Windows 11 et M365. Encadrement de 2 techniciens junior en fin d\'alternance.', '2 ans', '870 €/mois', 49.494400, 0.107900, 1, 'validee', 46, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(38, 'Stage Développeur Web – Symfony/Vue.js', 9, 'Lille', 'stage', 'Participez au développement de notre CRM interne en Symfony 6 et Vue.js 3. Équipe de 8 développeurs, méthode Scrum avec des sprints de 2 semaines.', '6 mois', '580 €/mois', 50.629200, 3.057300, 1, 'validee', 6, '2026-03-17 23:09:26', '2026-03-17 23:09:26'),
(39, 'Stage Développeur Back-End Node.js', 9, 'Lille', 'stage', 'Développez les APIs REST de notre plateforme SaaS B2B en Node.js/Express. Tests Jest, documentation Swagger, code reviews hebdomadaires.', '4 mois', '575 €/mois', 50.629200, 3.057300, 1, 'validee', 6, '2026-03-17 23:09:26', '2026-03-17 23:09:26'),
(40, 'Stage Développeur Vue.js', 9, 'Lille', 'stage', 'Créez des interfaces dynamiques en Vue.js 3 pour notre outil de gestion de projet. Composition API, Pinia, Vue Router. Collaboration quotidienne avec l\'équipe back-end.', '3 mois', '565 €/mois', 50.629200, 3.057300, 1, 'validee', 6, '2026-03-17 23:09:26', '2026-03-17 23:09:26'),
(41, 'Stage Développeur Full-Stack Node.js', 9, 'Lille', 'stage', 'Développez de nouvelles fonctionnalités sur notre plateforme B2B en Node.js/React. Sprints Agile, tests unitaires et documentation technique.', '6 mois', '590 €/mois', 50.629200, 3.057300, 1, 'validee', 50, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(42, 'Alternance Développeur PHP Symfony', 9, 'Lille', 'alternance', 'Contribuez au développement de notre ERP interne en Symfony 6. Migrations BDD, services métier, tests unitaires et d\'intégration. Environnement Docker/PostgreSQL.', '2 ans', '940 €/mois', 50.629200, 3.057300, 1, 'validee', 50, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(43, 'Alternance Ingénieur Cloud AWS', 9, 'Lille', 'alternance', 'Gérez notre infrastructure AWS avec Terraform. Monitoring CloudWatch, optimisation des coûts et politique de sécurité cloud.', '2 ans', '1050 €/mois', 50.629200, 3.057300, 1, 'validee', 50, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(44, 'Alternance Technicien Infogérance', 10, 'Lille', 'alternance', 'Gérez le parc de 200 postes clients. Missions : déploiement SCCM, gestion Active Directory, supervision Centreon. Formation ITIL Foundation offerte.', '2 ans', '880 €/mois', 50.629200, 3.057300, 1, 'validee', 47, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(45, 'Stage Administrateur Cloud Azure', 10, 'Lille', 'stage', 'Gérez et optimisez notre infrastructure Azure (VMs, AKS, Azure AD). Politiques de sécurité et scripts PowerShell. Préparation AZ-900 offerte.', '6 mois', '580 €/mois', 50.629200, 3.057300, 1, 'validee', 47, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(46, 'Alternance Ingénieur Réseau & Sécurité', 10, 'Lille', 'alternance', 'Gérez l\'infrastructure réseau de nos 80 clients PME (Cisco, Fortinet). Solutions SD-WAN, VPNs IPSEC et audits de sécurité trimestriels.', '2 ans', '890 €/mois', 50.629200, 3.057300, 1, 'validee', 47, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(47, 'Stage Data Scientist – IA / ML', 11, 'Lille', 'stage', 'Travaillez sur des modèles de machine learning pour la prédiction de churn client. Stack : Python, scikit-learn, TensorFlow, MLflow, Jupyter. Encadré par un PhD en IA.', '6 mois', '620 €/mois', 50.629200, 3.057300, 1, 'validee', 48, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(48, 'Stage Data Scientist – NLP', 11, 'Lille', 'stage', 'Développez des modèles de traitement du langage naturel pour l\'analyse de sentiment client. Stack : Python, HuggingFace Transformers, spaCy, FastAPI. Données réelles dès J+1.', '6 mois', '615 €/mois', 50.629200, 3.057300, 1, 'validee', 48, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(49, 'Alternance Data Analyst – E-commerce', 11, 'Lille', 'alternance', 'Analysez les données de vente de nos clients e-commerce (CA 10M€+). Modèles prédictifs, reportings automatisés et présentations COMEX chaque semaine.', '2 ans', '960 €/mois', 50.629200, 3.057300, 1, 'validee', 48, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(50, 'Stage Analyste Cybersécurité / SOC', 12, 'Lille', 'stage', 'Intégrez notre SOC 24/7 et analysez les alertes SIEM Splunk. Simulations d\'attaques (purple team), rédaction de playbooks, formation CompTIA Security+.', '4 mois', '570 €/mois', 50.627000, 3.062000, 1, 'validee', 49, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(51, 'Stage Ingénieur Sécurité Cloud', 12, 'Lille', 'stage', 'Auditez la sécurité des environnements cloud de nos clients industriels (AWS, Azure, GCP). Rédigez des plans de remédiation et accompagnez les équipes dans leur mise en œuvre.', '4 mois', '590 €/mois', 50.627000, 3.062000, 1, 'validee', 49, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(52, 'Alternance Architecte Sécurité', 12, 'Lille', 'alternance', 'Définissez et pilotez la stratégie de sécurité de nos clients grand compte. Zero Trust, SASE, SOAR. Certification OSCP ou CEH financée par l\'entreprise.', '2 ans', '1020 €/mois', 50.627000, 3.062000, 1, 'validee', 49, '2026-03-17 23:09:26', '2026-03-18 10:48:09'),
(53, 'Alternance Développeur PHP/Laravel', 9, 'Lille', 'alternance', 'Développez de nouvelles fonctionnalités sur notre plateforme SaaS en PHP/Laravel. TDD, revue de code et déploiement continu inclus.', '2 ans', '920 €/mois', 50.629200, 3.057300, 1, 'validee', 51, '2026-03-17 23:09:26', '2026-03-18 10:48:09');

-- --------------------------------------------------------

--
-- Structure de la table `offre_competences`
--

CREATE TABLE `offre_competences` (
  `offre_id` int(11) NOT NULL,
  `competence_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `offre_competences`
--

INSERT INTO `offre_competences` (`offre_id`, `competence_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 8),
(2, 2),
(2, 3),
(2, 6),
(2, 8),
(3, 2),
(3, 6),
(3, 15),
(4, 2),
(4, 3),
(4, 22),
(5, 1),
(5, 2),
(5, 3),
(5, 4),
(5, 15),
(6, 1),
(6, 8),
(6, 15),
(7, 11),
(7, 12),
(7, 15),
(7, 17),
(8, 11),
(8, 12),
(8, 18),
(9, 11),
(9, 15),
(10, 15),
(10, 18),
(10, 19),
(11, 5),
(11, 11),
(11, 15),
(11, 17),
(12, 18),
(12, 20),
(12, 21),
(13, 11),
(13, 18),
(13, 20),
(14, 11),
(14, 18),
(14, 20),
(15, 11),
(15, 18),
(15, 20),
(16, 18),
(16, 20),
(16, 21),
(17, 13),
(17, 14),
(17, 15),
(17, 16),
(18, 13),
(18, 14),
(18, 15),
(18, 16),
(19, 13),
(19, 15),
(19, 17),
(20, 13),
(20, 14),
(20, 16),
(21, 22),
(21, 23),
(22, 22),
(22, 23),
(23, 22),
(23, 24),
(24, 22),
(24, 23),
(25, 22),
(25, 23),
(26, 22),
(26, 24),
(27, 5),
(27, 8),
(27, 25),
(28, 5),
(28, 7),
(28, 25),
(29, 7),
(29, 8),
(29, 25),
(30, 5),
(30, 26),
(30, 27),
(31, 8),
(31, 11),
(31, 12),
(32, 8),
(32, 11),
(32, 12),
(33, 11),
(33, 12),
(33, 15),
(38, 1),
(38, 2),
(38, 8),
(39, 2),
(39, 4),
(39, 15),
(40, 2),
(40, 3),
(41, 2),
(41, 3),
(41, 4),
(42, 1),
(42, 9),
(42, 15),
(43, 13),
(43, 15),
(43, 16),
(44, 11),
(44, 12),
(44, 15),
(45, 11),
(45, 12),
(45, 13),
(46, 18),
(46, 19),
(46, 20),
(46, 21),
(47, 5),
(47, 26),
(47, 27),
(48, 5),
(48, 26),
(48, 27),
(49, 8),
(49, 25),
(49, 26),
(50, 11),
(50, 18),
(50, 20),
(51, 13),
(51, 20),
(51, 21),
(52, 13),
(52, 18),
(52, 20),
(52, 21),
(53, 1),
(53, 8),
(53, 15);

-- --------------------------------------------------------

--
-- Structure de la table `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `annee` year(4) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `promotions`
--

INSERT INTO `promotions` (`id`, `nom`, `annee`, `description`, `created_at`) VALUES
(1, 'BTS SIO SLAM 2025', '2025', 'Solutions Logicielles et Applications Métiers', '2026-03-17 23:09:25'),
(2, 'BTS SIO SISR 2025', '2025', 'Solutions Infrastructure, Systèmes et Réseaux', '2026-03-17 23:09:25'),
(3, 'LP DevWeb 2025', '2025', 'Licence Professionnelle Développement Web', '2026-03-17 23:09:25');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `prenom` varchar(80) NOT NULL,
  `nom` varchar(80) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('etudiant','pilote','admin') NOT NULL DEFAULT 'etudiant',
  `promotion_id` int(11) DEFAULT NULL,
  `entreprise_id` int(11) DEFAULT NULL,
  `nom_entreprise_demande` varchar(200) DEFAULT NULL,
  `statut` enum('actif','en_attente','suspendu') NOT NULL DEFAULT 'actif',
  `cv_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `prenom`, `nom`, `email`, `password`, `role`, `promotion_id`, `entreprise_id`, `nom_entreprise_demande`, `statut`, `cv_path`, `created_at`, `updated_at`, `reset_token`, `reset_token_expires`) VALUES
(1, 'Admin', 'Web4All', 'admin@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(2, 'Thomas', 'Leroy', 'pilote.rouen@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 1, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(3, 'Julie', 'Moreau', 'pilote.havre@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 2, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(4, 'Marc', 'Petit', 'pilote.caen@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 3, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(5, 'Sophie', 'Bernard', 'pilote.paris@web4all.fr', '$2y$10$IRwxfnhkWnPb4j3A0yu1JOzbReXjSEP5DP7N7AxYwyr1Oao21DZ1C', 'pilote', NULL, 5, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-31 15:36:21', NULL, NULL),
(6, 'Kevin', 'Durand', 'pilote.lille@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 9, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(7, 'Laura', 'Simon', 'pilote.attente@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 13, 'StartupNord – Lille', 'actif', NULL, '2026-03-17 23:09:26', '2026-03-18 02:44:06', NULL, NULL),
(8, 'Lucas', 'Martin', 'etudiant1@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(9, 'Emma', 'Dubois', 'etudiant2@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(10, 'Noah', 'Fontaine', 'etudiant3@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(11, 'Chloé', 'Laurent', 'chloe.laurent@bts.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(12, 'Hugo', 'Moreau', 'hugo.moreau@bts.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(13, 'Jade', 'Petit', 'jade.petit@bts.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(14, 'Théo', 'Simon', 'theo.simon@bts.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(15, 'Léa', 'Bernard', 'lea.bernard@bts.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(16, 'Antoine', 'Dupont', 'antoine.dupont@bts.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(17, 'Manon', 'Richard', 'manon.richard@bts.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(18, 'Romain', 'Thomas', 'romain.thomas@bts.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(19, 'Camille', 'Garcia', 'camille.garcia@bts.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(20, 'Louis', 'Robert', 'louis.robert@sisr.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 2, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(21, 'Sarah', 'Martinez', 'sarah.martinez@sisr.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 2, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(22, 'Enzo', 'Lopez', 'enzo.lopez@sisr.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 2, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(23, 'Inès', 'Gonzalez', 'ines.gonzalez@sisr.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 2, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(24, 'Axel', 'Wilson', 'axel.wilson@sisr.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 2, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(25, 'Zoé', 'Anderson', 'zoe.anderson@sisr.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 2, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(26, 'Nathan', 'Taylor', 'nathan.taylor@sisr.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 2, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(27, 'Pauline', 'Moore', 'pauline.moore@sisr.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 2, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(28, 'Alexis', 'Jackson', 'alexis.jackson@sisr.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 2, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(29, 'Clara', 'White', 'clara.white@sisr.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 2, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(30, 'Maxime', 'Harris', 'maxime.harris@sisr.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 2, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(31, 'Eva', 'Martin', 'eva.martin@lp.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 3, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(32, 'Pierre', 'Davies', 'pierre.davies@lp.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 3, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(33, 'Alice', 'Evans', 'alice.evans@lp.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 3, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(34, 'Victor', 'Turner', 'victor.turner@lp.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 3, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(35, 'Lucie', 'Collins', 'lucie.collins@lp.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 3, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(36, 'Florian', 'Stewart', 'florian.stewart@lp.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 3, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(37, 'Anaïs', 'Morris', 'anais.morris@lp.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 3, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(38, 'Dylan', 'Rogers', 'dylan.rogers@lp.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 3, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(39, 'Océane', 'Reed', 'oceane.reed@lp.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 3, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(40, 'Baptiste', 'Cook', 'baptiste.cook@lp.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 3, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(41, 'Ambre', 'Bell', 'ambre.bell@lp.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 3, NULL, NULL, 'actif', NULL, '2026-03-17 23:09:26', '2026-03-17 23:09:26', NULL, NULL),
(42, 'El', 'Piloto', 'pilote.test@web4all.fr', '$2y$10$/NMch1F.JCRj6vQVA321me9c8D1fxm4WJa.BttT3ezuqh6a556IPW', 'pilote', NULL, NULL, NULL, 'actif', NULL, '2026-03-18 02:37:28', '2026-03-18 02:44:02', NULL, NULL),
(43, 'Lucas', 'Beye', 'lucas.beye@viacesi.fr', '$2y$10$IICSlPvEnbn3FiJrRFqQI.uXsbavroruKGZZbXuM4NxBIUl.onJxa', 'etudiant', NULL, NULL, NULL, 'actif', NULL, '2026-03-18 11:31:12', '2026-03-18 11:34:25', NULL, NULL),
(44, 'Alexandre', 'Dupuis', 'pilote.cloudfactory@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 4, NULL, 'actif', NULL, '2026-03-18 10:48:09', '2026-03-18 10:48:09', NULL, NULL),
(45, 'Nathalie', 'Girard', 'pilote.datawave@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 6, NULL, 'actif', NULL, '2026-03-18 10:48:09', '2026-03-18 10:48:09', NULL, NULL),
(46, 'Frédéric', 'Legrand', 'pilote.helpdesk76@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 7, NULL, 'actif', NULL, '2026-03-18 10:48:09', '2026-03-18 10:48:09', NULL, NULL),
(47, 'Isabelle', 'Fontaine', 'pilote.infralille@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 10, NULL, 'actif', NULL, '2026-03-18 10:48:09', '2026-03-18 10:48:09', NULL, NULL),
(48, 'Sébastien', 'Perrin', 'pilote.datanord@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 11, NULL, 'actif', NULL, '2026-03-18 10:48:09', '2026-03-18 10:48:09', NULL, NULL),
(49, 'Céline', 'Rousseau', 'pilote.securelille@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 12, NULL, 'actif', NULL, '2026-03-18 10:48:09', '2026-03-18 10:48:09', NULL, NULL),
(50, 'Clément', 'Bouvier', 'pilote.nordtech2@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 9, NULL, 'actif', NULL, '2026-03-18 10:48:09', '2026-03-18 10:48:09', NULL, NULL),
(51, 'Marine', 'Carpentier', 'pilote.nordtech3@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 9, NULL, 'actif', NULL, '2026-03-18 10:48:09', '2026-03-18 10:48:09', NULL, NULL),
(52, 'Pauline', 'Renard', 'pilote.technormandie2@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 1, NULL, 'actif', NULL, '2026-03-18 10:48:09', '2026-03-18 10:48:09', NULL, NULL),
(53, 'Damien', 'Morel', 'pilote.pixelstudio2@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 5, NULL, 'actif', NULL, '2026-03-18 10:48:09', '2026-03-18 10:48:09', NULL, NULL),
(54, 'Caroline', 'Leblanc', 'pilote.infoservices2@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 2, NULL, 'actif', NULL, '2026-03-18 10:48:09', '2026-03-18 10:48:09', NULL, NULL),
(55, 'Julien', 'Marchand', 'pilote.cybernord2@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 3, NULL, 'actif', NULL, '2026-03-18 10:48:09', '2026-03-18 10:48:09', NULL, NULL),
(56, 'Hélène', 'Garnier', 'pilote.cloudfactory2@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 4, NULL, 'actif', NULL, '2026-03-18 10:48:09', '2026-03-18 10:48:09', NULL, NULL),
(57, 'Thibault', 'Faure', 'pilote.datawave2@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, 6, NULL, 'actif', NULL, '2026-03-18 10:48:09', '2026-03-18 10:48:09', NULL, NULL),
(58, 'Amandine', 'Cousin', 'pilote.appfactory2@web4all.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote', NULL, NULL, NULL, 'actif', NULL, '2026-03-18 10:48:09', '2026-03-18 10:48:09', NULL, NULL),
(59, 'Lionel', 'Dassi', 'lionneldassi1@gmail.com', '$2y$10$wd4.PwwydhG8dnNCebW/r.ibfjXDHnjYg5VYGy.zvwKcvwLg95I9a', 'etudiant', 2, NULL, NULL, 'actif', NULL, '2026-04-01 15:18:22', '2026-04-01 15:18:22', NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_candidature` (`utilisateur_id`,`offre_id`),
  ADD KEY `offre_id` (`offre_id`);

--
-- Index pour la table `competences`
--
ALTER TABLE `competences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `entreprises`
--
ALTER TABLE `entreprises`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `etudiant_competences`
--
ALTER TABLE `etudiant_competences`
  ADD PRIMARY KEY (`utilisateur_id`,`competence_id`),
  ADD KEY `competence_id` (`competence_id`);

--
-- Index pour la table `offres`
--
ALTER TABLE `offres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entreprise_id` (`entreprise_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Index pour la table `offre_competences`
--
ALTER TABLE `offre_competences`
  ADD PRIMARY KEY (`offre_id`,`competence_id`),
  ADD KEY `competence_id` (`competence_id`);

--
-- Index pour la table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `promotion_id` (`promotion_id`),
  ADD KEY `entreprise_id` (`entreprise_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `competences`
--
ALTER TABLE `competences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `entreprises`
--
ALTER TABLE `entreprises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `offres`
--
ALTER TABLE `offres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT pour la table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD CONSTRAINT `candidatures_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidatures_ibfk_2` FOREIGN KEY (`offre_id`) REFERENCES `offres` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `etudiant_competences`
--
ALTER TABLE `etudiant_competences`
  ADD CONSTRAINT `etudiant_competences_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `etudiant_competences_ibfk_2` FOREIGN KEY (`competence_id`) REFERENCES `competences` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `offres`
--
ALTER TABLE `offres`
  ADD CONSTRAINT `offres_ibfk_1` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offres_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `offre_competences`
--
ALTER TABLE `offre_competences`
  ADD CONSTRAINT `offre_competences_ibfk_1` FOREIGN KEY (`offre_id`) REFERENCES `offres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offre_competences_ibfk_2` FOREIGN KEY (`competence_id`) REFERENCES `competences` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `utilisateurs_ibfk_2` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
