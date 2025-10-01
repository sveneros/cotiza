
<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">
<?php
include('../layout/header_clientes.php');
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
                  <input type="hidden" id="client_id" name="client_id" value="<?php echo ($_SESSION['sml2020_svenerossys_id_cliente_usuario_registrado']);?>">
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
          <a href="#" class="f-s-14 f-w-500">Mis Cotizaciones</a>
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
client_id = $('#client_id').val();
console.log("elidcliente",client_id);
$.ajax({
    url: '../controllers/quotes_controller.php',
    type: 'GET',
    dataType: "json",
    data: { 'client_id': client_id, },
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
est='';
if(value.estado === 'CLI')
est = '<span class="badge rounded-pill bg-info badge-notification">REGISTRADO POR CLIENTE</span>';
else if(value.estado === 'RECH')
est = '<span class="badge rounded-pill bg-danger badge-notification">RECHAZADO</span>';
else if(value.estado === 'APRO')
est = '<span class="badge rounded-pill bg-success badge-notification">APROBADO</span>';
else if(value.estado === 'REV')
est = '<span class="badge rounded-pill bg-warning badge-notification">EN REVISIÓN</span>';


 
const edi = '<button class="btn btn-primary" onclick="Ver(\'' + value.numero + '\')"><i class="fa fa-eye"></i></button>';


html += `
    <tr role="row" class="odd">
        <td> ${value.numero }</td>
        <td class="sorting_1"><b>Nombre</b>: ${value.nombre } ${value.apellido1} ${value.apellido2}<br><b>Teléfono:</b> ${value.telefono}<br><b>Celular:</b> ${value.celular}<br><b>Dirección:</b> ${value.direccion}</td>
        <td>${value.fecha}</td>
        <td>${value.total}</td>
        <td>${est}</td>
        <td>${edi}</td>
        
    </tr>
    `;
});

$('#DetalleTabla').html(html);
$('#basic-1').DataTable();    
}

    function Ver(id) {
      window.location.href = 'cotizacion.php?id=' + encodeURIComponent(id);
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

    
</script>


