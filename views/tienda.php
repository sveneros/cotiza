<!-- lightGallery CSS -->
<link rel="stylesheet" href="../assets/css/lightgallery.min.css" />
<link rel="stylesheet" href="../assets/css/lg-zoom.min.css" />
<link rel="stylesheet" href="../assets/css/lg-fullscreen.min.css" />
<?php
include('../layout/header_clientes.php');
?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title">Catálogo Virtual</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="#" class="f-s-14 f-w-500">
            <span>
              <i class="ph-duotone ph-shopping-bag-open f-s-16"></i> Catálogo
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

  <!-- Product start -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="product-header d-flex justify-content-between gap-3 align-items-center">
            <div class="d-flex align-items-center">
              <a class="me-3 toggle-btn d-inline-block d-lg-none" role="button"><i class="ti ti-align-justified f-s-24"></i></a>
              <form class="app-form app-icon-form d-inline-block" action="#">
                <div class="position-relative">
                  <input type="search" class="form-control" id="search-input" placeholder="Buscar productos..." aria-label="Search">
                  <i class="ti ti-search text-dark"></i>
                </div>
              </form>
            </div>
           
          </div>
        </div>
      </div>
    </div>

    <!-- Filters start -->
    <div class="col-xxl-3 col-lg-4 product-box productbox">
      <div class="card">
        <div class="card-header">
          <h5>Filtros</h5>
        </div>
        <div class="card-body p-0">
          <div class="accordion accordion-flush app-accordion accordion-light-primary" id="accordion-flush-sort-by">

            <div class="accordion-item">
              <h2 class="accordion-header" id="flush-heading-brand">
                <button class="accordion-button bg-none p-1" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse_brand" aria-expanded="true" aria-controls="collapse_brand">
                  <span class="m-0 mt-1">Marcas</span>
                </button>
              </h2>
              <div id="collapse_brand" class="accordion-collapse collapse show"
                   aria-labelledby="flush-heading-brand" data-bs-parent="#accordion-flush-sort-by">
                <div class="accordion-body p-2" id="brands-container">
                  <div class="text-center py-3">
                    <div class="spinner-border spinner-border-sm"></div>
                    <p>Cargando marcas...</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="accordion-item">
              <h2 class="accordion-header" id="flush-heading-category">
                <button class="accordion-button bg-none p-1 collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse_category" aria-expanded="false" aria-controls="collapse_category">
                  <span class="m-0 mt-1">Categorías</span>
                </button>
              </h2>
              <div id="collapse_category" class="accordion-collapse collapse"
                   aria-labelledby="flush-heading-category" data-bs-parent="#accordion-flush-sort-by">
                <div class="accordion-body p-2" id="categories-container">
                  <div class="text-center py-3">
                    <div class="spinner-border spinner-border-sm"></div>
                    <p>Seleccione una marca primero</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="text-end m-3">
              <button id="clear-filters" class="btn btn-sm btn-primary">Limpiar filtros</button>
              <button id="apply-filters" class="btn btn-sm btn-secondary">Aplicar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Filters end -->

    <!-- product box start -->
    <div class="col-xxl-9 col-lg-8">
      <div class="product-wrapper-grid">
        <div class="row" id="product-grid">
          <!-- Los productos se cargarán aquí dinámicamente -->
          <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary"></div>
            <p>Cargando productos...</p>
          </div>
        </div>
      </div>
    </div>
    <!-- product box end -->
  </div>
  <!-- Product end -->
</div>

<!-- Modal para galería de imágenes -->
<div class="modal fade" id="imageGalleryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="galleryModalTitle">Galería de imágenes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="productGallery" class="row g-3"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para carrito de compras -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tu Carrito de Compras</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body app-scroll p-0">
        <div id="cart-items">
          <p class="text-center py-5">Tu carrito está vacío</p>
        </div>
        <div class="offcanvas-footer">
          <div class="head-box-footer p-3">
            <div class="mb-4">
              <h6 class="text-muted f-w-600">Total <span class="float-end" id="cart-subtotal">Bs. 0.00</span></h6>
            </div>
            <div class="header-cart-btn">
              
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

