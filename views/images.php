<link rel="stylesheet" href="../assets/css/jquery.fileupload.min.css">
<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">

<?php include('../layout/header.php'); ?>

<div class="container-fluid">
    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Gestor Media</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a href="#"><i class="ph-duotone ph-image"></i> Media</a></li>
                <li class="active">Gestor Media</li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Subir Nueva Imagen</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Tipo de Entidad</label>
                        <select class="form-control" id="selectEntidad">
                            <option value="">Seleccionar...</option>
                            <option value="producto">Producto</option>
                            <option value="usuario">Usuario</option>
                            <option value="categoria">Categoría</option>
                            <option value="marca">Marca</option>
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label>ID de Entidad</label>
                        <input type="number" class="form-control" id="inputEntidadId" placeholder="Ej: 123">
                    </div>
                    
                    <!-- Área de subida de archivos -->
                    <div class="upload-area mt-3 p-3 border rounded text-center" id="dropzone">
                        <span class="fs-1 text-primary"><i class="ti ti-cloud-upload"></i></span>
                        <h5>Arrastra archivos aquí</h5>
                        <p class="text-muted">o haz clic para seleccionar</p>
                        <input id="fileupload" type="file" name="files[]" multiple 
                               accept="image/jpeg,image/png,image/gif" 
                               style="display: none;">
                        <button class="btn btn-primary mt-2" id="triggerUpload">Seleccionar Archivos</button>
                    </div>
                    
                    <!-- Previsualización -->
                    <div id="files" class="files mt-3"></div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Imágenes Subidas</h5>
                    <div class="d-flex">
                        <div class="input-group ms-2" style="width: 200px;">
                            <select class="form-control" id="filterType">
                                <option value="">Todos los tipos</option>
                                <option value="producto">Productos</option>
                                <option value="usuario">Usuarios</option>
                                <option value="categoria">Categorías</option>
                                <option value="marca">Marcas</option>
                            </select>
                        </div>
                        <div class="input-group ms-2" style="width: 200px;">
                            <input type="text" class="form-control" id="searchImages" placeholder="Buscar...">
                            <button class="btn btn-primary"><i class="ti ti-search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="imagesTable">
                            <thead>
                                <tr>
                                    <th>Miniatura</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>ID Entidad</th>
                                    <th>Tamaño</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="spinner-border text-primary"></div>
                                        <p class="text-muted mt-2">Cargando imágenes...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php'); ?>

<script src="../assets/js/jquery.ui.widget.js"></script>
<script src="../assets/js/jquery.iframe-transport.js"></script>
<script src="../assets/js/jquery.fileupload.js"></script>

<script src="../assets/vendor/datatable/jquery.dataTables.min.js"></script>


