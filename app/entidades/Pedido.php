<?php
include_once("db/AccesoDatos.php");
include_once("interfaces/IEntidad.php");
date_default_timezone_set('America/Buenos_Aires');

class Pedido implements IEntidad
{
    public $id;
    public $id_mesa;
    public $id_cliente;
    public $foto;
    public $estado; //1 - "con cliente esperando pedido” , 2 - ”con cliente comiendo”, 3- “con cliente pagando” y 4- “cerrada”.
    public $created_at;
    public $fecha_prevista;
    public $fecha_fin;
    public $precio_final;
    public $updated_at;
    public $activo;
       
    
    public static function Alta($pedido)
    {
        $retorno = 0; 
        $idMesaAux = AccesoDatos::retornarObjetoActivoPorCampo($pedido->id_mesa, 'id_mesa', 'pedido', 'Pedido');
        if(sizeof($idMesaAux) == 0)
        {
            $idUsuarioAux = AccesoDatos::retornarObjetoActivoPorCampo($pedido->id_usuario, 'id', 'usuario', 'Usuario');
            $retorno = 2;
            if(sizeof($idUsuarioAux) > 0)
            {
                $pedido->crearRegistro();
                $retorno = 1;
            }
        }
        return $retorno;
    }

    public static function Baja($id)
    {
        $retorno = 0;
        $pedidoAux = AccesoDatos::retornarObjetoActivo($id, 'pedido', 'Pedido');

        if($pedidoAux != null)
        {
            AccesoDatos::borrarPorCondicion($pedidoAux[0]->id, 'id_pedido', 'pedido_producto');
            AccesoDatos::borrarRegistro($id, 'pedido');
            $retorno = 1;
        }         
        return $retorno;
    }

    public static function Modificacion($pedido)
    {
        $retorno = 0;
        $pedidoAux = AccesoDatos::retornarObjetoActivo($pedido->id, 'pedido', 'Pedido');
        if(sizeof($pedidoAux) > 0)
        {
            $mesaAux = AccesoDatos::retornarObjetoActivoPorCampo($pedido->id_mesa, 'id_mesa', 'pedido', 'Pedido');
            $mesaAuxEnMesa = AccesoDatos::retornarObjetoActivoPorCampo($pedido->id_mesa, 'id', 'mesa', 'Mesa');
            $retorno = 1; 
            if($mesaAux == null && $mesaAuxEnMesa != null)
            {                             
                $idUsuarioAux = AccesoDatos::retornarObjetoActivoPorCampo($pedido->id_usuario, 'id', 'usuario', 'Usuario');
                $retorno = 3;
                if(sizeof($idUsuarioAux) > 0)
                {
                    $pedidoAux[0]->id_mesa = $pedido->id_mesa;
                    $pedidoAux[0]->id_usuario = $pedido->id_usuario;
                    Pedido::modificarRegistro($pedidoAux[0]);
                    $retorno = 2; 
                }
            }
        }
        return $retorno;
    }

    public static function CambiarEstado($idPedido, $idEstado)
    {
        $pedido = AccesoDatos::retornarObjetoActivo($idPedido, 'pedido', 'Pedido');
        $pedido[0]->estado = $idEstado; //comiendo
        $retorno = 'Mesa pasada a comiendo.';
        switch($idEstado)
        {
            case 3: 
                $pedido[0]->precio_final = Pedido::CalcularPrecio($pedido[0]->id_mesa);//calcular precio           
                $retorno = $pedido[0]->precio_final;
                break;
            case 4: 
                $fecha = new DateTime(date("d-m-Y H:i:s"));
                $pedido[0]->fecha_fin = $fecha->format("Y-m-d H:i:s");
                $retorno = 'Mesa cerrada';
                break;

        }
        Pedido::modificarRegistro($pedido[0]);
        return $retorno;
    }