<!-- lightGallery JS -->
<script src="../assets/js/lightgallery.min.js"></script>
<script src="../assets/js/lg-zoom.min.js"></script>
<script src="../assets/js/lg-fullscreen.min.js"></script>

<script>
// Variable global para almacenar productos
let allProducts = [];
let allCategories = [];
let cart = [];


$('#btnActualizarCarrito').click(function() {
  updateCartModal();
});

$(document).ready(function() {
  // Cargar datos iniciales
  loadBrands();
  loadProducts();
  
  // Inicializar carrito
  initCart();

  // Eventos de filtros
  $('#apply-filters').click(applyFilters);
  $('#clear-filters').click(clearFilters);
  
  // Evento para búsqueda
  $('#search-input').on('input', debounce(searchProducts, 300));
  
  // Evento para cambio de marca (cargar categorías relacionadas)
  $(document).on('change', '.brand-filter', function() {
    loadCategoriesByBrand();
  });

  // Evento para limpiar carrito
  $('#clear-cart-btn').click(clearCart);
});

// Función para limpiar completamente el carrito
function clearCart() {
  Swal.fire({
    title: '¿Estás seguro?',
    text: "Esta acción vaciará completamente tu carrito de compras",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sí, limpiar carrito',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      localStorage.setItem('cart', JSON.stringify([]));
      updateCartCount();
      updateCartModal();
      Swal.fire(
        'Carrito vaciado',
        'Tu carrito de compras ha sido limpiado',
        'success'
      );
    }
  });
}

// Cargar marcas desde la API
function loadBrands() {
  $.ajax({
    url: '../controllers/brand_controller.php',
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      let html = '';
      data.forEach(brand => {
        html += `
          <div class="p-2 d-flex align-items-center gap-2">
            <label class="check-box">
              <input type="checkbox" class="brand-filter" value="${brand.id}">
              <span class="checkmark outline-secondary ms-2"></span>
            </label>
            <span class="f-s-15 f-w-500 text-secondary">${brand.nombre_marca}</span>
          </div>
        `;
      });
      $('#brands-container').html(html);
    },
    error: function() {
      $('#brands-container').html('<p class="text-danger">Error al cargar marcas</p>');
    }
  });
}

// Cargar categorías por marca seleccionada
function loadCategoriesByBrand() {
  const selectedBrands = [];
  $('.brand-filter:checked').each(function() {
    selectedBrands.push($(this).val());
  });

  if (selectedBrands.length === 0) {
    $('#categories-container').html('<div class="text-center py-3"><p>Seleccione una marca primero</p></div>');
    $('#collapse_category').collapse('hide');
    return;
  }

  $('#categories-container').html('<div class="text-center py-3"><div class="spinner-border spinner-border-sm"></div><p>Cargando categorías...</p></div>');
  $('#collapse_category').collapse('show');

  // Primero cargamos todas las categorías si no están cargadas
  if (allCategories.length === 0) {
    $.ajax({
      url: '../controllers/category_controller.php',
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        allCategories = data;
        filterCategoriesByBrand(selectedBrands);
      },
      error: function() {
        $('#categories-container').html('<p class="text-danger">Error al cargar categorías</p>');
      }
    });
  } else {
    filterCategoriesByBrand(selectedBrands);
  }
}

