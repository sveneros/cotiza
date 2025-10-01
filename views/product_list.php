<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">
<!-- Galería de imágenes -->
<!-- lightGallery CSS -->
<link rel="stylesheet" href="../assets/css/lightgallery.min.css" />
<link rel="stylesheet" href="../assets/css/lg-zoom.min.css" />
<link rel="stylesheet" href="../assets/css/lg-fullscreen.min.css" />

<?php
include('../layout/header.php');
?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12 ">
      <a href="product_form.php" class="btn btn-primary m-1 float-end">
        <i class="ti ti-plus"></i> Nuevo Producto
      </a>
      <h4 class="main-title">Productos</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="#" class="f-s-14 f-w-500">
            <span>
              <i class="ph-duotone ph-table f-s-16"></i> Operaciones
            </span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Productos</a>
        </li>
      </ul>
    </div>
  </div>
  <!-- Breadcrumb end -->

  <!-- Data Table start -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body p-0">
          <div class="table-responsive app-scroll app-datatable-default product-list-table">
            <table class="table-sm display align-middle" id="basic-1">
              <thead>
                <tr>
                  <th>Imágenes</th>
                  <th>Código</th>
                  <th>Nombre</th>
                  <th>Descripcion</th>
                  <th>Marca</th>
                  <th>Categoría</th>
                  <th>Precio BS.</th>
                  <th>Estado</th>
                  <th>Stock actual</th>
                  <th>Opciones</th>
                </tr>
              </thead>
              <tbody id="DetalleTabla">
                <tr>
                  <td>CARGANDO...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Data Table end -->
</div>

<!-- Modal para galería de imágenes -->
<div class="modal fade" id="imageGalleryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Galería de imágenes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="productGallery" class="row g-3"></div>
      </div>
    </div>
  </div>
</div>

<?php
include('../layout/footer.php');
?>

<!-- data table jquery-->
<script src="../assets/vendor/datatable/jquery.dataTables.min.js"></script>
<script src="../assets/js/jquery.validate.min.js"></script>
<script src="../assets/js/jquery-key-restrictions.min.js"></script>
<!-- Galería de imágenes -->
<!-- lightGallery JS -->
<script src=../assets/js/lightgallery.min.js"></script>
<script src="../assets/js/lg-zoom.min.js"></script>
<script src="../assets/js/lg-fullscreen.min.js"></script>
<script>
$(function($){
    ObtenerProductosIngreso();
});

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
        const est = value.estado === 'V' ?
            '<span class="badge rounded-pill bg-success badge-notification">HABILITADO</span>' :
            '<span class="badge rounded-pill bg-danger badge-notification">DESHABILITADO</span>';

        const edi = '<a href="product_form.php?id=' + value.id + '" class="btn btn-primary"><i class="fa fa-pencil"></i></a>';

        // Celda de imágenes - se cargarán dinámicamente
        const imagesCell = `
            <td class="product-images" data-product-id="${value.id}">
                <div class="spinner-border spinner-border-sm text-primary"></div>
            </td>
        `;

        html += `
            <tr role="row" class="odd">
                ${imagesCell}
                <td class="sorting_1">${value.producto_codigo}</td>
                <td>${value.producto_nombre}</td>
                <td>${value.producto_descripcion}</td>
                <td>${value.marca}</td>
                <td>${value.categoria}</td>
                <td>${value.puntos}</td>
                
                <td>${est}</td>
                <td>${value.stock_actual}</td>
                <td>${edi}</td>
            </tr>
        `;
    });

    $('#DetalleTabla').html(html);
    
    // Inicializar DataTable
    $('#basic-1').DataTable();
    
    // Cargar imágenes para cada producto
    $('.product-images').each(function() {
        const productId = $(this).data('product-id');
        loadProductImagesForList(productId, $(this));
    });
}

