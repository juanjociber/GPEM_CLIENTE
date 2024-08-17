<?php
  // CONSULTAR ARCHIVOS
  function FnBuscarEquipoArchivos($conmy, $id) {
    try {
      $stmt = $conmy->prepare("SELECT id, refid, nombre, titulo, descripcion, tipo FROM tblarchivos WHERE tabla='EQUI' AND refid=:Id");
      $stmt->execute(array(':Id' => $id));
      $archivos = $stmt->fetchAll(PDO::FETCH_ASSOC);        
      return $archivos;
    } catch (PDOException $ex) {
        throw new Exception($ex->getMessage());
    }
  }

  function FnBuscarActivos($conmy,$cliid) {
    try {
        $stmt = $conmy->prepare("SELECT idactivo, idcliente, idgrupo, codigo, activo, grupo, marca, modelo, serie, anio, fabricante, procedencia, caracteristicas, ubicacion, estado FROM man_activos WHERE idcliente = :IdCliente");
        $stmt->execute(array(':IdCliente' => $cliid));
        $activos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $activos;
    } catch (PDOException $ex) {
        throw new Exception($ex->getMessage());
    }
  }

  function FnBuscarEquipo($conmy, $id) {
    try {
      $stmt = $conmy->prepare("SELECT idactivo, idcliente, idgrupo, codigo, activo, grupo, marca, modelo, serie, anio, fabricante, procedencia, caracteristicas, ubicacion, archivo, estado FROM man_activos WHERE idactivo = :Id");
      $stmt->execute(array(':Id' => $id));
      $row = $stmt->fetch(PDO::FETCH_ASSOC); 
      if ($row) {
          $activo = new stdClass();
          $activo->IdActivo = $row['idactivo'];
          $activo->IdCliente = $row['idcliente'];
          $activo->IdGrupo = $row['idgrupo'];
          $activo->Codigo = $row['codigo'];
          $activo->Activo = $row['activo'];
          $activo->Grupo = $row['grupo'];
          $activo->Marca = $row['marca'];
          $activo->Modelo = $row['modelo'];
          $activo->Serie = $row['serie'];
          $activo->Anio = $row['anio'];
          $activo->Fabricante = $row['fabricante'];
          $activo->Procedencia = $row['procedencia'];
          $activo->Caracteristicas = $row['caracteristicas']; 
          $activo->Ubicacion = $row['ubicacion'];
          $activo->Archivo=$row['archivo'];
          $activo->Estado = $row['estado'];
          return $activo;
      } else {
        throw new Exception('Activo no disponible.');
      }
    } catch (PDOException $ex) {
        throw new Exception($ex->getMessage());
    }
  }

?>
