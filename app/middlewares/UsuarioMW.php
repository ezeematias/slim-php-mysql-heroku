<?php

include_once("token/Token.php");
include_once("db/AccesoDatos.php");

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class UsuarioMW
{
    public static function ValidarToken($request, $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $response = new Response();

        if(!empty($header))
        {
            $token = trim(explode("Bearer", $header)[1]);
            Token::verifyToken($token);
            $response = $handler->handle($request);
        }
        else
        {
            $response->getBody()->write(json_encode(array("Token error" => "No hay token.")));
            $response = $response->withStatus(401);
        }
        return  $response->withHeader('Content-Type', 'application/json');
    }

    public function ValidarMozo($request, $handler)
    {
        try 
        {
            $tipo = AccesoDatos::retornarObjetoActivoPorCampo('mozo', 'nombre', 'tipo_usuario', 'TipoUsuario');
            $header = $request->getHeaderLine('Authorization');
            if(!empty($header))
            {
                $token = trim(explode("Bearer", $header)[1]);
                $data = Token::verifyToken($token);
                if($data->tipo == $tipo[0]->id)
                {
                    return $handler->handle($request);
                }
                throw new Exception("Usuario no autorizado");
            }
            else
            {                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                throw new Exception("Token vacío");
            }
        } 
        catch (\Throwable $th) 
        {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "ERROR, ".$th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');;
        }
    }

    public function ValidarSocio($request, $handler)
    {
        try 
        {
            $tipo = AccesoDatos::retornarObjetoActivoPorCampo('socio', 'nombre', 'tipo_usuario', 'TipoUsuario');
            $header = $request->getHeaderLine('Authorization');
            if(!empty($header))
            {
                $token = trim(explode("Bearer", $header)[1]);
                $data = Token::verifyToken($token);
                if($data->tipo == $tipo[0]->id)
                {
                    return $handler->handle($request);
                }
                throw new Exception("Usuario no autorizado");
            }
            else
            {                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                throw new Exception("Token vacío");
            }
        } 
        catch (\Throwable $th) 
        {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "ERROR, ".$th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');;
        }
    }

    public function ValidarCervecero($request, $handler)
    {
        try 
        {
            $tipo = AccesoDatos::retornarObjetoActivoPorCampo('cervecero', 'nombre', 'tipo_usuario', 'TipoUsuario');
            $header = $request->getHeaderLine('Authorization');
            if(!empty($header))
            {
                $token = trim(explode("Bearer", $header)[1]);
                $data = Token::verifyToken($token);
                if($data->tipo == $tipo[0]->id)
                {
                    return $handler->handle($request);
                }
                throw new Exception("Usuario no autorizado");
            }
            else
            {                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                throw new Exception("Token vacío");
            }
        } 
        catch (\Throwable $th) 
        {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "ERROR, ".$th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');;
        }
    }

    public function ValidarBartender($request, $handler)
    {
        try 
        {
            $tipo = AccesoDatos::retornarObjetoActivoPorCampo('bartender', 'nombre', 'tipo_usuario', 'TipoUsuario');
            $header = $request->getHeaderLine('Authorization');
            if(!empty($header))
            {
                $token = trim(explode("Bearer", $header)[1]);
                $data = Token::verifyToken($token);
                if($data->tipo == $tipo[0]->id)
                {
                    return $handler->handle($request);
                }
                throw new Exception("Usuario no autorizado");
            }
            else
            {                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                throw new Exception("Token vacío");
            }
        } 
        catch (\Throwable $th) 
        {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "ERROR, ".$th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');;
        }
    }

    public function ValidarCocinero($request, $handler)
    {
        try 
        {
            $tipo = AccesoDatos::retornarObjetoActivoPorCampo('cocinero', 'nombre', 'tipo_usuario', 'TipoUsuario');
            $header = $request->getHeaderLine('Authorization');
            if(!empty($header))
            {
                $token = trim(explode("Bearer", $header)[1]);
                $data = Token::verifyToken($token);
                if($data->tipo == $tipo[0]->id)
                {
                    return $handler->handle($request);
                }
                throw new Exception("Usuario no autorizado");
            }
            else
            {                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                throw new Exception("Token vacío");
            }
        } 
        catch (\Throwable $th) 
        {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "ERROR, ".$th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');;
        }
    }

    public function ValidarRepostero($request, $handler)
    {
        try 
        {
            $tipo = AccesoDatos::retornarObjetoActivoPorCampo('repostero', 'nombre', 'tipo_usuario', 'TipoUsuario');
            $header = $request->getHeaderLine('Authorization');
            if(!empty($header))
            {
                $token = trim(explode("Bearer", $header)[1]);
                $data = Token::verifyToken($token);
                if($data->tipo == $tipo[0]->id)
                {
                    return $handler->handle($request);
                }
                throw new Exception("Usuario no autorizado");
            }
            else
            {                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                throw new Exception("Token vacío");
            }
        } 
        catch (\Throwable $th) 
        {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "ERROR, ".$th->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');;
        }
    }
 
}
?>


