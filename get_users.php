<?php
require 'vendor/autoload.php'; // Asegúrate de que la ubicación del archivo autoload.php sea correcta
use Firebase\JWT\JWT;

// Incluir el archivo que permite conectar a la base de datos y manejar configuraciones
include_once 'leer_configuracion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario o solicitud POST
    if (isset($_POST['nombre_usuario']) && isset($_POST['email'])) {
        $enteredUsername = $_POST['nombre_usuario'];
        $enteredEmail = $_POST['email'];

        try {
            // Consulta SQL para obtener el hash de la contraseña y la sal del usuario
            $stmt = $conexion->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $enteredEmail);
            $stmt->execute();
            $stmt->bind_result($id, $name, $userName, $email);

            // Inicializar una lista para almacenar los resultados
            $resultados = array();

            while ($stmt->fetch()) {
                $resultado = array(
                    "id" => $id,
                    "name" => $name,
                    "userName" => $userName,
                    "email" => $email
                );
                $resultados[] = $resultado;
            }

            // Cierra la conexión y la declaración
            $stmt->close();
            $conexion->close();

            // Convertir la lista a formato JSON
            $json_resultados = json_encode($resultados);

            // Devolver el JSON
            echo $json_resultados;

        } catch (Exception $ex) {
            $error_message = "Ocurrió una excepción al intentar obtener información de la tabla: " . $ex->getMessage();
            Logger::logError($error_message, $log_file);
            header("Location: server_error_500.html"); // Redirigir a la página de error
        }

    } elseif (isset($_POST['nombre']) && isset($_POST['precio']) && isset($_POST['descripcion'])) {
        $nombreProducto = $_POST['nombre'];
        $precioProducto = $_POST['precio'];
        $descripProducto = $_POST['descripcion'];

        try {
            // Preparamos la consulta
            $sql = "SELECT * FROM productos WHERE nombre = ? AND precio = ? AND descripcion = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sss", $nombreProducto, $precioProducto, $descripProducto);
            $stmt->execute();
            // Obtenemos los resultados 
            $stmt->bind_result($id, $nombre, $precio, $descripcion);

            // Inicializar una lista para almacenar los resultados
            $resultados = array();

            while ($stmt->fetch()) {
                $resultado = array(
                    "id" => $id,
                    "nombre" => $nombre,
                    "precio" => $precio,
                    "descripcion" => $descripcion
                );
                $resultados[] = $resultado;
            }

            // Cerrar las conexiones con la base de datos
            $stmt->close();
            $conexion->close();

            // Convertir los resultados a JSON
            $json_resultados = json_encode($resultados);
            echo $json_resultados;

        } catch (Exception $e) {
            $mensajeError = "Error al obtener los productos: " . $e->getMessage();
            Logger::logError($mensajeError);
            header("Location: server_error_500.html");
        }
    }
}

// Función para almacenar un token JWT en una cookie
function almacenar_cookie($token) {
    // Encriptar y establecer la cookie
    $token_encoded = base64_encode($token);
    $tiempo_expiracion = time() + 3600; // 1 hora
    setcookie('jwt', $token_encoded, $tiempo_expiracion, '/');
}
?>
