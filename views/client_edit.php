<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">
<?php include('../layout/header.php'); ?>

<style>
    .document-thumbnail {
        max-width: 100px;
        max-height: 100px;
        margin: 5px;
    }
    .document-item {
        border: 1px solid #ddd;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
    }
    .document-actions {
        margin-top: 5px;
    }
    .rubro-option {
        padding: 5px;
    }
</style>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12">
      <button type="button" class="btn btn-secondary m-1 float-end" onclick="window.history.back()">
        <i class="ti ti-arrow-left"></i> Volver
      </button>
      <h4 class="main-title"><?= isset($_GET['id']) ? 'Editar' : 'Nuevo' ?> Cliente</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="client.php" class="f-s-14 f-w-500">
            <span><i class="ph-duotone ph-table f-s-16"></i> Clientes</span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500"><?= isset($_GET['id']) ? 'Editar' : 'Nuevo' ?></a>
        </li>
      </ul>
    </div>
  </div>
  <!-- Breadcrumb end -->

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <form id="formClient">
            <input type="hidden" id="id_cliente" name="id_cliente" value="0">
            
            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <label class="form-label">NOMBRE<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nombre" name="nombre" maxlength="20" required>
              </div>
              
              <div class="col-md-4">
                <label class="form-label">APELLIDO PATERNO<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="apellido1" name="apellido1" maxlength="20" required>
              </div>
              
              <div class="col-md-4">
                <label class="form-label">APELLIDO MATERNO</label>
                <input type="text" class="form-control" id="apellido2" name="apellido2" maxlength="20">
              </div>
            </div>
            
            <div class="row g-3 mb-4">
              <div class="col-md-3">
                <label class="form-label">TELÉFONO<span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="cel1" name="cel1" maxlength="11" min="1" required>
              </div>
              
              <div class="col-md-3">
                <label class="form-label">CELULAR<span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="cel2" name="cel2" maxlength="11" min="1" required>
              </div>
              
              <div class="col-md-6">
                <label class="form-label">EMAIL<span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" maxlength="300" required>
              </div>
            </div>
            
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label">DIRECCIÓN<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="direccion" name="direccion" maxlength="300" required>
              </div>
              
              <div class="col-md-3">
                <label class="form-label">NIT<span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="nit" name="nit" maxlength="20" min="1" required>
              </div>
              
              <div class="col-md-3">
                <label class="form-label">ESTADO<span class="text-danger">*</span></label>
                <select class="form-control" id="estado" name="estado" required>
                  <option value="V">HABILITADO</option>
                  <option value="D">DESHABILITADO</option>
                </select>
              </div>
            </div>
            
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label">RAZÓN SOCIAL<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="razon_social" name="razon_social" maxlength="100" required>
              </div>
              
              <div class="col-md-6">
                <label class="form-label">CARGO EN LA EMPRESA</label>
                <input type="text" class="form-control" id="cargo" name="cargo" maxlength="100">
              </div>
            </div>
            
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label">AVALADO POR</label>
                <input type="text" class="form-control" id="avalado_por" name="avalado_por" maxlength="100">
              </div>
              
              <div class="col-md-6">
                <label class="form-label">PÁGINA WEB</label>
                <input type="url" class="form-control" id="web" name="web" maxlength="255">
              </div>
            </div>
            
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label">RUBRO DE LA EMPRESA</label>
                <select class="form-control" id="rubro" name="rubro">
                  <option value="">Seleccione un rubro</option>
                  <option value="Medicina">Medicina</option>
                  <option value="Construcción">Construcción</option>
                  <option value="Ingeniería">Ingeniería</option>
                  <option value="Tecnología">Tecnología</option>
                  <option value="Educación">Educación</option>
                  <option value="Comercio">Comercio</option>
                  <option value="Otros">Otros</option>
                </select>
              </div>
              
              <div class="col-md-6">
                <label class="form-label">FECHA DE REGISTRO</label>
                <input type="date" class="form-control" id="fecha_registro" name="fecha_registro" readonly>
              </div>
            </div>
            
            <div class="row g-3 mb-4" id="usuario-info" style="display: none;">
              <div class="col-md-12">
                <div class="alert alert-info">
                  <h5>Información de Acceso</h5>
                  <p><strong>Usuario:</strong> <span id="info-usuario"></span></p>
                  <p><strong>Contraseña:</strong> <span id="info-password"></span></p>
                  <p class="text-danger">Guarde esta información en un lugar seguro.</p>
                </div>
              </div>
            </div>
            
            <hr>
            
            <div class="row g-3 mb-4">
              <div class="col-md-12">
                <h5>Documentos del Cliente</h5>
                <div id="documentos-container">
                  <p>No hay documentos cargados.</p>
                </div>
                
                <div class="mt-3">
                  <label class="form-label">Subir Documento</label>
                  <div class="input-group">
                    <select class="form-control" id="tipo_documento">
                      <option value="Carnet">Fotocopia de Carnet</option>
                      <option value="NIT">Certificado de NIT</option>
                      <option value="Contrato">Contrato</option>
                      <option value="Otro">Otro Documento</option>
                    </select>
                    <input type="file" class="form-control" id="documento" accept=".pdf,.jpg,.jpeg,.png">
                    <button type="button" class="btn btn-primary" id="btn-subir-documento">
                      <i class="ti ti-upload"></i> Subir
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row g-3">
              <div class="col-md-12 text-end">
                <button type="submit" class="btn btn-primary waves-effect" id="btn_submit">
                  <i class="ti ti-check"></i> Guardar
                </button>
                <button type="button" class="btn btn-secondary waves-effect" onclick="window.history.back()">
                  <i class="ti ti-x"></i> Cancelar
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>

