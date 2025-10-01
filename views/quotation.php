<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">
<!-- Galería de imágenes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/css/lightgallery.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/css/lg-zoom.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/css/lg-fullscreen.min.css" />

<?php
include('../layout/header.php');
?>

<div class="container-fluid">
    <!-- Modal para finalizar cotización -->
    <div id="ModalFinVenta" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title">Finalizar Cotización</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <form id="FormFinVenta">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle"></i> Revise los detalles antes de finalizar la cotización
                        </div>
                        <div id="textoVenta" class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="btn_finalizar_venta">
                            <i class="ti ti-file-invoice"></i> Registrar Cotización
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para agregar producto -->
    <div class="modal fade" id="add-carrito" tabindex="-1" role="dialog" aria-labelledby="editCardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title">Agregar Producto</h4>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="FormAgregarCarrito">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Producto:</label>
                                    <input type="text" class="form-control" id="carrito_descripcion" readonly>
                                    <input type="hidden" id="carrito_id_producto">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Precio Unitario (Bs.):</label>
                                    <input type="text" class="form-control" id="carrito_precio" placeholder="0.00" required>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Cantidad:</label>
                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary decrement" type="button">-</button>
                                        <input type="number" class="form-control text-center" id="carrito_cantidad" min="1" value="1" required>
                                        <button class="btn btn-outline-secondary increment" type="button">+</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="product-image-preview border p-3 text-center">
                                    <img id="productImagePreview" src="../assets/images/no-image.jpg" class="img-fluid rounded" style="max-height: 200px;">
                                    <div class="mt-2">
                                        <small class="text-muted" id="productCodePreview">Código: -</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-shopping-cart-plus"></i> Agregar al carrito
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para seleccionar cliente -->
    <div class="modal fade" id="add-cliente" tabindex="-1" role="dialog" aria-labelledby="editCardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title">Seleccionar Cliente</h4>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="FormAgregarCliente">
                        
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Nombre Completo:</label>
                                    <input type="text" class="form-control" id="carrito_cliente_descripcion" readonly>
                                    <input type="hidden" id="carrito_id_cliente">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Teléfono:</label>
                                    <input type="text" class="form-control" id="carrito_cel1">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Celular:</label>
                                    <input type="text" class="form-control" id="carrito_cel2">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Email:</label>
                                    <input type="email" class="form-control" id="carrito_email">
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-user-check"></i> Seleccionar Cliente
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="row m-1">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="main-title">Cotización</h4>
                    <ul class="app-line-breadcrumbs mb-3">
                        <li>
                            <a href="#" class="f-s-14 f-w-500">
                                <i class="ph-duotone ph-stack f-s-16"></i> Operaciones
                            </a>
                        </li>
                        <li class="active">
                            <a href="#" class="f-s-14 f-w-500">Cotización</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <button class="btn btn-primary" id="btnNuevaCotizacion">
                        <i class="ti ti-plus"></i> Nueva Cotización
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills nav-justified mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="pill" href="#tab-cliente" role="tab">
                                <i class="ti ti-user-circle me-1"></i> Datos del Cliente
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="pill" href="#tab-productos" role="tab">
                                <i class="ti ti-shopping-cart me-1"></i> Productos
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-cliente" role="tabpanel">
                           
                            <div class="table-responsive">
                                <table class="table table-hover" id="basic-2">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Apellidos</th>
                                            <th>Contacto</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="DetalleTablaClientes">
                                        <tr>
                                            <td colspan="4" class="text-center">Cargando clientes...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="tab-productos" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title">Lista de productos disponibles</h5>
                               
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover" id="basic-1">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Imagen</th>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th>Precio</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="DetalleTabla">
                                        <tr>
                                            <td colspan="5" class="text-center">Cargando productos...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="ti ti-user me-1"></i> Información del Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar avatar-xl bg-light-primary rounded-circle mb-2">
                            <i class="ti ti-user f-s-24"></i>
                        </div>
                        <h5 class="mb-1" id="fin_nombre_cliente">No seleccionado</h5>
                        <p class="text-muted mb-1" id="fin_email">-</p>
                        <div class="d-flex justify-content-center gap-2 mt-2">
                            <span class="badge bg-light-secondary" id="fin_cel1">-</span>
                            <span class="badge bg-light-success" id="fin_cel2">-</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-shopping-cart me-2"></i>
                        <h5 class="mb-0">Productos seleccionados</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-hover" id="carritoList">
                            <thead class="sticky-top bg-light">
                                <tr>
                                    <th width="10%">Cant.</th>
                                    <th width="50%">Producto</th>
                                    <th width="20%">Total</th>
                                    <th width="20%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center py-4">No hay productos agregados</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="border-top mt-3 pt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Subtotal:</h6>
                            <h5 class="mb-0 text-primary" id="total_venta">0.00 Bs.</h5>
                        </div>
                        
                        <form id="FormFinalizarCotizacion" class="mt-3">
                            <input type="hidden" id="fin_id_cliente" required>
                            
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Fecha de cotización:</label>
                                <input class="form-control basic-date" type="text" placeholder="Seleccione fecha" id="fin_fecha" required>
                            </div>
                            
                            <div class="d-grid gap-2" id="boton_fin_venta">
                                <button type="button" class="btn btn-primary" disabled>
                                    <i class="ti ti-user-off me-1"></i> Seleccione un cliente primero
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include('../layout/footer.php');
?>

