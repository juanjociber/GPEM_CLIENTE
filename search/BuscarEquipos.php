<?php
    session_start();
	require_once $_SERVER['DOCUMENT_ROOT'].'/gesman_cliente/connection/ConnGesmanDb.php';

	$Bandera = false;
	if(isset($_SESSION['CliId'])){
		$Bandera = true;
	}
	
	$data['data'] = array();
	$data['res'] = false;
	$data['pag'] = 0;
	$data['msg'] = 'Error del sistema.';

	if($Bandera == true && $_SERVER['REQUEST_METHOD'] === 'POST'){
		$pagina = 0;
		$query = "";

		if(!empty($_POST['pagina'])){
			$pagina = (int)$_POST['pagina'];
		}
		
		if(!empty($_POST['equipo'])){
			$query = " and codigo='".$_POST['equipo']."'";
		}else{
			if(!empty($_POST['grupo'])){
				$query .=" and idgrupo=".$_POST['grupo'];
			}

			if(!empty($_POST['estado'])){
				$query .=" and estado=".$_POST['estado'];
			}
		}
		
		try{
			$conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt=$conmy->prepare("select idactivo, codigo, activo, grupo, marca, modelo, estado from man_activos where idcliente=:CliId".$query." limit :Pagina, 2;");
			$stmt->bindParam(':CliId', $_SESSION['CliId'], PDO::PARAM_INT); //$stmt->bindParam(':IdCliente', $_POST['empresa'], PDO::PARAM_INT);
			$stmt->bindParam(':Pagina', $pagina, PDO::PARAM_INT);
			$stmt->execute();
			$n=$stmt->rowCount();		
			if($n>0){
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					$data['data'][] = array(
						'id' => $row['idactivo'],
                        'codigo' => $row['codigo'],
						'nombre' => $row['activo'],
                        'grupo' => $row['grupo'],
						'marca' => $row['marca'],
						'modelo' => $row['modelo'],
						'estado' => (int)$row['estado']
					);
				}
				$data['res'] = true;
				$data['msg'] = 'Ok.';
				$data['pag'] = $n;
			}else{
				$data['msg'] = 'No se encontró resultados.';
			}
			$stmt = null;
		}catch(PDOException $e){
			$stmt = null;
			$data['msg'] = $e->getMessage();
		}
	}else{
		$data['msg'] = 'Usuario no autorizado.';
	}

	echo json_encode($data);
?>