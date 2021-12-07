<?php

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use Slim\Handlers\Strategies\RequestHandler;

require_once './db/AccesoDatos.php';
require_once "./models/Pedido.php";
require_once "./models/ProductoConsumido.php";
require_once "./interfaces/IApiUsable.php";

class PedidoApi extends Pedido implements IApiUsable
{
//region ABM
public function CargarUno($request, $response, $args)
{
    $ArrayDeParametros = $request->getParsedBody();
    $arraypedidos = $ArrayDeParametros['pedido'];
    $pedido = new Pedido();
    $pedido->idComanda = $arraypedidos['idComanda'];
    $pedido->sector = $arraypedidos['sector'];
    $pedido->idEmpleado = $arraypedidos['idEmpleado'];
    $pedido->descripcion = $arraypedidos['descripcion'];
    $pedido->estado = $arraypedidos['estado'];
    $pedido->fechaIngresado = $arraypedidos['fechaIngresado'];
    $pedido->estimacion = $arraypedidos['estimacion'];
    $pedido->codigo = $arraypedidos['codigo'];
    $id =    $pedido->InsertarPedido();

    if ($id) {
        $new_log = new Logger();
        $new_log->idEmpleado =  $ArrayDeParametros['empleado']['id'];
        $new_log->accion = "Insertar pedido unia";
        $new_log->InsertarLog();
    }
    $payload = json_encode(array("<li>mensaje: " => "", "status" => 200));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');    
}

    public function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
        $cantidadDeBorrados = Empleado::BorrarEmpleado($id);
        if ($cantidadDeBorrados > 0) {
            $new_log = new Logger();
            $new_log->idEmpleado = $id;
            $new_log->accion = "Borrar empleado";
            $new_log->InsertarLog();
            $payload = json_encode(array("<li>mensaje: " => "Empleado eliminado", "status" => 200));
        } else {
            $payload = json_encode(array("<li>mensaje: " => "Error al eliminar el empleado", "status" => 400));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $args['id'];
        $arraymodificar = $parametros['modificar'];

        $miempleado = new Empleado();
        $miempleado->estado = $arraymodificar['estado'];
        $miempleado->sector = $arraymodificar['sector'];
        $miempleado->puesto = $arraymodificar['puesto'];
        $miempleado->fechaIngreso = $arraymodificar['fechaIngreso'];
        $filasAfectadas = $miempleado->ModificarEmpleado($id);

        if ($filasAfectadas > 0) {
            $new_log = new Logger();
            $new_log->idEmpleado = $id;
            $new_log->accion = "Modificar empleados";
            $new_log->InsertarLog();
            $payload = json_encode(array("<li>mensaje: " => "Empleado modificado ", "status" => 200));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $empleado = Empleado::TraerEmpleado($id);
        $payload = json_encode(array("Empleado" => $empleado->toString()));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos2($request, $response, $args)
    {
        $lista = Empleado::TraerEmpleados();
        $payload = json_encode(array("Empleados" => Empleado::Listar($lista)));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::TraerProductos();
        $payload = json_encode(Empleado::Listar($lista));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    //endregion ABM

    //metricas empleado
    public function MetricasPedidos($request, $response)
    {
        $empleado = Pedido::Metricas();

        Pedido::MostrarMetricas($empleado);
        //	print_r($empleado);


        //$payload = json_encode(($empleado));
        $payload = json_encode("");
        $response->getBody()->write($payload);
        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public static function MostrarMetricas($listaAnalytics)
    {
        foreach ($listaAnalytics as $key => $value) {
            echo "<h1>$key</h1>";
            foreach ($value as $obj) {
                echo $obj->cantidad . " " . $obj->sector . "<br>";
            }
        }
    }
}
