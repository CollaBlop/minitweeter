<?php

namespace tweeterapp\control;


use mf\utils\HttpRequest as HttpRequest;
use mf\router\Router as Router;
use tweeterapp\model\Tweet as Tweet;
use tweeterapp\model\Follow as Follow;
use tweeterapp\model\User as User;
use tweeterapp\view\TweeterView as TweeterView;
use tweeterapp\auth\TweeterAuthentification;

/* Classe TweeterController :
 *  
 * Réalise les algorithmes des fonctionnalités suivantes: 
 *
 *  - afficher la liste des Tweets 
 *  - afficher un Tweet
 *  - afficher les tweet d'un utilisateur 
 *  - afficher la le formulaire pour poster un Tweet
 *  - afficher la liste des utilisateurs suivis 

 *   
 */

class TweeterController extends \mf\control\AbstractController {


    public function __construct()
    {
        parent::__construct();
    }

    //Fonction pour appeller l'affichage de la vue home
       public function viewHome()
    {
        $tweets = Tweet::orderBy('created_at','desc')->get(); //On recupere les informations dans la base de données
        $vueTweets = new TweeterView($tweets); //On crée une nouvelle vue et on lui envoie les données
        $vueTweets->render('renderHome'); //On appel la fonction pour render la page home
    }
   
    //Fonction pour appeller l'affichage de la vue d'un tweet
    public function viewTweet($id = null)
    {
         $route = new Router(); //On crée un nouveau router
         $id_tweet = $id ?? $this->request->get['id']; //On récupere l'id du tweet passé via la methode get
         $tweet = Tweet::find($id_tweet); //On cherche le tweet avec le bon id dans la base de données
         $vueTweet = new TweeterView($tweet); //On crée une vue avec la données récupérés
         $vueTweet->render('viewTweet'); //On appel le render pour afficher la vue
    }

    //Fonction poiur appeller l'affichage de la vue d'un utilisateur
    public function viewUserTweets()
    {
        $route = new Router(); //On crée un nouveau router
        $idUser = $this->request->get['id']; //On récupere l'id de l'user que l'on veut avec via la methode get
        $user = User::find($idUser); //On recherche l'user dans la base de données
        $tweets = $user->tweets()->get(); //On recupere les tweets de l'utilisateur
        $vueUserTweets = new TweeterView($user); //On crée une vue avec les données récupérés
        $vueUserTweets->render('userTweets'); //On appel le render pour afficher la vue
    }

    //Fonction pour appeller l'affichage de la creation d'un nouveau tweet
    public function viewPostTweet() 
    {
        $vuePostTweet = new TweeterView(""); //On crée une nouvelle vue 
        $vuePostTweet->render('viewPost'); //On appel le render pour afficher la vue
    }

    //Fonction pour afficher la vue de verification d'envoi de tweet
    public function sendPostTweet()
     {
        $text_form = $this->request->post['postTweet']; //On va chercher le text du tweet dans la methode post
        $auth = new TweeterAuthentification(); //On crée une nouvelle authentification
        $user_id = User::select('id')->where('username','=',"$auth->user_login")->first(); //On recupere l'user connecté qui envoie le tweet
        $new_tweet = new Tweet(); //On crée un nouveau tweet
        $new_tweet->text = filter_var($text_form,FILTER_SANITIZE_SPECIAL_CHARS); //On filtre le text pour éviter une injection sql
        $new_tweet->author = $user_id->id; //On indique le l'auteur du tweet est l'utilisateur connecté pour l'insertion dans la DB
        $new_tweet->save(); //On insere dans la DB
        $route = new Router(); //On crée une nouvelle route
        $route->executeRoute('home'); //On rediriger vers la route par defaut   
    }

    //Fonction pour afficher la vue de qui follow l'utilisateur
    public function viewFollowers() 
    {
        $auth = new TweeterAuthentification(); //On crée une nouvelle authentification
        $user = User::select()->where('username','=',"$auth->user_login")->first(); //On récupere l'utilisateur connecté
        $followers = $user->followedBy()->where('followee', $user->id)->get(); //On recupere la liste des gens qui follow l'utlisateur connecté
        $vueFollowers = new TweeterView($followers); //On crée une nouvelle vue avec les données récupérés
        $vueFollowers->render('viewFollowers'); //On appel le render de la vue pour l'afficher
    }

    //Fonction pour afficher la vue de qui l'utlisateur follow
    public function viewFollowing() 
    {
        $auth = new TweeterAuthentification();//On crée une nouvelle authentification
        $user = User::select()->where('username','=',"$auth->user_login")->first(); //On recupere l'utilisateur connecté
        $follows = $user->follows()->wherePivot('follower', $user->id)->get(); //On récupere la liste des gens qu'il follow
        $vueFollowing = new TweeterView($follows); //On crée une nouvelle vue avec les données recupérées 
        $vueFollowing->render('viewFollowing'); //On appel le render de la vue pour l'afficher
    }


}
