<?php

require_once './models/Usuario.php';
class Log
{
    public $fecha;
    public $nombre_empleado;

    public static function RegistrarLog($usuario)
    {
        $usuarioLog = Usuario::ObtenerPorUsuario($usuario);
        $hora_login = date("Y-m-d H:i:s");
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO registro (id_usuario, fecha) VALUES ( :id_usuario, :fecha)");
        $consulta->bindValue(':fecha', $hora_login, PDO::PARAM_STR);
        $consulta->bindValue(':id_usuario', $usuarioLog->id_empleado, PDO::PARAM_STR);
        $consulta->execute();

        return true;
    }

    public static function ObtenerRegistrosPorUsuario($id_empleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT R.fecha, E.nombre_empleado FROM registro AS R LEFT JOIN empleado AS E ON R.id_usuario = E.id_empleado WHERE id_empleado = :id_empleado");
        $consulta->bindValue(':id_empleado', $id_empleado, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Log');
    }

    public static function ObtenerRegistrosPorUsuarioFechas($id_empleado, $desde, $hasta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT R.fecha, E.nombre_empleado FROM registro AS R LEFT JOIN empleado AS E ON R.id_usuario = E.id_empleado WHERE fecha between :desde and :hasta AND id_empleado = :id_empleado");
        $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
        $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        $consulta->bindValue(':id_empleado', $id_empleado, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Log');
    }    
}

?>