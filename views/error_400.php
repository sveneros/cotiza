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

<div class="error-container p-0">
    <div class="container">
        <div>
            <div>
                <img src="../assets/images/background/error-400.png" class="img-fluid" alt="">
            </div>

            <div class="mb-3">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <p class="text-center text-secondary f-w-500">400 Bad Request response status code indicates that the
                            server
                            cannot or will not
                            process the request due to something that is perceived to be a client error</p>
                    </div>
                </div>
            </div>
            <a role="button" href="index.php" class="btn btn-lg btn-lg btn-danger"><i class="ti ti-home"></i> Back To Home</a>
        </div>
    </div>
</div>

<!-- essential   -->

<!-- latest jquery-->
<script src="../assets/js/jquery-3.6.3.min.js"></script>

<!-- Bootstrap js-->
<script src="../assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>


</body>


</html>
