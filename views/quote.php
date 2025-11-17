<!-- header -->
<?php
include('../layout/header.php');
?>
            <div class="container-fluid ">
                <div class="row m-1">
                    <div class="col-6">
                    <a href="quotes.php" class="btn m-1">
                            <i class="ti ti-door"></i> Volver
                        </a>
                    </div>

                    <div class="col-6">
                        <button type="button" class="btn btn-lg" onclick="generatePDF()">
                            <i class="ti ti-printer"></i> Exportar a PDF
                        </button>
                        <button type="button" class="btn btn-lg ms-2" id="btnConvertToDollars">
                            <i class="ti ti-currency-dollar"></i> <span id="btnConvertText">Mostrar en Dólares</span>
                        </button>
                        <!-- Botones de edición -->
                        <!-- <button type="button" class="btn btn-lg ms-2" id="btnEditQuote">
                            <i class="ti ti-edit"></i> Editar Cotización
                        </button> -->
                        <button type="button" class="btn btn-lg ms-2 btn-success" id="btnApproveQuote" style="display: none;">
                            <i class="ti ti-check"></i> Aprobar Cotización
                        </button>
                        <button type="button" class="btn btn-lg ms-2 btn-primary" id="btnSaveChanges" style="display: none;">
                            <i class="ti ti-device-floppy"></i> Guardar Cambios
                        </button>
                        <button type="button" class="btn btn-lg ms-2 btn-light" id="btnCancelEdit" style="display: none;">
                            <i class="ti ti-x"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Invoice start -->
            <div class="container invoice-container" id="pdfContent">
                <div class="row">
                    <div id="quoteDetails"></div>
                </div>
            </div>
            <!-- Invoice end -->
       
    <!-- footer -->
    <?php
    include('../layout/footer.php');
    ?>

<!-- Include jsPDF library -->
<script src="../assets/js/jspdf.umd.min.js"></script>
<script src="../assets/js/jspdf.plugin.autotable.min.js"></script>

<!-- Modal para editar producto -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm">
                    <input type="hidden" id="editItemIndex">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Producto</label>
                                <input type="text" class="form-control" id="editProductName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Código</label>
                                <input type="text" class="form-control" id="editProductCode">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Cantidad</label>
                                <input type="number" class="form-control" id="editProductQuantity" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Precio Unitario (Bs.)</label>
                                <input type="number" step="0.01" class="form-control" id="editProductPrice" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Total (Bs.)</label>
                                <input type="text" class="form-control" id="editProductTotal" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" id="editProductDescription" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnUpdateProduct">Actualizar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
var monedaActual = 'BS';
var tipoCambio = 1;
var doc; // Variable global para el documento PDF
var editMode = false;
var originalQuoteData = null;
var quote = {}; // Almacenará los datos de la cotización

$(document).ready(function() {
    obtenerTipoCambioActual().then(() => {
        mostrarQuote();
    });

    // Botón para entrar en modo edición
    $('#btnEditQuote').click(function() {
        enterEditMode();
    });

    // Botón para aprobar cotización
    $('#btnApproveQuote').click(function() {
        Swal.fire({
            title: '¿Aprobar esta cotización?',
            text: "Esta acción cambiará el estado a 'APR' (Aprobado)",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, aprobar',   
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                saveChanges(true);
            }
        });
    });

    // Botón para guardar cambios
    $('#btnSaveChanges').click(function() {
        saveChanges(false);
    });

    // Botón para cancelar edición
    $('#btnCancelEdit').click(function() {
        exitEditMode();
    });

    // Botón para actualizar producto en modal
    $('#btnUpdateProduct').click(function() {
        const index = $('#editItemIndex').val();
        const producto = quote.detalle[index];
        
        producto.producto = $('#editProductName').val();
        producto.codigo = $('#editProductCode').val();
        producto.cantidad = parseFloat($('#editProductQuantity').val()) || 0;
        producto.precio_unitario = parseFloat($('#editProductPrice').val()) || 0;
        producto.precio_total = producto.cantidad * producto.precio_unitario;
        producto.descripcion = $('#editProductDescription').val();
        
        // Cerrar modal
        bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
        
        // Re-renderizar
        renderizarQuote(quote);
        enterEditMode(); // Re-entrar en modo edición
        updateTotals();
    });
});

