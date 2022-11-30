<?php

date_default_timezone_set('America/Buenos_Aires');

class Cliente 
{
    public $id;
    public $nombre;

    
    public $activo;
    public $created_at;
    public $updated_at;
       
    public function __construct($nombre)
    {
        $this->nombre = $nombre;
    }

    public static function Alta($cliente)
    {
        return $cliente->crearRegistro();
    }
    
    public function crearRegistro()
    {
       $retorno = null;
       try
       {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO cliente (nombre, activo, created_at, updated_at) 
                                                              VALUES (:nombre, :activo, :created_at, :updated_at)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
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
            $consulta = $objAccesoDato->prepararConsulta("UPDATE cliente
                                                          SET nombre = :nombre, 
                                                              activo = :activo,
                                                              updated_at = :updated_at
                                                          WHERE id = :id");
            $consulta->bindValue(':id', $item->id, PDO::PARAM_STR);
            $consulta->bindValue(':nombre', $item->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':activo', $item->activo, PDO::PARAM_STR);
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