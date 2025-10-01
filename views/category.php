<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">
        <?php
        include('../layout/header.php');
        ?>

<div class="container-fluid">
     <!-- Breadcrumb start -->
     <div class="row m-1">
         <div class="col-12 ">
         <button type="button" class="btn btn-primary m-1 float-end" id="btn_nuevo" data-bs-toggle="modal" data-original-title="test"
    data-bs-target="#exampleModal"><i class="ti ti-plus"></i> Nueva Categoría</button>
             <h4 class="main-title">Categorías</h4>
             <ul class="app-line-breadcrumbs mb-3">
                 <li class="">
                     <a href="#" class="f-s-14 f-w-500">
                         <span>
                             <i class="ph-duotone  ph-table f-s-16"></i> Operaciones
                         </span>
                     </a>
                 </li>
                 <li class="active">
                     <a href="#" class="f-s-14 f-w-500">Categorías</a>
                 </li>
             </ul>
         </div>
     </div>

     <!-- Breadcrumb end -->

     <!-- Modal -->

     <div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog"
         aria-labelledby="editCardModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-lg" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel">CATEGORIA</h5>
                     <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">

                     <form id="FormCategoria">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">MARCA<span class="text-danger">*</span></label>
                                <select id="marcas_list" name="marcas_list" class="form-control" required></select>
                            </div>

                         </div>
                        <div class="row g-3">
                             <div class="col-md-4">
                                 <label class="form-label">CODIGO<span class="text-danger">*</span></label>
                                 <input type="hidden" class="form-control" id="id_categoria" name="id_categoria"
                                     required data-validation-required-message="Debe elegir un usuario de la lista"
                                     value="0">
                                 <input type="text" class="form-control" id="codigo" name="codigo" required
                                     minlength="2" maxlength="50">
                             </div>
                             <div class="col-md-8">
                                <label class="form-label">DESCRIPCION<span class="text-danger">*</span></label>
                                 <input type="text" class="form-control" id="descripcion" name="descripcion" required
                                     minlength="4" maxlength="150">
                             </div>

                         </div>
                         <div class="row g-3">
                             <div class="col-md-8">
                             <label class="form-label">CATEGORÍA PADRE (SUPERIOR)<span class="text-danger">*</span></label>
                             <select id="categories_list" name="categories_list" class="form-control" required></select>
                             </div>
                             <div class="col-md-4">
                                 <label class="form-label">ESTADO<span class="text-danger">*</span></label>
                                 <select class="form-control" id="estado" name="estado">
                                     <option value="V">HABILITADO</option>
                                     <option value="D">DESHABILITADO</option>
                                 </select>
                             </div>

                         </div>

                 </div>
                 <div class="modal-footer">
                     <button type="submit" class="btn btn-primary waves-effect" id="btn_submit">Guardar</button>
                     <button type="button" class="btn btn-secondary waves-effect"
                         data-bs-dismiss="modal">Cancelar</button>
                 </div>
                 </form>
             </div>

         </div>
     </div>

 <!-- Modal -->

 <!-- Data Table start -->
 <div class="row">
     <!-- Default Datatable start -->
     <div class="col-12">
         <div class="card ">
             
             <div class="card-body p-0">
                 <div class="app-datatable-default overflow-auto">
                     <table id="basic-1" class="display app-data-table default-data-table">
                         <thead>
                             <tr>
                                <th>Código</th>    
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Opciones</th>
                             </tr>
                         </thead>
                         <tbody id="DetalleTabla">
                             <tr>
                                 <td>CARGANDO...</td>
                             </tr>
                         </tbody>
                     </table>
                 </div>
             </div>
         </div>
     </div>
     <!-- Default Datatable end -->
 </div>
 <!-- Data Table end -->
</div>

    <?php
    include('../layout/footer.php');
    ?>

