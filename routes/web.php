<?php
use Illuminate\Routing\RouteAction;

Route::get('/post', 'PostController@pruebas');

// Vamos a utilizar post porque vamoa a


    // Ejemplos para aprender

    Route::get('/', function () {
        return view('welcome');
    });
    // Parametro ocpiones como angular con un ?
    // Para enviar un parametro es con {aqui parametro}

    // El parametro tiene que pasarse como parametroen la funcion
    Route::get('/pruebas/{nombre?}', function ($nombre = null)
    {
        /* ASI FUNCIONA SIN VISTA */
        // $texto = "Hola soy <br> Nombre: ";
        // $texto .= $nombre;
        // return $texto;
        /* Retorna Hola soy
        NOmbre: y pues aqui el name de la variable */

        // Pero ahora enviando data a una vista
            // Hay que crear una vista con el metodo view()
            // 1. parametro : Name de la vista
            // 2. parametro : Un array o un dato

        $texto = "Hola soy <br> Nombre: ";
        $texto .= $nombre;
        // Este es el name de la vista 'prueba'
        return view('prueba', array(
            'texto' => $texto
        ));
    });


    // Vaya a la ruta animales, y cargue la funcion index
    // del controlador llamado Pruebas controller

    // Como observamos este es otro mundo muy difirente
    // Yo acostumbrado a Angular :/
    // Pero hya vamos :D
    Route::get('/animales', 'PruebasController@index');


    // Ruta para ver el TestOrm
    Route::get('/test-orm', 'PruebasController@testORM');


    // Rutas API OFICAL

        /* Metodos HTTP

            * GET: Conseguir datos o recursos
            * POST: Guargar datos o recursos o hacer logica desde un form
            * PUT: Actualizar datos o recursos
            * DELETE: Eliminar datos o recursos
        
        El apiREST solo utiliza get y post
        EL apiRESTFUL utiliza todos los metodos
        */

    // En el Kernel hubo que comentar una linea
    // la linea 35 deñ kernel, que es de seguridad
    Route::post('api/register', 'UserController@register');

    Route::post('api/login', 'UserController@login');

    Route::put('api/user/update', 'UserController@update');

    Route::post('api/user/upload', 'UserController@upload')->middleware('api.auth');

    Route::get('api/user/avatar/{filename}', 'UserController@getImage');

    Route::get('api/user/detail/{id}', 'UserController@detail');

    //Rutas del controlador Categoria
    Route::resource('/api/category', 'CategoryController');
        // Este comando resource crea un listado de rutas ya preestablecidad
        // Para hacer todo el crud completo, -WOOOOOU

    // Rutas de controlador de POSTS

    Route::resource('/api/post', 'PostController');

    Route::post('api/post/upload', 'PostController@upload');

    Route::get('api/post/image/{filename}', 'PostController@getImage');

    Route::get('api/post/category/{id}', 'PostController@getPostByCategory');

    Route::get('api/post/user/{id}', 'PostController@getPostByUser');
