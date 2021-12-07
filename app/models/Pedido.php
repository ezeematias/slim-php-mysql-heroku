<?php

require_once './db/AccesoDatos.php';
require_once "./models/ProductoConsumido.php";

class Pedido
{
    public $id;
    public $idComanda;
    public $sector;
    public $idEmpleado;
    public $descripcion;
    public $estado;
    public $estimacion;
    public $codigo;
    public $fechaIngresado;
    public $fechaEntregado;
    public $demoro;


    //region Getters and Setters
    public function GetIdComanda() {
        return $this->idComanda;
    }
    public function GetSector() {
        return $this->sector;
    }
    public function GetIdEmpleado() {
        return $this->idEmpleado;
    }
    public function GetDescripcion() {
        return $this->descripcion;
    }
    public function GetEstado() {
        return $this->estado;
    }
    public function GetFechaIngresado() {
        return $this->fechaIngresado;
    }
    public function GetEstimacion() {
        return $this->estimacion;
    }
    public function GetFechaEntregado() {
        return $this->fechaEntregado;
    }

    public function SetIdComanda($value) {
        $this->idComanda = $value;
    }
    public function SetSector($value) {
        $this->sector = $value;
    }
    public function SetIdEmpleado($value) {
        $this->idEmpleado = $value;
    }
    public function SetDescripcion($value) {
        $this->descripcion = $value;
    }
    public function SetEstado($value) {
        $this->estado = $value;
    }
    public function SetFechaIngresado($value) {
        $this->fechaIngresado = $value;
    }

    /** Para dar un margen de tiempo a todo, se estima siempre alrededor de 10minutos.
     */
    public function SetEstimacion($value) {
        $now = date("Y-m-d i");
        $minimum_time = strtotime($now);
        $maximum_time = strtotime("+$value minutes", strtotime($now));
        $rand = rand($minimum_time, $maximum_time);
        $minutes =  date("i", $rand);
        if($minutes == "00" || $minutes < "10"){
            $minutes = "10";
        }
        $this->estimacion = $minutes;
    }

    public function SetFechaEntregado($value) {
        $this->fechaEntregado = $value;
    }

    public function GetCodigo() {
        return $this->codigo;
    }

    public function SetCodigo($value) {
        $this->codigo = $value;
    }



    //endregion Getters and Setters

