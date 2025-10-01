<?php
include('../layout/header.php');

// Obtener ID de cotización de la URL
$quoteId = isset($_GET['id']) ? $_GET['id'] : 0;
?>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.15.2/ui/trumbowyg.min.css">
<div class="container-fluid">
  <!-- Breadcrumb -->
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title">Editar Cotización</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li>
          <a href="quotes.php" class="f-s-14 f-w-500">
            <i class="ti ti-file-invoice"></i> Cotizaciones
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Editar</a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Formulario de cotización -->
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <form id="formQuote" class="app-form">
            <input type="hidden" id="id_cotizacion" value="<?php echo $quoteId; ?>">
            
            <!-- Información del cliente -->
            <div class="main-title">
              <h6>Información del Cliente</h6>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Nombre<span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Apellido Paterno<span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="apellido1" name="apellido1" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label">Apellido Materno</label>
                  <input type="text" class="form-control" id="apellido2" name="apellido2">
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Dirección</label>
                  <input type="text" class="form-control" id="direccion" name="direccion">
                </div>
              </div>
              <div class="col-md-3">
                <div class="mb-3">
                  <label class="form-label">Teléfono</label>
                  <input type="text" class="form-control" id="telefono" name="telefono">
                </div>
              </div>
              <div class="col-md-3">
                <div class="mb-3">
                  <label class="form-label">Celular</label>
                  <input type="text" class="form-control" id="celular" name="celular">
                </div>
              </div>
            </div>
            
            <!-- Detalle de productos -->
            <div class="main-title">
              <h6>Productos</h6>
              <button type="button" class="btn btn-sm btn-primary" id="btnAddProduct">
                <i class="ti ti-plus"></i> Agregar Producto
              </button>
            </div>
            
            <div class="table-responsive">
              <table class="table table-bordered" id="productsTable">
                <thead>
                  <tr>
                    <th width="5%">Item</th>
                    <th width="10%">Cantidad</th>
                    <th width="45%">Descripción</th>
                    <th width="15%">P.U. (Bs.)</th>
                    <th width="15%">Total (Bs.)</th>
                    <th width="10%">Acciones</th>
                  </tr>
                </thead>
                <tbody id="productsList">
                  <!-- Productos se cargarán aquí -->
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="5" class="text-end"><strong>SUBTOTAL</strong></td>
                    <td class="text-end" id="subtotal">Bs. 0.00</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td colspan="5" class="text-end"><strong>ENVÍO</strong></td>
                    <td class="text-end" id="envio">Bs. 50.00</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td colspan="5" class="text-end"><strong>TOTAL</strong></td>
                    <td class="text-end" id="total">Bs. 50.00</td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>
            </div>
            
            <!-- Información adicional -->
            <div class="main-title">
              <h6>Información Adicional</h6>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Notas</label>
                  <textarea class="form-control" id="notas" rows="3"></textarea>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Términos y Condiciones</label>
                  <div id="terminosEditor">
                    <p>Garantía: 1 año por defectos de fábrica</p>
                    <p>Validez de la oferta: 30 días Calendario</p>
                    <p>Forma de pago: A convenir</p>
                    <p>Tiempo de entrega: Aproximadamente 40 días calendario</p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Botones -->
            <div class="row mt-4">
              <div class="col-12 text-end">
                <a href="quotes.php" role="button" class="btn btn-light-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary" id="btnSave">Guardar Cambios</button>
                <a type="button" class="btn btn-success" id="btnApprove">Aprobar Cotización</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para agregar/editar producto -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Agregar Producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formProduct">
          <input type="text" id="productIndex">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Producto<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="productName" required>
              </div>
            </div>
           
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label">Cantidad<span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="productQuantity" min="1" value="1" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label">Precio Unitario (Bs.)<span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" id="productPrice" min="0" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label">Total (Bs.)</label>
                <input type="text" class="form-control" id="productTotal" readonly>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <div id="productDescriptionEditor">
              <p></p>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <a type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</a>
        <button type="button" class="btn btn-primary" id="btnSaveProduct">Guardar</button>
      </div>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>

