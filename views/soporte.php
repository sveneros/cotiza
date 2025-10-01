<?php
include('../layout/header_clientes.php');
?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title">Soporte Técnico</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="tienda.php" class="f-s-14 f-w-500">
            <span>
              <i class="ph-duotone ph-house f-s-16"></i> Inicio
            </span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Soporte</a>
        </li>
      </ul>
    </div>
  </div>
  <!-- Breadcrumb end -->

  <!-- Content start -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <h3 class="mb-4">Soporte Técnico</h3>
              <p class="f-s-16">Nuestro equipo de soporte está disponible para ayudarte con cualquier problema técnico que puedas tener con nuestros productos o servicios.</p>
              
              <div class="support-options mt-4">
                <div class="d-flex align-items-start mb-4">
                  <div class="me-3">
                    <i class="ph-duotone ph-headset f-s-32 text-primary"></i>
                  </div>
                  <div>
                    <h5 class="mb-1">Soporte por Teléfono</h5>
                    <p class="mb-2 text-secondary">+123 456 7890</p>
                    <p class="mb-0 text-muted f-s-14">Lunes a Viernes: 8:00 - 18:00</p>
                  </div>
                </div>
                
                <div class="d-flex align-items-start mb-4">
                  <div class="me-3">
                    <i class="ph-duotone ph-envelope f-s-32 text-primary"></i>
                  </div>
                  <div>
                    <h5 class="mb-1">Soporte por Email</h5>
                    <p class="mb-2 text-secondary">soporte@mitienda.com</p>
                    <p class="mb-0 text-muted f-s-14">Respuesta en menos de 24 horas</p>
                  </div>
                </div>
                
                <div class="d-flex align-items-start mb-4">
                  <div class="me-3">
                    <i class="ph-duotone ph-chat-circle-text f-s-32 text-primary"></i>
                  </div>
                  <div>
                    <h5 class="mb-1">Chat en Vivo</h5>
                    <p class="mb-2 text-secondary">Disponible en horario de oficina</p>
                    <button class="btn btn-sm btn-primary" id="startChatBtn">Iniciar Chat</button>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <h3 class="mb-4">Reportar un Problema</h3>
              <form id="supportForm">
                <div class="mb-3">
                  <label for="supportName" class="form-label">Nombre Completo</label>
                  <input type="text" class="form-control" id="supportName" required>
                </div>
                <div class="mb-3">
                  <label for="supportEmail" class="form-label">Correo Electrónico</label>
                  <input type="email" class="form-control" id="supportEmail" required>
                </div>
                <div class="mb-3">
                  <label for="supportOrder" class="form-label">Número de Pedido (opcional)</label>
                  <input type="text" class="form-control" id="supportOrder">
                </div>
                <div class="mb-3">
                  <label for="supportIssue" class="form-label">Tipo de Problema</label>
                  <select class="form-select" id="supportIssue" required>
                    <option value="" selected disabled>Seleccione un tipo</option>
                    <option value="Technical">Problema Técnico</option>
                    <option value="Delivery">Problema con Entrega</option>
                    <option value="Product">Problema con Producto</option>
                    <option value="Payment">Problema con Pago</option>
                    <option value="Other">Otro</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="supportDescription" class="form-label">Descripción del Problema</label>
                  <textarea class="form-control" id="supportDescription" rows="5" required></textarea>
                  <div class="form-text">Por favor, describa el problema con el mayor detalle posible.</div>
                </div>
                <div class="mb-3">
                  <label for="supportFile" class="form-label">Adjuntar Archivo (opcional)</label>
                  <input type="file" class="form-control" id="supportFile">
                  <div class="form-text">Puede adjuntar imágenes o documentos relacionados con el problema (máx. 5MB).</div>
                </div>
                <button type="submit" class="btn btn-primary">Enviar Reporte</button>
              </form>
            </div>
          </div>
          
          <div class="row mt-5">
            <div class="col-12">
              <h3 class="mb-4">Preguntas Frecuentes</h3>
              <p>¿Tiene alguna pregunta común? Consulte nuestra sección de <a href="preguntas.php" class="text-primary">preguntas frecuentes</a> antes de contactarnos.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Content end -->
</div>

<script>
$(document).ready(function() {
  $('#supportForm').submit(function(e) {
    e.preventDefault();
    
    // Aquí puedes agregar el código para enviar el formulario por AJAX
    Swal.fire({
      icon: 'success',
      title: 'Reporte enviado',
      text: 'Hemos recibido su reporte. Nuestro equipo de soporte se pondrá en contacto con usted pronto.',
      confirmButtonText: 'Aceptar'
    });
    
    // Limpiar el formulario
    this.reset();
  });
  
  $('#startChatBtn').click(function() {
    Swal.fire({
      icon: 'info',
      title: 'Chat en Vivo',
      text: 'El servicio de chat en vivo está disponible de Lunes a Viernes de 8:00 a 18:00. Por favor, intente durante ese horario.',
      confirmButtonText: 'Entendido'
    });
  });
});
</script>

<?php
include('../layout/footer.php');
?>