<?php
// Obtener el nombre del archivo (imagen o PDF) desde la URL
$nombre_archivo = isset($_GET['archivo']) ? $_GET['archivo'] : '';

// URL del archivo externo
$url_buscar_archivo = 'http://192.168.40.8/gpemsac/intranet/modulos/descargas/DescargarEquipoArchivo.php?archivo='.urlencode($nombre_archivo);

// Obtener el contenido desde la URL
$respuesta = @file_get_contents($url_buscar_archivo);

// Verificar si hubo un error al obtener el archivo
if ($respuesta === FALSE) {
    echo json_encode(array('error'=>'Error al obtener el archivo.'));
}

// Intentar decodificar el JSON
$datos = @json_decode($respuesta, true);

// Verificar si la decodificación fue exitosa
if ($datos === NULL) {
    echo json_encode(array('error'=>'Error al decodificar JSON.'));
}

// Verificar si se obtuvieron los datos del archivo correctamente
if (isset($datos['archivo']) && isset($datos['tipo'])) {
    // Configurar las cabeceras según el tipo de archivo (imagen o PDF)
    header('Content-Type: '.$datos['tipo']);
    
    // Mostrar el archivo (decodificado de base64)
    echo base64_decode($datos['archivo']);
} else {
    // Si los datos están incompletos o el JSON está mal formado
    echo json_encode(array('error'=>'Error al obtener los datos del archivo.'));
}
?>