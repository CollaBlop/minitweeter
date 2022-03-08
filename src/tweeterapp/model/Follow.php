<?php
namespace tweeterapp\model;

class Follow extends \Illuminate\Database\Eloquent\Model {

    protected $table      = 'follow';  /* le nom de la table */
    protected $primaryKey = 'id';     /* le nom de la clé primaire */
    public    $timestamps = false;    
    
    //Fonction pour rechercher les followers
    public function followers() 
    {
        return $this->hasMany('tweeterapp\model\User', 'follower');
    }
}