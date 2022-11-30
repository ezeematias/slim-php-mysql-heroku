<?php
include_once("interfaces/IEntidad.php");
date_default_timezone_set('America/Buenos_Aires');

class TipoUsuario implements IEntidad
{
    public $id;
    public $nombre;
    public $activo;
    public $created_at;
    public $updated_at;
       
    public static function Alta($tipoUsuario)
    {
        $retorno = 0;
        $tipoAux = AccesoDatos::retornarObjetoPorCampo($tipoUsuario->nombre, "nombre", "tipo_usuario", "TipoUsuario");
        if($tipoAux == null)
        {
            $tipoUsuario->crearRegistro();
            $retorno = 1;
        } 
        else
        {
            $tipoAux[0]->activo = 1;
            TipoUsuario::modificarRegistro($tipoAux[0]);
            $retorno = 2;
        }      
        return $retorno;
    }

    public static function Baja($id)
    { 
        $retorno = 0;
        $tipoAux = AccesoDatos::retornarObjetoActivo($id, 'tipo_usuario', 'TipoUsuario');

        if($tipoAux != null)
        {
            $usuarioAux = AccesoDatos::retornarObjetoActivoPorCampo($id, 'tipo', 'usuario', 'Usuario');
            $retorno = 2;
            if(sizeof($usuarioAux) == 0)
            {
                AccesoDatos::borrarRegistro($id, 'Tipo_usuario');
                $retorno = 1;
            }
        }         
        return $retorno;
    }

    public static function Modificacion($tipoUsuario)
    {
        $retorno = 3;   
        $tipoAux = AccesoDatos::retornarObjetoActivo($tipoUsuario->id, 'tipo_usuario', 'TipoUsuario');

        if($tipoAux != null)
        {
            $sectorAuxNombre = AccesoDatos::retornarObjetoPorCampo($tipoUsuario->nombre, 'nombre', 'tipo_usuario', 'TipoUsuario');
            $retorno = 2; //es el mismo nombre
            if($sectorAuxNombre == null)
            {
                $tipoUsuario->activo = 1;
                Sector::modificarRegistro($tipoUsuario);
                $retorno = 1; //se cambia el nombre 
            }
        }
        return $retorno;
    }
    
    public function crearRegistro()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO tipo_usuario (nombre, activo, updated_at, created_at) 
                                                                     VALUES (:nombre, :activo, :updated_at, :created_at)");

        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':activo', '1', PDO::PARAM_STR);
        $fecha = new DateTime(date("d-m-Y H:i:s"));
        $consulta->bindValue(':updated_at', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->bindValue(':created_at', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function modificarRegistro($item)
    {       
        try
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE tipo_usuario
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