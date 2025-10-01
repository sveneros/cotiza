<!-- apexcharts css-->
<link rel="stylesheet" type="text/css" href="../assets/vendor/apexcharts/apexcharts.css">
<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">

<?php include('../layout/header.php'); ?>

<div class="container-fluid">
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title">Análisis de Precios con IA</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="#" class="f-s-14 f-w-500">
            <span><i class="ph-duotone ph-chart-line f-s-16"></i> Análisis</span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Precios Sugeridos</a>
        </li>
      </ul>
    </div>
  </div>

  

  <div class="row mt-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h5>Distribución de Diferencias</h5>
        </div>
        <div class="card-body">
          <div id="differenceDistributionChart" style="height: 300px;"></div>
        </div>
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h5>Top 5 Productos con Mayor Desviación</h5>
        </div>
        <div class="card-body">
          <div id="topDeviationChart" style="height: 300px;"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5>Factores que Afectan el Precio</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div id="costBreakdownChart" style="height: 300px;"></div>
            </div>
            <div class="col-md-6">
              <div id="marketFactorsChart" style="height: 300px;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5>Comparación de Precios Actuales vs Sugeridos</h5>
        </div>
        <div class="card-body">
          <div id="priceComparisonChart" style="height: 350px;"></div>
        </div>
      </div>
    </div>
  </div> -->

  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5>Productos con Mayor Desviación de Precio</h5>
          <div class="card-header-right">
            <div class="btn-group card-option">
              <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="feather icon-more-vertical"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="#" id="exportExcel">Exportar a Excel</a>
                <a class="dropdown-item" href="#" id="exportPDF">Exportar a PDF</a>
              </div>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table display" id="deviationTable" style="width:100%">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Precio Actual (Bs.)</th>
                  <th>Precio Sugerido (Bs.)</th>
                  <th>Diferencia (Bs.)</th>
                  <th>% Diferencia</th>
                  <th>Margen</th>
                  <th>Factor Demanda</th>
                  <th>Factor Competencia</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>

<script src="../assets/vendor/datatable/jquery.dataTables.min.js"></script>
<script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>