<!-- data table jquery-->
<script src="../assets/vendor/datatable/jquery.dataTables.min.js"></script>
<script src="../assets/js/jquery.validate.min.js"></script>
<script src="../assets/js/jquery-key-restrictions.min.js"></script>

<!-- js-->
<script>
$(function($){
    // Cargar datos si estamos editando
    const urlParams = new URLSearchParams(window.location.search);
    const clienteId = urlParams.get('id');
    
    if (clienteId) {
        cargarCliente(clienteId);
        cargarDocumentos(clienteId);
    } else {
        // Establecer fecha actual para nuevos registros
        const today = new Date().toISOString().split('T')[0];
        $('#fecha_registro').val(today);
    }
    
    // Configurar validación del formulario
    $("#formClient").validate({
        rules: {
            nombre: "required",
            apellido1: "required",
            cel1: {
                required: true,
                minlength: 7
            },
            cel2: {
                required: true,
                minlength: 7
            },
            email: {
                required: true,
                email: true
            },
            direccion: "required",
            nit: {
                required: true,
                minlength: 6
            },
            razon_social: "required"
        },
        messages: {
            nombre: "Por favor ingrese el nombre",
            apellido1: "Por favor ingrese el apellido paterno",
            cel1: {
                required: "Por favor ingrese el teléfono",
                minlength: "El teléfono debe tener al menos 7 dígitos"
            },
            cel2: {
                required: "Por favor ingrese el celular",
                minlength: "El celular debe tener al menos 7 dígitos"
            },
            email: {
                required: "Por favor ingrese el email",
                email: "Por favor ingrese un email válido"
            },
            direccion: "Por favor ingrese la dirección",
            nit: {
                required: "Por favor ingrese el NIT",
                minlength: "El NIT debe tener al menos 6 dígitos"
            },
            razon_social: "Por favor ingrese la razón social"
        },
        submitHandler: function(form) {
            guardarCliente();
        }
    });
    
    // Configurar subida de documentos
    $("#btn-subir-documento").click(function() {
        subirDocumento();
    });
    
    // Restricciones de teclado
    $("#nombre").lettersOnly();
    $("#apellido1").lettersOnly();
    $("#apellido2").lettersOnly();
});

function cargarCliente(id) {
    $.ajax({
        url: '../controllers/client_controller.php?id=' + id,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data) {
                $('#id_cliente').val(data.id);
                $('#nombre').val(data.nombre);
                $('#apellido1').val(data.apellido1);
                $('#apellido2').val(data.apellido2 || '');
                $('#cel1').val(data.cel1);
                $('#cel2').val(data.cel2);
                $('#direccion').val(data.direccion);
                $('#email').val(data.email);
                $('#nit').val(data.nit);
                $('#razon_social').val(data.razon_social);
                $('#estado').val(data.estado);
                $('#cargo').val(data.cargo || '');
                $('#avalado_por').val(data.avalado_por || '');
                $('#rubro').val(data.rubro || '');
                $('#web').val(data.web || '');
                $('#fecha_registro').val(data.fecha_registro);
                
                if (data.usuario) {
                    $('#info-usuario').text(data.usuario);
                    $('#info-password').text('******** (oculto por seguridad)');
                    $('#usuario-info').show();
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            Swal.fire('Error', 'No se pudo cargar el cliente: ' + errorThrown, 'error');
        }
    });
}