function obtenerParametroId() {
    var params = new URLSearchParams(window.location.search);
    return params.get('id');
}

function formatearMoneda(valor, moneda) {
    if (moneda === 'USD') {
        return `$${parseFloat(valor).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`;
    } else {
        return `Bs. ${parseFloat(valor).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`;
    }
}

function convertirMonto(monto, aDolares) {
    return aDolares ? 
        (parseFloat(monto.replace(/,/g, '')) / tipoCambio).toFixed(2) : 
        (parseFloat(monto.replace(/,/g, '')) * tipoCambio).toFixed(2);
}

function obtenerTipoCambioActual() {
    return $.ajax({
        url: '../controllers/tipo_cambio_controller.php?current=true',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.tasa) {
                tipoCambio = parseFloat(response.tasa);
                console.log('Tipo de cambio actualizado:', tipoCambio);
            } else {
                console.warn('No se pudo obtener el tipo de cambio actual, usando valor por defecto');
            }
        },
        error: function() {
            console.error('Error al obtener tipo de cambio, usando valor por defecto');
        }
    });
}

function mostrarQuote() {
    var id = obtenerParametroId();
    if (!id) return;

    // Primero intentar con localStorage
    var localData = JSON.parse(localStorage.getItem('sml2025_quotes')) || [];
    var localQuote = localData.find(q => q.numero == id);
    
    if (localQuote) {
        $('#tipoCambioActual').text(tipoCambio.toFixed(2));
        quote = localQuote;
        renderizarQuote(quote);
    } else {
        // Si no está en localStorage, obtener del servidor
        $.ajax({
            url: '../controllers/cotizacion_controller.php?id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Formatear los datos para que coincidan con el formato esperado
                    const doc = response.documento;
                    const detalle = response.detalle;
                    
                    quote = {
                        numero: doc.id_documento.toString(),
                        nombre: doc.nombre,
                        apellido1: doc.apellido1,
                        apellido2: doc.apellido2,
                        direccion: doc.direccion,
                        telefono: doc.telefono,
                        celular: doc.celular,
                        fecha: doc.fecha.split(' ')[0], // Solo la fecha
                        vendedor: 'Representante de Ventas', // Puedes ajustar esto
                        detalle: detalle.map(item => ({
                            producto: item.producto,
                            descripcion: item.descripcion,
                            codigo: item.codigo || '',
                            cantidad: item.cantidad,
                            precio_unitario: item.precio_unitario,
                            precio_total: item.precio_total,
                            id_marca: item.id_marca || 0,
                            marca: item.marca || ''
                        }))
                    };
                    
                    renderizarQuote(quote);
                } else {
                    $('#quoteDetails').html('<p>No se encontró la cotización.</p>');
                }
            },
            error: function() {
                $('#quoteDetails').html('<p>Error al cargar la cotización.</p>');
            }
        });
    }
}

