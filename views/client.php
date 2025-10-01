<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">
<?php include('../layout/header.php'); ?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12">
      <a href="client_edit.php" class="btn btn-primary m-1 float-end" id="btn_nuevo">
        <i class="ti ti-plus"></i> Nuevo Cliente
      </a>
      <h4 class="main-title">Clientes</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="#" class="f-s-14 f-w-500">
            <span><i class="ph-duotone ph-table f-s-16"></i> Operaciones</span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Clientes</a>
        </li>
      </ul>
    </div>
  </div>
  <!-- Breadcrumb end -->

  <!-- Data Table start -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body p-0">
          <div class="table-responsive app-scroll app-datatable-default product-list-table">
            <table class="table-sm display align-middle" id="basic-1">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Apellidos</th>
                  <th>Teléfono</th>
                  <th>Email</th>
                  <th>NIT</th>
                  <th>Razón Social</th>
                  <th>Rubro</th>
                  <th>Fecha Registro</th>
                  <th>Estado</th>
                  <th>Opciones</th>
                </tr>
              </thead>
              <tbody id="DetalleTabla">
                <tr><td colspan="10">CARGANDO...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Data Table end -->
</div>

<?php include('../layout/footer.php'); ?>

<!-- data table jquery-->
<script src="../assets/vendor/datatable/jquery.dataTables.min.js"></script>
<script src="../assets/js/jquery.validate.min.js"></script>
<script src="../assets/js/jquery-key-restrictions.min.js"></script>

<!-- js-->
<script>
$(function($){
    ObtenerClientes();
    
    // Restricciones de teclado
    $("#nombre").lettersOnly();
    $("#apellido1").lettersOnly();
    $("#apellido2").lettersOnly();
});

function ObtenerClientes() {
    $.ajax({
        url: '../controllers/client_controller.php',
        type: 'GET',
        dataType: "json",
        success: function(data) {
            localStorage.setItem('sml2020_clientes', JSON.stringify(data));
            actualizarTabla();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (textStatus === 'timeout') {
                Swal.fire('Error', '¡La conexión a internet se ha interrumpido!', 'error');
            } else {
                Swal.fire('Error', 'Error al cargar clientes: ' + errorThrown, 'error');
            }
        }
    });
}

function actualizarTabla() {
    $('#DetalleTabla').empty();

    if ($.fn.dataTable.isDataTable('#basic-1')) {
        $('#basic-1').DataTable().destroy();
    }

    var localData = JSON.parse(localStorage.getItem('sml2020_clientes'));
    let html = '';

    $.each(localData, function(key, value) {
        const est = value.estado === 'V' ?
            '<span class="badge rounded-pill bg-success badge-notification">HABILITADO</span>' :
            '<span class="badge rounded-pill bg-danger badge-notification">DESHABILITADO</span>';

        const edi = `<a href="client_edit.php?id=${value.id}" class="btn btn-primary"><i class="fa fa-pencil"></i></a>`;
        
        const fechaRegistro = new Date(value.fecha_registro).toLocaleDateString();

        html += `
            <tr role="row" class="odd">
                <td class="sorting_1">${value.nombre}</td>
                <td>${value.apellido1} ${value.apellido2 || ''}</td>
                <td>${value.cel1}</td>
                <td>${value.email}</td>
                <td>${value.nit}</td>
                <td>${value.razon_social}</td>
                <td>${value.rubro}</td>
                <td>${fechaRegistro}</td>
                <td>${est}</td>
                <td>${edi}</td>
            </tr>
        `;
    });

    $('#DetalleTabla').html(html);
    $('#basic-1').DataTable();    
}
</script>