<?php

namespace tweeterapp\model;


class Tweet extends \Illuminate\Database\Eloquent\Model 
{

    protected $table      = 'tweet';  /* le nom de la table */
    protected $primaryKey = 'id';     /* le nom de la clé primaire */
    public    $timestamps = true;    /* si vrai la table doit contenir
                                            les deux colonnes updated_at,
                                            created_at */

    //Fonction pour lier avec la table des utilisateurs
    public function author() 
    {
        return $this->belongsTo('tweeterapp\model\User','author');
    }

    //Fonction pour lier avec la table des likes
    public function likedBy() 
    {
    return $this->belongsToMany('tweeterapp\model\User','tweeterapp\model\Like','tweet_id','user_id');
    }
}