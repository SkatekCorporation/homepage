# Web-server-home-page
Mettez le dans votre Web Local puis explorer la liste de vos dossier à la racine ainsi que les fichiers.
Parcourez toutes les dossiers ci ceux-ci n'ont pas un fichier index.php ou index.html. Ouvrez dans un nouveau onglet les autres dossiers qui contiennent un fichier d'index
# Installation
Cloner le depot depuis `https://github.com/SkatekCorporation/Web-server-home-page` en tapant ce commande dans votre terminal
`git clone https://github.com/SkatekCorporation/Web-server-home-page.git homepage`
Pensez &agrave; faire un coup de `composer install` dans le dossier `homepage`
# Utilisation
Creer un fichier `index.php` dans la racine de votre serveur puis mettez ceci dans

```
<?php
	header("Location : homepage/");
```
Supposons que ce projet est dans le dossier `homepage` de la racine de votre serveur

