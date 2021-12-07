<?php

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use Slim\Handlers\Strategies\RequestHandler;

require_once './db/AccesoDatos.php';
require_once "./models/Empleado.php";
require_once "./models/Pedido.php";
require_once "./interfaces/IApiUsable.php";

class EmpleadoApi extends Empleado implements IApiUsable
{

	//region ABM
	public function CargarUno($request, $response, $args)
	{
		$ArrayDeParametros = $request->getParsedBody();
		$arrayEmpleado = $ArrayDeParametros['empleado'];

		$miempleado = new Empleado();
		$miempleado->estado = $arrayEmpleado['nombre'];
		$miempleado->estado = $arrayEmpleado['estado'];
		$miempleado->sector = $arrayEmpleado['sector'];
		$miempleado->puesto = $arrayEmpleado['puesto'];
		$miempleado->fechaIngreso = $arrayEmpleado['fechaIngreso'];
		$miempleado->available = $arrayEmpleado['available'];
		$idEmpleado = $miempleado->InsertarEmpleado();
		//Cargo el log
		if ($idEmpleado) {
			$new_log = new Logger();
			$new_log->idEmpleado =  $idEmpleado;
			$new_log->accion = "Carga de empleado nuevo";
			$new_log->InsertarLog();
		}
		//--
		$payload = json_encode(array("mensaje: " => "Se ha ingresado el empleado", "status" => 200));
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
		$miempleado->nombre = $arraymodificar['nombre'];
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

	public function TraerTodos($request, $response, $args)
	{
		$lista = Empleado::TraerEmpleados();
		$payload = json_encode(array("Empleados" => Empleado::Listar($lista)));
		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}
	//endregion ABM

	/**  3- Cada empleado responsable de cada producto del pedido , debe: 
	 *   Listar todos los productos pendientes de este tipo de empleado.
	 *   Debe cambiar el estado a “en preparación” y agregarle el tiempo de preparación.
	 */
	public function TomarUnPedido($request, $response)
	{
		//Busco los pedidos pendientes, trae de a 1
		$pedido = Pedido::TraeLaComandaConPedidosPendientes();

		if($pedido){
			//Busco el empleado disponible
			$empleado = Empleado::TraerEmpleadosPorSector($pedido->sector);
			if($empleado){
				//Al pedido le seteo el empleado
				$pedido->SetIdEmpleado($empleado->id);
				$pedido->SetEstimacion(rand(0, 20));
				//Al empleado le seteo el estado
				$empleado->SetEstado("ocupado");
				$empleado->ModificarEmpleado($empleado->id);
				$pedido->SetEstado("en preparacion");
				$pedido->ModificarPedido();

				//log
				$new_log = new Logger();
				$new_log->idEmpleado = $empleado->id;
				$new_log->accion = "Tomar pedido";
				$new_log->InsertarLog();

				$payload = json_encode(array("mensaje: " => "Se ha tomado el pedido", "status" => 200));
			}else{
				$payload = json_encode(array("mensaje: " => "No hay empleados disponibles", "status" => 400));
			}
		}else {
			$payload = json_encode(array("mensaje: " => "No hay pedidos pendientes", "status" => 200));
		}
		$response->getBody()->write($payload);
		return $response->withHeader('Content-Type', 'application/json');
	}

	/**  6- Cada empleado responsable de cada producto del pedido, debe:
	 *    ❏ Listar todos los productos pendientes de este tipo de empleado
	 *    ❏ Debe cambiar el estado a “listo para servir
	 */
	public function EntregarUnPedido($request, $response)
	{
		//Traigo los pedidos en preparacion
		$pedido = Pedido::TraePedidosEnPreparacion();
		if($pedido)
		{
			//Traigo el empleado que se encuentra trabajando en el sector
			$empleado = Empleado::TraerEmpleadosPorOcupados($pedido->sector);
			if($empleado)
			{
				//Actualizo el estado del empleado
				$empleado->SetEstado("disponible");
				$empleado->ModificarEmpleado($empleado->id);
				$pedido->SetEstado("listo para servir");
				$pedido->estimacion > rand(0, 40) ? $pedido->demoro = 'si' : $pedido->demoro = 'no';
				$pedido->ModificarPedido();

				$new_log = new Logger();
				$new_log->idEmpleado = $empleado->id;
				$new_log->accion = "Entregar pedido";
				$new_log->InsertarLog();
				$payload = json_encode(array("mensaje: " => "Se ha entregado el pedido", "status" => 200));
			}
			else
			{
				$payload = json_encode(array("mensaje: " => "El empleado ya se encuentra trabajando.", "status" => 400));
			}
		}
		else 
		{
			$payload = json_encode(array("mensaje: " => "No hay pedidos en preparacion", "status" => 200));
		}
		$response->getBody()->write($payload);
		return $response->withHeader('Content-Type', 'application/json');
	}

	//metricas empleado
	public function MetricasEmpleado($request, $response)
	{
		$empleado = Empleado::Metricas();
		Empleado::MostrarMetricas($empleado);
		$payload = json_encode("");
		$response->getBody()->write($payload);
		return $response
			->withHeader('Content-Type', 'application/json');
	}

}
