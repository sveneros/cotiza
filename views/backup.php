<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">
<?php include('../layout/header.php'); ?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title">Backup de Base de Datos</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="#" class="f-s-14 f-w-500">
            <span><i class="ph-duotone ph-database f-s-16"></i> Sistema</span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Backup</a>
        </li>
      </ul>
    </div>
  </div>
  <!-- Breadcrumb end -->

  <div class="row">
    <!-- Exportar Backup -->
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="fas fa-download me-2"></i>Exportar Backup</h5>
        </div>
        <div class="card-body">
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Genera un respaldo completo de la base de datos en formato SQL comprimido (GZIP).
          </div>
          
         <div class="mb-3">
            <label class="form-label">Nombre del archivo:</label>
            <div class="input-group">
                <span class="input-group-text">Nombre:</span>
                <input type="text" class="form-control" id="backup_name" placeholder="sistema" value="bkp_iknow">
            </div>
            <small class="text-muted">Se agregará automáticamente: bkp_[nombre]_fecha_hora.sql.gz</small>
        </div>
          
          <div class="mb-3">
            <label class="form-label">Opciones de exportación:</label>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="include_drop" checked>
              <label class="form-check-label" for="include_drop">
                Incluir sentencias DROP TABLE
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="include_data" checked>
              <label class="form-check-label" for="include_data">
                Incluir datos de las tablas
              </label>
            </div>
          </div>
          
          <button type="button" class="btn btn-success w-100" id="btn_export">
            <i class="fas fa-file-export me-2"></i>Generar Backup
          </button>
          
          <div id="export_progress" class="mt-3 d-none">
            <div class="progress">
              <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
            </div>
            <p class="text-center mt-2 mb-0">Generando backup, por favor espere...</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Importar Backup -->
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-warning text-dark">
          <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Importar Backup</h5>
        </div>
        <div class="card-body">
          <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>¡Precaución!</strong> La importación sobrescribirá los datos existentes.
          </div>
          
          <div class="mb-3">
            <label class="form-label">Seleccionar archivo:</label>
            <input type="file" class="form-control" id="backup_file" accept=".sql,.gz,.gzip">
            <small class="text-muted">Formatos aceptados: SQL, GZ, GZIP</small>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Modo de importación:</label>
            <select class="form-select" id="import_mode">
              <option value="normal">Normal (con reemplazo)</option>
              <option value="safe">Modo Seguro (sin borrar datos existentes)</option>
            </select>
          </div>
          
          <button type="button" class="btn btn-primary w-100" id="btn_import">
            <i class="fas fa-file-import me-2"></i>Importar Backup
          </button>
          
          <div id="import_progress" class="mt-3 d-none">
            <div class="progress">
              <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
            </div>
            <p class="text-center mt-2 mb-0">Importando backup, por favor espere...</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Historial de Backups -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header bg-secondary text-white">
          <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial de Backups</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover" id="backup_table">
              <thead>
                <tr>
                  <th>Nombre del Archivo</th>
                  <th>Tamaño</th>
                  <th>Fecha de Creación</th>
                  <th>Formato</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="backup_list">
                <!-- Los backups se cargarán aquí -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>

<!-- SweetAlert2 -->
<script src="../assets/vendor/sweetalert/sweetalert.js"></script>

