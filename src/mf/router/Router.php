<?php

namespace mf\router;

use mf\auth\Authentification as Authentification;

class Router extends AbstractRouter
{
    
  public function __construct()
  {
    parent::__construct();        
  }

//Fonction pour ajouter une Route, on y rentre le nom,
//l'url, le controller à appeller et le niveau d'acces requis
  function addRoute($name, $url, $ctrl, $mth, $level)
  {
    self::$routes[$url] = [$ctrl, $mth, $level]; //On entre les données dans le tableau $route
    self::$aliases[$name] = $url; //On entre les données dans la tableau $aliases
  }

//Permet de définir une route par défaut qui sera executée en cas d'erreur
  function setDefaultRoute($url)
  {
    self::$aliases['default'] = $url; //On entre l'url par défaut dans la clé "défault" du tableau $aliases
  }

  //Fonction pour executer une Route si elle existe, sinon execute la route par défaut
  function run()
  {
    $auth = new Authentification(); //On crée une authentification
    $url = $this->http_req->path_info;//On récupère le path_info
      
    if(array_key_exists($url, self::$routes)) //On vérifie que  la route existe bien
    {
      if($auth->checkAccessRight(self::$routes[$url][2])) //Si l'utilisateur a les droits d'accéder à la route
      {
        $ctrl = self::$routes[$url][0]; //On attribue le controller
        $mth = self::$routes[$url][1]; //On a attribue la methode
       $c = new $ctrl(); //On on crée le controller
        $c->$mth(); //On execute la methode
      }
    }
    else 
    {
      $default_url = self::$aliases['default']; //Sinon on execute la route par défaut
      $default_ctrl_class = self::$routes[$default_url][0];
      $default_mth= self::$routes[$default_url][1];;
     
      $default_ctrl_obj = new $default_ctrl_class();
      $default_ctrl_obj->$default_mth();    
    }
  }

    //Fonction pour executer une URL qui possède des parametres entrés via la methode GET
  public function urlFor($route_name, $param_list=[]) {
        
    $url = self::$aliases[$route_name]; //On récupere l'url via le nom de l'alias de la route
    $fullUrl = $this->http_req->script_name . $url; //On crée l'url complete
    
    if (count($param_list) > 0) { //On vérifie qu'il y a au moins un parametre
        $fullUrl .= "?"; //On met un ? au début de la chaine pour indiquer qu'il s'agit d'un parametre GET

        for ($i=0; $i < count($param_list) ; $i++) {  //Pour répéter autant de fois qu'il y a de parametres
            if($i == (count($param_list) - 1)) { //Si c'est l'avant dernier parametre, on incremente sans le &
                $fullUrl .= $param_list[$i][0] . '=' . $param_list[$i][1]; //On incremente le nom du parametre puis sa valeur

            } else { //Sinon on ajoute un & temps qu'il reste des parametres à ajouter dans la methode GET
                $fullUrl .= $param_list[$i][0] . '=' . $param_list[$i][1] . '&amp;'; //On incremente le nom du parametre puis sa valeur puis un &
          }

        }   
    }

    return $fullUrl; //On retourne l'url complete
}

//Fonction pour executer une route, on entre son alias et il execute la Route correspondante 
//Avec le bon controller et la bonne methode de celui ci
  public static function executeRoute($alias) 
  {
    $url = self::$aliases[$alias]; //On recherche l'alias grace à son nom
    $ctrl = new self::$routes[$url][0](); //On recherche le nom du controller
    $meth = self::$routes[$url][1]; //On recherche le nom de la methode
    $c = new $ctrl(); //On crée le controller
    $c->$meth(); //On execute la methode
  }
}
?>