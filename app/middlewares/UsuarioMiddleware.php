<?php
require_once './models/Usuario.php';
require_once './models/Comanda.php';
require_once './utils/enums.php';

use GuzzleHttp\Psr7\Response;

class UsuarioMiddleware
{
    public function VerificarSocio($request, $handler)
    {
        $parametros = $request->getParsedBody();
        $usuario = Usuario::ObtenerPorUsuario($parametros['usuarioMW']);

        if ($usuario->id_tipo == TipoEmpleado::SOCIO->value) {
            $response = $handler->handle($request); 
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            $response = new Response();  
            $payload = json_encode(array("mensaje" => 'EL PERFIL DEL USUARIO NO ES SOCIO'));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::UNAUTHORIZED->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function VerificarMozo($request, $handler)
    {
        $parametros = $request->getParsedBody();
        $usuario = Usuario::ObtenerPorUsuario($parametros['usuarioMW']);
        if ($usuario->id_tipo == TipoEmpleado::MOZO->value) {
            $response = $handler->handle($request); 
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            $response = new Response();  
            $payload = json_encode(array("mensaje" => 'EL PERFIL DEL USUARIO NO ES MOZO'));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::UNAUTHORIZED->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function ListarPedidos($request, $handler)
    {
        $parametros = $request->getParsedBody();
        $usuario = Usuario::ObtenerPorUsuario($parametros['usuarioMW']);
        $rol = $parametros['rol'];

        switch($rol){
            case "Bartender":
                if($usuario->id_tipo == TipoEmpleado::BARTENDER->value){
                    $response = $handler->handle($request); 
                    $newResponse = $response->withStatus(HttpCode::OK->value);
                } else {
                    $response = new Response();  
                    $payload = json_encode(array("mensaje" => 'EL PERFIL DEL USUARIO NO ES BARTENDER'));
                    $response->getBody()->write($payload);
                    $newResponse = $response->withStatus(HttpCode::UNAUTHORIZED->value);
                }
                break;
            case "Cervecero":
                if($usuario->id_tipo == TipoEmpleado::CERVECERO->value){
                    $response = $handler->handle($request); 
                    $newResponse = $response->withStatus(HttpCode::OK->value);
                } else {
                    $response = new Response();  
                    $payload = json_encode(array("mensaje" => 'EL PERFIL DEL USUARIO NO ES CERVECERO'));
                    $response->getBody()->write($payload);
                    $newResponse = $response->withStatus(HttpCode::UNAUTHORIZED->value);
                }
                break;
            case "Cocina":
                if($usuario->id_tipo == TipoEmpleado::COCINERO->value){
                    $response = $handler->handle($request); 
                    $newResponse = $response->withStatus(HttpCode::OK->value);
                } else {
                    $response = new Response();  
                    $payload = json_encode(array("mensaje" => 'EL PERFIL DEL USUARIO NO ES COCINERO'));
                    $response->getBody()->write($payload);
                    $newResponse = $response->withStatus(HttpCode::UNAUTHORIZED->value);
                }
                break;
            case "CandyBar":
                if($usuario->id_tipo == TipoEmpleado::COCINERO->value){
                    $response = $handler->handle($request); 
                    $newResponse = $response->withStatus(HttpCode::OK->value);
                } else {
                    $response = new Response();  
                    $payload = json_encode(array("mensaje" => 'EL PERFIL DEL USUARIO NO ES COCINERO'));
                    $response->getBody()->write($payload);
                    $newResponse = $response->withStatus(HttpCode::UNAUTHORIZED->value);
                }
                break;
            default:
                $response = new Response();  
                $payload = json_encode(array("mensaje" => 'PERFIL DE USUARIO INEXISTENTE'));
                $response->getBody()->write($payload);
                $newResponse = $response->withStatus(HttpCode::UNAUTHORIZED->value);
                break;
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function AdministrarPedidos($request, $handler)
    {
        $parametros = $request->getParsedBody();
        $usuario = Usuario::ObtenerPorUsuario($parametros['usuarioMW']);
        $rol = $parametros['rol'];
        $id = $parametros['id_pedido'];
        $comanda = Comanda::ObtenerPorId($id);

        if($comanda)
        {
            switch($rol){
                case "Bartender":
                    if($usuario->id_tipo == TipoEmpleado::BARTENDER->value && $comanda->id_sector == Sector::BARRA->value){
                        $response = $handler->handle($request); 
                        $newResponse = $response->withStatus(HttpCode::OK->value);
                    } else {
                        $response = new Response();  
                        $payload = json_encode(array("mensaje" => 'EL PERFIL DEL USUARIO NO ESTA HABILITADO PARA ACCEDER A ESTA COMANDA'));
                        $response->getBody()->write($payload);
                        $newResponse = $response->withStatus(HttpCode::UNAUTHORIZED->value);
                    }
                    break;
                case "Cervecero":
                    if($usuario->id_tipo == TipoEmpleado::CERVECERO->value && $comanda->id_sector == Sector::CHOPERA->value){
                        $response = $handler->handle($request); 
                        $newResponse = $response->withStatus(HttpCode::OK->value);
                    } else {
                        $response = new Response();  
                        $payload = json_encode(array("mensaje" => 'EL PERFIL DEL USUARIO NO ESTA HABILITADO PARA ACCEDER A ESTA COMANDA'));
                        $response->getBody()->write($payload);
                        $newResponse = $response->withStatus(HttpCode::UNAUTHORIZED->value);
                    }
                    break;
                case "Cocina":
                    if($usuario->id_tipo == TipoEmpleado::COCINERO->value && $comanda->id_sector == Sector::COCINA->value){
                        $response = $handler->handle($request); 
                        $newResponse = $response->withStatus(HttpCode::OK->value);
                    } else {
                        $response = new Response();  
                        $payload = json_encode(array("mensaje" => 'EL PERFIL DEL USUARIO NO ESTA HABILITADO PARA ACCEDER A ESTA COMANDA'));
                        $response->getBody()->write($payload);
                        $newResponse = $response->withStatus(HttpCode::UNAUTHORIZED->value);
                    }
                    break;
                case "CandyBar":
                    if($usuario->id_tipo == TipoEmpleado::COCINERO->value && $comanda->id_sector == Sector::CANDYBAR->value){
                        $response = $handler->handle($request); 
                        $newResponse = $response->withStatus(HttpCode::OK->value);
                    } else {
                        $response = new Response();  
                        $payload = json_encode(array("mensaje" => 'EL PERFIL DEL USUARIO NO ESTA HABILITADO PARA ACCEDER A ESTA COMANDA'));
                        $response->getBody()->write($payload);
                        $newResponse = $response->withStatus(HttpCode::UNAUTHORIZED->value);
                    }
                    break;
                default:
                    $response = new Response();  
                    $payload = json_encode(array("mensaje" => 'PERFIL DE USUARIO INEXISTENTE'));
                    $response->getBody()->write($payload);
                    $newResponse = $response->withStatus(HttpCode::UNAUTHORIZED->value);
                    break;
            } 
        }
        else
        {
            $response = new Response();  
            $payload = json_encode(array("mensaje" => 'COMANDA INEXISTENTE'));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }           

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }
} 
