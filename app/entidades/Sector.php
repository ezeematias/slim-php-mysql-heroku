<?php
include_once("interfaces/IEntidad.php");
date_default_timezone_set('America/Buenos_Aires');

class Sector implements IEntidad
{
    public $id;
    public $nombre;
    public $activo;
    public $created_at;
    public $updated_at;

    public static function Alta($sector)
    {
        $retorno = 0;
        $sectorAux = AccesoDatos::retornarObjetoPorCampo($sector->nombre, "nombre", "sector", "Sector");
        if($sectorAux == null)
        {
            $sector->crearRegistro();
            $retorno = 1;
        } 
        else
        {
            $sectorAux[0]->activo = 1;
            Sector::modificarRegistro($sectorAux[0]);
            $retorno = 2;
        }      
        return $retorno;
    }

    public static function Baja($id)
    { 
        $retorno = 0;
        $sectorAux = AccesoDatos::retornarObjetoActivo($id, 'sector', 'Sector');

        if($sectorAux != null)
        {
            $productoAux = AccesoDatos::retornarObjetoActivoPorCampo($id, 'id_sector', 'producto', 'Producto');
            $retorno = 2;
            if(sizeof($productoAux) == 0)
            {
                AccesoDatos::borrarRegistro($id, 'sector');
                $retorno = 1;
            }
        }         
        return $retorno;
    }

    public static function Modificacion($sector)
    {
        $retorno = 3;
        $sectorAux = AccesoDatos::retornarObjetoActivo($sector->id, 'sector', 'Sector');

        if($sectorAux != null)
        {
            $sectorAuxNombre = AccesoDatos::retornarObjetoPorCampo($sector->nombre, 'nombre', 'sector', 'Sector');
            $retorno = 2; //es el mismo nombre
            if($sectorAuxNombre == null)
            {
                $sector->activo = 1;
                Sector::modificarRegistro($sector);
                $retorno = 1; //se cambia el nombre 
            }
        }
        return $retorno;
    }
       
    public function crearRegistro()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO sector (nombre, activo, created_at, updated_at) 
                                                              VALUES (:nombre, :activo, :created_at, :updated_at)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':activo', '1', PDO::PARAM_STR);
        $fecha = new DateTime(date("d-m-Y H:i:s"));
        $consulta->bindValue(':created_at', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->bindValue(':updated_at', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function modificarRegistro($item)
    {       
        try
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE sector
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