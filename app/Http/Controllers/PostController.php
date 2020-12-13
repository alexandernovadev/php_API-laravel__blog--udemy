<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Helpers\JwtAuth;
use Illuminate\Http\Response;

class PostController extends Controller
{
    public function __construct()
    {
        // $this->middleware('api.auth', ['except' =>
        //                                     ['index',
        //                                      'show',
        //                                      'getImage',
        //                                      'getPostByUser',
        //                                      'getPostByCategory']]);
    }

    public function pruebas(Request $request)
    {
        return "Estoo es desde el controller de post";
    }

    public function getUser($request)
    {
        $jwauth = new JwtAuth();

        // conseguir el token
        $token = $request->header('Authorization', null);
        $user = $jwauth->checkToken($token, true);

        return $user;
    }

    public function index()
    {
        $posts = Post::all()->load('category')->load('user');
        print $post;
        // Con load Category, carga la categoria y la mete al JSON
        // GRACIAS A LA MAGIA DE ORM
        
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'posts' =>$posts

        ],200);
    }

    public function show($id)
    {
        $post = Post::find($id)->load('category')->load('user');
       
        if(is_object($post))
        {
            $data = [
                'code' => 200,
                'status' => 'success',
                'posts' => $post
            ];
        }
        else
        {
            $data = [
                'code' => 404,
                'status' => 'error',
                'messague' => "El id   $id   no se encuentra"
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function store(Request $request)
    {
        $json = $request->input('json' , null);
        $params_array = json_decode($json, true);

        if(!empty($params_array))
        {
            // CONSEGUIR USER AUTENTICADO 

        
            $user = $this->getUser($request);

            // Validar datos

                $validate = \Validator::make($params_array,[
                    'title' => 'required',
                    'content' => 'required',
                    'category_id' => 'required',
                    'image' => 'required'
                ]);

                if($validate->fails())
                {
                    $data = [
                    'code' => 400,
                    'status' => 'error',
                    'messague' => 'Error al guardar datos',
                    'error' => $validate->errors()
                    ];
                }
                else
                {
                    $post = new Post();
                    $post->user_id = $user[0]->sub;
                    $post->category_id = $params_array['category_id'];
                    $post->title = $params_array['title'];
                    $post->content = $params_array['content'];
                    $post->image = $params_array['image'];

                    $post->save();

                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'messague' => 'Post Guardado',
                        'post' => $post
                        ];
                }   
        }
        else
        {
            $data = [
                'code' => 400,
                'status' => 'error',
                'messague' => 'No ha enviado Informacion'
                ];
        }
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request)
    {
        // Recojer datos por POST
        $json = $request->input('json', null);
        $params_array = json_decode($json , true);

        if(!empty($params_array))
        {

            // Validar los datos
            $validate = \Validator::make($params_array,[
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
            ]);

            if($validate->fails())
            {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'messague' => 'No ha enviado Informacion',
                    'error' => $validate->errors()
                    ];
            }
            else
            {
                // Eliminar los que no se quiere actualizar
                unset($params_array['id']);
                unset($params_array['user_id']);
                unset($params_array['created_at']);
                unset($params_array['user']);
                // Actualizar Datos

                $comprobacionpostuser = $this->checkUserPropiety($request , $id);

                if($comprobacionpostuser)
                {
                        Post::where('id', $id)->first()->update($params_array);
                    // updatedOrCreate : Nos sirve para crear un objeto completo con 
                    // los datos actualizados
                  
                    $postupdated = Post::where('id', $id)->get();
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'messague' => 'Post Actualizado',
                        'post' =>  $postupdated[0],
                        'changues' => $params_array
                        ];
                }  
                else
                {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'messague' => 'No tienes permisos, porque no eres el dueño de post',
        
                        ];
                }

                //->updateOrCreate($params_array);
                // updatedOrCreate : Nos sirve para crear un objeto completo con 
                // los datos actualizados


            }
        }
        else
        {
            $data = [
                'code' => 400,
                'status' => 'error',
                'messague' => 'No ha enviado Informacion'
                ];
        }

        // Devolver Algop
        return response()->json($data , $data['code']);
    }
    
    public function destroy($id, Request $request)
    {
        $comprobacionpostuser = $this->checkUserPropiety($request , $id);

        if($comprobacionpostuser)
        {
            // Conseguir el registro existente
            $post = Post::find($id);

            if (!empty($post))
            {
                // Borrarlo
                $post->delete();

                // Devolver RTA
                $data = [
                'code' => 200,
                'status' => 'success',
                'messague' => 'Post Eliminado',
                'post' => $post
                ];
            }
            else
            {
                // Devolver RTA
                $data = [
                'code' => 400,
                'status' => 'error',
                'messague' => 'Post no se encuentra',
                ];
            }
        }  
        else
        {
            $data = [
                'code' => 400,
                'status' => 'error',
                'messague' => 'No tienes permisos, porque no eres el dueño de post'
            ];
        }
        
        return response()->json($data , $data['code']);
    }

    public function checkUserPropiety($request , $id)
    {
        // Este metodo comprueba que tu eres el propietario
        // del post, para poder editar o eliminar
        
        $user = $this->getUser($request);

        $post = Post::where('id', $id)
                    ->where('user_id' , $user[0]->sub)->get();

        if(!empty($post[0]))
        {
            // return "Eres el propietario de post";
            return true;  
        }
        else
        {
            // return "NOOOOOOO HP NO Eres el propietario de post";
            return false;  
        }
    }

    public function upload(Request $request)
    {
        $image = $request->file('file0');

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
                \Storage::disk('images')->put($image_name, \File::get($image));
                $data = array(
                    'code'    => 200,
                    'status'  => 'success',
                    'mensaje' => 'Imagen subida',
                    'nameimage'=> $image_name
                );
                
            }

        return response()->json($data, $data['code']);
    }

    public function getImage($filename)
    {

        // Comprobar si existe la imagen
        $isset = \Storage::disk('images')->exists($filename);

        if ($isset)
        {
            // Seleciona la images
            $file = \Storage::disk('images')->get($filename);
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

    public function getPostByCategory($id)
    {
        $post = Post::where('category_id' , $id)->get();

        return response()->json([
            'status' => 'success',
            'post' => $post
        ],200);
    }

    public function getPostByUser($id)
    {
        $postold = Post::where('user_id' , $id)->get();

        $post = array();

        // Hace Consulta
            // Segun los post que halla obtenido de usuario
            // Agreguele info de la categoria y el Uuario
        foreach ($postold as $p ) {
            $ide = Post::find($p['id'])->load('user')->load('category');
            array_push($post, $ide);
        }
       // Hace Consulta

        //  $post = Post::find($id);
        return response()->json([
            'status' => 'success',
            'post' => $post
        ],200);
    }
}
