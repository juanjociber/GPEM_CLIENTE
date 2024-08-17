<?php 
    session_start();

    if(!isset($_SESSION['UserName']) || !isset($_SESSION['CliId'])){
        header("location:/gesman_cliente");
        exit();
    }

    require_once $_SERVER['DOCUMENT_ROOT'].'/gesman_cliente/connection/ConnGesmanDb.php';
    $cbGrupos='<option value="0">Seleccionar</option>';
    try{
        $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt=$conmy->prepare("select idgrupo, grupo from man_grupos where idcliente=:CliId and estado=2;");
        $stmt->bindParam(':CliId', $_SESSION['CliId'], PDO::PARAM_INT);
        $stmt->execute();
        while ($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            $cbGrupos.='<option value="'.$row['idgrupo'].'">'.$row['grupo'].'</option>';
        }
    }catch(PDOException $e){
        //Mensaje de Error
    } 
    $conmy = null;
    
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipos | GPEM SAC.</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/mycloud/library/select2-4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css"> 
    <link rel="stylesheet" href="/gesman_cliente/menu/sidebar.css">
    <style>
        .divselect {
            cursor: pointer;
            transition: all .25s ease-in-out;
        }
        .divselect:hover {
            background-color: #ccd1d1;
            transition: background-color .5s;
        }
    </style>

</head>
<body>
    
    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman_cliente/menu/sidebar.php';?>
    
    <div class="container section-top">
        <div class="row p-1 mb-3">
            <div class="col-12 border-bottom fw-bold fs-5">
                <p class="m-0 p-0"><?php echo $_SESSION['CliNombre'];?></p>
            </div>
        </div>
        <div class="row mb-1 border-bottom mb-2">
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0" style="font-size:12px;">Código</p>
                <input type="text" class="form-control" id="txtEquipo"/>
            </div>
            <div class="col-6 col-sm-4 mb-2">
                <p class="m-0" style="font-size:12px;">Grupo</p>
                <select class="form-select" id="cbGrupo">
                    <?php echo $cbGrupos;?>
                </select>
            </div>
            <div class="col-4 d-none d-sm-block mb-2">
                <p class="m-0" style="font-size:12px;">Estado</p>
                <select class="form-select" id="cbEstado">
                    <option value="0">Seleccionar</option>
                    <option value="2">ACTIVO</option>
                    <option value="1">INACTIVO</option>
                </select>
            </div>
            <div class="col-12 mb-2">
                <button type="button" class="btn btn-outline-primary form-control" onclick="FnBuscarEquipos(); return false;"><i class="fas fa-search"></i> Buscar</button>
            </div>  
        </div>
        
        <div class="row" id="tblEquipos">
            <div class="col-12">
                <p class="fst-italic">Haga clic en el botón Buscar para obtener resultados.</p>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-12 font-weight-bold d-flex justify-content-center mb-3">
                <button type="button" id="btnPrimero" class="btn btn-sm btn-outline-primary d-none mx-2" onclick="FnBuscarPrimero(); return false;">PRIMERO</button>
                <button type="button" id="btnSiguiente" class="btn btn-sm btn-outline-primary d-none mx-2" onclick="FnBuscarSiguiente(); return false;">SIGUIENTE</button>
            </div>
        </div>
    </div>

    
  <!-- MODAL -->
  <div class="modal fade" id="modalEquipo" tabindex="-1" aria-labelledby="equipoModalLabel" aria-hidden="true" data-idactivo="">
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fs-5 fw-bold" id="equipoModalLabel">DATOS TECNICOS </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          <input id="txtEquId" type="hidden" value="0"/>
        </div>

        <div class="modal-body">

          <ul class="nav nav-tabs mt-2 ml-2 mb-2" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="caracteristica-tab" data-bs-toggle="tab" data-bs-target="#caracteristica-tab-pane" type="button" role="tab" aria-controls="caracteristica-tab-pane" aria-selected="true">CARACTERÍSTICAS TÉCNICAS</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="anexo-tab" data-bs-toggle="tab" data-bs-target="#anexo-tab-pane" type="button" role="tab" aria-controls="anexo-tab-pane" aria-selected="false" onclick="FnAnexo()">ANEXOS</button>
            </li>
          </ul>
          <div class="tab-content" id="myTabContent">
            <!-- ESPACIO PARA CARACTERÍSICAS -->
            <div class="tab-pane fade show active" id="caracteristica-tab-pane" role="tabpanel" aria-labelledby="caracteristica-tab" tabindex="0">
              <!-- START MODAL-BODY -->             

                <div class="row" id="caracteristica-tab-content">
                  <div id="imgEquipo" class="col-12 col-sm-6"></div>
                  <div class="col-12 col-sm-6">
                    <div class= "row">          
                      <div class="col-6 col-sm-4">
                        <p class="mb-0">Código</>s
                        <p id="txtCodigo"></p>
                      </div>
                      <div class ="col-6 col-sm-4">
                        <p class="mb-0">Nombre</p>
                        <p id="txtActivo"></p>
                      </div>
                      <div class="col-6 col-sm-4">
                        <p class="mb-0">Grupo</p>
                        <p id="txtGrupo"></p>
                      </div>
                      <div class="col-6 col-sm-4">
                        <p class="mb-0">Marca</p>
                        <p id="txtMarca"></p>
                      </div>
                      <div class="col-6 col-sm-4">
                        <p class="mb-0">Modelo</p>
                        <p id="txtModelo"></p>
                      </div>
                      <div class ="col-6 col-sm-4">
                        <p class="mb-0">Serie</p>
                        <p id="txtSerie"></p>
                      </div>
                      <div class ="col-6 col-sm-4">
                        <p class="mb-0">Año</p>
                        <p id="txtAnio"></p>
                      </div>
                      <div class ="col-6 col-sm-4">
                        <p class="mb-0">Fabricante</p>
                        <p id="txtFabricante"></p>
                      </div>
                      <div class ="col-6 col-sm-4">
                        <p class="mb-0">Procedendia</p>
                        <p id="txtProcedencia"></p>
                      </div>
                      <div class ="col-6 col-sm-4">
                        <p class="mb-0">Ubicación</p>
                        <p id="txtUbicacion"></p>
                      </div>
                      <div class="col-6 col-sm-4">
                        <p class="m-0 text-secondary" style="font-size: 12px;">Estado</p>
                        <p class="m-0"><span id="sEstado" class='badge text-white m-0'>Unknown</span></p>
                      </div>
                      <div class ="col-12">
                        <p class="mb-0">Características</label>
                        <p id="txtCaracteristica"></p>
                      </div>
                    </div>
                  </div>
                </div>

            </div>
             
            <!-- ESPACIO PARA ANEXO -->
            <div class="tab-pane fade" id="anexo-tab-pane" role="tabpanel" aria-labelledby="anexo-tab" tabindex="0">
              <div class="row">
                <div class="col-12" id="anexo-tab-content"></div>
                <div class="col-12">
                  <div class="row text-center fw-bold">                        
                      <div class="col-12 mb-1">
                          <p id="pNombre" class="m-0"></p>
                      </div>
                      <div class="col-12 mb-1" id="fileContainer">
                      </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div><!-- END MODAL -->

    <div class="container-loader-full">
        <div class="loader-full"></div>
    </div>

    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/bootstrap-5-alerta-1.0/js/bootstrap-5-alerta-1.0.js"></script>
    <script src="/gesman_cliente/js/Equipos.js"></script>
    <script src="/gesman_cliente/menu/sidebar.js"></script>
</body>
</html>