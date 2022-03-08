<?php

namespace tweeterapp\model;

class User extends \Illuminate\Database\Eloquent\Model {

    protected $table      = 'user';  /* le nom de la table */
    protected $primaryKey = 'id';     /* le nom de la clé primaire */
    public    $timestamps = false;    /* si vrai la table doit contenir
                                            les deux colonnes updated_at,
                                            created_at */

    //Fonction pour lier avec la table des tweets
    public function tweets() 
    {
        return $this->hasMany('tweeterapp\model\Tweet', 'author');
    }

    //Fonction pour lier avec la table des likes
    public function liked() 
    {
        return $this->belongsToMany('tweeterapp\model\Tweet','tweeterapp\model\Like','user_id','tweet_id');         
    }

    //Fonction pour lier avec la table des follows
    public function follows() 
    {
        return $this->belongsToMany('tweeterapp\model\User','tweeterapp\model\Follow','follower','followee')->withPivot( ['follower', 'followee' ] );
    }

    //Fonction poir lier avec la table des followers
    public function followedBy() 
    {
        return $this->belongsToMany('tweeterapp\model\User','tweeterapp\model\Follow','followee','follower')->withPivot( ['follower', 'followee' ] );  
    }
}