<!-- Scripts -->
<script src="../assets/vendor/datatable/jquery.dataTables.min.js"></script>
<script src="../assets/js/jquery.validate.min.js"></script>

<!-- Galería de imágenes -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/lightgallery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/plugins/zoom/lg-zoom.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/plugins/fullscreen/lg-fullscreen.min.js"></script>

<script>
var elCodigoProducto;
var ventas = [];
var currentCliente = null;

$(document).ready(function() {
    // Inicializar datepicker
    flatpickr(".basic-date", {
        enableTime: false,
        dateFormat: "Y-m-d",
        defaultDate: new Date()
    });
    
    
    
    
    // Botón para nueva cotización
    $('#btnNuevaCotizacion').click(function() {
        resetCotizacion();
    });
    
    // Cargar datos iniciales
    ObtenerProductosIngreso();
    ObtenerClientesIngreso();
    
    // Validación de precio
    $('#carrito_precio').on('blur', function() {
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
            
            // Calcular total si hay cantidad
            if ($('#carrito_cantidad').val() > 0) {
                calcularTotalProducto();
            }
        }
    });
    
    // Incrementar/decrementar cantidad
    $('.increment').click(function() {
        var input = $('#carrito_cantidad');
        var value = parseInt(input.val()) || 0;
        input.val(value + 1);
        calcularTotalProducto();
    });
    
    $('.decrement').click(function() {
        var input = $('#carrito_cantidad');
        var value = parseInt(input.val()) || 0;
        if (value > 1) {
            input.val(value - 1);
            calcularTotalProducto();
        }
    });
    
    $('#carrito_cantidad').on('change', function() {
        calcularTotalProducto();
    });
});

function calcularTotalProducto() {
    var cantidad = parseInt($('#carrito_cantidad').val()) || 0;
    var precio = parseFloat($('#carrito_precio').val()) || 0;
    var total = cantidad * precio;
    $('#productTotalPreview').text('Total: ' + total.toFixed(2) + ' Bs.');
}

function resetCotizacion() {
    ventas = [];
    currentCliente = null;
    $('#fin_id_cliente').val('');
    $('#fin_nombre_cliente').text('No seleccionado');
    $('#fin_email').text('-');
    $('#fin_cel1').text('-');
    $('#fin_cel2').text('-');
    $('#carritoList tbody').html('<tr><td colspan="4" class="text-center py-4">No hay productos agregados</td></tr>');
    $('#total_venta').text('0.00 Bs.');
    $('#boton_fin_venta').html('<button type="button" class="btn btn-primary" disabled><i class="ti ti-user-off me-1"></i> Seleccione un cliente primero</button>');
    $('.nav-pills a[href="#tab-cliente"]').tab('show');
}

function ObtenerProductosIngreso() {
    $.ajax({
        url: '../controllers/product_controller.php',
        type: 'GET',
        dataType: "json",
        data: { },
        success: function(data) {
            localStorage.setItem('sml2020_productos', JSON.stringify(data));
            actualizarTabla();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (textStatus === 'timeout') {
                Swal.fire('Error', '¡La conexión a internet se ha interrumpido!', 'error');
            } else {
                Swal.fire('Error', 'Error al cargar productos: ' + errorThrown, 'error');
            }
        }
    });
}

