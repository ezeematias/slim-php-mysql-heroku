<?php
date_default_timezone_set('America/Buenos_Aires');

class Log 
{
    public $usuario;
    public $accion;

    public function __construct($usuario, $accion)
    {
        $this->usuario = $usuario;
        $this->accion = $accion;
    }
    public static function Alta($log)
    {
        return $log->crearRegistro();
    }

    public function crearRegistro()
    {
        $retorno = null;
        try
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO log (usuario, accion, activo, created_at, updated_at) 
                                                                  VALUES (:usuario, :accion, :activo, :created_at, :updated_at)");
            $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
            $consulta->bindValue(':accion', $this->accion, PDO::PARAM_STR);
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
            $consulta = $objAccesoDato->prepararConsulta("UPDATE 'log'
                                                          SET usuario = :usuario, 
                                                              accion = :accion,
                                                              activo = :activo,
                                                              updated_at = :updated_at
                                                          WHERE id = :id");
            $consulta->bindValue(':usuario', $item->usuario, PDO::PARAM_STR);
            $consulta->bindValue(':accion', $item->accion, PDO::PARAM_STR);
            $consulta->bindValue(':activo', $item->activo, PDO::PARAM_STR);
            $fecha = new DateTime(date("d-m-Y H:i:s"));
            $consulta->bindValue(':updated_at', date_format($item, 'Y-m-d H:i:s'));
            $consulta->execute();
        }
        catch(Throwable $mensaje)
        {
            printf("Error al conectar en la base de datos: <br> $mensaje .<br>");
        }
    }
}
?>