function renderizarQuote(quote) {
    // Calcular totales
    var totalBs = quote.detalle.reduce((sum, item) => sum + parseFloat(item.precio_total), 0);
    var totalUsd = (totalBs / tipoCambio).toFixed(2);
    
    var html = `
        <div class="quote-page">
            <div class="quote-header">
                <div class="row">
                    <div class="col-md-6">
                        <img src="../assets/images/logo/1.png" class="logo" alt="Logo">
                        <div class="company-info">
                            <p>Av. Irpavi 3334, Piso 1</p>
                            <p>Tel/Fax: 22332223</p>
                            <p>Email: info@IKNOWENGINEERING.com</p>
                            <p>La Paz - Bolivia</p>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <h2 class="text-primary">COTIZACIÓN</h2>
                        <p><strong>Nro:</strong> ${quote.numero}</p>
                        <p><strong>Para:</strong> ${quote.nombre} ${quote.apellido1} ${quote.apellido2}</p>
                        <p><strong>Fecha:</strong> ${quote.fecha}</p>
                        <p><strong>Tipo de cambio:</strong> Bs. <span id="tipoCambioActual">${tipoCambio.toFixed(2)}</span></p>
                    </div>
                </div>
                
                <div class="client-info mt-3">
                    <p><strong>Cliente:</strong> ${quote.nombre} ${quote.apellido1} ${quote.apellido2}</p>
                    <p><strong>Dirección:</strong> ${quote.direccion}</p>
                    <p><strong>Teléfono:</strong> ${quote.telefono} <strong>Celular:</strong> ${quote.celular}</p>
                </div>
            </div>

            <div class="quote-body mt-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">Item</th>
                            <th width="15%">Código</th>
                            <th width="10%">Cant.</th>
                            <th width="45%">Descripción</th>
                            <th width="15%">P.U. (<span id="monedaHeader">Bs.</span>)</th>
                            <th width="10%">Total (<span id="monedaHeaderTotal">Bs.</span>)</th>
                        </tr>
                    </thead>
                    <tbody>`;
    
    quote.detalle.forEach((detalle, index) => {
        html += `
            <tr>
                <td>${index + 1}</td>
                <td>${detalle.codigo || 'N/A'}</td>
                <td>${editMode ? `<input type="number" class="form-control form-control-sm edit-quantity" value="${detalle.cantidad}" min="1">` : detalle.cantidad}</td>
                <td>
                    <strong>${detalle.producto}</strong><br>
                    ${detalle.descripcion}
                </td>
                <td class="precio-unitario text-end">${editMode ? `<input type="number" class="form-control form-control-sm edit-price" value="${detalle.precio_unitario}" step="0.01" min="0">` : formatearMoneda(detalle.precio_unitario, 'BS')}</td>
                <td class="precio-total text-end ${editMode ? 'editable-total' : ''}">${formatearMoneda(detalle.precio_total, 'BS')}</td>
            </tr>`;
        
        if (editMode) {
            html += `
                <div class="d-flex gap-1 mt-1">
                    <button class="btn btn-sm btn-outline-primary edit-product-btn" data-index="${index}">
                        <i class="ti ti-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger remove-product-btn" data-index="${index}">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>`;
        }
    });
    
    if (editMode) {
        html += `
            <tr class="add-product-row">
                <td colspan="4">
                    <button class="btn btn-sm btn-success" id="btnAddProduct">
                        <i class="ti ti-plus"></i> Agregar Producto
                    </button>
                </td>
                <td class="text-end"><strong>SUBTOTAL</strong></td>
                <td class="text-end"><strong class="subtotal">${formatearMoneda(calculateSubtotal(), 'BS')}</strong></td>
            </tr>`;
    }
    
    html += `
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end"><strong>TOTAL</strong></td>
                            <td class="total-cotizacion text-end"><strong>${formatearMoneda(totalBs + 50, 'BS')}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="quote-footer mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Garantía:</strong> 1 año por defectos de fábrica</p>
                        <p><strong>Validez de la oferta:</strong> 30 días Calendario</p>
                        <p><strong>Forma de pago:</strong> A convenir</p>
                        <p><strong>Costo de envío:</strong> <span class="envio">${formatearMoneda(50, 'BS')}</span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Tiempo de entrega:</strong> Aproximadamente 40 días calendario; computables desde el siguiente día hábil de recibida su Orden de Compra, firma de contrato o Pago por adelantado</p>
                    </div>
                </div>
                <div class="signature mt-4 text-end">
                    <p>Atentamente,</p>
                    <p class="mt-5">_________________________</p>
                    <p>${quote.vendedor || 'Representante de Ventas'}</p>
                    <p>IKNOW ENGINEERING S.R.L.</p>
                </div>
            </div>
        </div>
    `;
    
    $('#quoteDetails').html(html);
    
    // Evento para el botón de conversión
    $('#btnConvertToDollars').off('click').on('click', function() {
        if (monedaActual === 'BS') {
            convertirADolares();
            $(this).html('<i class="ti ti-currency-dollar"></i> Mostrar en Bolivianos');
            monedaActual = 'USD';
        } else {
            convertirABolivianos();
            $(this).html('<i class="ti ti-currency-dollar"></i> Mostrar en Dólares');
            monedaActual = 'BS';
        }
    });

    // Eventos para edición si estamos en modo edición
    if (editMode) {
        // Eventos para edición rápida
        $('.edit-quantity, .edit-price').on('change input', function() {
            const row = $(this).closest('tr');
            const index = row.index();
            
            // Actualizar datos en memoria
            quote.detalle[index].cantidad = parseFloat($(this).closest('tr').find('.edit-quantity').val()) || 0;
            quote.detalle[index].precio_unitario = parseFloat($(this).closest('tr').find('.edit-price').val()) || 0;
            quote.detalle[index].precio_total = quote.detalle[index].cantidad * quote.detalle[index].precio_unitario;
            
            // Actualizar total en fila
            row.find('.editable-total').text(formatearMoneda(quote.detalle[index].precio_total, 'BS'));
            
            // Actualizar totales
            updateTotals();
        });
        
        // Botón para agregar producto
        $('#btnAddProduct').click(showAddProductModal);
        
        // Botones para editar producto (modal completo)
        $('.edit-product-btn').click(function() {
            const index = $(this).data('index');
            showEditProductModal(index);
        });
        
        // Botones para eliminar producto
        $('.remove-product-btn').click(function() {
            const index = $(this).data('index');
            
            Swal.fire({
                title: '¿Eliminar este producto?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    quote.detalle.splice(index, 1);
                    renderizarQuote(quote);
                    enterEditMode(); // Re-entrar en modo edición
                    updateTotals();
                }
            });
        });
    }
}

