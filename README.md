# 📊 Projet Kaggle - Analyse de Données Urbaines

Une application web PHP complète pour l'analyse et la visualisation de données sur la qualité de vie urbaine, entièrement conteneurisée avec Docker.

## 🎯 Description du Projet

Cette application permet d'analyser des données de qualité de vie de zones urbaines à travers le monde. Elle offre des outils complets pour l'importation, l'édition, l'analyse statistique et la visualisation de données CSV.

### 🎥 Démonstration

<!-- Insérez votre vidéo de démonstration ici -->
*[Vidéo de démonstration à venir]*

## ✨ Fonctionnalités

- 📁 **Import/Export CSV** - Importation et exportation de fichiers de données
- ✏️ **Édition Interactive** - Modification en temps réel des cellules de tableau
- 📊 **Analyses Statistiques** - Calculs de moyenne, médiane, écart-type, min/max
- 📈 **Visualisations** - Génération de graphiques et charts
- ⚡ **Tests de Performance** - Analyse des performances de requêtes sur différentes tailles de datasets
- 🔍 **Recherche et Tri** - Filtrage et tri des données par colonnes
- 🎨 **Interface Responsive** - Design moderne et adaptatif

## 🚀 Installation Rapide

### Prérequis
- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)
- Git

### Étapes d'Installation

1. **Cloner le projet**
   ```bash
   git clone <votre-repository-url>
   cd Projet_KAGGLE
   ```

2. **Configuration de l'environnement**
   ```bash
   cp .env.example .env
   ```

3. **Lancement de l'application**
   ```bash
   docker-compose up -d
   ```

4. **Accès à l'application**
   - 🌐 **Application principale** : http://localhost:8080
   - 🗄️ **phpMyAdmin** : http://localhost:8081

**C'est tout !** L'application est prête à utiliser avec des données pré-chargées.

## 🗃️ Données Incluses

L'application est livrée avec 3 jeux de données sur la qualité de vie urbaine :

- **`uascores`** - Dataset complet avec toutes les villes
- **`uascores_1k`** - Version réduite (1000 entrées) pour tests rapides  
- **`uascores_10k`** - Version intermédiaire (10000 entrées)

### 📋 Colonnes de Données

| Colonne | Description |
|---------|-------------|
| `UA_Name` | Nom de la zone urbaine |
| `UA_Country` | Pays |
| `UA_Continent` | Continent |
| `Housing` | Score logement |
| `Cost of Living` | Coût de la vie |
| `Startups` | Écosystème startup |
| `Venture Capital` | Accès au capital risque |
| `Travel Connectivity` | Connectivité transport |
| `Business Freedom` | Liberté d'entreprise |
| `Safety` | Sécurité |
| `Healthcare` | Soins de santé |
| `Education` | Éducation |
| `Environmental Quality` | Qualité environnementale |
| `Economy` | Économie |
| `Internet Access` | Accès internet |
| `Leisure & Culture` | Loisirs et culture |
| `Tolerance` | Tolérance |
| `Outdoors` | Activités extérieures |

## 🎮 Guide d'Utilisation

### 1. Sélection des Données
- Choisissez un dataset dans la liste déroulante en haut à gauche
- Les données s'affichent automatiquement dans le tableau

### 2. Édition Interactive
- **Cliquez** sur n'importe quelle cellule pour la modifier
- **Appuyez sur Entrée** ou cliquez ailleurs pour sauvegarder
- **Ajoutez des colonnes** avec le bouton "Ajouter une colonne"
- **Ajoutez des lignes** avec le bouton "Ajouter une ligne"

### 3. Analyses Statistiques
- Sélectionnez une colonne dans la liste déroulante
- Choisissez le type de statistique (Moyenne, Médiane, etc.)
- Les résultats s'affichent instantanément

### 4. Visualisations
- Cliquez sur "Afficher les graphiques pour une colonne"
- Sélectionnez la colonne à visualiser
- Les graphiques s'ouvrent dans un nouvel onglet

### 5. Tests de Performance
- Définissez le nombre de simulations
- Cliquez sur "Lancer la simulation"
- Consultez les métriques de performance en temps réel

