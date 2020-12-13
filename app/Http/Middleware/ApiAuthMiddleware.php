<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\JwtAuth;

class ApiAuthMiddleware
{
   
    public function handle($request, Closure $next)
    {
        $token=$request->header('Authorization');

        $jwtAuth=new JwtAuth();
        $checkToken=$jwtAuth->checkToken($token);
        
        if($checkToken) 
        {
            return $next($request);
        }
        else
        {
            $data = array(
                'code'    => 400,
                'status'  => 'error',
                'mensaje' => 'El usuario NO ESTA LOGEADO'
            );
            return response()->json($data, $data['code']);
        }
    }
}
