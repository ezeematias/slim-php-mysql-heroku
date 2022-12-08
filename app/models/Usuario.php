<?php

class Usuario
{
    public $id_empleado;
    public $usuario;
    public $clave;
    public $id_tipo;
    public $id_sector;
    public $nombre_empleado;
    public $estado;
    public $fecha_registro;
    public $fecha_ultimo_login;

    public function CrearUsuario()
    {
        $hora_login = date("Y-m-d H:i:s");
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO empleado (id_empleado, usuario, clave, id_tipo, id_sector, nombre_empleado,  estado, fecha_registro, fecha_ultimo_login) VALUES (:id_empleado, :usuario, :clave, :id_tipo, :id_sector, :nombre_empleado, :estado, :fecha_registro, :fecha_ultimo_login)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':id_empleado', $this->id_empleado, PDO::PARAM_INT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':id_tipo', $this->id_tipo, PDO::PARAM_INT);
        $consulta->bindValue(':id_sector', $this->id_sector, PDO::PARAM_INT);
        $consulta->bindValue(':nombre_empleado', $this->nombre_empleado, PDO::PARAM_STR);
        $consulta->bindValue(':estado', 1, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_registro', $hora_login, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_ultimo_login', $hora_login, PDO::PARAM_STR);
        $consulta->execute();
        
        return true;
    }

    public static function ObtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleado WHERE estado = 1");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public function ObtenerPorTipo($tipo)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleado WHERE id_tipo = :id_tipo AND estado = 1");
            $consulta->bindValue(':id_tipo', $tipo, PDO::PARAM_INT);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");
    }

    public static function ObtenerPorSector($sector)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleado WHERE id_sector = :id_sector AND estado = 1");
            $consulta->bindValue(':id_sector', $sector, PDO::PARAM_INT);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");
    }

    public static function ObtenerPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleado WHERE id_empleado = :id_empleado");
        $consulta->bindValue(':id_empleado', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function ObtenerPorUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleado WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function ModificarUsuario($objeto)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleado SET usuario = :usuario, clave = :clave, id_tipo = :id_tipo, nombre_empleado = :nombre_empleado WHERE id_empleado = :id_empleado");
        $claveHash = password_hash($objeto->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':id_empleado', $objeto->id_empleado, PDO::PARAM_INT);
        $consulta->bindValue(':usuario', $objeto->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash, PDO::PARAM_STR);
        $consulta->bindValue(':id_tipo', $objeto->id_tipo, PDO::PARAM_INT);
        $consulta->bindValue(':nombre_empleado', $objeto->nombre_empleado, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function CambiarEstadoUsuario($obj, $estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleado SET estado = :estado WHERE id_empleado = :id_empleado");
        $consulta->bindValue(':id_empleado', $obj->id_empleado, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function Login($user, $clave)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT id_empleado, usuario, clave, id_tipo, nombre_empleado, estado FROM empleado as E WHERE E.usuario = :user AND E.clave = :clave AND E.estado = 1");
        $consulta->execute(array(":user" => $user, ":clave" => $clave));
        $resultado = $consulta->fetch();
        return $resultado;
    }

    public static function GetHash($user)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT clave FROM empleado as E WHERE E.usuario = :user");
        $consulta->execute(array(":user" => $user));
        $resultado = $consulta->fetch();
        return $resultado;
    }

    public static function ActualizarFechaLogin($id_usuario)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        date_default_timezone_set("America/Argentina/Buenos_Aires");
        $fecha = date('Y-m-d H:i:s');
        $consulta = $objetoAccesoDato->prepararConsulta("UPDATE empleado SET fecha_ultimo_login = :fecha WHERE id_empleado = :id");
        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id_usuario, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function ObtenerIngresos($fecha1, $fecha2)
    {
        if ($fecha2 == null) {
            echo 'Ingresos para la fecha ' . $fecha1 . PHP_EOL;
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT fecha_ultimo_login, usuario, nombre_empleado FROM empleado WHERE fecha_ultimo_login = :fecha_ultimo_login");
            $consulta->bindValue(':fecha_ultimo_login', $fecha1, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        } else {
            echo 'Ingresos entre la fecha ' . $fecha1 . ' y ' . $fecha2 . PHP_EOL;
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT fecha_ultimo_login, usuario, nombre_empleado FROM empleado WHERE fecha_ultimo_login BETWEEN :fecha_ultimo_login AND :fecha_ultimo_login2");
            $consulta->bindValue(':fecha_ultimo_login', $fecha1, PDO::PARAM_STR);
            $consulta->bindValue(':fecha_ultimo_login2', $fecha2, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        }
    }

    public static function AsignarEmpleado($id_empleado, $id_tipo)
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleado WHERE id_empleado = :id_empleado AND id_tipo = :id_tipo AND estado = 1");
        $consulta->bindValue(':id_empleado', $id_empleado, PDO::PARAM_INT);
        $consulta->bindValue(':id_tipo', $id_tipo, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');

    }    
}