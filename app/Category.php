<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //Hay por obligacion crear como un "apuntador"
    // hacia la tabla, en este caso, la tabla de llama
    // categorias.
    // En laravel dice que tiene que llevar el protected y la variable
    // llama $table ..  Se podra cambia ==

    protected $table = 'categories';
    // Tiene que ser como esta en la base de datos

    /* Ahora hay que especificar la relacion
    en este case de Uno a mucho

    Una categoria tiene muchos post

    pero no un post tiene muchas categorias. o si ? NO un post
    solo tiene una categoria, a no ser que sean como tags ¡?

    En fin aqui esta que solo tenga muchos

    Para lograr todo esta hay que crear una funcion*/

    public function post()
    {
        /*
            Cuando llame esta fucion va a retornar todos
            los post de esta categoria en particular de la 
            category que hago referencia

            Es como hacer una consulta.. !!Interesante
        */

        return $this->hasMany('App\Post');
        // Relacion de uno a muchos, sacame todos los post+
        // aue contengan la categoria que estoy llamando
    }
}