// Cargar imágenes para la lista de productos
function loadProductImagesForList(productId, cellElement) {
    $.ajax({
        url: '../controllers/image_controller.php?entidad_tipo=producto&entidad_id=' + productId,
        type: 'GET',
        dataType: 'json',
        success: function(images) {
            let html = '';
            if (images.length > 0) {
                // Mostrar hasta 3 miniaturas
                const imagesToShow = images.slice(0, 3);
                
                html += `<div class="d-flex align-items-center gap-1">`;
                
                imagesToShow.forEach(function(image, index) {
                    html += `
                        <div class="image-thumbnail position-relative" data-product-id="${productId}">
                            <img src="../${image.ruta}" class="img-thumbnail" 
                                 style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                 data-image-index="${index}"
                                 data-product-id="${productId}">
                            ${index === 2 && images.length > 3 ? 
                                `<span class="badge bg-primary position-absolute bottom-0 end-0">+${images.length - 3}</span>` : ''}
                        </div>
                    `;
                });
                
                html += `</div>`;
                
                // Agregar botón para ver todas las imágenes si hay más de 3
                if (images.length > 3) {
                    html += `
                        <button class="btn btn-sm btn-link see-all-images" 
                                data-product-id="${productId}"
                                style="font-size: 0.7rem;">
                            Ver todas (${images.length})
                        </button>
                    `;
                }
            } else {
                html = `<div class="text-muted">Sin imágenes</div>`;
            }
            
            cellElement.html(html);
            
            // Inicializar eventos para las miniaturas
            initImageThumbnailEvents(productId);
        },
        error: function() {
            cellElement.html('<div class="text-muted">Error al cargar imágenes</div>');
        }
    });
}

// Inicializar eventos para las miniaturas de imágenes
function initImageThumbnailEvents(productId) {
    $(`.image-thumbnail[data-product-id="${productId}"] img`).on('click', function() {
        const imageIndex = $(this).data('image-index');
        showImageGallery(productId, imageIndex);
    });
    
    $(`.see-all-images[data-product-id="${productId}"]`).on('click', function() {
        showImageGallery(productId, 0);
    });
}

// Mostrar galería de imágenes en modal
function showImageGallery(productId, startIndex) {
    $.ajax({
        url: '../controllers/image_controller.php?entidad_tipo=producto&entidad_id=' + productId,
        type: 'GET',
        dataType: 'json',
        success: function(images) {
            if (images.length > 0) {
                let galleryHtml = '';
                
                images.forEach(function(image, index) {
                    galleryHtml += `
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="../${image.ruta}" class="gallery-item">
                                <img src="../${image.ruta}" class="img-fluid rounded" alt="">
                            </a>
                        </div>
                    `;
                });
                
                $('#productGallery').html(galleryHtml);
                
                // Inicializar lightGallery CORRECTAMENTE
                const gallery = document.getElementById('productGallery');
                lightGallery(gallery, {
                    selector: '.gallery-item',
                    plugins: [lgZoom, lgFullscreen],
                    download: false,
                    startClass: '',
                    mode: 'lg-fade',
                    cssEasing: 'cubic-bezier(0.25, 0, 0.25, 1)'
                });
                
                // Mostrar el modal
                const galleryModal = new bootstrap.Modal(document.getElementById('imageGalleryModal'));
                galleryModal.show();
                
                // Abrir la imagen específica
                if (startIndex >= 0 && startIndex < images.length) {
                    setTimeout(() => {
                        $(gallery).find('.gallery-item').eq(startIndex).trigger('click');
                    }, 500);
                }
            }
        }
    });
}
</script>

<style>
.image-thumbnail {
    position: relative;
    display: inline-block;
    margin-right: 5px;
}
.image-thumbnail img {
    transition: transform 0.3s;
}
.image-thumbnail img:hover {
    transform: scale(1.1);
}
.gallery-item {
    display: block;
    margin-bottom: 15px;
    text-decoration: none;
    color: inherit;
}
.gallery-item img {
    transition: transform 0.3s;
    border: 1px solid #dee2e6;
}
.gallery-item:hover img {
    transform: scale(1.05);
    border-color: #86b7fe;
}
.product-images {
    min-width: 180px;
}
</style>