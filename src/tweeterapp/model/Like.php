<?php
namespace tweeterapp\model;

class Like extends \Illuminate\Database\Eloquent\Model {

    protected $table      = 'like';  /* le nom de la table */
    protected $primaryKey = 'id';     /* le nom de la clé primaire */
    public    $timestamps = false;    
}