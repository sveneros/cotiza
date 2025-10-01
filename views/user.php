
<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">
<?php
include('../layout/header.php');
?>

<style>
       
        .error {
            color: red;
        }
        .success {
            color: green;
            display: none;
        }
       
        .toggle-password {
            font-size: 0.7 em;
        }
    </style>
<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12 ">
    <button type="button" class="btn btn-primary m-1 float-end" id="btn_nuevo" data-bs-toggle="modal" data-original-title="test"
    data-bs-target="#exampleModal"><i class="ti ti-plus"></i> Nuevo Usuario</button>
      <h4 class="main-title">Usuarios</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="#" class="f-s-14 f-w-500">
            <span>
              <i class="ph-duotone  ph-table f-s-16"></i> Operaciones
            </span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Usuarios</a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Breadcrumb end -->
   <!-- Modal -->

  <div class="modal fade bd-example-modal-lg" id="exampleModalPass" tabindex="-1" role="dialog"
    aria-labelledby="editCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Resetear Password</h5>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formProductPass">
          <div class="row g-3">
            <div class="col-md-12">
              <input type="text" id="pass_usr" name="pass_usr" class="form-control" readonly>
            </div>
          </div>
            <div class="row g-3">
              <div class="col-md-6">
                  
                  <label for="pass_password">PASSWORD</label><a type="button" class="btn toggle-password" id="pass_togglePassword">Mostrar texto 🔓</a>
                  <input type="password" id="pass_password" name="pass_password" class="form-control" maxlength="100" required>
                  <input type="hidden" id="pass_id_usuario" name="pass_id_usuario" required>
                  
                  <div class="error" id="pass_passwordError">La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial.</div>
                  <div class="success" id="pass_passwordSuccess">Contraseña válida.</div>
              </div>
              <div class="col-md-6">
                  
              <label for="pass_confirmPassword">CONFIRMAR PASSWORD:</label><a type="button" class="btn toggle-password" id="pass_toggleConfirmPassword">Mostrar texto 🔓</a>
                  <input type="password" id="pass_confirmPassword" name="pass_confirmPassword" class="form-control" maxlength="100" required>
                  
                  <div class="error" id="pass_confirmPasswordError">Las contraseñas no coinciden.</div>
                  <div class="success" id="pass_confirmPasswordSuccess">Las contraseñas coinciden.</div>
              </div>
            </div>
            
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary waves-effect" id="btn_submit_pass">Guardar</button>
          <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Cancelar</button>
        </div>
        </form>
      </div>

    </div>
  
  </div>
