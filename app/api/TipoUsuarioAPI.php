<?php
include_once("Entidades/TipoUsuario.php");

class TipoUsuarioAPI
{

    public function Alta($request, $response, $args)
    {
        try
        {
            $params = $request->getParsedBody();
            $tipoUsuario = new TipoUsuario();
            $tipoUsuario->nombre = $params["nombre"];
            $alta = TipoUsuario::Alta($tipoUsuario);
            if($alta > 0)
            { 
                $respuesta = "Tipo de usuario creado con éxito;";
            }
            else
            {
                $respuesta = "Problemas creando el tipo de usuario.";
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

    public function Baja($request, $response, $args)
    {
        try
        {
            //var_dump($args);
            $idDelTipo = $args["id"];
            $modificacion = TipoUsuario::Baja($idDelTipo);
            switch($modificacion)
            {
                case 0:
                    $respuesta = "No existe este tipo de usuario.";
                    break;
                case 1:
                    $respuesta = "Tipo de usuario borrado con éxito.";
                    break;
                case 2:
                    $respuesta = "No se puede borrar (Hay usuario relacionados con este sector).";
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


    public function Modificacion($request, $response, $args)
    {
        try
        {
            $params = $request->getParsedBody();
            $tipoUsuario = new TipoUsuario();
            $tipoUsuario->id = $params["idDelTipo"];
            $tipoUsuario->nombre = $params["nuevoNombre"];
            $modificacion = TipoUsuario::Modificacion($tipoUsuario);
            switch($modificacion)
            {
                case 1:
                    $respuesta = "Nombre de tipo de usuario cambiado con éxito;";
                    break;
                case 2:
                    $respuesta = "El nombre de tipo de usuario ya existe en la base de datos.";
                    break;
                case 3:
                    $respuesta = "Este ID no corresponde a ningún tipo de usuario";
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

    public function Listar($request, $response, $args)
    {
        try
        {
            $lista = AccesoDatos::ImprimirTabla('tipo_usuario', 'TipoUsuario');
            $payload = json_encode(array("lista" => $lista));
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