<!-- data table jquery-->
<script src="../assets/vendor/datatable/jquery.dataTables.min.js"></script>
<script src="../assets/js/jquery.validate.min.js"></script>
<script src="../assets/js/jquery-key-restrictions.min.js"></script>

<!-- js-->
<script src="../assets/js/data_table.js"></script>
<script>  
$(function($){
    CargarMarcas();
    CargarCategorias();
    //validation
    $("#descripcion").lettersOnly();
});

 $('#btn_nuevo').on('click', function(e){
        e.preventDefault();
        $("#id_categoria").val("0");
   $("#descripcion").val("");
   
   $("#descripcion").focus();
        }); 

    function CargarMarcas() {
    $.ajax({
        url: '../controllers/brand_controller.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            localStorage.setItem('sml2020_marcas', JSON.stringify(data)); 
            ObtenerMarcas('');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (textStatus === 'timeout') {
                alert("Internet connection is down!");
            } else {
                alert("Error loading brands: " + errorThrown);
            }
        }
    });
}

function ObtenerMarcas(selected){
    $('#marcas_list').empty();
    var localData=JSON.parse(localStorage.getItem('sml2020_marcas'));

    var html='';
    $.each(localData,function(key,value){
        
      html+='<option value="'+value.id+'"> ('+value.codigo + ") " + value.nombre_marca+'</option>';
      if (value.descripcion==selected)
      html+='<option value="'+value.id+'" selected> ('+value.codigo + ") " + value.nombre_marca+'</option>';
      
    });
    $('#marcas_list').append(html);
}

function CargarCategorias() {
    $.ajax({
        url: '../controllers/category_controller.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            localStorage.setItem('sml2020_categorias', JSON.stringify(data)); 
            ObtenerCategorias1();
            ObtenerCategoriasList();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (textStatus === 'timeout') {
                alert("Internet connection is down!");
            } else {
                alert("Error loading categories: " + errorThrown);
            }
        }
    });
}

function ObtenerCategoriasList(selected){
    $('#categories_list').empty();
    var localData=JSON.parse(localStorage.getItem('sml2020_categorias'));
    var html='';
    html+='<option value="0" >-- NINGUNA</option>';
    $.each(localData,function(key,value){
        html+='<option value="'+value.id+'"> ('+value.codigo + ") " +value.descripcion+'</option>';
        if (value.descripcion==selected)
        html+='<option value="'+value.id+'" selected> ('+value.codigo + ") " +value.descripcion+'</option>';
    });
    $('#categories_list').append(html);
}

function ObtenerCategorias1() {
  $('#DetalleTabla').empty();

  if ($.fn.dataTable.isDataTable('#basic-1')) {
    $('#basic-1').DataTable().destroy();
  }

  const localData = JSON.parse(localStorage.getItem('sml2020_categorias'));
  
  // Ordenamos las categorías alfabéticamente por descripción
  localData.sort((a, b) => a.descripcion.localeCompare(b.descripcion));
  
  // Creamos un mapa para agrupar categorías hijas por padre
  const categoriasPorPadre = {};
  localData.forEach(categoria => {
    if (!categoriasPorPadre[categoria.padre]) {
      categoriasPorPadre[categoria.padre] = [];
    }
    categoriasPorPadre[categoria.padre].push(categoria);
  });

  let html = '';
  
  // Procesamos primero las categorías padre (padre = 0)
  if (categoriasPorPadre[0]) {
    categoriasPorPadre[0].forEach(categoria => {
      // Mostramos la categoría padre
      html += crearFilaCategoria(categoria, 0);
      
      // Mostramos las subcategorías si existen
      if (categoriasPorPadre[categoria.id]) {
        categoriasPorPadre[categoria.id].forEach(subcategoria => {
          html += crearFilaCategoria(subcategoria, 1);
          
          // Mostramos sub-subcategorías si existen (nivel 2)
          if (categoriasPorPadre[subcategoria.id]) {
            categoriasPorPadre[subcategoria.id].forEach(subsubcategoria => {
              html += crearFilaCategoria(subsubcategoria, 2);
            });
          }
        });
      }
    });
  }

  $('#DetalleTabla').html(html);
  $('#basic-1').DataTable({
    "order": [], // Desactivar ordenación inicial
    "paging": false // Opcional: desactivar paginación para ver todo el árbol
  });
}

