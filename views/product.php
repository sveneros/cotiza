
<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">
<!-- <link rel="stylesheet" type="text/css" href="../assets/css/quill.snow.css"> -->
<!-- editor css -->
<link rel="stylesheet" href="../assets/vendor/trumbowyg/trumbowyg.min.css">

        <?php
        include('../layout/header.php');
        ?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12 ">
    <button type="button" class="btn btn-primary m-1 float-end" id="btn_nuevo" data-bs-toggle="modal" data-original-title="test"
    data-bs-target="#exampleModal"><i
    class="ti ti-plus"></i> Nuevo Producto</button>
      <h4 class="main-title">Productos</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="#" class="f-s-14 f-w-500">
            <span>
              <i class="ph-duotone  ph-table f-s-16"></i> Operaciones
            </span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Productos</a>
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
          <h5 class="modal-title" id="exampleModalLabel">PRODUCTO</h5>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formProduct">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">CÓDIGO<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="codigo" name="codigo" maxlength="13"
                  size="13" required>
              </div>
              <div class="col-md-6">
                <input type="hidden" class="form-control" id="id_producto">
                <label class="form-label">NOMBRE DE PRODUCTO <span class="text-danger">*</span></label>

                <input type="text" class="form-control" id="nombre" name="nombre" maxlength="150" required>
              </div>
            </div>
            <div class="app-divider-v"></div>
            <div class="row g-3">
              <div class="col-md-12">
              <label class="form-label">DESCRIPCIÓN <span class="text-danger">*</span></label>
                <div class="app-editor" id="editor2">
                    <p></p>
                </div>
              </div>
            </div>
            <div class="app-divider-v"></div>
            <div class="row g-3">

              <div class="col-md-6">
                <label class="form-label">MARCA<span class="text-danger">*</span></label>

                <select id="marcas_list" name="marcas_list" class="form-control" required></select>
              </div>
              <div class="col-md-6 ">
                <label class="form-label">CATEGORÍA <span class="text-danger">*</span></label>
                <select id="categories_list" name="categories_list" class="form-control" required></select>
              </div>
            </div>
            <div class="row g-3">

              <div class="col-md-6">
                <label class="form-label">PRECIO<span class="text-danger">*</span></label>
                <input type="text" class="numberformat form-control" id="puntos" name="puntos" min="1" max="99999999"
                  maxlength="11" size="11" required>
              </div>

              <div class="col-md-6">
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
          <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Cancelar</button>
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
       
          <div class="table-responsive app-scroll app-datatable-default product-list-table">
            <table class="table-sm display align-middle" id="basic-1">

              <thead>
                <tr>
                  <th>Imagen</th>
                  <th>Código</th>
                  <th>Nombre</th>
                  <th>Descripcion</th>
                  <th>Marca</th>
                  <th>Categoría</th>
                  <th>Precio BS.</th>
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

<script>
// Get HTML content
//$('#editor').trumbowyg('html');
//$('#editor').trumbowyg('empty');

