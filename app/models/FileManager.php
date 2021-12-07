<?php

class FileManager
{
    public static function guardarTxt(string $path, string $textoAEscribir)
    {
        $archivo = fopen($path, 'a+');

        fwrite($archivo, $textoAEscribir . PHP_EOL);

        return fclose($archivo);
    }


    public static function leerTxt(string $fileName)
    {
        $archivoTxt = array();

        $archivo = fopen($fileName, 'r');

        if ($archivo != null) {
            while (!feof($archivo)) {
                array_push($archivoTxt, str_replace(PHP_EOL, '', fgets($archivo)));
            }
        }

        fclose($archivo);

        return $archivoTxt;
    }

    public static function BringArray(string $fileName = '')
    {

        //Traer array
        $linea = "";
        $datos = "";
        $lista = array();
        if ($fileName !== "") {
            $archivo = fopen($fileName, 'r');
            while (!feof($archivo)) {
                $linea = fgets($archivo);
                if (!empty($linea)) {
                    $datos = explode(",", str_replace("\r\n", "", $linea));
                    array_push($lista, $datos);
                }
            }
            fclose($archivo);
        } else {
            echo 'El archivo no existe. Se ha creado uno nuevo en caso de ser posible.<br>';
        }

        return $lista;
    }


    public static function guardarJson($objeto, $path)
    {
        if ($objeto != null && !empty($path)) {
            if (file_exists($path)) {
                $array = (array) FileManager::leerJson($path);
            } else {
                $array = array();
            }
            array_push($array, $objeto);
            $archivo = fopen($path, 'w');
            fwrite($archivo, json_encode($array, JSON_PRETTY_PRINT));

            return fclose($archivo);
        } else {
            echo "Error. La ruta al archivo no puede estar vacia y el objeto a escribir no puede ser nulo.";
        }
    }


    public static function sobreEscribirJson($objeto, $path)
    {
        if ($objeto != null && !empty($path)) {

            $archivo = fopen($path, 'w');
            fwrite($archivo, json_encode($objeto, JSON_PRETTY_PRINT));

            return fclose($archivo);
        } else {
            echo "Error. La ruta al archivo no puede estar vacia y el objeto a escribir no puede ser nulo.";
        }
    }


    public static function leerJson(string $path)
    {

        $json = null;

        if (!empty($path) && file_exists($path)) {
            $archivo = fopen($path, 'r');
            $fileSize = filesize($path);


            if ($fileSize > 0) {
                $datos = fread($archivo, $fileSize);
                $json = json_decode($datos);
            } else {
                $readFile = '{}';
                $json = json_decode($readFile);
            }

            fclose($archivo);
        }

        return $json;
    }


    public static function serializar($objeto, string $path)
    {
        if ($objeto != null  && !empty($path)) {
            if (file_exists($path)) {
                $array = (array) FileManager::deserializar($path);
            } else {
                $array = array();
            }


            array_push($array, $objeto);

            $archivo = fopen($path, 'w');
            fwrite($archivo, serialize($array));

            fclose($archivo);
        } else {
            echo "Error. La ruta esta vacia o el objeto es nulo.";
        }
    }


    public static function deserializar(string $path)
    {


        if (!empty($path) && file_exists($path)) {
            $archivo = fopen($path, 'r');
            $fileSize = filesize($path);


            if ($fileSize > 0) {
                $datos = fread($archivo, $fileSize);
                $array = unserialize($datos);
            }

            fclose($archivo);
        } else {
            echo "Error. La ruta esta vacia o el archivo no existe.";
        }
        return $array;
    }
}
