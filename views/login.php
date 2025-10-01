<!DOCTYPE html>
<html lang="en">

<head>
    <!-- css -->
    <?php
    include('../layout/head.php');
    include('../layout/css.php');
    ?>
    <!-- reCAPTCHA API -->
    <!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->
</head>

<body>
<div class="app-wrapper d-block">
    <div class="">
        <!-- Body main section starts -->
        <main class="w-100 p-0">
            <!-- Login to your Account start -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 p-0">
                        <div class="login-form-container">
                            <div class="mb-4">
                                <a class="logo d-inline-block" href="index.php">
                                    <img src="../assets/images/logo/1.png" width="250" alt="#">
                                </a>
                            </div>
                            <div class="form_container">
                                <form class="app-form" id="sml2020_login_form">
                                    <div class="mb-3 text-center">
                                        <h3>Login</h3>
                                        <p class="f-s-12 text-secondary">Ingresa tu usuario y password</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Usuario</label>
                                        <input class="form-control" id="sml2020_username" name="sml2020_username" type="text" required placeholder="Ingrese su usuario" autofocus>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input class="form-control" id="sml2020_password" name="sml2020_password" type="password" required autocomplete="on" placeholder="Ingrese su contraseña">
                                        <div class="error" id="passwordError" style="color: red; display: none;">
                                            La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial.
                                        </div>
                                        <div class="success" id="passwordSuccess" style="color: green; display: none;">
                                            Contraseña válida.
                                        </div>
                                    </div>
                                   
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" onclick="togglePassword()"> Mostrar Password
                                    </div>
                                    
                                    <!-- reCAPTCHA -->
                                    <!-- <div class="mb-3 g-recaptcha" data-sitekey="TU_SITE_KEY_AQUI"></div> -->
                                    
                                    <div>
                                        <button class="btn btn-primary w-100" type="submit" id="loginButton">Ingresar</button>
                                    </div>
                                    <div class="app-divider-v justify-content-center">
                                        <div id="msgbox"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Login to your Account end -->
        </main>
        <!-- Body main section ends -->
    </div>
</div>

<!-- Javascript -->
<script src="../assets/js/jquery-3.6.3.min.js"></script>
<script src="../assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>

<script type="text/javascript">
    // Función para validar la contraseña
    function validatePassword(password) {
        const minLength = 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        return password.length >= minLength && hasUpperCase && hasNumber && hasSpecialChar;
    }

    // Función para mostrar/ocultar contraseña
    function togglePassword() {
        const passwordField = document.getElementById("sml2020_password");
        passwordField.type = passwordField.type === "password" ? "text" : "password";
    }

    $(document).ready(function() {
        // Validación de la contraseña en tiempo real
        $('#sml2020_password').on('input', function() {
            const password = $(this).val();
            const isValid = validatePassword(password);

            if (isValid) {
                $('#passwordError').hide();
                $('#passwordSuccess').show();
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                $('#passwordError').show();
                $('#passwordSuccess').hide();
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        });

        // Enviar formulario de inicio de sesión
        $("#sml2020_login_form").submit(function(e) {
            e.preventDefault();
            
            // Validar CAPTCHA
           /*  const captchaResponse = grecaptcha.getResponse();
            if (!captchaResponse) {
                $("#msgbox").removeClass().addClass('alert alert-danger').text('Por favor complete el CAPTCHA').fadeIn(1000);
                return false;
            } */

            const password = $('#sml2020_password').val();
            if (!validatePassword(password)) {
                $("#msgbox").removeClass().addClass('alert alert-danger').text('Por favor, corrija los errores en el formulario.').fadeIn(1000);
                return false;
            }

            // Deshabilitar botón para evitar múltiples envíos
            $('#loginButton').prop('disabled', true);
            
            $("#msgbox").removeClass().addClass('alert alert-info').text('Verificando...').fadeIn(1000);
            
            $.ajax({
                url: "../controllers/login_controller.php",
                type: "POST",
                dataType: "json",
                contentType: "application/json",
                data: JSON.stringify({
                    username: $('#sml2020_username').val(),
                    password: password,
                    //captcha: captchaResponse
                }),
                success: function(data) {
                    if (data.status === 'success') {
                        $("#msgbox").fadeTo(200, 0.1, function() {
                            $(this).removeClass().html('Ingresando...').addClass('alert alert-success').fadeTo(900, 1, function() {
                                // Redirigir según el rol
                                if (data.id_rol === "3" || data.rol === "cliente") {
                                    console.log('res_ rol: ' + data.id_rol )
                                    window.location.href = 'tienda.php';
                                } else {
                                    window.location.href = 'profile.php';
                                }
                            });
                        });
                    } else {
                        $("#msgbox").fadeTo(200, 0.1, function() {
                            $(this).removeClass().html(data.message).addClass('alert alert-danger').fadeTo(900, 1);
                        });
                        $('#loginButton').prop('disabled', false);
                        //grecaptcha.reset();
                    }
                },
                error: function() {
                    $("#msgbox").fadeTo(200, 0.1, function() {
                        $(this).removeClass().html('Error en la conexión').addClass('alert alert-danger').fadeTo(900, 1);
                    });
                    $('#loginButton').prop('disabled', false);
                    //grecaptcha.reset();
                }
            });
        });

        $('#sml2020_username').focus();
    });
</script>
</body>
</html>