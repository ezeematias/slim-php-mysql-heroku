<?php
class AccesoDatos
{
    private static $objAccesoDatos;
    private $objetoPDO;

    private function __construct()
    {
        try {
            $this->objetoPDO = new PDO('mysql:host='.$_ENV['MYSQL_HOST'].';dbname='.$_ENV['MYSQL_DB'].';charset=utf8', $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASS'], array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            //$this->objetoPDO = new PDO("mysql:host=127.0.0.1:3306;dbname=comandaApp", 'root', '', array(PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $this->objetoPDO->exec("SET CHARACTER SET utf8");
        } catch (PDOException $e) {
            print "Error: " . $e->getMessage();
            die();
        }
    }

    public static function obtenerInstancia()
    {
        if (!isset(self::$objAccesoDatos)) {
            self::$objAccesoDatos = new AccesoDatos();
        }
        return self::$objAccesoDatos;
    }

    public function prepararConsulta($sql)
    {
        return $this->objetoPDO->prepare($sql);
    }

    public function obtenerUltimoId()
    {
        return $this->objetoPDO->lastInsertId();
    }

    public function __clone()
    {
        trigger_error('ERROR: La clonación de este objeto no está permitida', E_USER_ERROR);
    }

    public static function ObtenerConsulta($sql, $clase = null)
    {
        try
        {
            $conexion = AccesoDatos::obtenerInstancia();
            $consulta = $conexion->prepararConsulta($sql);
            $consulta->execute();
            $retorno = $consulta->fetchAll(PDO::FETCH_CLASS, $clase);
            
        }
        catch(Throwable $mensaje)
        {
            printf("Error de la BD: <br> $mensaje .<br>");
        }
        finally
        {
            return $retorno;
        }    
    }

    public static function borrarPorCondicion($valor, $campo, $tabla)
    {
        $retorno = false;
        try
        {   
            if($campo != 'id')
            {
                $conexion = AccesoDatos::obtenerInstancia();
                $consulta = $conexion->prepararConsulta("UPDATE $tabla 
                                                         SET activo = '0', updated_at = :updated_at 
                                                         WHERE $valor = $campo");
                $fecha = new DateTime(date("d-m-Y"));
                $consulta->bindValue(':updated_at', date_format($fecha, 'Y-m-d H:i:s'));
                $consulta->execute();
                $retorno = true;
            }
        }
        catch(Throwable $mensaje)
        {
            printf("Error al borrar en la base de datos: <br> $mensaje .<br>");
        }
        finally
        {
            return $retorno;
        }
    }

    public static function modificarCampo($id, $tabla, $campo, $valor)
    {
        $retorno = false;
        try
        {   
            if($id != null && $campo != 'id')
            {
                $conexion = AccesoDatos::obtenerInstancia();
                $consulta = $conexion->prepararConsulta("UPDATE $tabla 
                                                         SET $campo = $valor, updated_at = :updated_at 
                                                         WHERE id = $id");
                $fecha = new DateTime(date("d-m-Y"));
                $consulta->bindValue(':updated_at', date_format($fecha, 'Y-m-d H:i:s'));
                $consulta->execute();
                $retorno = true;
            }
        }
        catch(Throwable $mensaje)
        {
            printf("Error al modificar en la base de datos: <br> $mensaje .<br>");
        }
        finally
        {
            return $retorno;
        }
    }

    public static function borrarRegistro($id, $tabla)
    {
        return AccesoDatos::modificarCampo($id, $tabla, 'activo', '0');
    }

    public static function retornarObjeto($id, $tabla, $clase)
    {
        $sql = "SELECT * FROM $tabla WHERE $id = $tabla.id";
        return AccesoDatos::ObtenerConsulta($sql, $clase);
    }

    public static function retornarObjetoActivo($id, $tabla, $clase)
    {
        $sql = "SELECT * FROM $tabla WHERE $id = $tabla.id AND $tabla.activo = 1";
        return AccesoDatos::ObtenerConsulta($sql, $clase);
    }

    public static function retornarObjetoPorCampo($valor, $campo, $tabla, $clase)
    {
        $sql = "SELECT * FROM $tabla WHERE $tabla.$campo = '$valor'";
        return AccesoDatos::ObtenerConsulta($sql, $clase);
    }

    public static function retornarObjetoActivoPorCampo($valor, $campo, $tabla, $clase)
    {
        $sql = "SELECT * FROM $tabla WHERE $tabla.$campo = '$valor' AND $tabla.activo = 1";
        return AccesoDatos::ObtenerConsulta($sql, $clase);
    }

    public static function obtenerTodos($tabla, $clase)
    {
        $sql = "SELECT * FROM $tabla;";
        return AccesoDatos::ObtenerConsulta($sql, $clase);
    }

    public static function ObtenerPedidosPorSector($sector)
    {
        $sql = "SELECT  pp.id,
                        pp.id_pedido AS pedido, 
                        pr.nombre AS producto, 
                        pp.cantidad AS cantidad, 
                        CASE 
                        WHEN pp.estado = 0 THEN 'Pendiente' 
                        WHEN pp.estado = 1 THEN 'En preparación' 
                        WHEN pp.estado = 2 THEN 'Listo' 
                        ELSE 'Error' end
                        as Estado 
                FROM pedido_producto pp 
                    LEFT JOIN producto pr ON pp.id_producto = pr.id
                    LEFT JOIN sector s ON pr.id_sector = s.id
                WHERE s.id = $sector and pp.estado < 2
                ORDER BY pp.id_pedido, pp.created_at;";
         
        return AccesoDatos::ObtenerConsulta($sql);

    }

    public static function ImprimirTabla($tabla, $clase)
    {           
        $retorno = null;
        try
        {
            $retorno = array();
            $conexion = AccesoDatos::obtenerInstancia();
            $retorno = $conexion->obtenerTodos($tabla, $clase);
            //$retorno = AccesoDatos::ImprimirArray($resultado);
        }
        catch (Throwable $mensaje)
        {
            printf("Error al consultar la base de datos: <br> $mensaje .<br>");
            die();
        }
        finally
        {
            return $retorno;
        }  
    }

}
