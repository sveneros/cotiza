<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">
<!-- Dropzone CSS -->
<link rel="stylesheet" href="../assets/css/dropzone.min.css">

<?php
include('../layout/header.php');
?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title">Carga Masiva de Productos</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="#" class="f-s-14 f-w-500">
            <span>
              <i class="ph-duotone ph-table f-s-16"></i> Operaciones
            </span>
          </a>
        </li>
        <li>
          <a href="product_list.php" class="f-s-14 f-w-500">Productos</a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Carga Masiva</a>
        </li>
      </ul>
    </div>
  </div>
  <!-- Breadcrumb end -->

  <!-- Upload Section -->
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Subir Archivo Excel</h5>
          <div class="upload-area p-5 text-center border rounded" id="dropzone">
            <i class="ti ti-file-spreadsheet fs-1 text-primary mb-3"></i>
            <h5>Arrastra tu archivo Excel aquí</h5>
            <p class="text-muted">o haz clic para seleccionar</p>
            <small class="text-muted d-block mb-3">Formatos soportados: .xlsx, .xls</small>
            
            <form id="uploadForm" enctype="multipart/form-data">
              <input type="file" id="excelFile" name="archivo" accept=".xlsx,.xls" style="display: none;">
              <button type="button" class="btn btn-primary" id="selectFileBtn">
                <i class="ti ti-upload me-2"></i>Seleccionar Archivo
              </button>
            </form>
          </div>
          
          <div class="mt-4">
            <h6>Plantilla Requerida:</h6>
            <div class="table-responsive">
              <table class="table table-bordered table-sm">
                <thead class="table-light">
                  <tr>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Descripcion</th>
                    <th>Marca</th>
                    <th>Categoria</th>
                    <th>Precio</th>
                    <th>Stock Actual</th>
                    <th>Stock Minimo</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>PROD-001</td>
                    <td>Nombre del Producto</td>
                    <td>Descripción detallada</td>
                    <td>Nombre de Marca</td>
                    <td>Nombre de Categoría</td>
                    <td>150.00</td>
                    <td>50</td>
                    <td>5</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="mt-2">
              <a href="../templates/plantilla_productos.xlsx" class="btn btn-outline-primary btn-sm">
                <i class="ti ti-download me-1"></i>Descargar Plantilla
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Instrucciones</h5>
          <div class="alert alert-info">
            <h6><i class="ti ti-info-circle me-2"></i>Información Importante</h6>
            <ul class="mb-0 ps-3">
              <li>El archivo debe estar en formato Excel (.xlsx o .xls)</li>
              <li>La primera fila debe contener los encabezados exactos</li>
              <li>Los productos serán validados antes de ser guardados</li>
              <li>Marcas y categorías nuevas se crearán automáticamente</li>
              <li>La carga requiere aprobación del administrador</li>
            </ul>
          </div>
          
          <h6 class="mt-4">Validaciones:</h6>
          <ul class="list-unstyled">
            <li><i class="ti ti-check text-success me-2"></i>Código único y requerido</li>
            <li><i class="ti ti-check text-success me-2"></i>Nombre requerido</li>
            <li><i class="ti ti-check text-success me-2"></i>Marca y categoría requeridas</li>
            <li><i class="ti ti-check text-success me-2"></i>Precio mayor a 0</li>
            <li><i class="ti ti-check text-success me-2"></i>Stock no negativo</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Progress Section -->
  <div class="row mt-4 d-none" id="progressSection">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Procesando Archivo</h5>
          <div class="progress mb-3">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
          </div>
          <div id="progressMessage" class="text-center"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Results Section -->
  <div class="row mt-4 d-none" id="resultsSection">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Resultados de la Carga</h5>
          <div class="row">
            <div class="col-md-3">
              <div class="card bg-success text-white">
                <div class="card-body text-center">
                  <h3 id="correctCount">0</h3>
                  <p>Productos Correctos</p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-danger text-white">
                <div class="card-body text-center">
                  <h3 id="errorCount">0</h3>
                  <p>Productos con Error</p>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card bg-primary text-white">
                <div class="card-body text-center">
                  <h3 id="totalCount">0</h3>
                  <p>Total de Productos</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="mt-4">
            <button id="viewDetailsBtn" class="btn btn-outline-primary">
              <i class="ti ti-eye me-2"></i>Ver Detalles
            </button>
            <button id="confirmUploadBtn" class="btn btn-success ms-2">
              <i class="ti ti-check me-2"></i>Confirmar Carga
            </button>
            <button id="newUploadBtn" class="btn btn-outline-secondary ms-2">
              <i class="ti ti-upload me-2"></i>Nueva Carga
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para detalles -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalles de la Carga</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="detailsTable">
            <thead>
              <tr>
                <th>Estado</th>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Marca</th>
                <th>Categoria</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Errores</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include('../layout/footer.php');
