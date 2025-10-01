<?php include("../controllers/conx.php"); include("../controllers/funciones.php");?>

<!-- Menu Navigation starts opcion:horizontal-sidebar -->
<nav class="dark-sidebar semi-nav">
    <div class="app-logo">
        <a class="logo d-inline-block" href="index.php">
            <img src="../assets/images/logo/ra-white.png" alt="#">
        </a>

        <span class="bg-light-primary toggle-semi-nav">
          <i class="ti ti-chevrons-right f-s-20"></i>
      </span>
    </div>
    <div class="app-nav" id="app-simple-bar">
        <ul class="main-nav p-0 mt-2">
           
            <li>
                <a class="" data-bs-toggle="collapse" href="#operaciones" aria-expanded="false">

                    <i class="ph-duotone  ph-briefcase"></i>
                    OPERACIONES
                </a>
               
                <?php
                    $link=conectarse();
                    $sql="SELECT `id`, `texto`, `enlace`, `padre` FROM `menu` where estado='0' order by `texto` ";
                    //segunda opcion
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
                        <i class="ph-duotone  ph-briefcase-metal"></i> CONFIGURACIONES
                  </span>
                </a>
                <?php
                    $link=conectarse();
                    $sql="SELECT `id`, `texto`, `enlace`, `padre` FROM `menu` where estado='0' order by `texto` ";
                    //segunda opcion
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
                    <i class="ph-duotone  ph-shapes"></i> REPORTES
                </a>
                <?php
                    $link=conectarse();
                    $sql="SELECT `id`, `texto`, `enlace`, `padre` FROM `menu` where estado='0' order by `texto` ";
                    //segunda opcion
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
            <!-- <li class="no-sub">
                <a class="" href="misc.php">
                    <i class="ph-duotone  ph-certificate"></i> Misc
                </a>
            </li>
           

            <li class="no-sub">
                <a class="" href="mailto:teqlathemes@gmail.com">
                    <i class="ph-duotone  ph-chats"></i> Support
                </a>
            </li> -->


        </ul>
    </div>

    <div class="menu-navs">
        <span class="menu-previous"><i class="ti ti-chevron-left"></i></span>
        <span class="menu-next"><i class="ti ti-chevron-right"></i></span>
    </div>

</nav>