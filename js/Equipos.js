var Equipo = '';
var Grupo = 0;
var Estado = 0;
var PaginasTotal = 0;
var PaginaActual = 0;

const vgLoader = document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuEquipos').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');
};

document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('modalEquipo');
    var caracteristicaTab = new bootstrap.Tab(document.getElementById('caracteristica-tab'));

    modal.addEventListener('hidden.bs.modal', function () {
        caracteristicaTab.show();
    });
});

async function FnBuscarEquipos(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        Equipo = document.getElementById('txtEquipo').value;
        Grupo = document.getElementById('cbGrupo').value;
        Estado = document.getElementById('cbEstado').value;
        PaginasTotal = 0
        PaginaActual = 0
        await FnBuscarEquipos2();
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function () { vgLoader.classList.add('loader-full-hidden'); }, 500);
    }
}

async function FnBuscarEquipos2(){
    try {
        const formData = new FormData();
        formData.append('equipo', Equipo);
        formData.append('grupo', Grupo);
        formData.append('estado', Estado);
        formData.append('pagina', PaginasTotal);

        const response = await fetch('/gesman_cliente/search/BuscarEquipos.php', {
            method:'POST',
            body: formData
        });/*.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));*/

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }

        document.getElementById('tblEquipos').innerHTML = '';

        let estado = '';
        datos.data.forEach(equipo => {
            switch (equipo.estado){
                case 1:
                    estado='<span class="badge bg-danger">Inactivo</span>';
                break;
                case 2:
                    estado='<span class="badge bg-success">Activo</span>';
                break;
                default:
                    estado='<span class="badge bg-light text-dark">Unknown</span>';
            }

            document.getElementById('tblEquipos').innerHTML +=`
            <div class="col-12">
                <div class="divselect border-bottom border-secondary mb-2 px-1" onclick="FnModalEquipo(${equipo.id}); return false;">
                    <div class="div d-flex justify-content-between">
                        <p class="m-0"><span class="fw-bold">${equipo.codigo}</span> <span class="text-secondary" style="font-size: 13px;">${equipo.grupo}</span></p><p class="m-0">${estado}</p>
                    </div>
                    <div class="div"><span>${equipo.nombre}</span> <span class="text-secondary">${equipo.marca} ${equipo.modelo}</span></div>
                </div>
            </div>`;
        });
        FnPaginacion(datos.pag);
    } catch (ex) {
        throw ex;
    }
}

function FnPaginacion(cantidad) {
    try {
        PaginaActual += 1;
        if (cantidad == 2) {
            PaginasTotal += 2;
            document.getElementById("btnSiguiente").classList.remove('d-none');
        } else {
            document.getElementById("btnSiguiente").classList.add('d-none');
        }

        if (PaginaActual > 1) {
            document.getElementById("btnPrimero").classList.remove('d-none');
        } else {
            document.getElementById("btnPrimero").classList.add('d-none');
        }
    } catch (ex) {
        throw ex;
    }
}

async function FnBuscarSiguiente() {
    vgLoader.classList.remove('loader-full-hidden');
    try {
        await FnBuscarEquipos2();
    } catch (ex) {
        document.getElementById("btnSiguiente").classList.add('d-none');
        showToast(ex.message, 'bg-warning');
    } finally {
        setTimeout(function () { vgLoader.classList.add('loader-full-hidden'); }, 500);
    }
}

async function FnBuscarPrimero() {
    vgLoader.classList.remove('loader-full-hidden');
    try {
        PaginasTotal = 0
        PaginaActual = 0
        await FnBuscarEquipos2()
    } catch (ex) {
        document.getElementById("btnPrimero").classList.add('d-none');
        showToast(ex.message, 'bg-warning');
    } finally {
        setTimeout(function () { vgLoader.classList.add('loader-full-hidden'); }, 500);
    }
}



var ANEXO = false;

