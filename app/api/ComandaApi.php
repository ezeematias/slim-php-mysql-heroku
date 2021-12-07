<?php

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use Slim\Handlers\Strategies\RequestHandler;

require_once './db/AccesoDatos.php';
require_once "./models/Comanda.php";
require_once "./models/Usuario.php";
require_once "./models/Pedido.php";
require_once "./models/Mesa.php";
require_once "./models/Encuesta.php";
require_once "./interfaces/IApiUsable.php";
require_once "./models/Logger.php";

class ComandaApi extends Comanda implements IApiUsable
{

    /**  2- El mozo saca una foto de la mesa y lo relaciona con el pedido
     *   1- Una moza toma el pedido de una:
     */
    public function CargarUno($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $archivos = $request->getUploadedFiles();
        $arraycomanda = $ArrayDeParametros['comanda'];

        $comanda = new Comanda();
        $comanda->nombreCliente = $arraycomanda['nombreCliente'];

        $mesaLibre = Mesa::TraerMesaCerrada();
      
        if($mesaLibre){
            //Creo nuevo codigo para comanda y mesa
            $nuevoCodigo = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 5);

            //Cargo informacion a la mesa
            $mesaLibre->codigo = $nuevoCodigo;
            $mesaLibre->estado = 'con cliente esperando pedido';
            $mesaLibre->ModificarMesa();
            //A la comanda le envio esta mesa.
            $comanda->idMesa = $mesaLibre->id;
       
            $archivos != null ?  $comanda->AgregarFoto($archivos, $nuevoCodigo) : $comanda->SetFoto(null);
            $id = $comanda->InsertarComanda($nuevoCodigo);

            if ($id) {
                if (Pedido::CargarPedidos($ArrayDeParametros['pedidos'], $id)) {
                    $arrayempleado = $ArrayDeParametros['empleado'];
                    if ($arrayempleado) {
                        $new_log = new Logger();
                        $new_log->idEmpleado = $arrayempleado['id'];
           
                        $new_log->accion = "Cargar comanda";
                        $new_log->InsertarLog();
                    }
                    $datos = Comanda::TraerInformacionDeComandaRecienCargada($nuevoCodigo);
                    $payload = json_encode(array("" =>Comanda::MostrarDatos($datos)));

                } else {
                    $payload = json_encode(array("mensaje: " => "No se ha ingresado los pedidos", "status" => 400));
                }
            } else {
                $payload = json_encode(array("mensaje: " => "No se ha ingresado la mesa", "status" => 400));
            }
        }else {
            $payload = json_encode(array("mensaje: " => "No hay mesas disponibles", "status" => 400));
        } 
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $codigo = $args['codigo'];
        $comanda = Comanda::TraerComanda($codigo);
        $comanda->BorrarComanda();

        Pedido::CerrarPedidos($comanda->id);
        $cerrarMesa = Mesa::TraerMesa($comanda->codigo);
        $cerrarMesa->estado = 'cerrada';
        $mesaCerrada = $cerrarMesa->ModificarMesa();

