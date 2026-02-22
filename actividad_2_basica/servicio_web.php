<?php
// 1. Configuramos las cabeceras para que responda en JSON y permita peticiones
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

// 2. Detectamos qué tipo de petición está haciendo el cliente
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    // INTERCAMBIO 1: El cliente solo está "pidiendo" información (GET)
    $respuesta = [
        "estatus" => "exito",
        "mensaje" => "El servidor te envía esta información",
        "catalogo" => [
            ["id" => 1, "titulo" => "Rápidos y Furiosos", "tipo" => "Película"],
            ["id" => 2, "titulo" => "Ubuntu Server", "tipo" => "Sistema Operativo"]
        ]
    ];
    echo json_encode($respuesta);

} elseif ($metodo === 'POST') {
    // INTERCAMBIO 2: El cliente está "enviando" información al servidor (POST)
    // Leemos el JSON que nos manda el cliente
    $json_recibido = file_get_contents("php://input");
    $datos_cliente = json_decode($json_recibido, true);

    // Verificamos si nos enviaron la variable "usuario"
    if(isset($datos_cliente['usuario'])) {
        $respuesta = [
            "estatus" => "exito",
            "mensaje" => "¡Hola " . $datos_cliente['usuario'] . "! El servidor recibió tus datos correctamente.",
            "datos_que_enviaste" => $datos_cliente
        ];
    } else {
        // Si mandaron datos incompletos
        $respuesta = [
            "estatus" => "error",
            "mensaje" => "No se recibió el nombre de usuario"
        ];
    }
    
    echo json_encode($respuesta);
}
?>