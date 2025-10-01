<!DOCTYPE html>
<html lang="en">

<head>
    <!-- editor css -->
    <link rel="stylesheet" href="../assets/vendor/trumbowyg/trumbowyg.min.css">

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
                        <h4 class="main-title">Editor</h4>
                        <ul class="app-line-breadcrumbs mb-3">
                            <li class="">
                                <a href="#" class="f-s-14 f-w-500">
									<span>
									  <i class="ph-duotone  ph-briefcase f-s-16"></i> Ui kits
									</span>
                                </a>
                            </li>
                            <li class="active">
                                <a href="#" class="f-s-14 f-w-500">Editor</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Breadcrumb end -->

                <!-- Editor start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="app-editor" id="editor">
                                    <p>Hello !</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div id="editor1">
                                    <p>Hello !</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Editor end -->
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

<!-- Trumbowyg js -->
<script src="../assets/vendor/trumbowyg/trumbowyg.min.js"></script>

<!-- editor js -->
<script src="../assets/js/editor.js"></script>

</html>