<script>
$(document).ready(function() {
    // Cargar datos de análisis
    $.get('../controllers/product_price_controller.php?action=pricing_analysis', function(data) {
        initCharts(data);
        initDeviationTable(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error al cargar datos:", textStatus, errorThrown);
    });

    function initCharts(analysisData) {
        // Verificar si hay datos
        if (!analysisData || analysisData.length === 0) {
            console.error("No se recibieron datos para los gráficos");
            return;
        }

        // Preparar datos para gráficos
        const labels = analysisData.map(item => item.nombre.substring(0, 15) + (item.nombre.length > 15 ? '...' : ''));
        const currentPrices = analysisData.map(item => parseFloat(item.precio_actual));
        const suggestedPrices = analysisData.map(item => parseFloat(item.precio_sugerido));
        const differences = analysisData.map(item => parseFloat(item.diferencia));
        const percentageDifferences = analysisData.map(item => parseFloat(item.porcentaje_diferencia));
        
        // Gráfico de comparación de precios (ocupando todo el ancho)
        var priceComparisonOptions = {
            series: [{
                name: 'Precio Actual',
                data: currentPrices
            }, {
                name: 'Precio Sugerido (IA)',
                data: suggestedPrices
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded',
                    dataLabels: {
                        position: 'top'
                    }
                },
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return "Bs. " + val.toFixed(2);
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#333"]
                }
            },
            colors: ['#3a86ff', '#8338ec'],
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: labels,
                labels: {
                    rotate: -45,
                    style: {
                        fontSize: '12px'
                    },
                    formatter: function(value) {
                       // return value.length > 15 ? value.substring(0, 15) + '...' : value;
                    }
                },
                tickPlacement: 'on'
            },
            yaxis: {
                title: {
                    text: 'Precio (Bs.)',
                    style: {
                        fontSize: '14px'
                    }
                },
                min: 0,
                labels: {
                    formatter: function(val) {
                        return "Bs. " + val.toFixed(2);
                    }
                }
            },
            fill: {
                opacity: 0.8,
                gradient: {
                    shade: 'light',
                    type: "vertical",
                    shadeIntensity: 0.25,
                    gradientToColors: undefined,
                    inverseColors: true,
                    opacityFrom: 0.85,
                    opacityTo: 0.85,
                    stops: [0, 100]
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "Bs. " + val.toFixed(2);
                    }
                },
                shared: true,
                intersect: false
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                offsetY: -5
            },
            responsive: [{
                breakpoint: 1000,
                options: {
                    plotOptions: {
                        bar: {
                            columnWidth: '70%'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    }
                }
            }]
        };

        var priceComparisonChart = new ApexCharts(document.querySelector("#priceComparisonChart"), priceComparisonOptions);
        priceComparisonChart.render();

        // Gráfico de distribución de diferencias
        const higherCurrent = analysisData.filter(item => parseFloat(item.diferencia) < 0).length;
        const higherSuggested = analysisData.filter(item => parseFloat(item.diferencia) > 0).length;
        const equalPrices = analysisData.filter(item => parseFloat(item.diferencia) === 0).length;
        
        var differenceDistributionOptions = {
            series: [higherCurrent, higherSuggested, equalPrices],
            chart: {
                type: 'donut',
                height: 300,
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            labels: ['Precio Actual Mayor', 'Precio Sugerido Mayor', 'Precios Iguales'],
            colors: ['#ff006e', '#3a86ff', '#ffbe0b'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            legend: {
                position: 'bottom',
                formatter: function(seriesName, opts) {
                    const total = opts.w.globals.series.reduce((a, b) => a + b, 0);
                    const percentage = Math.round((opts.w.globals.series[opts.seriesIndex] / total) * 100);
                    return seriesName + ': ' + percentage + '%';
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        const total = higherCurrent + higherSuggested + equalPrices;
                        const percentage = Math.round((value / total) * 100);
                        return value + ' productos (' + percentage + '%)';
                    }
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Productos',
                                formatter: function() {
                                    return higherCurrent + higherSuggested + equalPrices;
                                }
                            }
                        }
                    }
                }
            }
        };

        var differenceDistributionChart = new ApexCharts(document.querySelector("#differenceDistributionChart"), differenceDistributionOptions);
        differenceDistributionChart.render();

        // Gráfico de Top 5 desviaciones
        const sortedByDeviation = [...analysisData].sort((a, b) => 
            Math.abs(parseFloat(b.porcentaje_diferencia)) - Math.abs(parseFloat(a.porcentaje_diferencia))
        ).slice(0, 5);
        
        var topDeviationOptions = {
            series: [{
                name: '% Diferencia',
                data: sortedByDeviation.map(item => parseFloat(item.porcentaje_diferencia))
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: {
                    show: true
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toFixed(2) + '%';
                },
                style: {
                    fontSize: '12px',
                    colors: ["#333"]
                }
            },
            colors: ['#ff006e'],
            xaxis: {
                categories: sortedByDeviation.map(item => 
                    item.nombre.substring(0, 20) + (item.nombre.length > 20 ? '...' : '')
                ),
                title: {
                    text: '% Diferencia',
                    style: {
                        fontSize: '14px'
                    }
                },
                labels: {
                    formatter: function(val) {
                        return val + '%';
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val.toFixed(2) + '%';
                    }
                }
            }
        };

        var topDeviationChart = new ApexCharts(document.querySelector("#topDeviationChart"), topDeviationOptions);
        topDeviationChart.render();

        // Gráfico de desglose de costos (usando el primer producto como ejemplo)
        if (analysisData.length > 0) {
            const sampleProduct = analysisData[0];
            const costData = [
                parseFloat(sampleProduct.base_cost_local),
                parseFloat(sampleProduct.base_cost_local * sampleProduct.import_tax / 100),
                parseFloat(sampleProduct.shipping_cost),
                parseFloat(sampleProduct.local_transport),
                parseFloat(sampleProduct.storage_cost),
                parseFloat(sampleProduct.insurance),
                parseFloat(sampleProduct.customs_fees)
            ];
            
            var costBreakdownOptions = {
                series: costData,
                chart: {
                    type: 'pie',
                    height: 300,
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                labels: ['Costo Base', 'Impuestos', 'Envío', 'Transporte', 'Almacenamiento', 'Seguro', 'Aduana'],
                colors: ['#3a86ff', '#ff006e', '#ffbe0b', '#8338ec', '#fb5607', '#06d6a0', '#118ab2'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                title: {
                    text: `Desglose de Costos: ${sampleProduct.nombre.substring(0, 20)}...`,
                    align: 'center',
                    style: {
                        fontSize: '14px'
                    }
                },
                legend: {
                    position: 'bottom',
                    formatter: function(seriesName, opts) {
                        return seriesName + ': Bs. ' + opts.w.globals.series[opts.seriesIndex].toFixed(2);
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return "Bs. " + value.toFixed(2);
                        }
                    }
                },
                dataLabels: {
                    formatter: function(val, opts) {
                        return opts.w.config.labels[opts.seriesIndex] + ': Bs. ' + val.toFixed(2);
                    }
                }
            };

            var costBreakdownChart = new ApexCharts(document.querySelector("#costBreakdownChart"), costBreakdownOptions);
            costBreakdownChart.render();
        }

        // Gráfico de factores de mercado
        const radarSeries = analysisData.slice(0, 5).map(product => ({
            name: product.nombre.substring(0, 15) + (product.nombre.length > 15 ? '...' : ''),
            data: [
                parseFloat(product.profit_margin),
                parseFloat(product.market_demand_factor) * 100,
                parseFloat(product.competition_factor) * 100
            ]
        }));
        
        var marketFactorsOptions = {
            series: radarSeries,
            chart: {
                height: 300,
                type: 'radar',
                dropShadow: {
                    enabled: true,
                    blur: 1,
                    left: 1,
                    top: 1
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            title: {
                text: 'Factores de Mercado por Producto',
                align: 'center',
                style: {
                    fontSize: '14px'
                }
            },
            colors: ['#3a86ff', '#ff006e', '#ffbe0b', '#8338ec', '#fb5607'],
            stroke: {
                width: 2
            },
            fill: {
                opacity: 0.1
            },
            markers: {
                size: 4,
                hover: {
                    size: 6
                }
            },
            xaxis: {
                categories: ['Margen Ganancia (%)', 'Factor Demanda', 'Factor Competencia']
            },
            yaxis: {
                min: 0,
                max: 100,
                tickAmount: 5,
                labels: {
                    formatter: function(val, i) {
                        if (i === 1 || i === 2) {
                            // Mostrar valor original para factores de demanda/competencia
                            return (val / 100).toFixed(2);
                        }
                        return val.toFixed(2);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val, { seriesIndex, dataPointIndex }) {
                        if (dataPointIndex === 1 || dataPointIndex === 2) {
                            // Mostrar valor original para factores de demanda/competencia
                            return (val / 100).toFixed(2);
                        }
                        return val.toFixed(2) + '%';
                    }
                }
            },
            legend: {
                position: 'bottom',
                markers: {
                    radius: 3
                }
            }
        };

        var marketFactorsChart = new ApexCharts(document.querySelector("#marketFactorsChart"), marketFactorsOptions);
        marketFactorsChart.render();
    }

    function initDeviationTable(analysisData) {
        $('#deviationTable').DataTable({
            data: analysisData,
            columns: [
                { 
                    data: 'nombre',
                    render: function(data, type, row) {
                        if (type === 'display' && data.length > 30) {
                            return data.substring(0, 30) + '...';
                        }
                        return data;
                    }
                },
                { 
                    data: 'precio_actual',
                    render: function(data) {
                        return 'Bs. ' + parseFloat(data).toFixed(2);
                    }
                },
                { 
                    data: 'precio_sugerido',
                    render: function(data) {
                        return 'Bs. ' + parseFloat(data).toFixed(2);
                    }
                },
                { 
                    data: 'diferencia',
                    render: function(data) {
                        const diff = parseFloat(data);
                        const color = diff > 0 ? 'text-success' : diff < 0 ? 'text-danger' : 'text-muted';
                        const symbol = diff > 0 ? '+' : '';
                        return `<span class="${color}">${symbol}Bs. ${Math.abs(diff).toFixed(2)}</span>`;
                    }
                },
                { 
                    data: 'porcentaje_diferencia',
                    render: function(data) {
                        const percent = parseFloat(data);
                        const color = percent > 0 ? 'text-success' : percent < 0 ? 'text-danger' : 'text-muted';
                        const symbol = percent > 0 ? '+' : '';
                        return `<span class="${color}">${symbol}${Math.abs(percent).toFixed(2)}%</span>`;
                    }
                },
                { 
                    data: 'profit_margin',
                    render: function(data) {
                        return parseFloat(data).toFixed(2) + '%';
                    }
                },
                { 
                    data: 'market_demand_factor',
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                { 
                    data: 'competition_factor',
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                }
            ],
            order: [[4, 'desc']],
            dom: '<"top"Bf>rt<"bottom"lip>',
            pageLength: 10,
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    title: 'Analisis_Precios',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    title: 'Analisis_Precios',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Imprimir',
                    title: 'Analisis_Precios',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ],
          
            responsive: true
        });
    }
});
</script>