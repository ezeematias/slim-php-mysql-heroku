<?php

class ProductoConsumido
{
    public $id;
    public $idPedido;
    public $idProducto;
    public $cantidadConsumido;

    //region getters and setters
    public function GetId() {
        return $this->id;
    }
    public function GetIdPedido() {
        return $this->idPedido;
    }
    public function GetIdProducto() {
        return $this->idProducto;
    }
    public function GetCantidadConsumido() {
        return $this->cantidadConsumido;
    }

    public function SetId($value) {
        $this->id = $value;
    }

    public function SetIdPedido($value) {
        $this->idPedido = $value;
    }

    public function SetIdProducto($value) {
        $this->idProducto = $value;
    }

    public function SetCantidadConsumido($value) {
        $this->cantidadConsumido = $value;
    }

    //endregion

    public function CargarUno(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();  
        $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into consumidos (idPedido, idProducto, cantidadConsumido)values(:idPedido, :idProducto, :cantidadConsumido)");
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':cantidadConsumido', $this->cantidadConsumido, PDO::PARAM_INT);
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function TraerTodosLosProductosConsumidos()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM consumidos");
        $consulta->execute();			
        return $consulta->fetchAll(PDO::FETCH_CLASS, "ProductoConsumido");	
    }

    public static function TraerUnProductoConsumido($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM consumidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $productoConsumido = $consulta->fetchObject('ProductoConsumido');
        return $productoConsumido;
    }

    public static function TraerProductosConsumidosPorPedido($idPedido)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM consumidos WHERE idPedido = :idPedido");
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();
        $productosConsumidos = $consulta->fetchAll(PDO::FETCH_CLASS, "ProductoConsumido");
        return $productosConsumidos;
    }

    public static function TraerProductosConsumidosPorProducto($idProducto)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM consumidos WHERE idProducto = :idProducto");
        $consulta->bindValue(':idProducto', $idProducto, PDO::PARAM_INT);
        $consulta->execute();
        $productosConsumidos = $consulta->fetchAll(PDO::FETCH_CLASS, "ProductoConsumido");
        return $productosConsumidos;
    }



}
