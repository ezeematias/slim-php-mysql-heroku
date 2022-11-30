<?php
include_once("db/AccesoDatos.php");

    class CSV
    {

        public static function GrabarEnCsv($item, $ruta)
        {             
            $retorno = false;
            //var_dump($usuario);
            if($item)
            {
                $separadoPorComa = implode(",", (array)$item);
                $file = fopen($ruta, "a+");
                if($file)
                {
                    fwrite($file, $separadoPorComa.PHP_EOL); 
                }                 
                fclose($file);   
                $retorno = true;
            }
            return $retorno;                  
        }

        public static function ExportarTabla($tabla, $clase, $ruta)
        {
            $listaMesas = AccesoDatos::obtenerTodos($tabla, $clase); 
    
            foreach($listaMesas as $item)
            {
                CSV::GrabarEnCsv($item, $ruta);
            }
        }

        public static function LeerCsv($archivo)
        {
            var_dump($archivo);
            $auxArchivo = fopen($archivo, "r");

            $array = [];

            if(isset($auxArchivo))
            {
                try
                {
                    while(!feof($auxArchivo))
                    {
                        $registro = fgets($auxArchivo);                        
                        if(!empty($registro))
                        {
                            //printf("entra a este if");
                            //$campo = explode(",", $registro); 
                            //var_dump($campo);   
                            //var_dump($registro);                  
                            array_push($array, $registro);                                                
                        }
                    }
                    //var_dump($array); 
                }
                catch(\Throwable $e)
                {
                    echo "No se pudo leer el archivo<br>";
                    printf($e);
                }
                finally
                {
                    fclose($auxArchivo);
                    return $array;
                }
            }
        }
    }







?>