<?php

namespace tweeterapp\auth;

use mf\auth\exception\AuthentificationException as AuthentificationException;
use tweeterapp\model\User as User;

class TweeterAuthentification extends \mf\auth\Authentification {


    const ACCESS_LEVEL_USER  = 100;   
    const ACCESS_LEVEL_ADMIN = 200;

    /* constructeur */
    public function __construct(){
        parent::__construct();
    }


    //Fonction pour créer un utilisateur 
    public function createUser($username, $pass, $fullname,$level=self::ACCESS_LEVEL_USER) 
    {
        if(User::select()->where('username','=',"$username")->exists()) //Si le nom d'utilisateur existe déjà dans la DB
        {
        $emess = "Le nom $username est déjà pris. Veuillez en choisir un autre"; //On crée le message de l'exception
        throw new AuthentificationException($emess); //On retourne le message de l'exception
        } 
        else //Sinon
        {
            $new_user = new User(); //On crée un utilisateur
            $new_user->username = $username; //On attribue son username
            $new_user->password = $this->hashPassword($pass); //On attribue son mot de passe hashé
            $new_user->fullname = $fullname; //On attribue son fullname
            $new_user->level= $level; //On attribue son level
            $new_user->followers= 0; //On initialise son nombre de follower à 0 comme c'est un nouveau compte
            $new_user->save(); //On sauvegarde dans la ba   se de données
        }
    }

    //Fonction pour se connecter
    public function loginUser($username, $password){

        if(!User::select()->where('username','=',"$username")->exists()) //Si l'utilisateur n'existe pas
        {
            $emess = "User $username doesn't exist"; //On crée le message de l'exception
            throw new AuthentificationException($emess); //On retourne le message de l'exception
        } 
        else //Sinon
        {
            $user = User::select()->where('username','=',"$username")->first(); //On récupere les données de l'utilisateur dans la base de données
            $this->login($user->username, $user->password, $password, $user->level); //On effectue la connection avec avec les infos récupérées
        }

    }

}
