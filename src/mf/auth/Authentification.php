<?php

namespace mf\auth;

use mf\auth\exception\AuthentificationException as AuthentificationException;

class Authentification extends AbstractAuthentification
{
    //Construire l'authentification
    public function __construct()
    {
        if (isset($_SESSION['user_login'])) //Si le user_login est défini
        {
            $this->user_login = $_SESSION['user_login']; //On récupere l'user login et on l'attribue
            $this->access_level = $_SESSION['access_level']; //On récupere le niveau d'accès et on l'attribue
            $this->logged_in = true; //On met la variable logged_in à true pour indiquer que l'user est connecté
        } 
        else //Sinon
        {
            $this->user_login = null; //On met l'user login à null
            $this->access_level = self::ACCESS_LEVEL_NONE; //on met le niveau d'accès à celui par défaut
            $this->logged_in = false; //On met la variable logged_in à false pour indiquer que l'user n'est  pas connecté
        }
    }
    
    //Fonction pour mettre à jour la session
     protected function updateSession($username, $level)
     {
        $this->user_login = $username; //On récupere l'user name et on l'attribue
        $this->access_level = $level; //On récupere le niveau d'accès et on l'attribue
        $_SESSION['user_login'] = $username; //On attribue à la session le nouvel user name
        $_SESSION['access_level'] = $level; //On attribue à la session le nouveau niveau d'acces
        $this->logged_in = true; //On met la variable logged_in à true pour indiquer que l'user est connecté
     }
   
     //Fonction pour se deconnecter
     public function logout()
     {
        unset($_SESSION['user_login']); //On enleve le user login du tableau $_SESSION
        unset($_SESSION['access_right']); //On enleve les droits d'acces du tableau $_SESSION
        $this->user_login = null; //On mets la variable user login à null
        $this->access_level = self::ACCESS_LEVEL_NONE; //On defini le access level à celui par défaut
        $this->logged_in = false; //On met la variable logged_in à false pour indiquer que l'user est n'est pas connecté

    }

    //Fonction pour vérifier les droits d'accès
     public function checkAccessRight($requested) 
     {
         if ($requested > $this->access_level ) //Si les droits d'acces de l'utilisateur son inférieur au droit d'accès requis pour accéder à la page
         {
             return false; //On return false
         }
         else //Sinon
         {
             return true; //On return true
         }
    }

    //fonction pour se connecter
     public function login($username, $db_pass, $given_pass, $level)
     {
         if($this->verifyPassword($given_pass, $db_pass) == true) //On vérifie le mot de passe et si il correspond à celui de la base de données
         {
             $this->updateSession($username, $level); //On met à jour la session avec le nom d'utilisateur et le niveau d'accès
         }

    }
   
    //Fonction pour hasher le mot de passe
     protected function hashPassword($password)
     {
        $hache = password_hash($password, PASSWORD_DEFAULT); //On hash le mot de passe avec la fonction native de php
        return $hache; //On return le mot de passe hashé
    }
       
    //Fonction pour vérifier le mot de passe
     protected function verifyPassword($password, $hash)
     {
        $verify_password = password_verify($password, $hash); //On effectue la vérification du mot de passe avec la fonction native de php
        return $verify_password; //On return true ou false selon le resultat de la fonction
    }
}  