    public static function CalcularPrecio($mesa)
    {
        $precio = 0;

        $sql = "SELECT pp.cantidad as cantidad, pr.precio as precio 
                FROM mesa m
                    LEFT JOIN pedido p ON m.id = p.id_mesa
                    LEFT JOIN pedido_producto pp ON p.id = pp.id_pedido
                    LEFT JOIN producto pr ON pr.id = pp.id_producto
                WHERE p.id_mesa = $mesa AND p.estado = 2 AND pp.estado = 2;";       
        $lista = AccesoDatos::ObtenerConsulta($sql);

        foreach($lista as $item)
        {
            $precioItem = $item->cantidad * $item->precio;
            $precio = $precio + $precioItem;
        }
        return $precio;
    }

    public function GuardarImagen()
    {     
        $nombreFoto = "foto_pedido_".$this->id.".jpg";
        $destino = ".".DIRECTORY_SEPARATOR."fotospedidos".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR;

        if(!file_exists($destino))
        {
            mkdir($destino, 0777, true);
        }

        $dir = $destino.$nombreFoto;
        move_uploaded_file($this->foto, $dir);
        $this->foto = $dir;
        Pedido::grabarFoto($this);
        return $dir;
    }

    public function crearRegistro() 
    {
        $retorno = null;
        try
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO  pedido (id_mesa, id_cliente, id_usuario, estado, created_at, fecha_prevista, updated_at, activo) 
                                                                  VALUES (:id_mesa, :id_cliente, :id_usuario, :estado, :created_at, :fecha_prevista, :updated_at, :activo)");
            $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':id_cliente', $this->id_cliente, PDO::PARAM_STR); 
            $consulta->bindValue(':id_usuario', $this->id_usuario, PDO::PARAM_STR); 
            $consulta->bindValue(':estado', '1', PDO::PARAM_STR); 
            $consulta->bindValue(':activo', '1', PDO::PARAM_STR);
            $fecha = new DateTime(date("d-m-Y H:i:s"));
            $consulta->bindValue(':created_at', date_format($fecha, 'Y-m-d H:i:s'));
            $consulta->bindValue(':updated_at', date_format($fecha, 'Y-m-d H:i:s'));
            $fecha_prevista = $fecha->modify('+'.$this->fecha_prevista.' minutes');
            $consulta->bindValue(':fecha_prevista', date_format($fecha_prevista,'Y-m-d H:i:s'), PDO::PARAM_STR);
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


    public static function modificarRegistro($pedido)
    {       
        try
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido
                                                          SET id_mesa = :id_mesa, 
                                                              id_cliente = :id_cliente,
                                                              id_usuario = :id_usuario, 
                                                              estado = :estado, 
                                                              fecha_fin = :fecha_fin,
                                                              precio_final = :precio_final,
                                                              activo = :activo,
                                                              foto = :foto,
                                                              updated_at = :updated_at
                                                          WHERE id = :id");
            $consulta->bindValue(':id', $pedido->id, PDO::PARAM_STR);
            $consulta->bindValue(':id_mesa', $pedido->id_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':id_cliente', $pedido->id_cliente, PDO::PARAM_STR);
            $consulta->bindValue(':id_usuario', $pedido->id_usuario);
            $consulta->bindValue(':estado', $pedido->estado, PDO::PARAM_STR);
            $consulta->bindValue(':fecha_fin', $pedido->fecha_fin);
            $consulta->bindValue(':precio_final', $pedido->precio_final);
            $consulta->bindValue(':activo', $pedido->activo, PDO::PARAM_STR);
            $consulta->bindValue(':foto', $pedido->foto, PDO::PARAM_STR);
            $fecha = new DateTime(date("d-m-Y H:i:s"));
            $consulta->bindValue(':updated_at', date_format($fecha, 'Y-m-d H:i:s'));
            $consulta->execute();
        }
        catch(Throwable $mensaje)
        {
            printf("Error al conectar en la base de datos: <br> $mensaje .<br>");
        }
    }

    public static function grabarFoto($pedido)
    {       
        try
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido
                                                          SET foto = :foto,                                                            
                                                              updated_at = :updated_at
                                                          WHERE id = :id");
            $consulta->bindValue(':id', $pedido->id, PDO::PARAM_STR);
            $consulta->bindValue(':foto', $pedido->foto, PDO::PARAM_STR);
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