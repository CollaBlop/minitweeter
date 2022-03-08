<?php

// Autoloader
require_once 'src/mf/utils/AbstractClassLoader.php';
require_once 'src/mf/utils/ClassLoader.php';

/* pour le chargement automatique des classes d'Eloquent (dans le répertoire vendor) */
require_once 'vendor/autoload.php';

$loader = new \mf\utils\ClassLoader('src');
$loader->register();

// Router
use mf\router\Router as Router;

// Modeles
use tweeterapp\model\Follow as Follow;
use tweeterapp\model\Like as Like;
use tweeterapp\model\Tweet as Tweet;
use tweeterapp\model\User as User;

// Controlleurs
use tweeterapp\control\TweeterController as TweeterController;
use tweeterapp\control\TweeterAdminController as TweeterAdminController;

// Views
use tweeterapp\view\TweeterView as TweeterView;

// Authentification
use tweeterapp\auth\TweeterAuthentification as TweeterAuthentification;

// Debut de la session
session_start();

// Paramètre de connexion issus de conf.ini
$ini = parse_ini_file("conf/config.ini");

/* une instance de connexion  */
$db = new Illuminate\Database\Capsule\Manager();

$db->addConnection( $ini ); /* configuration avec nos paramètres */
$db->setAsGlobal();            /* rendre la connexion visible dans tout le projet */
$db->bootEloquent();           /* établir la connexion */


TweeterView::addStyleSheet('html/style.css');

//Definition des Routes

$router = new Router();
$router->addRoute('home', //alias
                  '/home/',   //route
                  '\tweeterapp\control\TweeterController',   // controller
                  'viewHome',                 // methode
                  TweeterAuthentification::ACCESS_LEVEL_NONE);      // niveau accès

$router->addRoute('tweet',
                  '/tweet/',
                  '\tweeterapp\control\TweeterController',
                  'viewTweet',
                  TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('usertweets',
                  '/usertweets/',
                  '\tweeterapp\control\TweeterController',
                  'viewUserTweets',
                  TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('post',
                  '/post/',
                  '\tweeterapp\control\TweeterController',
                  'viewPostTweet',
                  TweeterAuthentification::ACCESS_LEVEL_USER);

$router->addRoute('send',
                  '/send/',
                  '\tweeterapp\control\TweeterController',
                  'sendPostTweet',
                  TweeterAuthentification::ACCESS_LEVEL_USER);
            
$router->addRoute('followers',
                  '/followers/',
                  '\tweeterapp\control\TweeterController',
                  'viewFollowers',
                  TweeterAuthentification::ACCESS_LEVEL_USER);

$router->addRoute('following',
                  '/following/',
                  '\tweeterapp\control\TweeterController',
                  'viewFollowing',
                  TweeterAuthentification::ACCESS_LEVEL_USER);

$router->addRoute('login',
                 '/login/',
                 '\tweeterapp\control\TweeterAdminController',
                 'login',
                 TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('check_login',
                 '/check_login/',
                 '\tweeterapp\control\TweeterAdminController',
                 'CheckLogin',
                 TweeterAuthentification::ACCESS_LEVEL_NONE);

$router->addRoute('logout',
                 '/logout/',
                 '\tweeterapp\control\TweeterAdminController',
                 'logOut',
                 TweeterAuthentification::ACCESS_LEVEL_NONE);
                
$router->addRoute('signup',
                '/signup/',
                '\tweeterapp\control\TweeterAdminController',
                'signup',
                TweeterAuthentification::ACCESS_LEVEL_NONE);
                
$router->addRoute('check_signup',
                '/check_signup/',
                '\tweeterapp\control\TweeterAdminController',
                'checkSignup',
                TweeterAuthentification::ACCESS_LEVEL_NONE);
            

$router->setDefaultRoute('/home/');
$router->run();