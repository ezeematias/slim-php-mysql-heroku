<?php

require_once './utils/enums.php';
class Pedido
{
    public $id_pedido;
    public $id_mesa;
    public $id_mozo;
    public $cliente;
    public $estado;
    public $created_at;
    public $hora_entrega;
    public $precio_final;
    public $activo;


    public static function Alta($pedido, $codigo)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO  pedido (codigo_pedido, id_mesa, id_mozo, cliente, estado, created_at, hora_entrega, activo, fecha)  VALUES (:codigo_pedido, :id_mesa, :id_mozo, :cliente, :estado, :created_at, :hora_entrega, :activo, :fecha)");

            $consulta->bindValue(':codigo_pedido', $codigo, PDO::PARAM_STR);
            $consulta->bindValue(':id_mesa', $pedido->id_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':id_mozo', $pedido->id_mozo, PDO::PARAM_STR);
            $consulta->bindValue(':cliente', $pedido->cliente, PDO::PARAM_STR);
            $consulta->bindValue(':estado', EstadoPedido::PENDIENTE->value, PDO::PARAM_STR);
            $consulta->bindValue(':activo', AltaBaja::ALTA->value, PDO::PARAM_STR);
            $fecha = new DateTime(date("d-m-Y H:i:s"));
            $consulta->bindValue(':created_at', date_format($fecha, 'Y-m-d H:i:s'));
            $fecha_prevista = $fecha->modify('+' . 0 . ' minutes');
            $consulta->bindValue(':hora_entrega', date_format($fecha_prevista, 'Y-m-d H:i:s'), PDO::PARAM_STR);
            $hora_login = date("Y-m-d H:i:s");
            $consulta->bindValue(':fecha', $hora_login, PDO::PARAM_STR);
            return $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public static function MostrarPedidos()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta('SELECT * FROM pedido WHERE activo = :estado');
            $consulta->bindValue(':estado', AltaBaja::ALTA->value, PDO::PARAM_STR);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
    }

    public static function ObtenerPorEstado($estado)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE estado = :estado AND activo = :activo");
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $consulta->bindValue(':activo', AltaBaja::ALTA->value, PDO::PARAM_STR);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
    }

    public static function ObtenerPorId($id_pedido)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE id_pedido = :id_pedido");
            $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchObject('Pedido');
    }

    public static function ObtenerPorCodigo($codigo_pedido)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE codigo_pedido = :codigo_pedido");
            $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_STR);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchObject('Pedido');
    }

    public static function ObtenerPorDemora($demora)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE entrega_demorada = :entrega_demorada");
            $consulta->bindValue(':entrega_demorada', $demora, PDO::PARAM_STR);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
    }

    public static function ObtenerPorDemoraFechas($demora, $desde, $hasta)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE fecha between :desde and :hasta AND entrega_demorada = :entrega_demorada");
            $consulta->bindValue(':entrega_demorada', $demora, PDO::PARAM_STR);
            $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
            $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
    }

    public static function ObtenerCanceladosFechas($estado, $desde, $hasta)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedido WHERE fecha between :desde and :hasta AND estado = :estado");
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
            $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
    }

    public function ModificarPedido($pedido)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta('UPDATE pedido SET id_mesa = :id_mesa, id_mozo = :id_mozo, cliente = :cliente, hora_entrega = :hora_entrega WHERE id_pedido = :id_pedido');
            $consulta->bindValue(':id_pedido', $pedido->id_pedido, PDO::PARAM_INT);
            $consulta->bindValue(':id_mesa', $pedido->id_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':id_mozo', $pedido->id_mozo, PDO::PARAM_INT);
            $consulta->bindValue(':cliente', $pedido->cliente, PDO::PARAM_INT);
            $consulta->bindValue(':hora_entrega', $pedido->hora_entrega, PDO::PARAM_INT);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public static function CambiarActivoPedido($pedido, $activo)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta('UPDATE pedido SET activo = :activo WHERE id_pedido = :id_pedido');
            $consulta->bindValue(':id_pedido', $pedido->id_pedido, PDO::PARAM_STR);
            $consulta->bindValue(':activo', $activo, PDO::PARAM_INT);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public static function SetEntrega($pedido)
    {
        try {
            $entrega = new DateTime($pedido->hora_entrega);
            $fecha = new DateTime(date("d-m-Y H:i:s"));

            if ($entrega < $fecha) {
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta('UPDATE pedido SET entrega_demorada = :entrega_demorada WHERE id_pedido = :id_pedido');
                $consulta->bindValue(':id_pedido', $pedido->id_pedido, PDO::PARAM_STR);
                $consulta->bindValue(':entrega_demorada', 'SI', PDO::PARAM_STR);
                $consulta->execute();
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public static function CambiarEstadoPedido($pedido, $estado)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta('UPDATE pedido SET estado = :estado WHERE id_pedido = :id_pedido');
            $consulta->bindValue(':id_pedido', $pedido->id_pedido, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_INT);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }



    public static function ActualizarPrecioPedido($codigo_pedido, $precioTotal)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta('UPDATE pedido SET precio_final = :precio_final WHERE codigo_pedido = :codigo_pedido');

            $pedido = self::ObtenerPorCodigo($codigo_pedido);
            $precioFinal = $pedido->precio_final + $precioTotal;

            $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_INT);
            $consulta->bindValue(':precio_final', $precioFinal, PDO::PARAM_INT);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public static function ActualizarTiempoEspera($codigo_pedido, $hora_entrega)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta('UPDATE pedido SET hora_entrega = :hora_entrega WHERE codigo_pedido = :codigo_pedido');

            $pedido = self::ObtenerPorCodigo($codigo_pedido);
            $inicio = new DateTime($pedido->created_at);
            $entrega = new DateTime($pedido->hora_entrega);

            $hora_prevista = $inicio->modify('+' . $hora_entrega . ' minutes');

            if ($hora_prevista > $entrega) {
                $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_INT);
                $consulta->bindValue(':hora_entrega', date_format($hora_prevista, 'Y-m-d H:i:s'), PDO::PARAM_STR);
                $consulta->execute();
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public static function ActualizarFoto($id_pedido, $foto)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta('UPDATE pedido SET foto = :foto WHERE id_pedido = :id_pedido');

            $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
            $consulta->bindValue(':foto', $foto, PDO::PARAM_INT);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

?>