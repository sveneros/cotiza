<!-- header -->
<?php
include('../layout/header_clientes.php');
?>
            <div class="container-fluid ">
                <div class="row m-1">
                    <div class="col-6">
                    <a href="cotizaciones.php" class="btn m-1">
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

<script>
// Variables globales
var monedaActual = 'BS';
var tipoCambio = 1;
var doc; // Variable global para el documento PDF

$(document).ready(function() {
    obtenerTipoCambioActual().then(() => {
        mostrarQuote();
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

    var localData = JSON.parse(localStorage.getItem('sml2025_quotes')) || [];
    var quote = localData.find(q => q.numero == id);
    
    if (quote) {
        $('#tipoCambioActual').text(tipoCambio.toFixed(2));
        renderizarQuote(quote);
    } else {
        $('#quoteDetails').html('<p>No se encontró la cotización.</p>');
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
                            <p>Av. 20 de Octubre Nro 2332, Edif. Guadalquivir</p>
                            <p>Tel/Fax: 2417461</p>
                            <p>Email: info@aplitec.com</p>
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
                <td>${detalle.cantidad}</td>
                <td>
                    <strong>${detalle.producto}</strong><br>
                    ${detalle.descripcion}
                </td>
                <td class="precio-unitario text-end">${formatearMoneda(detalle.precio_unitario, 'BS')}</td>
                <td class="precio-total text-end">${formatearMoneda(detalle.precio_total, 'BS')}</td>
            </tr>`;
    });
    
    html += `
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end"><strong>TOTAL</strong></td>
                            <td class="total-cotizacion text-end"><strong>${formatearMoneda(totalBs, 'BS')}</strong></td>
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
                    <p>Aplitec S.R.L.</p>
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
        doc.text('Av. 20 de Octubre Nro 2332, Edif. Guadalquivir', pageWidth - margin, currentY, {align: 'right'});
        doc.text('Tel/Fax: 2417461', pageWidth - margin, currentY + lineHeight, {align: 'right'});
        doc.text('Email: info@aplitec.com', pageWidth - margin, currentY + (lineHeight * 2), {align: 'right'});
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
    doc.text(`TOTAL: ${formatearMoneda(totalBs, 'BS')}`, pageWidth - margin, finalY, {align: 'right'});
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
    doc.text(`Aplitec S.R.L.`, pageWidth - margin, finalY, {align: 'right'});
    
    // Guardar el PDF
    doc.save(`Cotizacion_${quote.numero}.pdf`);
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
}
</style>