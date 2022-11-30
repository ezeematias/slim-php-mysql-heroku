<?php
include_once("db/AccesoDatos.php");
include_once("importexport/CSV.php");
include_once("interfaces/IEntidad.php");
date_default_timezone_set('America/Buenos_Aires');


class Mesa implements IEntidad
{
    public $id;
    public $nombre;
    public $activo;
    public $created_at;
    public $updated_at;

    public static function Alta($mesa)
    {
        $retorno = 0;
        $mesaAux = AccesoDatos::retornarObjetoPorCampo($mesa->nombre, "nombre", "mesa", "Mesa");
        if($mesaAux == null)
        {
            $mesa->crearRegistro();
            $retorno = 1;
        } 
        else
        {
            $mesaAux[0]->activo = 1;
            Sector::modificarRegistro($mesaAux[0]);
            $retorno = 2;
        }      
        return $retorno;
    }

    public static function Baja($id)
    { 
        $retorno = 0;
        $mesaAux = AccesoDatos::retornarObjetoActivo($id, 'mesa', 'Mesa');
        //var_dump($mesaAux);
        if($mesaAux != null)
        {
            $estaOcupada = Mesa::EstaOcupada($mesaAux[0]);
            $retorno = 2;
            if($estaOcupada == 0)
            {
                AccesoDatos::borrarRegistro($id, 'mesa');
                $retorno = 1;
            }
        }         
        return $retorno;
    }

    public static function EstaOcupada($mesa)
    {
        $retorno = 0;
        $sql = "SELECT * 
                FROM pedido 
               WHERE id_mesa = $mesa->id AND pedido.activo = '1' AND pedido.estado < '4';";
        
        $mesas = AccesoDatos::ObtenerConsulta($sql);
        if(sizeof($mesas) > 0)
        {
            $retorno = 1;
        }
        return $retorno;
    }

    public static function Modificacion($mesa)
    {
        $retorno = 3;
        $mesaAux = AccesoDatos::retornarObjetoActivo($mesa->id, 'mesa', 'Mesa');

        if($mesaAux != null)
        {
            $mesaAuxNombre = AccesoDatos::retornarObjetoPorCampo($mesa->nombre, 'nombre', 'mesa', 'Mesa');
            $retorno = 2; //es el mismo nombre
            if($mesaAuxNombre == null)
            {
                $mesa->activo = 1;
                Mesa::modificarRegistro($mesa);
                $retorno = 1; //se cambia el nombre 
            }
        }
        return $retorno;
    }
       
    public static function CargarCSV($archivo)
    {
        $array = CSV::LeerCsv($archivo);
        //var_dump($array);
        for($i = 0; $i < sizeof($array); $i++)
        {
            $mesa = new Mesa();
            $mesa->nombre = $array[$i];
            $mesa->crearRegistro();
        }
    }
  
    public function crearRegistro()
    {
        $retorno = null;
        try
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesa (nombre, activo, created_at, updated_at) 
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
            $consulta = $objAccesoDato->prepararConsulta("UPDATE mesa
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