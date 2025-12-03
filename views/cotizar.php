<?php
include('../layout/header_clientes.php');

// Obtener el ID del cliente desde la sesión o establecer uno por defecto
if (isset($_SESSION['sml2020_svenerossys_id_usuario_registrado'])) {
    $id_cliente = $_SESSION['sml2020_svenerossys_id_usuario_registrado'];
} else {
    // Si no hay sesión, usar un cliente por defecto (modifica según tu lógica)
    $id_cliente = 1; // Cliente por defecto para pruebas
}
?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title">Cotización</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="tienda.php" class="f-s-14 f-w-500">
            <span>
              <i class="ph-duotone ph-shopping-bag-open f-s-16"></i> Catálogo
            </span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Cotización</a>
        </li>
      </ul>
    </div>
  </div>
  <!-- Breadcrumb end -->

  <!-- Input oculto para el ID del cliente -->
  <input type="hidden" id="id_cliente" value="<?php echo $id_cliente; ?>">

  <!-- Cart content start -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5>Productos en cotización</h5>
          <div class="d-flex align-items-center">
            <span class="badge bg-primary me-2" id="cart-count">0</span>
            <small class="text-muted">productos en cotización</small>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th width="40%">Producto</th>
                  <th width="25%">Detalles</th>
                  <th width="20%">Cantidad</th>
                  <th width="15%">Acciones</th>
                </tr>
              </thead>
              <tbody id="cart-items-table">
                <!-- Los productos del carrito se cargarán aquí -->
                <tr>
                  <td colspan="4" class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <p>Cargando productos...</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Product Suggestions Section -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5>Productos sugeridos</h5>
          <small class="text-muted">Basado en cotizaciones de otros clientes</small>
        </div>
        <div class="card-body">
          <div id="suggestions-loading" class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary"></div>
            <p class="mt-2">Analizando sugerencias...</p>
          </div>
          <div id="suggestions-container" class="d-none">
            <div class="row" id="suggestions-row">
              <!-- Las sugerencias se cargarán aquí -->
            </div>
          </div>
          <div id="no-suggestions" class="text-center py-3 d-none">
            <i class="ti ti-info-circle f-s-48 text-muted mb-3"></i>
            <p>No hay sugerencias disponibles en este momento</p>
            <p class="small text-muted">Agrega más productos a tu cotización para recibir sugerencias personalizadas</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Action buttons -->
  <div class="row mt-3">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex justify-content-between">
          <a href="tienda.php" class="btn btn-secondary">
            <i class="ti ti-arrow-left me-1"></i> Seguir comprando
          </a>
          <div>
            <button id="clear-cart-btn" class="btn btn-danger me-2">
              <i class="ti ti-trash me-1"></i> Vaciar carrito
            </button>
            <button id="generate-quote-btn" class="btn btn-primary" disabled>
              <i class="ti ti-file-invoice me-1"></i> Generar cotización
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmQuoteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmar cotización</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>¿Estás seguro que deseas generar esta cotización?</p>
        <div class="alert alert-info">
          <i class="ti ti-info-circle me-2"></i>
          La cotización será registrada en el sistema y podrás consultarla posteriormente.
        </div>
        <div class="alert alert-warning">
          <i class="ti ti-alert-triangle me-2"></i>
          <strong>Nota:</strong> Los precios deben ser consultados con el vendedor.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="confirm-quote">Generar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de éxito -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Cotización generada</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="text-center py-3">
          <i class="ti ti-circle-check text-success f-s-48 mb-3"></i>
          <h4>¡Cotización generada con éxito!</h4>
          <p id="quote-details"></p>
          <div class="alert alert-success mt-3">
            <i class="ti ti-check me-2"></i>
            Tu cotización ha sido registrada. Pronto un vendedor se pondrá en contacto contigo.
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-outline-success" onclick="window.location.href='tienda.php'">
          <i class="ti ti-shopping-bag me-1"></i> Volver al catálogo
        </button>
      </div>
    </div>
  </div>
</div>

<?php
include('../layout/footer.php');
?>

