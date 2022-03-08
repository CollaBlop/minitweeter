<?php

namespace tweeterapp\control;

use mf\auth\exception\AuthentificationException as AuthentificationException;
use tweeterapp\auth\TweeterAuthentification as TweeterAuthentification;
use tweeterapp\view\TweeterView as TweeterView;
use tweeterapp\model\User as User;
use mf\router\Router as Router;

class TweeterAdminController extends \mf\control\AbstractController {
    
    //Fonction pour la connection
    public function login() 
    {
        $view_login = new TweeterView(""); //On crée une nouvelle vue
        $view_login->render("viewLogin"); //On crée le render viewLogin pour afficher la page de connection
    }

    //Fonction pour vérifier les informations de connection
    public function checkLogin() 
    {
        $router = new Router(); //On crée un nouveau router
        
        $user_name = $this->request->post['username']; //On recupere l'username et on l'attribue
        $user_password = $this->request->post['password']; //On recupere le password et on l'attribue
        $tweeter_auth = new TweeterAuthentification; //On crée une nouvelle authentifications
        
        try {
            $tweeter_auth->loginUser($user_name, $user_password); //On essaie de se connecter avec les infos rentrées
        } 
        catch (AuthentificationException $emessage) //On crée un message en cas d'erreur
        { 
            $router->executeRoute('login'); //On execute la route pour se connecter
        }
       
        
        if($tweeter_auth->logged_in) //Si l'utilisateur est connecté
        {
            $user = User::select()->where('username','=',"$user_name")->first();//On récupere l'utilisateur dans la DB
            $follows = $user->follows()->wherePivot('follower', $user->id)->get(); //On récupere la liste des gens que l'utilisateur follow
            $vueFollowing = new TweeterView($follows); //On crée une nouvelle vue avec ces données
            $vueFollowing->render('viewFollowing'); //On render la vue qui affiche la page des gens que l'utilisateur follow
        } 
        else //Sinon
        {
            echo "Vous n'êtes pas connecté, veuillez vous identifier"; //On affiche un message disant que l'utilisateur n'est pas connecté
        }
    }

    //Fonction piur se déconnecter
    public function logOut() 
    {
        $tweeter_auth = new TweeterAuthentification; //On crée une nouvelle authentification
        $tweeter_auth->logout(); //On se deconnecte
        $route = new Router(); //On crée un nouveau router
        $route->executeRoute('home'); //On execute la route home, route par défault qui ne necessite aucun droits  
    }

    //Fonction pour la redirection vers la page d'inscription
    public function signup() 
    {
        $view_signup = new TweeterView(""); //On crée une nouvelle vue
        $view_signup->render("viewSignup"); //On render la page d'inscription
    }
    //Fonction pour la verification de l'inscription
    public function checkSignup() 
    {
        
        $router = new Router(); //On crée un nouveau router
        
        $fullname = $this->request->post['fullname']; //On récupere le fullname et on l'attribue
        $username = $this->request->post['username']; //On recupere l'username et on l'attribue
        $password = $this->request->post['password']; //On recupere le mot de passe et on l'attribue
        $password_retyped = $this->request->post['password_retyped']; //On recupere le mot de passe retapé et on l'attribue
        $tweeter_auth = new TweeterAuthentification; //On crée une nouvelle authentification
        
        try 
        {
            if($password !== $password_retyped) //Si les deux mots de passe entrés par l'utilisateur ne sont pas les memes
            {
                throw new AuthentificationException("Les deux mots de passes rentrées ne sont pas les memes"); //On retourne une exception
            } 
            echo("Compte créé avec succes"); //Sinon, on affiche que le compte a été créé
            $tweeter_auth->createUser($username, $password, $password_retyped); //On crée un utilisateur
            $router->executeRoute('home'); //On execute le route par default
        } catch (AuthentificationException $emessage) //On attrape le message d'exception en cas d'erreur
        {
            echo $emessage; //On affiche le message
            $router->executeRoute('signup'); //On redirige vers la page d'inscription
        }
    }
}