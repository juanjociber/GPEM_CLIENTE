<?php 
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman_cliente/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman_cliente/datos/InformesData.php";

  $data = ['data' => [], 'res' => false, 'msg' => 'Error general.'];

  try {
    if (empty($_POST['id'])) {
      throw new Exception("La información está incompleta.");
    }

    $archivos = FnBuscarEquipoArchivos($conmy, $_POST['id']);

    if ($archivos) {
      $data['data'] = $archivos;
      $data['res'] = true;
      $data['msg'] = 'Ok.';
    } else {
      $data['msg'] = 'No se encontraron archivos.';
    }

  } catch(PDOException $ex) {
    $data['msg'] = $ex->getMessage();
  } catch (Exception $ex) {
    $data['msg'] = $ex->getMessage();
  } finally {
    $conmy = null;
  }
  header('Content-Type: application/json');
  echo json_encode($data);
?>
