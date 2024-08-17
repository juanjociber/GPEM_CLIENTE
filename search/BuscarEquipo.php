<?php 
require_once $_SERVER['DOCUMENT_ROOT']."/gesman_cliente/connection/ConnGesmanDb.php";
require_once $_SERVER['DOCUMENT_ROOT']."/gesman_cliente/datos/InformesData.php";

$data = array('data' => array(), 'res' => false, 'msg' => 'Error general.');

try {
  if (empty($_POST['id'])) {
    throw new Exception("La Información está incompleta.");
  }

  $activo = FnBuscarEquipo($conmy, $_POST['id']); 
  if ($activo) {
    $data['data'] = $activo; 
    $data['res'] = true;
    $data['msg'] = 'Ok.';
  } else {
    $data['msg'] = 'No se encontró resultados.';
  }
} catch(PDOException $ex) {
    $data['msg'] = $ex->getMessage();
} catch (Exception $ex) {
    $data['msg'] = $ex->getMessage();
} finally {
    $conmy = null;
}
echo json_encode($data);
?>