<script>
$(document).ready(function() {
  // Cargar lista de backups
  loadBackupList();

  // Exportar Backup
  $('#btn_export').click(function() {
    const backupName = $('#backup_name').val().trim() || 'backup';
    const includeDrop = $('#include_drop').is(':checked');
    const includeData = $('#include_data').is(':checked');
    
    $('#export_progress').removeClass('d-none');
    $('#btn_export').prop('disabled', true);
    
    $.ajax({
      url: '../controllers/backup_controller.php',
      type: 'POST',
      data: {
        action: 'export',
        name: backupName,
        include_drop: includeDrop,
        include_data: includeData
      },
      xhr: function() {
        const xhr = new window.XMLHttpRequest();
        xhr.upload.addEventListener("progress", function(evt) {
          if (evt.lengthComputable) {
            const percentComplete = evt.loaded / evt.total * 100;
            $('#export_progress .progress-bar').css('width', percentComplete + '%');
          }
        }, false);
        return xhr;
      },
      success: function(response) {
        $('#export_progress').addClass('d-none');
        $('#btn_export').prop('disabled', false);
        
        if (response.success) {
          Swal.fire({
            icon: 'success',
            title: '¡Backup Generado!',
            html: `Backup creado exitosamente:<br>
                   <strong>${response.filename}</strong><br>
                   <small>Tamaño: ${response.size}</small>`,
            showCancelButton: true,
            confirmButtonText: 'Descargar',
            cancelButtonText: 'Cerrar'
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = response.download_url;
            }
          });
          loadBackupList();
        } else {
          Swal.fire('Error', response.message, 'error');
        }
      },
      error: function(xhr, status, error) {
        $('#export_progress').addClass('d-none');
        $('#btn_export').prop('disabled', false);
        Swal.fire('Error', 'Error al generar backup: ' + error, 'error');
      }
    });
  });

  // Importar Backup
  $('#btn_import').click(function() {
    const fileInput = $('#backup_file')[0];
    const importMode = $('#import_mode').val();
    
    if (!fileInput.files.length) {
      Swal.fire('Error', 'Por favor selecciona un archivo', 'warning');
      return;
    }
    
    Swal.fire({
      title: '¿Estás seguro?',
      text: 'Esta acción sobrescribirá los datos existentes en la base de datos.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, importar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        importBackup(fileInput, importMode);
      }
    });
  });

  function importBackup(fileInput, mode) {
    const formData = new FormData();
    formData.append('action', 'import');
    formData.append('mode', mode);
    formData.append('backup_file', fileInput.files[0]);
    
    $('#import_progress').removeClass('d-none');
    $('#btn_import').prop('disabled', true);
    
    $.ajax({
      url: '../controllers/backup_controller.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      xhr: function() {
        const xhr = new window.XMLHttpRequest();
        xhr.upload.addEventListener("progress", function(evt) {
          if (evt.lengthComputable) {
            const percentComplete = evt.loaded / evt.total * 100;
            $('#import_progress .progress-bar').css('width', percentComplete + '%');
          }
        }, false);
        return xhr;
      },
      success: function(response) {
        $('#import_progress').addClass('d-none');
        $('#btn_import').prop('disabled', false);
        
        if (response.success) {
          Swal.fire({
            icon: 'success',
            title: '¡Importación Exitosa!',
            text: response.message,
            timer: 3000,
            showConfirmButton: false
          }).then(() => {
            // Recargar la página para reflejar cambios
            window.location.reload();
          });
        } else {
          Swal.fire('Error', response.message, 'error');
        }
      },
      error: function(xhr, status, error) {
        $('#import_progress').addClass('d-none');
        $('#btn_import').prop('disabled', false);
        Swal.fire('Error', 'Error al importar backup: ' + error, 'error');
      }
    });
  }

  // Cargar lista de backups
  function loadBackupList() {
    $.ajax({
      url: '../controllers/backup_controller.php?action=list',
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          renderBackupList(response.backups);
        }
      },
      error: function() {
        $('#backup_list').html('<tr><td colspan="5" class="text-center">Error al cargar la lista de backups</td></tr>');
      }
    });
  }

  function renderBackupList(backups) {
    let html = '';
    
    if (backups.length === 0) {
      html = '<tr><td colspan="5" class="text-center">No hay backups disponibles</td></tr>';
    } else {
      backups.forEach(backup => {
        html += `
          <tr>
            <td>${backup.name}</td>
            <td>${backup.size}</td>
            <td>${backup.date}</td>
            <td>${backup.type}</td>
            <td>
              <div class="btn-group btn-group-sm">
                <button class="btn btn-primary" onclick="downloadBackup('${backup.filename}')">
                  <i class="fas fa-download"></i>
                </button>
                <button class="btn btn-danger" onclick="deleteBackup('${backup.filename}')">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
    }
    
    $('#backup_list').html(html);
  }
});

// Función para descargar backup
function downloadBackup(filename) {
  window.location.href = `../controllers/backup_controller.php?action=download&file=${encodeURIComponent(filename)}`;
}

// Función para eliminar backup
function deleteBackup(filename) {
  Swal.fire({
    title: '¿Eliminar backup?',
    text: `¿Estás seguro de eliminar "${filename}"?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: '../controllers/backup_controller.php',
        type: 'POST',
        data: {
          action: 'delete',
          filename: filename
        },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            Swal.fire('Eliminado!', response.message, 'success');
            loadBackupList();
          } else {
            Swal.fire('Error', response.message, 'error');
          }
        }
      });
    }
  });
}
</script>