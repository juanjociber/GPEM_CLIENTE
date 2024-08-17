<?php
// Obtener el nombre de la imagen desde la URL (suponiendo que se pasa como parámetro GET)
$nombre_imagen = isset($_GET['img']) ? $_GET['img'] : '';

// URL del archivo buscar_imagen.php en tu servidor
//$url_buscar_imagen=$_SERVER['DOCUMENT_ROOT'].'/gpemsac/intranet/modulos/descargas/BuscarFoto.php?imagen='.urlencode($nombre_imagen);

// $url_buscar_imagen = 'http://192.168.40.8/gpemsac/intranet/modulos/descargas/BuscarFoto.php?imagen='.urlencode($nombre_imagen); // Incluye el nombre de la imagen como parámetro
$url_buscar_imagen = 'http://192.168.40.8/DATA/GPEMSYSTEM/ARCHIVOS/ACTIVOS/BuscarEquipoFoto.php?imagen='.urlencode($nombre_imagen);

// Hacer una solicitud GET a buscar_imagen.php
$respuesta = file_get_contents($url_buscar_imagen);
// Decodificar la respuesta JSON
$datos = json_decode($respuesta, true);

// Verificar si se obtuvo la imagen correctamente
if ($datos && isset($datos['imagen'], $datos['tipo'])) {
    // Configurar las cabeceras para mostrar la imagen
    header('Content-Type: ' . $datos['tipo']);
    
    // Mostrar la imagen
    echo base64_decode($datos['imagen']);
} else {
    // Si hubo un error al obtener la imagen
    echo 'Error al obtener la imagen';
}
?>