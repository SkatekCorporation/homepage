# Web-server-home-page
Mettez le dans votre Web Local puis explorer la liste de vos dossier à la racine ainsi que les fichiers.
Parcourez toutes les dossiers ci ceux-ci n'ont pas un fichier index.php ou index.html. Ouvrez dans un nouveau onglet les autres dossiers qui contiennent un fichier d'index
# Installation
Cloner le depot depuis `https://github.com/SkatekCorporation/homepage` en tapant ce commande dans votre terminal
`git clone https://github.com/SkatekCorporation/homepage.git homepage`
Pensez &agrave; faire un coup de `composer install` dans le dossier `homepage` afin de charger toutes les dependances.
# Utilisation
Creer un fichier `index.php` dans la racine de votre serveur puis mettez ceci dedans :

```
<?php
	header("Location : homepage/");
```
Supposons que ce projet est dans le dossier `homepage` de la racine de votre serveur.

C'est terminer. Ouvrez votre navigateur Web préféré puis taper ```localhost``` ou un lien que vous utilisez pour acceder à votre serveur et explorer.