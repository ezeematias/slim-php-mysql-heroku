<?php

use Fpdf\Fpdf;
require_once './models/Comanda.php';
require_once './models/Producto.php';

class ExtrasApi
{

    public function CargarProductoCSV($request, $response, $args) {
         
        $archivo = $request->getUploadedFiles();
        $data = $archivo['archivo'];
        $json = $data->getStream()->getContents();
        $arrayData = array();
        foreach (json_decode($json) as $key => $value) {
            $arrayData[$key] = $value;
        }

        $producto = new Producto();
        $producto->nombre = $arrayData['nombre'];
        $producto->precio = $arrayData['precio'];
        $producto->id_sector = $arrayData['id_sector'];
        $producto->tiempo_preparacion = $arrayData['tiempo_preparacion']; 

        $retorno = $producto->CrearProducto();

        if ($retorno == true) {
            $payload = json_encode(array("mensaje" => "Producto creado con exito"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(201);  

        } else {
            $payload = json_encode(array("mensaje" => "ERROR AL CREAR EL PRODUCTO"));
            $response->getBody()->write($payload);
            $newResponse = $response->withStatus(400);
        }

        return $newResponse
            ->withHeader('Content-Type', 'application/json');        
    }

	public function TraerComandasCSV($request, $response, $args) {
        
        $comandas = Comanda::MostrarComandas();
        $arrayComandas = array();

        foreach ($comandas as $comanda) {

            $pedido = Pedido::ObtenerPorCodigo($comanda->codigo_pedido);
            $mesa = Mesa::ObtenerPorId($pedido->id_mesa);
            $producto = Producto::ObtenerPorId($comanda->id_producto);

            $Id = $comanda->id_comanda;
            $CodigoPedido = $pedido->codigo_pedido;
            $CodigoMesa = $mesa->codigo_mesa;
            $Cliente = $pedido->cliente;
            $Producto = $producto->nombre;
            $Cantidad = $comanda->cantidad;
            $SectorElaboracion = $producto->id_sector;

            switch($SectorElaboracion){
                case 1:
                    $SectorElaboracion = "BARRA";
                    break;
                case 2:
                    $SectorElaboracion = "CHOPERIA";
                    break;
                case 3:
                    $SectorElaboracion = "COCINA";
                    break;
                case 4:
                    $SectorElaboracion = "CANDY BAR";
                    break;              
            } 

            $arrayComandas[] = array("Id" => $Id,
                                     "CodigoPedido" => $CodigoPedido,
                                     "CodigoMesa" => $CodigoMesa,
                                     "Cliente" => $Cliente,
                                     "Producto" => $Producto,
                                     "Cantidad" => $Cantidad,
                                     "SectorElaboracion" => $SectorElaboracion
                                    );
        }

        if (count($arrayComandas) > 0) {
            $destination = ".\Reportes\\";
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $fecha = new DateTime(date("d-m-Y"));
            FilesManager::guardarJson($arrayComandas, $destination . 'Comanda' . "_" . $fecha->format('d-m-Y') . '.csv');
            $ruta = $destination . 'Comanda' . "_" . $fecha->format('d-m-Y') . '.csv';
            $payload = json_encode(array("Archivo en: " => $ruta));
        } else {
            $payload = json_encode(array("mensaje" => "No hay comandas cargadas"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');

    }

	public function TraerComandasPDF($request, $response, $args) {
        
        $comandas = Comanda::MostrarComandas();

        $pdf = new Fpdf();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);
        //Titulo
        $pdf->Cell(80);
        $pdf->SetDrawColor(198, 67, 39);
        $pdf->Cell(30, 10, 'PEDIDOS', 0, 0, 'C');

        $pdf->Ln(20);

        $pdf->Cell(15, 10, 'ID', 1);
        $pdf->Cell(30, 10, 'Codigo Pedido', 1);
        $pdf->Cell(30, 10, 'Codigo Mesa', 1);
        $pdf->Cell(20, 10, 'Cliente', 1);
        $pdf->Cell(35, 10, 'Producto', 1);
        $pdf->Cell(25, 10, 'Cantidad', 1);
        $pdf->Cell(25, 10, 'Sector', 1);
        $pdf->Ln();

        foreach ($comandas as $comanda) {

            $pedido = Pedido::ObtenerPorCodigo($comanda->codigo_pedido);
            $mesa = Mesa::ObtenerPorId($pedido->id_mesa);
            $producto = Producto::ObtenerPorId($comanda->id_producto);

            $Id = $comanda->id_comanda;
            $CodigoPedido = $pedido->codigo_pedido;
            $CodigoMesa = $mesa->codigo_mesa;
            $Cliente = $pedido->cliente;
            $Producto = $producto->nombre;
            $Cantidad = $comanda->cantidad;
            $SectorElaboracion = $producto->id_sector;

            switch($SectorElaboracion){
                case 1:
                    $SectorElaboracion = "BARRA";
                    break;
                case 2:
                    $SectorElaboracion = "CHOPERIA";
                    break;
                case 3:
                    $SectorElaboracion = "COCINA";
                    break;
                case 4:
                    $SectorElaboracion = "CANDY BAR";
                    break;              
            } 

            $pdf->Cell(15, 10, $Id, 1,);
            $pdf->Cell(30, 10, $CodigoPedido, 1);
            $pdf->Cell(30, 10, $CodigoMesa, 1);
            $pdf->Cell(20, 10, $Cliente, 1);
            $pdf->Cell(35, 10, $Producto, 1);
            $pdf->Cell(25, 10, $Cantidad, 1);
            $pdf->Cell(25, 10, $SectorElaboracion, 1);
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

    public function LogoPDF($request, $response, $args)
    {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->Image("./assets/logo.jpg",20,100,100);
        $fecha = new DateTime(date("d-m-Y"));
        $destination = ".\Reportes\\";
        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $pdf->Output('F', $destination . 'Logo' . "_" . $fecha->format('d-m-Y') . '.pdf');
        $payload = json_encode(array("mensaje" => 'archivo generado en' . $destination . 'Logo' . "_" . $fecha->format('d-m-Y') . '.pdf'));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


}


?>