// Filtrar categorías por marca
function filterCategoriesByBrand(selectedBrands) {
  // Obtenemos los productos que pertenecen a las marcas seleccionadas
  const filteredProducts = allProducts.filter(product => 
    selectedBrands.includes(product.id_marca.toString())
  );
  
  // Obtenemos las categorías únicas de esos productos
  const categoryIds = [...new Set(filteredProducts.map(product => product.id_categoria))];
  
  // Filtramos las categorías
  const filteredCategories = allCategories.filter(category => 
    categoryIds.includes(category.id)
  );
  
  let html = '';
  if (filteredCategories.length === 0) {
    html = '<p class="text-center py-3">No hay categorías para las marcas seleccionadas</p>';
  } else {
    filteredCategories.forEach(category => {
      html += `
        <div class="p-2 d-flex align-items-center gap-2">
          <label class="check-box">
            <input type="checkbox" class="category-filter" value="${category.id}">
            <span class="checkmark outline-secondary ms-2"></span>
          </label>
          <span class="f-s-15 f-w-500 text-secondary">${category.descripcion}</span>
        </div>
      `;
    });
  }
  
  $('#categories-container').html(html);
}

// Cargar productos desde la API
function loadProducts() {
  $.ajax({
    url: '../controllers/product_controller.php',
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      allProducts = data;
      
      // Cargar imágenes para cada producto
      loadProductImages(data);
    },
    error: function() {
      $('#product-grid').html('<p class="text-danger">Error al cargar productos</p>');
    }
  });
}

// Cargar imágenes para cada producto
function loadProductImages(products) {
  let productsWithImages = [];
  let loadedCount = 0;
  
  if (products.length === 0) {
    renderProducts([]);
    return;
  }
  
  products.forEach(product => {
    $.ajax({
      url: `../controllers/image_controller.php?entidad_tipo=producto&entidad_id=${product.id}`,
      type: 'GET',
      dataType: 'json',
      success: function(images) {
        product.imagenes = images;
        loadedCount++;
        
        // Cuando todas las imágenes estén cargadas, renderizar productos
        if (loadedCount === products.length) {
          renderProducts(products);
        }
      },
      error: function() {
        product.imagenes = [];
        loadedCount++;
        
        if (loadedCount === products.length) {
          renderProducts(products);
        }
      }
    });
  });
}

