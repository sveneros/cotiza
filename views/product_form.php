<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">
<!-- editor css -->
<link rel="stylesheet" href="../assets/vendor/trumbowyg/trumbowyg.min.css">
<!-- File upload css -->
<link rel="stylesheet" href="../assets/css/jquery.fileupload.min.css">

<?php
include('../layout/header.php');

// Get product ID from URL if editing
$productId = isset($_GET['id']) ? $_GET['id'] : 0;
?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title"><?php echo $productId ? 'Editar' : 'Nuevo'; ?> Producto</h4>
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
          <a href="#" class="f-s-14 f-w-500"><?php echo $productId ? 'Editar' : 'Nuevo'; ?></a>
        </li>
      </ul>
    </div>
  </div>
  <!-- Breadcrumb end -->

  <!-- Product Form start -->
  <div class="row">
    <div class="col-lg-8 col-xxl-9">
      <div class="card">
        <div class="card-body">
          <div class="app-product-section">
            <div class="main-title">
              <h6>Información General</h6>
            </div>
            <div>
              <form id="formProduct" class="app-form">
                <input type="hidden" id="id_producto" value="<?php echo $productId; ?>">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">CÓDIGO<span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="codigo" name="codigo" maxlength="13" size="13" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">NOMBRE DE PRODUCTO<span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="nombre" name="nombre" maxlength="150" required>
                    </div>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">DESCRIPCIÓN<span class="text-danger">*</span></label>
                  <div id="editor2">
                    <p></p>
                  </div>
                </div>
            </div>

            <div class="app-divider-v dashed"></div>

            <div class="main-title">
              <h6>Categoría</h6>
            </div>

            <div>
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">MARCA<span class="text-danger">*</span></label>
                    <select id="marcas_list" name="marcas_list" class="form-control" required></select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">CATEGORÍA<span class="text-danger">*</span></label>
                    <select id="categories_list" name="categories_list" class="form-control" required></select>
                  </div>
                </div>
              </div>
            </div>

            <div class="app-divider-v dashed"></div>

            <div class="main-title">
              <h6>Stock</h6>
            </div>

            <div class="row">
                <div class="col-md-4">
                  <div class="mb-3">
                    <label class="form-label">STOCK ACTUAL<span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="stock_actual" name="stock_actual" min="0" max="9999" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label class="form-label">STOCK MÍNIMO<span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" min="0" max="9999" required>
                  </div>
                </div>
            </div>

            <div class="app-divider-v dashed"></div>

            <div class="main-title">
              <h6>Precio</h6>
            </div>

            <div>
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">PRECIO<span class="text-danger">*</span></label>
                    <div class="input-group mb-3">
                      <span class="input-group-text b-r-left">Bs.</span>
                      <input type="text" class="form-control b-r-right numberformat" id="puntos" name="puntos" min="1" max="99999999" maxlength="11" size="11" required>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">ESTADO<span class="text-danger">*</span></label>
                    <select class="form-control" id="estado" name="estado">
                      <option value="V">HABILITADO</option>
                      <option value="D">DESHABILITADO</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="mt-4 d-flex justify-content-end gap-2 flex-column flex-sm-row text-end">
                <a href="product_list.php" role="button" class="btn btn-light-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary" id="btn_submit">Guardar</button>
              </div>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-4 col-xxl-3">
      <div class="card">
        <div class="card-body">
          <div class="app-product-section">
            <div class="main-title">
              <h6>Imágenes del Producto</h6>
            </div>
            <div>
              <!-- Área de subida de archivos -->
              <div class="upload-area mt-3 p-3 border rounded text-center" id="dropzone">
                <span class="fs-1 text-primary"><i class="ti ti-cloud-upload"></i></span>
                <h5>Arrastra imágenes aquí</h5>
                <p class="text-muted">o haz clic para seleccionar</p>
                <input id="fileupload" type="file" name="files[]" multiple 
                       accept="image/jpeg,image/png,image/gif" 
                       style="display: none;">
                <button class="btn btn-primary mt-2" id="triggerUpload">Seleccionar Archivos</button>
              </div>
              
              <!-- Previsualización -->
              <div id="files" class="files mt-3"></div>
              
              <!-- Galería de imágenes existentes -->
              <div id="gallery" class="mt-3">
                <h6 class="mb-3">Imágenes actuales</h6>
                <div id="existingImages" class="d-flex flex-wrap gap-2"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Product Form end -->
</div>

<?php
include('../layout/footer.php');
?>