function cargarDocumentos(idCliente) {
    $.ajax({
        url: '../controllers/document_controller.php?id_cliente=' + idCliente,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            const container = $('#documentos-container');
            container.empty();
            
            if (data.length === 0) {
                container.html('<p>No hay documentos cargados.</p>');
                return;
            }
            
            $.each(data, function(index, doc) {
                const isImage = doc.nombre_archivo.match(/\.(jpg|jpeg|png|gif)$/i);
                const preview = isImage ? 
                    `<img src="${doc.ruta_archivo}" class="document-thumbnail" alt="${doc.nombre_archivo}">` :
                    `<i class="ti ti-file-text" style="font-size: 50px;"></i>`;
                
                const docItem = $(`
                    <div class="document-item">
                        <div class="row">
                            <div class="col-md-2 text-center">
                                ${preview}
                            </div>
                            <div class="col-md-8">
                                <h6>${doc.tipo_documento}</h6>
                                <p>${doc.nombre_archivo}</p>
                                <small class="text-muted">Subido el: ${new Date(doc.fecha_subida).toLocaleString()}</small>
                            </div>
                            <div class="col-md-2 text-end">
                                <div class="document-actions">
                                    <a href="${doc.ruta_archivo}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarDocumento(${doc.id})">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                
                container.append(docItem);
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error al cargar documentos:', errorThrown);
        }
    });
}

function subirDocumento() {
    const idCliente = $('#id_cliente').val();
    const tipoDocumento = $('#tipo_documento').val();
    const fileInput = $('#documento')[0];
    
    if (!fileInput.files || fileInput.files.length === 0) {
        Swal.fire('Advertencia', 'Por favor seleccione un archivo', 'warning');
        return;
    }
    
    if (idCliente === '0') {
        Swal.fire('Advertencia', 'Debe guardar el cliente primero antes de subir documentos', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('documento', fileInput.files[0]);
    formData.append('id_cliente', idCliente);
    formData.append('tipo_documento', tipoDocumento);
    
    $.ajax({
        url: '../controllers/document_controller.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire('Éxito', 'Documento subido correctamente', 'success');
                cargarDocumentos(idCliente);
                $('#documento').val('');
            } else {
                Swal.fire('Error', response.error || 'Error al subir el documento', 'error');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            Swal.fire('Error', 'Error al subir el documento: ' + errorThrown, 'error');
        }
    });
}

function eliminarDocumento(idDocumento) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../controllers/document_controller.php?id=' + idDocumento,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Éxito', 'Documento eliminado correctamente', 'success');
                        cargarDocumentos($('#id_cliente').val());
                    } else {
                        Swal.fire('Error', 'No se pudo eliminar el documento', 'error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire('Error', 'Error al eliminar el documento: ' + errorThrown, 'error');
                }
            });
        }
    });
}

function guardarCliente() {
    $('#btn_submit').prop('disabled', true);
    
    const id = $('#id_cliente').val();
    const method = id === '0' ? 'POST' : 'PUT';
    const url = '../controllers/client_controller.php';
    
    const data = {
        id: id,
        nombre: $('#nombre').val(),
        apellido1: $('#apellido1').val(),
        apellido2: $('#apellido2').val(),
        cel1: $('#cel1').val(),
        cel2: $('#cel2').val(),
        direccion: $('#direccion').val(),
        email: $('#email').val(),
        nit: $('#nit').val(),
        razon_social: $('#razon_social').val(),
        estado: $('#estado').val(),
        cargo: $('#cargo').val(),
        avalado_por: $('#avalado_por').val(),
        rubro: $('#rubro').val(),
        web: $('#web').val()
    };
    
    $.ajax({
        url: url,
        type: method,
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(response) {
            if (response.success) {
                if (method === 'POST') {
                    // Mostrar información de usuario creado
                    $('#info-usuario').text(response.data.username);
                    $('#info-password').text(response.data.password);
                    $('#usuario-info').show();
                    
                    // Actualizar el ID del cliente para poder subir documentos
                    $('#id_cliente').val(response.data.id);
                    
                    Swal.fire({
                        title: 'Cliente creado',
                        html: `Usuario creado:<br><strong>Usuario:</strong> ${response.data.username}<br><strong>Contraseña:</strong> ${response.data.password}`,
                        icon: 'success'
                    });
                } else {
                    Swal.fire('Éxito', 'Cliente actualizado correctamente', 'success');
                }
            } else {
                Swal.fire('Error', 'No se pudo guardar el cliente', 'error');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            Swal.fire('Error', 'Error al guardar el cliente: ' + errorThrown, 'error');
        },
        complete: function() {
            $('#btn_submit').prop('disabled', false);
        }
    });
}
</script>