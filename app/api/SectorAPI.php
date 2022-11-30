<?php
include_once("Entidades/Sector.php");

class SectorAPI
{

    public function Alta($request, $response, $args)
    {
        try
        {
            $params = $request->getParsedBody();
            $sector = new Sector();
            $sector->nombre = $params["nombre"];
            $alta = Sector::Alta($sector);
            switch ($alta)
            {
                case '0':
                    $respuesta = "Problemas creando el sector.";
                    break;
                case '1':
                    $respuesta = "Sector creado con éxito.";
                    break;
                case '2':
                    $respuesta = "El sector ya existía.";
                    break;
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
            $idDelSector = $args["id"];
            $modificacion = Sector::Baja($idDelSector);
            switch($modificacion)
            {
                case 0:
                    $respuesta = "No existe este sector.";
                    break;
                case 1:
                    $respuesta = "Sector borrado con éxito.";
                    break;
                case 2:
                    $respuesta = "No se puede borrar (Hay productos relacionados con este sector).";
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
            $sector = new Sector();
            $sector->id = $params["idDelSector"];
            $sector->nombre = $params["nuevoNombre"];
            $modificacion = Sector::Modificacion($sector);
            switch($modificacion)
            {
                case 1:
                    $respuesta = "Nombre de sector cambiado con éxito;";
                    break;
                case 2:
                    $respuesta = "El nombre ya existe en la base de datos.";
                    break;
                case 3:
                    $respuesta = "Este ID no corresponde a ningún sector";
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
            printf("Error al modifcar: <br> $mensaje .<br>");
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
            $lista = AccesoDatos::ImprimirTabla('sector', 'Sector');
            $payload = json_encode(array("listaSectores" => $lista));
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