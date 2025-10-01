
<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">
<?php
include('../layout/header.php');
?>

<div class="container-fluid">
  <!-- Modal -->

  <div class="modal fade bd-example-modal-lg" id="ModalEmail1" tabindex="-1" role="dialog"
    aria-labelledby="editCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">ENVIAR EMAIL</h5>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formEmail1">
          <div class="row g-3">
              <div class="col-md-12"> 
                  <label for="nombre">NOMBRE</label>
                  <input type="text" id="nombre" name="nombre" class="form-control" readonly>
                  <input type="hidden" id="id_cotizacion" name="id_cotizacion">
              </div>
             
             
          </div>
            <div class="row g-3">
              <div class="col-md-12"> 
                  <label for="pass_password">EMAIL</label>
                  <input type="email" id="email1" name="email1" class="form-control" readonly>
              </div>
             
          </div>
          </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary waves-effect" id="btn_submit_email1">Guardar</button>
          <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Cancelar</button>
        </div>
        </form>
      </div>

    </div>
  
  </div>
<!-- Modal -->
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12 ">
    <!-- <button type="button" class="btn btn-secondary m-1 float-end" onclick="window.print()"><i
    class="ti ti-printer"></i> Imprimir</button> -->
      <h4 class="main-title">Cotizaciones</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="#" class="f-s-14 f-w-500">
            <span>
              <i class="ph-duotone  ph-table f-s-16"></i> Reportes
            </span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Cotizaciones</a>
        </li>
      </ul>
      
    </div>
  </div>

  <!-- Breadcrumb end -->

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
                  <th>#</th>  
                  <th>Cliente</th>
                  <th>Fecha y Hora</th>
                  <th>Total</th>
                  <th>Estado</th>
                  <th>Ver</th>
                  <th>Convertir a Venta</th>
                  <th>Email</th>
                  
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
<script>


$(function($){
    ObtenerCotizaciones();

    $("#formEmail1").submit(function(e) {
            e.preventDefault();
        }).validate({
            submitHandler: function(form) {
              EnviarEmail1(); // Enviar el formulario mediante AJAX
                return false; // Evitar el envío tradicional del formulario
            }
      });
});

  
function ObtenerCotizaciones() {
$.ajax({
    url: '../controllers/quotes_controller.php',
    type: 'GET',
    dataType: "json",
    data: {  },
    success: function (data) {
    
    localStorage.setItem('sml2025_quotes', JSON.stringify(data)); // Store all categories in localStorage

    actualizarTabla();
    },
    error: function (jqXHR, textStatus, errorThrown) {
    if (textStatus === 'timeout') {
        Swal.fire('Error', '¡La conexión a internet se ha interrumpido!', 'error');
    } else {
        Swal.fire('Error', 'Error al cargar clientes de ingreso: ' + errorThrown, 'error');
    }
    }
});
}

function actualizarTabla() {
$('#DetalleTabla').empty();

if ($.fn.dataTable.isDataTable('#basic-1')) {
    $('#basic-1').DataTable().destroy();
}
var localData=JSON.parse(localStorage.getItem('sml2025_quotes'));
cant_prod=localData.length;

let html = '';
$.each(localData, function(key, value) {
  if(value.estado === 'CLI')
est = '<span class="badge rounded-pill bg-info badge-notification">REGISTRADO POR CLIENTE</span>';
else if(value.estado === 'RECH')
est = '<span class="badge rounded-pill bg-danger badge-notification">RECHAZADO</span>';
else if(value.estado === 'APRO')
est = '<span class="badge rounded-pill bg-success badge-notification">APROBADO</span>';
else
est = '<span class="badge rounded-pill bg-warning badge-notification">EN REVISIÓN</span>';

const edi = '<button class="btn btn-primary" onclick="Ver(\'' + value.numero + '\')"><i class="fa fa-eye"></i></button> <button class="btn btn-warning" onclick="Editar(\'' + value.numero + '\')"><i class="fa fa-pencil"></i></button>';

const verVenta = '<button class="btn btn-info" onclick="VerParaVenta(\'' + value.numero + '\')"><i class="fa fa-arrow-right"></i></button>';

const email1 = '<button class="btn btn-primary" onclick="Email1(\'' + value.numero + '\')"data-bs-toggle="modal" data-bs-target="#ModalEmail1"><i class="fa fa-message"></i></button>';
html += `
    <tr role="row" class="odd">
        <td> ${value.numero }</td>
        <td class="sorting_1"><b>Nombre</b>: ${value.nombre } ${value.apellido1} ${value.apellido2}<br><b>Teléfono:</b> ${value.telefono}<br><b>Celular:</b> ${value.celular}<br><b>Dirección:</b> ${value.direccion}</td>
        <td>${value.fecha}</td>
        <td>${value.total}</td>
        <td>${est}</td>
        <td>${edi}</td>
        <td>${verVenta}</td>
        <td>${email1}</td> 
    </tr>
    `;
});

$('#DetalleTabla').html(html);
$('#basic-1').DataTable();    
}

    function Ver(id) {
      window.location.href = 'quote.php?id=' + encodeURIComponent(id);
    }

    function Editar(id) {
      window.location.href = 'quote_edit.php?id=' + encodeURIComponent(id);
    }

    function Email1(elId){
      const localData = JSON.parse(localStorage.getItem('sml2025_quotes'));
        $.each(localData, function(key, value) {
            if (value.numero === elId) {
                $('#id_cotizacion').val(value.numero);
                $('#nombre').val(value.nombre);
                $('#email1').val(value.email);
                return;
            }
        });
    }

    function EnviarEmail1() {
    $('#btn_submit_email1').hide(); // Ocultar el botón de enviar

    // Obtener los valores del formulario
    const email1 = $('#email1').val();
    const nombre_usuario = $('#nombre').val();
    const id_cotizacion = $('#id_cotizacion').val();

    // Validar que el campo de email no esté vacío
    if (!email1) {
        alert('Por favor, corrige los errores en el formulario.');
        $('#btn_submit_email1').show(); // Mostrar el botón de enviar nuevamente
        return;
    }

    // Crear el objeto de datos
    const data = {
        email: email1,
        nombre: nombre_usuario,
        asunto: 'COTIZACION CONFIRMADA',
        cuerpo: id_cotizacion
    };

    console.log("Datos a enviar:", data);

    // Enviar la solicitud GET
    $.ajax({
        url: '../controllers/mailer.php', // URL del servidor
        type: 'GET', // Método HTTP
        data: data, // Datos a enviar (se convierten en parámetros de la URL)
        success: (response) => {
            if (response.success) {
                $('#ModalEmail1').modal('hide'); // Cerrar el modal
                Swal.fire('Email enviado', '', 'success'); // Mostrar mensaje de éxito
            } else {
                alert(response.error || 'Ocurrió un error al enviar email.'); // Mostrar error
                $('#ModalEmail1').modal('show'); // Mostrar el modal nuevamente
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (textStatus === 'timeout') {
                Swal.fire('Error', '¡La conexión a internet se ha interrumpido!', 'error'); // Error de timeout
            } else {
                Swal.fire('Error', 'Error al enviar: ' + errorThrown, 'error'); // Otros errores
            }
        }
    }).always(() => {
        $('#btn_submit_email1').show(); // Mostrar el botón de enviar nuevamente
    });
}

function VerParaVenta(id) {
    window.location.href = 'quote_view.php?id=' + encodeURIComponent(id);
}
</script>


