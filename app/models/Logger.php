<?php
class Logger
{
    public $id;
    public $idEmpleado;
    public $fecha;
    public $accion;
    
    public function GetIdEmpleado() {
        return $this->idEmpleado;
    }
    public function GetFecha() {
        return $this->fecha;
    }
    public function GetAccion() {
        return $this->accion;
    }

    public function SetIdEmpleado($value) {
        $this->idEmpleado = $value;
    }
    public function SetFecha($value) {
        $this->fecha = $value;
    }
    public function SetAccion($value) {
        $this->accion = $value;
    }

    public function InsertarLog() {
        $datetime_now = date("Y-m-d H:i:s");
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into logs (idEmpleado,fecha,accion)values(:idEmpleado,:fecha,:accion)");
        $consulta->bindValue(':idEmpleado', $this->idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', $datetime_now, PDO::PARAM_STR);
        $consulta->bindValue(':accion', $this->accion, PDO::PARAM_STR);
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function TraerLogs() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select l.id, e.usuario as idEmpleado, l.fecha,l.accion FROM logs l LEFT JOIN empleados e on l.idEmpleado = e.id");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Log");
    }

    public static function TraerLog($id) {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select * FROM logs where id=$id");
        $consulta->execute();
        $logResultado = $consulta->fetchObject('Log');
        return $logResultado;
    }

    public function toString() {
        return 
         "<ul>" .
         "<li>"."idEmpleado: ".$this->idEmpleado."</li>" .
         "<li>"."fecha: ".$this->fecha."</li>".
         "<li>"."accion: ".$this->accion.
         "</ul>";
    }
}
?>