<script>
$(document).ready(function() {
  // Actualizar contador del carrito al iniciar
  updateCartCount();
  
  // Cargar carrito al iniciar
  loadCartItems();
  
  // Evento para vaciar carrito
  $('#clear-cart-btn').click(clearCart);
  
  // Evento para abrir modal de confirmación
  $('#generate-quote-btn').click(function() {
    $('#confirmQuoteModal').modal('show');
  });
  
  // Evento para generar cotización
  $('#confirm-quote').click(generateQuote);
});

// Actualizar contador del carrito
function updateCartCount() {
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
  $('#cart-count').text(totalItems);
  $('#generate-quote-btn').prop('disabled', totalItems === 0);
}

// Cargar productos del carrito
function loadCartItems() {
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  
  // Actualizar contador
  updateCartCount();
  
  // Cargar sugerencias basadas en los productos del carrito
  const productIds = cart.map(item => item.productId);
  if (productIds.length > 0) {
    loadProductSuggestions(productIds);
  } else {
    $('#suggestions-loading').addClass('d-none');
    $('#no-suggestions').removeClass('d-none');
    $('#suggestions-container').addClass('d-none');
  }
  
  if (cart.length === 0) {
    $('#cart-items-table').html(`
      <tr>
        <td colspan="4" class="text-center py-5">
          <i class="ti ti-shopping-cart-off f-s-48 text-muted mb-3"></i>
          <p>No hay productos en tu carrito</p>
          <a href="tienda.php" class="btn btn-primary">Ir al catálogo</a>
        </td>
      </tr>
    `);
    return;
  }
  
  // Mostrar spinner mientras se cargan los productos
  $('#cart-items-table').html(`
    <tr>
      <td colspan="4" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
        <p>Cargando productos...</p>
      </td>
    </tr>
  `);
  
  // Obtener detalles completos de los productos CON SUS IMÁGENES
  $.ajax({
    url: '../controllers/product_controller.php',
    type: 'GET',
    dataType: 'json',
    success: function(products) {
      // Ahora cargamos las imágenes para cada producto
      loadCartProductsWithImages(cart, products);
    },
    error: function() {
      $('#cart-items-table').html(`
        <tr>
          <td colspan="4" class="text-center py-5 text-danger">
            <i class="ti ti-alert-triangle f-s-48 mb-3"></i>
            <p>Error al cargar los productos del carrito</p>
            <button class="btn btn-outline-primary" onclick="loadCartItems()">
              <i class="ti ti-refresh me-1"></i> Reintentar
            </button>
          </td>
        </tr>
      `);
    }
  });
}

// Cargar productos del carrito con sus imágenes
function loadCartProductsWithImages(cart, allProducts) {
  let productsWithImages = [];
  let loadedCount = 0;
  
  if (allProducts.length === 0) {
    renderCartItems(cart, []);
    return;
  }
  
  // Para cada producto en el carrito, buscar su información completa
  cart.forEach(cartItem => {
    const product = allProducts.find(p => p.id == cartItem.productId);
    
    if (!product) {
      // Si no encontramos el producto, usar información básica del carrito
      productsWithImages.push({
        ...cartItem,
        producto_nombre: cartItem.name || 'Producto no encontrado',
        marca: '',
        producto_codigo: '',
        producto_descripcion: '',
        imagenes: []
      });
      loadedCount++;
      
      if (loadedCount === cart.length) {
        renderCartItems(cart, productsWithImages);
      }
      return;
    }
    
    // Si el producto ya tiene imágenes en la respuesta, usarlas
    if (product.imagenes && Array.isArray(product.imagenes) && product.imagenes.length > 0) {
      productsWithImages.push({
        ...product,
        image: product.imagenes[0].ruta || cartItem.image
      });
      loadedCount++;
    } else {
      // Si no tiene imágenes, cargarlas desde el controlador de imágenes
      $.ajax({
        url: `../controllers/image_controller.php?entidad_tipo=producto&entidad_id=${product.id}`,
        type: 'GET',
        dataType: 'json',
        success: function(images) {
          product.imagenes = images;
          product.image = images && images.length > 0 ? images[0].ruta : cartItem.image;
          productsWithImages.push(product);
          loadedCount++;
          
          if (loadedCount === cart.length) {
            renderCartItems(cart, productsWithImages);
          }
        },
        error: function() {
          // Si hay error, usar imagen del carrito o por defecto
          product.imagenes = [];
          product.image = cartItem.image || 'assets/images/ecommerce/no-image.jpg';
          productsWithImages.push(product);
          loadedCount++;
          
          if (loadedCount === cart.length) {
            renderCartItems(cart, productsWithImages);
          }
        }
      });
    }
    
    // Verificar si ya cargamos todos los productos
    if (product.imagenes && Array.isArray(product.imagenes) && product.imagenes.length > 0) {
      if (loadedCount === cart.length) {
        renderCartItems(cart, productsWithImages);
      }
    }
  });
}

