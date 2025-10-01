<?php
include('../layout/header.php');

// Obtener ID de cotización desde URL
$id_cotizacion = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id_cotizacion) {
    echo "<script>window.location.href = 'quotes.php';</script>";
    exit;
}
?>

<div class="container-fluid">
    <!-- Breadcrumb start -->
    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Detalle de Cotización</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li class="">
                    <a href="#" class="f-s-14 f-w-500">
                        <span><i class="ph-duotone ph-table f-s-16"></i> Reportes</span>
                    </a>
                </li>
                <li class="">
                    <a href="quotes.php" class="f-s-14 f-w-500">Cotizaciones</a>
                </li>
                <li class="active">
                    <a href="#" class="f-s-14 f-w-500">Detalle</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Breadcrumb end -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Información de Cotización</h5>
                    <button class="btn btn-success float-end" id="btnConvertirVenta">Convertir a Venta</button>
                </div>
                <div class="card-body">
                    <div id="cabeceraCotizacion"></div>
                    <hr>
                    <h5>Productos</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="detalleProductos">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="detalleProductosBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php'); ?>

<script>
$(document).ready(function() {
    // Obtener datos de la cotización
    cargarDetalleCotizacion('<?php echo $id_cotizacion; ?>');
    
    // Evento para convertir a venta
    $('#btnConvertirVenta').click(function() {
        Swal.fire({
            title: '¿Convertir a Venta?',
            text: "¿Está seguro de convertir esta cotización en una venta?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, convertir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                convertirAVenta('<?php echo $id_cotizacion; ?>');
            }
        });
    });
});

function cargarDetalleCotizacion(id) {
    $.ajax({
        url: '../controllers/quotes_controller.php',
        type: 'GET',
        data: { action: 'get_quote_detail', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Mostrar cabecera
                let cabecera = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Número:</strong> ${response.data.cabecera.numero}</p>
                            <p><strong>Cliente:</strong> ${response.data.cabecera.nombre_cliente}</p>
                            <p><strong>Fecha:</strong> ${response.data.cabecera.fecha}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total:</strong> ${response.data.cabecera.total}</p>
                            <p><strong>Estado:</strong> ${response.data.cabecera.estado}</p>
                        </div>
                    </div>
                `;
                $('#cabeceraCotizacion').html(cabecera);
                
                // Mostrar detalle de productos
                let detalleHtml = '';
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
                });
                $('#detalleProductosBody').html(detalleHtml);
                
                // Deshabilitar botón si no está aprobada
                if (response.data.cabecera.estado !== 'APRO') {
                    $('#btnConvertirVenta').prop('disabled', true)
                        .removeClass('btn-success')
                        .addClass('btn-secondary')
                        .text('Cotización no aprobada');
                }
            } else {
                Swal.fire('Error', response.error, 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', 'Error al cargar detalle: ' + error, 'error');
        }
    });
}

function convertirAVenta(idCotizacion) {
    $.ajax({
        url: '../controllers/sale_controller.php',
        type: 'POST',
        data: { action: 'convert_to_sale', id_cotizacion: idCotizacion },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Venta Generada',
                    html: `La venta se ha registrado con el número: <strong>${response.numero_venta}</strong>`,
                    icon: 'success',
                    confirmButtonText: 'Ver Venta'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'sale_view.php?id=' + response.numero_venta;
                    }
                });
            } else {
                Swal.fire('Error', response.error, 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', 'Error al convertir a venta: ' + error, 'error');
        }
    });
}
</script>