<!-- data table jquery-->
<script src="../assets/vendor/datatable/jquery.dataTables.min.js"></script>
<script src="../assets/js/jquery.validate.min.js"></script>
<script src="../assets/js/jquery-key-restrictions.min.js"></script>
<script src="../assets/vendor/trumbowyg/trumbowyg.min.js"></script>
<!-- File upload scripts -->
<script src="../assets/js/jquery.ui.widget.js"></script>
<script src="../assets/js/jquery.iframe-transport.js"></script>
<script src="../assets/js/jquery.fileupload.js"></script>

<script>
$(function($){
    // Initialize editor
    $('#editor2').trumbowyg();
    
    CargarMarcas();
    CargarCategorias();
    
    // Load product data if editing
    var productId = $('#id_producto').val();
    if(productId && productId != "0") {
        CargarProducto(productId);
        loadProductImages(productId);
        
    } else {
        // Ocultar galería si es producto nuevo
        ObtenerMarcas('');
        setTimeout(function() {
                ObtenerCategorias('', $('#marcas_list option:selected').val());
            }, 300);
        $('#gallery').hide();
    }
    
    //validation
    //$("#nombre").lettersOnly();
    
    $('#puntos').on('blur', function() {
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

            // Convert to number and format to two decimals
            value = parseFloat(value).toFixed(2);
            $(this).val(value);
        }
    });
    
    // Configuración de jQuery File Upload
    $('#fileupload').fileupload({
        url: '../controllers/image_controller.php',
        dataType: 'json',
        autoUpload: true,
        formData: function() {
            // Usar el ID existente o uno temporal para nuevos productos
            const productId = $('#id_producto').val() || 'new_product';
            return [
                { name: 'entidad_tipo', value: 'producto' },
                { name: 'entidad_id', value: productId }
            ];
        },
        add: function(e, data) {
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
                
                // Si es un producto nuevo, actualizar el ID temporal con el real después de guardar
                const productId = $('#id_producto').val();
                if (productId && productId !== '0') {
                    updateImageEntityId(data.result.id, productId);
                }
                
                // Recargar imágenes si estamos editando
                if (productId && productId !== '0') {
                    loadProductImages(productId);
                }
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
    
    
    // Cargar imágenes existentes del producto
    function loadProductImages(productId) {
        $.ajax({
            url: '../controllers/image_controller.php?entidad_tipo=producto&entidad_id=' + productId,
            type: 'GET',
            dataType: 'json',
            success: function(images) {
                if (images.length > 0) {
                    $('#gallery').show();
                    let html = '';
                    images.forEach(function(image) {
                        html += `
                            <div class="image-thumbnail position-relative" data-id="${image.id}">
                                <img src="../${image.ruta}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 delete-image" data-id="${image.id}">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        `;
                    });
                    $('#existingImages').html(html);
                } else {
                    $('#gallery').hide();
                }
            },
            error: function() {
                showNotification('error', 'Error al cargar las imágenes del producto');
            }
        });
    }
    
    // Eliminar imagen del producto
    $(document).on('click', '.delete-image', function(e) {
        e.preventDefault();
        const imageId = $(this).data('id');
        const productId = $('#id_producto').val();
        
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
                    data: JSON.stringify({ id: imageId }),
                    success: function(response) {
                        if (response.success) {
                            showNotification('success', 'Imagen eliminada correctamente');
                            loadProductImages(productId);
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
    
    // Actualizar el ID de entidad de una imagen (para nuevos productos)
    function updateImageEntityId(imageId, productId) {
        $.ajax({
            url: '../controllers/image_controller.php',
            type: 'PUT',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({ 
                id: imageId,
                entidad_id: productId 
            }),
            success: function(response) {
                if (!response.success) {
                    console.error('Error al actualizar ID de imagen:', response.error);
                }
            },
            error: function() {
                console.error('Error al actualizar ID de imagen');
            }
        });
    }
});

function CargarProducto(productId) {
    var localData = JSON.parse(localStorage.getItem('sml2020_productos'));
    $.each(localData, function(key, value) {
        if(value.id == productId) {
            $('#codigo').val(value.producto_codigo);
            $('#nombre').val(value.producto_nombre);
            $('#editor2').trumbowyg('html', value.producto_descripcion);
            $('#puntos').val(value.puntos);
            $('#estado').val(value.estado).prop('selected', true);
            $('#stock_minimo').val(value.stock_minimo);
            $('#stock_actual').val(value.stock_actual);
            
            // Load brand and category
            ObtenerMarcas(value.marca);
            // Need to wait for brands to load before setting category
            setTimeout(function() {
                ObtenerCategorias(value.categoria, $('#marcas_list option:selected').val());
            }, 300);
            
            return false;
        }
    });
}

function CargarMarcas() {
    $.ajax({
        url: '../controllers/brand_controller.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            localStorage.setItem('sml2020_marcas', JSON.stringify(data)); 
            //ObtenerMarcas('');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (textStatus === 'timeout') {
                alert("Internet connection is down!");
            } else {
                alert("Error loading brands: " + errorThrown);
            }
        }
    });
}


function ObtenerMarcas(selected){
    $('#marcas_list').empty();
    var localData=JSON.parse(localStorage.getItem('sml2020_marcas'));

    var html='';
    $.each(localData,function(key,value){
      html+='<option value="'+value.id+'"> ('+value.codigo + ") " + value.nombre_marca+'</option>';
      if (value.nombre_marca==selected)
      html+='<option value="'+value.id+'" selected> ('+value.codigo + ") " + value.nombre_marca+'</option>';
    });
    $('#marcas_list').append(html);
}

  function CargarCategorias() {
    $.ajax({
        url: '../controllers/category_controller.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            localStorage.setItem('sml2020_categorias', JSON.stringify(data)); // Store all categories in localStorage
            ObtenerCategorias('', $('#marcas_list option:selected').val()); // Assuming this function processes the categories
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (textStatus === 'timeout') {
                alert("Internet connection is down!");
            } else {
                alert("Error loading categories: " + errorThrown);
            }
        }
    });
}


  $('#marcas_list').on('change', function() {
        var selectedMarca = $(this).val(); 
        ObtenerCategorias('', $('#marcas_list option:selected').val());
  });

  function ObtenerCategorias(selected, id_marca) {
    $('#categories_list').empty();
    var localData=JSON.parse(localStorage.getItem('sml2020_categorias'));

    var html='';
    $.each(localData,function(key,value){
      if (value.id_marca==id_marca)
        html+='<option value="'+value.id+'"> ('+value.codigo + ") " +value.descripcion+'</option>';
      if (value.descripcion==selected)
        html+='<option value="'+value.id+'" selected> ('+value.codigo + ") " +value.descripcion+'</option>';
    });
    $('#categories_list').append(html);
}

function GuardarProducto() {
    $('#btn_submit').hide();

    const elId = $('#id_producto').val();
    const codigo = $('#codigo').val();
    const nombre = $('#nombre').val();
    const descripcion = $('#editor2').trumbowyg('html');
    const idMarca = $('#marcas_list option:selected').val();
    const idCategoria = $('#categories_list option:selected').val();
    const puntos = $('#puntos').val();
    const estado = $('#estado option:selected').val();
    const stock_actual = $('#stock_actual').val();
    const stock_minimo = $('#stock_minimo').val();
    const method = elId === "0" || elId === null || elId === "" ? 'POST' : 'PUT';
    
    const data = {
        id: elId,
        nombre: nombre,
        descripcion: descripcion,
        id_marca: idMarca,
        id_categoria: idCategoria,
        codigo: codigo,
        puntos: puntos,
        estado: estado,
        stock_actual: stock_actual,
        stock_minimo: stock_minimo,
    };

    $.ajax({
        url: '../controllers/product_controller.php',
        type: method,
        dataType: "json",
        data: JSON.stringify(data),
        success: (response) => {
            if (response.success) {
                // Si es un nuevo producto, actualizar el ID en el formulario
                if (method === 'POST' && response.id) {
                    $('#id_producto').val(response.id);
                    
                    // Actualizar imágenes temporales con el ID real del producto
                    updateTemporaryImages(response.id);
                }
                
                Swal.fire({
                    title: 'Producto Guardado',
                    text: '',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'product_list.php';
                    }
                });
            } else {
                Swal.fire('Error', response.error || 'Ocurrió un error al guardar el producto.', 'error');
                $('#btn_submit').show();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (textStatus === 'timeout') {
                Swal.fire('Error', '¡La conexión a internet se ha interrumpido!', 'error');
            } else {
                Swal.fire('Error', 'Error al guardar producto: ' + errorThrown, 'error');
            }
            $('#btn_submit').show();
        }
    });
}

// Actualizar imágenes temporales con el ID real del producto
function updateTemporaryImages(productId) {
    $.ajax({
        url: '../controllers/image_controller.php?entidad_tipo=producto&entidad_id=new_product',
        type: 'GET',
        dataType: 'json',
        success: function(images) {
            if (images.length > 0) {
                images.forEach(function(image) {
                    updateImageEntityId(image.id, productId);
                });
                loadProductImages(productId);
            }
        },
        error: function() {
            console.error('Error al cargar imágenes temporales');
        }
    });
}

$("#formProduct").submit(function(e) {
    e.preventDefault();
}).validate({  
    submitHandler: function(form) { 
        GuardarProducto();
        return false;
    }
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
.image-thumbnail {
    position: relative;
    display: inline-block;
}
.delete-image {
    padding: 0.15rem 0.25rem;
    font-size: 0.7rem;
}
</style>