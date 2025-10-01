<?php
include('../layout/header.php');

// Obtener ID de venta desde URL
$id_venta = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id_venta) {
    echo "<script>window.location.href = 'quotes.php';</script>";
    exit;
}
?>

<div class="container-fluid">
    <!-- Breadcrumb start -->
    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Comprobante de Venta</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li class="">
                    <a href="#" class="f-s-14 f-w-500">
                        <span><i class="ph-duotone ph-table f-s-16"></i> Ventas</span>
                    </a>
                </li>
                <li class="active">
                    <a href="#" class="f-s-14 f-w-500">Comprobante</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Breadcrumb end -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Detalle de Venta</h5>
                    <button class="btn btn-primary float-end" onclick="window.print()">
                        <i class="ti ti-printer"></i> Imprimir
                    </button>
                </div>
                <div class="card-body">
                    <div id="cabeceraVenta"></div>
                    <hr>
                    <h5>Productos</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="detalleProductosVenta">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="detalleProductosVentaBody"></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th id="totalVenta"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">Gracias por su compra</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php'); ?>

<script>
$(document).ready(function() {
    cargarDetalleVenta('<?php echo $id_venta; ?>');
});

function cargarDetalleVenta(id) {
    $.ajax({
        url: '../controllers/sale_controller.php',
        type: 'GET',
        data: { action: 'get_sale_detail', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Mostrar cabecera
                let cabecera = `
                    <div class="row">
                        <div class="col-md-6">
                            <h5>${response.data.cabecera.razon_social || response.data.cabecera.nombre_cliente}</h5>
                            <p><strong>NIT/CI:</strong> ${response.data.cabecera.nit || 'No especificado'}</p>
                            <p><strong>Dirección:</strong> ${response.data.cabecera.direccion || 'No especificada'}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h4>VENTA #${response.data.cabecera.numero}</h4>
                            <p><strong>Fecha:</strong> ${response.data.cabecera.fecha}</p>
                            <p><strong>Original:</strong> Cotización #${response.data.cabecera.id_cotizacion_origen || 'N/A'}</p>
                        </div>
                    </div>
                `;
                $('#cabeceraVenta').html(cabecera);
                
                // Mostrar detalle de productos
                let detalleHtml = '';
                let totalVenta = 0;
                
                response.data.detalle.forEach((producto, index) => {
                    detalleHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${producto.producto}</td>
                            <td>${producto.cantidad}</td>
                            <td>${producto.precio_unitario}</td>
                            <td>${producto.precio_total}</td>
                        </tr>
                    `;
                    totalVenta += parseFloat(producto.precio_total);
                });
                
                $('#detalleProductosVentaBody').html(detalleHtml);
                $('#totalVenta').text(totalVenta.toFixed(2));
            } else {
                Swal.fire('Error', response.error, 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', 'Error al cargar detalle de venta: ' + error, 'error');
        }
    });
}
</script>