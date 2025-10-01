<!DOCTYPE html>
<html lang="en">

<head>
    <!-- css -->
    <?php
    include('../layout/head.php');
    include('../layout/css.php');
    ?>
</head>

<body>

<div class="app-wrapper">
    <!-- app loader -->
    <div class="loader-wrapper">
        <div class="loader_16"></div>
    </div>

    <?php
    include('../layout/sidebar.php');
    ?>

    <div class="app-content">
        <!-- header -->
        <?php
        include('../layout/header.php');
        ?>

        <!-- main section -->
        <main>
            <div class="container-fluid">
                <!-- Breadcrumb start -->
                <div class="row m-1">
                    <div class="col-12 ">
                        <h4 class="main-title">Pie</h4>
                        <ul class="app-line-breadcrumbs mb-3">
                            <li class="">
                                <a href="#" class="f-s-14 f-w-500">
                      <span>
                        <i class="ph-duotone  ph-chart-pie-slice f-s-16"></i> Chart
                      </span>
                                </a>
                            </li>
                            <li class="">
                                <a href="#" class="f-s-14 f-w-500">
                      <span>
                        Apexcharts
                      </span>
                                </a>
                            </li>
                            <li class="active">
                                <a href="#" class="f-s-14 f-w-500">Pie</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Breadcrumb end -->
                <div class="row">
                    <!-- Simple Pie Chart start -->
                    <div class="col-lg-6 col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h5> Simple Pie Chart</h5>
                            </div>
                            <div class="card-body">
                                <div id="pie1"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Simple Pie Chart end -->
                    <!-- Simple Donut Chart start -->
                    <div class="col-lg-6 col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Simple Donut Chart</h5>
                            </div>
                            <div class="card-body">
                                <div id="pie2"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Simple Donut Chart end -->
                    <!-- Gradient Donut Chart start -->
                    <div class="col-lg-6 col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h5> Gradient Donut Chart</h5>
                            </div>
                            <div class="card-body">
                                <div id="pie5"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Gradient Donut Chart end -->
                    <!-- Patterned Donut Chart start -->
                    <div class="col-lg-6 col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h5> Patterned Donut Chart</h5>
                            </div>
                            <div class="card-body">
                                <div id="pie6"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Patterned Donut Chart end -->
                    <!-- Pie Chart with Image fill start -->
                    <div class="col-lg-6 col-xl-4">
                        <div class="card equal-card">
                            <div class="card-header">
                                <h5> Pie Chart with Image fill</h5>
                            </div>
                            <div class="card-body">
                                <div id="pie7"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Pie Chart with Image fill end -->
                    <!--  Updating Donut Chart start -->
                    <div class="col-lg-6 col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h5> Updating Donut Chart</h5>
                            </div>
                            <div class="card-body">
                                <div class="card-body">
                                    <div class="updating-btn-box actions text-center">
                                        <button id="add" class="btn btn-sm btn-primary" onclick="appendData(this)">
                                            + ADD
                                        </button>

                                        <button id="remove" class="btn btn-sm btn-danger">
                                            - REMOVE
                                        </button>

                                        <button id="reset" class="btn btn-sm btn-success">
                                            RESET
                                        </button>
                                    </div>
                                    <div id="chart9" ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  Updating Donut Chart end -->
                    <!-- Monochrome Pie Chart start -->
                    <div class="col-lg-6 col-xl-4">
                        <div class="card equal-card">
                            <div class="card-header">
                                <h5> Monochrome Pie Chart</h5>
                            </div>
                            <div class="card-body">
                                <div id="pie4"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Monochrome Pie Chart end -->

                    <!-- Basic Polar-Area Chart start -->
                    <div class="col-md-6 col-xl-4">
                        <div class="card equal-card">
                            <div class="card-header">
                                <h5> Basic Polar-Area Chart</h5>
                            </div>
                            <div class="card-body">
                                <div id="polar1"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Basic Polar-Area Chart end -->
                    <!-- Polar-Area MonoChrome start -->
                    <div class="col-md-6 col-xl-4">
                        <div class="card equal-card">
                            <div class="card-header">
                                <h5> Polar-Area MonoChrome</h5>
                            </div>
                            <div class="card-body">
                                <div id="polar2"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Polar-Area MonoChrome end -->

                </div>
            </div>
        </main>
    </div>

    <!-- tap on top -->
    <div class="go-top">
      <span class="progress-value">
        <i class="ti ti-arrow-up"></i>
      </span>
    </div>

    <!-- footer -->
    <?php
    include('../layout/footer.php');
    ?>
</div>

<!--customizer-->
<div id="customizer"></div>

</body>

<!-- Javascript -->
<?php
include('../layout/script.php');
?>

<!-- apexcharts-->
<script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>

<!-- js-->
<script src="../assets/js/pie_charts.js"></script>

</html>
