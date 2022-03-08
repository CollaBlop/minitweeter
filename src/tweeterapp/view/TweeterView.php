<?php

namespace tweeterapp\view;

use mf\router\Router as Router;
use tweeterapp\model\Tweet as Tweet;
use tweeterapp\model\User as User;
use tweeterapp\view\TweeterView as TweeterView;
use tweeterapp\auth\TweeterAuthentification as TweeterAuthentification;

use mf\utils\HttpRequest as HttpRequest;

class TweeterView extends \mf\view\AbstractView {
  

// Appelle le constructeur de la classe parent
    public function __construct( $data )
    {
        parent::__construct($data);
    }

    /* Méthode renderHeader
     *
     *  Retourne le fragment HTML de l'entête (unique pour toutes les vues)
     */ 
    private function renderHeader(){
        // header balise closed in renderTopMenu
        return '<header class="theme-backcolor1"><h1>MiniTweeTR</h1>';
    }
    
    /* Méthode renderFooter
     *
     * Retourne le fragment HTML du bas de la page (unique pour toutes les vues)
     */
    private function renderFooter(){
        return "<footer class='theme-backcolor1'>Tweeter</footer>";
    }

    /* Méthode renderHome
     *
     * Vue de la fonctionalité afficher tous les Tweets. 
     *  
     */
    
    private function renderHome(){

        /*
         * Retourne le fragment HTML qui affiche tous les Tweets. 
         *  
         * L'attribut $this->data contient un tableau d'objets tweet.
         * 
         */

        $route = new Router(); //On crée un nouveau router
        
       $tweets = $this->data; //On attribue les données envoyées par le controller
       
       $displayTweets = "<article class='theme-backcolor2'>"; //On initialise l'html avec des classes css

        foreach ($tweets as $tweet) //Pour tous les tweets 
        {
            $author = $tweet->author()->first(); //On recupere l'auteur du tweet
            $link_tweet =$route->urlFor('tweet',[['id',"$tweet->id"]]); ///On crée l'url de redirection pour afficher le tweet
            $link_user = $route->urlFor('usertweets',[['id',"$author->id"]]); //On crée l'url de redirection pour afficher les utilisateurs
        
            $displayTweets .= "<div class='tweet'>
                                 <a href=" . $link_tweet . "> $tweet->text</a>
                                 <div class='tweet-footer'>
                                     <div class='tweet-timestamp'>$tweet->created_at</div>
                                     <div class='tweet-author'><a href= " .  $link_user . "> $author->username </a></div>
                                 </div>
                              </div>";
                    
        } //On insere les données dans l'html
        $displayTweets .= "</article>";//On ferme la balise 

         return $displayTweets; //On return l'html

    }
  
    /* Méthode renderUeserTweets
     *
     * Vue de la fonctionalité afficher tout les Tweets d'un utilisateur donné. 
     * 
     */
     
    private function renderUserTweets()
    {

        /* 
         * Retourne le fragment HTML pour afficher
         * tous les Tweets d'un utilisateur donné. 
         *  
         * L'attribut $this->data contient un objet User.
         *
         */

        $route = new Router(); //On crée un router
        $user = $this->data; //On attribue les données envoyées par le controller
        $tweets = $user->tweets()->get(); //On récupere les tweets 

        $tweets_reversed_order;  //On crée une variable pour plus tard
        $count_tweet = count($tweets); //On compte le nombre de tweet
        for ($i = $count_tweet - 1; $i >= 0; $i--) //On fait un if pour remonter en sens inverse la liste des tweets
        {
            $tweets_reversed_order[] = $tweets[$i]; //On crée la liste inversée
        }
        //On crée l'html de l'user
        $htmlUser = "
        <div style='font-weight: bolder'>User</div>
        <div> Fullname : $user->fullname, Username : $user->username, Followers : $user->followers </div>
        ";
        //On crée l'html des tweets
        $htmlTweets = "<div style='font-weight: xx-bold; text-align: right'> TWEETS </div>";
        
        foreach ($tweets_reversed_order as $tweet) //Pour tous les tweets
        {
            $link_tweet = $route->urlFor('tweet',[['id',"$tweet->id"]]); //On crée le lien de redirection vers le tweet
            $htmlTweets .= "
                    <div style='border: 1px solid yellow; text-align: center'><a href=" . $link_tweet ."> $tweet->text </a></div>
                    <div style='font-weight: bolder'>AUTHOR : $user->username \n</div>
                    <div style='font-size: smaller'>Created at $tweet->created_at \n</div>
            ";
        }

        return $htmlUser . $htmlTweets; //On retourne les deux html
    }
  
