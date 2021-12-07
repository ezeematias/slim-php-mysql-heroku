<?php
class Comanda
{
  public $id;
  public $nombreCliente;
  public $codigo;
  public $importe;
  public $idMesa;
  public $foto;

    //region getters and setters
    public function GetId() {
        return $this->id;
    }
    public function GetNombreCliente() {
        return $this->nombreCliente;
    }   
    public function GetCodigo() {
        return $this->codigo;
    }   

    public function GetImporte() {
        return $this->importe;
    }   

    public function GetIdMesa() {
        return $this->idMesa;
    }   

    public function GetFoto() {
        return $this->foto;
    }   

    public function SetId($value) {
        $this->id = $value;
    }       

    public function SetNombreCliente($value) {
        $this->nombreCliente = $value;
    }       

    public function SetCodigo($value) {
        $this->codigo = $value;
    }       

    /**
     * Setea importe
     */
    public function SetImporte_($value) {
        $this->importe = $value;
    }       

    public function SetIdMesa($value) {
        $this->idMesa = $value;
    }       

    public function SetFoto($value) {
        $this->foto = $value;
    }       

    //endregion

    public function InsertarComanda($nuevoCodigo){
      
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta(
            "INSERT into comandas (codigo,nombreCliente,idMesa,foto)values(:codigo,:nombreCliente,:idMesa,:foto)"
        );
        $consulta->bindValue(':codigo', $nuevoCodigo, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public function BorrarComanda()    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
            update comandas 
            set available=0
            WHERE id=:id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->rowCount();
    }

    /**
     * ModificarComanda
     */
    public function ModificarComanda_()    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        if ($this->foto == null) {
            $consulta = $objetoAccesoDato->RetornarConsulta("
                update comandas 
                set codigo=:codigo,nombreCliente=:nombreCliente,importe=:importe,idMesa=:idMesa
                WHERE id=:id");
        } else {
            $consulta = $objetoAccesoDato->RetornarConsulta("
                update comandas 
                set codigo=:codigo,nombreCliente=:nombreCliente,importe=:importe,idMesa=:idMesa,foto=:foto
                WHERE id=:id");
        }
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':importe', $this->importe, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        return $consulta->execute();
    }

    public function AgregarFoto($archivos, $codComanda)    {
        $destino = "./fotos/";
        if (!file_exists($destino)) {
            mkdir($destino, 0777, true);
        }
        $nombreAnterior = $archivos['foto']->getClientFilename();
        $extension = explode(".", $nombreAnterior);
        $extension = array_reverse($extension);
        $archivos['foto']->moveTo($destino . $codComanda . "." . $extension[0]);
        $this->foto = $codComanda . "." . $extension[0];
    }

    public static function TraerComanda($codigo)    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
            select *
            from comandas 
            where codigo=:codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
        $comanda = $consulta->fetchObject('Comanda');
        return $comanda;
    }

    public static function TraerComandas()    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
            select * 
            from comandas 
            where available=1");
        $consulta->execute();
        $comandas = $consulta->fetchAll(PDO::FETCH_CLASS, "Comanda");
        return $comandas;
    }

    //traer comanda mesas pedidos y consumidos
    public static function TraerComandaMesasPedidosYConsumidos()    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
            select comandas.id,comandas.codigo,comandas.nombreCliente,comandas.importe,comandas.idMesa,comandas.foto,mesas.estado
            from comandas
            inner join mesas on mesas.id=comandas.idMesa
            where comandas.available=1");
        $consulta->execute();
        $comandas = $consulta->fetchAll(PDO::FETCH_CLASS, "Comanda");
        return $comandas;
    }

    //traer comanda mesas pedidos y consumidos
    public static function TraerInformacionDeComandaRecienCargada($codigo)    {

        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
        select c.codigo as codigo , c.nombreCliente as cliente,m.id as mesa, c.foto,
            p.id as pedido, p.estado, p.estimacion, p.codigo as codigo_pedido
        from comandas as c 
        left join mesas as m on c.codigo = m.codigo 
        left join pedidos as p on c.id = p.idComanda
        where c.codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(\PDO::FETCH_BOTH);
    }

    //mostrar datos de un objeto
    public static function MostrarDatos($datos)    {
        echo "Sus datos son: " . "<br>";
       foreach ($datos as $dato) {
          
            // codigo cliente mesa foto pedido estado estimacion
            echo "codigo: " . $dato['codigo'] . " </br> ";
            echo "cliente: " . $dato['cliente'] . " </br> ";
            echo "mesa: " . $dato['mesa'] . " </br> ";
            echo "foto: " . $dato['foto'] . " </br> ";
            echo "pedido: " . $dato['pedido'] . " </br> ";
            echo "estado: " . $dato['estado'] . " </br> ";
            echo "codigo pedido: " . $dato['codigo_pedido'] . " </br> ";
            echo "</p>";     
        }
    }

    //traer informacion completa de comanda con pedido
    public static function TraerInformacionComandaConPedido($codigo)    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
        select 
        c.id, c.codigoas codigo_mesa , c.nombreCliente, c.importe,  m.id as mesa, c.foto,
        m.estado as estado_mesa, p.id as NumeroPedido, p.estado, p.estimacion
        from comandas as c 
        left join mesas as m on c.codigo = m.codigo 
        left join pedidos as p on c.id = p.idComanda
        where c.codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(\PDO::FETCH_BOTH);
    }

    public static function TraerTodasLasComandas() {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
            select * 
            from comandas 
            where available=1");
        $consulta->execute();
        $comandas = $consulta->fetchAll(PDO::FETCH_CLASS, "Comanda");
        return $comandas;
    }

    //calcular importe segun los pedidos
    public static function CalcularImporte($codigo){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
        select 
        sum(cms.cantidadConsumido * pdt.precio) as total
        from comandas as c
        left join mesas as m on c.codigo = m.codigo
        left join pedidos as p on c.id = p.idComanda
        left join consumidos as cms on p.id = cms.idpedido
        left join productos pdt on pdt.id = cms.idProducto
        where c.codigo = :codigo"
        );
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetch();
    }

    //traer codigo de la comanda por su id
    public static function TraerCodigoComandaPorId($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
        select *
        from comandas
        where id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Comanda');
    }




}
