<?php

class Producto
{
    public $id;
    public $nombre;
    public $tipo;
    public $precio;
    public $available;

    //region Getters and Setters

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }

    public function getAvailable()
    {
        return $this->available;
    }

    public function setAvailable($available)
    {
        $this->available = $available;
    }
    

    //endregion Getters and Setters

    public function InsertarProducto()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into 
        productos (nombre,tipo,precio,available)
        values(:nombre,:tipo,:precio,:available)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':available', 1, PDO::PARAM_INT);
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function BorrarProducto($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE productos set available = 0 where id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public  function ModificarProducto($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "UPDATE productos 
            set 
            nombre=:nombre,
            tipo=:tipo,
            precio=:precio
            WHERE id=:id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        return $consulta->execute();
    }

    //Trae todos los productos
    public static function TraerProductos()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select * from productos");
        $consulta->execute();
        $productos = $consulta->fetchAll(PDO::FETCH_CLASS, "Producto");
        return $productos;
    }

    //Trae un Producto por ID
    public static function TraerProducto($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select * from productos where id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        $ProductoResultado = $consulta->fetchObject('Producto');
        return $ProductoResultado;
    }

    public static function Listar($lista)
    {
        foreach ($lista as $obj) {          
            echo Producto::MostrarDatos($obj);
        }
    }

    public function toString(): String
    {
        return
            "<ul>" .
            "<li>id: " . $this->id . "</li>" .   
            "<li>nombre: " . $this->nombre . "</li>" .
            "<li>tipo: " . $this->tipo . "</li>" .
            "<li>precio: " . $this->precio . "</li>" .
            "<li>available: " . $this->available . "</li>" .
            "<ul>";
    }

    public static function MostrarDatos($Producto)
    {
        return
            "<ul>" .
            "<li>id: " . $Producto->getId() . "</li>" .
            "<li>nombre: " . $Producto->getNombre() . "</li>" .
            "<li>tipo: " . $Producto->getTipo() . "</li>" .
            "<li>precio: " . $Producto->getPrecio() . "</li>" .
            "<li>available: " . $Producto->getAvailable() . "</li>" .
            "</ul>";
    }





}
