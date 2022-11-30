<?php
include_once("db/AccesoDatos.php");
include_once("interfaces/IEntidad.php");
date_default_timezone_set('America/Buenos_Aires');

class PedidoProducto implements IEntidad
{
    public $id;
    public $id_pedido;
    public $id_producto;
    public $cantidad;
    public $estado; //0 - Pendiente // 1 - En preparación // 2 - Listo 
    public $fecha_prevista;
    public $fecha_fin;
    public $updated_at;
    public $created_at;
    public $activo;

    public static function Alta($pedidoProducto)
    {
        $retorno = 0;
        
        $pedidoAux = AccesoDatos::retornarObjetoActivo($pedidoProducto->id_pedido, 'pedido','Pedido');
        $productoAux = AccesoDatos::retornarObjetoActivo($pedidoProducto->id_producto, 'producto','Producto');
        if(sizeof($pedidoAux) > 0 && sizeof($productoAux) > 0)
        {
            $pedidoProducto->crearRegistro();
            $retorno = 1;
        }
        return $retorno;
    }

    public static function Baja($id)
    {
        $retorno = 0;
        $pedidoProductoAux = AccesoDatos::retornarObjetoActivo($id, 'pedido_producto', 'PedidoProducto');

        if($pedidoProductoAux != null)
        {
            AccesoDatos::borrarRegistro($id, 'pedido_producto');
            $retorno = 1;
        }         
        return $retorno;
    }

    public static function Modificacion($pedidoProducto)
    {
        //var_dump($pedidoProducto);
        $retorno = 0;
        $pedidoAux = AccesoDatos::retornarObjetoActivo($pedidoProducto->id, 'pedido_producto', 'Pedido');
        $productoAux = AccesoDatos::retornarObjetoActivo($pedidoProducto->id_producto, 'producto', 'Producto');
        if(sizeof($pedidoAux) > 0 && sizeof($productoAux) > 0)
        {
            $pedidoAux[0]->cantidad = $pedidoProducto->id_cantidad;
            $pedidoAux[0]->id_producto = $pedidoProducto->id_producto;
            PedidoProducto::modificarRegistro($pedidoAux[0]);
            $retorno = 1;
        }
        return $retorno;
    }

    public static function CambiarAEnPreparacion($pedidoProducto, $tardanzaEnMinutos)
    {
        //var_dump($pedidoProducto);
        $pedidoProducto->estado = 1;
        $fecha = new DateTime(date("d-m-Y H:i:s"));
        $pedidoProducto->fecha_prevista = $fecha->modify('+'.$tardanzaEnMinutos.' minutes');
        $pedidoProducto->fecha_prevista = $pedidoProducto->fecha_prevista->format("Y-m-d H:i:s");

        return $pedidoProducto;
    }

    public static function CambiarAParaServir($pedidoProducto)
    {
     
        $pedidoProducto->estado = 2;
        $pedidoProducto->fecha_fin = new DateTime(date("d-m-Y H:i:s"));
        $pedidoProducto->fecha_fin = $pedidoProducto->fecha_fin->format("Y-m-d H:i:s");
        return $pedidoProducto;
    }

    public static function CambiarEstado($idEstado, $idPedidoProducto, $idUsuario = null, $tardanzaEnMinutos = null)
    {
        $pedidoProducto = AccesoDatos::retornarObjetoActivoPorCampo($idPedidoProducto, 'id', 'pedido_producto', 'PedidoProducto');

        switch ($idEstado)
        {
            case 1:
                $pedidoProducto[0]->id_usuario = $idUsuario;
                $pedidoProductoAux = PedidoProducto::CambiarAEnPreparacion($pedidoProducto[0], $tardanzaEnMinutos);
                break;
            case 2:
                $pedidoProductoAux = PedidoProducto::CambiarAParaServir($pedidoProducto[0], $tardanzaEnMinutos);
                break;
        }
        PedidoProducto::modificarRegistro($pedidoProductoAux);
    }

    public function crearRegistro() 
    {
        $retorno = null;
        try
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO  pedido_producto (id_pedido, id_producto, id_usuario, cantidad, estado, created_at, updated_at, activo) 
                                                                  VALUES (:id_pedido, :id_producto, :id_usuario, :cantidad, :estado, :created_at, :updated_at, :activo)");
            $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_STR);
            $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_STR);
            $consulta->bindValue(':id_usuario', '-1', PDO::PARAM_STR); 
            $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_STR); 
            $consulta->bindValue(':estado', '0', PDO::PARAM_STR); 
            $consulta->bindValue(':activo', '1', PDO::PARAM_STR);
            $fecha = new DateTime(date("d-m-Y H:i:s"));
            $consulta->bindValue(':created_at', date_format($fecha, 'Y-m-d H:i:s'));
            $consulta->bindValue(':updated_at', date_format($fecha, 'Y-m-d H:i:s'));
            $consulta->execute();
            $retorno = $objAccesoDatos->obtenerUltimoId();
        }
        catch(Throwable $mensaje)
        {
            printf("Error al conectar en la base de datos: <br> $mensaje .<br>");
        }
        finally
        {
            return $retorno;
        }    
    }

    public static function modificarRegistro($pedidoProducto)
    {       
        try
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido_producto
                                                          SET id_pedido = :id_pedido, 
                                                              id_producto = :id_producto,
                                                              id_usuario = :id_usuario, 
                                                              cantidad = :cantidad,
                                                              estado = :estado, 
                                                              fecha_prevista = :fecha_prevista,
                                                              fecha_fin = :fecha_fin,
                                                              activo = :activo,
                                                              updated_at = :updated_at
                                                          WHERE id = :id");
            $consulta->bindValue(':id', $pedidoProducto->id, PDO::PARAM_STR);
            $consulta->bindValue(':id_pedido', $pedidoProducto->id_pedido, PDO::PARAM_STR);
            $consulta->bindValue(':id_usuario', $pedidoProducto->id_usuario, PDO::PARAM_STR);
            $consulta->bindValue(':id_producto', $pedidoProducto->id_producto, PDO::PARAM_STR);
            $consulta->bindValue(':cantidad', $pedidoProducto->cantidad, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $pedidoProducto->estado, PDO::PARAM_STR);
            $consulta->bindValue(':fecha_prevista', $pedidoProducto->fecha_prevista, PDO::PARAM_STR);
            $consulta->bindValue(':fecha_fin', $pedidoProducto->fecha_fin);
            $consulta->bindValue(':activo', $pedidoProducto->activo, PDO::PARAM_STR);
            $fecha = new DateTime(date("d-m-Y H:i:s"));
            $consulta->bindValue(':updated_at', date_format($fecha, 'Y-m-d H:i:s'));
            $consulta->execute();
        }
        catch(Throwable $mensaje)
        {
            printf("Error al conectar en la base de datos: <br> $mensaje .<br>");
        }
    }  
}
?>