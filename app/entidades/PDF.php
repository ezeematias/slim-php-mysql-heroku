<?php
include_once("interfaces/IEntidad.php");
include_once("fpdf/fpdf.php");

class PDF
{
    public static function hacerPDF($idPedido)
    {
        
        $pdf = new Fpdf(); 
        $pdf->AddPage();

        //$pdf->Image('./assets/logo.png',10,8,33);
        $pdf->Ln(10);
        $pdf->Cell(40);

        $pdf->SetFont('Helvetica','',16);
        $pdf->Cell(60,4,'Unía',0,1,'C');
        $pdf->Cell(40);
        $pdf->SetFont('Helvetica','',8);
        $pdf->Cell(60,4,'CUIL: 20-35019857-2',0,1,'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(80,10, 'Producto', 1);
        $pdf->Cell(80,10, 'Cantidad', 1);
        $pdf->Ln();
        
        $lista = Reportes::Cuenta($idPedido);
        //var_dump($lista);
        
        foreach ($lista as $item) 
        {
            $pdf->Cell(80,10, $item->producto, 1);
            $pdf->Cell(80,10, $item->cantidad, 1);
            $pdf->Ln();
        }

        $pedido = AccesoDatos::retornarObjetoActivo($idPedido, 'pedido', 'Pedido');

        $pdf->Cell(80,10, 'Precio Final:', 1);
        $pdf->Cell(80,10, $pedido[0]->precio_final, 1);

        $pdf->Output(PDF::destinoPDF(),'f', $isUTF8=true);
        $pdf->Output(PDF::destinoPDF(),'i', $isUTF8=true);
        return;
    }

    public static function destinoPDF(){
        if(!file_exists("Tickets/")){
            mkdir("Tickets/",0777,true);
        }
        $date = new DateTime("now");
        $tiempoAhora = $date->format('Y-m-d-H_i_s');
        $nombreArchivo = "ticket_".$tiempoAhora.".pdf";
        $destino = "Tickets/".$nombreArchivo;
        return $destino;
    }
}


?>