<?php

error_reporting(E_ALL ^ E_WARNING); 
ob_start();

require_once '../class/class.compras.php';
$factura = new Compra();

require_once '../class/class.detallecompras.php';
$JSONdetalle = new DetalleCompra();

session_start();
$sesion = $_SESSION['usuario'];
$arrDetalles = $JSONdetalle->getDetalles($sesion);
$count = 0;
foreach ($arrDetalles as $detalle) { 
    $count++; 
}

if ($count == 0) {
    echo "<script>alert('No hay articulos agregados a la compra')</script>";
    echo "<script>window.close();</script>";
    exit;
}

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

// Variables por REQUEST
$numero_factura = $_REQUEST['numero'];
$idproveedor = $_REQUEST['idproveedor'];
$fecha = $_REQUEST['fecha'];
$estado = $_REQUEST['estado'];
$idusuario = $_SESSION['idUsuario'];

// Get the HTML
include(dirname('__FILE__') . '/documentos/compra_html.php');
$content = ob_get_clean();
// ob_end_clean();

try {
    // Init HTML2PDF
    $html2pdf = new HTML2PDF('P', 'A4', 'es', true, 'UTF-8', array(0, 0, 0, 0));
    // Display the full page
    $html2pdf->pdf->SetDisplayMode('fullpage');
    // Convert
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    // Send the PDF
    $html2pdf->Output('planillacompras' . $sesion . '.pdf');
} catch (Exception $e) {
    echo $e;
    exit;
}
