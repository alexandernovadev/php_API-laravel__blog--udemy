<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
use App\Helpers\JwtAuth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function pruebas(Request $request)
    {
        return "User controller";
    }

    public function register (Request $request)
    {

        // Asi es como obtengo un valor de un
        // formulario por Post
        // $name = $request->input('raro');

        // Vamos a validar en capas

        // 1. Recoger los datos del usuario  por post

            // Se recogen por JSON, el formulario los
            // envia, me imagino que hace un OBjeto de Javascritp 
            // Para enviarlos al Laravel

            
            $json = $request->input('json',null);
            // Si no me llega el json ponga null
                // var_dump($json);
                // die();

            // Vamos a decodificar Datos
            
            // con json_decode lo convierto a ojeto
            // si pongo como segundo parametro true
            // me devuelve un array

            $params =  json_decode($json , true); 


        // 2. Validar datos

            // Se va a utilizar un metodo de laravel para 
            // validar datos, sin embargo habia leido que 
            // mejor validar info en el frontend.


            // Utilizo \Validator::make, el primer parametro
            // es el array a validar y el segundo la relas
            // que estas reglas estan en la doc de laravel

        if(!empty($params))
        {

            // Limpiar datos de espacios en blanco
            // array_map(trim, ..) Es una funcion de PHP
            // que me permite recorrer el Array Y Limpiar de 
            // espacios en blanco
            $params = array_map('trim',$params);

            // cabe resaltar que toca especificar el campo
            // como esta en el array
            $validate = \Validator::make ($params, [
                'name'     => 'required|alpha',
             
                'email'    => 'required|email|unique:users',
                'password' => 'required'

            ]);

              // 4. Comprobar si el usuario existe (duplicado)
                // Con unique:users le digo, verifique que en la tabla
                // users no hay un email igual, si hay uno igual vote error


            // Vamos a comprobar que no salgan errores

            if ($validate->fails())
            {
                // Si validatoR fALLA HAGA ESTO
             $data = array(
                    'code'    => 400,
                    'status'  => 'error',
                    'message' => 'Hay errores en la validacion',
                    'nameerror'  => $validate->errors()
                );

            }
            else
            {
                // 3. Cifrar la password

                //$pwc = password_hash($params['password'], PASSWORD_BCRYPT, ['cost'=> 4]);
                // Cifre el campo passdword con bycryp que es una cifrado de PHP
                // y cifrelo 4 Veces ??? No se de eso, pero es asi 

                $pwc = hash('sha256', $params['password']);
                // Toco utilizar este cifrado, porque siempre genera el mismo pass
                // cifrado ¿, en cambio el de arriba cifrada diferente siempre

                // 5. Crear el usuario

                // Creo una instacia del Objeto y le asigno valores
                $user = new User();
                $user->name     = $params['name'];
                $user->surname  = $params['surname'];
                $user->email    = $params['email'];
                $user->role     = 'ROLE_USER';
                $user->password = $pwc;
                
                // Guardo en la base de datos
                // Asi de facil, sin necesidad de INSTERT BLA VLA BLA
                // nononono NADA ESO SOLO ASI

                $user->save();
                        
                $data = array(
                    'code'    => 200,
                    'status'  => 'success',
                    'message' => 'Usuario Creado Satisfactoriamente',
                    'user'  =>$user
                );

            }
        }
        else
        {
            $data = array(
                'code'    => 400,
                'status'  => 'errore',
                'message' => 'Los Datos no son validos'
            );
        }

        // return "Hola como estas : $name ";

        // Un apiREST siempre tiene que devolver em JSON
        // Ahora un response devuelve la data y una 
        // code, los cuales son codes http
        return response()->json($data, $data['code']);
    }

    public function login(Request $request)
    {
        $jwtAuth = new JwtAuth;

        // Recibir datos por post
            $json = $request->input('json', null);
            $params = json_decode($json, true);

            // return $params['email'].'-'. $params['password'];
            // die();

        // Validar Post
            $validate = \Validator::make ($params, [
                'email'    => 'required|email',
                'password' => 'required'

            ]);

            if ($validate->fails())
            {
                // Si validatoR fALLA HAGA ESTO
                $singup = array(
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => 'Error al Loggearsr',
                    'error'   => $validate->errors()
                );  
            }

        // Cr4ifrar Password
            else
            {
                $pwc = hash('sha256',$params['password']);                
                $singup = $jwtAuth->singup($params['email'],$pwc);

                if(!empty($params['getToken']))
                {
                    $singup = $jwtAuth->singup($params['email'],$pwc, true);
                    $singup = $singup[0];
                }
            }
        // Devolver Token  o datos

        return response()->json( $singup, 200);
    
    }

    public function update(Request $request)
    {
        // Comprobar si el usuario esta autentificado
        $token=$request->header('Authorization');

        $jwtAuth=new JwtAuth();
        $checkToken=$jwtAuth->checkToken($token);

        // Recoger datos por post

        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if($checkToken && !empty($params_array)) 
        {
            // Si esta loggeado Actuliza datos

            // Traer el objeto 

            $user = $jwtAuth->checkToken($token, true);

            // validar datos

            $validate = \Validator::make($params_array,[
                'name'     => 'required|alpha',
                'surname'  => 'required|alpha',
                'email'    => 'required|email|unique:users,'.$user[0]->sub
            ]);
                // la validacion de Email, dice que puede actualizar 
                // un email, asi deje el mismo email

            // Quitar campos que no quiero UPDATE

            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);

            // Actualizar en user en la bbddd

            $userupdate = User::where('id', $user[0]->sub)->update($params_array);
            
            // ESto es para enviar el nuevo objeto actualizado
            // PORQUE VICTOR SE ME HACE QUE HACE UN MONTON DE CODIGO
            // PARA ACTUALIZAR EL USUARIO
            if($userupdate)
            {
                $usernew = User::where('id', $user[0]->sub)->get();
                $usernew[0]['password'] = null;
                unset($usernew[0]['updated_at']);
                unset($usernew[0]['remember_token']);
                unset($usernew[0]['created_at']);
            }

            // Devolver array con resultado

            $data = array(
                'code'    => 200,
                'status'  => 'success',
                'user' => $usernew[0],
                'changes' => $params_array
            );
        }
        else
        {
            $data = array(
                'code'    => 400,
                'status'  => 'error',
                'mensaje' => 'El usuario no esta identicado'
            );

        }
 
        return response()->json($data, $data['code']);
    }

    public function upload(Request $request)
    {
        // Se creo un middleware, que es un filtro
        // que hace antes de entrar a una ruta
        // ese middelware, comprueba si esta autenticado
        // se add a rautesmidleware de KERNEL
         
        // el midleware se utiliza en la parte de las rutas


        // Recoger los datos de la peticion

            $image = $request->file('file0');
            // recojo la imagen, se llama file0


            // Valida imagers

            $validate = \Validator::make($request->all(),[
                'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
            ]);

        // Guardar o Subir IMG(crear disco virtual)

            if (!$image || $validate->fails())
            {
                $data = array(
                    'code'    => 400,
                    'status'  => 'error',
                    'mensaje' => 'Error al subir la imagen'
                );
            }
            else
            {
                $image_name = time().$image->getClientOriginalName();
                // Le agrego un name a img, time y concatena el name original
                // time es el time exacto de el momento



                // Llamo el storage de Laravel
                // Se agrega una carpeta llamada user
                // voy a config y la agrego
                
                \Storage::disk('users')->put($image_name, \File::get($image));
                // Aqui up save la img, en el put recibo dos parametros
                // el name de la image y con el metodp FILE consigo el 
                // archivo de la imagen

                // Devolver el Resultado
                $data = array(
                    'code'    => 200,
                    'status'  => 'success',
                    'mensaje' => 'Imagen subida',
                    'imagename' => $image_name 
                );
                
            }
        


        return response()->json($data, $data['code']);
    }

    public function getImage($filename)
    {

        // Comprobar si existe la imagen
        $isset = \Storage::disk('users')->exists($filename);

        if ($isset)
        {
            // Seleciona la images
            $file = \Storage::disk('users')->get($filename);
            return new Response($file , 200);
        }
        else
        {
            $data = array(
                'code'    => 404,
                'status'  => 'error',
                'mensaje' => 'Imagen No encontrada'
            );
            return response()->json($data, $data['code']);
        }
    }

    // Obtener datos de Usuario

    public function detail($id)
    {
        $user = User::find($id);

        if (is_object($user))
        {
            $data = array(
                'code'    => 200,
                'status'  => 'success',
                'user'    => $user
            );

        }
        else
        {
            $data = array(
                'code'    => 400,
                'status'  => 'error',
                'messague'    => 'usuario no Encontrado'
            );
        }

        return response()->json($data , $data['code']);
    }
}
