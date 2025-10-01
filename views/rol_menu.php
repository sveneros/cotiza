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
    }
    .permission-item {
        margin-bottom: 10px;
    }
    .permission-group {
        border: 1px solid #ddd;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }
    .permission-group h5 {
        margin-bottom: 15px;
        padding-bottom: 5px;
        border-bottom: 1px solid #eee;
    }
</style>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title">Gestión de Permisos</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="#" class="f-s-14 f-w-500">
            <span><i class="ph-duotone ph-table f-s-16"></i> Configuración</span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Permisos</a>
        </li>
      </ul>
    </div>
  </div>
  <!-- Breadcrumb end -->

  <!-- Modal para editar permisos -->
  <div class="modal fade bd-example-modal-lg" id="editPermissionsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Permisos</h5>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="permissionsForm">
            <input type="hidden" id="roleId" name="roleId">
            <div class="row">
              <div class="col-md-12">
                <h4 id="roleNameDisplay"></h4>
                <div id="permissionsContainer" class="permission-group">
                  <p>Cargando menús disponibles...</p>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary waves-effect" id="btnSavePermissions">Guardar Cambios</button>
          <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Data Table start -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body p-0">
          <div class="table-responsive app-scroll app-datatable-default">
            <table class="table-sm display align-middle" id="basic-1">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Rol</th>
                  <th>Menús Asignados</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="DetalleTabla">
                <tr>
                  <td colspan="4">Cargando roles...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Data Table end -->
</div>

<?php
include('../layout/footer.php');
?>

<!-- data table jquery-->
<script src="../assets/vendor/datatable/jquery.dataTables.min.js"></script>
<script src="../assets/js/jquery.validate.min.js"></script>

<script>
$(document).ready(function() {
    // Función para cargar los datos
    function ObtenerPermisos() {
        $.ajax({
            url: '../controllers/role_menu_controller.php?action=getAllWithMenus',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if(data && data.roles && data.permissions) {
                    localStorage.setItem('sml2020_permisos', JSON.stringify(data));
                    actualizarTabla();
                } else {
                    Swal.fire('Error', 'Datos recibidos en formato incorrecto', 'error');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (textStatus === 'timeout') {
                    Swal.fire('Error', '¡La conexión a internet se ha interrumpido!', 'error');
                } else {
                    Swal.fire('Error', 'Error al cargar permisos: ' + errorThrown, 'error');
                }
            }
        });
    }

    // Función para actualizar la tabla
    function actualizarTabla() {
        $('#DetalleTabla').empty();

        if ($.fn.dataTable.isDataTable('#basic-1')) {
            $('#basic-1').DataTable().destroy();
        }

        const localData = JSON.parse(localStorage.getItem('sml2020_permisos'));
        
        if(!localData || !localData.roles) {
            $('#DetalleTabla').html('<tr><td colspan="4">No hay datos disponibles</td></tr>');
            return;
        }

        let html = '';

        $.each(localData.roles, function(key, role) {
            const assignedMenus = localData.permissions && localData.permissions[role.id] ? localData.permissions[role.id] : [];
            
            html += `
                <tr role="row" class="odd">
                    <td class="sorting_1">${role.id}</td>
                    <td>${role.rol}</td>
                    <td>${assignedMenus.length} menú(s) asignado(s)</td>
                    <td>
                        <button class="btn btn-primary" onclick="EditarPermisos('${role.id}', '${role.rol.replace(/'/g, "\\'")}')" 
                                data-bs-toggle="modal" data-bs-target="#editPermissionsModal">
                            <i class="fa fa-edit"></i> Editar
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#DetalleTabla').html(html);
        $('#basic-1').DataTable();
    }

    // Función para editar permisos (global para que sea accesible desde los botones)
    window.EditarPermisos = function(roleId, roleName) {
        $('#roleId').val(roleId);
        $('#roleNameDisplay').text('Permisos para: ' + roleName);
        
        $.ajax({
            url: '../controllers/role_menu_controller.php?action=getForEdit&roleId=' + roleId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if(data && data.menus && data.assignedMenus) {
                    // Convertir los IDs de menús asignados a strings para comparación
                    const assignedMenus = data.assignedMenus.map(String);
                    renderPermissionsForm(data.menus, assignedMenus);
                } else {
                    Swal.fire('Error', 'Datos recibidos en formato incorrecto', 'error');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.fire('Error', 'Error al cargar permisos: ' + errorThrown, 'error');
            }
        });
    }

    // Función para renderizar el formulario de permisos
    function renderPermissionsForm(menus, assignedMenus) {
        let html = '';
        
        // Agrupar menús por categoría
        const groupedMenus = {};
        $.each(menus, function(index, menu) {
            const category = menu.categoria || 'General';
            if (!groupedMenus[category]) {
                groupedMenus[category] = [];
            }
            groupedMenus[category].push(menu);
        });
        
        // Crear grupos de checkboxes
        $.each(groupedMenus, function(category, menusInCategory) {
            html += `<div class="permission-group">`;
            html += `<h5>${category}</h5>`;
            
            $.each(menusInCategory, function(index, menu) {
                const isChecked = assignedMenus.includes(menu.id.toString());
                html += `
                    <div class="form-check permission-item">
                        <input class="form-check-input" type="checkbox" 
                               id="menu_${menu.id}" 
                               name="menus[]" 
                               value="${menu.id}" 
                               ${isChecked ? 'checked' : ''}>
                        <label class="form-check-label" for="menu_${menu.id}">
                            ${menu.texto} <small class="text-muted">(${menu.enlace})</small>
                        </label>
                    </div>
                `;
            });
            
            html += `</div>`;
        });
        
        $('#permissionsContainer').html(html);
    }

    // Guardar los permisos
    $('#btnSavePermissions').on('click', function() {
        const roleId = $('#roleId').val();
        const selectedMenus = [];
        
        $('input[name="menus[]"]:checked').each(function() {
            selectedMenus.push($(this).val());
        });
        
        $('#btnSavePermissions').prop('disabled', true);
        
        $.ajax({
            url: '../controllers/role_menu_controller.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'update',
                roleId: roleId,
                menus: selectedMenus
            },
            success: function(response) {
                if (response && response.success) {
                    $('#editPermissionsModal').modal('hide');
                    Swal.fire('Éxito', 'Permisos actualizados correctamente', 'success');
                    ObtenerPermisos();
                } else {
                    Swal.fire('Error', response.error || 'Ocurrió un error al actualizar los permisos', 'error');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (textStatus === 'timeout') {
                    Swal.fire('Error', '¡La conexión a internet se ha interrumpido!', 'error');
                } else {
                    Swal.fire('Error', 'Error al guardar permisos: ' + errorThrown, 'error');
                }
            },
            complete: function() {
                $('#btnSavePermissions').prop('disabled', false);
            }
        });
    });

    // Inicialización
    ObtenerPermisos();
});
</script>