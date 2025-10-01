<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">

<?php
include('../layout/header.php');
?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title">Aprobación de Cargas Masivas</h4>
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
          <a href="#" class="f-s-14 f-w-500">Aprobación Cargas</a>
        </li>
      </ul>
    </div>
  </div>
  <!-- Breadcrumb end -->

  <!-- Filtros -->
  <div class="row mb-3">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Estado</label>
              <select class="form-select" id="filterEstado">
                <option value="">Todos</option>
                <option value="pendiente">Pendientes</option>
                <option value="aprobada">Aprobadas</option>
                <option value="rechazada">Rechazadas</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Fecha Desde</label>
              <input type="date" class="form-control" id="filterFechaDesde">
            </div>
            <div class="col-md-3">
              <label class="form-label">Fecha Hasta</label>
              <input type="date" class="form-control" id="filterFechaHasta">
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button class="btn btn-primary me-2" id="btnFiltrar">
                <i class="ti ti-filter me-2"></i>Filtrar
              </button>
              <button class="btn btn-outline-secondary" id="btnLimpiar">
                <i class="ti ti-refresh me-2"></i>Limpiar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabla de cargas -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="cargasTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Archivo</th>
                  <th>Usuario</th>
                  <th>Fecha Carga</th>
                  <th>Productos</th>
                  <th>Errores</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para detalles de carga -->
<div class="modal fade" id="cargaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalles de Carga - <span id="modalCargaId"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-4">
          <div class="col-md-6">
            <h6>Información General</h6>
            <table class="table table-sm">
              <tr>
                <td><strong>Archivo:</strong></td>
                <td id="modalArchivo"></td>
              </tr>
              <tr>
                <td><strong>Usuario:</strong></td>
                <td id="modalUsuario"></td>
              </tr>
              <tr>
                <td><strong>Fecha Carga:</strong></td>
                <td id="modalFecha"></td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <h6>Estadísticas</h6>
            <table class="table table-sm">
              <tr>
                <td><strong>Productos Correctos:</strong></td>
                <td><span class="badge bg-success" id="modalCorrectos">0</span></td>
              </tr>
              <tr>
                <td><strong>Productos con Error:</strong></td>
                <td><span class="badge bg-danger" id="modalErrores">0</span></td>
              </tr>
              <tr>
                <td><strong>Estado:</strong></td>
                <td id="modalEstado"></td>
              </tr>
            </table>
          </div>
        </div>
        
        <h6>Productos</h6>
        <div class="table-responsive">
          <table class="table table-bordered table-sm" id="modalProductosTable">
            <thead>
              <tr>
                <th>Estado</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Marca</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Errores</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        
        <!-- Sección de aprobación (solo para cargas pendientes) -->
        <div class="row mt-4" id="aprobacionSection">
          <div class="col-12">
            <div class="card border-primary">
              <div class="card-header bg-primary text-white">
                <h6 class="mb-0">Aprobación</h6>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-8">
                    <label class="form-label">Observaciones (opcional)</label>
                    <textarea class="form-control" id="observaciones" rows="3" placeholder="Ingrese observaciones si es necesario..."></textarea>
                  </div>
                  <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-success me-2" id="btnAprobar">
                      <i class="ti ti-check me-2"></i>Aprobar Carga
                    </button>
                    <button class="btn btn-danger" id="btnRechazar">
                      <i class="ti ti-x me-2"></i>Rechazar Carga
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
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

