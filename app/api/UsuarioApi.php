<?php
//require_once("Entidades/Usuario.php");
include_once("Entidades/Usuario.php");
include_once("token/Token.php");


class UsuarioAPI
{
    public function Alta($request, $response, $args)
    {
        try
        {
            $params = $request->getParsedBody();
            //var_dump($params);
            $usuario = new Usuario();
            $usuario->dni = $params["dni"];
            $usuario->clave = $params["clave"];
            $usuario->tipo = $params["tipo"];
            $alta = Usuario::Alta($usuario);
            switch($alta)
            {
                case -1:
                    $respuesta = "Problema generando el alta;";
                    break;
                case 0:
                    $respuesta = "ERROR. No existe este tipo.";
                    break;
                case 1:
                    $respuesta = "El usuario ya existía en la BD. Se ha pasado activo si no lo estaba y se ha actualizado la información.";
                    break;
                case 2:
                    $respuesta = "Usuario creado con éxito.";
                    break;
                default:
                    $respuesta = "Nunca llega al alta";
            }    
            $payload = json_encode($respuesta);
            $response->getBody()->write($payload);
            $newResponse = $response->withHeader('Content-Type', 'application/json');
        }
        catch(Throwable $mensaje)
        {
            printf("Error al dar de alta: <br> $mensaje .<br>");
        }
        finally
        {
            return $newResponse;
        }
    }

    
    public function Modificacion($request, $response, $args)
    {
        try
        {
            $params = $request->getParsedBody();
            $usuario = new Usuario();
            $usuario->id = $params["id"];
            $usuario->tipo = $params["tipo"];
            $usuario->dni = $params["dni"];
            $usuario->clave = $params["clave"];
            $modificacion = Usuario::Modificacion($usuario);

            switch($modificacion)
            {
                case 1:
                    $respuesta = "Usuario modificado con éxito.";
                    break;
                case 2:
                    $respuesta = "El DNI ya existe en la base de datos.";
                    break;
                case 3:
                    $respuesta = "Este ID no corresponde a ningún usuario";
                    break;
                default:
                    $respuesta = "Nunca llega a la modificacion";
            }    
            $payload = json_encode($respuesta);
            $response->getBody()->write($payload);
            $newResponse = $response->withHeader('Content-Type', 'application/json');
        }
        catch(Throwable $mensaje)
        {
            printf("Error al modificar: <br> $mensaje .<br>");
        }
        finally
        {
            return $newResponse;
        }
    }

    public function Baja($request, $response, $args)
    {
        try
        {
            //var_dump($args);
            $idDelUsuario = $args["id"];
            $modificacion = Usuario::Baja($idDelUsuario);
            switch($modificacion)
            {
                case 0:
                    $respuesta = "No existe este Usuario.";
                    break;
                case 1:
                    $respuesta = "Usuario borrado con éxito.";
                    break;
                case 2:
                    $respuesta = "No se puede borrar (Tiene pedidos pendientes).";
                    break;
                default:
                    $respuesta = "Nunca llega a la modificacion";
            }    
            $payload = json_encode($respuesta);
            $response->getBody()->write($payload);
            $newResponse = $response->withHeader('Content-Type', 'application/json');
        }
        catch(Throwable $mensaje)
        {
            printf("Error al dar de baja: <br> $mensaje .<br>");
        }
        finally
        {
            return $newResponse;
        }
    }

    public function Login($request, $response, $args)
    {
        try
        {
            $params = $request->getParsedBody();
            $dni = $params["dni"];
            $clave = $params["clave"];
            $usuario = Usuario::Login($dni, $clave);
            //var_dump($usuario);
    
            if($usuario != null)
            {
                $token = Token::GenerarToken($usuario->id, $usuario->tipo);
                $respuesta = $token;
                //$respuesta = "Usuario logueado con exito";
            }
            else
            {
                $respuesta = "Credenciales incorrectas.";
            }
    
            $payload = json_encode($respuesta);
            $response->getBody()->write($payload);
            $newResponse = $response->withHeader('Content-Type', 'application/json');
        }
        catch(Throwable $mensaje)
        {
            printf("Error al loguearse: <br> $mensaje .<br>");
        }
        finally
        {
            return $newResponse;
        }
    }

    public function Listar($request, $response, $args)
    {
        try
        {
            $lista = AccesoDatos::ImprimirTabla('usuario', 'Usuario');
            $payload = json_encode(array("listaUsuarios" => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withHeader('Content-Type', 'application/json');
        }
        catch(Throwable $mensaje)
        {
            printf("Error al listar: <br> $mensaje .<br>");
        }
        finally
        {
            return $newResponse;
        }    
    }
}

?>