// Renderizar productos en la vista
function renderProducts(products) {
  let html = '';
  
  if (products.length === 0) {
    html = '<div class="col-12 text-center py-5"><p>No se encontraron productos</p></div>';
  } else {
    products.forEach(product => {
      // Obtener la primera imagen de la galería o usar una por defecto
      let firstImage = 'assets/images/ecommerce/no-image.jpg';
      if (product.imagenes && product.imagenes.length > 0) {
        firstImage = product.imagenes[0].ruta;
      } else if (product.imagen_principal) {
        firstImage = product.imagen_principal;
      }
      
      html += `
        <div class="col-xxl-3 col-md-4 col-sm-6 product-item" 
             data-category="${product.id_categoria}" 
             data-brand="${product.id_marca}"
             data-name="${product.producto_nombre.toLowerCase()}">
          <div class="card overflow-hidden">
            <div class="card-body p-0">
              <div class="product-content-box">
                <div class="product-grid">
                  <div class="product-image">
                    <a href="#" class="image" data-product-id="${product.id}">
                      <img class="pic-1" src="../${firstImage}" alt="${product.producto_nombre}" onerror="this.src='../assets/images/ecommerce/no-image.jpg'">
                    </a>
                    <ul class="product-links">
                      <li>
                        <a href="#" class="bg-primary h-30 w-30 d-flex-center b-r-20 add-to-cart" 
                           data-product-id="${product.id}" title="Agregar al carrito">
                          <i class="ti ti-shopping-cart f-s-18 text-light"></i>
                        </a>
                      </li>
                      <li>
                        <a href="#" class="bg-success h-30 w-30 d-flex-center b-r-20 view-gallery" 
                           data-product-id="${product.id}" title="Ver galería">
                          <i class="ti ti-eye f-s-18 text-light"></i>
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="p-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <a href="#" class="m-0 f-s-20 f-w-500">${product.producto_nombre}</a>
                  </div>
                  <p class="text-secondary">${product.producto_descripcion || 'Sin descripción'}</p>
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-light-secondary">${product.marca || 'Sin marca'}</span>
                    <span class="badge bg-light-primary">${product.categoria || 'Sin categoría'}</span>
                  </div>
                  <div class="d-flex justify-content-between mt-2">
                    <button class="btn btn-sm btn-primary add-to-cart" data-product-id="${product.id}">
                      <i class="ti ti-shopping-cart me-1"></i> Agregar
                    </button>
                    <button class="btn btn-sm btn-outline-secondary view-gallery" data-product-id="${product.id}">
                      <i class="ti ti-photo me-1"></i> Ver más
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
    });
  }
  
  $('#product-grid').html(html);
  initProductEvents();
}

// Inicializar eventos de productos
function initProductEvents() {
  // Evento para agregar al carrito
  $('.add-to-cart').click(function(e) {
    e.preventDefault();
    const productId = $(this).data('product-id');
    addToCart(productId);
  });
  
  // Evento para ver galería
  $('.view-gallery').click(function(e) {
    e.preventDefault();
    const productId = $(this).data('product-id');
    showImageGallery(productId);
  });
}

// Función para buscar productos
function searchProducts() {
  const searchTerm = $('#search-input').val().toLowerCase();
  
  if (searchTerm === '') {
    renderProducts(allProducts);
    return;
  }
  
  const filteredProducts = allProducts.filter(product => 
    product.producto_nombre.toLowerCase().includes(searchTerm) || 
    (product.producto_descripcion && product.producto_descripcion.toLowerCase().includes(searchTerm))
  );
  
  renderProducts(filteredProducts);
}

// Función para debounce (evitar múltiples llamadas durante la escritura)
function debounce(func, wait) {
  let timeout;
  return function() {
    const context = this, args = arguments;
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(context, args), wait);
  };
}

// Aplicar filtros
function applyFilters() {
  const selectedCategories = [];
  const selectedBrands = [];
  
  $('.category-filter:checked').each(function() {
    selectedCategories.push($(this).val());
  });
  
  $('.brand-filter:checked').each(function() {
    selectedBrands.push($(this).val());
  });
  
  // Si no hay filtros seleccionados, mostrar todos los productos
  if (selectedCategories.length === 0 && selectedBrands.length === 0) {
    renderProducts(allProducts);
    return;
  }
  
  const filteredProducts = allProducts.filter(product => {
    const categoryMatch = selectedCategories.length === 0 || selectedCategories.includes(product.id_categoria.toString());
    const brandMatch = selectedBrands.length === 0 || selectedBrands.includes(product.id_marca.toString());
    return categoryMatch && brandMatch;
  });
  
  renderProducts(filteredProducts);
}

// Limpiar filtros
function clearFilters() {
  $('.category-filter, .brand-filter').prop('checked', false);
  $('#categories-container').html('<div class="text-center py-3"><p>Seleccione una marca primero</p></div>');
  $('#collapse_category').collapse('hide');
  renderProducts(allProducts);
}

// Funciones del carrito
function initCart() {
  if (!localStorage.getItem('cart')) {
    localStorage.setItem('cart', JSON.stringify([]));
  }
  updateCartCount();
}

function addToCart(productId) {
  cart = JSON.parse(localStorage.getItem('cart'));
  const product = allProducts.find(p => p.id == productId);
  
  if (!product) {
    Swal.fire('Error', 'Producto no encontrado', 'error');
    return;
  }
  
  // Validar que el producto tenga precio
  /* const price = product.precio_venta || 0;
  if (price <= 0) {
    Swal.fire('Advertencia', 'Este producto no tiene precio definido', 'warning');
    return;
  } */
  
  // Buscar si el producto ya está en el carrito
  const existingItem = cart.find(item => item.productId === productId);
  
  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    // Obtener la primera imagen del producto
    let productImage = 'assets/images/ecommerce/no-image.jpg';
    if (product.imagenes && product.imagenes.length > 0) {
      productImage = product.imagenes[0].ruta;
    } else if (product.imagen_principal) {
      productImage = product.imagen_principal;
    }
    
    cart.push({
      productId: productId,
      quantity: 1,
      name: product.producto_nombre,
      image: productImage
    });
  }
  
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartCount();
  
  // Mostrar notificación
  Swal.fire({
    position: 'top-end',
    icon: 'success',
    title: 'Producto agregado al carrito',
    showConfirmButton: false,
    timer: 1500
  });
}

function updateCartCount() {
  cart = JSON.parse(localStorage.getItem('cart'));
  const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
  
  // Actualizar en el modal
  $('#cart-count').text(totalItems);
  
  // Actualizar también en el header (si existe el elemento)
  $('.header-cart .badge-notification').text(totalItems);
  
  // Actualizar estado del botón de checkout
  if (cart.length === 0) {
    $('#checkout-btn').addClass('disabled');
  } else {
    $('#checkout-btn').removeClass('disabled');
  }
}

function updateCartModal() {
  cart = JSON.parse(localStorage.getItem('cart'));
  
  if (cart.length === 0) {
    $('#cart-items').html('<p class="text-center py-5">Tu carrito está vacío</p>');
    $('#cart-subtotal').text('Bs. 0.00');
    $('#checkout-btn').addClass('disabled');
    return;
  }
  
  let html = '<div class="head-container">';
  let subtotal = 0;
  
  cart.forEach(item => {
    //const itemSubtotal = item.price * item.quantity;
    //subtotal += itemSubtotal;
    
    html += `
      <div class="head-box">
        <img src="../${item.image}" alt="${item.name}" class="h-50 me-3 b-r-10" onerror="this.src='../assets/images/ecommerce/no-image.jpg'">
        <div class="flex-grow-1">
          <a class="mb-0 f-w-600 f-s-16">${item.name}</a>
          <div>
            <span class="text-secondary"><span class="text-dark f-w-400">Cantidad:</span> ${item.quantity}</span>
          </div>
        </div>
        <div class="text-end">
          <i class="ph ph-trash f-s-18 text-danger close-btn remove-from-cart" data-product-id="${item.productId}"></i>
         
        </div>
      </div>
    `;
  });
  
  html += '</div>';
  
  $('#cart-items').html(html);
  $('#cart-subtotal').text(`Bs. ${subtotal.toFixed(2)}`);
  $('#checkout-btn').removeClass('disabled');
  
  // Evento para eliminar del carrito
  $('.remove-from-cart').click(function() {
    const productId = $(this).data('product-id');
    removeFromCart(productId);
  });
}

function updateCartQuantity(productId, action) {
  cart = JSON.parse(localStorage.getItem('cart'));
  const item = cart.find(item => item.productId == productId);
  
  if (!item) return;
  
  if (action === 'increase') {
    item.quantity += 1;
  } else if (action === 'decrease' && item.quantity > 1) {
    item.quantity -= 1;
  }
  
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartCount();
  updateCartModal();
}

function setCartQuantity(productId, quantity) {
  cart = JSON.parse(localStorage.getItem('cart'));
  const item = cart.find(item => item.productId == productId);
  
  if (!item) return;
  
  item.quantity = quantity;
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartCount();
  updateCartModal();
}

function removeFromCart(productId) {
  cart = JSON.parse(localStorage.getItem('cart'));
  cart = cart.filter(item => item.productId != productId);
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartCount();
  updateCartModal();
  
  if (cart.length === 0) {
    // Cerrar el modal si el carrito queda vacío
    $('#cartModal').modal('hide');
  }
}

// Mostrar galería de imágenes
function showImageGallery(productId) {
  const product = allProducts.find(p => p.id == productId);
  
  if (!product) {
    Swal.fire('Error', 'Producto no encontrado', 'error');
    return;
  }
  
  // Verificar si el producto ya tiene imágenes cargadas
  if (product.imagenes && product.imagenes.length > 0) {
    displayImageGallery(product);
    return;
  }
  
  // Si no, cargarlas desde la API
  $.ajax({
    url: '../controllers/image_controller.php?entidad_tipo=producto&entidad_id=' + productId,
    type: 'GET',
    dataType: 'json',
    success: function(images) {
      // Guardar las imágenes en el producto para futuras referencias
      product.imagenes = images;
      displayImageGallery(product);
    },
    error: function() {
      Swal.fire('Error', 'No se pudieron cargar las imágenes del producto', 'error');
    }
  });
}

function displayImageGallery(product) {
  let galleryHtml = '';
  
  if (product.imagenes && product.imagenes.length > 0) {
    product.imagenes.forEach((image, index) => {
      galleryHtml += `
        <div class="col-6 col-md-4 col-lg-3">
          <a href="../${image.ruta}" class="gallery-item">
            <img src="../${image.ruta}" class="img-fluid rounded" alt="Imagen ${index + 1}" onerror="this.src='../assets/images/ecommerce/no-image.jpg'">
          </a>
        </div>
      `;
    });
  } else {
    // Mostrar al menos la imagen principal si no hay galería
    const mainImage = product.imagen_principal || 'assets/images/ecommerce/no-image.jpg';
    galleryHtml = `
      <div class="col-12 text-center">
        <a href="../${mainImage}" class="gallery-item">
          <img src="../${mainImage}" class="img-fluid rounded" alt="Imagen principal" onerror="this.src='../assets/images/ecommerce/no-image.jpg'">
        </a>
        <p class="mt-3">Este producto no tiene imágenes adicionales</p>
      </div>
    `;
  }
  
  $('#productGallery').html(galleryHtml);
  $('#galleryModalTitle').text(`Galería de ${product.producto_nombre}`);
  
  // Inicializar lightGallery
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
  $('#imageGalleryModal').modal('show');
}
</script>

<style>
.product-image {
  position: relative;
  overflow: hidden;
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f8f9fa;
}

.product-image img {
  max-height: 100%;
  max-width: 100%;
  object-fit: contain;
  transition: transform 0.5s;
}

.product-image:hover img {
  transform: scale(1.05);
}

.product-links {
  position: absolute;
  bottom: 10px;
  right: 10px;
  display: flex;
  gap: 5px;
}

.product-links a {
  opacity: 0;
  transform: translateY(20px);
  transition: all 0.3s;
}

.product-image:hover .product-links a {
  opacity: 1;
  transform: translateY(0);
}

.product-links a:nth-child(1) {
  transition-delay: 0.1s;
}

.product-links a:nth-child(2) {
  transition-delay: 0.2s;
}

.gallery-item img {
  transition: transform 0.3s;
  cursor: pointer;
  width: 100%;
  height: 150px;
  object-fit: cover;
}

.gallery-item:hover img {
  transform: scale(1.05);
}

#cart-count {
  font-size: 0.7rem;
}

.quantity-input {
  max-width: 50px;
}

#cart-subtotal {
  color: var(--bs-primary);
  font-weight: bold;
}

#checkout-btn.disabled {
  pointer-events: none;
  opacity: 0.65;
}

/* Estilos para el modal del carrito */
#cartModal .modal-body {
  max-height: 60vh;
  overflow-y: auto;
}

/* Estilos para la galería de imágenes */
#imageGalleryModal .modal-body {
  max-height: 70vh;
  overflow-y: auto;
}

/* Estilo para el botón limpiar carrito */
#clear-cart-btn {
  margin-right: auto;
}

/* Estilos para el modal del carrito */
#cartModal .modal-body {
  padding: 0;
}

#cartModal .head-container {
  max-height: 50vh;
  overflow-y: auto;
}

#cartModal .head-box {
  display: flex;
  padding: 15px;
  border-bottom: 1px solid #eee;
  align-items: center;
}

#cartModal .head-box img {
  width: 60px;
  height: 60px;
  object-fit: cover;
}

#cartModal .head-box-footer {
  border-top: 1px solid #eee;
}

#cartModal .header-cart-btn {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

#cartModal .close-btn {
  cursor: pointer;
}

#cartModal .offcanvas-footer {
  position: sticky;
  bottom: 0;
  background: white;
  z-index: 1;
}
</style>