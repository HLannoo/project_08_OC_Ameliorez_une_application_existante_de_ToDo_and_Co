#Contribuer au projet

## Réaliser un fork

Dans un premier temps il est nécessaire créer une copie de ce projet sur votre machine, pour cela
rendez vous sur le repository du projet :
- https://github.com/Kakahuette400/project_08

Dans le coin en haut et à droite de la page, cliquez sur Fork.

## Cloner votre repository

Après avoir “fork” le projet, il faut désormais cloner ce dernier localement via la commande:
- git clone https://github.com/USER_NAME/project_08.git
*Remplacer USER_NAME par votre pseudo github.  

## Installer le projet

Prérequis :
- Dans un premier temps installer composer : https://getcomposer.org/
- Modifier votre "DATABASE_URL=" dans le .env file
- Modifier votre "MAILER_DSN=" dans le .env file
- Installer APCU : http://pecl.php.net/package/APCu/5.1.21/windows

Installer les différentes dépendances avec la commande "composer install" dans votre terminal.


Pour créer la base de données suivez ces instructions :

`Placez ces commandes dans votre terminal, dans l’ordre.`

1 - php bin/console doctrine:database:create
2 - php bin/console doctrine:migrations:migrate
3 - php bin/console doctrine:fixtures:load
Lancer l’application via votre terminal par la commande :
- php -S localhost:8000 -t public


Enfin rendez vous sur :
- http://localhost:8000/login

`Pour vous connecter, 2 choix s’offrent à vous`

Compte Admin : admin-test@gmail.com
Password : password

Compte utilisateur simple - user-test@gmail.com
Password : password

## Gestion des branches

`Dans un premier temps, créer votre nouvelle branche local à l’aide de la commande`
- git checkout -b nom_branche*

*nom de votre nouvelle branche

`Pour pousser votre branche sur votre repository distant veuillez insérer la commande`
- git push origin nom_branche

Enfin pour soumettre votre branche, créer une pull request depuis le répertoire github de votre projet.
Celui-ci sera validé et ajouté au projet originel, si ce dernier respecte certaines conditions**

Attention : N’oubliez pas de mettre à jour régulièrement votre repository en le synchronisant avec
le projet initial.

## Conditions de validation**

Pour toute modification apportée, chaques méthodes devront avoir été testées et validées via PHP
UNIT.

Chaque branche devra respecter une certaine nomenclature “contribution/nom_branche”.

Votre code devra également être soumis à CodeClimate et Codacy est avoir au minimum le rang A,
ceci afin de garder un code respectant les normes essentielles de développement et une maintenabilité
constante du projet.

N.B : Le possesseur de projet originel se réserve le droit d’accepter ou de refuser toutes soumissions
selon si ce dernier respecte ou non la charte de contribution.