### 6. Import/Export
- **Import** : Utilisez le bouton "Choisir un fichier" pour importer vos CSV
- **Export** : Cliquez sur "Exporter en CSV" pour télécharger les données

## 🏗️ Architecture Technique

### Services Docker

```yaml
🌐 Web (Port 8080)     - PHP 8.1 + Apache
🗄️ Database (Port 3306) - MySQL 8.0  
🔧 phpMyAdmin (Port 8081) - Administration BDD
```

### Structure des Fichiers

```
📁 Projet_KAGGLE/
├── 🔧 config.php              # Configuration BDD centralisée
├── 🏠 index.php               # Interface principale
├── 📤 import.php              # Import CSV
├── 📥 export.php              # Export CSV
├── 📊 calculate_stats.php     # Calculs statistiques
├── 📈 graphs.php              # Visualisations
├── ⚡ graphsPerformance.php   # Tests de performance
├── 🎨 style.css               # Styles CSS
├── 📁 database/
│   ├── setup.sql              # Initialisation BDD
│   └── *.csv                  # Fichiers de données
├── 🐳 docker-compose.yml      # Configuration Docker
├── 🐳 Dockerfile              # Image web personnalisée
└── ⚙️ .env.example            # Variables d'environnement
```

## 🔧 Configuration Avancée

### Variables d'Environnement (.env)

```env
# Base de données
DB_NAME=kaggle_project
DB_USER=kaggle_user  
DB_PASSWORD=kaggle_password_123
DB_ROOT_PASSWORD=root_password_123

# Ports d'accès
WEB_PORT=8080
PHPMYADMIN_PORT=8081
DB_PORT=3306
```

### Accès à la Base de Données

**Via phpMyAdmin (Recommandé)**
- URL : http://localhost:8081
- Connexion automatique configurée

**Via ligne de commande**
```bash
docker-compose exec db mysql -u kaggle_user -p kaggle_project
```

## 🛠️ Développement

### Logs et Debugging
```bash
# Voir tous les logs
docker-compose logs

# Logs d'un service spécifique  
docker-compose logs web
docker-compose logs db
```

### Modifications du Code
1. Modifiez les fichiers PHP directement
2. Rafraîchissez le navigateur (pas de rebuild nécessaire)
3. Pour les changements de BDD, modifiez `database/setup.sql`

### Redémarrage Propre
```bash
# Arrêt et suppression complète
docker-compose down -v

# Reconstruction et redémarrage
docker-compose up -d --build
```

## 🔒 Sécurité

- ⚠️ **Configuration de développement** - Ne pas utiliser en production avec les mots de passe par défaut
- 🔐 **Changez les mots de passe** dans le fichier `.env` pour un usage en production
- 📁 **Upload de fichiers activé** pour l'import CSV
- 🗄️ **Accès root MySQL** disponible via phpMyAdmin

## 🚨 Dépannage

### Conflits de Ports
Si les ports sont déjà utilisés, modifiez `docker-compose.yml` :
```yaml
ports:
  - "8090:80"  # Au lieu de 8080:80
```

### Problèmes de Connexion BDD
```bash
# Vérifier le statut des conteneurs
docker-compose ps

# Redémarrer un service
docker-compose restart db
```

### Réinitialisation Complète
```bash
# Suppression de tout (données incluses)
docker-compose down -v
docker system prune -a

# Redémarrage propre
docker-compose up -d
```

## 🤝 Contribution

1. 🍴 Forkez le projet
2. 🌿 Créez une branche feature (`git checkout -b feature/AmazingFeature`)
3. 💾 Commitez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. 📤 Poussez vers la branche (`git push origin feature/AmazingFeature`)
5. 🔄 Ouvrez une Pull Request

## 📄 Licence

Distribué sous licence MIT. Voir `LICENSE` pour plus d'informations.

---

## 🙏 Remerciements

- Données de qualité de vie urbaine provenant de diverses sources publiques
- Interface construite avec PHP, MySQL et Chart.js
- Containerisation Docker pour un déploiement simplifié

---

**🚀 Prêt en 3 commandes - Développé avec ❤️ pour l'analyse de données urbaines**