// Renderizar productos del carrito CON IMÁGENES
function renderCartItems(cart, productsWithImages) {
  let html = '';
  
  if (productsWithImages.length === 0) {
    html = `
      <tr>
        <td colspan="4" class="text-center py-5">
          <i class="ti ti-alert-circle f-s-48 text-warning mb-3"></i>
          <p>No se pudieron cargar los detalles de los productos</p>
        </td>
      </tr>
    `;
  } else {
    cart.forEach(cartItem => {
      const product = productsWithImages.find(p => p.id == cartItem.productId) || {};
      
      // Determinar la imagen a mostrar
      let productImage = 'assets/images/ecommerce/no-image.jpg';
      
      // 1. Intentar con imagen del producto cargada
      if (product.image) {
        productImage = product.image;
      }
      // 2. Intentar con imagen del carrito
      else if (cartItem.image) {
        productImage = cartItem.image;
      }
      // 3. Intentar con imágenes del producto
      else if (product.imagenes && product.imagenes.length > 0) {
        productImage = product.imagenes[0].ruta;
      }
      
      // Asegurar que la ruta no comience con ../
      if (productImage.startsWith('../')) {
        productImage = productImage.substring(3);
      }
      
      html += `
        <tr data-product-id="${cartItem.productId}">
          <td>
            <div class="d-flex align-items-center">
              <div class="position-relative me-3">
                <img src="../${productImage}" alt="${product.producto_nombre || cartItem.name || 'Producto'}" 
                     class="rounded" width="70" height="70" 
                     style="object-fit: cover;"
                     onerror="this.src='../assets/images/ecommerce/no-image.jpg'">
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                  ${cartItem.quantity}
                </span>
              </div>
              <div>
                <h6 class="mb-0">${product.producto_nombre || cartItem.name || 'Producto no encontrado'}</h6>
                <small class="text-muted">${product.marca || ''}</small>
                <div>
                  <small class="text-muted">Código: ${product.producto_codigo || 'N/A'}</small>
                </div>
              </div>
            </div>
          </td>
          <td>
            <small class="text-muted">${product.producto_descripcion || 'Sin descripción'}</small>
            ${product.puntos ? `<div><small class="text-warning"><i class="ti ti-star me-1"></i>${product.puntos} puntos</small></div>` : ''}
          </td>
          <td>
            <div class="input-group quantity-selector" style="max-width: 140px;">
              <button class="btn btn-outline-secondary decrease-qty" type="button">
                <i class="ti ti-minus"></i>
              </button>
              <input type="number" class="form-control text-center quantity-input" 
                     value="${cartItem.quantity}" min="1">
              <button class="btn btn-outline-secondary increase-qty" type="button">
                <i class="ti ti-plus"></i>
              </button>
            </div>
          </td>
          <td>
            <button class="btn btn-sm btn-danger remove-item">
              <i class="ti ti-trash"></i> Eliminar
            </button>
          </td>
        </tr>
      `;
    });
  }
  
  $('#cart-items-table').html(html);
  
  // Asignar eventos a los botones
  $('.decrease-qty').click(function() {
    const row = $(this).closest('tr');
    const productId = row.data('product-id');
    const input = row.find('.quantity-input');
    let currentQty = parseInt(input.val());
    
    if (currentQty > 1) {
      input.val(currentQty - 1);
      updateCartItemQuantity(productId, currentQty - 1);
    }
  });
  
  $('.increase-qty').click(function() {
    const row = $(this).closest('tr');
    const productId = row.data('product-id');
    const input = row.find('.quantity-input');
    let currentQty = parseInt(input.val());
    
    input.val(currentQty + 1);
    updateCartItemQuantity(productId, currentQty + 1);
  });
  
  $('.quantity-input').on('change', function() {
    const row = $(this).closest('tr');
    const productId = row.data('product-id');
    let currentQty = parseInt($(this).val());
    
    if (isNaN(currentQty)) currentQty = 1;
    if (currentQty < 1) currentQty = 1;
    
    $(this).val(currentQty);
    updateCartItemQuantity(productId, currentQty);
  });
  
  $('.remove-item').click(function() {
    const row = $(this).closest('tr');
    const productId = row.data('product-id');
    const productName = row.find('h6').text().trim();
    
    Swal.fire({
      title: '¿Eliminar producto?',
      text: `¿Estás seguro de que deseas eliminar "${productName}" de tu cotización?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        removeFromCart(productId);
        row.fadeOut(300, function() {
          row.remove();
          if ($('#cart-items-table tr').length === 0) {
            loadCartItems(); // Recargar vista si no hay más productos
          }
        });
      }
    });
  });
}

// Actualizar cantidad de un producto en el carrito
function updateCartItemQuantity(productId, quantity) {
  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  const item = cart.find(item => item.productId == productId);
  
  if (item) {
    item.quantity = quantity;
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    
    // Actualizar el badge en la fila
    $(`tr[data-product-id="${productId}"] .badge`).text(quantity);
  }
}

// Eliminar producto del carrito
function removeFromCart(productId) {
  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  cart = cart.filter(item => item.productId != productId);
  localStorage.setItem('cart', JSON.stringify(cart));
  
  updateCartCount();
  
  // Recargar sugerencias con los nuevos productos
  const productIds = cart.map(item => item.productId);
  if (productIds.length > 0) {
    loadProductSuggestions(productIds);
  } else {
    $('#suggestions-loading').addClass('d-none');
    $('#no-suggestions').removeClass('d-none');
    $('#suggestions-container').addClass('d-none');
  }
}

// Vaciar carrito completamente
function clearCart() {
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  
  if (cart.length === 0) {
    Swal.fire('Carrito vacío', 'No hay productos para eliminar', 'info');
    return;
  }
  
  Swal.fire({
    title: '¿Vaciar carrito?',
    text: "¿Estás seguro de que deseas eliminar todos los productos de tu cotización?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sí, vaciar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      localStorage.setItem('cart', JSON.stringify([]));
      
      updateCartCount();
      loadCartItems(); // Recargar vista
      
      Swal.fire(
        'Carrito vaciado',
        'Todos los productos han sido eliminados',
        'success'
      );
    }
  });
}

// Generar cotización
function generateQuote() {
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  
  if (cart.length === 0) {
    Swal.fire('Error', 'No hay productos en el carrito', 'error');
    return;
  }
  
  // Obtener detalles completos de los productos
  $.ajax({
    url: '../controllers/product_controller.php',
    type: 'GET',
    dataType: 'json',
    success: function(products) {
      prepareQuoteData(cart, products);
    },
    error: function() {
      Swal.fire('Error', 'No se pudieron cargar los detalles de los productos', 'error');
    }
  });
}

// Preparar datos para la cotización
function prepareQuoteData(cart, allProducts) {
  const productsArr = [];
  
  cart.forEach(item => {
    const product = allProducts.find(p => p.id == item.productId);
    
    if (product) {
      productsArr.push([
        product.id,
        product.producto_nombre,
        item.quantity,
        0, // Precio unitario (0 para cotización)
        0  // Precio total (0 para cotización)
      ]);
    }
  });
  
  if (productsArr.length === 0) {
    Swal.fire('Error', 'No se encontraron productos válidos', 'error');
  } else {
    sendQuoteRequest(productsArr);
  }
}

// Enviar solicitud de cotización al servidor
function sendQuoteRequest(productsArr) {
  $('#confirmQuoteModal').modal('hide');
  
  // Mostrar loading mientras se procesa
  Swal.fire({
    title: 'Generando cotización',
    html: 'Por favor espera mientras se procesa tu solicitud...',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });
  
  // Obtener fecha actual en formato YYYY-MM-DD
  const today = new Date();
  const fecha = today.toISOString().split('T')[0];
  const id_cliente = $('#id_cliente').val();
  
  // Enviar datos al servicio de cotización
  $.ajax({
    url: '../controllers/cotizacion_service.php',
    type: 'POST',
    dataType: 'json',
    data: {
      productos: JSON.stringify(productsArr),
      id_cliente: id_cliente,
      fecha: fecha,
      'tipo': "cliente"
    },
    success: function(response) {
      Swal.close();
      
      if (response.success) {
        // Mostrar modal de éxito
        $('#quote-details').html(`
          <div class="text-start">
            <p><strong>Número de cotización:</strong> ${response.document_number}</p>
            <p><strong>Cliente:</strong> ${response.client_name}</p>
            <p><strong>Fecha:</strong> ${fecha}</p>
            <p><strong>Productos:</strong> ${productsArr.length}</p>
          </div>
        `);
        $('#successModal').modal('show');
        
        // Vaciar el carrito después de generar la cotización
        localStorage.setItem('cart', JSON.stringify([]));
        
        updateCartCount();
        
        // Recargar la vista del carrito
        loadCartItems();
      } else {
        Swal.fire('Error', response.error || 'Ocurrió un error al generar la cotización', 'error');
      }
    },
    error: function(xhr, status, error) {
      Swal.close();
      Swal.fire('Error', 'Error en la conexión con el servidor: ' + error, 'error');
    }
  });
}

// Función para cargar sugerencias de productos
function loadProductSuggestions(productIds) {
  $('#suggestions-loading').removeClass('d-none');
  $('#suggestions-container').addClass('d-none');
  $('#no-suggestions').addClass('d-none');

  $.ajax({
    url: '../controllers/suggestions_controller.php',
    type: 'POST',
    dataType: 'json',
    data: JSON.stringify({ productIds: productIds }),
    contentType: 'application/json',
    success: function(response) {
      $('#suggestions-loading').addClass('d-none');
      
      if (response.success && response.suggestions && response.suggestions.length > 0) {
        renderProductSuggestions(response.suggestions);
        $('#suggestions-container').removeClass('d-none');
      } else {
        $('#no-suggestions').removeClass('d-none');
      }
    },
    error: function() {
      $('#suggestions-loading').addClass('d-none');
      $('#no-suggestions').removeClass('d-none');
    }
  });
}

// Función para renderizar las sugerencias de productos
function renderProductSuggestions(suggestions) {
  const $suggestionsRow = $('#suggestions-row');
  $suggestionsRow.empty();
  
  suggestions.forEach(product => {
    // Obtener la imagen del producto
    let imageUrl = 'assets/images/ecommerce/no-image.jpg';
    if (product.imagen_principal) {
      imageUrl = product.imagen_principal;
      if (imageUrl.startsWith('../')) {
        imageUrl = imageUrl.substring(3);
      }
    }
    
    const productCard = `
      <div class="col-md-4 col-sm-6 mb-3">
        <div class="card h-100 suggestion-card" data-product-id="${product.id}">
          <div class="card-body">
            <div class="d-flex flex-column h-100">
              <div class="text-center mb-3">
                <img src="../${imageUrl}" alt="${product.producto_nombre}" 
                     class="rounded" width="120" height="120" 
                     style="object-fit: cover;"
                     onerror="this.src='../assets/images/ecommerce/no-image.jpg'">
              </div>
              <h6 class="mb-1">${product.producto_nombre}</h6>
              <small class="text-muted mb-2">${product.marca || 'Marca no especificada'}</small>
              <p class="small text-muted flex-grow-1 mb-3">${product.producto_descripcion || 'Sin descripción disponible'}</p>
              <div class="mt-auto">
                <button class="btn btn-sm btn-primary w-100 add-suggestion" 
                        data-product-id="${product.id}"
                        data-product-name="${product.producto_nombre}">
                  <i class="ti ti-plus me-1"></i> Agregar a cotización
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
    
    $suggestionsRow.append(productCard);
  });
  
  // Asignar evento a los botones de agregar
  $('.add-suggestion').click(function() {
    const productId = $(this).data('product-id');
    const productName = $(this).data('product-name');
    addSuggestedProductToCart(productId, productName);
  });
}

// Función para agregar un producto sugerido al carrito
function addSuggestedProductToCart(productId, productName) {
  // Mostrar loading
  const $btn = $(`.add-suggestion[data-product-id="${productId}"]`);
  const originalText = $btn.html();
  $btn.prop('disabled', true).html('<i class="ti ti-loader me-1"></i> Agregando...');
  
  // Obtener detalles del producto desde la API principal
  $.ajax({
    url: '../controllers/product_controller.php',
    type: 'GET',
    data: { id: productId },
    dataType: 'json',
    success: function(product) {
      if (product && product.id) {
        // Obtener imagen del producto
        let productImage = 'assets/images/ecommerce/no-image.jpg';
        if (product.imagenes && product.imagenes.length > 0) {
          productImage = product.imagenes[0].ruta;
        }
        
        // Agregar al carrito
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        // Verificar si el producto ya está en el carrito
        const existingItem = cart.find(item => item.productId == product.id);
        
        if (existingItem) {
          existingItem.quantity += 1;
        } else {
          cart.push({
            productId: product.id,
            quantity: 1,
            name: product.producto_nombre,
            image: productImage
          });
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Actualizar contador
        updateCartCount();
        
        // Recargar la vista del carrito
        loadCartItems();
        
        // Mostrar feedback
        Swal.fire({
          icon: 'success',
          title: 'Producto agregado',
          text: `${product.producto_nombre} ha sido agregado a tu cotización`,
          timer: 1500,
          showConfirmButton: false
        });
        
        // Restaurar botón
        $btn.prop('disabled', false).html(originalText);
      } else {
        Swal.fire('Error', 'No se pudo obtener la información del producto', 'error');
        $btn.prop('disabled', false).html(originalText);
      }
    },
    error: function() {
      Swal.fire('Error', 'Error al cargar el producto', 'error');
      $btn.prop('disabled', false).html(originalText);
    }
  });
}

</script>

<style>
.quantity-selector .btn {
  padding: 0.25rem 0.5rem;
}

.quantity-input {
  max-width: 50px;
  text-align: center;
}

#generate-quote-btn:disabled {
  opacity: 0.65;
  pointer-events: none;
}

.table img {
  object-fit: cover;
}

#successModal .modal-header {
  border-bottom: none;
}

#successModal .modal-footer {
  border-top: none;
}

.suggestion-card {
  transition: transform 0.2s, box-shadow 0.2s;
  border: 1px solid #e9ecef;
}

.suggestion-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

#suggestions-loading .spinner-border {
  width: 2rem;
  height: 2rem;
}

#cart-count {
  font-size: 0.9rem;
  padding: 0.35rem 0.65rem;
}

/* Mejoras visuales para las imágenes del carrito */
.table tbody tr:hover {
  background-color: rgba(0, 123, 255, 0.05);
}

.table tbody tr td:first-child {
  border-left: 3px solid transparent;
}

.table tbody tr:hover td:first-child {
  border-left-color: #0d6efd;
}

.position-relative .badge {
  font-size: 0.7rem;
  padding: 0.25rem 0.5rem;
}

/* Estilo para cuando no hay imágenes */
img[src*="no-image.jpg"] {
  opacity: 0.7;
  background-color: #f8f9fa;
  padding: 10px;
  border-radius: 8px;
}
</style>