<!-- Modal -->
<!-- Modal -->

  <div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog"
    aria-labelledby="editCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Usuario</h5>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formProduct">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">NOMBRE<span class="text-danger">*</span></label>
                <input type="text" class="numberformat form-control" id="nombre" name="nombre" maxlength="20" size="13"
                  required>
              </div>
              <div class="col-md-6">
                <input type="hidden" class="form-control" id="id_usuario">
                <label class="form-label">APELLIDO 1 <span class="text-danger">*</span></label>

                <input type="text" class="form-control" id="apellido1" name="apellido1" maxlength="20" required>
              </div>
            </div>
            <div class="row g-3">

              <div class="col-md-6">
                <label class="form-label">APELLIDO 2<span class="text-danger">*</span></label>

                <input type="text" class="form-control" id="apellido2" name="apellido2" maxlength="20" required>
              </div>
              <div class="col-md-6 ">
                <label class="form-label">CELULAR <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="celular" name="celular" maxlength="11" min="1" required>
              </div>
            </div>
            <div class="row g-3">

              <div class="col-md-6 ">
                <label class="form-label">CI <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="ci" name="ci" maxlength="11" min="1" required>
              </div>

              <div class="col-md-6">

              </div>
            </div>
            <div class="row g-3">

              <div class="col-md-6 ">
                <label class="form-label">DIRECCIÓN <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="direccion" name="direccion" maxlength="300" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">CIUDAD<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="ciudad" name="ciudad" maxlength="50" required>
              </div>
            </div>
            <div class="row g-3">

              <div class="col-md-6">
                <label class="form-label">EMAIL<span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" maxlength="300" required>
              </div>
            </div>
            <div class="row g-3">

              <div class="col-md-6 ">
                <label class="form-label">USUARIO <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="usr" name="usr" maxlength="20" required>
              </div>

              <div class="col-md-6">
                
                
              </div>
            </div>
            <div class="row g-3">
            <div class="col-md-6">
                
                <label for="password">PASSWORD</label><a type="button" class="btn toggle-password" id="togglePassword">Mostrar texto 🔓</a>
                <input type="password" id="password" name="password" class="form-control" maxlength="100" required>
                
                <div class="error" id="passwordError">La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial.</div>
                <div class="success" id="passwordSuccess">Contraseña válida.</div>
            </div>
            <div class="col-md-6">
                
            <label for="confirmPassword">CONFIRMAR PASSWORD:</label><a type="button" class="btn toggle-password" id="toggleConfirmPassword">Mostrar texto 🔓</a>
                <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" maxlength="100" required>
                
                <div class="error" id="confirmPasswordError">Las contraseñas no coinciden.</div>
                <div class="success" id="confirmPasswordSuccess">Las contraseñas coinciden.</div>
            </div>
            </div>
            <div class="row g-3">

              <div class="col-md-6 ">
              <label class="form-label">ROL<span class="text-danger">*</span></label>
                <select class="form-control" id="id_rol" name="id_rol">
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">ESTADO<span class="text-danger">*</span></label>
                <select class="form-control" id="estado" name="estado">
                  <option value="V">HABILITADO</option>
                  <option value="D">DESHABILITADO</option>
                </select>
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary waves-effect" id="btn_submit">Guardar</button>
          <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Cancelar</button>
        </div>
        </form>
      </div>

    </div>
  
  </div>
<!-- Modal -->
 <!-- Modal -->

 <div class="modal fade bd-example-modal-lg" id="exampleModalEdit" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalEdit" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalEditLabel">Usuario</h5>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formProductEdit">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">NOMBRE<span class="text-danger">*</span></label>
                <input type="text" class="numberformat form-control" id="edit_nombre" name="edit_nombre" maxlength="20" size="13"
                  required>
              </div>
              <div class="col-md-6">
                <input type="hidden" class="form-control" id="edit_id_usuario">
                <label class="form-label">APELLIDO 1 <span class="text-danger">*</span></label>

                <input type="text" class="form-control" id="edit_apellido1" name="edit_apellido1" maxlength="20" required>
              </div>
            </div>
            <div class="row g-3">

              <div class="col-md-6">
                <label class="form-label">APELLIDO 2<span class="text-danger">*</span></label>

                <input type="text" class="form-control" id="edit_apellido2" name="edit_apellido2" maxlength="20" required>
              </div>
              <div class="col-md-6 ">
                <label class="form-label">CELULAR <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="edit_celular" name="edit_celular" maxlength="11" min="1" required>
              </div>
            </div>
            <div class="row g-3">

              <div class="col-md-6 ">
                <label class="form-label">CI <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="edit_ci" name="edit_ci" maxlength="11" min="1" required>
              </div>

              <div class="col-md-6">

              </div>
            </div>
            <div class="row g-3">

              <div class="col-md-6 ">
                <label class="form-label">DIRECCIÓN <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="edit_direccion" name="edit_direccion" maxlength="300" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">CIUDAD<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="edit_ciudad" name="edit_ciudad" maxlength="50" required>
              </div>
            </div>
            <div class="row g-3">

              <div class="col-md-6">
                <label class="form-label">EMAIL<span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="edit_email" name="edit_email" maxlength="300" required>
              </div>
            </div>
           
            
            <div class="row g-3">

              <div class="col-md-6 ">
              <label class="form-label">ROL<span class="text-danger">*</span></label>
                <select class="form-control" id="edit_id_rol" name="edit_id_rol">
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">ESTADO<span class="text-danger">*</span></label>
                <select class="form-control" id="edit_estado" name="edit_estado">
                  <option value="V">HABILITADO</option>
                  <option value="D">DESHABILITADO</option>
                </select>
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary waves-effect" id="btn_submit_edit">Guardar</button>
          <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Cancelar</button>
        </div>
        </form>
      </div>

    </div>
  
  </div>
