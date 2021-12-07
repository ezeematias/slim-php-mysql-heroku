<?php

require_once './db/AccesoDatos.php';
require_once './models/Comanda.php';
require_once './models/Mesa.php';
require_once './models/Empleado.php';
require_once './models/Encuesta.php';
require_once './models/Logger.php';
require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/ProductoConsumido.php';
require_once './models/Usuario.php';
require_once './models/FileManager.php';

use Fpdf\Fpdf;

class FileManagerApi extends FileManager
{

    public function ComandaCSV($request, $response, $args)
    {
        $lista = Comanda::TraerComandas();
        if (count($lista) > 0) {
            $destination = ".\Reportes\\";
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $fecha = new DateTime(date("d-m-Y"));
            FileManager::guardarJson($lista, $destination . 'Comanda' . "_" . $fecha->format('d-m-Y') . '.csv');
            $ruta = $destination . 'Comanda' . "_" . $fecha->format('d-m-Y') . '.csv';
            $payload = json_encode(array("Archivo en: " => $ruta));
        } else {
            $payload = json_encode(array("mensaje" => "No hay comandas cargadas"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function ComandaPDF($request, $response, $args)
    {
        echo "<br>" . "Comanda" . "<br>";
        $lista = Comanda::TraerComandas();
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);
        //Titulo
        $pdf->Cell(80);
        $pdf->SetDrawColor(198, 67, 39);
        $pdf->Cell(30, 10, 'Comandas', 0, 0, 'C');

        $pdf->Ln(20);

       
        $pdf->Cell(20, 10, 'ID', 1);
        $pdf->Cell(20, 10, 'Codigo', 1);
        $pdf->Cell(20, 10, 'Cliente', 1);
        $pdf->Cell(20, 10, 'Importe', 1);
        $pdf->Cell(20, 10, 'Mesa', 1);
        $pdf->Cell(20, 10, 'Foto', 1);
        $pdf->Ln();

        foreach ($lista as $comanda) {
            $pdf->Cell(20, 10, $comanda->id, 1,);
            $pdf->Cell(20, 10, $comanda->codigo, 1);
            $pdf->Cell(20, 10, $comanda->nombreCliente, 1);
            $pdf->Cell(20, 10, $comanda->importe, 1);
            $pdf->Cell(20, 10, $comanda->idMesa, 1);
            $pdf->Cell(20, 10, $comanda->foto, 1);
            //No envio la ruta de la foto por que ocupa mucho lugar en el pdf
            $pdf->Ln();
        }

        $fecha = new DateTime(date("d-m-Y"));
        $destination = ".\Reportes\\";
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $pdf->Output('F', $destination . 'Comandas' . "_" . $fecha->format('d-m-Y') . '.pdf');
        $payload = json_encode(array("mensaje" => 'archivo generado en' . $destination . 'Comandas' . "_" . $fecha->format('d-m-Y') . '.pdf'));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function EmpleadoCSV($request, $response, $args)
    {
        $lista = Empleado::TraerEmpleados();
        if (count($lista) > 0) {
            $destination = ".\Reportes\\";
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $fecha = new DateTime(date("d-m-Y"));
            FileManager::guardarJson($lista, $destination . 'Empleado' . "_" . $fecha->format('d-m-Y') . '.csv');
            $ruta = $destination . 'Empleado' . "_" . $fecha->format('d-m-Y') . '.csv';
            $payload = json_encode(array("Archivo en: " => $ruta));
        } else {
            $payload = json_encode(array("mensaje" => "No hay Empleado cargadas"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function EmpleadoPDF($request, $response, $args)
    {
        echo "<br>" . "Empleado" . "<br>";
        $lista = Empleado::TraerEmpleados();
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);
        //Titulo
        $pdf->Cell(80);
        $pdf->SetDrawColor(198, 67, 39);
        $pdf->Cell(30, 10, 'Empleado', 0, 0, 'C');

        $pdf->Ln(20);


        $pdf->Cell(20, 10, 'ID', 1);
        $pdf->Cell(20, 10, 'Estado', 1);
        $pdf->Cell(20, 10, 'Sector', 1);
        $pdf->Cell(20, 10, 'Puesto', 1);
        $pdf->Cell(20, 10, 'FechaIngreso', 1);
        $pdf->Cell(20, 10, 'Nombre', 1);
        $pdf->Ln();

        foreach ($lista as $empleado) {
            $pdf->Cell(20, 10, $empleado->id, 1,);
            $pdf->Cell(20, 10, $empleado->estado, 1);
            $pdf->Cell(20, 10, $empleado->sector, 1);
            $pdf->Cell(20, 10, $empleado->puesto, 1);
            $pdf->Cell(20, 10, $empleado->fechaIngreso, 1);
            $pdf->Cell(20, 10, $empleado->nombre, 1);
           
            $pdf->Ln();
        }

        $fecha = new DateTime(date("d-m-Y"));
        $destination = ".\Reportes\\";
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $pdf->Output('F', $destination . 'Empleados' . "_" . $fecha->format('d-m-Y') . '.pdf');
        $payload = json_encode(array("mensaje" => 'archivo generado en' . $destination . 'Empleados' . "_" . $fecha->format('d-m-Y') . '.pdf'));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function PedidoCSV($request, $response, $args)
    {
        $lista = Pedido::TraerTodosLosPedidos();
        if (count($lista) > 0) {
            $destination = ".\Reportes\\";
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $fecha = new DateTime(date("d-m-Y"));
            FileManager::guardarJson($lista, $destination . 'Pedido' . "_" . $fecha->format('d-m-Y') . '.csv');
            $ruta = $destination . 'Pedido' . "_" . $fecha->format('d-m-Y') . '.csv';
            $payload = json_encode(array("Archivo en: " => $ruta));
        } else {
            $payload = json_encode(array("mensaje" => "No hay Empleado cargadas"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function PedidoPDF($request, $response, $args)
    {
        echo "<br>" . "Pedido" . "<br>";
        $lista = Pedido::TraerTodosLosPedidos();
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);
        //Titulo
        $pdf->Cell(80);
        $pdf->SetDrawColor(198, 67, 39);
        $pdf->Cell(30, 10, 'Pedido', 0, 0, 'C');
        $pdf->Ln(20);

        $pdf->Cell(15, 10, 'ID', 1);
        $pdf->Cell(15, 10, 'idComanda', 1);
        $pdf->Cell(15, 10, 'Sector', 1);
        $pdf->Cell(15, 10, 'IdEmpleado', 1);
        $pdf->Cell(15, 10, 'Descripcion', 1);
        $pdf->Cell(15, 10, 'Estado', 1);
        $pdf->Cell(15, 10, 'Estimacion', 1);
        $pdf->Cell(15, 10, 'FechaIngresado', 1);
        $pdf->Cell(15, 10, 'Codigo', 1);
        $pdf->Cell(15, 10, 'FechaEntregado', 1);

        $pdf->Ln();

        foreach ($lista as $obj) {
            $pdf->Cell(15, 10, $obj->id, 1,);
            $pdf->Cell(15, 10, $obj->idComanda, 1);
            $pdf->Cell(15, 10, $obj->sector, 1);
            $pdf->Cell(15, 10, $obj->idEmpleado, 1);
            $pdf->Cell(15, 10, $obj->descripcion, 1);
            $pdf->Cell(15, 10, $obj->estado, 1);
            $pdf->Cell(15, 10, $obj->estimacion, 1);
            $pdf->Cell(15, 10, $obj->fechaIngresado, 1);
            $pdf->Cell(15, 10, $obj->codigo, 1);
            $pdf->Cell(15, 10, $obj->fechaEntregado, 1);
            $pdf->Ln();
        }

        $fecha = new DateTime(date("d-m-Y"));
        $destination = ".\Reportes\\";
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $pdf->Output('F', $destination . 'Pedido' . "_" . $fecha->format('d-m-Y') . '.pdf');
        $payload = json_encode(array("mensaje" => 'archivo generado en' . $destination . 'Pedido' . "_" . $fecha->format('d-m-Y') . '.pdf'));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function MesaCSV($request, $response, $args)
    {
        $lista = Mesa::TraerMesas();
        if (count($lista) > 0) {
            $destination = ".\Reportes\\";
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $fecha = new DateTime(date("d-m-Y"));
            FileManager::guardarJson($lista, $destination . 'Mesa' . "_" . $fecha->format('d-m-Y') . '.csv');
            $ruta = $destination . 'Mesa' . "_" . $fecha->format('d-m-Y') . '.csv';
            $payload = json_encode(array("Archivo en: " => $ruta));
        } else {
            $payload = json_encode(array("mensaje" => "No hay Mesa cargadas"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function MesaPDF($request, $response, $args)
    {
        echo "<br>" . "Mesa" . "<br>";
        $lista = Mesa::TraerMesas();
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);
        //Titulo
        $pdf->Cell(80);
        $pdf->SetDrawColor(198, 67, 39);
        $pdf->Cell(30, 10, 'Mesa', 0, 0, 'C');
        $pdf->Ln(20);

        $pdf->Cell(15, 10, 'ID', 1);
        $pdf->Cell(15, 10, 'Codigo', 1);
        $pdf->Cell(15, 10, 'Estado', 1);
        $pdf->Ln();

        foreach ($lista as $obj) {
            $pdf->Cell(15, 10, $obj->id, 1,);
            $pdf->Cell(15, 10, $obj->codigo, 1);
            $pdf->Cell(15, 10, $obj->estado, 1);
            $pdf->Ln();
        }

        $fecha = new DateTime(date("d-m-Y"));
        $destination = ".\Reportes\\";
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $pdf->Output('F', $destination . 'Mesa' . "_" . $fecha->format('d-m-Y') . '.pdf');
        $payload = json_encode(array("mensaje" => 'archivo generado en' . $destination . 'Mesa' . "_" . $fecha->format('d-m-Y') . '.pdf'));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function ReadComandaCSV($request,$response,$args)
    {
        $fp = file_get_contents($_FILES['archivo']['tmp_name']);
        $f = fopen('temporal.csv', "w");
        fwrite($f, $fp);
        fclose($f);

        $fp = fopen('temporal.csv', "r");
        $key = fgetcsv($fp, 1024, ",");
        $json = array();
        $comandas = array();
        while ($line = fgetcsv($fp, 1024, ",")) {
            $json = array_combine($key, $line);
            $comanda = new Comanda();
            $comanda->id = $json['id'];
            $comanda->idEmpleado = $json['idEmpleado'];
            $comanda->sector = $json['sector'];
            $comanda->descripcion = $json['descripcion'];
            $comanda->estado = $json['estado'];
            $comanda->estimacion = $json['estimacion'];
            $comanda->fechaIngresado = $json['fechaIngresado'];
            $comanda->codigo = $json['codigo'];
            $comanda->fechaEntregado = $json['fechaEntregado'];
            $comandas[] = $comanda;
        }






        //payload
        $payload = json_encode(array("mensaje" => "Archivo cargado"));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    public function leerCSV($request, $response, $args)
    {
        //leer un archivo csv desde postman y enviar los datos a la base de hortalizas
        $archivo = $request->getUploadedFiles();

        $data = $archivo['archivo'];
        $json = $data->getStream()->getContents();
        var_dump(json_decode($json));

        foreach (json_decode($json) as $key => $value) {
            echo $key . ": " . $value . "<br>";
            /*             $hortaliza = new Hortaliza();

            $hortaliza->id = $value['id'];
            $hortaliza->nombre = $value['nombre'];
            $hortaliza->precio = $value['precio'];
            $hortaliza->cantidad = $value['cantidad'];
            $hortaliza->fecha = $value['fecha'];
            $hortaliza->crearHortaliza(); */
        }


        /*        foreach (json_decode($json) as $key => $value) {
            $hortaliza = new Hortaliza();
            $hortaliza->nombre = $value->nombre;
            $hortaliza->precio = $value->precio;
            $hortaliza->tipo = $value->tipo;
            $hortaliza->cantidad = $value->cantidad;
            $hortaliza->fecha = $value->fecha;
            $hortaliza->crearHortaliza();
        } */
        $payload = json_encode(array("mensaje" => "Archivo subido"));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }












} //clase