?>

<!-- DataTables -->
<script src="../assets/vendor/datatable/jquery.dataTables.min.js"></script>
<!-- Dropzone -->
<script src="../assets/js/dropzone.min.js"></script>

<script>
$(document).ready(function() {
    let currentCargaId = null;
    
    // Seleccionar archivo
    $('#selectFileBtn').click(function() {
        $('#excelFile').click();
    });
    
    $('#excelFile').change(function(e) {
        if (this.files.length > 0) {
            procesarArchivo(this.files[0]);
        }
    });
    
    // Drag and drop
    const dropzone = $('#dropzone')[0];
    
    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('border-primary bg-light');
    });
    
    dropzone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('border-primary bg-light');
    });
    
    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('border-primary bg-light');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            procesarArchivo(files[0]);
        }
    });
    
    function procesarArchivo(file) {
        // Validar tipo de archivo
        const validTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        if (!validTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls)$/)) {
            Swal.fire('Error', 'Por favor selecciona un archivo Excel válido (.xlsx o .xls)', 'error');
            return;
        }
        
        // Mostrar progreso
        $('#progressSection').removeClass('d-none');
        $('#progressMessage').html('<i class="ti ti-loader me-2"></i>Procesando archivo...');
        
        const formData = new FormData();
        formData.append('archivo', file);
        
        $.ajax({
            url: '../controllers/product_bulk_controller.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#progressSection').addClass('d-none');
                
                if (response.success) {
                    currentCargaId = response.carga_id;
                    mostrarResultados(response);
                } else {
                    Swal.fire('Error', response.error || 'Error al procesar el archivo', 'error');
                }
            },
            error: function() {
                $('#progressSection').addClass('d-none');
                Swal.fire('Error', 'Error de conexión al procesar el archivo', 'error');
            }
        });
    }
    
    function mostrarResultados(data) {
        $('#correctCount').text(data.productos_correctos);
        $('#errorCount').text(data.productos_error);
        $('#totalCount').text(data.productos_correctos + data.productos_error);
        $('#resultsSection').removeClass('d-none');
        
        // Scroll a resultados
        $('html, body').animate({
            scrollTop: $('#resultsSection').offset().top - 100
        }, 500);
    }
    
    // Ver detalles
    $('#viewDetailsBtn').click(function() {
        if (!currentCargaId) return;
        
        $.ajax({
            url: '../controllers/product_bulk_controller.php?carga_id=' + currentCargaId,
            type: 'GET',
            success: function(productos) {
                const tbody = $('#detailsTable tbody');
                tbody.empty();
                
                productos.forEach(function(producto) {
                    const estadoBadge = producto.estado === 'correcto' ? 
                        '<span class="badge bg-success">Correcto</span>' : 
                        '<span class="badge bg-danger">Error</span>';
                    
                    const errores = producto.errores ? 
                        `<small class="text-danger">${producto.errores}</small>` : 
                        '<small class="text-success">Sin errores</small>';
                    
                    tbody.append(`
                        <tr>
                            <td>${estadoBadge}</td>
                            <td>${producto.codigo}</td>
                            <td>${producto.nombre}</td>
                            <td>${producto.marca_nombre}</td>
                            <td>${producto.categoria_nombre}</td>
                            <td>Bs. ${parseFloat(producto.puntos).toFixed(2)}</td>
                            <td>${producto.stock_actual}</td>
                            <td>${errores}</td>
                        </tr>
                    `);
                });
                
                $('#detailsModal').modal('show');
            }
        });
    });
    
    // Confirmar carga
    $('#confirmUploadBtn').click(function() {
        if (!currentCargaId) return;
        
        Swal.fire({
            title: '¿Confirmar carga?',
            text: 'Los productos correctos serán enviados para aprobación del administrador',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Carga confirmada',
                    text: 'Los productos han sido guardados temporalmente y están pendientes de aprobación',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    window.location.href = 'product_bulk_approval.php';
                });
            }
        });
    });
    
    // Nueva carga
    $('#newUploadBtn').click(function() {
        $('#resultsSection').addClass('d-none');
        $('#excelFile').val('');
        currentCargaId = null;
    });
});
</script>