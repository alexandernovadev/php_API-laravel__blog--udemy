<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    protected $table = 'posts';

    // Relacion de muchos a uno
    // Muchos post pueden ser creados por un usuario

    protected $fillable = [
        'title', 'content', 'image','user_id','category_id'
    ];


    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
        // Sacame los obejtos de usuario relacionado con 
        // el user_id 

        // Saceme el user que hizo el post
    }

    public function category()
    {
        return $this->belongsTo('App\Category' , 'category_id');
        // Sacame los obejtos de Category relacionado con 
        // el category_id  

        // Saceme el category del post
    }

}