function convertirADolares() {
    $('.precio-unitario').each(function() {
        var valorBs = $(this).text().replace('Bs. ', '').replace(/,/g, '').trim();
        $(this).text(formatearMoneda(convertirMonto(valorBs, true), 'USD'));
    });
    
    $('.precio-total').each(function() {
        var valorBs = $(this).text().replace('Bs. ', '').replace(/,/g, '').trim();
        $(this).text(formatearMoneda(convertirMonto(valorBs, true), 'USD'));
    });
    
    $('.envio').each(function() {
        var valorBs = $(this).text().replace('Bs. ', '').replace(/,/g, '').trim();
        $(this).text(formatearMoneda(convertirMonto(valorBs, true), 'USD'));
    });
    
    $('.total-cotizacion').each(function() {
        var valorBs = $(this).text().replace('Bs. ', '').replace(/,/g, '').trim();
        $(this).text(formatearMoneda(convertirMonto(valorBs, true), 'USD'));
    });
    
    $('#monedaHeader, #monedaHeaderTotal').text('$');
}

function convertirABolivianos() {
    $('.precio-unitario').each(function() {
        var valorUsd = $(this).text().replace('$', '').replace(/,/g, '').trim();
        $(this).text(formatearMoneda(convertirMonto(valorUsd, false), 'BS'));
    });
    
    $('.precio-total').each(function() {
        var valorUsd = $(this).text().replace('$', '').replace(/,/g, '').trim();
        $(this).text(formatearMoneda(convertirMonto(valorUsd, false), 'BS'));
    });
    
    $('.envio').each(function() {
        var valorUsd = $(this).text().replace('$', '').replace(/,/g, '').trim();
        $(this).text(formatearMoneda(convertirMonto(valorUsd, false), 'BS'));
    });
    
    $('.total-cotizacion').each(function() {
        var valorUsd = $(this).text().replace('$', '').replace(/,/g, '').trim();
        $(this).text(formatearMoneda(convertirMonto(valorUsd, false), 'BS'));
    });
    
    $('#monedaHeader, #monedaHeaderTotal').text('Bs.');
}

