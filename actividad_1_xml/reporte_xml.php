<?php
// 1. Datos de conexión a la base de datos en XAMPP
$host = "localhost";
$usuario = "root";
$password = ""; // Por defecto en XAMPP va vacío
$base_datos = "activiades_web";

// Crear la conexión
$conn = new mysqli($host, $usuario, $password, $base_datos);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// 2. Consulta SQL
// Filtramos por Coahuila para ver los precios locales y limitamos a 100 para que el XML no sea inmenso y cargue rápido
$sql = "SELECT estado, ciudad, anio, precio FROM tortilla_prices WHERE estado = 'Coahuila' LIMIT 100";
$resultado = $conn->query($sql);

// 3. Inicializar el documento XML
$xml = new DOMDocument("1.0", "UTF-8");
$xml->formatOutput = true; // Indentación para que se vea ordenado

// Crear el nodo principal (raíz)
$raiz = $xml->createElement("reporte_tortillas");
$xml->appendChild($raiz);

// 4. Recorrer los resultados y crear la estructura XML
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $registro = $xml->createElement("registro");
        
        // El año lo ponemos como un atributo del registro
        $registro->setAttribute("anio", $fila['anio']);

        // Creamos los nodos hijos y limpiamos caracteres especiales por seguridad
        $estado = $xml->createElement("estado", htmlspecialchars($fila['estado']));
        $registro->appendChild($estado);

        $ciudad = $xml->createElement("ciudad", htmlspecialchars($fila['ciudad']));
        $registro->appendChild($ciudad);

        $precio = $xml->createElement("precio_kg", htmlspecialchars($fila['precio']));
        $registro->appendChild($precio);

        // Agregamos este registro completo a la raíz
        $raiz->appendChild($registro);
    }
}

// 5. Configurar el header para que el navegador sepa que es un archivo XML puro
header("Content-Type: text/xml");
echo $xml->saveXML();

// Cerrar conexión
$conn->close();
?>