    /* Méthode renderViewTweet 
     * 
     * Rréalise la vue de la fonctionnalité affichage d'un tweet
     *
     */
    
    private function renderViewTweet()
    {

        /* 
         * Retourne le fragment HTML qui réalise l'affichage d'un tweet 
         * en particulié 
         * 
         * L'attribut $this->data contient un objet Tweet
         *
         */

        $route = new Router(); //On crée un nouveau router

        $tweet = $this->data; //On attribue les données envoyées par le controller
        $author = $tweet->author()->first(); //On recupere l'utilisateur

        $link_user = $route->urlFor('usertweets',[['id',"$author->id"]]); //On crée le lien de redirection vers le créateur du tweet
//On cfée l'html 
        $htmlTweet =
            "<div style='border: 1px solid yellow; text-align: center'> $tweet->text</div>
             <div style='font-weight: bolder'>AUTHOR : <a href=" . $link_user . "> $author->username </a>\n</div>
             <div style='font-size: smaller'>Created at $tweet->created_at \n</div>
             <div style='font-size: smaller'>Score $tweet->score \n</div>";

       return $htmlTweet; //On retourne l'html
        
    }



    /* Méthode renderPostTweet
     *
     * Realise la vue de régider un Tweet
     *
     */
    protected function renderPostTweet(){
        
        /* Méthode renderPostTweet
         *
         * Retourne la framgment HTML qui dessine un formulaire pour la rédaction 
         * d'un tweet, l'action (bouton de validation) du formulaire est la route "/send/"
         *
         */

         $route = new Router(); //On crée un nouveau router
         $send_route = $route->urlFor('send'); //On crée l'url de l'envoi
//On crée le formulaire html
$form = <<<EOT
<form method="post" action="$send_route" class="forms">

<div class = "tweet-form">
  
  <label for="postTweet">Tweet : </label>
  <textarea class="forms-text" id="postTweet" name="postTweet" rows="5" cols="33">
  </textarea>
  
  <input class="forms_button send_button" type="submit" value="submit">

</div>

</form>
EOT;


        return $form; //On retourne le formulaire
    }




    public function renderLogin() 
    {

        $route = new Router(); //On crée un nouveau router
        $check_login_route = $route->urlFor('check_login'); //On crée l'url de redirection pour la verification
//On crée l'html
$login_form = <<<EOT
<article class='theme-backcolor1'>
    <form id="login" method="post" class="form" action="$check_login_route">    

        <label> User Name </label>    
        <input type="text" name="username" id="username" class="forms-text" placeholder="Username">        

        <label> Password </label>    
        <input type="password" name="password" id="password" class="forms-text" placeholder="Password">    
        
        <input type="submit" name="log" id="log" class="forms-button" value="Log In Here" >       

    </form>
</article>
EOT;

        return $login_form; //On retourne l'html

    }


    //Fonction pour afficher le rendu des tweets des users que l'utilsiateur connecté follow
    public function renderFollowing() 
    {
        $route = new Router(); //On crée un nouveau router
        $users = $this->data; //On attribue les données envoyées par le controller
        $count = $users->count(); //On compte le nombre d'utilisateurs
        //On crée l'html
        $displayTweets = <<<EOT
        <div>
                <h2> Vous suivez $count personne(s)</h2>
EOT;
foreach ($users as $user) //Pour tous les utilisateurs
{
    $tweets = Tweet::where('author', '=', $user->id)->orderBy('created_at','desc')->get(); //On recupere les tweets
        foreach($tweets as $tweet) //Pour tous les tweets
        {
            $author = $tweet->author()->first(); //On recupere les tweets
            $link_tweet =$route->urlFor('tweet',[['id',"$tweet->id"]]); //On crée le lien de redirection vers le tweet
            $link_user = $route->urlFor('usertweets',[['id',"$author->id"]]); //On crée le lien de redirection vers l'auteur
            
            //On incremente l'html
            $displayTweets .= "<div class='tweet'>
                                 <a href=" . $link_tweet . "> $tweet->text</a>
                                 <div class='tweet-footer'>
                                     <div class='tweet-timestamp'>$tweet->created_at</div>
                                     <div class='tweet-author'><a href= " .  $link_user . "> $author->username </a></div>
                                 </div>
                                 </div>
                              </div>";
                    
        }
        //On ferme la balise
        $displayTweets .= "</article>";

         
        }
        return $displayTweets; //On retourne l'html
    }

