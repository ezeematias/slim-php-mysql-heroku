<?php
//include_once("Entidades/Mesa.php");//anda en local
include_once("././Entidades/Mesa.php");

include_once("importexport/CSV.php");


class MesaAPI
{
    public function Alta($request, $response, $args)
    {
        try
        {
            $params = $request->getParsedBody();
            $mesa = new Mesa();
            $mesa->nombre = $params["nombre"];
            $alta = Mesa::Alta($mesa);
            switch($alta)
            {
                case 1:
                    $respuesta = "Mesa creada con éxito;";
                    break;
                case 2:
                    $respuesta = "Este nombre ya existe para una msa.";
                    break;
                case 3:
                    $respuesta = "Mesa dada de alta de nuevo.";
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
    public function Baja($request, $response, $args)
    {
        try
        {
            $idMesa = $args["id"];
            $modificacion = Mesa::Baja($idMesa);
            switch($modificacion)
            {
                case 0:
                    $respuesta = "No existe esta mesa.";
                    break;
                case 1:
                    $respuesta = "Mesa borrada con éxito.";
                    break;
                case 2:
                    $respuesta = "No se puede borrar (la mesa está en uso).";
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
            $mesa = new Mesa();
            $mesa->id = $params["idMesa"];
            $mesa->nombre = $params["nuevoNombre"];
            $modificacion = Mesa::Modificacion($mesa);

            switch($modificacion)
            {
                case 1:
                    $respuesta = "Nombre de mesa cambiado con éxito;";
                    break;
                case 2:
                    $respuesta = "Ese nombre para una mesa ya existe en la base de datos.";
                    break;
                case 3:
                    $respuesta = "Este ID no corresponde a ninguna mesa";
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
            printf("Error al dar de alta: <br> $mensaje .<br>");
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
            $lista = AccesoDatos::ImprimirTabla('mesa', 'Mesa');
            $payload = json_encode(array("listaMesas" => $lista));
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

    public function ExportarTabla($request, $response, $args)
    {
        try
        {
            CSV::ExportarTabla('mesa', 'Mesa', 'mesa.csv');
            $payload = json_encode("Tabla exportada con éxito");
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

    public function ImportarTabla($request, $response, $args)
    {
        try
        {
            $archivo = ($_FILES["archivo"]);
            //var_dump($archivo);
            Mesa::CargarCSV($archivo["tmp_name"]);
            $payload = json_encode("Carga exitosa.");
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