// Función para generar PDF con jsPDF
function generatePDF() {
    const { jsPDF } = window.jspdf;
    doc = new jsPDF();
    
    var quoteId = obtenerParametroId();
    var localData = JSON.parse(localStorage.getItem('sml2025_quotes')) || [];
    var quote = localData.find(q => q.numero == quoteId);
    
    if (!quote) {
        alert('No se encontró la cotización');
        return;
    }
    
    // Configuración inicial
    var pageWidth = doc.internal.pageSize.getWidth();
    var margin = 15;
    var lineHeight = 7;
    var currentY = margin;
    
    // Calcular totales
    var totalBs = quote.detalle.reduce((sum, item) => sum + parseFloat(item.precio_total), 0);
    
    // Función para agregar nueva página con encabezado
    function addNewPage() {
        doc.addPage();
        currentY = margin;
        addHeader();
    }
    
    // Función para agregar encabezado
    function addHeader() {
        // Logo
        var imgData = '../assets/images/logo/1.png';
        doc.addImage(imgData, 'PNG', margin, currentY, 55, 15);
        
        // Información de la empresa
        doc.setFontSize(10);
        doc.text('Av. Irpavi, Piso 1', pageWidth - margin, currentY, {align: 'right'});
        doc.text('Tel/Fax: 7777', pageWidth - margin, currentY + lineHeight, {align: 'right'});
        doc.text('Email: INFO@IKNOWENGINEERING.com', pageWidth - margin, currentY + (lineHeight * 2), {align: 'right'});
        doc.text('La Paz - Bolivia', pageWidth - margin, currentY + (lineHeight * 3), {align: 'right'});
        
        currentY += 25;
        
        // Título Cotización
        doc.setFontSize(14);
        doc.setFont(undefined, 'bold');
        doc.text('COTIZACIÓN', pageWidth / 2, currentY, {align: 'center'});
        currentY += lineHeight;
        
        // Número de cotización y fecha
        doc.setFontSize(10);
        doc.text(`Nro: ${quote.numero}`, margin, currentY);
        doc.text(`Fecha: ${quote.fecha}`, pageWidth - margin, currentY, {align: 'right'});
        currentY += lineHeight;
        
        // Información del cliente
        doc.text(`Para: ${quote.nombre} ${quote.apellido1} ${quote.apellido2}`, margin, currentY);
        currentY += lineHeight;
        doc.text(`Dirección: ${quote.direccion}`, margin, currentY);
        currentY += lineHeight;
        doc.text(`Teléfono: ${quote.telefono}  Celular: ${quote.celular}`, margin, currentY);
        currentY += lineHeight * 2;
    }
    
    // Agregar primera página con encabezado
    addHeader();
    
    // Tabla de productos
    var columns = [
        {title: "Item", dataKey: "item"},
        {title: "Código", dataKey: "codigo"},
        {title: "Cant.", dataKey: "cantidad"},
        {title: "Descripción", dataKey: "descripcion"},
        {title: "P.U. (Bs.)", dataKey: "precio_unitario"},
        {title: "Total (Bs.)", dataKey: "precio_total"}
    ];
    
    var rows = quote.detalle.map((item, index) => ({
        item: index + 1,
        codigo: item.codigo || 'N/A',
        cantidad: item.cantidad,
        descripcion: `${item.producto}\n${item.descripcion}`,
        precio_unitario: formatearMoneda(item.precio_unitario, 'BS'),
        precio_total: formatearMoneda(item.precio_total, 'BS')
    }));
    
    doc.autoTable({
        columns: columns,
        body: rows,
        startY: currentY,
        margin: {top: currentY},
        styles: {
            fontSize: 8,
            cellPadding: 2,
            overflow: 'linebreak'
        },
        columnStyles: {
            item: {cellWidth: 10},
            codigo: {cellWidth: 20},
            cantidad: {cellWidth: 15},
            descripcion: {cellWidth: 85},
            precio_unitario: {cellWidth: 25, halign: 'right'},
            precio_total: {cellWidth: 25, halign: 'right'}
        },
        didDrawPage: function(data) {
            // Agregar número de página
            var pageCount = doc.internal.getNumberOfPages();
            doc.setFontSize(8);
            for (var i = 1; i <= pageCount; i++) {
                doc.setPage(i);
                doc.text(`Página ${i} de ${pageCount}`, pageWidth -10 - margin, doc.internal.pageSize.getHeight() - 10);
            }
        }
    });
    
    // Agregar total y pie de página en la última página
    var finalY = doc.lastAutoTable.finalY + 10;
    
    // Verificar si hay espacio suficiente, si no, agregar nueva página
    if (finalY > doc.internal.pageSize.getHeight() - 50) {
        addNewPage();
        finalY = margin + 10;
    }
    
    // Total
    doc.setFontSize(10);
    doc.setFont(undefined, 'bold');
    doc.text(`TOTAL: ${formatearMoneda(totalBs + 50, 'BS')}`, pageWidth - margin, finalY, {align: 'right'});
    finalY += lineHeight * 2;
    
    // Términos y condiciones
    doc.setFont(undefined, 'normal');
    doc.text(`Garantía: 1 año por defectos de fábrica`, margin, finalY);
    finalY += lineHeight;
    doc.text(`Validez de la oferta: 30 días Calendario`, margin, finalY);
    finalY += lineHeight;
    doc.text(`Forma de pago: A convenir`, margin, finalY);
    finalY += lineHeight;
    doc.text(`Costo de envío: ${formatearMoneda(50, 'BS')}`, margin, finalY);
    finalY += lineHeight * 2;
    doc.text(`Tiempo de entrega: Aproximadamente 40 días calendario; computables desde el siguiente día hábil de recibida su Orden de Compra, firma de contrato o Pago por adelantado`, margin, finalY, {maxWidth: pageWidth - (margin * 2)});
    finalY += lineHeight * 4;
    
    // Firma
    doc.text(`Atentamente,`, pageWidth - margin, finalY, {align: 'right'});
    finalY += lineHeight * 5;
    doc.text(`_________________________`, pageWidth - margin, finalY, {align: 'right'});
    finalY += lineHeight;
    doc.text(`${quote.vendedor || 'Representante de Ventas'}`, pageWidth - margin, finalY, {align: 'right'});
    finalY += lineHeight;
    doc.text(`IKNOW ENGINEERING S.R.L.`, pageWidth - margin, finalY, {align: 'right'});
    
    // Guardar el PDF
    doc.save(`Cotizacion_${quote.numero}.pdf`);
}