function actualizarTabla() {
    $('#DetalleTabla').empty();

    if ($.fn.dataTable.isDataTable('#basic-1')) {
        $('#basic-1').DataTable().destroy();
    }
    
    var localData = JSON.parse(localStorage.getItem('sml2020_productos'));
    let html = '';
    
    $.each(localData, function(key, value) {
        const edi = `<button class="btn btn-sm btn-primary" onclick="Editar('${value.id}')" data-bs-toggle="modal" data-bs-target="#add-carrito">
                        <i class="ti ti-shopping-cart-plus"></i> Agregar
                    </button>`;
        
        // Celda de imagen
        const imageCell = `<td class="product-image-td" data-product-id="${value.id}">
                              <div class="spinner-border spinner-border-sm text-primary"></div>
                          </td>`;
        
        html += `
            <tr>
                ${imageCell}
                <td>${value.producto_codigo}</td>
                <td><b>${value.producto_nombre}</b><br><small class="text-muted">${value.producto_descripcion}</small></td>
                <td>${parseFloat(value.puntos).toFixed(2)} Bs.</td>
                <td>${edi}</td>
            </tr>
        `;
    });

    $('#DetalleTabla').html(html);
    
    // Inicializar DataTable con búsqueda
    $('#basic-1').DataTable({
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        language: {
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros por página",
            zeroRecords: "No se encontraron productos",
            info: "Mostrando _START_ a _END_ de _TOTAL_ productos",
            infoEmpty: "No hay productos disponibles",
            infoFiltered: "(filtrado de _MAX_ productos totales)"
        }
    });
    
    // Cargar imágenes para cada producto
    $('.product-image-td').each(function() {
        const productId = $(this).data('product-id');
        loadProductImageForQuotation(productId, $(this));
    });
}

// Cargar imagen del producto para cotización
function loadProductImageForQuotation(productId, cellElement) {
    $.ajax({
        url: '../controllers/image_controller.php?entidad_tipo=producto&entidad_id=' + productId,
        type: 'GET',
        dataType: 'json',
        success: function(images) {
            let html = '';
            if (images.length > 0) {
                html = `<img src="../${images[0].ruta}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">`;
            } else {
                html = `<div class="no-image-placeholder bg-light rounded" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                          <i class="ti ti-photo text-muted"></i>
                       </div>`;
            }
            cellElement.html(html);
        },
        error: function() {
            cellElement.html('<div class="text-danger">Error</div>');
        }
    });
}

