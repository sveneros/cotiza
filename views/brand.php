<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">
<?php
include('../layout/header.php');
?>
<div class="container-fluid">
    <!-- Breadcrumb start -->
    <div class="row m-1">
        <div class="col-12 ">
            <button type="button" class="btn btn-primary m-1 float-end" id="btn_nuevo" data-bs-toggle="modal" data-original-title="test"
                data-bs-target="#exampleModal"><i class="ti ti-plus"></i> Nueva Marca</button>
            <h4 class="main-title">Marcas</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li class="">
                    <a href="#" class="f-s-14 f-w-500">
                        <span>
                            <i class="ph-duotone  ph-table f-s-16"></i> Operaciones
                        </span>
                    </a>
                </li>
                <li class="active">
                    <a href="#" class="f-s-14 f-w-500">Marcas</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Breadcrumb end -->

    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog"
        aria-labelledby="editCardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">MARCA</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="FormMarca">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">CÓDIGO<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="codigo" name="codigo"
                                    required minlength="2" maxlength="50">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NOMBRE DE MARCA<span class="text-danger">*</span></label>
                                <input type="hidden" class="form-control" id="id_marca" name="id_marca"
                                    required value="0">
                                <input type="text" class="form-control" id="nombre_marca" name="nombre_marca"
                                    required minlength="4" maxlength="150">
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">PAÍS DE ORIGEN<span class="text-danger">*</span></label>
                                <select class="form-control" id="pais_origen" name="pais_origen">
                                    <option value="Estados Unidos">Estados Unidos</option>
                                    <option value="Alemania">Alemania</option>
                                    <option value="China">China</option>
                                    <option value="Japón">Japón</option>
                                    <option value="Reino Unido">Reino Unido</option>
                                    <option value="Francia">Francia</option>
                                    <option value="Italia">Italia</option>
                                    <option value="Corea del Sur">Corea del Sur</option>
                                    <option value="Otro">Otro</option>
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

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">FECHA DE REGISTRO</label>
                                <input type="date" class="form-control" id="fecha_registro" name="fecha_registro">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">PÁGINA WEB</label>
                                <input type="url" class="form-control" id="pagina_web" name="pagina_web" placeholder="https://ejemplo.com">
                            </div>
                        </div>

                        <h5 class="mt-4">INFORMACIÓN DE CONTACTO</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">NOMBRE DEL CONTACTO</label>
                                <input type="text" class="form-control" id="contacto_nombre" name="contacto_nombre">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CARGO</label>
                                <input type="text" class="form-control" id="contacto_cargo" name="contacto_cargo">
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-12">
                                <label class="form-label">DIRECCIÓN</label>
                                <input type="text" class="form-control" id="contacto_direccion" name="contacto_direccion">
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">TELÉFONO</label>
                                <input type="tel" class="form-control" id="contacto_telefono" name="contacto_telefono">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">EMAIL</label>
                                <input type="email" class="form-control" id="contacto_email" name="contacto_email">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary waves-effect" id="btn_submit">Guardar</button>
                            <button type="button" class="btn btn-secondary waves-effect"
                                data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
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
                    <div class="app-datatable-default overflow-auto">
                        <table id="basic-1" class="display app-data-table default-data-table">
                            <thead>
                                <tr>
                                    <th>Código</th>    
                                    <th>Nombre de Marca</th>
                                    <th>País de Origen</th>
                                    <th>Contacto</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
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
<script src="../assets/js/data_table.js"></script>
<script>
$(function($){
    CargarMarcas();
    //validation
    $("#nombre_marca").lettersOnly();
});

$('#btn_nuevo').on('click', function(e){
    e.preventDefault();
    $("#id_marca").val("0");
    $("#codigo").val("");
    $("#nombre_marca").val("");
    $("#pais_origen").val("Estados Unidos");
    $("#estado").val("V");
    $("#fecha_registro").val("");
    $("#pagina_web").val("");
    $("#contacto_nombre").val("");
    $("#contacto_cargo").val("");
    $("#contacto_direccion").val("");
    $("#contacto_telefono").val("");
    $("#contacto_email").val("");
});

