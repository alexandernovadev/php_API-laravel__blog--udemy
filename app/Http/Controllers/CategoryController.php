<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{

    public function __construct()
    {
        // $this->middleware('api.auth', ['except'=>['index', 'show']]);
        // Utiliza el middleware api.auth, exepto en el index y show

        // Buenas practicas, por Victor Robles
    }

    public function pruebas(Request $request)
    {
        return "Category controller";
    }

    public function index()
    {
        // Este metodo saca todas las categorias
        $categories = Category::all();

        return response()->json([
            'code'  => 200,
            'status' => 'success',
            'categories' => $categories
        ]);
    }

    public function show($id)
    {
        // Este metodo saca una categoria en especifico
        $category = Category::find($id);

        if(is_object($category))
        {

            return response()->json([
                'code'  => 200,
                'status' => 'success',
                'category' => $category
                ]);
        }
        else
        {
            return response()->json([
                'code'  => 404,
                'status' => 'error',
                'category' => 'La categoria no existe'
                ]);
        }

        return response()->json($data , $data['code']);
    }

    public function store(Request $request)
    {
        // Este metodo requiere autentficacion
        // en el contructor pongo un middleware

        // Recoger datos por POST
        $json = $request->input('json', null);
        $params_array = json_decode($json , true);

      
        if(!empty($params_array))
        {
              // Valida datos
            $validate = \Validator::make($params_array, [
            'name' =>'required'
             ]);


            //Guarda datos
            if ($validate->fails())
            {
                $data = [
                    'code'  => 400,
                    'status' => 'error',
                    'error' => $validate->errors()
                ];
            }
            else
            {
                $category = new Category();

                $category->name = $params_array['name'];
                $category->save();
                
                $data = [
                    'code'  => 200,
                    'status' => 'success',
                    'data' => $params_array
                ];
            }
        }
        else
        {
            $data = [
                'code'  => 400,
                'status' => 'error',
                'messgue' => 'No ha ni un dato'
            ];
        }
        return response()->json($data , $data['code']);
    }

    public function update($id , Request $request)
    {
        // Recoger los datos por POST
            $json = $request->input('json', null);
            $params_array = json_decode($json, true);

        // Validar los datos
        if(!empty($params_array))
        {
            $validate = \Validator::make($params_array ,[
                'name' => 'required'
            ]);
            
            if($validate->fails())
            {
                $data = [
                    'code'  => 400,
                    'status' => 'error',
                    'messgue' => $validate->errors()
                ];
            }
            // Quitar lo que no quiero actualizar

            unset($params_array['id']);
            unset($params_array['created_at']);

            // Actualizar el registro

            $category = Category::where('id', $id)->update($params_array);

            $data = [
                'code'  => 200,
                'status' => 'success',
                'messague' => $params_array
            ];
        }
        else
        {
            $data = [
                'code'  => 400,
                'status' => 'error',
                'messgue' => 'No hay ni un dato'
            ];
        }
        // Devolver los datos

        return response()->json($data , $data['code']);
    }
}
