<?php

require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaApi extends Mesa implements IApiUsable
{
    public function TraerTodos($request, $response, $args)
    {
        $mesas = Mesa::MostrarMesas();
        if (count($mesas) > 0) {
            $payload = json_encode(array("Lista: " => $mesas));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);
        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN MESAS"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMesasFacturacion($request, $response, $args)
    {
        $mesas = Mesa::MostrarMesasPorFacturacion();
        if (count($mesas) > 0) {
            $payload = json_encode(array("Mesas: " => $mesas));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);
        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN MESAS"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }
    public function TraerMesasTodaFacturacion($request, $response, $args)
    {
        $mesas = Mesa::MostrarMesasPorTodaFacturacion();
        if (count($mesas) > 0) {
            $payload = json_encode(array("Mesas: " => $mesas));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);
        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN MESAS"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodosPorEstado($request, $response, $args)
    {
        $estado = $args['estado'];
        $lista = Mesa::ObtenerPorEstado($estado);
        if (count($lista) > 0) {
            $payload = json_encode(array("Lista: " => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);

        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN MESAS DEL ESATADO INDICADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMesaFacturacionPorFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id_mesa = $args['mesa'];
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];

        $lista = Mesa::MostrarMesaFacturacionPorFechas($desde, $hasta, $id_mesa);
        if (count($lista) > 0) {
            $payload = json_encode(array("Facturación: " => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);

        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN MESAS EN EL PERIODO INDICADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerFacturacionPorFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];

        $lista = Mesa::MostrarMesasFacturacionPorFechas($desde, $hasta);
        if (count($lista) > 0) {
            $payload = json_encode(array("Facturación: " => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);

        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN MESAS EN EL PERIODO INDICADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMesaFacturaMayorPorFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];

        $lista = Mesa::MostrarMesasFacturaMayorPorFechas($desde, $hasta);
        if (count($lista) > 0) {
            $payload = json_encode(array("La/s Mesa/s Con Mayor Factura: " => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);

        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN MESAS EN EL PERIODO INDICADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerMesaFacturaMenorPorFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];

        $lista = Mesa::MostrarMesasFacturaMenorPorFechas($desde, $hasta);
        if (count($lista) > 0) {
            $payload = json_encode(array("La/s Mesa/s Con Menor Factura: " => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);

        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN MESAS EN EL PERIODO INDICADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMesaUsadaFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];

        $lista = Mesa::MostrarMesasUsadasFechas($desde, $hasta);
        if (count($lista) > 0) {
            $payload = json_encode(array("Mesa Más Usada: " => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);

        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN MESAS EN EL PERIODO INDICADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMesaMenosUsadaFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];
        var_dump($desde);
        var_dump($hasta);
        $lista = Mesa::MostrarMesasMenosUsadasFechas($desde, $hasta);
        if (count($lista) > 0) {
            $payload = json_encode(array("Mesa Menos Usada: " => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);

        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN MESAS EN EL PERIODO INDICADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMesaConMasFacturacionFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];

        $lista = Mesa::MostrarMesasConMasFacturacionFechas($desde, $hasta);
        if (count($lista) > 0) {
            $payload = json_encode(array("Mesa Con Más Facturación: " => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);

        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN MESAS EN EL PERIODO INDICADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMesaConMenosFacturacionFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];

        $lista = Mesa::MostrarMesasConMenosFacturacionFechas($desde, $hasta);
        if (count($lista) > 0) {
            $payload = json_encode(array("Mesa Con Menos Facturación: " => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);

        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN MESAS EN EL PERIODO INDICADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerFacturacionFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];

        $lista = Mesa::MostrarFacturacionPorFechas($desde, $hasta);
        if (count($lista) > 0) {
            $payload = json_encode(array("Mesa Con Menos Facturación: " => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);

        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN MESAS EN EL PERIODO INDICADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response, $args)
    {
        $mesa = new Mesa();
        $mesa->estado_mesa = 5;

        $retorno = $mesa->CrearMesa();

        if ($retorno == true) {
            $payload = json_encode(array("mensaje" => "Mesa creada con exito"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(201);

        } else {
            $payload = json_encode(array("mensaje" => "ERROR AL CREAR LA MESA"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(400);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $mesaModificar = $args['identificador'];
        $mesa = Mesa::ObtenerPorId($mesaModificar);

        if ($mesa != null) {
            Mesa::CambiarEstadoMesa($mesa, 3);
            $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);
        } else {
            $payload = json_encode(array("mensaje" => "MESA INEXISTENTE"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mesaModificar = $args['identificador'];
        $estado_mesa = $parametros['estado_mesa'];
        $mesa = Mesa::ObtenerPorId($mesaModificar);
        if ($mesa != null) {
            $mesa->estado_mesa = $estado_mesa;
            Mesa::modificarMesa($mesa);
            $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);
        } else {
            $payload = json_encode(array("mensaje" => "MESA INEXISTENTE"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['identificador'];
        $mesa = Mesa::ObtenerPorId($id);

        if ($mesa != null) {
            $payload = json_encode(array("Mesa: " => $mesa));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);
        } else {
            $payload = json_encode(array("mensaje" => "MESA NO ENCONTRADA"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function VerEstadoMesas($request, $response, $args)
    {
        $mesas = Mesa::MostrarMesas();

        foreach ($mesas as $mesa) {
            switch ($mesa->estado_mesa) {
                case 1:
                    $estado = "Cliente Esperando Pedido";
                    break;
                case 2:
                    $estado = "Cliente Comiendo";
                    break;
                case 3:
                    $estado = "Cliente Pagando";
                    break;
                case 4:
                    $estado = "Cerrada";
                    break;
                case 5:
                    $estado = "Libre";
                    break;
            }

            $codigo_mesa = $mesa->codigo_mesa;

            $arrayMesas[] = array("codigo_mesa" => $codigo_mesa, "estado" => $estado);
        }

        if (count($mesas) > 0) {
            $payload = json_encode(array("Mesas: " => $arrayMesas));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN MESAS CARGADAS"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function CerrarMesa($request, $response, $args)
    {
        $id = $args['identificador'];
        $pedido = Pedido::ObtenerPorCodigo($id);
        $mesa = Mesa::ObtenerPorId($pedido->id_mesa);

        if ($pedido != null) {
            if ($pedido->estado == EstadoPedido::CERRADO->value) {
                Mesa::CambiarEstadoMesa($mesa, EstadoMesa::CERRADA->value);
                $payload = json_encode(array("mensaje:" => "Mesa cerrada con exito"));
                $response->getBody()->write($payload);
                $newResponse = $response->withStatus(HttpCode::OK->value);
            } else {
                $payload = json_encode(array("mensaje: " => "EL CLIENTE TODAVIA NO PAGO EL PEDIDO"));
                $response->getBody()->write($payload);
                $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
            }
        } else {
            $payload = json_encode(array("mensaje" => "PEDIDO INEXISTENTE"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function LiberarMesa($request, $response, $args)
    {
        $id = $args['identificador'];
        $pedido = Pedido::ObtenerPorCodigo($id);
        $mesa = Mesa::ObtenerPorId($pedido->id_mesa);

        if ($pedido != null) {
            if ($mesa->estado_mesa == EstadoMesa::CERRADA->value) {
                Mesa::CambiarEstadoMesa($mesa, EstadoMesa::LIBRE->value);
                $payload = json_encode(array("mensaje:" => "Mesa liberada con exito"));
                $response->getBody()->write($payload);
                $newResponse = $response->withStatus(HttpCode::OK->value);
            } else {
                $payload = json_encode(array("mensaje: " => "LA MESA NO SE ENCUENTRA CERRADA"));
                $response->getBody()->write($payload);
                $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
            }
        } else {
            $payload = json_encode(array("mensaje" => "PEDIDO INEXISTENTE"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }


    public function TraerMesaMasUsada($request, $response, $args)
    {
        $mesaMasUsada = Mesa::MesaMasUsada();

        if ($mesaMasUsada != null) {
            $payload = json_encode(array("Mesa mas usada:" => $mesaMasUsada));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            $payload = json_encode(array("mensaje" => "NO HAY INFORMACION"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }
        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }
}