    //fonction pour  afficher le rendu de la page qui affiche les gens qui follow l'utilisateur connecté
    public function renderFollowers() 
    {
        $route = new Router(); //On crée un nouveau router
        $followers = $this->data; //On attribue les données envoyées par le controller
        $count = $followers->count(); //On compte le nombre de followers
        //On crée l'html
        $followers_list = <<<EOT
        <div>
                <h2> Vous êtes suivis par $count personne(s)</h2>
EOT;
foreach ($followers as $follower) //Poiur tous les followers
{
    $link_follower = $route->urlFor('usertweets',[['id',"$follower->id"]]); //On crée un lien vers le follow

    //On crée le bouton pour rejoindre le profil du follower
       $followers_list .= "<div>  <a href=" . $link_follower . "> $follower->username </a>
       
       </div>";

   }

        return $followers_list; //On retourne l'html
    }

    //Fonction pour afficher le rendu de la page d'inscription
    public function renderSignup() 
    {
        
        $route = new Router(); //On crée un nouveau router
        $check_signup_route = $route->urlFor('check_signup'); //On crée la redirection vers la page de verification

//On crée l'html
$signup_form = <<<EOT
<article class='theme-backcolor2'>
    <form method="post" action="$check_signup_route" class="forms">

        <div>
            <label> Fullname </label>    
            <input type="text" name="fullname" id="fullname" class="forms-text" placeholder="Fullname">        
        </div>

        <div>
            <label> Username </label>    
            <input type="text" name="username" id="username" class="forms-text" placeholder="Username">    
        </div>
        
        <div>
            <label> Password </label>    
            <input type="password" name="password" id="password" class="forms-text" placeholder="Password">    
        </div>
        
        <div>
            <label> Retype Password </label>    
            <input type="password" name="password_retyped" id="password_retyped" class="forms-text" placeholder="Retype Password">    
        </div>
    
        </div>
            <input type="submit" name="lsignup" id="signup" class="forms-button" value="Sign Up">    
        </div>

    </form>
</article>
EOT;


       return $signup_form; //On retourne l'html

    }

    //Créer le menu du haut lorsqu'on est déconnecté
    public function renderTopMenu_logged_out()
    {
        $route = new Router(); //On crée un nouveau router
        $home_route = $route->urlFor('home'); //On recupere l'url de la page par defaut
        $login_route = $route->urlFor('login'); //On recupere l'url de la page de connexion
        $signup_route = $route->urlFor('signup'); //On recupere l'url de la page d'inscription
        $http_req = new HttpRequest(); //On cree un nouvel httprequest
        $url_home_png = $http_req->root . "/html/home.png"; //On recupere le chemin de l'image
        $url_login_png = $http_req->root . "/html/login.png"; //On recupere le chemin de l'image
        $url_signup_png = $http_req->root . "/html/signup.png"; //On recupere le chemin de l'image

        //On crée l'html
$topMenu_html = <<<EOT
    <nav id="navbar">
            <a class="tweet-control" href="$home_route"> 
                <img alt="home" title="Home" src="$url_home_png"> 
            </a>
            <a class="tweet-control" href="$login_route">
                <img alt="Login" title="Login" src="$url_login_png"> 
            </a>
            <a class="tweet-control" href="$signup_route">
                <img alt="Sing Up" title="Sign Up" src="$url_signup_png"> 
            </a>
    </nav>
</header>
EOT;

        return $topMenu_html; //On retiurne l'html
    }

