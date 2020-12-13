<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class JwtAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path().'/Helpers/JwtAuth.php';
        // Ahora hay que add en la carpeta de config 
        // en el fichero app
        /*
          'JwtAuth' => App\Helpers\JwtAuth::class,
          add esta linea es aliases 

          y

           App\Providers\JwtAuthServiceProvider::class,
           esta en provider
        */
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
