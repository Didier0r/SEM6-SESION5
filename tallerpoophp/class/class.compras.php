<?php
require_once '../class/class.conexion.php';

class Compra extends Conexion {
    function __construct() {
        parent::__construct();
    }

    public function getFactura($numero) {
        $sql = "SELECT * FROM compras WHERE numero = '".$numero."'"; // Cambio aquí
        $resultado = $this->conexion->query($sql);
        return $resultado;
    }

    public function getFacturas() {
        $sql = 'SELECT * FROM compras ORDER BY numero DESC'; // Cambio aquí
        $resultado = $this->conexion->query($sql);
        return $resultado;
    }

    public function getFacturaSQL($sql) {
        $resultado = $this->conexion->query($sql);
        return $resultado;
    }

    public function addFactura($numero, $fecha, $idproveedor, $estado, $idusuario) {
        // Especificamos los nombres de las columnas explícitamente
        $sql = "INSERT INTO compras (numero, fecha, estado, idUsuario, idPersona) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('ssiii', $numero, $fecha, $estado, $idusuario, $idproveedor);
        $stmt->execute();
        $stmt->close();
    }

    public function addDetalleFactura($numero, $idarticulo, $unitario, $cantidad, $impuesto) {
        // Comprobar si el detalle de compra ya existe
        $sqlCheck = "SELECT COUNT(*) FROM detallecompras WHERE numero = ? AND idArticulo = ?";
        $stmtCheck = $this->conexion->prepare($sqlCheck);
        $stmtCheck->bind_param('si', $numero, $idarticulo);
        $stmtCheck->execute();
        $stmtCheck->bind_result($count);
        $stmtCheck->fetch();
        $stmtCheck->close();
    
        if ($count > 0) {
            throw new Exception("Este artículo ya ha sido agregado a la compra.");
        }
    
        // Si no existe, proceder con la inserción
        $sql = "INSERT INTO detallecompras (numero, idArticulo, preciocompra, cantidad, impuesto) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('siiii', $numero, $idarticulo, $unitario, $cantidad, $impuesto);
        $stmt->execute();
        $stmt->close();
    }
    
}
?>