<!-- Modal -->
<!-- Data Table start -->
  <div class="row">
    <!-- Default Datatable start -->
    <div class="col-12">
      <div class="card ">
       
        <div class="card-body p-0">
          <div class="table-responsive app-scroll app-datatable-default product-list-table">
            <table class="table-sm display align-middle" id="basic-1">

              <thead>
                <tr>
                  <th>Usuario</th>
                  <th>Nombre</th>
                  <th>Apellido 1</th>
                  <th>Celular</th>
                  <th>Rol</th>
                  <th>CI</th>
                  <th>Email</th>
                  <th>Estado</th>
                  <th>Editar</th>
                  <th>Password</th>
                </tr>
              </thead>
              <tbody id="DetalleTabla">
                <tr>
                  <td>CARGANDO...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- Default Datatable end -->
  </div>
  <!-- Data Table end -->
</div>
          

    <?php
    include('../layout/footer.php');
    ?>


<!-- data table jquery-->
<script src="../assets/vendor/datatable/jquery.dataTables.min.js"></script>
<script src="../assets/js/jquery.validate.min.js"></script>
<script src="../assets/js/jquery-key-restrictions.min.js"></script>

<!-- js-->
<script>
    // Función para validar la contraseña (ahora es global)
    function validatePassword(password) {
        const minLength = 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        return password.length >= minLength && hasUpperCase && hasNumber && hasSpecialChar;
    }

    $(function($) {
        // Validación de la contraseña en tiempo real
        $('#password').on('input', function() {
            const password = $(this).val();
            const isValid = validatePassword(password);

            if (isValid) {
                $('#passwordError').hide();
                $('#passwordSuccess').show();
                $(this).removeClass('invalid').addClass('valid');
            } else {
                $('#passwordError').show();
                $('#passwordSuccess').hide();
                $(this).removeClass('valid').addClass('invalid');
            }
        });

        // Validación de la confirmación de contraseña en tiempo real
        $('#confirmPassword').on('input', function() {
            const password = $('#password').val();
            const confirmPassword = $(this).val();

            if (password === confirmPassword && password !== '') {
                $('#confirmPasswordError').hide();
                $('#confirmPasswordSuccess').show();
                $(this).removeClass('invalid').addClass('valid');
            } else {
                $('#confirmPasswordError').show();
                $('#confirmPasswordSuccess').hide();
                $(this).removeClass('valid').addClass('invalid');
            }
        });

        // Función para mostrar/ocultar contraseña
        function togglePasswordVisibility(inputId, buttonId) {
            const input = $(inputId);
            const button = $(buttonId);
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                button.text('Ocultar texto 🔒');
            } else {
                input.attr('type', 'password');
                button.text('Mostrar texto 🔓');
            }
        }

        // Evento para mostrar/ocultar la contraseña principal
        $('#togglePassword').on('click', function() {
            togglePasswordVisibility('#password', '#togglePassword');
        });

        // Evento para mostrar/ocultar la confirmación de contraseña
        $('#toggleConfirmPassword').on('click', function() {
            togglePasswordVisibility('#confirmPassword', '#toggleConfirmPassword');
        });

        //edit pass form
        $('#pass_password').on('input', function() {
            const password = $(this).val();
            const isValid = validatePassword(password);

            if (isValid) {
                $('#pass_passwordError').hide();
                $('#pass_passwordSuccess').show();
                $(this).removeClass('invalid').addClass('valid');
            } else {
                $('#pass_passwordError').show();
                $('#pass_passwordSuccess').hide();
                $(this).removeClass('valid').addClass('invalid');
            }
        });

        // Validación de la confirmación de contraseña en tiempo real
        $('#pass_confirmPassword').on('input', function() {
            const password = $('#pass_password').val();
            const confirmPassword = $(this).val();

            if (password === confirmPassword && password !== '') {
                $('#pass_confirmPasswordError').hide();
                $('#pass_confirmPasswordSuccess').show();
                $(this).removeClass('invalid').addClass('valid');
            } else {
                $('#pass_confirmPasswordError').show();
                $('#pass_confirmPasswordSuccess').hide();
                $(this).removeClass('valid').addClass('invalid');
            }
        });

        // Evento para mostrar/ocultar la contraseña principal
        $('#pass_togglePassword').on('click', function() {
            togglePasswordVisibility('#pass_password', '#pass_togglePassword');
        });

        // Evento para mostrar/ocultar la confirmación de contraseña
        $('#pass_toggleConfirmPassword').on('click', function() {
            togglePasswordVisibility('#pass_confirmPassword', '#pass_toggleConfirmPassword');
        });
        //end edit pass form

        // Inicialización de funciones
        ObtenerUsusarios();
        $("#usr").lettersOnly();
        $("#nombre").lettersOnly();
        $("#apellido1").lettersOnly();
        $("#apellido2").lettersOnly();
        $("#ciudad").lettersOnly();

        
        $("#edit_nombre").lettersOnly();
        $("#edit_apellido1").lettersOnly();
        $("#edit_apellido2").lettersOnly();
        $("#edit_ciudad").lettersOnly();

        // Evento para el botón "Nuevo"
        $('#btn_nuevo').on('click', function(e) {
            e.preventDefault();
            resetUsuarioForm();
            CargarRoles('');
            $('#nombre').focus();
        });

        // Validación del formulario
        $("#formProduct").submit(function(e) {
            e.preventDefault();
        }).validate({
            submitHandler: function(form) {
                GuardarUsuario(); // Enviar el formulario mediante AJAX
                return false; // Evitar el envío tradicional del formulario
            }
        });
        
        $("#formProductEdit").submit(function(e) {
            e.preventDefault();
        }).validate({
            submitHandler: function(form) {
                EditarUsuario(); // Enviar el formulario mediante AJAX
                return false; // Evitar el envío tradicional del formulario
            }
        });

        $("#formProductPass").submit(function(e) {
            e.preventDefault();
        }).validate({
            submitHandler: function(form) {
                EditarUsuarioPassword(); // Enviar el formulario mediante AJAX
                return false; // Evitar el envío tradicional del formulario
            }
        });
    });

    // Función para guardar el usuario
    function GuardarUsuario() {
        $('#btn_submit').hide();

        const password = $('#password').val();
        const confirmPassword = $('#confirmPassword').val();

        if (!validatePassword(password) || password !== confirmPassword) {
            alert('Por favor, corrige los errores en el formulario.');
            $('#btn_submit').show();
            return;
        }

        const elId = $('#id_usuario').val();
        const nombre = $('#nombre').val();
        const apellido1 = $('#apellido1').val();
        const apellido2 = $('#apellido2').val();
        const celular = $('#celular').val();
        const ci = $('#ci').val();
        const direccion = $('#direccion').val();
        const ciudad = $('#ciudad').val();
        const email = $('#email').val();
        const usr = $('#usr').val();
        const pwd = $('#password').val();
        const id_rol = $('#id_rol option:selected').val();
        const estado = $('#estado option:selected').val();

        const method = elId === "0" || elId === null || elId === "" ? 'POST' : 'PUT';
        const data = {
            id: elId,
            nombre: nombre,
            apellido1: apellido1,
            apellido2: apellido2,
            celular: celular,
            ci: ci,
            direccion: direccion,
            ciudad: ciudad,
            email: email,
            usr: usr,
            pwd: pwd,
            id_rol: id_rol,
            estado: estado,
        };

        $.ajax({
            url: '../controllers/user_controller.php',
            type: method,
            dataType: "json",
            data: JSON.stringify(data),
            success: (response) => {
                if (response.success) {
                    $('#exampleModal').modal('hide');
                    Swal.fire('Usuario Actualizado', '', 'success');
                    ObtenerUsusarios();
                } else {
                    alert(response.error || 'Ocurrió un error al actualizar el usuario.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (textStatus === 'timeout') {
                    Swal.fire('Error', '¡La conexión a internet se ha interrumpido!', 'error');
                } else {
                    Swal.fire('Error', 'Error al guardar Usuario: ' + errorThrown, 'error');
                }
            }
        }).always(() => {
            $('#btn_submit').show();
        });
    }

    //editar
    function EditarUsuario() {
        $('#btn_submit_edit').hide();

        const elId = $('#edit_id_usuario').val();
        const nombre = $('#edit_nombre').val();
        const apellido1 = $('#edit_apellido1').val();
        const apellido2 = $('#edit_apellido2').val();
        const celular = $('#edit_celular').val();
        const ci = $('#edit_ci').val();
        const direccion = $('#edit_direccion').val();
        const ciudad = $('#edit_ciudad').val();
        const email = $('#edit_email').val();
        
        const id_rol = $('#edit_id_rol option:selected').val();
        const estado = $('#edit_estado option:selected').val();

        const method = elId === "0" || elId === null || elId === "" ? 'POST' : 'PUT';
        const data = {
            id: elId,
            nombre: nombre,
            apellido1: apellido1,
            apellido2: apellido2,
            celular: celular,
            ci: ci,
            direccion: direccion,
            ciudad: ciudad,
            email: email,
        
            id_rol: id_rol,
            estado: estado,
        };

        $.ajax({
            url: '../controllers/user_controller.php',
            type: method,
            dataType: "json",
            data: JSON.stringify(data),
            success: (response) => {
                if (response.success) {
                    $('#exampleModalEdit').modal('hide');
                    Swal.fire('Usuario Actualizado', '', 'success');
                    ObtenerUsusarios();
                } else {
                    alert(response.error || 'Ocurrió un error al actualizar el usuario.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (textStatus === 'timeout') {
                    Swal.fire('Error', '¡La conexión a internet se ha interrumpido!', 'error');
                } else {
                    Swal.fire('Error', 'Error al guardar Usuario: ' + errorThrown, 'error');
                }
            }
        }).always(() => {
            $('#btn_submit_edit').show();
        });
    }

    // Función para resetear el formulario de usuario
    function resetUsuarioForm() {
        $('#id_usuario').val("0");
        $('#nombre').val("");
        $('#apellido1').val("");
        $('#apellido2').val("");
        $('#celular').val("");
        $('#ci').val("");
        $('#direccion').val("");
        $('#ciudad').val("");
        $('#email').val("");
        $('#usr').val("");
        $('#pwd').val("");
        $('#id_rol').prop('selectedIndex', 0);
        $('#estado').prop('selectedIndex', 0);
    }

    // Función para cargar roles
    function CargarRoles() {
        $.ajax({
            url: '../controllers/role_controller.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                localStorage.setItem('sml2020_roles', JSON.stringify(data));
                ObtenerRoles();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (textStatus === 'timeout') {
                    alert("¡La conexión a internet está caída!");
                } else {
                    alert("Error al cargar los roles: " + errorThrown);
                }
            }
        });
    }

    // Función para obtener roles y llenar el select
    function ObtenerRoles(selectedId) {
        $('#id_rol').empty();
        $('#edit_id_rol').empty();
        const localData = JSON.parse(localStorage.getItem('sml2020_roles'));

        let html = '';
        $.each(localData, function(key, value) {
            html += `<option value="${value.id}">${value.rol}</option>`;
        });

        $('#id_rol').append(html);
        $('#edit_id_rol').append(html);

        if (selectedId) {
            $('#id_rol').val(selectedId).prop('selected', true);
            $('#edit_id_rol').val(selectedId).prop('selected', true);
        }
    }

    // Función para obtener usuarios
    function ObtenerUsusarios() {
        $.ajax({
            url: '../controllers/user_controller.php',
            type: 'GET',
            dataType: "json",
            success: function(data) {
                localStorage.setItem('sml2020_usuarios', JSON.stringify(data));
                actualizarTabla();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (textStatus === 'timeout') {
                    Swal.fire('Error', '¡La conexión a internet se ha interrumpido!', 'error');
                } else {
                    Swal.fire('Error', 'Error al cargar usuarios: ' + errorThrown, 'error');
                }
            }
        });
    }

    // Función para actualizar la tabla de usuarios
    function actualizarTabla() {
        $('#DetalleTabla').empty();

        if ($.fn.dataTable.isDataTable('#basic-1')) {
            $('#basic-1').DataTable().destroy();
        }

        const localData = JSON.parse(localStorage.getItem('sml2020_usuarios'));
        let html = '';

        $.each(localData, function(key, value) {
            const est = value.estado === 'V' ?
                '<span class="badge rounded-pill bg-success badge-notification">HABILITADO</span>' :
                '<span class="badge rounded-pill bg-danger badge-notification">DESHABILITADO</span>';

            html += `
                <tr role="row" class="odd">
                    <td class="sorting_1">${value.usr}</td>
                    <td>${value.nombre}</td>
                    <td>${value.apellido1}</td>
                    <td>${value.celular}</td>
                    <td>${value.rol}</td>
                    <td>${value.ci}</td>
                    <td>${value.email}</td>
                    <td>${est}</td>
                    <td><button class="btn btn-primary" onclick="Editar('${value.id}')" data-bs-toggle="modal" data-bs-target="#exampleModalEdit"><i class="fa fa-pencil"></i></button></td>
                    <td><button class="btn btn-primary" onclick="EditarPass('${value.id}')" data-bs-toggle="modal" data-bs-target="#exampleModalPass"><i class="fa fa-key"></i></button></td>
                </tr>
            `;
        });

        $('#DetalleTabla').html(html);
        $('#basic-1').DataTable();
    }

    // Función para editar un usuario
    function Editar(elId) {
        const localData = JSON.parse(localStorage.getItem('sml2020_usuarios'));

        $.each(localData, function(key, value) {
            if (value.id === elId) {
                $('#edit_id_usuario').val(value.id);
                $('#edit_nombre').val(value.nombre);
                $('#edit_apellido1').val(value.apellido1);
                $('#edit_apellido2').val(value.apellido2);
                $('#edit_celular').val(value.celular);
                $('#edit_ci').val(value.ci);
                $('#edit_direccion').val(value.direccion);
                $('#edit_ciudad').val(value.ciudad);
                $('#edit_email').val(value.email);
               
                ObtenerRoles(value.id_rol);
                $('#estado').val(value.estado).prop('selected', true);
                return;
            }
        });

        $('#edit_nombre').focus();
    }

    // Función para editar password
    function EditarPass(elId) {
      const localData = JSON.parse(localStorage.getItem('sml2020_usuarios'));
      $.each(localData, function(key, value) {
            if (value.id === elId) {
                $('#pass_usr').val(value.usr);
                return;
            }
        });
      $('#pass_id_usuario').val(elId);
      $('#pass_password').val("");
      $('#pass_confirmPassword').val("");
      $('#pass_password').focus();
    }

    //editar
    function EditarUsuarioPassword() {
        $('#btn_submit_pass').hide();
        const pass_password = $('#pass_password').val();
        const pass_confirmPassword = $('#pass_confirmPassword').val();

        if (!validatePassword(pass_password) || pass_password !== pass_confirmPassword) {
            alert('Por favor, corrige los errores en el formulario.');
            $('#btn_submit_pass').show();
            return;
        }

        const elId = $('#pass_id_usuario').val();
        const password = $('#pass_password').val();

        const method = 'PATCH';
        const data = {
          id: elId,
          password: password,  
        };

        $.ajax({
            url: '../controllers/user_controller.php',
            type: method,
            dataType: "json",
            data: JSON.stringify(data),
            success: (response) => {
                if (response.success) {
                    $('#exampleModalPass').modal('hide');
                    Swal.fire('Usuario Actualizado', '', 'success');
                } else {
                    alert(response.error || 'Ocurrió un error al actualizar el password.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (textStatus === 'timeout') {
                    Swal.fire('Error', '¡La conexión a internet se ha interrumpido!', 'error');
                } else {
                    Swal.fire('Error', 'Error al guardar Usuario: ' + errorThrown, 'error');
                }
            }
        }).always(() => {
            $('#btn_submit_edit').show();
        });
    }
</script>