    public static function CargarPedidos($array,$comanda){

        foreach ($array as $pedido) { 
            $newPedido = new Pedido();
            $newPedido->SetIdComanda($comanda);
            $newPedido->SetSector($pedido['sector']);
            $newPedido->SetDescripcion($pedido['descripcion']);
            $newPedido->SetEstado($pedido['estado']);
            $datetime_now = date("Y-m-d H:i:s");
            $newPedido->SetFechaIngresado($datetime_now);
          
            $nuevoCodigo = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 5);
            $newPedido->SetCodigo($nuevoCodigo);
            $idPedido = $newPedido->InsertarPedido();
            $productoConsumido = new ProductoConsumido();
            $productoConsumido->SetIdPedido($idPedido);
            $productoConsumido->SetIdProducto($pedido['idProducto']);
            $productoConsumido->SetCantidadConsumido($pedido['cantidad']);
            $productoConsumido->CargarUno();
        }
        return true;
    }

    public function InsertarPedido(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT INTO pedidos (idComanda,sector,idEmpleado,descripcion,estado,fechaIngresado,estimacion,codigo)
        VALUES (:idComanda,:sector,:idEmpleado,:descripcion,:estado,:fechaIngresado,:estimacion,:codigo)");
        $consulta->bindValue(':idComanda', $this->GetIdComanda(), PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->GetSector(), PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado', $this->GetIdEmpleado(), PDO::PARAM_STR);
        $consulta->bindValue(':descripcion', $this->GetDescripcion(), PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->GetEstado(), PDO::PARAM_STR);
        $consulta->bindValue(':fechaIngresado', $this->GetFechaIngresado(), PDO::PARAM_STR);
        $consulta->bindValue(':estimacion', $this->GetEstimacion(), PDO::PARAM_STR);
        $consulta->bindValue(':codigo', $this->GetCodigo(), PDO::PARAM_STR);
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    //cerrar fecha de pedidos con id de comanda
    public static function CerrarPedidos($idComanda){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE pedidos SET estado = 'CERRADO', fechaEntregado = :fechaEntregado WHERE idComanda = :idComanda");
        $consulta->bindValue(':idComanda', $idComanda, PDO::PARAM_STR);
        $consulta->bindValue(':fechaEntregado', date("Y-m-d"), PDO::PARAM_STR);
        $consulta->execute();
        return true;
    }

    //cambio el estado de los pedidos de una comanda a COBRADO
    public static function CobrarPedidos($idComanda)    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
        "UPDATE pedidos SET estado = 'COBRADO', fechaEntregado = :fechaEntregado WHERE idComanda = :idComanda"
        );
        $consulta->bindValue(':idComanda', $idComanda, PDO::PARAM_STR);
        $consulta->bindValue(':fechaEntregado', date("Y-m-d"), PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }

    //traer pedido
    public static function TraerPedido($idComanda){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM pedidos WHERE idComanda = :idComanda");
        $consulta->bindValue(':idComanda', $idComanda, PDO::PARAM_STR);
        $consulta->execute();
        $pedido = $consulta->fetchObject("Pedido");
        return $pedido;
    }

    //traer pedidos pendientes
    public static function TraeLaComandaConPedidosPendientes(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM pedidos where estado = :pendiente  limit 1");
        $consulta->bindValue(':pendiente', 'pendiente', PDO::PARAM_STR);
        $consulta->execute();
        $pedido = $consulta->fetchObject('Pedido');
        return  $pedido; 
    }

    //traer pedidos en preparacion
    public static function TraePedidosEnPreparacion()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM pedidos where estado = :en_preparacion  limit 1");
        $consulta->bindValue(':en_preparacion', 'en preparacion', PDO::PARAM_STR);
        $consulta->execute();
        $pedido = $consulta->fetchObject('Pedido');
        return  $pedido;
    }

    //traer pedidos listo para servir
    public static function TraePedidosListosParaServir()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM pedidos where estado = :listo_para_servir  limit 1");
        $consulta->bindValue(':listo_para_servir', 'listo para servir', PDO::PARAM_STR);
        $consulta->execute();
        $pedido = $consulta->fetchObject('Pedido');
        return  $pedido;
    }

    //modificar pedido
    public function ModificarPedido(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE pedidos SET idEmpleado = :idEmpleado, estado = :estado, estimacion = :estimacion, demoro = :demoro WHERE id = :id");
        $consulta->bindValue(':idEmpleado', $this->idEmpleado, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':estimacion', $this->estimacion, PDO::PARAM_STR);
        $consulta->bindValue(':demoro', $this->demoro, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $consulta->execute();
    }

    //traer pedidos listo para servir
    public static function BuscarMisPedidos($codigo, $p_id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
        select c.codigo as codigo_mesa , c.nombreCliente as nombre_cliente,m.id as mesa, c.foto,
	    p.id as numero_pedido, p.estado, p.estimacion, p.codigo as codigo_pedido
        from comandas as c 
        left join mesas as m on c.codigo = m.codigo 
        left join pedidos as p on c.id = p.idComanda
        where c.codigo = :codigo and p.id = :p_id");

        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->bindValue(':p_id', $p_id, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(\PDO::FETCH_BOTH);
    }

    //traer pedido por id
    public static function TraerPedidoPorID($id){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $pedido = $consulta->fetchObject('Pedido');
        return $pedido;
    }

    //traer todos los pedidos
    public static function TraerTodosLosPedidos(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM pedidos");
        $consulta->execute();
        $pedidos = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");
        return $pedidos;
    }

    public static function Metricas()
    {
        $listaAnalytics = array();
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        //a- Lo que más se vendió
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select SUM(cantidadConsumido) as cantidad , productos.nombre from consumidos
            left join productos on consumidos.idProducto = productos.id
            group by idProducto
            limit 1"
        );
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->cantidad = $row['cantidad'];
                $rowObj->nombre = $row['nombre'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['a_'] = $rows;
        }

        //b- Lo que menos se vendió
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select SUM(cantidadConsumido) as cantidad , productos.nombre from consumidos
            left join productos on consumidos.idProducto = productos.id
            group by idProducto
            order by cantidad asc
            limit 1"
        );
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->cantidad = $row['cantidad'];
                $rowObj->sector = $row['nombre'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['b_'] = $rows;
        }

        //c- Los que no se entregaron en el tiempo estipulado
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select id,idEmpleado from pedidos where demoro = 'si' "
        );
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->id = $row['id'];
                $rowObj->idEmpleado = $row['idEmpleado'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['c_'] = $rows;
        }

        //d- Los cancelados.
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "select id as idPedido,idEmpleado from pedidos where estado = 'CANCELADO' "
        );
        $consulta->execute();
        $resultado = $consulta->fetchAll();
        if ($resultado) {
            $rows = array();
            foreach ($resultado as $row) {
                $rowObj = new stdclass();
                $rowObj->idPedido = $row['idPedido'];
                $rowObj->idEmpleado = $row['idEmpleado'];
                array_push($rows, $rowObj);
            }
            $listaAnalytics['d_'] = $rows;
        }


        return $listaAnalytics;
    }

    public static function Listar($lista)
    {
        foreach ($lista as $obj) {          
            echo Pedido::MostrarDatos($obj);
        }
    }

    public function toString(): String
    {
        return
            "<ul>" .
            "idComanda: ".$this->idComanda . "<br>" .
            "sector: " . $this->sector . "<br>" .
            "idEmpleado: " . $this->idEmpleado . "<br>" .
            "descripcion: " . $this->descripcion . "<br>" .
            "estado: " . $this->estado . "<br>" .
            "estimacion: " . $this->estimacion . "<br>" .
            "codigo: " . $this->codigo . "<br>" .
            "fechaIngresado: " . $this->fechaIngresado . "<br>" .
            "fechaEntregado: " . $this->fechaEntregado . "<br>";
            "demoro: " . $this->demoro . "</ul>";   
            "<ul>";
    }

    public static function MostrarDatos($Producto)
    {
        return
        "<br>" .    
        "idComanda: ".$Producto->idComanda . "<br>" .
        "sector: " . $Producto->sector . "<br>" .
        "idEmpleado: " . $Producto->idEmpleado . "<br>" .
        "descripcion: " . $Producto->descripcion . "<br>" .
        "estado: " . $Producto->estado . "<br>" .
        "estimacion: " . $Producto->estimacion . "<br>" .
        "codigo: " . $Producto->codigo . "<br>" .
        "fechaIngresado: " . $Producto->fechaIngresado . "<br>" .
        "fechaEntregado: " . $Producto->fechaEntregado . "<br>";
        "demoro: " . $Producto->demoro . "<br>";  
    }

    public static function Mostrar($array){
        foreach ($array as $key) {
        echo
        "<ul>" .
        "codigo mesa: ".$key['codigo_mesa'] . "<br>" .
        "cliente: " . $key['nombre_cliente'] . "<br>" .
        "mesa: " . $key['mesa'] . "<br>" .
        "foto: " . $key['foto'] . "<br>" .
        "pedido n°: " . $key['numero_pedido'] . "<br>" .
        "estado: " . $key['estado'] . "<br>" .
        "codigo pedido: " . $key['codigo_pedido'] . "<br>" .
        "codigo mesa: " . $key['codigo_mesa'] . "<br>" .
        "estimacion: " . $key['estimacion'] ." minutos ". "</ul>";
        }
    }
} //clase

