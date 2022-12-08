<?php

require_once './models/Encuesta.php';
require_once './models/Pedido.php';
require_once './models/Mesa.php';


class EncuestaApi extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigo_pedido = $parametros['codigo_pedido'];
        $codigo_mesa = $parametros['codigo_mesa'];
        $cliente = $parametros['cliente'];
        $rating_mesa = $parametros['rating_mesa'];
        $rating_restaurante = $parametros['rating_restaurante'];
        $rating_mozo = $parametros['rating_mozo'];
        $rating_cocinero = $parametros['rating_cocinero'];
        $opinion = $parametros['opinion'];

        $encuesta = new Encuesta();
        $encuesta->codigo_pedido = $codigo_pedido;
        $encuesta->codigo_mesa = $codigo_mesa;
        $encuesta->cliente = $cliente;
        $encuesta->rating_mesa = $rating_mesa;
        $encuesta->rating_restaurante = $rating_restaurante;
        $encuesta->rating_mozo = $rating_mozo;
        $encuesta->rating_cocinero = $rating_cocinero;
        $encuesta->opinion = $opinion;

        $pedido = Pedido::ObtenerPorCodigo($codigo_pedido);
        $mesa = Mesa::ObtenerPorCodigo($codigo_mesa);

        if ($pedido == null || $mesa == null) {
            $payload = json_encode(array("mensaje" => "VERIFIQUE LOS CODIGOS DE PEDIDO Y MESA"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        } else {
            if ($pedido->estado = EstadoPedido::CERRADO) {
                $retorno = Encuesta::Alta($encuesta);

                if ($retorno) {
                    $payload = json_encode(array("mensaje" => "Encuesta cargada correctamente"));
                    $response->getBody()->write($payload);
                    $newResponse = $response->withStatus(HttpCode::CREATED->value);
                } else {
                    $payload = json_encode(array("mensaje" => "ERROR AL CARGAR LA ENCUESTA"));
                    $response->getBody()->write($payload);
                    $newResponse = $response->withStatus(HttpCode::BAD_REQUEST->value);
                }
            } else {
                $payload = json_encode(array("mensaje" => "SU PEDIDO AUN NO HA SIDO CERRADO"));
                $response->getBody()->write($payload);
                $newResponse = $response->withStatus(HttpCode::BAD_REQUEST->value);
            }
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $encuestas = Encuesta::MostrarEncuestas();
        if (count($encuestas) > 0) {
            $payload = json_encode(array("Encuestas: " => $encuestas));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN ENCUESTAS"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMejores($request, $response, $args)
    {

        $encuestas = Encuesta::TraerMejoresComentarios();
        if (count($encuestas) > 0) {
            $payload = json_encode(array("Comentarios: " => $encuestas));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN COMENTARIOS BUENOS"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMejoresFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];

        $encuestas = Encuesta::TraerMejoresComentariosFechas($desde, $hasta);
        if (count($encuestas) > 0) {
            $payload = json_encode(array("Comentarios: " => $encuestas));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN COMENTARIOS BUENOS"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }
    public function TraerPeoresFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];

        $encuestas = Encuesta::TraerPeoresComentariosFechas($desde, $hasta);
        if (count($encuestas) > 0) {
            $payload = json_encode(array("Comentarios: " => $encuestas));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::OK->value);
        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN COMENTARIOS MALOS"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(HttpCode::NOT_FOUND->value);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }


    public function TraerUno($request, $response, $args)
    {
    }
    public function BorrarUno($request, $response, $args)
    {
    }
    public function ModificarUno($request, $response, $args)
    {
    }

}

?>