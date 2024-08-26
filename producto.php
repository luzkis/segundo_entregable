<?php 
//Importamos el codigo que permite escribir en el log y conectar co BD
include_once 'leer_configuracion.php';

// Reciber una solicitud POS en el servidor 

$nombreProducto = $_POST['nombre'];
$precioProducto = $_POST['precio'];
$descripProducto = $_POST['descripcion'];

try{
  //Preparamos la consulta
  $sql = "SELECT * FROM productos WHERE nombre = ? AND precio = ? AND descripcion = ? ";
  $stmt = $conexion->prepare($sql);
  $stmt->bind_param("ss", $nombreProducto, $precioProducto, $descripProducto);
  $stmt->execute();
  //Obtenemos los resultados 
  $stmt->bind_result($id, $nombre, $precio, $descrpcion);

  // Inicializar una lista para almacenar los resultados
  $resultados = array();

  while ($stmt->fetch()) {
      $resultado = array(
          "id" => $id,
          "nombre" => $nombre,
          "precio" => $precio,
          "descripcion" => $descrpcion

      );
      $resultados[] = $resultado;
  }

  //Cerrar las conexiones con la base de datos
  $stmt->close();
  $conexion->close();

  //Convertir los resultados a JSON
  $json_resultados = json_ecode($resultados);
  echo $json_resultados;


}catch(Exception $e){
  $mensajeError = "Error al obtener los usuarios";
  LLogger::logError($mensajeError);
  header("Location: server_error_500.html");
}


?>