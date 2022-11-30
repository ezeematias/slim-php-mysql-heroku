<?php

include_once("Entidades/Cliente.php");
include_once("Entidades/Pedido.php");
include_once("Entidades/PDF.php");


class PedidoAPI
{
    
    public function Alta($request, $response, $args)
    {
        try
        {
            $params = $request->getParsedBody();
            //var_dump($params);
            $cliente = new Cliente($params["cliente"]);
            $pedido = new Pedido();
            $pedido->id_mesa= $params["mesa"];
            $pedido->id_cliente =  Cliente::Alta($cliente);
            $pedido->id_usuario= $params["id_usuario"];
            $pedido->fecha_prevista = $params["estara_en"];
            $alta = Pedido::Alta($pedido);
            switch($alta)
            {
                case '1':
                    $respuesta = 'Pedido generado.';
                    break;
                case '0':
                    $respuesta = 'No se generó el pedido pues la mesa está ocupada';
                    break;   
                case '2':
                    $respuesta = 'Usuario inválido.';
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
            $idDelPedido = $args["id"];
            $modificacion = Pedido::Baja($idDelPedido);
            switch($modificacion)
            {
                case 0:
                    $respuesta = "No existe este pedido.";
                    break;
                case 1:
                    $respuesta = "Pedido borrado con éxito.";
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
            $pedido = new Pedido();
            $pedido->id = $params["idDelPedido"];
            $pedido->id_mesa = $params["nuevaMesa"];
            $pedido->id_usuario = $params["nuevoMozo"];
            $modificacion = Pedido::Modificacion($pedido);
            switch($modificacion)
            {
                case 0:
                    $respuesta = "Este ID no corresponde a ningún pedido.";
                    break;
                case 1:
                    $respuesta = "Mesa no disponible.";
                    break;
                case 2:
                    $respuesta = "Pedido modificado con éxito.";
                    break;
                case 3:
                    $respuesta = "No existe el empleado asignado.";
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
            $lista = AccesoDatos::ImprimirTabla('pedido', 'Pedido');
            $payload = json_encode(array("listaPedidos" => $lista));
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

    public function SubirFoto($request, $response, $args)
    {
        try
        {
            $params = $request->getParsedBody();
            $pedido = new Pedido();
            $pedido->id = $params["id"];
            $archivo = ($_FILES["archivo"]);
            $pedido->foto = ($archivo["tmp_name"]);
            $pedido->GuardarImagen();
            //var_dump($archivo);
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

    public function PasarAComiendo($request, $response, $args)
    {
        try
        {           
            $params = $request->getParsedBody();
            $pedido = $params["pedido"];
            Pedido::CambiarEstado($pedido, '2');
            $payload = json_encode("En la mesa están comiendo.");
            $response->getBody()->write($payload);
            $newResponse = $response->withHeader('Content-Type', 'application/json');

        }
        catch(Throwable $mensaje)
        {
            printf("Error al cambia el estado: <br> $mensaje .<br>");
        }
        finally
        {
            return $newResponse;
        }  
    }

    public function PasarAPagando($request, $response, $args)
    {
        try
        {           
            $params = $request->getParsedBody();
            $pedido = $params["pedido"];
            $respuesta = Pedido::CambiarEstado($pedido, '3');
            $payload = json_encode("Pagando. La cuenta es: ".$respuesta);
            $response->getBody()->write($payload);
            $newResponse = $response->withHeader('Content-Type', 'application/json');

        }
        catch(Throwable $mensaje)
        {
            printf("Error al cambia el estado: <br> $mensaje .<br>");
        }
        finally
        {
            return $newResponse;
        }  
    }

    public function CerrarPedido($request, $response, $args)
    {
        try
        {           
            $params = $request->getParsedBody();
            $pedido = $params["pedido"];
            Pedido::CambiarEstado($pedido, '4');
            $payload = json_encode("Mesa cerrada.");
            $response->getBody()->write($payload);
            $newResponse = $response->withHeader('Content-Type', 'application/json');

        }
        catch(Throwable $mensaje)
        {
            printf("Error al cambia el estado: <br> $mensaje .<br>");
        }
        finally
        {
            return $newResponse;
        }  
    }

    public function HacerPdf($request, $response, $args)
    {
        try
        {
            $params = $request->getParsedBody();
            $pedido = $params["pedido"];
            $lista = PDF::hacerPDF($pedido);
            $payload = json_encode(array("listaPedidosCerrados" => $lista));
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