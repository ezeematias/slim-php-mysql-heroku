<?php

require_once './utils/enums.php';
require_once './models/Comentario.php';

class Encuesta
{

    public $id_encuesta;
    public $codigo_pedido;
    public $codigo_mesa;
    public $cliente;
    public $rating_mesa;
    public $rating_restaurante;
    public $rating_mozo;
    public $rating_cocinero;
    public $opinion;

    public static function Alta($encuesta)
    {
        try {
            $hora_login = date("Y-m-d H:i:s");
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO  encuesta (codigo_pedido, codigo_mesa, cliente, rating_mesa, rating_restaurante, rating_mozo, rating_cocinero, opinion, fecha)  VALUES ( :codigo_pedido, :codigo_mesa, :cliente, :rating_mesa, :rating_restaurante, :rating_mozo, :rating_cocinero, :opinion, :fecha)");
            $consulta->bindValue(':fecha', $hora_login, PDO::PARAM_STR);
            $consulta->bindValue(':codigo_pedido', $encuesta->codigo_pedido, PDO::PARAM_STR);
            $consulta->bindValue(':codigo_mesa', $encuesta->codigo_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':cliente', $encuesta->cliente, PDO::PARAM_STR);
            $consulta->bindValue(':rating_mesa', $encuesta->rating_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':rating_restaurante', $encuesta->rating_restaurante, PDO::PARAM_STR);
            $consulta->bindValue(':rating_mozo', $encuesta->rating_mozo, PDO::PARAM_STR);
            $consulta->bindValue(':rating_cocinero', $encuesta->rating_cocinero, PDO::PARAM_STR);
            $consulta->bindValue(':opinion', $encuesta->opinion, PDO::PARAM_STR);

            return $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public static function MostrarEncuestas()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta('SELECT * FROM encuesta');
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Encuesta");
    }

    public static function TraerMejoresComentarios()
    {
        try {
            $objetoAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objetoAccesoDato->prepararConsulta("SELECT cliente, opinion FROM encuesta WHERE opinion LIKE '%muy buena%' OR opinion LIKE '%buena%'");
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Comentario");
    }

    public static function TraerMejoresComentariosFechas($desde, $hasta)
    {
        try {
            $objetoAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objetoAccesoDato->prepararConsulta("SELECT cliente, opinion FROM encuesta WHERE fecha between :desde and :hasta AND opinion LIKE '%muy buena%' OR opinion LIKE '%buena%'");
            $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
            $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Comentario");
    }

    public static function TraerPeoresComentariosFechas($desde, $hasta)
    {
        try {
            $objetoAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objetoAccesoDato->prepararConsulta("SELECT cliente, opinion FROM encuesta WHERE fecha between :desde and :hasta AND opinion LIKE '%muy mala%' OR opinion LIKE '%mala%'");
            $consulta->bindValue(':desde', $desde, PDO::PARAM_STR);
            $consulta->bindValue(':hasta', $hasta, PDO::PARAM_STR);
            $consulta->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $consulta->fetchAll(PDO::FETCH_CLASS, "Comentario");
    }
}


?>