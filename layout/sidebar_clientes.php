<?php include("../controllers/conx.php"); include("../controllers/funciones.php");?>

<!-- Menu Navigation starts opcion:horizontal-sidebar -->
<nav class="dark-sidebar horizontal-sidebar">
    <div class="app-logo">
        <a class="logo d-inline-block" href="tienda.php">
            <img src="../assets/images/logo/1.png" alt="#">
        </a>

        <span class="bg-light-primary toggle-semi-nav">
          <i class="ti ti-chevrons-right f-s-20"></i>
      </span>
    </div>
    <div class="app-nav" id="app-simple-bar">
        <ul class="main-nav p-0 mt-2">
           
            <!-- Enlaces estáticos de la tienda -->
            <li class="no-sub">
                <a class="" href="tienda.php">
                    <i class="ph-duotone ph-house"></i> Inicio
                </a>
            </li>
            
            <li class="no-sub">
                <a class="" href="nosotros.php">
                    <i class="ph-duotone ph-users-three"></i> Nosotros
                </a>
            </li>
            
            
            
            <li class="no-sub">
                <a class="" href="contacto.php">
                    <i class="ph-duotone ph-phone"></i> Contacto
                </a>
            </li>
            
            <li class="no-sub">
                <a class="" href="terminos.php">
                    <i class="ph-duotone ph-file-text"></i> Términos y Condiciones
                </a>
            </li>
            
            <li class="no-sub">
                <a class="" href="preguntas.php">
                    <i class="ph-duotone ph-question"></i> Preguntas Frecuentes
                </a>
            </li>
            
            <li class="no-sub">
                <a class="" href="soporte.php">
                    <i class="ph-duotone ph-headset"></i> Soporte
                </a>
            </li>

            <!-- Menús dinámicos originales -->
            <li>
                <a class="" data-bs-toggle="collapse" href="#operaciones" aria-expanded="false">
                    <i class="ph-duotone ph-briefcase"></i>
                    OPERACIONES
                </a>
               
                <?php
                    $link=conectarse();
                    $sql="SELECT `id`, `texto`, `enlace`, `padre` FROM `menu` order by `texto`";
                    printf ('<ul class="collapse" id="operaciones">');
                    $result2= mysqli_query($link, $sql);
                    while($row = mysqli_fetch_array($result2,MYSQLI_ASSOC)) {
                        if($row["padre"]=="2"){
                            if(tieneAccesoAlMenu($row["id"])){
                                printf ('<li><a href="%s">%s</a></li>',$row["enlace"], $row["texto"]);
                            }
                        }
                    }
                    printf ('</ul>');
                ?>
            </li>

            <li>
                <a class="" data-bs-toggle="collapse" href="#configuraciones" aria-expanded="false">
                    <i class="ph-duotone ph-briefcase-metal"></i> CONFIGURACIONES
                </a>
                <?php
                    printf ('<ul class="collapse" id="configuraciones">');
                    $result2= mysqli_query($link, $sql);
                    while($row = mysqli_fetch_array($result2,MYSQLI_ASSOC)) {
                        if($row["padre"]=="1"){
                            if(tieneAccesoAlMenu($row["id"])){
                                printf ('<li><a href="%s">%s</a></li>',$row["enlace"], $row["texto"]);
                            }
                        }
                    }
                    printf ('</ul>');
                ?>
            </li>
            
            <li>
                <a class="" data-bs-toggle="collapse" href="#reportes" aria-expanded="false">
                    <i class="ph-duotone ph-shapes"></i> REPORTES
                </a>
                <?php
                    printf ('<ul class="collapse" id="reportes">');
                    $result2= mysqli_query($link, $sql);
                    while($row = mysqli_fetch_array($result2,MYSQLI_ASSOC)) {
                        if($row["padre"]=="3"){
                            if(tieneAccesoAlMenu($row["id"])){
                                printf ('<li><a href="%s">%s</a></li>',$row["enlace"], $row["texto"]);
                            }
                        }
                    }
                    printf ('</ul>');
                ?>
            </li>
        </ul>
    </div>

    <div class="menu-navs">
        <span class="menu-previous"><i class="ti ti-chevron-left"></i></span>
        <span class="menu-next"><i class="ti ti-chevron-right"></i></span>
    </div>
</nav>