<script>
$(document).ready(function() {
    let table;
    let currentCargaId = null;
    
    // Inicializar DataTable
    function initTable() {
        table = $('#cargasTable').DataTable({
            ajax: {
                url: '../controllers/product_bulk_controller.php',
                dataSrc: ''
            },
            columns: [
                { data: 'id' },
                { data: 'nombre_archivo' },
                { data: 'usuario_nombre' },
                { 
                    data: 'fecha_carga',
                    render: function(data) {
                        return new Date(data).toLocaleString();
                    }
                },
                { 
                    data: 'productos_cargados',
                    render: function(data, type, row) {
                        return `<span class="badge bg-success">${data}</span>`;
                    }
                },
                { 
                    data: 'productos_error',
                    render: function(data, type, row) {
                        return data > 0 ? `<span class="badge bg-danger">${data}</span>` : '<span class="badge bg-secondary">0</span>';
                    }
                },
                { 
                    data: 'estado',
                    render: function(data) {
                        const estados = {
                            'pendiente': '<span class="badge bg-warning">Pendiente</span>',
                            'aprobada': '<span class="badge bg-success">Aprobada</span>',
                            'rechazada': '<span class="badge bg-danger">Rechazada</span>'
                        };
                        return estados[data] || data;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-outline-primary btn-ver" data-id="${row.id}">
                                <i class="ti ti-eye"></i>
                            </button>
                        `;
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            }
        });
    }
    
    initTable();
    
    // Filtrar
    $('#btnFiltrar').click(function() {
        const estado = $('#filterEstado').val();
        const fechaDesde = $('#filterFechaDesde').val();
        const fechaHasta = $('#filterFechaHasta').val();
        
        table.ajax.url(`../controllers/product_bulk_controller.php?estado=${estado}`).load();
    });
    
    $('#btnLimpiar').click(function() {
        $('#filterEstado').val('');
        $('#filterFechaDesde').val('');
        $('#filterFechaHasta').val('');
        table.ajax.url('../controllers/product_bulk_controller.php').load();
    });
    
    // Ver detalles de carga
    $(document).on('click', '.btn-ver', function() {
        currentCargaId = $(this).data('id');
        
        // Obtener información de la carga
        $.ajax({
            url: '../controllers/product_bulk_controller.php?carga_id=' + currentCargaId,
            type: 'GET',
            success: function(productos) {
                if (productos.length > 0) {
                    const carga = productos[0]; // La primera fila tiene info de la carga
                    
                    $('#modalCargaId').text('Carga #' + currentCargaId);
                    $('#modalArchivo').text(carga.nombre_archivo || 'N/A');
                    $('#modalUsuario').text(carga.usuario_nombre || 'N/A');
                    $('#modalFecha').text(new Date(carga.fecha_carga).toLocaleString());
                    
                    // Calcular estadísticas
                    const correctos = productos.filter(p => p.estado === 'correcto').length;
                    const errores = productos.filter(p => p.estado === 'error').length;
                    
                    $('#modalCorrectos').text(correctos);
                    $('#modalErrores').text(errores);
                    $('#modalEstado').html(carga.estado === 'pendiente' ? 
                        '<span class="badge bg-warning">Pendiente</span>' : 
                        carga.estado === 'aprobada' ? 
                        '<span class="badge bg-success">Aprobada</span>' : 
                        '<span class="badge bg-danger">Rechazada</span>');
                    
                    // Llenar tabla de productos
                    const tbody = $('#modalProductosTable tbody');
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
                    
                    // Mostrar/ocultar sección de aprobación
                    /*
                    if (carga.estado === 'pendiente') {
                        $('#aprobacionSection').removeClass('d-none');
                    } else {
                        $('#aprobacionSection').addClass('d-none');
                    }*/
                    
                    $('#cargaModal').modal('show');
                }
            }
        });
    });
    
    // Aprobar carga
    $('#btnAprobar').click(function() {
        if (!currentCargaId) return;
        
        Swal.fire({
            title: '¿Aprobar carga?',
            text: 'Los productos correctos serán agregados al sistema permanentemente',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, aprobar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const observaciones = $('#observaciones').val();
                
                $.ajax({
                    url: '../controllers/product_bulk_controller.php',
                    type: 'POST',
                    data: {
                        accion: 'aprobar',
                        carga_id: currentCargaId,
                        observaciones: observaciones
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: '¡Carga aprobada!',
                                text: `Se aprobaron ${response.productos_aprobados} productos correctamente`,
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                $('#cargaModal').modal('hide');
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire('Error', response.error || 'Error al aprobar la carga', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error de conexión', 'error');
                    }
                });
            }
        });
    });
    
    // Rechazar carga
    $('#btnRechazar').click(function() {
        if (!currentCargaId) return;
        
        Swal.fire({
            title: '¿Rechazar carga?',
            text: 'La carga será marcada como rechazada y no se agregarán productos',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, rechazar',
            cancelButtonText: 'Cancelar',
            input: 'textarea',
            inputLabel: 'Motivo del rechazo (opcional)',
            inputPlaceholder: 'Ingrese el motivo del rechazo...'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../controllers/product_bulk_controller.php',
                    type: 'POST',
                    data: {
                        accion: 'rechazar',
                        carga_id: currentCargaId,
                        observaciones: result.value || ''
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Carga rechazada',
                                text: 'La carga ha sido rechazada exitosamente',
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                $('#cargaModal').modal('hide');
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire('Error', response.error || 'Error al rechazar la carga', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error de conexión', 'error');
                    }
                });
            }
        });
    });
});
</script>