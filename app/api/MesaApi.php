<?php

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use Slim\Handlers\Strategies\RequestHandler;

require_once './db/AccesoDatos.php';
require_once "./models/Mesa.php";
require_once "./interfaces/IApiUsable.php";

class MesaApi extends Mesa implements IApiUsable
{

    //region ABM
    public function CargarUno($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arrayMesa = $ArrayDeParametros['mesa'];

        $mimesa = new Mesa();
        $mimesa->estado = $arrayMesa['estado'];
        $codigo = $mimesa->InsertarMesa();
        //Cargo el log
        if ($codigo) {
            $new_log = new Logger();
            $new_log->idEmpleado = $ArrayDeParametros['empleado'];
            $new_log->accion = "Cargar mesa";
            $new_log->InsertarLog();
            $payload = json_encode(array("mensaje: " => "Se ha ingresado la mesa, su codigo es $codigo", "status" => 200));
        }else {
            $payload = json_encode(array("mensaje: " => "No se ha ingresado la mesa", "status" => 400));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $codigo = $args['codigo'];
        $mimesa = new Mesa();
        $mimesa->codigo = $codigo;
        $cantidadDeBorrados = $mimesa->BorrarMesa();
        if ($cantidadDeBorrados > 0) {
            $new_log = new Logger();
            $new_log->idEmpleado = $ArrayDeParametros['empleado'];
            $new_log->accion = "Borrar Mesa";
            $new_log->InsertarLog();
            $payload = json_encode(array("<li>mensaje: " => "Mesa eliminado", "status" => 200));
        } else {
            $payload = json_encode(array("<li>mensaje: " => "Error al eliminar el Mesa", "status" => 400));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arraymodificar = $ArrayDeParametros['modificar'];
        $miMesa = new Mesa();
        $miMesa->estado = $arraymodificar['estado'];
        $miMesa->codigo = $arraymodificar['codigo'];
        $filasAfectadas = $miMesa->ModificarMesa();

        if ($filasAfectadas > 0) {
            $new_log = new Logger();
            $new_log->idEmpleado = $ArrayDeParametros['empleado'];
            $new_log->accion = "Modificar Mesas";
            $new_log->InsertarLog();
            $payload = json_encode(array("<li>mensaje: " => "Mesa modificado ", "status" => 200));
        }else {
            $payload = json_encode(array("<li>mensaje: " => "Error al modificar la mesa", "status" => 400));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $codigo = $args['codigo'];
        $Mesa = Mesa::TraerMesa($codigo);

        $data = (array)$Mesa;
        $payload = json_encode($data);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::TraerMesas();
        $payload = json_encode(Mesa::Listar($lista));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    //endregion ABM


    public function MetricasMesas($request, $response)
    {
        $empleado = Mesa::Metricas("","");
        Mesa::MostrarMetricas($empleado);
        $payload = json_encode("");
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


}
