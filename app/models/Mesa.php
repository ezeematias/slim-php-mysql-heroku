<?php
class Mesa
{
    public $id;
    public $codigo;
    public $estado;

    public function GetCodigo()
    {
        return $this->codigo;
    }
    public function GetEstado()
    {
        return $this->estado;
    }

    public function SetCodigo($value)
    {
        $this->codigo = $value;
    }
    public function SetEstado($value)
    {
        $this->estado = $value;
    }

    public function InsertarMesa()
    {
        $nuevoCodigo = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 5);
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "INSERT into mesas (codigo,estado)values(:codigo,:estado)"
        );
        $consulta->bindValue(':codigo', $nuevoCodigo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();
        return $nuevoCodigo;
    }

    public function BorrarMesa()
    {

        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "update mesas 
            set available=0
            WHERE codigo=:codigo");
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }

    /**
     * asdasdasdasdasdasd
     */
    public function ModificarMesa()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
            update mesas 
            set codigo=:codigo,estado=:estado
            WHERE id=:id");
        $consulta->bindValue(':codigo', trim($this->codigo), PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function TraerMesa($codigo)
    {
        //echo codigo de la mesa
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,codigo,estado from mesas where codigo = :codigo"
        );
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
        $mesaResultado = $consulta->fetchObject('Mesa');
        return $mesaResultado;
    }

    public static function TraerMesas()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select * from mesas");
        $consulta->execute();
        $mesas = $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
        return $mesas;
    }

    public static function TraerMesasPorEstado($estado)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select * from mesas where estado = :estado");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        $mesas = $consulta->fetchAll(PDO::FETCH_CLASS, "Mesa");
        return $mesas;
    }

    //traer mesas  cerradas
    public static function TraerMesaCerrada()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select * from mesas where estado = 'cerrada' and available=1 LIMIT 1");
        $consulta->execute();
        $mesa = $consulta->fetchObject('Mesa');
        return $mesa;
    }

    //traer mesas  cerradas
    public static function TraerMesasConClientePagando()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select * from mesas where estado = 'con cliente pagando' and available=1 LIMIT 1");
        $consulta->execute();
        $mesa = $consulta->fetchObject('Mesa');
        return $mesa;
    }

    public static function MesaMasUsada(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
        select count(idMesa) as mesa_mas_usada from comandas
        group by idMesa");
        $consulta->execute();
        return $consulta->fetch();
    }

    

    public static function TraerMesaPorId($id)
    {
        //echo codigo de la mesa
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select id,codigo,estado from mesas where id = :id"
        );
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        $mesaResultado = $consulta->fetchObject('Mesa');
        return $mesaResultado;
    }

    public static function CerrarMesasSinComanda()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
        update mesas 
        set estado = 'cerrada'
        WHERE codigo not in (SELECT idMesa FROM comandas)");
        $consulta->execute();
        return $consulta->rowCount();
    }


    public static function Listar($lista)
    {
        foreach ($lista as $obj) {
            echo $obj->toString();
        }
    }

    public function toString(): String
    {
        return
            "<ul>" .
            "<li>id: " . $this->id .
            "<li>codigo: " . $this->codigo .
            "<li>estado: " . $this->estado .
            "</ul>";
    }

    public static function MostrarDatos($mesa)
    {
        return
            "<ul>" .
            "<li>id: " . $mesa->id .
            "<li>codigo: " . $mesa->codigo .
            "<li>estado: " . $mesa->estado .
            "</ul>";
    }

    // 9- De las mesas:










    public static function Metricas($desde, $hasta)
    
    {
        $listaAnalytics = array();
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        // a- La más usada.
        $consulta = $objetoAccesoDato->RetornarConsulta(
        "select count(idMesa) as mesa_mas_usada , idMesa as mesa from comandas
        group by idMesa"
        );
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->mesa_mas_usada = $row['mesa_mas_usada'];
                $rowObj->mesa = $row['mesa'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['a_'] = $rows;
        }

            // b- La menos usada.
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select SUM(idMesa) as mesa_menos_usada , idMesa as mesa from comandas
            group by idMesa
            order by mesa_menos_usada asc
            limit 1"
        );
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->mesa_menos_usada = $row['mesa_menos_usada'];
                $rowObj->idMesa = $row['idMesa'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['b_'] = $rows;
        }

        // c- La que más facturó.
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select SUM(importe) as masFacturo , idMesa as mesa from comandas
                group by idMesa
                limit 1"
        );
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->masFacturo = $row['masFacturo'];
                $rowObj->idMesa = $row['idMesa'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['c_'] = $rows;
        }


        // d- La que menos facturó.
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select SUM(importe) as menosFacturo , idMesa as mesa from comandas
group by idMesa
order by menosFacturo asc
limit 1"
        );
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->menosFacturo = $row['menosFacturo'];
                $rowObj->idMesa = $row['idMesa'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['d_'] = $rows;
        }

        // e- La/s que tuvo la factura con el mayor importe.
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select SUM(importe) as masFacturo , idMesa as mesa from comandas
                group by idMesa
                "
        );
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->masFacturo = $row['masFacturo'];
                $rowObj->idMesa = $row['idMesa'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['e_'] = $rows;
        }

        // f- La/s que tuvo la factura con el menor importe.
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select SUM(importe) as menosFacturo , idMesa as mesa from comandas
group by idMesa
order by menosFacturo asc
"
        );
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->menosFacturo = $row['menosFacturo'];
                $rowObj->idMesa = $row['idMesa'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['f_'] = $rows;
        }


        // g- Lo que facturó entre dos fechas dadas.
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select SUM(importe) as facturo , idMesa as mesa from comandas
            left join pedidos on pedidos.idComanda = comandas.id
            where pedidos.fechaIngresado BETWEEN :desde AND :hasta AND pedidos.estado = 'COBRADO'
            group by comandas.idMesa");
        $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
        $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->facturo = $row['facturo'];
                $rowObj->mesa = $row['mesa'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['g_'] = $rows;
        }

        // h- Mejores comentarios.
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select max(mesa) as mesa_mejor_puntuada from encuestas where texto LIKE '%muy buena%' OR texto LIKE '%buena%'"
        );
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->mesa_mejor_puntuada = $row['mesa_mejor_puntuada'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['h_'] = $rows;
        }

        // i- Peores comentario
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select min(mesa) as mesa_peor_puntuada from encuestas where texto LIKE '%muy mala%' OR texto LIKE '%mala%'"
        );
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->mesa_peor_puntuada = $row['mesa_peor_puntuada'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['i_'] = $rows;
        }


        return $listaAnalytics;
    }

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