        if ($mesaCerrada) {
            $new_log = new Logger();
            $arrayUsuario = $ArrayDeParametros['usuario'];
            $esAdmin = Usuario::obtenerUsuario($arrayUsuario['user']);
          
            if ($esAdmin) {
                $new_log->idEmpleado = $esAdmin->id;
                $new_log->accion = "Cerrar comanda";
                $new_log->InsertarLog();
            }
            $payload = json_encode(array("mensaje: " => "Se ha cerrado la mesa", "status" => 200));
        }
        $payload = json_encode(array("mensaje: " => "Se ha borrado la comanda, se libera la mesa: ".$cerrarMesa->id, "status" => 200));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }//metodo
    
    public function ModificarUno($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $archivos = $request->getUploadedFiles();

        $arraycomanda = $ArrayDeParametros['comanda'];
        $codigo = $args['codigo'];
        $comanda = Comanda::TraerComanda($codigo);

        if ($comanda) {
            $comanda->nombreCliente = $arraycomanda['nombreCliente'];
            $comanda->importe = $arraycomanda['importe'];

        if ($archivos != null) {
            $comanda->AgregarFoto($archivos, $codigo);
        }
        $comanda->ModificarComanda_();

        $arrayempleado = $ArrayDeParametros['empleado'];
        if ($arrayempleado) {
            $new_log = new Logger();
            $new_log->idEmpleado = $arrayempleado['id'];
            $new_log->accion = "Modificar comanda";
            $new_log->InsertarLog();
        }
        $payload = json_encode(array("mensaje: " => "Se ha modificado la comanda", "status" => 200));
        }else {
        $payload = json_encode(array("mensaje: " => "No se ha encontrado la comanda", "status" => 400));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }//metodo

    public function TraerUno($request, $response, $args)
    {
        $codigo = $args['codigo'];
        $comanda = Comanda::TraerComanda($codigo);
        if ($comanda) {
            $payload = json_encode($comanda);
        } else {
            $payload = json_encode(array("mensaje: " => "No se ha encontrado la comanda", "status" => 400));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

     public function TraerTodos($request, $response, $args)
    {
        $comandas = Comanda::TraerTodasLasComandas();
        $payload = json_encode($comandas);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**  9- La moza cobra la cuenta.
     * 
     */
    public function CobrarComanda($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arraycomanda = $ArrayDeParametros['comanda'];
        $arrayempleado = $ArrayDeParametros['empleado'];

        //cargo importe
        $comanda = Comanda::TraerComanda($arraycomanda['codigo']);
        $comanda->SetImporte_(Comanda::CalcularImporte($comanda->codigo)['total']);
        $comanda->ModificarComanda_();

        //actualizo estado de mesa
        $mesa = Mesa::TraerMesaPorId($comanda->idMesa);
        $mesa->estado = 'con cliente pagando';
        $mesa->ModificarMesa();

        //actualizo el estado de los pedidos a "cobrado".
        $pedidosCobrados = Pedido::CobrarPedidos($comanda->id);
        if($pedidosCobrados)
        {
            $payload = json_encode(array("mensaje: " => "Pedidos cobrados.", "status" => 200));
        }else{
            $payload = json_encode(array("Error: " => "Hubo un error cobrando los pedidos", "status" => 400));
        }
        if ($arrayempleado) {
            $new_log = new Logger();
            $new_log->idEmpleado = $arrayempleado['id'];
            $new_log->accion = "Cobrar comanda";
            $new_log->InsertarLog();
        } 
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /** 10- Alguno de los socios cierra la mesa.
     */  
    public function CerrarComanda($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arrayencuesta = $ArrayDeParametros['encuesta'];
        $arrayUsuario = $ArrayDeParametros['socio'];
        $esAdmin = Usuario::obtenerUsuario($arrayUsuario['user']);

        //traer mesas con cliente pagando
        $mesa = Mesa::TraerMesasConClientePagando();
      
        if($mesa){
            $mesa->estado = 'cerrada';
            $mesa->ModificarMesa();

            //encuesta
            $encuesta = new Encuesta();
            $encuesta->mesa = $arrayencuesta['mesa'];
            $encuesta->restaurante = $arrayencuesta['restaurante'];
            $encuesta->mozo = $arrayencuesta['mozo'];
            $encuesta->cocinero = $arrayencuesta['cocinero'];
            $encuesta->texto = $arrayencuesta['texto'];
            $encuesta->Cargar();

            //log
            $new_log = new Logger();
            $new_log->idEmpleado = $esAdmin->id;
            $new_log->accion = "Cerrar comanda";
            $new_log->InsertarLog();


            $payload = json_encode(array("mensaje: " => "Comanda cobrada y se completo lo encuesta", "status" => 200));
        }else
        {
            $payload = json_encode(array("Error: " => "No hay mesas con cliente pagando", "status" => 400));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    /**  7- La moza se fija los pedidos que están listos para servir , cambia el estado de la mesa
     * 
     */
    public function LLevarComida($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arrayempleado = $ArrayDeParametros['empleado'];

        //Traigo los pedidos listos para servir
        $pedido = Pedido::TraePedidosListosParaServir();

        if ($pedido) {
            $pedido->SetEstado("servido");
            $pedido->SetFechaEntregado(date("Y-m-d"));
            $pedido->ModificarPedido();

            //actualizo info de la comanda
            $comanda = Comanda::TraerCodigoComandaPorId($pedido->idComanda);
            if($comanda){
                $mesa = Mesa::TraerMesaPorId($comanda->idMesa);
                if($mesa){
                    $mesa->estado = 'con cliente comiendo';
                    $mesa->ModificarMesa();
                    //log
                    $new_log = new Logger();
                    $new_log->idEmpleado = $arrayempleado['id'];
                    $new_log->accion = "Lleva pedido";
                    $new_log->InsertarLog();

                    $payload = json_encode(array("mensaje: " => "Se ha llevado el pedido", "status" => 200));
                }else{
                    $payload = json_encode(array("Error: " => "No se encontro la mesa", "status" => 400));
                }
            }else{
                $payload = json_encode(array("Error: " => "No se encontro la comanda", "status" => 400));
            }
        } else {
            $payload = json_encode(array("mensaje: " => "No hay pedidos listos para servir", "status" => 400));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**  4- El cliente ingresa el código de la mesa junto con el número de pedido y ve el tiempo de demora de su pedido
     * 
     */
    public function VerMisPedidos($request, $response, $args)
    {
        $pedidos = Pedido::BuscarMisPedidos($args['codigo'], $args['npedido']);
        Pedido::Mostrar($pedidos);
        $payload = json_encode(array("mensaje" => "ok"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /** 11- El cliente ingresa el código de mesa y el del pedido junto con los datos de la encuesta.
     */ 
    public function CompletarEncuesta($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arraydatos = $ArrayDeParametros['datos'];
        
        $pedido = Pedido::TraePedidoPorId($arraydatos['idPedido']);
        Pedido::Mostrar($pedido);
        $encuesta = Encuesta::TraerEncuestaPorMesa($arraydatos['mesa']);
        $encuesta->__toString();
        $payload = json_encode(array("mensaje" => "ok"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**  12- Alguno de los socios pide los mejores comentarios
     * 
     */
    public function VerComentarios($request, $response, $args)
    {
        $mejoresComentarios = Encuesta::TraerMejoresComentarios();
        Encuesta::Listar($mejoresComentarios);
        $payload = json_encode(array("mensaje" => "ok"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**  13- Alguno de los socios pide la mesa más usada.
     * 
     */
    public function VerMesaMasUsada($request, $response, $args)
    {
        $mesaMasUsada = Mesa::MesaMasUsada();
        echo "Mesa mas usada". $mesaMasUsada->mesa_mas_usada;
        $payload = json_encode(array("mensaje" => "ok"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

}