<script>
$(document).ready(function() {
    let allImages = [];
    let dataTable;
    
    // Inicializar la página
    loadAllImages();
    
    // Configurar DataTable
    function initDataTable() {
        dataTable = $('#imagesTable').DataTable({
            dom: 'rtip',
            paging: true,
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            }
        });
    }
    
    // Cargar todas las imágenes al inicio
    function loadAllImages() {
        $.ajax({
            url: '../controllers/image_controller.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                allImages = data;
                renderTable(data);
                initDataTable();
            },
            error: function() {
                $('#tableBody').html(`
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="ti ti-photo-off fs-1 text-muted"></i>
                            <p class="text-muted">Error al cargar las imágenes</p>
                        </td>
                    </tr>
                `);
            }
        });
    }
    
    // Renderizar la tabla
    function renderTable(images) {
        let html = '';
        
        if (images.length > 0) {
            images.forEach(function(imagen) {
                html += `
                    <tr>
                        <td>
                            <img src="../${imagen.ruta}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                        </td>
                        <td>${imagen.nombre_archivo}</td>
                        <td>
                            <span class="badge bg-primary">
                                ${capitalizeFirstLetter(imagen.entidad_tipo)}
                            </span>
                        </td>
                        <td>${imagen.entidad_id}</td>
                        <td>${formatBytes(imagen.tamano)}</td>
                        <td>${new Date(imagen.fecha_subida).toLocaleString()}</td>
                        <td>
                            <button class="btn btn-sm btn-danger delete-image" data-id="${imagen.id}">
                                <i class="ti ti-trash"></i>
                            </button>
                            <a href="../${imagen.ruta}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="ti ti-external-link"></i>
                            </a>
                        </td>
                    </tr>
                `;
            });
        } else {
            html = `
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="ti ti-photo-off fs-1 text-muted"></i>
                        <p class="text-muted">No hay imágenes para mostrar</p>
                    </td>
                </tr>
            `;
        }
        
        $('#tableBody').html(html);
    }
    
    // Configuración de jQuery File Upload
    $('#fileupload').fileupload({
        url: '../controllers/image_controller.php',
        dataType: 'json',
        autoUpload: true,
        formData: function() {
            return [
                { name: 'entidad_tipo', value: $('#selectEntidad').val() },
                { name: 'entidad_id', value: $('#inputEntidadId').val() }
            ];
        },
        add: function(e, data) {
            // Validar selección de entidad
            if (!$('#selectEntidad').val() || !$('#inputEntidadId').val()) {
                showNotification('error', 'Debes seleccionar un tipo de entidad y su ID');
                return false;
            }
            
            // Validar tipos de archivo
            var isValid = true;
            $.each(data.files, function(index, file) {
                if (!file.type.match('image.*')) {
                    isValid = false;
                    return false;
                }
            });
            
            if (!isValid) {
                showNotification('error', 'Solo se permiten imágenes (JPEG, PNG, GIF)');
                return false;
            }
            
            // Mostrar previsualización
            $.each(data.files, function(index, file) {
                var previewHtml = `
                    <div class="file-preview mb-2 p-2 border rounded" data-file="${file.name}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${file.name}</strong> (${formatFileSize(file.size)})
                            </div>
                            <div class="spinner-border spinner-border-sm text-primary" role="status" style="display:none;">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 5px; display: none;">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                `;
                $('#files').append(previewHtml);
            });
            
            data.submit();
        },
        progress: function(e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            var filePreview = $(`[data-file="${data.files[0].name}"]`);
            
            filePreview.find('.progress').show();
            filePreview.find('.progress-bar').css('width', progress + '%');
            
            if (progress === 100) {
                filePreview.find('.spinner-border').show();
                filePreview.find('.progress').hide();
            }
        },
        done: function(e, data) {
            var filePreview = $(`[data-file="${data.files[0].name}"]`);
            
            if (data.result.success) {
                filePreview.html(`
                    <div class="text-success">
                        <i class="ti ti-check"></i> ${data.result.nombre} subido correctamente
                    </div>
                `).addClass('bg-light-success');
                
                showNotification('success', 'Imagen subida correctamente');
                
                // Actualizar listado
                if (dataTable) {
                    dataTable.destroy();
                }
                loadAllImages();
            } else {
                filePreview.html(`
                    <div class="text-danger">
                        <i class="ti ti-x"></i> Error: ${data.result.error || 'Error desconocido'}
                    </div>
                `).addClass('bg-light-danger');
                
                showNotification('error', data.result.error || 'Error al subir la imagen');
            }
        },
        fail: function(e, data) {
            var filePreview = $(`[data-file="${data.files[0].name}"]`);
            
            filePreview.html(`
                <div class="text-danger">
                    <i class="ti ti-x"></i> Error en la subida
                </div>
            `).addClass('bg-light-danger');
            
            showNotification('error', 'Error en la subida del archivo');
        }
    });

    // Trigger manual del input file
    $('#triggerUpload').click(function() {
        $('#fileupload').click();
    });

    // Filtrar por tipo
    $('#filterType').change(function() {
        const type = $(this).val();
        
        if (type === '') {
            renderTable(allImages);
        } else {
            const filtered = allImages.filter(img => img.entidad_tipo === type);
            renderTable(filtered);
        }
    });

    // Buscar imágenes
    $('#searchImages').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        if (searchTerm === '') {
            renderTable(allImages);
        } else {
            const filtered = allImages.filter(img => 
                img.nombre_archivo.toLowerCase().includes(searchTerm) || 
                img.entidad_id.toString().includes(searchTerm) ||
                img.entidad_tipo.toLowerCase().includes(searchTerm)
            );
            renderTable(filtered);
        }
    });

    // Eliminar imagen
    $(document).on('click', '.delete-image', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: '¿Eliminar imagen?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../controllers/image_controller.php',
                    type: 'DELETE',
                    dataType: 'json',
                    contentType: 'application/json',
                    data: JSON.stringify({ id: id }),
                    success: function(response) {
                        if (response.success) {
                            showNotification('success', 'Imagen eliminada correctamente');
                            if (dataTable) {
                                dataTable.destroy();
                            }
                            loadAllImages();
                        } else {
                            showNotification('error', response.error || 'Error al eliminar la imagen');
                        }
                    },
                    error: function() {
                        showNotification('error', 'Error al eliminar la imagen');
                    }
                });
            }
        });
    });

    // Función para mostrar notificaciones con SweetAlert2
    function showNotification(type, message) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        
        Toast.fire({
            icon: type,
            title: message
        });
    }

    // Función para formatear tamaño de archivo
    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') return '';
        if (bytes >= 1000000) return (bytes / 1000000).toFixed(2) + ' MB';
        if (bytes >= 1000) return (bytes / 1000).toFixed(2) + ' KB';
        return bytes + ' bytes';
    }

    // Función para formatear bytes (para la galería)
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
    
    // Capitalizar primera letra
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Efecto drag and drop
    $('#dropzone').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('border-primary bg-light');
    });

    $('#dropzone').on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('border-primary bg-light');
    });

    $('#dropzone').on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('border-primary bg-light');
    });
});
</script>

<style>
.upload-area {
    cursor: pointer;
    transition: all 0.3s;
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6;
}
.upload-area:hover, .upload-area.dragover {
    background-color: #e9ecef;
    border-color: #86b7fe;
}
.file-preview {
    transition: all 0.3s;
}
.bg-light-success {
    background-color: rgba(25, 135, 84, 0.1);
}
.bg-light-danger {
    background-color: rgba(220, 53, 69, 0.1);
}
.img-thumbnail {
    transition: all 0.3s;
}
.img-thumbnail:hover {
    transform: scale(1.1);
}
#imagesTable th {
    white-space: nowrap;
}
</style>