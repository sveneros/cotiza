<?php
include('../layout/header_clientes.php');
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

  <!-- Cart content start -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5>Productos en cotización</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Detalles</th>
                  <th>Cantidad</th>
                  <th>Acciones</th>
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
            <div class="spinner-border text-primary"></div>
            <p>Analizando sugerencias...</p>
          </div>
          <div id="suggestions-container" class="d-none">
            <div class="row" id="suggestions-row">
              <!-- Las sugerencias se cargarán aquí -->
            </div>
          </div>
          <div id="no-suggestions" class="text-center py-3 d-none">
            <i class="ti ti-info-circle f-s-48 text-muted mb-3"></i>
            <p>No hay sugerencias disponibles en este momento</p>
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
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<?php
include('../layout/footer.php');
?>

<script>
$(document).ready(function() {
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

// Cargar productos del carrito
function loadCartItems() {
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  // Cargar sugerencias basadas en los productos del carrito
  const productIds = cart.map(item => item.productId);
  loadProductSuggestions(productIds);
  
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
    $('#generate-quote-btn').prop('disabled', true);
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
  
  // Obtener detalles completos de los productos
  $.ajax({
    url: '../controllers/product_controller.php',
    type: 'GET',
    dataType: 'json',
    success: function(products) {
      renderCartItems(cart, products);
    },
    error: function() {
      $('#cart-items-table').html(`
        <tr>
          <td colspan="4" class="text-center py-5 text-danger">
            Error al cargar los productos del carrito
          </td>
        </tr>
      `);
    }
  });
}

// Renderizar productos del carrito
function renderCartItems(cart, allProducts) {
  let html = '';
  
  cart.forEach(item => {
    const product = allProducts.find(p => p.id == item.productId) || {};
    const image = item.image || 'assets/images/ecommerce/no-image.jpg';
    
    html += `
      <tr data-product-id="${item.productId}">
        <td>
          <div class="d-flex align-items-center">
            <img src="../${image}" alt="${product.producto_nombre || 'Producto'}" 
                 class="rounded me-3" width="60" height="60" onerror="this.src='../assets/images/ecommerce/no-image.jpg'">
            <div>
              <h6 class="mb-0">${product.producto_nombre || 'Producto no encontrado'}</h6>
              <small class="text-muted">${product.marca || ''}</small>
            </div>
          </div>
        </td>
        <td>
          <small class="text-muted">${product.producto_descripcion || 'Sin descripción'}</small>
        </td>
        <td>
          <div class="input-group quantity-selector" style="max-width: 120px;">
            <button class="btn btn-outline-secondary decrease-qty" type="button">
              <i class="ti ti-minus"></i>
            </button>
            <input type="number" class="form-control text-center quantity-input" 
                   value="${item.quantity}" min="1">
            <button class="btn btn-outline-secondary increase-qty" type="button">
              <i class="ti ti-plus"></i>
            </button>
          </div>
        </td>
        <td>
          <button class="btn btn-sm btn-danger remove-item">
            <i class="ti ti-trash"></i>
          </button>
        </td>
      </tr>
    `;
  });
  
  $('#cart-items-table').html(html);
  $('#generate-quote-btn').prop('disabled', false);
  
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
    
    Swal.fire({
      title: '¿Eliminar producto?',
      text: "¿Estás seguro de que deseas eliminar este producto de tu cotización?",
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
  }
}

// Eliminar producto del carrito
function removeFromCart(productId) {
  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  cart = cart.filter(item => item.productId != productId);
  localStorage.setItem('cart', JSON.stringify(cart));
  
  // Actualizar contador en el header si existe
  if ($('.header-cart .badge-notification').length) {
    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
    $('.header-cart .badge-notification').text(totalItems);
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
      
      // Actualizar contador en el header si existe
      if ($('.header-cart .badge-notification').length) {
        $('.header-cart .badge-notification').text('0');
      }
      
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
  
  // Obtener detalles completos de los productos para los precios
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
      // Aquí puedes incluir el precio si es necesario, aunque dijiste que no es requerido
      // En este ejemplo lo dejamos como 0 ya que es para cotización
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
  id_cliente = $('#id_cliente').val();
  
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
          <p>Número de cotización: <strong>${response.document_number}</strong></p>
          <p>Cliente: <strong>${response.client_name}</strong></p>
          <p>Fecha: <strong>${fecha}</strong></p>
        `);
        $('#successModal').modal('show');
        
        // Vaciar el carrito después de generar la cotización
        localStorage.setItem('cart', JSON.stringify([]));
        
        // Actualizar contador en el header si existe
        if ($('.header-cart .badge-notification').length) {
          $('.header-cart .badge-notification').text('0');
        }
        
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
  if (productIds.length === 0) {
    $('#suggestions-loading').addClass('d-none');
    $('#no-suggestions').removeClass('d-none');
    return;
  }

  $.ajax({
    url: '../controllers/suggestions_controller.php',
    type: 'POST',
    dataType: 'json',
    data: JSON.stringify({ productIds: productIds }),
    contentType: 'application/json',
    success: function(response) {
      $('#suggestions-loading').addClass('d-none');
      
      if (response.success && response.suggestions.length > 0) {
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
    const imageUrl = '../assets/images/ecommerce/no-image.jpg';
    
    $suggestionsRow.append(`
      <div class="col-md-4 mb-3">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex flex-column h-100">
              <div class="text-center mb-3">
                <img src="${imageUrl}" alt="${product.producto_nombre}" 
                     class="rounded" width="120" height="120" 
                     onerror="this.src='../assets/images/ecommerce/no-image.jpg'">
              </div>
              <h6 class="mb-1">${product.producto_nombre}</h6>
              <small class="text-muted mb-2">${product.marca || 'Marca no especificada'}</small>
              <p class="small text-muted flex-grow-1">${product.producto_descripcion || 'Sin descripción disponible'}</p>
              <button class="btn btn-sm btn-primary add-suggestion" data-product-id="${product.id}">
                <i class="ti ti-plus me-1"></i> Agregar a cotización
              </button>
            </div>
          </div>
        </div>
      </div>
    `);
  });
  
  // Asignar evento a los botones de agregar
  $('.add-suggestion').click(function() {
    const productId = $(this).data('product-id');
    addSuggestedProductToCart(productId);
  });
}

// Función para agregar un producto sugerido al carrito
function addSuggestedProductToCart(productId) {
  // Mostrar loading
  const $btn = $(`.add-suggestion[data-product-id="${productId}"]`);
  $btn.prop('disabled', true).html('<i class="ti ti-loader me-1"></i> Agregando...');
  
  // Obtener detalles del producto
  $.ajax({
    url: '../controllers/product_controller.php',
    type: 'GET',
    data: { id: productId },
    dataType: 'json',
    success: function(product) {
      if (product && product.id) {
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
            image: product.imagenes && product.imagenes.length > 0 ? product.imagenes[0].ruta : null
          });
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Actualizar contador en el header si existe
        if ($('.header-cart .badge-notification').length) {
          const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
          $('.header-cart .badge-notification').text(totalItems);
        }
        
        // Mostrar feedback
        Swal.fire({
          icon: 'success',
          title: 'Producto agregado',
          text: `${product.producto_nombre} ha sido agregado a tu cotización`,
          timer: 1500,
          showConfirmButton: false
        });
        
        // Recargar el carrito
        loadCartItems();
        
        // Actualizar sugerencias
        const productIds = cart.map(item => item.productId);
        loadProductSuggestions(productIds);
      } else {
        Swal.fire('Error', 'No se pudo obtener la información del producto', 'error');
        $btn.prop('disabled', false).html('<i class="ti ti-plus me-1"></i> Agregar a cotización');
      }
    },
    error: function() {
      Swal.fire('Error', 'Error al cargar el producto', 'error');
      $btn.prop('disabled', false).html('<i class="ti ti-plus me-1"></i> Agregar a cotización');
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
  justify-content: center;
}
</style>