<?php

require_once './models/Pedido.php';
require_once './models/Usuario.php';
require_once './models/Mesa.php';
require_once './utils/enums.php';
require_once './utils/filesManager.php';


class PedidoApi extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_mesa = $parametros['id_mesa'];
        $id_mozo = $parametros['id_mozo'];
        $cliente = $parametros['cliente'];

        $pedido = new Pedido();
        $pedido->id_mesa = $id_mesa;
        $pedido->id_mozo = $id_mozo;
        $pedido->cliente = $cliente;

        self::CheckMesa($pedido, $response);

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    private function CheckMesa($pedido, $response)
    {

        $retorno = Mesa::ObtenerEstadoMesaLibre($pedido->id_mesa, EstadoMesa::LIBRE->value);

        if ($retorno != NULL) {
            self::CheckMozo($pedido, $response);

        } else {
            $payload = json_encode(array("mensaje" => "LA MESA ELEGIDA ESTA OCUPADA O NO EXISTE"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
            return $newResponse
                ->withHeader('Content-Type', 'application/json');
        }
    }

    private function CheckMozo($pedido, $response)
    {

        $retorno = Usuario::AsignarEmpleado($pedido->id_mozo, TipoEmpleado::MOZO->value);

        if ($retorno != null) {

            self::AltaPedido($pedido, $response);

        } else {
            $payload = json_encode(array("mensaje" => "EL MOZO ELEGIDO NO EXISTE"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
            return $newResponse
                ->withHeader('Content-Type', 'application/json');
        }
    }

    private function AltaPedido($pedido, $response)
    {
        $nuevoCodigo = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 5);

        $retorno = Pedido::Alta($pedido, $nuevoCodigo);

        if ($retorno == true) {

            $mesa = new Mesa();
            $mesa->id_mesa = $pedido->id_mesa;
            $mesa->estado_mesa = EstadoMesa::CLIENTE_ESPERADO_PEDIDO->value;
            $mesa->ModificarMesa($mesa);

            $pedido = Pedido::ObtenerPorCodigo($nuevoCodigo);
            $mesa = Mesa::ObtenerPorId($pedido->id_mesa);


            $payload = json_encode(array("mensaje" => "Pedido creado con exito", "codigo_pedido" => $nuevoCodigo, "id_pedido" => $pedido->id_pedido, "codigo_mesa" => $mesa->codigo_mesa));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::CREATED->value);
        } else {
            $payload = json_encode(array("mensaje" => "ERROR AL CARGAR EL PEDIDO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::BAD_REQUEST->value);
            return $newResponse
                ->withHeader('Content-Type', 'application/json');
        }

    }

    public function TraerTodos($request, $response, $args)
    {
        $pedidos = Pedido::MostrarPedidos();
        if (count($pedidos) > 0) {
            $payload = json_encode(array("Pedidos: " => $pedidos));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN PEDIDOS"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodosPorEstado($request, $response, $args)
    {
        $estado = $args['estado'];
        $lista = Pedido::ObtenerPorEstado($estado);
        if (count($lista) > 0) {
            $payload = json_encode(array("Pedidos: " => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN PEDIDOS CON EL ESTADO INDICADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodosPorDemora($request, $response, $args)
    {
        $demora = $args['demora'];
        $lista = Pedido::ObtenerPorDemora($demora);
        if (count($lista) > 0) {
            $payload = json_encode(array("Pedidos: " => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            var_dump($lista);
            $payload = json_encode(array("mensaje" => "NO EXISTEN PEDIDOS CON LA DEMORA INDICADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodosPorDemoraFechas($request, $response, $args)
    {
        $demora = $args['demora'];
        $parametros = $request->getParsedBody();
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];
        $lista = Pedido::ObtenerPorDemoraFechas($demora, $desde, $hasta);
        if (count($lista) > 0) {
            $payload = json_encode(array("Pedidos: " => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            var_dump($lista);
            $payload = json_encode(array("mensaje" => "NO EXISTEN PEDIDOS CON LA DEMORA INDICADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodosCanceladosFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];
        $estado = $parametros['estado'];
        $lista = Pedido::ObtenerCanceladosFechas($estado, $desde, $hasta);
        if (count($lista) > 0) {
            $payload = json_encode(array("Pedidos: " => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            var_dump($lista);
            $payload = json_encode(array("mensaje" => "NO EXISTEN PEDIDOS CANCELADOS PARA LA FECHA INDICADA"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['identificador'];
        $pedido = Pedido::ObtenerPorId($id);

        if ($pedido != null) {
            $comandas = Comanda::ObtenerPorIdPedido($pedido->id_pedido);

            $payload = json_encode(array("Pedido: " => $pedido, "Comandas: " => $comandas));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            $payload = json_encode(array("mensaje" => "PEDIDO NO ENCONTRADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function ConfirmarPedido($request, $response, $args)
    {
        $codigoPedido = $args['identificador'];
        $pedido = Pedido::ObtenerPorCodigo($codigoPedido);

        if ($pedido != null) {
            $comandas = Comanda::ObtenerPorIdPedido($codigoPedido);

            $payload = json_encode(array("Pedido: " => $pedido, "Comandas: " => $comandas));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            $payload = json_encode(array("mensaje" => "PEDIDO NO ENCONTRADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function EntregarPedido($request, $response, $args)
    {
        $id = $args['identificador'];
        $pedido = Pedido::ObtenerPorCodigo($id);
        $mesa = Mesa::ObtenerPorId($pedido->id_mesa);

        if ($pedido != null) {
            if ($pedido->estado == EstadoPedido::LISTO->value) {
                Pedido::SetEntrega($pedido);
                Mesa::CambiarEstadoMesa($mesa, EstadoMesa::CLIENTE_COMIENDO->value);
                $payload = json_encode(array("mensaje: " => "Se entrego el Pedido a la mesa"));
                $response->getBody()->write($payload);
                $newResponse = $response->withStatus(HttpCode::OK->value);
            } else {
                $payload = json_encode(array("mensaje: " => "EL PEDIDO NO ESTA LISTO PARA LLEVAR A LA MESA"));
                $response->getBody()->write($payload);
                $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
            }
        } else {
            $payload = json_encode(array("mensaje" => "PEDIDO NO ENCONTRADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function CerrarPedido($request, $response, $args)
    {
        $id = $args['identificador'];
        $pedido = Pedido::ObtenerPorCodigo($id);
        $mesa = Mesa::ObtenerPorId($pedido->id_mesa);

        if ($pedido != null) {
            if ($pedido->estado == EstadoPedido::LISTO->value) {
                Pedido::CambiarEstadoPedido($pedido, EstadoPedido::CERRADO->value);
                Mesa::CambiarEstadoMesa($mesa, EstadoMesa::CLIENTE_PAGANDO->value);
                $payload = json_encode(array("mensaje:" => "El cliente pago su pedido", "Monto:" => "$ " . $pedido->precio_final));
                $response->getBody()->write($payload);
                $newResponse = $response->withStatus(HttpCode::OK->value);
            } else {
                $payload = json_encode(array("mensaje: " => "EL CLIENTE TODAVIA NO RECIBIO SU PEDIDO"));
                $response->getBody()->write($payload);
                $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
            }
        } else {
            $payload = json_encode(array("mensaje" => "PEDIDO NO ENCONTRADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $args['identificador'];
        $id_mesa = $parametros['id_mesa'];
        $id_mozo = $parametros['id_mozo'];
        $cliente = $parametros['cliente'];
        $hora_entrega = $parametros['hora_entrega'];

        $pedido = Pedido::ObtenerPorId($id);

        if ($pedido != null) {

            $checkMesa = Mesa::ObtenerEstadoMesaLibre($id_mesa, EstadoMesa::LIBRE->value);

            if ($checkMesa == NULL) {

                $payload = json_encode(array("mensaje" => "LA MESA ELEGIDA ESTA OCUPADA O NO EXISTE"));
                $response->getBody()->write($payload);
                $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
                return $newResponse
                    ->withHeader('Content-Type', 'application/json');
            }

            $checkMozo = Usuario::AsignarEmpleado($id_mozo, TipoEmpleado::MOZO->value);

            if ($checkMozo == null) {

                $payload = json_encode(array("mensaje" => "EL MOZO ELEGIDO NO EXISTE"));
                $response->getBody()->write($payload);
                $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
                return $newResponse
                    ->withHeader('Content-Type', 'application/json');
            }

            $pedido->id_mesa = $id_mesa;
            $pedido->id_mozo = $id_mozo;
            $pedido->cliente = $cliente;
            $pedido->hora_entrega = $hora_entrega;
            Pedido::ModificarPedido($pedido);
            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            $payload = json_encode(array("mensaje" => "PEDIDO INEXISTENTE"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $pedidoModificar = $args['identificador'];
        $pedido = Pedido::ObtenerPorId($pedidoModificar);

        if ($pedido != null) {
            Pedido::CambiarActivoPedido($pedido, AltaBaja::BAJA->value);
            $payload = json_encode(array("mensaje" => "Pedido Borrado con exito"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            $payload = json_encode(array("mensaje" => "PEDIDO INEXISTENTE"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function SacarFoto($request, $response, $args)
    {
        $id = $args['identificador'];
        $archivo = $request->getUploadedFiles();
        $pedido = Pedido::ObtenerPorCodigo($id);
        $mesa = Mesa::ObtenerPorId($pedido->id_mesa);

        if ($pedido != null) {
            if (FilesManager::UploadFotoPedido($archivo, $pedido, $mesa)) {
                $payload = json_encode(array("mensaje" => "Foto subida con exito"));
                $response->getBody()->write($payload);
                $newResponse = $response->withStatus(HttpCode::OK->value);
            } else {
                $payload = json_encode(array("mensaje" => "ERROR AL SUBIR LA FOTO"));
                $response->getBody()->write($payload);
                $newResponse = $response->withStatus(HttpCode::BAD_REQUEST->value);
            }
        } else {
            $payload = json_encode(array("mensaje" => "PEDIDO NO ENCONTRADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function VerEstadoPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigo_pedido = $parametros['codigo_pedido'];
        $codigo_mesa = $parametros['codigo_mesa'];

        $pedido = Pedido::ObtenerPorCodigo($codigo_pedido);
        $mesa = Mesa::ObtenerPorCodigo($codigo_mesa);

        $inicio = new DateTime($pedido->created_at);
        $entrega = new DateTime($pedido->hora_entrega);

        $demora = abs($inicio->getTimestamp() - $entrega->getTimestamp()) / 60;


        switch ($pedido->estado) {
            case 1:
                $estado = "PENDIENTE";
                break;
            case 2:
                $estado = "EN PREPARACION";
                break;
            case 3:
                $estado = "LISTO";
                break;
            case 4:
                $estado = "CANCELADO";
                break;
            case 5:
                $estado = "CERRADO";
                break;
        }

        if ($pedido == null || $mesa == null) {
            $payload = json_encode(array("mensaje" => "PEDIDO NO ENCONTRADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        } else {
            $payload = json_encode(array("Estado Pedido: " => $estado, "Demora: " => $demora . " minutos"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function VerEstadoPedidos($request, $response, $args)
    {
        $pedidos = Pedido::MostrarPedidos();

        $arrayPedidos = array();

        foreach ($pedidos as $pedido) {
            switch ($pedido->estado) {
                case 1:
                    $estado = "PENDIENTE";
                    break;
                case 2:
                    $estado = "EN PREPARACION";
                    break;
                case 3:
                    $estado = "LISTO";
                    break;
                case 4:
                    $estado = "CANCELADO";
                    break;
                case 5:
                    $estado = "CERRADO";
                    break;
            }

            $inicio = new DateTime($pedido->created_at);
            $entrega = new DateTime($pedido->hora_entrega);

            $demora = abs($inicio->getTimestamp() - $entrega->getTimestamp()) / 60;

            $cliente = $pedido->cliente;

            $arrayPedidos[] = array("codigo_pedido" => $pedido->codigo_pedido, "estado" => $estado, "demora" => $demora . " minutos", "cliente" => $cliente);
        }

        if (count($pedidos) > 0) {
            $payload = json_encode(array("Pedidos: " => $arrayPedidos));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN PEDIDOS"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }
}