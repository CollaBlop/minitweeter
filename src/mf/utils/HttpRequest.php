<?php

namespace mf\utils;

 class HttpRequest extends AbstractHttpRequest
 {

    //Fonction pour construire une requete HTTP
    public function __construct()
    {
        $this->script_name=$_SERVER['SCRIPT_NAME']; //On récupere le nom du script et on l'attribue
        if (isset($_SERVER['PATH_INFO']))//Si le path info est déclaré
        {
            $this->path_info=$_SERVER['PATH_INFO']; //On récupere le path info et on l'attribue
        }
        $this->root=dirname($_SERVER['SCRIPT_NAME']); //On récupere le nom du dossier ou est script name et on l'attribue
        $this->method=$_SERVER['REQUEST_METHOD']; //On récupere la methode et on l'attribue
        $this->get=$_GET; //On récupere le tableau GET
        $this->post=$_POST; //On récupere le tableau POST
    }
 }

?>