$(function($){
    CargarMarcas();
    CargarCategorias();
    ObtenerProductosIngreso();
    //validation
    $("#nombre").lettersOnly();
    
    //$("#codigo").numbersOnly();

    $('#puntos').on('blur', function() {
        let value = $(this).val();
        if (value) {
            let isValid = true;
            let dotCount = 0;

            for (let i = 0; i < value.length; i++) {
                const char = value[i];
                if (char === '.') {
                    dotCount++;
                } else if (char < '0' || char > '9') {
                    isValid = false;
                    break;
                }
            }

            if (dotCount > 1 || !isValid) {
                $(this).val('');
                return;
            }

            // Convertir a número y formatear a dos decimales
            value = parseFloat(value).toFixed(2);
            $(this).val(value);
        }
    });
});

  $('#btn_nuevo').on('click', function (e) {
    e.preventDefault();
    resetProductoForm(); // New function to simplify form reset
    CargarMarcas('');
    CargarCategorias('');
    $('#descripcion').focus();
  });

  function resetProductoForm() {
    $('#id_producto').val("0");
    $('#codigo').val("");
    $('#nombre').val("");
    $('#editor2').html("");
    $('#descripcion').val("");
    $('#puntos').val("0");
    $('#estado').prop('selectedIndex', 0); // Reset dropdown selection
  }
  
  function ObtenerProductosIngreso() {
    $.ajax({
      url: '../controllers/product_controller.php',
      type: 'GET',
      dataType: "json",
      data: {  },
      success: function (data) {
        
        localStorage.setItem('sml2020_productos', JSON.stringify(data)); // Store all categories in localStorage

        actualizarTabla();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        if (textStatus === 'timeout') {
          Swal.fire('Error', '¡La conexión a internet se ha interrumpido!', 'error');
        } else {
          Swal.fire('Error', 'Error al cargar productos de ingreso: ' + errorThrown, 'error');
        }
      }
    });
  }

  function actualizarTabla() {
    $('#DetalleTabla').empty();

    if ($.fn.dataTable.isDataTable('#basic-1')) {
        $('#basic-1').DataTable().destroy();
    }
    var localData=JSON.parse(localStorage.getItem('sml2020_productos'));
    cant_prod=localData.length;

    let html = '';
  $.each(localData, function(key, value) {
    const est = value.estado === 'V' ?
      '<span class="badge rounded-pill bg-success badge-notification">HABILITADO</span>' :
      '<span class="badge rounded-pill bg-danger badge-notification">DESHABILITADO</span>';

    const edi = '<button class="btn btn-primary" onclick="Editar(\'' + value.id + '\')" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-pencil"></i></button>';

    html += `
      <tr role="row" class="odd">
        <td><div class="d-flex align-items-center">
                <div class="h-35 w-35 d-flex-center b-r-10 overflow-hidden me-2">
                    <img src="../assets/images/ecommerce/08.jpg" alt=""
                            class="img-fluid">
                </div>
                
            </div>
        </td>
        <td class="sorting_1">${value.producto_codigo}</td>
        <td>${value.producto_nombre}</td>
        <td>${value.producto_descripcion}</td>
        <td>${value.marca}</td>
        <td>${value.categoria}</td>
        <td> ${value.puntos}</td>
        <td>${est}</td>
        <td>${edi}</td>
      </tr>
        `;
    });

    $('#DetalleTabla').html(html);
    $('#basic-1').DataTable();    
  }

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
      html+='<option value="'+value.id+'"> ('+value.codigo + ") " + value.descripcion+'</option>';
      if (value.descripcion==selected)
      html+='<option value="'+value.id+'" selected> ('+value.codigo + ") " + value.descripcion+'</option>';
    });
    $('#marcas_list').append(html);
}

  function CargarCategorias() {
    $.ajax({
        url: '../controllers/category_controller.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            localStorage.setItem('sml2020_categorias', JSON.stringify(data)); // Store all categories in localStorage
            ObtenerCategorias('', $('#marcas_list option:selected').val()); // Assuming this function processes the categories
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


  $('#marcas_list').on('change', function() {
        var selectedMarca = $(this).val(); 
        ObtenerCategorias('', $('#marcas_list option:selected').val());
  });

  function ObtenerCategorias(selected, id_marca) {
    $('#categories_list').empty();
    var localData=JSON.parse(localStorage.getItem('sml2020_categorias'));

    var html='';
    $.each(localData,function(key,value){
      if (value.id_marca==id_marca)
        html+='<option value="'+value.id+'"> ('+value.codigo + ") " +value.descripcion+'</option>';
      if (value.descripcion==selected)
        html+='<option value="'+value.id+'" selected> ('+value.codigo + ") " +value.descripcion+'</option>';
    });
    $('#categories_list').append(html);
}

  function GuardarProducto() {
  $('#btn_submit').hide();

  const elId = $('#id_producto').val();
  const codigo = $('#codigo').val();
  const nombre = $('#nombre').val();
  const descripcion = $('#editor2').trumbowyg('html');
  const idMarca = $('#marcas_list option:selected').val();
  const idCategoria = $('#categories_list option:selected').val();
  const puntos = $('#puntos').val();
  const estado = $('#estado option:selected').val();
  const method = elId === "0" || elId === null || elId === "" ? 'POST' : 'PUT';
   // Datos a enviar
   const data = {
    id: elId,
    nombre: nombre,
      descripcion: descripcion,
      id_marca: idMarca,
      id_categoria: idCategoria,
      codigo: codigo,
      puntos: puntos,
      estado: estado,
  };
  $.ajax({
    url: '../controllers/product_controller.php',
    type: method,
    dataType: "json",
    data: JSON.stringify(data),
    
    success: (response) => {
      if (response.success) {
        $('#exampleModal').modal('hide');
        ObtenerProductosIngreso();
        Swal.fire(
            'Producto Actualizado',
            '',
            'success'
        ); 
        
      } else {
        // Mostrar un mensaje de error más específico basado en la respuesta del servidor
        alert(response.error || 'Ocurrió un error al actualizar la categoría.');
      }
    },
    error(jqXHR, textStatus, errorThrown) {
      if (textStatus === 'timeout') {
        Swal.fire('Error', '¡La conexión a internet se ha interrumpido!', 'error');
      } else {
        Swal.fire('Error', 'Error al guardar producto: ' + errorThrown, 'error');
      }
    }
  }).always(() => {
    $('#btn_submit').show();
  });
}

function Editar(elId){ 
    var medida;
    var categoria;
    var presentacion;
    var localData=JSON.parse(localStorage.getItem('sml2020_productos'));
        $.each(localData,function(key,value){
            if(value.id===elId){
            $('#id_producto').val(value.id);
            marca=value.marca;
            categoria=value.categoria;
            $('#nombre').val(value.producto_nombre);
            $('#editor2').html(value.producto_descripcion);
            $('#codigo').val(value.producto_codigo); 
            $('#puntos').val(value.puntos); 
              
            $('#estado').val(value.estado).prop('selected', true);  
            return}
        }); 
            ObtenerMarcas(marca);
            ObtenerCategorias(categoria, $('#marcas_list option:selected').val()); 
            
           $('#codigo').focus();      
}


$("#formProduct").submit(function(e) {
    e.preventDefault();
    }).validate({  
    submitHandler: function(form) { 
      GuardarProducto(); //submit via ajax
        return false;  //This doesn't prevent the form from submitting.
    }});

</script>


