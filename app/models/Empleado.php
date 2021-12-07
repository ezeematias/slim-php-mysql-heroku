<?php

class Empleado
{
    public $id;
    public $nombre;
    public $sector;
    public $estado;
    public $puesto;
    public $fechaIngreso;
    public $available;

    //region Getters and Setters
    public function GetSector()
    {
        return $this->sector;
    }
    public function GetEstado()
    {
        return $this->estado;
    }
    public function GetPuesto()
    {
        return $this->puesto;
    }
    public function GetFechaDeIngreso()
    {
        return $this->fecha_de_ingreso;
    }

    public function SetSector($value)
    {
        $this->sector = $value;
    }
    public function SetEstado($value)
    {
        $this->estado = $value;
    }
    public function SetPuesto($value)
    {
        $this->puesto = $value;
    }
    public function SetFechaDeIngreso($value)
    {
        $this->fecha_de_ingreso = $value;
    }

    public function GetAvailable()
    {
        return $this->available;
    }

    public function SetAvailable($value)
    {
        $this->available = $value;
    }

    //endregion Getters and Setters

    public function InsertarEmpleado()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into 
        empleados (sector,estado,puesto,fechaIngreso,available)
        values (:sector,:estado,:puesto,:fechaIngreso,:available)");
        $consulta->bindValue(':nombre', $this->sector, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':puesto', $this->puesto, PDO::PARAM_STR);
        $consulta->bindValue(':fechaIngreso', $this->fechaIngreso, PDO::PARAM_STR);
        $consulta->bindValue(':available', $this->available, PDO::PARAM_INT);
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    //1true disponible  0false 
    public static function BorrarEmpleado($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE empleados set available = 0 where id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public  function ModificarEmpleado($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
            update empleados 
            set sector='$this->sector',
            nombre='$this->nombre',
            estado='$this->estado',
            puesto='$this->puesto',
            fechaIngreso='$this->fechaIngreso'
            WHERE id='$id'");
        return $consulta->execute();
    }

    //Trae todos los empleados
  
    public static function TraerEmpleados()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select * from empleados");
        $consulta->execute();
        $empleados = $consulta->fetchAll(PDO::FETCH_CLASS, "Empleado");

        return $empleados;
    }

    //Trae un empleado por ID
    public static function TraerEmpleado($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select * from empleados where id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        $empleadoResultado = $consulta->fetchObject('empleado');
        return $empleadoResultado;
    }

    //Trae un empleado disponible por puesto
    public static function TraerEmpleadoDisponiblePorPuesto($puesto)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select * from empleados where puesto = :puesto and estado = 'disponible' and available='1' ");
        $consulta->bindValue(':puesto', $puesto, PDO::PARAM_STR);
        $consulta->execute();
        $empleadoResultado = $consulta->fetchObject('empleado');

        $cambiarEstado = $objetoAccesoDato->RetornarConsulta("update empleados set estado = 'ocupado' where id = :id");
        $cambiarEstado->bindValue(':id', $empleadoResultado->id, PDO::PARAM_INT);
        $cambiarEstado->execute();


        return $empleadoResultado;
    }

    //Actualiza el estado de un empleado
    public static function LiberarEstadoEmpleado($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("update empleados set estado = 'disponible' where id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        $empleadoResultado = $consulta->fetchObject('empleado');
        return $empleadoResultado;
    }

    public static function Listar($lista)
    {
        foreach ($lista as $obj) {
         //   echo $obj->toString();
            echo Empleado::MostrarDatos($obj);
        }
    }

    public function toString(): String
    {
        return
            "<ul>" .
            "<li>ID: " . $this->id . "" .
            "<li>nombre: " . $this->nombre .
            "<li>sector: " . $this->sector .
            "<li>estado: " . $this->estado .
            "<li>puesto: " . $this->puesto .
            "<li>fechaIngreso: " . $this->fechaIngreso .
            "<li>available: " . $this->available .
            "<ul>";
    }

    public static function MostrarDatos($empleado)
    {
        return
            "<ul>" .
            "<li>id: " . $empleado->id . "" .
            "<li>nombre: " . $empleado->nombre .
            "<li>sector: " . $empleado->sector .
            "<li>estado: " . $empleado->estado .
            "<li>puesto: " . $empleado->puesto .
            "<li>fechaIngreso: " . $empleado->fechaIngreso .
            "<li>available: " . $empleado->available .
            "</ul>";
    }

    //traer empleados por sector y disponible
    public static function TraerEmpleadosPorSector($sector)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select * from empleados where sector = :sector and estado = 'disponible' limit 1");
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Empleado');
    }

    //traer empleados por sector y trabajando
    public static function TraerEmpleadosPorOcupados($sector)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select * from empleados where sector = :sector and estado = 'ocupado' limit 1");
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Empleado');
    }

    public static function Metricas(){
        $listaAnalytics = array();
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        //a- Los días y horarios que se ingresaron al sistema
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select nombre, fechaIngreso from empleados"
        );
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->cantidad = $row['nombre'];
                $rowObj->sector = $row['fechaIngreso'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['a_'] = $rows;
        }

        //b- Cantidad de operaciones de todos por sector
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select count(logs.id) as operaciones , empleados.sector as sector from logs inner join empleados on empleados.id = logs.idEmpleado group by empleados.sector"
        );
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->cantidad = $row['operaciones'];
                $rowObj->sector = $row['sector'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['b_'] = $rows;
        }

        //d- Cantidad de operaciones de cada uno por separado.
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select count(logs.id) as operaciones, empleados.nombre from logs inner join empleados on logs.idEmpleado = empleados.id group by empleados.nombre"
        );
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->cantidad = $row['operaciones'];
                $rowObj->nombre = $row['nombre'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['d_'] = $rows;
        }
        return $listaAnalytics;
    }

    //mostrar con foreach key value
    public static function MostrarMetricas($listaAnalytics)
    {
        foreach ($listaAnalytics as $key => $value) {
            echo "<h1>$key</h1>";
            foreach ($value as $obj) {
                echo $obj->cantidad . " " . $obj->sector . "<br>";
            }
        }
    }



}
