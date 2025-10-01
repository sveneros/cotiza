<?php
include('../layout/header_clientes.php');
?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title">Sobre Nosotros</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="tienda.php" class="f-s-14 f-w-500">
            <span>
              <i class="ph-duotone ph-house f-s-16"></i> Inicio
            </span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Nosotros</a>
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
          <div class="row align-items-center">
            <div class="col-md-6">
              <h2 class="mb-3">Nuestra Historia</h2>
              <p class="f-s-16">Somos una empresa comprometida con la calidad y el servicio al cliente desde nuestro inicio en 2010. Nuestra misión es proporcionar productos de alta calidad a precios competitivos.</p>
              <p class="f-s-16">Hemos crecido gracias a la confianza de nuestros clientes y seguimos innovando para ofrecer la mejor experiencia de compra.</p>
              
              <h4 class="mt-4 mb-3">Nuestros Valores</h4>
              <ul class="list-group list-group-flush">
                <li class="list-group-item border-0 ps-0"><i class="ph-duotone ph-check-circle text-primary me-2"></i> Calidad en nuestros productos</li>
                <li class="list-group-item border-0 ps-0"><i class="ph-duotone ph-check-circle text-primary me-2"></i> Servicio al cliente excepcional</li>
                <li class="list-group-item border-0 ps-0"><i class="ph-duotone ph-check-circle text-primary me-2"></i> Innovación constante</li>
                <li class="list-group-item border-0 ps-0"><i class="ph-duotone ph-check-circle text-primary me-2"></i> Responsabilidad social</li>
              </ul>
            </div>
            <div class="col-md-6">
              <img src="../assets/images/about-us.avif" class="img-fluid rounded" alt="Nuestro equipo">
            </div>
          </div>
          
          <div class="row mt-5">
            <div class="col-12">
              <h3 class="mb-4">Nuestro Equipo</h3>
              <div class="row">
                <div class="col-md-3 col-sm-6 mb-4">
                  <div class="card text-center border-0">
                    <img src="../assets/images/team/1.jpg" class="card-img-top rounded-circle mx-auto mt-3" style="width: 150px; height: 150px; object-fit: cover;" alt="Miembro del equipo">
                    <div class="card-body">
                      <h5 class="card-title">Juan Pérez</h5>
                      <p class="card-text text-secondary">CEO & Fundador</p>
                    </div>
                  </div>
                </div>
                <!-- Repetir para otros miembros del equipo -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Content end -->
</div>

<?php
include('../layout/footer.php');
?>