// Función para entrar en modo edición
function enterEditMode() {
    editMode = true;
    originalQuoteData = JSON.parse(JSON.stringify(quote)); // Copia profunda
    
    // Mostrar/ocultar botones
    $('#btnEditQuote').hide();
    $('#btnConvertToDollars').hide();
    $('#btnApproveQuote').show();
    $('#btnSaveChanges').show();
    $('#btnCancelEdit').show();
    
    // Re-renderizar en modo edición
    renderizarQuote(quote);
}

// Función para salir del modo edición
function exitEditMode() {
    editMode = false;
    
    // Restaurar datos originales
    quote = JSON.parse(JSON.stringify(originalQuoteData));
    renderizarQuote(quote);
    
    // Mostrar/ocultar botones
    $('#btnEditQuote').show();
    $('#btnConvertToDollars').show();
    $('#btnApproveQuote').hide();
    $('#btnSaveChanges').hide();
    $('#btnCancelEdit').hide();
}

// Función para mostrar modal de agregar producto
function showAddProductModal() {
    $('#editItemIndex').val('-1'); // Indicador de nuevo producto
    $('#editProductName').val('');
    $('#editProductCode').val('');
    $('#editProductQuantity').val(1);
    $('#editProductPrice').val(0);
    $('#editProductDescription').val('');
    $('#editProductTotal').val('0.00');
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
    modal.show();
}