function crearFilaCategoria(categoria, nivel) {
  const est = categoria.estado === 'V' ?
    '<span class="badge rounded-pill bg-success badge-notification">HABILITADO</span>' :
    '<span class="badge rounded-pill bg-danger badge-notification">DESHABILITADO</span>';

  const edi = '<button class="btn btn-primary btn-sm" onclick="Editar(\'' + categoria.id + '\')" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-pencil"></i></button>';

  // Prefijo jerárquico con guiones y flecha
  let prefijo = '';
  if (nivel > 0) {
    prefijo = '&nbsp;&nbsp;'.repeat(nivel) + '↳ ';
  }
  
  // Aplicamos sangría según el nivel de jerarquía
  const estiloCelda = nivel > 0 ? 'style="padding-left: ' + (nivel * 40) + 'px;"' : '';
  
  return `
    <tr role="row" class="${nivel > 0 ? 'child-row' : 'parent-row'}">
      <td class="sorting_1" ${estiloCelda}>${prefijo}${categoria.codigo}</td>
      <td ${estiloCelda}>${prefijo}${categoria.descripcion}</td>
      <td>${est}</td>
      <td>${edi}</td>
    </tr>
  `;
}

function Editar(elId){ 
  var localData=JSON.parse(localStorage.getItem('sml2020_categorias'));
       $.each(localData,function(key,value){
        if(value.id===elId){
        $('#id_categoria').val(value.id);
        $('#codigo').val(value.codigo);
        $('#descripcion').val(value.descripcion);
        $('#marcas_list').val(value.id_marca).prop('selected', true);
        $('#categories_list').val(value.padre).prop('selected', true);
        $('#estado').val(value.estado).prop('selected', true);  
        return}
      });
      $('#descripcion').focus();      
} 

$("#FormCategoria").submit(function(e) {
    e.preventDefault();
}).validate({  
    submitHandler: function(form) { 
        GuardarCategoria();
        return false;  //This doesn't prevent the form from submitting.
    }
});

function GuardarCategoria() {
  // Validación de datos (ejemplo)
  const el_id = $('#id_categoria').val();
  const codigo = $('#codigo').val();
  const descripcion = $('#descripcion').val();
  const id_marca = $('#marcas_list option:selected').val();
  const padre = $('#categories_list option:selected').val();
  const estado = $('#estado option:selected').val();
  const method = el_id === "0" || el_id === null || el_id === "" ? 'POST' : 'PUT';


  if (!descripcion) {
    alert('Por favor, ingrese una descripción.');
    return;
  }

  // Datos a enviar
  const data = {
    id: el_id,
    id_marca : id_marca,
    codigo: codigo,
    descripcion,
    padre,
    estado
  };

  $.ajax({
    url: '../controllers/category_controller.php',
    type: method,
    dataType: 'json',
    data: JSON.stringify(data),
    contentType: 'application/json',
    success: (response) => {
      if (response.success) {
        $('#exampleModal').modal('hide');
        Swal.fire(
            'Categoría Actualizada',
            '',
            'success'
        ); 
        CargarCategorias();
      } else {
        alert(response.error || 'Ocurrió un error al actualizar la categoría.');
      }
    },
    error: (jqXHR, textStatus, errorThrown) => {
      if (textStatus === 'timeout') {
        alert("La conexión a internet se ha interrumpido.");
      } else {
        alert('Ocurrió un error inesperado: ' + errorThrown);
        Swal.fire(
            'Error',
            errorThrown,
            'danger'
        ); 
      }
    }
  });
}
</script>