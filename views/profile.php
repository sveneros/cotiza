
<?php
        include('../layout/header.php');
        ?>

      
            <div class="container-fluid">
                <!-- Breadcrumb start -->
                <div class="row m-1">
                    <div class="col-12 ">
                        <h4 class="main-title">Inicio</h4>
                        <ul class="app-line-breadcrumbs mb-3">
                            <li class="">
                                <a href="#" class="f-s-14 f-w-500">
                      <span>
                        <i class="ph-duotone  ph-newspaper f-s-16"></i> IKNOW
                      </span>
                                </a>
                            </li>
                            <li class="active">
                                <a href="#" class="f-s-14 f-w-500">Inicio</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Breadcrumb end -->

                <!-- Blank start -->
                <div class="row">
                    
                    <div class="col-md-5 col-lg-4 col-xxl-3 order--3-lg">
                        <div class="card education-profile-card">
                            <div class="card-body">
                                <div class="profile-header">
                                <h5 class="header-title-text">Perfil</h5>
                                </div>
                                <div class="profile-top-content">
                                <div class="h-80 w-80 d-flex-center b-r-50 overflow-hidden">
                                    <img src="../assets/images/avtar/woman.jpg" alt="" class="img-fluid">
                                </div>
                                <h6 class="text-dark f-w-600 mb-0"><?php if (isset($_SESSION['sml2020_svenerossys_nombre_usuario_registrado'])) echo ($_SESSION['sml2020_svenerossys_nombre_usuario_registrado']);?></h6>
                                <p class="text-secondary f-s-13 mb-0"><?php if (isset($_SESSION['sml2020_svenerossys_nombre_usuario_registrado']))echo ($_SESSION['sml2020_svenerossys_email_usuario_registrado']);?></p>
                                <div>
                                    <button type="button" class="btn btn-primary">Cambiar password</button>
                                    
                                </div>
                                
                                </div>
                                <div class="profile-content-box">
                                <div class="profile-details">
                                    <p class="f-s-18 mb-0"><i class="ph-bold  ph-clock-countdown"></i></p>
                                    <span class="badge text-light-primary">45H</span>
                                </div>
                                <div class="profile-details">
                                    <p class="f-s-18 mb-0"><i class="ph-fill  ph-book-bookmark"></i></p>
                                    <span class="badge text-light-secondary">10</span>
                                </div>
                                <div class="profile-details">
                                    <p class="f-s-18 mb-0"><i class="ph-fill  ph-seal-check"></i></p>
                                    <span class="badge text-light-success">2K</span>
                                </div>
                                <div class="profile-details">
                                    <p class="f-s-18 mb-0"><i class="ph-fill  ph-user-circle"></i></p>
                                    <span class="badge text-light-info">34K</span>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <!-- Blank end -->
            </div>
     
    <?php
    include('../layout/footer.php');
    ?>
