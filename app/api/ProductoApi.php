<?php
include_once("Entidades/Producto.php");

class ProductoAPI
{
    public function Alta($request, $response, $args)
    {  
        try
        {
            $params = $request->getParsedBody();
            //var_dump($params);
            $producto = new Producto();
            $producto->id_sector = $params["sector"];
            $producto->nombre= $params["nombre"];
            $producto->precio= $params["precio"];
            $alta = Producto::Alta($producto);
            switch($alta)
            {
                case -1:
                    $respuesta = "Problema generando el alta.";
                    break;
                case 0:
                    $respuesta = "ERROR. No existe este sector.";
                    break;
                case 1:
                    $respuesta = "El producto ya existía en la BD. Se ha pasado activo si no lo estaba y se ha actualizado la información.";
                    break;
                case 2:
                    $respuesta = "Producto creado con éxito.";
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
            //var_dump($args);
            $idDelProducto = $args["id"];
            $modificacion = Producto::Baja($idDelProducto);
            switch($modificacion)
            {
                case 0:
                    $respuesta = "No existe este producto.";
                    break;
                case 1:
                    $respuesta = "Producto borrado con éxito.";
                    break;
                default:
                    $respuesta = "Nunca llega a la baja";
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
            $producto = new Producto();
            $producto->id = $params["idDelProducto"];
            $producto->id_sector = $params["idDelSector"];
            $producto->nombre = $params["nuevoNombre"];
            $producto->precio = $params["nuevoPrecio"];
   
            $modificacion = Producto::Modificacion($producto);
            switch($modificacion)
            {
                case 3:
                    $respuesta = "El id introducido no existe.";
                    break;
                case 2:
                    $respuesta = "El nombre ya existe.";
                    break;
                case 1:
                    $respuesta = "Producto modificado con éxito.";
                    break;
                default:
                    $respuesta = "Nunca llega a la modificacion.";
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
            $lista = AccesoDatos::ImprimirTabla('producto', 'Producto');
            $payload = json_encode(array("listaProductos" => $lista));
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