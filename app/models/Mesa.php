<?php


include_once '../app/models/Facturacion.php';
include_once '../app/models/Uso.php';

class Mesa
{

    public $id_mesa;
    public $estado_mesa;

    public function CrearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('INSERT INTO mesa (id_mesa, estado_mesa, codigo_mesa) VALUES (:id_mesa, :estado_mesa , :codigo_mesa)');

        $nuevoCodigo = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 5);

        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado_mesa', $this->estado_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':codigo_mesa', $nuevoCodigo, PDO::PARAM_STR);
        $consulta->execute();
        return true;
    }

    public function ModificarMesa($mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('UPDATE mesa SET estado_mesa = :estado_mesa WHERE id_mesa = :id_mesa');
        $consulta->bindValue(':id_mesa', $mesa->id_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado_mesa', $mesa->estado_mesa, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function CambiarEstadoMesa($mesa, $estado_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('UPDATE mesa SET estado_mesa = :estado_mesa WHERE id_mesa = :id_mesa');
        $consulta->bindValue(':id_mesa', $mesa->id_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado_mesa', $estado_mesa, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function MostrarMesas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT * FROM mesa');
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function ObtenerPorId($id_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesa WHERE id_mesa = :id_mesa");
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function ObtenerPorCodigo($codigo_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesa WHERE codigo_mesa = :codigo_mesa");
        $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function ObtenerPorEstado($estado)
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesa WHERE estado_mesa = :estado");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
    }

    public static function ObtenerEstadoMesaLibre($id_mesa, $estado_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT * FROM mesa WHERE id_mesa = :id_mesa AND estado_mesa = :estado_mesa');
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado_mesa', $estado_mesa, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
    }

    public static function MesaMasUsada()
    {
        $mesas = self::mostrarMesas();
        $pedidos = Pedido::mostrarPedidos();
        $mesasUsadas = array();

        if (count($pedidos) > 0) {

            foreach ($mesas as $mesa) {
                $cant = 0;
                $mesasUsadas[$mesa->id_mesa] = $cant;
                foreach ($pedidos as $pedido) {
                    if ($pedido->id_mesa == $mesa->id_mesa) {
                        $mesasUsadas[$mesa->id_mesa]++;
                    }
                }
            }
            $max = max($mesasUsadas);
            foreach ($mesasUsadas as $key => $value) {
                if ($value == $max) {
                    $mesaMasUsada = $key;
                }
            }
            return $mesaMasUsada;
        } else {
            return null;
        }
    }

    public static function MostrarMesasPorFacturacion()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT id_mesa AS mesa, MAX(precio_final) AS facturacion FROM pedido GROUP BY id_mesa ORDER BY facturacion ASC');
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Facturacion");
    }

    public static function MostrarMesasPorTodaFacturacion()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT id_mesa AS mesa, precio_final AS facturacion FROM pedido ORDER BY precio_final ASC');
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Facturacion");
    }

    public static function MostrarMesasConMasFacturacionFechas($desde, $hasta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT SUM(precio_final) AS facturacion, id_mesa AS mesa FROM `pedido` WHERE fecha between :desde and :hasta GROUP BY id_mesa ORDER BY `facturacion` DESC LIMIT 1');
        $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
        $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Facturacion");
    }

    public static function MostrarMesasConMenosFacturacionFechas($desde, $hasta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT SUM(precio_final) AS facturacion, id_mesa AS mesa FROM `pedido` WHERE fecha between :desde and :hasta GROUP BY id_mesa ORDER BY `facturacion` ASC LIMIT 1');
        $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
        $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Facturacion");
    }

    public static function MostrarMesaFacturacionPorFechas($desde, $hasta, $id_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT id_mesa AS mesa, precio_final AS facturacion, fecha FROM pedido WHERE fecha between :desde and :hasta AND id_mesa = :id_mesa');
        $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
        $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Facturacion");
    }

    public static function MostrarMesasFacturacionPorFechas($desde, $hasta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT id_mesa AS mesa, precio_final AS facturacion, fecha FROM pedido WHERE fecha between :desde and :hasta');
        $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
        $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Facturacion");
    }

    public static function MostrarMesasFacturaMayorPorFechas($desde, $hasta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(' SELECT id_mesa AS mesa, precio_final AS facturacion, fecha FROM pedido
                                                        WHERE precio_final = (SELECT MAX(precio_final ) FROM pedido WHERE fecha between :desde and :hasta)');
        $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
        $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Facturacion");
    }
    
    public static function MostrarMesasFacturaMenorPorFechas($desde, $hasta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(' SELECT id_mesa AS mesa, precio_final AS facturacion, fecha FROM pedido
                                                        WHERE precio_final = (SELECT MIN(precio_final ) FROM pedido WHERE fecha between :desde and :hasta)');
        $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
        $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Facturacion");
    }

    public static function MostrarFacturacionPorFechas($desde, $hasta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT SUM(precio_final) AS facturacion FROM `pedido` WHERE fecha between :desde and :hasta');
        $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
        $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "FacturacionSample");
    }

    public static function MostrarMesasUsadasFechas($desde, $hasta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(' SELECT id_mesa AS mesa, COUNT( id_mesa ) AS total FROM pedido WHERE fecha between :desde and :hasta GROUP BY id_mesa ORDER BY total DESC LIMIT 1');
        $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
        $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Uso");
    }
    public static function MostrarMesasMenosUsadasFechas($desde, $hasta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(' SELECT id_mesa AS mesa, COUNT( id_mesa ) AS total FROM pedido WHERE fecha between :desde and :hasta GROUP BY id_mesa ORDER BY total ASC LIMIT 1');
        $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
        $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Uso");
    }
}