function CargarMarcas() {
    $.ajax({
        url: '../controllers/brand_controller.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            localStorage.setItem('sml2020_marcas', JSON.stringify(data));
            ObtenerMarcas1(); 
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (textStatus === 'timeout') {
                alert("Internet connection is down!");
            } else {
                alert("Error loading brands: " + errorThrown);
            }
        }
    });
}

function ObtenerMarcas1() {
    $('#DetalleTabla').empty();

    if ($.fn.dataTable.isDataTable('#basic-1')) {
        $('#basic-1').DataTable().destroy();
    }

    const localData = JSON.parse(localStorage.getItem('sml2020_marcas'));

    let html = '';
    $.each(localData, function(key, value) {
        const est = value.estado === 'V' ?
            '<span class="badge rounded-pill bg-success badge-notification">HABILITADO</span>' :
            '<span class="badge rounded-pill bg-danger badge-notification">DESHABILITADO</span>';

        const edi = '<button class="btn btn-primary" onclick="Editar(\'' + value.id + '\')" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-pencil"></i></button>';

        html += `
            <tr role="row" class="odd">
                <td class="sorting_1">${value.codigo}</td>
                <td>${value.nombre_marca}</td>
                <td>${value.pais_origen || 'N/A'}</td>
                <td>${value.contacto_nombre || 'N/A'}</td>
                <td>${value.contacto_telefono || 'N/A'}</td>
                <td>${est}</td>
                <td>${edi}</td>
            </tr>
        `;
    });

    $('#DetalleTabla').html(html);
    $('#basic-1').DataTable();
}

function Editar(elId){ 
    var localData = JSON.parse(localStorage.getItem('sml2020_marcas'));
    $.each(localData, function(key, value){
        if(value.id == elId){
            $('#id_marca').val(value.id);
            $('#codigo').val(value.codigo);
            $('#nombre_marca').val(value.nombre_marca);
            $('#estado').val(value.estado).prop('selected', true);
            $('#pais_origen').val(value.pais_origen || 'Estados Unidos').prop('selected', true);
            $('#fecha_registro').val(value.fecha_registro || '');
            $('#pagina_web').val(value.pagina_web || '');
            $('#contacto_nombre').val(value.contacto_nombre || '');
            $('#contacto_cargo').val(value.contacto_cargo || '');
            $('#contacto_direccion').val(value.contacto_direccion || '');
            $('#contacto_telefono').val(value.contacto_telefono || '');
            $('#contacto_email').val(value.contacto_email || '');
            return;
        }
    });
    $('#nombre_marca').focus();      
} 

$("#FormMarca").submit(function(e) {
    e.preventDefault();
}).validate({  
    submitHandler: function(form) { 
        GuardarMarca();
        return false;
    }
});

function GuardarMarca() {
    const el_id = $('#id_marca').val();
    const method = el_id === "0" || el_id === null || el_id === "" ? 'POST' : 'PUT';

    // Datos a enviar
    const data = {
        id: el_id,
        codigo: $('#codigo').val(),
        nombre_marca: $('#nombre_marca').val(),
        estado: $('#estado option:selected').val(),
        pais_origen: $('#pais_origen option:selected').val(),
        fecha_registro: $('#fecha_registro').val(),
        pagina_web: $('#pagina_web').val(),
        contacto_nombre: $('#contacto_nombre').val(),
        contacto_cargo: $('#contacto_cargo').val(),
        contacto_direccion: $('#contacto_direccion').val(),
        contacto_telefono: $('#contacto_telefono').val(),
        contacto_email: $('#contacto_email').val()
    };

    $.ajax({
        url: '../controllers/brand_controller.php',
        type: method,
        dataType: 'json',
        data: JSON.stringify(data),
        contentType: 'application/json',
        success: (response) => {
            if (response.success) {
                $('#exampleModal').modal('hide');
                Swal.fire(
                    'Marca Actualizada',
                    '',
                    'success'
                ); 
                CargarMarcas();
            } else {
                alert(response.error || 'Ocurrió un error al actualizar la marca.');
            }
        },
        error: (jqXHR, textStatus, errorThrown) => {
            if (textStatus === 'timeout') {
                alert("La conexión a internet se ha interrumpido.");
            } else {
                alert('Ocurrió un error inesperado: ' + errorThrown);
                Swal.fire(
                    'Error',
                    errorThrown,
                    'danger'
                ); 
            }
        }
    });
}
</script>