// Función para calcular subtotal
function calculateSubtotal() {
    return quote.detalle.reduce((sum, item) => sum + (item.precio_total || 0), 0);
}

// Función para actualizar totales
function updateTotals() {
    const subtotal = calculateSubtotal();
    const envio = 50; // Costo fijo de envío
    
    $('.subtotal').text(formatearMoneda(subtotal, 'BS'));
    $('.total-cotizacion').html(`<strong>${formatearMoneda(subtotal + envio, 'BS')}</strong>`);
}

// Función para guardar cambios
function saveChanges(approve) {
    const id = obtenerParametroId();
    if (!id) return;
    
    // Preparar datos para enviar
    const data = {
        id_documento: parseInt(id),
        detalle: quote.detalle.map(item => ({
            producto: item.producto,
            descripcion: item.descripcion || '',
            codigo: item.codigo || '',
            cantidad: item.cantidad,
            precio_unitario: item.precio_unitario,
            precio_total: item.precio_total,
            id_marca: item.id_marca || 0,
            marca: item.marca || ''
        })),
        total: calculateSubtotal() + 50, // Subtotal + envío
        estado: approve ? 'APR' : 'COT'
    };
    
    // Enviar al servidor
    $.ajax({
        url: '../controllers/cotizacion_controller.php',
        type: 'PUT',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: approve ? 'Cotización Aprobada' : 'Cambios Guardados',
                    text: response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Recargar la cotización con los nuevos datos
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.error || 'Error al guardar cambios', 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', 'Error al conectar con el servidor: ' + error, 'error');
        }
    });
}
</script>

<style>
.quote-page {
    font-family: Arial, sans-serif;
    font-size: 12px;
    color: #333;
    padding: 20px;
    background-color: white;
}

.quote-header {
    border-bottom: 2px solid #333;
    padding-bottom: 15px;
    margin-bottom: 15px;
}

.quote-header .logo {
    max-height: 80px;
    margin-bottom: 10px;
}

.company-info {
    font-size: 11px;
    margin-bottom: 10px;
}

.client-info {
    font-size: 11px;
}

.quote-body table {
    width: 100%;
    border-collapse: collapse;
}

.quote-body th {
    background-color: #f5f5f5;
    text-align: center;
    padding: 5px;
    border: 1px solid #ddd;
}

.quote-body td {
    padding: 5px;
    border: 1px solid #ddd;
    vertical-align: top;
}

.quote-footer {
    margin-top: 20px;
    font-size: 11px;
}

.signature {
    margin-top: 50px;
}

.text-end {
    text-align: right;
}

/* Estilos para modo edición */
.edit-quantity, .edit-price {
    width: 80px;
    display: inline-block;
}

.editable-total {
    font-weight: bold;
}

.add-product-row td {
    background-color: #f8f9fa;
}

/* Estilos para el modal */
.modal-content {
    border-radius: 10px;
}

.modal-header {
    border-bottom: 1px solid #dee2e6;
    background-color: #f8f9fa;
    border-radius: 10px 10px 0 0;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
    background-color: #f8f9fa;
    border-radius: 0 0 10px 10px;
}

@media print {
    body {
        background-color: white;
    }
    
    .container-fluid {
        display: none;
    }
    
    .quote-page {
        padding: 0;
        margin: 0;
    }
    
    .quote-header {
        page-break-after: avoid;
    }
    
    .quote-body tr {
        page-break-inside: avoid;
    }
    
    /* Ocultar botones de edición al imprimir */
    #btnEditQuote, #btnApproveQuote, #btnSaveChanges, #btnCancelEdit {
        display: none !important;
    }
}
</style>