<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">
<?php
include('../layout/header.php');
?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12 ">
      <h4 class="main-title">Ventas</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="#" class="f-s-14 f-w-500">
            <span>
              <i class="ph-duotone ph-table f-s-16"></i> Reportes
            </span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Ventas</a>
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
                  <th>Acciones</th>
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
    ObtenerVentas();
});

function ObtenerVentas() {
    $.ajax({
        url: '../controllers/sales_controller.php',
        type: 'GET',
        dataType: "json",
        data: {  },
        success: function (data) {
            localStorage.setItem('sml2025_sales', JSON.stringify(data)); // Store all sales in localStorage
            actualizarTablaVentas();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if (textStatus === 'timeout') {
                Swal.fire('Error', '¡La conexión a internet se ha interrumpido!', 'error');
            } else {
                Swal.fire('Error', 'Error al cargar ventas: ' + errorThrown, 'error');
            }
        }
    });
}

function actualizarTablaVentas() {
    $('#DetalleTabla').empty();

    if ($.fn.dataTable.isDataTable('#basic-1')) {
        $('#basic-1').DataTable().destroy();
    }
    
    var localData = JSON.parse(localStorage.getItem('sml2025_sales'));
    cant_prod = localData.length;

    let html = '';
    $.each(localData, function(key, value) {
        let estado = '';
        if(value.estado === 'V') {
            estado = '<span class="badge rounded-pill bg-success badge-notification">VENDIDO</span>';
        } else if(value.estado === 'ANU') {
            estado = '<span class="badge rounded-pill bg-danger badge-notification">ANULADO</span>';
        } else {
            estado = '<span class="badge rounded-pill bg-warning badge-notification">PENDIENTE</span>';
        }

        const acciones = `
            <button class="btn btn-primary" onclick="VerVenta('${value.numero}')"><i class="fa fa-eye"></i></button>
            <button class="btn btn-info" onclick="GenerarNotaEntrega('${value.numero}')"><i class="fa fa-file-text"></i></button>
        `;

        html += `
            <tr role="row" class="odd">
                <td>${value.numero}</td>
                <td class="sorting_1"><b>Nombre</b>: ${value.nombre} ${value.apellido1} ${value.apellido2}<br><b>Teléfono:</b> ${value.telefono}<br><b>Celular:</b> ${value.celular}<br><b>Dirección:</b> ${value.direccion}</td>
                <td>${value.fecha}</td>
                <td>${value.total}</td>
                <td>${estado}</td>
                <td>${acciones}</td>
            </tr>
        `;
    });

    $('#DetalleTabla').html(html);
    $('#basic-1').DataTable();    
}

function VerVenta(id) {
    window.location.href = 'sale_detail_view.php?id=' + encodeURIComponent(id);
}

function GenerarNotaEntrega(idVenta) {
    Swal.fire({
        title: 'Generar Nota de Entrega',
        text: "¿Desea generar una nota de entrega para esta venta?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, generar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../controllers/sales_controller.php',
                type: 'POST',
                data: { 
                    action: 'generate_delivery_note',
                    sale_id: idVenta
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Nota de Entrega Generada',
                            text: 'La nota de entrega se ha creado correctamente',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire('Error', response.error, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Error al generar nota de entrega: ' + error, 'error');
                }
            });
        }
    });
}
</script>