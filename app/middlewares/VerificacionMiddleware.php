<?php
require_once './models/AutentificadorJWT.php';
require_once './models/Usuario.php';
require_once './models/Empleado.php';

use GuzzleHttp\Psr7\Response;

class VerificacionMiddleware
{

    //region Empleados
    public static function VerificarAdmin($request, $handler)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arrayUsuario = $ArrayDeParametros['usuario'];
        $esAdmin = Usuario::obtenerUsuario($arrayUsuario['usuario']);
        if ($esAdmin->perfil == "admin") {
            $response = $handler->handle($request);
            $payload = json_encode(array(
                "<li>Perfil: " => "Acceso habilitado",
                "status" => 200
            ));
        } else {
            $payload = json_encode(array(
                "Perfil" => 'No tienes habilitado el acceso.',
                "status" => 400
            ));
        }
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarEmpleado($request, $response)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arrayempleado = $ArrayDeParametros['empleado'];
        $empleado = Empleado::TraerEmpleado($arrayempleado['id']);
        if ($empleado->sector == "barra" || $empleado->sector == "cerveza" || $empleado->sector == "cocina" || $empleado->sector == "candy" || $empleado->sector == "admin") {         
            $payload = json_encode(array(
                "mensaje: " => "El empleado pertenece al sector " . $empleado->sector,
                "status" => 200
            ));
        } else {
            $response = new Response();    
            $payload = json_encode(array(
                "mensaje" => 'Solo empleados',
                "status" => 401
            ));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarMozo($request, $handler)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arrayempleado = $ArrayDeParametros['empleado'];
        $empleado = Empleado::TraerEmpleado($arrayempleado['id']);
        $response = $handler->handle($request); 
        if ($empleado->puesto == "mozo") {
            $payload = json_encode(array(
                "mensaje" => "El empleado es mozo",
                "status" => 200
            ));      
        } else {
            $response = new Response();    
            $payload = json_encode(array(
                "mensaje" => 'Solo mozos',
                "status" => 401
            ));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    public function VerificarSocio($request, $handler)
    {
        $parametros = $request->getParsedBody();
        $usuario = Usuario::obtenerUsuario($parametros['socio']['user']);
        $response = $handler->handle($request); 
        if ($usuario->perfil == "socio" && $usuario->fechaBaja== NULL)
         {
            $payload = json_encode(array("mensaje" => "Usuario con perfil socio", "status" => 200));  
        } else {
            $response = new Response();  
            $payload = json_encode(array("mensaje" => 'El perfil del usuario no es socio',"status" => 401));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    //endregion Empleados

    //region Token
    public static function ValidarToken($request, $handler)
    {
        //Tengo que meter el token en Authorization -> Bearer token -> token
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $esValido = false;
        try {
            AutentificadorJWT::VerificarToken($token);
            $esValido = true;
        } catch (Exception $e) {
            $payload = json_encode(array(
                'error' => $e->getMessage()
            ));
        }
        if ($esValido) {
            $response = $handler->handle($request);
            $payload = json_encode(array('Token valido'));
        } else {
            $response = new Response();
            $payload = json_encode(array(
                'Token invalido'
            ));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function DevolverDatos($request, $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        try {
            $response = $handler->handle($request);
            $data = AutentificadorJWT::ObtenerData($token);
            $payload = json_encode(array(
                'datos token ' => $data
            ));
        } catch (Exception $e) {
            $payload = json_encode(array(
                'error token' => $e->getMessage()
            ));
        }
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function DevolverPayLoad($request, $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $response = $handler->handle($request);
        try {
            $payload = json_encode(array(
                'payload' => AutentificadorJWT::ObtenerPayLoad($token)
            ));
        } catch (Exception $e) {

            $payload = json_encode(array(
                'error payload' => $e->getMessage()
            ));
        }
        $response->getBody()
            ->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    //endregion token

} //class
