<?php
include('../layout/header_clientes.php');
?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title">Contacto</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="tienda.php" class="f-s-14 f-w-500">
            <span>
              <i class="ph-duotone ph-house f-s-16"></i> Inicio
            </span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Contacto</a>
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
              <h3 class="mb-4">Información de Contacto</h3>
              <div class="d-flex align-items-start mb-4">
                <div class="me-3">
                  <i class="ph-duotone ph-map-pin f-s-32 text-primary"></i>
                </div>
                <div>
                  <h5 class="mb-1">Dirección</h5>
                  <p class="mb-0 text-secondary">Av. Principal #123, Ciudad, País</p>
                </div>
              </div>
              
              <div class="d-flex align-items-start mb-4">
                <div class="me-3">
                  <i class="ph-duotone ph-phone f-s-32 text-primary"></i>
                </div>
                <div>
                  <h5 class="mb-1">Teléfonos</h5>
                  <p class="mb-0 text-secondary">+123 456 7890</p>
                  <p class="mb-0 text-secondary">+123 456 7891</p>
                </div>
              </div>
              
              <div class="d-flex align-items-start mb-4">
                <div class="me-3">
                  <i class="ph-duotone ph-envelope f-s-32 text-primary"></i>
                </div>
                <div>
                  <h5 class="mb-1">Correo Electrónico</h5>
                  <p class="mb-0 text-secondary">info@mitienda.com</p>
                  <p class="mb-0 text-secondary">ventas@mitienda.com</p>
                </div>
              </div>
              
              <div class="d-flex align-items-start mb-4">
                <div class="me-3">
                  <i class="ph-duotone ph-clock f-s-32 text-primary"></i>
                </div>
                <div>
                  <h5 class="mb-1">Horario de Atención</h5>
                  <p class="mb-0 text-secondary">Lunes a Viernes: 8:00 - 18:00</p>
                  <p class="mb-0 text-secondary">Sábados: 9:00 - 14:00</p>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <h3 class="mb-4">Formulario de Contacto</h3>
              <form id="contactForm">
                <div class="mb-3">
                  <label for="name" class="form-label">Nombre Completo</label>
                  <input type="text" class="form-control" id="name" required>
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label">Correo Electrónico</label>
                  <input type="email" class="form-control" id="email" required>
                </div>
                <div class="mb-3">
                  <label for="phone" class="form-label">Teléfono</label>
                  <input type="tel" class="form-control" id="phone">
                </div>
                <div class="mb-3">
                  <label for="subject" class="form-label">Asunto</label>
                  <select class="form-select" id="subject" required>
                    <option value="" selected disabled>Seleccione un asunto</option>
                    <option value="Consulta">Consulta</option>
                    <option value="Soporte">Soporte Técnico</option>
                    <option value="Ventas">Información de Ventas</option>
                    <option value="Otro">Otro</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="message" class="form-label">Mensaje</label>
                  <textarea class="form-control" id="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
              </form>
            </div>
          </div>
          
          <div class="row mt-5">
            <div class="col-12">
              <h3 class="mb-4">Ubicación</h3>
              <div class="ratio ratio-16x9">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12345.678901234567!2d-123.456789!3d12.3456789!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTLCsDIwJzQ0LjQiTiAxMjPCsDI3JzIxLjEiVw!5e0!3m2!1sen!2sus!4v1234567890123!5m2!1sen!2sus" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
              </div>
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
  $('#contactForm').submit(function(e) {
    e.preventDefault();
    
    // Aquí puedes agregar el código para enviar el formulario por AJAX
    Swal.fire({
      icon: 'success',
      title: 'Mensaje enviado',
      text: 'Gracias por contactarnos. Nos pondremos en contacto contigo pronto.',
      confirmButtonText: 'Aceptar'
    });
    
    // Limpiar el formulario
    this.reset();
  });
});
</script>

<?php
include('../layout/footer.php');
?>