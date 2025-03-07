# 🚀 Guide d'installation du projet Reservation

## 📂 Étape 1 : Récupération du projet
Clonez les deux repositories et placez-les dans le même répertoire :

```sh
    # Clonez le backend (Symfony)
    git clone https://github.com/votre-repo/reservation_symfony.git
    
    # Clonez le frontend (Angular)
    git clone https://github.com/votre-repo/reservation_angular.git
```

---

## 🐳 Étape 2 : Accéder au conteneur backend

Accédez au conteneur Docker du backend :
```sh
  docker compose exec -it <nom_du_service> bash
```
Remplacez `<nom_du_service>` par le nom du conteneur backend défini dans `docker-compose.yml`.

---

## 📦 Étape 3 : Installer les dépendances Symfony

Dans le conteneur, exécutez :
```sh
  composer install
```

---

## 📁 Étape 4 : Création du dossier migrations

À la racine du projet Symfony, créez un dossier `migrations` :
```sh
  mkdir migrations
```

---

## 🛠 Étape 5 : Configuration des variables d'environnement

Copiez le fichier `.env.example` et renommez-le en `.env` puis modifiez les valeurs si nécessaire :
```sh
  cp .env.example .env
```

---

## 🔑 Étape 6 : Génération de la clé JWT

Exécutez la commande suivante pour générer la clé JWT :
```sh
  php bin/console lexik:jwt:generate-keypair
```

---

## 🗄 Étape 7 : Génération et exécution des migrations

Exécutez les commandes suivantes pour créer et appliquer les migrations :
```sh
    symfony console make:migration
    symfony console doctrine:migration:migrate  # ou d:m:m
```

---

## 🚀 Étape 8 : Lancer l'application

Vous pouvez maintenant accéder à l'application sur :
🔗 **http://localhost:80** (sauf si vous avez modifié les ports dans `docker-compose.yml`)

Bon développement ! 🎉
