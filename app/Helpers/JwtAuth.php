<?php 

namespace App\Helpers; // Nombramos la clase, o el paquete
use Firebase\JWT\JWT;// Clase de JWT para utilizar los metodos de la libreria
use Illuminate\Support\Facades\DB;// Para hacer consultar a BASE DE DATOS

use App\User;

class JwtAuth
{

    // Hay que crear una Key, para cifrar la JWT
    public $llave;
    
    public function __construct()
    {
        $this->llave= "Esto_es_una_llave_que_solo_va_en_el_backend_515151";
    }

    // Para que sirva este Helper, hay que crear un provider con PHPARTISAN
    // El cual se creo ya y se llama JwtAuthServiceProvider
    // y meter este Helper en el metodo de register
    public function singup($email, $password, $getToken = null)
    {

        // Buscar User con su credencial
            // Para comprobar las credenciales lo vamos a hacer con
            // $email, $password 
            // que las voy a meter en los parametros de singUP

            // Vamos a hacer una consulta

            // var_dump($this->llave); die();

            $user = User::where([
                'email' => $email,
                'password' => $password
            ])->first();// El firts dice : Solo sacame un objeto.


                // return $email.'-----'.$password.'--------|'.$user;
        // Comprobar si son correctas-> no return a Objeto

            // Voy a crear una variable para comprobar si esta Loggeado

            $singup = false;

            if(is_object($user))
            {
                // Si el usuario es de arriba es un obejo
                // Significa que la consulta en valida
                $singup = true;
            }


        // Generar el Tpken con lo datos de usuario

        if ($singup)
        {
            // Si esta validados los datos then

            unset($user['created_at']);
            unset($user['updated_at']);
            
            $token = array([
                'sub'      =>  $user['id'],
                'email'    =>  $user['email'],
                'name'     =>  $user['name'],
                'surname'  =>  $user['surname'],
                'userall'  =>  $user,
                'iat'      =>  time(),
                'exp'      =>   time() + (7 * 24 * 60 * 60)
            ]);
            // Se crea el Token con datos del usuario
            // iat : Tiempo en que creo el token
            // exp : Tiempo de duracion en este caso Un semana creo?

            $jwt = JWT::encode($token, $this->llave, 'HS256');
            $decoded = JWT::decode($jwt, $this->llave, array('HS256'));

            // Devolver los datos Decoficados, o el token en funcion de un Parametro
            // Si el tercer parametro no es nulo decodifica el token , con la unica
            // llave que esta en este codigo y devuelve un objeto ya hecho ya decodificado
            // el usuario con los datos, para eso sirve en decode 
            // MUY MUY INTERESANTE ESTA TECNICA DE JWT 
            
            if (is_null($getToken)) 
            {
               $data = $jwt;
               // Si el token en NULL me decuelve el token
            }
            else
            {
                $data = $decoded;
                // Si no el nullo devuelva en objeto decodificado
            }
        }
        else
        {
            $data = array(
                'status'   => 'error',
                'message'  => 'Login Incorrecto.'
            );  
        }
        return $data;
    }

    public function checkToken($jwt, $getidentity = false)
    {
        $auth = false;

        $comilla = '"';
        try 
        {
            $jwt =  str_replace($comilla,'',$jwt);
            // remplaze " por nada '', en la variable $jwt
            $decoded = JWT::decode($jwt, $this->llave,  ['HS256']);
        }
        catch (\UnexpectedValueException $e) 
        {
            $auth = false;
        }
        catch (\DomainException $e)
        {
            $auth = false;
        } 

        if ((!empty($decoded)) && is_object($decoded[0]) && isset($decoded[0]->sub)) 
        {
            // Si existe decoded, si es un object y si decoded->sub, ose si el id esxiste
            $auth = true;
        }
        else
        {
            $auth = false;
        }
        if($getidentity)
        {
            return $decoded;
        }
        return $auth;
    }
}
?>