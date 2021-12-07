<?php

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use Slim\Handlers\Strategies\RequestHandler;

require_once './db/AccesoDatos.php';
require_once "./models/Producto.php";
require_once "./interfaces/IApiUsable.php";

class ProductoApi extends Producto implements IApiUsable
{

    public function CargarUno($request, $response, $args)
    {        
        $ArrayDeParametros = $request->getParsedBody();
        $arrayProducto = $ArrayDeParametros['producto'];

        foreach ($arrayProducto as $producto) {
            $miProducto = new Producto(); 
            $miProducto->nombre = $producto['nombre'];
            $miProducto->tipo = $producto['tipo'];
            $miProducto->precio = $producto['precio'];
            $idProducto = $miProducto->InsertarProducto();
        }

        if ($idProducto != -1) {
            $payload = json_encode(array("mensaje: " => "Se ha ingresado el Producto", "status" => 200));
        } else {
            $payload = json_encode(array("mensaje: " => "No se ha ingresado el Producto", "status" => 404));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
        $cantidadDeBorrados = Producto::BorrarProducto($id);

        if ($cantidadDeBorrados > 0) {
            $new_log = new Logger();
            $new_log->idEmpleado = $id;
            $new_log->accion = "Borrar Producto";
            $new_log->InsertarLog();
            $payload = json_encode(array("<li>mensaje: " => "Producto eliminado", "status" => 200));
        } else {
            $payload = json_encode(array("<li>mensaje: " => "Error al eliminar el Producto", "status" => 400));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $id = $args['id'];
        $arraymodificar = $ArrayDeParametros['modificar'];

        $miProducto = new Producto();
        $miProducto->id = $id;
        $miProducto->nombre = $arraymodificar['nombre'];
        $miProducto->tipo = $arraymodificar['tipo'];
        $miProducto->precio = $arraymodificar['precio'];  
        $filasAfectadas = $miProducto->ModificarProducto($id);

        if ($filasAfectadas > 0) {
            $new_log = new Logger();
            $new_log->idEmpleado = $id;
            $new_log->accion = "Modificar Productos";
            $new_log->InsertarLog();
            $payload = json_encode(array("<li>mensaje: " => "Producto modificado ", "status" => 200));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $Producto = Producto::TraerProducto($id);
        $Producto->available ? $Producto->available = "DISPONIBLE" : $Producto->available = "NO DISPONIBLE";
        $data = (array)$Producto;
        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::TraerProductos();
        $payload = json_encode(Producto::Listar($lista));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    //endregion ABM
}
