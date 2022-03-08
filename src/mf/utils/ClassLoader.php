<?php

namespace mf\utils;

 class ClassLoader extends AbstractClassLoader
{

    //Fonction pour récupérer le nom d'un fichier
    public function getFilename(string $classname) : string
        {
            $chemin = str_replace("\\", DIRECTORY_SEPARATOR, $classname); //remplace les \ par le séparateur liécà l'OS
            $chemin .=  ".php"; // concatene .php à notre chaine de caractere
            return $chemin; //on retourne le chemin
        }
    
    //Fonction piur créer un chemin
    function makePath(string $filename): string
    {
        $fichier = $this->prefix . DIRECTORY_SEPARATOR . $filename; // concatene le prefixe, un séparateur de dossiers et  le chemin du dossier 
        return $fichier; //on return le fichier
    }

    //Fonction pour charger une classe
   public function loadClass(string $classname)
    {
        $path = $this->getFilename($classname); // on utilise une variable temporaire pour créer le chemin
        $cheminComplet = $this->makePath($path); //On fait le chemin complet avec préfixe
        if (file_exists($cheminComplet)) //On vérifie si le fichier existe
        {
            require_once($cheminComplet); //On effectue le require avec le nom du fichier
            //echo "Le chargement du fichier a réussi";
        }
    }
    
}