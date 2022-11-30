<?php
include_once("db/AccesoDatos.php");
date_default_timezone_set('America/Buenos_Aires');

class Encuesta 
{
    public $cliente;
    public $pedido;
    public $nota_restaurante;
    public $nota_mozo;
    public $nota_cocinero;
    public $texto;
    public $activo;

    public static function Alta($encuesta)
    {
        $retorno = 0;
        $pedido = AccesoDatos::retornarObjetoActivo($encuesta->pedido, 'pedido', 'Pedido');
        if(sizeof($pedido) != 0)
        {
            $pedidoEnEncuesta = AccesoDatos::retornarObjetoActivoPorCampo($pedido[0]->id, 'pedido', 'encuesta', 'Encuesta');
            if(sizeof($pedido) != 0 && sizeof($pedidoEnEncuesta) == 0)
            {
                $encuesta->cliente = $pedido[0]->id_cliente;
                $encuesta->crearRegistro();
                $retorno = 1;
            }
        }
        return $retorno;  
    }

    public function crearRegistro()
    {
       $retorno = null;
       try
       {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuesta (cliente, pedido, nota_restaurante, nota_mozo, nota_cocinero, texto, activo, created_at, updated_at) 
                                                              VALUES (:cliente, :pedido, :nota_restaurante, :nota_mozo, :nota_cocinero, :texto, :activo, :created_at, :updated_at)");
        $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);
        $consulta->bindValue(':pedido', $this->pedido, PDO::PARAM_STR);
        $consulta->bindValue(':nota_restaurante', $this->nota_restaurante, PDO::PARAM_STR);
        $consulta->bindValue(':nota_mozo', $this->nota_mozo, PDO::PARAM_STR);
        $consulta->bindValue(':nota_cocinero', $this->nota_cocinero, PDO::PARAM_STR);
        $consulta->bindValue(':texto', $this->texto, PDO::PARAM_STR);
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

    public static function modificarRegistro($item)
    {       
        try
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE encuesta
                                                          SET cliente = :cliente, 
                                                              pedido = :pedido,
                                                              nota_restaurante = :nota_restaurante,
                                                              nota_mozo = :nota_mozo,
                                                              nota_cocinero = :nota_cocinero,
                                                              texto = :texto,
                                                              activo = :activo,
                                                              updated_at = :updated_at
                                                          WHERE id = :id");
            $consulta->bindValue(':id', $item->id, PDO::PARAM_STR);
            $consulta->bindValue(':cliente', $item->cliente, PDO::PARAM_STR);
            $consulta->bindValue(':pedido', $item->pedido, PDO::PARAM_STR);
            $consulta->bindValue(':nota_restaurante', $item->nota_restaurante, PDO::PARAM_STR);
            $consulta->bindValue(':nota_mozo', $item->nota_mozo, PDO::PARAM_STR);
            $consulta->bindValue(':nota_cocinero', $item->nota_cocinero, PDO::PARAM_STR);
            $consulta->bindValue(':texto', $item->texto, PDO::PARAM_STR);
            $consulta->bindValue(':activo', $item->texto, PDO::PARAM_STR);
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