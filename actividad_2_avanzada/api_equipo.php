<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$conn = new mysqli("localhost", "root", "", "api_avanzada");
if ($conn->connect_error) {
    http_response_code(500); 
    die(json_encode(["error" => "Error de conexión a la base de datos"]));
}

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        $resultado = $conn->query("SELECT * FROM plantilla_futbol");
        $jugadores = [];
        
        while ($fila = $resultado->fetch_assoc()) {
            $jugadores[] = $fila;
        }
        
        http_response_code(200); 
        echo json_encode(["estatus" => "exito", "total" => count($jugadores), "datos" => $jugadores]);
        break;

    case 'POST':
        
        $datos = json_decode(file_get_contents("php://input"), true);
        
        if (!empty($datos['nombre']) && !empty($datos['posicion']) && !empty($datos['dorsal'])) {
            $stmt = $conn->prepare("INSERT INTO plantilla_futbol (nombre, posicion, dorsal) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $datos['nombre'], $datos['posicion'], $datos['dorsal']);
            
            if ($stmt->execute()) {
                http_response_code(201); 
                echo json_encode(["estatus" => "exito", "mensaje" => "Jugador registrado correctamente"]);
            } else {
                http_response_code(503); 
                echo json_encode(["error" => "No se pudo registrar al jugador"]);
            }
            $stmt->close();
        } else {
            http_response_code(400); 
            echo json_encode(["error" => "Datos incompletos. Se requiere nombre, posicion y dorsal."]);
        }
        break;

    case 'DELETE':
        
        $datos = json_decode(file_get_contents("php://input"), true);
        
        if (!empty($datos['id'])) {
            $stmt = $conn->prepare("DELETE FROM plantilla_futbol WHERE id = ?");
            $stmt->bind_param("i", $datos['id']);
            $stmt->execute();
            
            http_response_code(200);
            echo json_encode(["estatus" => "exito", "mensaje" => "Jugador eliminado de la plantilla"]);
            $stmt->close();
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Se requiere el ID del jugador para eliminarlo"]);
        }
        break;

    default:
        
        http_response_code(405); 
        echo json_encode(["error" => "Método HTTP no soportado"]);
        break;
}

$conn->close();
?>