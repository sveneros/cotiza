<?php
include('../layout/head.php');
include('../layout/css.php');
?>


<body text="medium-text">

<div class="app-wrapper">
    <!-- app loader -->
    <div class="loader-wrapper">
        <div class="loader_16"></div>
    </div>

    <?php
    include('../layout/sidebar_clientes.php');
    ?>

    <div class="app-content">
        
<!-- Header Section starts -->
<header class="header-main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-6 col-sm-4 d-flex align-items-center header-left p-0">
                <span class="header-toggle me-3">
                  <i class="ph ph-circles-four"></i>
                </span>
            </div>

            <div class="col-6 col-sm-8 d-flex align-items-center justify-content-end header-right p-0">

                <ul class="d-flex align-items-center">
                    <!-- Enlace a la tienda -->
                    <li class="header-cart">
                    <a href="#" class="d-block head-icon position-relative" role="button" data-bs-toggle="offcanvas"
                       data-bs-target="#cartcanvasRight" aria-controls="cartcanvasRight" id="btnActualizarCarrito">
                      <i class="ph ph-shopping-cart-simple"></i>
                      <span
                              class="position-absolute translate-middle badge rounded-pill bg-danger badge-notification">4</span>
                    </a>
                    <div class="offcanvas offcanvas-end header-cart-canvas" tabindex="-1" id="cartcanvasRight"
                         aria-labelledby="cartcanvasRightLabel">
                        <div class="offcanvas-header">
                          <h5 class="offcanvas-title" id="cartcanvasRightLabel">Cart</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body app-scroll p-0">
                            <button type="button" class="btn btn-light-primary btn-close" data-bs-dismiss="offcanvas">
                                 
                            </button>
                          <div id="cart-items">
                            <p class="text-center py-5">Tu carrito está vacío</p>
                          </div>
                        </div>
                          
                        <div class="offcanvas-footer">
                            <div class="head-box-footer p-3">
                              <!-- <div class="mb-4">
                                <h6 class="text-muted f-w-600">Total <span class="float-end" id="cart-subtotal">Bs. 0.00</span></h6>
                              </div> -->
                              <div class="header-cart-btn">
                                
                                <a href="cotizar.php" class="btn btn-light-success" id="checkout-btn">
                                  Proceder a Cotizar <i class="ti ti-shopping-cart"></i>
                                </a>
                              </div>
                            </div>
                        </div>
                    </div>
                  </li>

                    <li class="header-dark">
                        <div class="sun-logo head-icon">
                            <!-- <i class="ph ph-moon-stars"></i> -->
                        </div>
                        <div class="moon-logo head-icon">
                            <!-- <i class="ph ph-sun-dim"></i> -->
                        </div>
                    </li>

                    <li class="header-profile">
                        
                        <a href="#" class="d-block head-icon" role="button" data-bs-toggle="offcanvas"
                           data-bs-target="#profilecanvasRight" aria-controls="profilecanvasRight">
                            <img src="../assets/images/avtar/woman.jpg" alt="avtar"
                                 class="b-r-10 h-35 w-35">
                        </a>

                        <div class="offcanvas offcanvas-end header-profile-canvas" tabindex="-1"
                             id="profilecanvasRight" aria-labelledby="profilecanvasRight">
                             <div class="offcanvas-header">
                              <h5 class="offcanvas-title" id="cartc8sdf78as7">Perfil</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body app-scroll">
                                <ul class="">
                                    <li>
                                        <div class="d-flex-center">
                              <span
                                      class="h-45 w-45 d-flex-center b-r-10 position-relative">
                                <img src="../assets/images/avtar/woman.jpg" alt=""
                                     class="img-fluid b-r-10">
                              </span>
                                        </div>
                                        <div class="text-center mt-2">
                                            <h6 class="mb-0"> <?php if (isset($_SESSION['sml2020_svenerossys_nombre_usuario_registrado'])) echo ($_SESSION['sml2020_svenerossys_nombre_usuario_registrado']);?></h6>
                                            <p class="f-s-12 mb-0 text-secondary"><?php if (isset($_SESSION['sml2020_svenerossys_nombre_usuario_registrado']))echo ($_SESSION['sml2020_svenerossys_email_usuario_registrado']);?>
                                            <input type="hidden" id="id_cliente" value="<?php  echo ($_SESSION['sml2020_svenerossys_id_cliente_usuario_registrado']);?>">
                                            </p>
                                        </div>
                                    </li>

                                    <li class="app-divider-v dotted py-1"></li>
                                    <li>
                                        <a class="f-w-500" href="./profile.php" target="_self">
                                            <i class="ph-duotone  ph-user-circle pe-1 f-s-20"></i>
                                            Perfil de usuario
                                        </a>
                                    </li>
                                    <li>
                                        <a class="f-w-500" href="./logout.php" target="_self">
                                            <i class="ph-bold  ph-lock-open pe-1 f-s-20"></i> Cerrar Sesión
                                        </a>
                                    </li>
                                   
                                    <li class="app-divider-v dotted py-1"></li>
                                    <!-- <li>
                                        <a class="f-w-500" href="./faq.php" target="_self">
                                            <i class="ph-duotone  ph-question pe-1 f-s-20"></i> Help
                                        </a>
                                    </li> -->
                                    
                                    
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>



<!-- main section -->
<main>