const FnModalEquipo = async (id) => {
    ANEXO = false;
    document.getElementById('txtEquId').value=id;
    document.getElementById('imgEquipo').innerHTML = '';
    document.getElementById('anexo-tab-content').innerHTML = '';
    document.getElementById('fileContainer').innerHTML='';

    const modal = new bootstrap.Modal(document.getElementById('modalEquipo'));
    modal.show();


    const formData = new FormData();
    formData.append('id', id);
    
    try {
        const response = await fetch('/gesman_cliente/search/BuscarEquipo.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) { 
            throw new Error(response.status + ' ' + response.statusText); 
        }

        const datos = await response.json();
        if (!datos.res) { 
            throw new Error(datos.msg); 
        }

        const img = document.createElement('img');
        img.classList.add('img-fluid');
        img.src = `/gesman_cliente/search/BuscarEquipoFoto.php?img=${datos.data.Archivo}`;
        document.getElementById('imgEquipo').appendChild(img);

        // MOSTRANDO DATA RECIBIDA DE SERVIDOR EN EL MODAL
        document.getElementById('txtCodigo').textContent = datos.data.Codigo;
        document.getElementById('txtActivo').textContent = datos.data.Activo;
        document.getElementById('txtGrupo').textContent = datos.data.Grupo;
        document.getElementById('txtMarca').textContent = datos.data.Marca;
        document.getElementById('txtModelo').textContent = datos.data.Modelo;
        document.getElementById('txtSerie').textContent = datos.data.Serie;
        document.getElementById('txtAnio').textContent = datos.data.Anio;
        document.getElementById('txtFabricante').textContent = datos.data.Fabricante;
        document.getElementById('txtProcedencia').textContent = datos.data.Procedencia;
        document.getElementById('txtUbicacion').textContent = datos.data.Ubicacion;
        document.getElementById('txtCaracteristica').textContent = datos.data.Caracteristicas;
    
        // ACTUALIZAR EL TEXTO DEL MODAL SEGÚN EL ESTADO
        switch (datos.data.Estado) {
        case 1:
            document.getElementById('sEstado').textContent = 'Inactivo';
            document.getElementById('sEstado').classList.add('bg-danger');
            break;
        case 2:
            document.getElementById('sEstado').textContent = 'Activo';
            document.getElementById('sEstado').classList.add('bg-success');
            break;
        default:
            document.getElementById('sEstado').textContent = 'Unknown';
            document.getElementById('sEstado').classList.add('bg-secondary');
        }
  } catch (error) {
    console.error('Error:', error);
  }
};


const FnAnexo = async () => {
  if (!ANEXO) {
    try {
      const formData = new FormData();
      formData.append('id', document.getElementById('txtEquId').value);

    //   const response = await fetch('/gesman_cliente/search/BuscarEquipoArchivos.php', {
    const response = await fetch('/gesman_cliente/search/BuscarEquipoArchivo.php', {
        method: 'POST',
        body: formData
      });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));
      
      if (!response.ok) { 
        throw new Error(response.status + ' ' + response.statusText); 
      }
      const datos = await response.json();
      if (!datos.res) { 
          throw new Error(datos.msg); 
      }
      console.log(datos);
      const contenedorAnexos = document.getElementById('anexo-tab-content');
      contenedorAnexos.innerHTML = '';
      let htmlContent = '';
      datos.data.forEach(item => {
        htmlContent += `<button type="button" class="btn btn-outline-secondary m-2" datanombre=${item.nombre} datatipo=${item.tipo} onclick="FnVerArchivo(this); return false;">${item.nombre}</button>`;
      });
      contenedorAnexos.innerHTML = htmlContent;

    } catch (error) {
        console.error('Error:', error.message);
    } finally {
      console.log('OK');
      ANEXO = true;
    }
  } else {
    console.log('Los anexos ya se han cargado.');
  }
};

async function FnVerArchivo(archivo){
    console.log(archivo.getAttribute('datanombre'));
    console.log(archivo.getAttribute('datatipo'));
    try{
    var fileContainer=document.getElementById('fileContainer');
        if (fileContainer.childElementCount === 1) {
            var hijoUnico = fileContainer.firstElementChild;
            fileContainer.removeChild(hijoUnico);
        }
        
        switch (archivo.getAttribute('datatipo')) {
            case 'IMG':
                var nuevaImagen = document.createElement('img');
                nuevaImagen.src = 'http://192.168.40.8/mycloud/gesman/files/'+archivo.getAttribute('datanombre');
                nuevaImagen.alt = "Imagen";
                nuevaImagen.classList.add('img-fluid');
                fileContainer.appendChild(nuevaImagen);
                break;
            case 'PDF':
                var nuevoPdf = document.createElement("embed");
                nuevoPdf.src = 'http://192.168.40.8/mycloud/gesman/files/'+archivo.getAttribute('datanombre');
                nuevoPdf.type = "application/pdf";
                nuevoPdf.width = "100%";
                nuevoPdf.height = "600px";
                fileContainer.appendChild(nuevoPdf);
                break;        
            default:
                throw new Error('El tipo no esta disponible.');
        }
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    } finally{
        setTimeout(function () { vgLoader.classList.add('loader-full-hidden'); }, 1000);
    }
}