<?php

class FilesManager
{
    public static function UploadFotoPedido($file, $pedido, $mesa)
    {
        try {

            $nombreArchivo = $pedido->codigo_pedido . "_" . $mesa->codigo_mesa . "_" . $pedido->cliente;

            $destino = "./fotos/";
            if (!file_exists($destino)) {
                mkdir($destino, 0777, true);
            }

            $nombreAnterior = $file['archivo']->getClientFilename();
            $extension = explode(".", $nombreAnterior);
            $extension = array_reverse($extension);

            $file['archivo']->moveTo($destino . $nombreArchivo . "." . $extension[0]);

            $foto = $nombreArchivo . "." . $extension[0];

            Pedido::ActualizarFoto($pedido->id_pedido, $foto);

            return true;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public static function guardarJson($objeto, $path)
    {
        if ($objeto != null && !empty($path)) {
            if (file_exists($path)) {
                $array = (array) FilesManager::leerJson($path);
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
}
?>