<!-- Editor de texto -->
<script src="//cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.15.2/trumbowyg.min.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.15.2/plugins/upload/trumbowyg.cleanpaste.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.15.2/plugins/upload/trumbowyg.pasteimage.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar editores
    $('#terminosEditor').trumbowyg({
        btns: [
            ['viewHTML'],
            ['undo', 'redo'],
            ['formatting'],
            ['strong', 'em', 'del'],
            ['link'],
            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
            ['unorderedList', 'orderedList'],
            ['horizontalRule'],
            ['removeformat']
        ]
    });
    
    $('#productDescriptionEditor').trumbowyg({
        btns: [
            ['viewHTML'],
            ['undo', 'redo'],
            ['formatting'],
            ['strong', 'em', 'del'],
            ['link'],
            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
            ['unorderedList', 'orderedList'],
            ['horizontalRule'],
            ['removeformat']
        ]
    });
    
    // Variables
    let productos = [];
    let editIndex = -1;
    let tipoCambio = 1;
    
    // Cargar datos de la cotización
    function loadQuoteData() {
        const quoteId = $('#id_cotizacion').val();
        if (!quoteId || quoteId == "0") return;
        
        $.ajax({
            url: '../controllers/cotizacion_controller.php?id=' + quoteId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const doc = response.documento;
                    const detalle = response.detalle;
                    
                    // Llenar datos del cliente
                    $('#nombre').val(doc.nombre);
                    $('#apellido1').val(doc.apellido1);
                    $('#apellido2').val(doc.apellido2 || '');
                    $('#direccion').val(doc.direccion || '');
                    $('#telefono').val(doc.cel1 || '');
                    $('#celular').val(doc.cel2 || '');
                    $('#notas').val(doc.glosa || '');
                    
                    // Cargar productos
                    productos = detalle.map(item => ({
                        producto: item.producto,
                        codigo: item.codigo || '',
                        cantidad: item.cantidad,
                        precio_unitario: item.precio_unitario,
                        precio_total: item.precio_total,
                        descripcion: item.descripcion || '',
                        id_marca: item.id_marca || 0,
                        marca: item.marca || ''
                    }));
                    
                    renderProducts();
                    updateTotals();
                    
                    // Obtener tipo de cambio actual
                    obtenerTipoCambio();
                } else {
                    Swal.fire('Error', response.error || 'Error al cargar la cotización', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error al cargar la cotización', 'error');
            }
        });
    }
    
    // Obtener tipo de cambio actual
    function obtenerTipoCambio() {
        $.ajax({
            url: '../controllers/tipo_cambio_controller.php?current=true',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response && response.tasa) {
                    tipoCambio = parseFloat(response.tasa);
                }
            }
        });
    }
    
    // Renderizar lista de productos
    function renderProducts() {
        const $tbody = $('#productsList');
        $tbody.empty();
        
        if (productos.length === 0) {
            $tbody.append('<tr><td colspan="7" class="text-center">No hay productos agregados</td></tr>');
            return;
        }
        
        productos.forEach((producto, index) => {
            const $tr = $(`
                <tr data-index="${index}">
                    <td>${index + 1}</td>
                    
                    <td>${producto.cantidad}</td>
                    <td>
                        <strong>${producto.producto}</strong>
                        <div class="product-description">${producto.descripcion || ''}</div>
                    </td>
                    <td class="text-end">${formatCurrency(producto.precio_unitario)}</td>
                    <td class="text-end">${formatCurrency(producto.precio_total)}</td>
                    <td class="text-center">
                        <a class="btn btn-sm btn-outline-primary edit-product" title="Editar">
                            <i class="ti ti-edit"></i>
                        </a>
                        <a class="btn btn-sm btn-outline-danger delete-product" title="Eliminar">
                            <i class="ti ti-trash"></i>
                        </a>
                    </td>
                </tr>
            `);
            
            $tbody.append($tr);
        });
    }
    
    // Formatear moneda
    function formatCurrency(amount) {
        return 'Bs. ' + parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    // Actualizar totales
    function updateTotals() {
        const subtotal = productos.reduce((sum, item) => sum + parseFloat(item.precio_total), 0);
        const envio = 50; // Costo fijo de envío
        const total = subtotal + envio;
        
        $('#subtotal').text(formatCurrency(subtotal));
        $('#envio').text(formatCurrency(envio));
        $('#total').text(formatCurrency(total));
    }
    
    // Abrir modal para agregar producto
    $('#btnAddProduct').click(function() {
        $('#modalTitle').text('Agregar Producto');
        $('#productIndex').val('');
        $('#productName').val('');
        
        $('#productQuantity').val(1);
        $('#productPrice').val('');
        $('#productTotal').val('0.00');
        $('#productDescriptionEditor').trumbowyg('html', '');
        
        const modal = new bootstrap.Modal(document.getElementById('productModal'));
        modal.show();
    });
    
    // Calcular total cuando cambia cantidad o precio
    $('#productQuantity, #productPrice').on('input', function() {
        const cantidad = parseFloat($('#productQuantity').val()) || 0;
        const precio = parseFloat($('#productPrice').val()) || 0;
        const total = cantidad * precio;
        
        $('#productTotal').val(total.toFixed(2));
    });
    
    // Guardar producto en el modal
    $('#btnSaveProduct').click(function() {
        const nombre = $('#productName').val().trim();
        const cantidad = parseFloat($('#productQuantity').val());
        const precio = parseFloat($('#productPrice').val());
        
        if (!nombre) {
            Swal.fire('Error', 'El nombre del producto es requerido', 'error');
            return;
        }
        
        if (isNaN(cantidad) || cantidad <= 0) {
            Swal.fire('Error', 'La cantidad debe ser mayor a cero', 'error');
            return;
        }
        
        if (isNaN(precio) || precio < 0) {
            Swal.fire('Error', 'El precio unitario debe ser válido', 'error');
            return;
        }
        
        const producto = {
            producto: nombre,
          
            cantidad: cantidad,
            precio_unitario: precio,
            precio_total: cantidad * precio,
            descripcion: $('#productDescriptionEditor').trumbowyg('html'),
            id_marca: 0,
            marca: ''
        };
        
        const index = $('#productIndex').val();
        if (index !== '') {
            // Editar producto existente
            productos[index] = producto;
        } else {
            // Agregar nuevo producto
            productos.push(producto);
        }
        
        renderProducts();
        updateTotals();
        
        // Cerrar modal
        bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
    });
    
    // Editar producto
    $(document).on('click', '.edit-product', function() {
        const index = $(this).closest('tr').data('index');
        const producto = productos[index];
        
        $('#modalTitle').text('Editar Producto');
        $('#productIndex').val(index);
        $('#productName').val(producto.producto);
       
        $('#productQuantity').val(producto.cantidad);
        $('#productPrice').val(producto.precio_unitario);
        $('#productTotal').val(producto.precio_total.toFixed(2));
        $('#productDescriptionEditor').trumbowyg('html', producto.descripcion || '');
        
        const modal = new bootstrap.Modal(document.getElementById('productModal'));
        modal.show();
    });
    
    // Eliminar producto
    $(document).on('click', '.delete-product', function() {
        const index = $(this).closest('tr').data('index');
        
        Swal.fire({
            title: '¿Eliminar producto?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                productos.splice(index, 1);
                renderProducts();
                updateTotals();
            }
        });
    });
    
    // Guardar cotización
    $('#formQuote').submit(function(e) {
        e.preventDefault();
        saveQuote(false);
    });
    
    // Aprobar cotización
    $('#btnApprove').click(function() {
        Swal.fire({
            title: '¿Aprobar esta cotización?',
            text: "Esta acción cambiará el estado a 'Aprobado'",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, aprobar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                aprobarCotizacion();
            }
        });
    });
    
    function aprobarCotizacion() {
    const idCotizacion = $('#id_cotizacion').val(); // Asumo que tienes un campo oculto con el ID
            
            $.ajax({
                url: '../controllers/cotizacion_controller.php',
                type: 'PATCH', // Usamos PATCH en lugar de PUT
                contentType: 'application/json',
                data: JSON.stringify({
                    id_documento: idCotizacion,
                    estado: 'APRO' // Solo enviamos estos dos campos
                }),
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = 'quotes.php';
                        });
                    } else {
                        Swal.fire('Error', response.error || 'Error al aprobar la cotización', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Ocurrió un error en la comunicación con el servidor', 'error');
                }
            });
}

    // Función para guardar cotización
    function saveQuote(approve) {
        const quoteId = $('#id_cotizacion').val();
        if (!quoteId || quoteId == "0") return;
        
        // Validar que haya al menos un producto
        if (productos.length === 0) {
            Swal.fire('Error', 'Debe agregar al menos un producto a la cotización', 'error');
            return;
        }
        
        // Validar datos del cliente
        const nombre = $('#nombre').val().trim();
        const apellido1 = $('#apellido1').val().trim();
        
        if (!nombre || !apellido1) {
            Swal.fire('Error', 'El nombre y apellido paterno del cliente son requeridos', 'error');
            return;
        }
        
        // Calcular total
        const subtotal = productos.reduce((sum, item) => sum + parseFloat(item.precio_total), 0);
        const envio = 50; // Costo fijo de envío
        const total = subtotal + envio;
        
        // Preparar datos para enviar
        const data = {
            id_documento: parseInt(quoteId),
            nombre: nombre,
            apellido1: apellido1,
            apellido2: $('#apellido2').val().trim(),
            direccion: $('#direccion').val().trim(),
            telefono: $('#telefono').val().trim(),
            celular: $('#celular').val().trim(),
            notas: $('#notas').val().trim(),
            terminos: $('#terminosEditor').trumbowyg('html'),
            detalle: productos,
            total: total,
            estado: approve ? 'APRO' : 'CLI'
        };
        
        // Mostrar carga
        $('#btnSave').prop('disabled', true);
        $('#btnApprove').prop('disabled', true);
        
        // Enviar al servidor
        $.ajax({
            url: '../controllers/cotizacion_controller.php',
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: approve ? 'Cotización Aprobada' : 'Cambios Guardados',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'quotes.php';
                        }
                    });
                } else {
                    Swal.fire('Error', response.error || 'Error al guardar la cotización', 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Error al conectar con el servidor: ' + error, 'error');
            },
            complete: function() {
                $('#btnSave').prop('disabled', false);
                $('#btnApprove').prop('disabled', false);
            }
        });
    }
    
    // Cargar datos iniciales
    loadQuoteData();
});
</script>

<style>
.main-title {
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #dee2e6;
}

.product-description {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.table tfoot td {
    font-weight: 600;
    background-color: #f8f9fa;
}

.trumbowyg-box {
    margin-top: 0;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

.trumbowyg-editor {
    min-height: 100px;
}
</style>