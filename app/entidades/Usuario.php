<?php
include_once("db/AccesoDatos.php");
include_once("entidades/Log.php");
include_once("interfaces/IEntidad.php");

date_default_timezone_set('America/Buenos_Aires');

class Usuario implements IEntidad
{
    public $id;
    public $dni;
    public $clave;
    public $tipo;
    public $activo;
    public $created_at;
    public $updated_at;
    
    public static function Alta($usuario)
    {
        $retorno = -1;
        $tipoAux= AccesoDatos::retornarObjetoActivoPorCampo($usuario->tipo, "nombre", "tipo_usuario", "TipoUsuario");
        $usuarioAux =  AccesoDatos::retornarObjetoPorCampo($usuario->dni, "dni", "usuario", "Usuario");

        if($tipoAux== null)
        {
            $retorno = 0;
        }
        else
        {
            if($usuarioAux != null)
            {
                $usuarioAux[0]->tipo = $tipoAux[0]->id;
                Usuario::modificarRegistro($usuarioAux[0]);
                $retorno = 1;
            }
            else
            {
                $usuario->tipo = $tipoAux[0]->id;
                //var_dump($usuario->tipo);
                $usuario->crearRegistro();
                $retorno = 2;
            }
        }
        return $retorno;
    }

    public static function Baja($id)
    { 
        $retorno = 0;
        $usuarioAux = AccesoDatos::retornarObjetoActivo($id, 'usuario', 'Usuario');

        if($usuarioAux != null)
        {           
            $tienePedidosPendientes = Usuario::TienePedidosPendientes($usuarioAux[0]);
            $retorno = 2;
            if($tienePedidosPendientes == 0)
            {
                AccesoDatos::borrarRegistro($id, 'usuario');
                $retorno = 1;
            }
        }         
        return $retorno;
    }

    public static function Modificacion($usuario)
    {
        $retorno = 3;
    
        $usuarioAux = AccesoDatos::retornarObjetoActivo($usuario->id, 'usuario', 'Usuario');
        if($usuarioAux != null)
        {
            $usuarioAuxDNI = AccesoDatos::retornarObjetoPorCampo($usuario->dni, 'dni', 'usuario', 'Usuario');
            $retorno = 2; //es el mismo dni
            if($usuarioAuxDNI  == null || $usuarioAuxDNI[0]->id == $usuario->id)
            {
                $usuario->activo = 1;
                Usuario::modificarRegistro($usuario);
                $retorno = 1; //se cambia el nombre 
            }
        }
        return $retorno;
    }

    public static function Login($dni, $clave)
    {
        $retorno = null;
        $idDni = AccesoDatos::retornarObjetoActivoPorCampo($dni, 'dni', 'usuario', 'Usuario');
        if($idDni != null)
        {
            if(password_verify($clave, $idDni[0]->clave))
            {
                $log = new Log($idDni[0]->id, 'Login');
                Log::Alta($log);
                $retorno = $idDni[0];
            }
        }
        return $retorno;
    }

    public static function TienePedidosPendientes($usuario)
    {

        $retorno = 1;
        $sqlEnPedido = "SELECT * FROM pedido WHERE id_usuario = $usuario->id 
                                                   AND activo = '1'
                                                   AND estado < 4;";
        $enPedido = AccesoDatos::ObtenerConsulta($sqlEnPedido, 'Pedido');

        $sqlEnPedidoProducto = "SELECT * FROM pedido_producto WHERE id_usuario = $usuario->id 
                                                                    AND activo = '1'
                                                                    AND estado < 3;";
        $enPedidoProducto = AccesoDatos::ObtenerConsulta($sqlEnPedidoProducto, 'PedidoProducto');

        if(sizeof($enPedido) == 0 && sizeof($enPedidoProducto) == 0)
        {
            $retorno = 0;
        }

        return $retorno;
    }
    //Manejo BD
    public function crearRegistro()
    {
        $retorno = null;
        try
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuario (dni, clave, tipo, activo, created_at, updated_at) 
                                                                         VALUES (:dni, :clave, :tipo, :activo, :created_at, :updated_at) ");
            $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
            $consulta->bindValue(':dni', $this->dni, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $claveHash);
            $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':activo', '1', PDO::PARAM_STR);
            $fecha = new DateTime(date("d-m-Y H:i:s"));
            $consulta->bindValue(':created_at', date_format($fecha, 'Y-m-d H:i:s')); //POR QUÉ NO GRABA LA HORA?
            $consulta->bindValue(':updated_at', date_format($fecha, 'Y-m-d H:i:s'));
            $consulta->execute();
            $retorno =  $objAccesoDatos->obtenerUltimoId();
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

    public static function modificarRegistro($usuario)
    {       
        try
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE usuario
                                                          SET dni = :dni, 
                                                              clave = :clave, 
                                                              tipo = :tipo, 
                                                              activo = :activo,
                                                              updated_at = :updated_at
                                                          WHERE id = :id");
            //$claveHash = password_hash($usuario->clave, PASSWORD_DEFAULT);
            $consulta->bindValue(':id', $usuario->id, PDO::PARAM_STR);
            $consulta->bindValue(':dni', $usuario->dni, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $usuario->clave);
            $consulta->bindValue(':tipo', $usuario->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':activo', '1', PDO::PARAM_STR);
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