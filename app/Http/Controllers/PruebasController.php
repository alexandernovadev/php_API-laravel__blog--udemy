<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Se importan los modelos que vamos a utlizar,
// Para sacar la data

use App\Post;
use App\Category;


class PruebasController extends Controller
{
    public function index()
    {
        // Aqui pongo la data, que sera cargada
        // en la vista
        $titulo = 'Animales';
        $animales = ['Gato', 'Tigre', 'Perro'];

        return view('pruebas.index', array(
            'animales' => $animales,
            'titulo' => $titulo
        ));
    }

    public function testORM()
    {
        $posts = Post::all();
        //Select * post from post
        // Eso es lo que hace esa linea de arriba
        // Me va a devolver un array de datos.

        //Var_dump me permite ver el tipo de variable o dato que es
        // me salio un array con los datos, de tipo ELOQUENT
        // y un monton de metada
        
        //var_dump($posts);

        foreach($posts as $post)
        {
            // Esto es como un objto OJO con eso
            // de post quiero el title
            echo '<h1>'.$post->title.'</h1>';

            // WUAAAu
            // Vamos a traer la relacion
            // de la relacion user que tiene como Foraing key
            // traigame el nombre

            /* PAra hacer esto con SQL a pelo
            habria que primero traer el id del user en una
            consulta, despues hacer otra consulta a user con un where
            y especificar el id, y que me traiga en nombre
            
            Aqui solo hacemos esto, bueno hay que tener en cuanta
            que el metodo creado en el modelo es impresindible
            ya que le especifique la relacion que tiene la tabla
            
            */

            // ES IMPORTANTE TRAER EL MPDE
            
            echo "<span style='color: red;'>{$post->user->name} -- {$post->category->name}</span>";

            
            // Del post quiero el nombre del usuario que lo creo
            // como escribi arriba hay que hacer mas cosas con SQL puro
            // Aqui es asi de facil


            // echo '<p>'.$post->content.'</p>';
            // Se puede add info como Angular
            // No se si esto es de PHP 7
            // o de laravel. SOLO se puede con comilla doble

            // Efectivamente es de PHP 7
            echo "<p>{$post->content}</p>";


        }
        die();
    }
}

