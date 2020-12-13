<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',  'surname', 'description'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    
    public function posts()
    {
        /*
            Cuando llame esta fucion va a retornar todos
            los post del user que llame en particular de la 
            category que hago referencia

            Es como hacer una consulta.. !!Interesante
        */

        return $this->hasMany('App\Post');
        // Relacion de uno a muchos, sacame todos los post+
        // aue contengan el USUARIO que estoy llamando
    }
}