function ObtenerClientesIngreso() {
    $.ajax({
        url: '../controllers/client_controller.php',
        type: 'GET',
        dataType: "json",
        data: { },
        success: function(data) {
            localStorage.setItem('sml2020_clientes', JSON.stringify(data));
            actualizarTablaClientes();
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

function actualizarTablaClientes() {
    $('#DetalleTablaClientes').empty();

    if ($.fn.dataTable.isDataTable('#basic-2')) {
        $('#basic-2').DataTable().destroy();
    }
    
    var localData = JSON.parse(localStorage.getItem('sml2020_clientes'));
    let html = '';
    
    $.each(localData, function(key, value) {
        const edi = `<button class="btn btn-sm btn-primary" onclick="SeleccionarCliente('${value.id}')">
                        <i class="ti ti-user-check"></i> Seleccionar
                    </button>`;
        
        html += `
            <tr>
                <td>${value.nombre}</td>
                <td>${value.apellido1} ${value.apellido2}</td>
                <td><small>${value.cel2 || '-'}<br>${value.email || '-'}</small></td>
                <td>${edi}</td>
            </tr>
        `;
    });

    $('#DetalleTablaClientes').html(html);
    $('#basic-2').DataTable({
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        language: {
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros por página",
            zeroRecords: "No se encontraron clientes",
            info: "Mostrando _START_ a _END_ de _TOTAL_ clientes",
            infoEmpty: "No hay clientes disponibles",
            infoFiltered: "(filtrado de _MAX_ clientes totales)"
        }
    });
}

function SeleccionarCliente(elId) { 
    var localData = JSON.parse(localStorage.getItem('sml2020_clientes'));
    $.each(localData, function(key, value) {
        if (value.id === elId) { 
            $('#carrito_id_cliente').val(value.id);
            $('#carrito_cliente_descripcion').val(value.nombre + " " + value.apellido1 + " " + value.apellido2);
            $('#carrito_cel1').val(value.cel1);
            $('#carrito_cel2').val(value.cel2);
            $('#carrito_email').val(value.email);
            
            // Mostrar modal para confirmar o editar datos
            $('#add-cliente').modal("show");
            return;
        }
    });          
}

function Editar(elId) { 
    var validator = $("#FormAgregarCarrito").validate();
    validator.resetForm();
    var localData = JSON.parse(localStorage.getItem('sml2020_productos'));

    $.each(localData, function(key, value) {
        if (value.id === elId) { 
            $('#carrito_id_producto').val(value.id);
            $('#carrito_descripcion').val(value.producto_nombre);
            precio = parseFloat(value.puntos);
            $('#carrito_precio').val(precio.toFixed(2));
            $('#carrito_cantidad').val("1");
            
            // Cargar imagen del producto
            loadProductImageForModal(value.id, value.producto_codigo);
            return;
        }
    }); 
    
    $('#add-carrito').modal("show");
}

// Cargar imagen para el modal de producto
function loadProductImageForModal(productId, productCode) {
    $.ajax({
        url: '../controllers/image_controller.php?entidad_tipo=producto&entidad_id=' + productId,
        type: 'GET',
        dataType: 'json',
        success: function(images) {
            if (images.length > 0) {
                $('#productImagePreview').attr('src', '../' + images[0].ruta);
            } else {
                $('#productImagePreview').attr('src', '../assets/images/no-image.jpg');
            }
            $('#productCodePreview').text('Código: ' + productCode);
        }
    });
}

$("#FormAgregarCarrito").submit(function(e) {
    e.preventDefault();
}).validate({  
    submitHandler: function(form) { 
        GuardarEnCarrito();
        return false;
    }
});

$("#FormAgregarCliente").submit(function(e) {
    e.preventDefault();
}).validate({  
    submitHandler: function(form) { 
        GuardarClienteEnCarrito(); 
        $('#add-cliente').modal('hide');
        return false;
    }
});

function GuardarClienteEnCarrito() {
    var Id = $('#carrito_id_cliente').val();
    var descripcion = $('#carrito_cliente_descripcion').val();
    var cel1 = $('#carrito_cel1').val();
    var cel2 = $('#carrito_cel2').val();
    var email = $('#carrito_email').val();
    
    $('#fin_id_cliente').val(Id);
    $('#fin_nombre_cliente').text(descripcion);
    $('#fin_cel1').text(cel1 || '-');
    $('#fin_cel2').text(cel2 || '-');
    $('#fin_email').text(email || '-');
    
    // Habilitar botón de finalizar
    $('#boton_fin_venta').html('<button type="button" class="btn btn-success w-100" onclick="MostrarModalFinVenta()">' +
                               '<i class="ti ti-check me-1"></i> Finalizar Cotización</button>');
    
    // Cambiar a pestaña de productos
    $('.nav-pills a[href="#tab-productos"]').tab('show');
}

function GuardarEnCarrito() {
    var Id = $('#carrito_id_producto').val();
    var descripcion = $('#carrito_descripcion').val();
    var cant = $('#carrito_cantidad').val();
    var precioU = parseFloat($('#carrito_precio').val());
    var precioT = parseFloat(precioU) * cant; 
    
    // Verificar si el producto ya está en el carrito
    var existe = false;
    $.each(ventas, function(index, prod) {
        if (prod[0] === Id) {
            ventas[index][2] = parseInt(ventas[index][2]) + parseInt(cant);
            ventas[index][4] = parseFloat(ventas[index][3]) * parseInt(ventas[index][2]);
            existe = true;
            return false; // Salir del each
        }
    });
    
    if (!existe) {
        var product = [Id, descripcion, cant, precioU, precioT];
        ventas.push(product);
    }
    
    UpdateCarrito();
    $('#add-carrito').modal("hide");
}

function UpdateCarrito() {
    $('#carritoList tbody').empty();
    $('#total_venta').empty();
    
    var html = '';
    var total = 0.00;
    
    if (ventas.length === 0) {
        html = '<tr><td colspan="4" class="text-center py-4">No hay productos agregados</td></tr>';
    } else {
        $.each(ventas, function(index, prod) {
            total += parseFloat(prod[4]);
            preciou = parseFloat(prod[3]).toFixed(2);
            preciot = parseFloat(prod[4]).toFixed(2);
            
            html += `
                <tr>
                    <td class="text-center">${prod[2]}</td>
                    <td><small>${prod[1]}</small></td>
                    <td class="text-end">${preciot} Bs.</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-danger" onclick="Borrar(${index})">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#carritoList tbody').html(html);
    $('#total_venta').text(total.toFixed(2) + ' Bs.');
}

function Borrar(index) {
    ventas.splice(index, 1);
    UpdateCarrito();
}

function MostrarModalFinVenta() {
    if (ventas.length === 0) {
        Swal.fire('Advertencia', 'Debe agregar al menos un producto a la cotización', 'warning');
        return;
    }
    
    if (!$('#fin_id_cliente').val()) {
        Swal.fire('Advertencia', 'Debe seleccionar un cliente', 'warning');
        return;
    }
    
    if (!$('#fin_fecha').val()) {
        Swal.fire('Advertencia', 'Debe seleccionar una fecha', 'warning');
        return;
    }
    
    $('#ModalFinVenta').modal("show");
    $('#btn_finalizar_venta').show();
}

$("#FormFinalizarCotizacion").submit(function(e) {
    e.preventDefault();
}).validate({  
    submitHandler: function(form) { 
        MostrarModalFinVenta();
        return false;  
    }
});

$("#FormFinVenta").submit(function(e) {
    e.preventDefault();
}).validate({  
    submitHandler: function(form) { 
        FinalizarCotizacion();
        return false;
    }
});

function FinalizarCotizacion() {
    var id_cliente = $('#fin_id_cliente').val();
    var fecha = $('#fin_fecha').val();
    
    if (!id_cliente || !fecha || ventas.length === 0) {
        Swal.fire('Error', 'Complete todos los datos requeridos', 'error');
        return;
    }
    
    $("#btn_finalizar_venta").prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...');
    
    var productsJSON = JSON.stringify(ventas);
    
    $.ajax({
        url: '../controllers/cotizacion_service.php',
        type: 'POST',
        dataType: "json",
        data: {
            'productos': productsJSON,
            'id_cliente': id_cliente,
            'fecha': fecha,
            'tipo': "cotizador"
        },
        success: function(data) { 
            if (data.success) { 
                var documento = data.results[0].documento;
                
                var html = `
                    <div class="alert alert-success">
                        <i class="ti ti-check-circle"></i> Cotización registrada exitosamente
                    </div>
                    <div class="text-center">
                        <h5 class="mb-3">Número de cotización: <strong>${documento}</strong></h5>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="invoice.php?iddoc=${documento}" class="btn btn-primary">
                                <i class="ti ti-file-invoice me-1"></i> Ver Cotización
                            </a>
                            <button class="btn btn-outline-secondary" onclick="resetCotizacion()">
                                <i class="ti ti-plus me-1"></i> Nueva Cotización
                            </button>
                        </div>
                    </div>
                `;
                
                $('#textoVenta').html(html);
                
                // Resetear después de 5 segundos
                setTimeout(function() {
                    resetCotizacion();
                    $('#ModalFinVenta').modal('hide');
                }, 5000);
            } else {
                $('#textoVenta').html('<div class="alert alert-danger">Error al registrar la cotización</div>');
                $("#btn_finalizar_venta").prop('disabled', false).html('<i class="ti ti-file-invoice"></i> Registrar Cotización');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            $('#textoVenta').html('<div class="alert alert-danger">Error: ' + errorThrown + '</div>');
            $("#btn_finalizar_venta").prop('disabled', false).html('<i class="ti ti-file-invoice"></i> Registrar Cotización');
        }
    });
}
</script>

<style>
/* Estilos personalizados */
.card-header {
    border-bottom: none;
}

.nav-pills .nav-link {
    border-radius: 0;
    padding: 12px 0;
    color: #495057;
    border-bottom: 3px solid transparent;
}

.nav-pills .nav-link.active {
    background: none;
    color: #0d6efd;
    border-bottom-color: #0d6efd;
    font-weight: 500;
}

.product-image-preview {
    background-color: #f8f9fa;
    border-radius: 8px;
}

.no-image-placeholder {
    background-color: #f8f9fa;
    color: #6c757d;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

#carritoList thead th {
    position: sticky;
    top: 0;
    background-color: #f8f9fa;
    z-index: 10;
}

.flatpickr-input {
    background-color: white;
}
</style>