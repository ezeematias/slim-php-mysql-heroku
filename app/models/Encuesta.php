<?php
class Encuesta
{
    public $id;
    public $mesa;
    public $restaurante;
    public $mozo;
    public $cocinero;
    public $texto;
    public $fecha;

    //region getters and setters
    public function GetId() {
        return $this->id;
    }
    public function GetMesa() {
        return $this->mesa;
    }
    public function GetRestaurante() {
        return $this->restaurante;
    }
    public function GetMozo() {
        return $this->mozo;
    }
    public function GetCocinero() {
        return $this->cocinero;
    }
    public function GetTexto() {
        return $this->texto;
    }
    
    public function GetFecha() {
        return $this->fecha;
    }

    public function SetId($value) {
        $this->id = $value;
    }
    public function SetMesa($value) {
        $this->mesa = $value;
    }
    public function SetRestaurante($value) {
        $this->restaurante = $value;
    }
    public function SetMozo($value) {
        $this->mozo = $value;
    }
    public function SetCocinero($value) {
        $this->cocinero = $value;
    }
    public function SetTexto($value) {
        $this->texto = $value;
    }

    public function SetFecha($value) {
        $this->fecha = $value;
    }

    //endregion


    public function Cargar()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into encuestas (mesa,restaurante,mozo,cocinero,texto,fecha)values(:mesa,:restaurante,:mozo,:cocinero,:texto,:fecha)");
        $consulta->bindValue(':mesa', $this->mesa, PDO::PARAM_INT);
        $consulta->bindValue(':restaurante',$this->restaurante , PDO::PARAM_INT);
        $consulta->bindValue(':mozo', $this->mozo, PDO::PARAM_INT);
        $consulta->bindValue(':cocinero', $this->cocinero, PDO::PARAM_INT);
        $consulta->bindValue(':texto', $this->texto, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', date("Y-m-d"), PDO::PARAM_INT);
        $consulta->execute();
        
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function TraerEncuestaPorMesa(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM encuestas WHERE mesa = :mesa");
        $consulta->bindValue(':mesa', $_SESSION['mesa'], PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Encuesta");
    }

    //obtener mejores textos
    public static function TraerMejoresComentarios(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM encuestas WHERE texto LIKE '%muy buena%' OR texto LIKE '%buena%'");
        $consulta->execute();
        return $consulta->fetchAll(\PDO::FETCH_BOTH);
    } 

    public static function Listar($array){
        foreach($array as $encuesta){
            $encuesta->__toString();
        }
    }

    //encuesta tostring con br
    public function __toString()
    {
        return "id: " . $this->id . "<br>" .
               "mesa: " . $this->mesa . "<br>" .
               "restaurante: " . $this->restaurante . "<br>" .
               "mozo: " . $this->mozo . "<br>" .
               "cocinero: " . $this->cocinero . "<br>" .
               "texto: " . $this->texto . "<br>" .
               "fecha: " . $this->fecha . "<br>";
    }






}
