<?php

require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoApi extends Producto implements IApiUsable
{
    public function TraerTodos($request, $response, $args)
    {
        $productos = Producto::MostrarProductos();
        if (count($productos) > 0) {
            $payload = json_encode(array("Lista: " => $productos));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);
        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN PRODUCTOS"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodosMasVendidos($request, $response, $args)
    {
        $productos = Producto::MostrarProductosMasVendidos();
        if (count($productos) > 0) {
            $payload = json_encode(array("Productos Más Vendidos: " => $productos));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);
        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN PRODUCTOS"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodosMasVendidosFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];
        $productos = Producto::MostrarProductosMasVendidosFechas($desde, $hasta);
        if (count($productos) > 0) {
            $payload = json_encode(array("Productos Más Vendidos: " => $productos));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);
        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN PRODUCTOS"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }
    public function TraerTodosMenosVendidosFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];
        $productos = Producto::MostrarProductosMenosVendidosFechas($desde, $hasta);
        if (count($productos) > 0) {
            $payload = json_encode(array("Productos Más Vendidos: " => $productos));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);
        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN PRODUCTOS"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodosPorSector($request, $response, $args)
    {
        $sector = $args['sector'];
        $lista = Producto::ObtenerPorSector($sector);
        if (count($lista) > 0) {
            $payload = json_encode(array("Lista: " => $lista));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);

        } else {
            $payload = json_encode(array("mensaje" => "NO EXISTEN PRODUCTOS DEL SECTOR INDICADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['identificador'];
        $usuario = Producto::ObtenerPorId($id);

        if ($usuario != null) {
            $payload = json_encode(array("Producto: " => $usuario));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);
        } else {
            $payload = json_encode(array("mensaje" => "PRODUCTO NO ENCONTRADO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $id_sector = $parametros['id_sector'];
        $tiempo_preparacion = $parametros['tiempo_preparacion'];

        $producto = new Producto();
        $producto->nombre = $nombre;
        $producto->precio = $precio;
        $producto->id_sector = $id_sector;
        $producto->tiempo_preparacion = $tiempo_preparacion;

        $retorno = $producto->CrearProducto();

        if ($retorno == true) {
            $payload = json_encode(array("mensaje" => "Producto creado con exito"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(201);

        } else {
            $payload = json_encode(array("mensaje" => "ERROR AL CREAR EL PRODUCTO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(400);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $productoModificar = $args['identificador'];
        $producto = Producto::ObtenerPorId($productoModificar);

        if ($producto != null) {
            Producto::CambiarEstadoProducto($producto->id_producto, 0);
            $payload = json_encode(array("mensaje" => "Producto borrado con exito"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);
        } else {
            $payload = json_encode(array("mensaje" => "PRODUCTO INEXISTENTE"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $productoModificar = $args['identificador'];
        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $id_sector = $parametros['id_sector'];
        $tiempo_preparacion = $parametros['tiempo_preparacion'];
        $producto = Producto::ObtenerPorId($productoModificar);

        if ($producto != null) {
            $producto->nombre = $nombre;
            $producto->precio = $precio;
            $producto->id_sector = $id_sector;
            $producto->tiempo_preparacion = $tiempo_preparacion;
            Producto::ModificarProducto($producto);
            $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(200);
        } else {
            $payload = json_encode(array("mensaje" => "PRODUCTO INEXISTENTE"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(404);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }
}