
<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">
<?php
include('../layout/header.php');
?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12 ">
    <button type="button" class="btn btn-secondary m-1 float-end" onclick="window.print()"><i
    class="ti ti-printer"></i> Imprimir</button>
      <h4 class="main-title">Logs</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="#" class="f-s-14 f-w-500">
            <span>
              <i class="ph-duotone  ph-table f-s-16"></i> Reportes
            </span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Logs</a>
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
                  <th>Usuario</th>
                  <th>Mensaje</th>
                  <th>Ruta</th>
                  <th>Fecha y Hora</th>
                  
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
    ObtenerLogs();
   
   
});

  
  function ObtenerLogs() {
    $.ajax({
      url: '../controllers/logs_controller.php',
      type: 'GET',
      dataType: "json",
      data: {  },
      success: function (data) {
        
        localStorage.setItem('sml2025_logs', JSON.stringify(data)); // Store all categories in localStorage

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
    var localData=JSON.parse(localStorage.getItem('sml2025_logs'));
    cant_prod=localData.length;

    let html = '';
  $.each(localData, function(key, value) {
    const est = value.estado === 'VIG' ?
      '<span class="badge rounded-pill bg-success badge-notification">HABILITADO</span>' :
      '<span class="badge rounded-pill bg-danger badge-notification">DESHABILITADO</span>';

    html += `
      <tr role="row" class="odd">
        
        <td class="sorting_1">${value.usuario}</td>
        <td>${value.mensaje}</td>
        <td>${value.archivo}</td>
        <td>${value.fecha_hora}</td>
        
       
      </tr>
        `;
    });

    $('#DetalleTabla').html(html);
    $('#basic-1').DataTable();    
  }


</script>


