<?php

$host = "localhost";
$usuario = "root";
$password = ""; // Por defecto en XAMPP va vacío
$base_datos = "activiades_web";


$conn = new mysqli($host, $usuario, $password, $base_datos);


if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql = "SELECT estado, ciudad, anio, precio FROM tortilla_prices WHERE estado = 'Coahuila' LIMIT 100";
$resultado = $conn->query($sql);


$xml = new DOMDocument("1.0", "UTF-8");
$xml->formatOutput = true; 


$raiz = $xml->createElement("reporte_tortillas");
$xml->appendChild($raiz);


if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $registro = $xml->createElement("registro");
        
        
        $registro->setAttribute("anio", $fila['anio']);

       
        $estado = $xml->createElement("estado", htmlspecialchars($fila['estado']));
        $registro->appendChild($estado);

        $ciudad = $xml->createElement("ciudad", htmlspecialchars($fila['ciudad']));
        $registro->appendChild($ciudad);

        $precio = $xml->createElement("precio_kg", htmlspecialchars($fila['precio']));
        $registro->appendChild($precio);

        
        $raiz->appendChild($registro);
    }
}


header("Content-Type: text/xml");
echo $xml->saveXML();


$conn->close();
?>