    //Fonction pour créer le menu header quand on est connecté
    public function renderTopMenu_logged_in() 
    {
        $route = new Router(); //On crée un nouveau router

        $home_route = $route->urlFor('home'); //On recupere l'url de la page par defaut
        $followers_route = $route->urlFor('followers'); //On recupere l'url de la page des followers
        $following_route = $route->urlFor('following'); //On recupere l'url de la page des gens qu'on follow
        $logout_route = $route->urlFor('logout'); //On recupere l'url de la page de deconnexion

        $http_req = new HttpRequest(); //On cree un nouvel httprequest

        $url_home_png = $http_req->root . "/html/home.png"; //On recupere le chemin de l'image
        $url_followers_png = $http_req->root . "/html/followers.png"; //On recupere le chemin de l'image
        $url_following_png = $http_req->root . "/html/followees.png"; //On recupere le chemin de l'image
        $url_logout_png = $http_req->root . "/html/logout.png"; //On recupere le chemin de l'image

        $auth = new TweeterAuthentification(); //On crée une nouvelle authentification

        $welcome_message = "Bonjour, $auth->user_login, vous êtes connecté."; //message d'accueil

        // On crée l'html
$topMenu_html = <<<EOT
    <nav id="navbar">
    <a class="tweet-control" href="$home_route"> 
    <img alt="home" title="Home" src="$url_home_png"> 
    </a>
    <a class="tweet-control" href="$following_route">
    <img alt="Following" title="Following" src="$url_following_png"> 
    </a>
    <a class="tweet-control" href="$followers_route">
    <img alt="Followers" title="Followers" src="$url_followers_png"> 
    </a>
    <a class="tweet-control" href="$logout_route">
    <img alt="Logout" title="Log Out" src="$url_logout_png"> 
    </a>
    </nav>
</header>
<p><center>$welcome_message</center></p>
EOT;

        return $topMenu_html; //On retourne l'html

    }

    //Fonction pour afficher le menu du bas footer
    public function renderBottomMenu() 
    {
        $route = new Router(); //On crée un noiuveau router
        $post_route = $route->urlFor('post'); //On crée une url pour la page de post de tweet

        //On crée l'html
        $bottom_menu_html = <<<EOT
<nav id="menu" class="theme-backcolor1">
        <div id="nav-menu">
            <div class="button theme-backcolor2" style="text-align: center">
                <a href = "$post_route" alt="New Post" title="New Post"> + New Post </a>
            </div>
        </div>
</nav>

EOT;
        return $bottom_menu_html; //On retourne l'html
    }


    /* Méthode renderBody
     *
     * Retourne la framgment HTML de la balise <body> elle est appelée
     * par la méthode héritée render.
     *
     */
    
    public function renderBody($selector){

        $auth = new TweeterAuthentification;

        /*
         * voire la classe AbstractView
         * 
         */
        $header = $this->renderHeader();
        // $topMenu;
        // $center;
        // $bottomMenu;
        $footer = $this->renderFooter();
        
        // On choisit la page a afficher a l'aide du selecteur 
        switch ($selector) {
            case 'renderHome':
                $center = $this->renderHome();
                break;
            
            case 'viewTweet':
                $center = $this->renderViewTweet();
                break;

            case 'userTweets':
                $center = $this->renderUserTweets();
                break;

            case 'viewPost':
                $center = $this->renderPostTweet();
                break;

            case 'viewLogin':
                $center = $this->renderLogin();
                break;

            case 'viewFollowers':
                $center = $this->renderFollowers();
                break;

            case 'viewFollowing':
                $center = $this->renderFollowing();
                break;

            case 'viewSignup':
                $center = $this->renderSignup();
                break;

            default:
                echo "Erreur : pas de vue";
                break;
        }
        //On choisit le header si l'user est connecté ou pas
        if(!$auth->logged_in) {
            $topMenu = $this->renderTopMenu_logged_out();
            $bottomMenu = "";
        } else {
            $topMenu = $this->renderTopMenu_logged_in();
            $bottomMenu = $this->renderBottomMenu();
        }
        
$body = <<<EOT
${header}
${topMenu}
<section>
${center}
${bottomMenu}
</section>
${footer}
EOT;

        return $body; //On return  le body
        
    }   
}






