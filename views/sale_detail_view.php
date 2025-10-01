<?php
include('../layout/header.php');

// Obtener ID de venta desde URL
$id_venta = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id_venta) {
    echo "<script>window.location.href = 'sales.php';</script>";
    exit;
}
?>

<div class="container-fluid">
    <!-- Breadcrumb start -->
    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Detalle de Venta</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li class="">
                    <a href="#" class="f-s-14 f-w-500">
                        <span><i class="ph-duotone ph-table f-s-16"></i> Reportes</span>
                    </a>
                </li>
                <li class="">
                    <a href="sales.php" class="f-s-14 f-w-500">Ventas</a>
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
                    <h5>Información de Venta</h5>
                    <div class="float-end">
                        <!-- <button class="btn btn-primary" id="btnImprimir" onclick="imprimirDocumento()">
                            <i class="fa fa-print"></i> Imprimir
                        </button> -->
                        <button class="btn btn-success" id="btnNotaEntrega" onclick="generarNotaEntrega()">
                            <i class="fa fa-file-text"></i> Nota de Entrega
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="cabeceraVenta"></div>
                    <hr>
                    <h5>Productos</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="detalleProductos">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th>Descripción</th>
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
    // Obtener datos de la venta
    cargarDetalleVenta('<?php echo $id_venta; ?>');
});

function cargarDetalleVenta(id) {
    $.ajax({
        url: '../controllers/sales_controller.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Mostrar cabecera
                let cabecera = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Número de Venta:</strong> ${response.data.cabecera.numero}</p>
                            <p><strong>Cliente:</strong> ${response.data.cabecera.nombre_cliente}</p>
                            <p><strong>NIT/CI:</strong> ${response.data.cabecera.nit || 'No especificado'}</p>
                            <p><strong>Fecha:</strong> ${response.data.cabecera.fecha}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total:</strong> ${response.data.cabecera.total}</p>
                            <p><strong>Estado:</strong> ${response.data.cabecera.estado === 'V' ? 'Vendido' : 'Anulado'}</p>
                            <p><strong>Teléfono:</strong> ${response.data.cabecera.telefono}</p>
                            <p><strong>Dirección:</strong> ${response.data.cabecera.direccion}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Glosa:</strong> ${response.data.cabecera.glosa}</p>
                        </div>
                    </div>
                `;
                $('#cabeceraVenta').html(cabecera);
                
                // Mostrar detalle de productos
                let detalleHtml = '';
                let subtotal = 0;
                
                response.data.detalle.forEach((producto, index) => {
                    detalleHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${producto.producto}</td>
                            <td>${producto.descripcion}</td>
                            <td>${producto.cantidad}</td>
                            <td>${producto.precio_unitario}</td>
                            <td>${producto.precio_total}</td>
                        </tr>
                    `;
                    subtotal += parseFloat(producto.precio_total);
                });
                
                // Agregar fila de total
                detalleHtml += `
                    <tr>
                        <td colspan="5" class="text-end"><strong>TOTAL:</strong></td>
                        <td><strong>${subtotal.toFixed(2)}</strong></td>
                    </tr>
                `;
                
                $('#detalleProductosBody').html(detalleHtml);
                
                // Deshabilitar botones si está anulado
                if (response.data.cabecera.estado === 'ANU') {
                    $('#btnNotaEntrega').prop('disabled', true)
                        .removeClass('btn-success')
                        .addClass('btn-secondary');
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

function generarNotaEntrega() {
    const idVenta = '<?php echo $id_venta; ?>';
    
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
                            html: `La nota de entrega se ha creado con el número: <strong>${response.delivery_note_id}</strong>`,
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

function imprimirDocumento() {
    // Abrir una nueva ventana con el contenido para imprimir
    const ventanaImpresion = window.open('', '_blank');
    
    // Obtener los datos de la venta
    const cabecera = $('#cabeceraVenta').html();
    const detalle = $('#detalleProductos').html();
    
    // Construir el contenido HTML para imprimir
    const contenido = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Comprobante de Venta</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 20px; }
                .info { margin-bottom: 15px; }
                .info p { margin: 5px 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .total-row { font-weight: bold; }
                .footer { margin-top: 30px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>COMPROBANTE DE VENTA</h2>
                <p>Fecha: ${new Date().toLocaleDateString()}</p>
            </div>
            
            <div class="info">
                ${cabecera}
            </div>
            
            <div class="detalle">
                <h3>Detalle de Productos</h3>
                ${detalle}
            </div>
            
            <div class="footer">
                <p>Gracias por su compra</p>
                <p>Este documento no es válido como factura</p>
            </div>
            
            `;
    
    
    ventanaImpresion.document.open();
    ventanaImpresion.document.write(contenido);
    ventanaImpresion.document.close();
  
}
</script>