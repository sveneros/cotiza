
<?php
        include('../layout/header.php');
        ?>
<!-- apexcharts css-->
<link rel="stylesheet" type="text/css" href="../assets/vendor/apexcharts/apexcharts.css">
      
            <div class="container-fluid">
                <!-- Breadcrumb start -->
                <div class="row m-1">
                    <div class="col-12 ">
                        <h4 class="main-title">Inicio</h4>
                        <ul class="app-line-breadcrumbs mb-3">
                            <li class="">
                                <a href="#" class="f-s-14 f-w-500">
                      <span>
                        <i class="ph-duotone  ph-newspaper f-s-16"></i> Aplitec
                      </span>
                                </a>
                            </li>
                            <li class="active">
                                <a href="#" class="f-s-14 f-w-500">Inicio</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Breadcrumb end -->
                <!-- Sección de Reporte Estadístico -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="header-title-text">Reporte Estadístico de Cotizaciones</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <select id="periodoReporte" class="form-select">
                                            <option value="semanal">Semanal</option>
                                            <option value="mensual">Mensual</option>
                                            <option value="trimestral">Trimestral</option>
                                            <option value="personalizado">Personalizado</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3" id="fechaInicioContainer" style="display:none;">
                                        <input type="date" id="fechaInicio" class="form-control">
                                    </div>
                                    <div class="col-md-3" id="fechaFinContainer" style="display:none;">
                                        <input type="date" id="fechaFin" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <button id="btnGenerarReporte" class="btn btn-primary">Generar Reporte</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="loadingReporte" class="text-center" style="display:none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p>Generando reporte...</p>
                                </div>
                                
                                <!-- Gráfico de Tiempo de Aprobación -->
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6>Tiempo Promedio de Aprobación</h6>
                                            </div>
                                            <div class="card-body">
                                                <div id="tiempoAprobacionChart"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                  
                                </div>
                                
                                <!-- Gráfico de Evolución -->
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6>Evolución de Cotizaciones</h6>
                                            </div>
                                            <div class="card-body">
                                                <div id="evolucionChart"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Blank start -->
                <div class="row">
                    
                    


                    <!-- order-3-lg -->
                    <div class="col-md-12 col-lg-12 order-2-lg">
                        <div class="card">
                        <div class="card-header">
                        <h5 class="header-title-text">Productos más solicitados (Top 5) según Algoritmo Genético</h5>
                        <button class="btn btn-primary b-r-right" id="btnCargar">Generar Recomendados</button>
                        </div>
                        <div class="card-body">
                        
                            
                            <div class="mt-3">
                            <div class="today-task-input">
                                <div class="input-group mb-3">
                                
                                
                                <span id="loading" class="loading" style="display:none;">Cargando...</span>
                                </div>
                            </div>
                           
                                <div id="chart"></div>
                                <!-- <div id="resultado" class="table-responsive">
                                    <table id="productTable" class="table align-middle mb-0 style="display:none;">
                                        <thead>
                                            <tr>
                                                <th>Pos.</th>
                                                <th>Producto</th>
                                                <th>Solicitudes</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <div id="errorMessage" class="error" style="display:none;"></div>
                                </div> -->
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-12 order-2-lg">
                        <div class="card">
                            <div id="chart2"></div>
                        </div>
                    </div>

                    
                   
                    <div class="col-md-6 col-lg-6 order-2-lg">

                        <div class="card">
                            <div class="card-header">
                                <h5 class="header-title-text">Análisis Genético de Cotizaciones</h5><button id="btnAnalizar" class="btn btn-warning b-r-right">Realizar Análisis</button>
                            </div>
                            <div class="card-body">
                                
                                
                                
                                <span id="loading2" class="loading" style="display:none;">Analizando datos...</span>
                                
                                <div id="resultados">
                                    <div class="card" id="clienteMasCotizaciones">
                                        <p class="text-secondary mb-0 ms-4">Cliente con más cotizaciones</p>
                                        <div class="resultado"></div>
                                    </div>
                                    
                                    <div class="card" id="clienteMenosCotizaciones">
                                        
                                        <p class="text-secondary mb-0 ms-4">Cliente con menos cotizaciones</p>
                                        <div class="resultado"></div>
                                    </div>
                                    
                                    <div class="card" id="marcaMasCotizada">
                                        
                                        <p class="text-secondary mb-0 ms-4">Marca más cotizada</p>
                                        <div class="resultado"></div>
                                    </div>
                                    
                                    <div class="card" id="marcaMenosCotizada">
                                        
                                        <p class="text-secondary mb-0 ms-4">Marca menos cotizada</p>
                                        <div class="resultado"></div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6 order-2-lg">

                        <div class="card">
                            <div id="comparisonChart"></div>
                        </div>
                    </div>
                    
                </div>
                <!-- Blank end -->
            </div>
     
    <?php
    include('../layout/footer.php');
    ?>

<script>
        $(document).ready(function() {
            $('#btnCargar').click(function() {
                cargarProductos();
                $('#btnCargar').hide();
            });
            
            // Cargar automáticamente al inicio (opcional)
            // cargarProductos();
        });
        
        function cargarProductos() {
            $('#loading').show();
            $('#productTable').hide();
            $('#errorMessage').hide();
            
            $.ajax({
                url: '../controllers/algoritmo_genetico.php', // Reemplaza con la ruta correcta
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#loading').hide();
                    
                    if (response.success) {
                        mostrarProductos(response.data);
                        //Renderizar grafico2:
                        renderizarGrafico(response.training_data);
                    } else {
                        mostrarError(response.message);
                    }
                    $('#btnCargar').show();
                },
                error: function(xhr, status, error) {
                    $('#loading').hide();
                    mostrarError('Error al conectar con el servidor: ' + error);
                    $('#btnCargar').show();
                }
            });
        }
        
        function mostrarProductos(productos) {
           /*  var tbody = $('#productTable tbody');
            tbody.empty();
            
            $.each(productos, function(index, producto) {
                var row = $('<tr>');
                row.append($('<td>').text(producto.position));
                row.append($('<td>').text(producto.producto));
                row.append($('<td>').text(producto.solicitudes));
                tbody.append(row);
            });
            
            $('#productTable').show(); */

            //graficos
            const responseData = productos;
            // Preparar datos para el gráfico
            const categories = responseData.map(item => 
                item.producto.length > 30 ? item.producto.substring(0, 30) + '...' : item.producto
            );
            const fullCategories = responseData.map(item => item.producto);
            const seriesData = responseData.map(item => item.solicitudes);

            // Configuración del gráfico
              // Color palette with different tonalities (blue-based in this case)
        const colorPalette = [
            '#1a5276',  // Darkest
            '#2874a6',  // Dark
            '#3498db',  // Base
            '#5dade2',  // Light
            '#85c1e9'   // Lightest
        ];

        // Configuración del gráfico
        const options = {
            series: [{
                name: 'Solicitudes',
                data: seriesData
            }],
            chart: {
                type: 'bar',
                height: 450,
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
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                    distributed: false,
                    barHeight: '80%',
                    dataLabels: {
                        position: 'bottom'
                    }
                }
            },
            colors: colorPalette,
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val;
                },
                offsetX: 0,
                style: {
                    fontSize: '12px',
                    colors: ['#fff']
                }
            },
            stroke: {
                show: true,
                width: 1,
                colors: ['#fff']
            },
            xaxis: {
                categories: categories,
                title: {
                    text: 'Número de solicitudes',
                    style: {
                        fontSize: '14px',
                        fontWeight: 'bold'
                    }
                },
                labels: {
                    formatter: function(val) {
                        return val;
                    }
                }
            },
            yaxis: {
                labels: {
                    show: true,
                    align: 'right',
                    minWidth: 0,
                    maxWidth: 200,
                    style: {
                        fontSize: '12px'
                    },
                    formatter: function(value) {
                        return fullCategories[categories.indexOf(value)] || value;
                    },
                    tooltip: {
                        enabled: true,
                        offsetX: 0
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(value, { series, seriesIndex, dataPointIndex, w }) {
                        return fullCategories[dataPointIndex] + ': ' + value + ' solicitudes';
                    }
                }
            },
            title: {
                text: 'Distribución de solicitudes por producto',
                align: 'center',
                style: {
                    fontSize: '18px',
                    fontWeight: 'bold'
                }
            },
            subtitle: {
                text: 'Top 5 productos más solicitados',
                align: 'center',
                margin: 10,
                style: {
                    fontSize: '14px',
                    color: '#7f8c8d'
                }
            }
        };

            // Renderizar el gráfico
            const chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();
            

        }

        // Función para renderizar el gráfico con los datos
        function renderizarGrafico(data) {
            // Preparar datos para el candlestick chart
            const seriesData = data.map(generacion => {
                return {
                    x: `Gen ${generacion.generacion}`,
                    y: [
                        generacion.fitness_peor,
                        generacion.fitness_mejor,
                        generacion.fitness_promedio,
                        generacion.fitness_mediana
                    ]
                };
            });

            const options = {
                series: [{
                    name: 'Fitness',
                    data: seriesData
                }],
                chart: {
                    type: 'candlestick',
                    height: 450,
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
                    }
                },
                title: {
                    text: 'Evolución del Fitness por Generación',
                    align: 'center',
                    style: {
                        fontSize: '18px',
                        fontWeight: 'bold'
                    }
                },
                xaxis: {
                    type: 'category',
                    labels: {
                        formatter: function(val) {
                            return val;
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Valor de Fitness',
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                tooltip: {
                    custom: function({ seriesIndex, dataPointIndex, w }) {
                        const gen = data[dataPointIndex];
                        return `
                            <div class="tooltip-container">
                                <strong>Generación ${gen.generacion}</strong><br>
                                Mejor: ${gen.fitness_mejor}<br>
                                Peor: ${gen.fitness_peor}<br>
                                Promedio: ${gen.fitness_promedio}<br>
                                Mediana: ${gen.fitness_mediana}
                            </div>
                        `;
                    }
                },
                annotations: {
                    points: [{
                        x: seriesData[seriesData.length - 1].x,
                        y: seriesData[seriesData.length - 1].y[1],
                        marker: {
                            size: 6,
                            fillColor: "#2ecc71",
                            strokeColor: "#27ae60",
                            radius: 2
                        },
                        label: {
                            text: "Mejor fitness",
                            style: {
                                background: "#2ecc71",
                                color: "#fff",
                                padding: {
                                    left: 5,
                                    right: 5,
                                    top: 2,
                                    bottom: 2
                                }
                            }
                        }
                    }]
                }
            };

            const chart2 = new ApexCharts(document.querySelector("#chart2"), options);
            chart2.render();
        }
        
        function mostrarError(mensaje) {
            $('#errorMessage').text(mensaje).show();
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#btnAnalizar').click(function() {
                realizarAnalisis();
            });
        });
        
        function realizarAnalisis() {
            $('#btnAnalizar').hide();
            $('#loading2').show();
            $('.resultado').empty();
            
            $.ajax({
                url: '../controllers/algoritmo_genetico_analisis.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#loading2').hide();
                    $('#btnAnalizar').show();
                    if (response.success) {
                        mostrarResultados(response.data);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    $('#loading2').hide();
                    alert('Error al conectar con el servidor: ' + error);
                    $('#btnAnalizar').show();
                }
            });
        }
        
        function mostrarResultados(data) {
            // Cliente con más cotizaciones
            $('#clienteMasCotizaciones .resultado').html(`
                <div class="result-item">
                    <strong>Cliente ID:</strong> ${data.cliente_mas_cotizaciones.cliente}<br>
                    <strong>Total cotizaciones:</strong> ${data.cliente_mas_cotizaciones.cotizaciones}
                </div>
            `);
            
            // Cliente con menos cotizaciones
            $('#clienteMenosCotizaciones .resultado').html(`
                <div class="result-item">
                    <strong>Cliente ID:</strong> ${data.cliente_menos_cotizaciones.cliente}<br>
                    <strong>Total cotizaciones:</strong> ${data.cliente_menos_cotizaciones.cotizaciones}
                </div>
            `);
            
            // Marca más cotizada
            $('#marcaMasCotizada .resultado').html(`
                <div class="result-item">
                    <strong>Marca:</strong> ${data.marca_mas_cotizada.marca}<br>
                    <strong>Total cotizaciones:</strong> ${data.marca_mas_cotizada.cotizaciones}
                </div>
            `);
            
            // Marca menos cotizada
            $('#marcaMenosCotizada .resultado').html(`
                <div class="result-item">
                    <strong>Marca:</strong> ${data.marca_menos_cotizada.marca}<br>
                    <strong>Total cotizaciones:</strong> ${data.marca_menos_cotizada.cotizaciones}
                </div>
            `);
            
            //grafico
            // Extraer datos
        const clientData = [
            {
                name: data.cliente_mas_cotizaciones.cliente,
                value: data.cliente_mas_cotizaciones.cotizaciones
            },
            {
                name: data.cliente_menos_cotizaciones.cliente,
                value: data.cliente_menos_cotizaciones.cotizaciones
            }
        ];

        const brandData = [
            {
                name: data.marca_mas_cotizada.marca,
                value: data.marca_mas_cotizada.cotizaciones
            },
            {
                name: data.marca_menos_cotizada.marca,
                value: data.marca_menos_cotizada.cotizaciones
            }
        ];

        // Configuración del gráfico comparativo
        const options = {
            series: [
                {
                    name: 'Clientes',
                    data: clientData.map(item => item.value)
                },
                {
                    name: 'Marcas',
                    data: brandData.map(item => item.value)
                }
            ],
            chart: {
                type: 'bar',
                height: 400,
                stacked: false,
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
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded',
                    borderRadius: 5,
                },
            },
            colors: ['#3498db', '#e74c3c'],
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val;
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#fff"]
                }
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['Más cotizaciones', 'Menos cotizaciones'],
                labels: {
                    style: {
                        fontSize: '14px',
                        fontWeight: 'bold'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Número de cotizaciones',
                    style: {
                        fontSize: '14px',
                        fontWeight: 'bold'
                    }
                },
                labels: {
                    formatter: function(val) {
                        return val.toFixed(0);
                    }
                }
            },
            fill: {
                opacity: 0.9
            },
            tooltip: {
                y: {
                    formatter: function(val, { series, seriesIndex, dataPointIndex, w }) {
                        const names = seriesIndex === 0 ? 
                            [clientData[0].name, clientData[1].name] : 
                            [brandData[0].name, brandData[1].name];
                        return `${names[dataPointIndex]}: ${val} cotizaciones`;
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '14px',
                markers: {
                    width: 12,
                    height: 12,
                    radius: 12,
                }
            },
            title: {
                text: 'Comparación de Extremos en Cotizaciones',
                align: 'center',
                style: {
                    fontSize: '20px',
                    fontWeight: 'bold',
                    color: '#2c3e50'
                }
            },
            responsive: [{
                breakpoint: 600,
                options: {
                    chart: {
                        height: 500
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '70%'
                        }
                    }
                }
            }]
        };

        // Renderizar el gráfico
        const chart = new ApexCharts(document.querySelector("#comparisonChart"), options);
        chart.render();
           
        }
    </script>

    <script>
$(document).ready(function() {
    // Manejar cambio en el selector de período
    $('#periodoReporte').change(function() {
        if ($(this).val() === 'personalizado') {
            $('#fechaInicioContainer, #fechaFinContainer').show();
            // Establecer fechas por defecto para personalizado
            const hoy = new Date().toISOString().split('T')[0];
            const haceUnMes = new Date();
            haceUnMes.setMonth(haceUnMes.getMonth() - 1);
            $('#fechaInicio').val(haceUnMes.toISOString().split('T')[0]);
            $('#fechaFin').val(hoy);
        } else {
            $('#fechaInicioContainer, #fechaFinContainer').hide();
        }
    });
    
    // Generar reporte al cargar la página
    generarReporte();
    
    // Manejar clic en el botón de generar reporte
    $('#btnGenerarReporte').click(function() {
        generarReporte();
    });
    
    function generarReporte() {
        $('#loadingReporte').show();
        
        const periodo = $('#periodoReporte').val();
        const fechaInicio = $('#fechaInicio').val();
        const fechaFin = $('#fechaFin').val();
        
        $.ajax({
            url: '../controllers/reporte_cotizaciones_controller.php',
            type: 'GET',
            dataType: 'json',
            data: {
                periodo: periodo,
                fecha_inicio: periodo === 'personalizado' ? fechaInicio : null,
                fecha_fin: periodo === 'personalizado' ? fechaFin : null
            },
            success: function(response) {
                $('#loadingReporte').hide();
                
                if (response.success) {
                    renderizarReporte(response.data);
                } else {
                    alert('Error: ' + (response.error || 'No se pudo generar el reporte'));
                }
            },
            error: function(xhr, status, error) {
                $('#loadingReporte').hide();
                alert('Error al conectar con el servidor: ' + error);
            }
        });
    }
    
    function renderizarReporte(data) {
        // 1. Gráfico de tiempo de aprobación
        const tiempoOptions = {
            series: [{
                name: 'Tiempo',
                data: [
                    Math.round(data.tiempo_aprobacion.tiempo_promedio_horas * 100) / 100,
                    Math.round(data.tiempo_aprobacion.tiempo_promedio_dias * 100) / 100
                ]
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: true,
                    tools: {
                        download: true
                    }
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                }
            },
            colors: ['#3498db'],
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val + (this.seriesIndex === 0 ? ' horas' : ' días');
                }
            },
            xaxis: {
                categories: ['Horas', 'Días'],
                title: {
                    text: 'Tiempo promedio',
                    style: {
                        fontSize: '14px',
                        fontWeight: 'bold'
                    }
                }
            },
            yaxis: {
                labels: {
                    show: true
                }
            },
            title: {
                text: 'Tiempo promedio desde creación hasta aprobación',
                align: 'center',
                style: {
                    fontSize: '16px'
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + (this.seriesIndex === 0 ? ' horas' : ' días');
                    }
                }
            }
        };
        
        const tiempoChart = new ApexCharts(document.querySelector("#tiempoAprobacionChart"), tiempoOptions);
        tiempoChart.render();
                
        // 3. Gráfico de evolución (line chart)
        const fechas = data.evolucion.map(item => item.fecha);
        const creadas = data.evolucion.map(item => item.creadas);
        const aprobadas = data.evolucion.map(item => item.aprobadas);
        
        const evolucionOptions = {
            series: [
                {
                    name: 'Creadas',
                    data: creadas
                },
                {
                    name: 'Aprobadas',
                    data: aprobadas
                }
            ],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: true
                },
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: [3, 3]
            },
            colors: ['#3498db', '#2ecc71'],
            xaxis: {
                type: 'datetime',
                categories: fechas,
                labels: {
                    formatter: function(value) {
                        return new Date(value).toLocaleDateString();
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Número de cotizaciones',
                    style: {
                        fontSize: '14px',
                        fontWeight: 'bold'
                    }
                }
            },
            title: {
                text: 'Evolución diaria de cotizaciones',
                align: 'center',
                style: {
                    fontSize: '16px'
                }
            },
            legend: {
                position: 'top'
            },
            tooltip: {
                x: {
                    formatter: function(value) {
                        return new Date(value).toLocaleDateString();
                    }
                }
            }
        };
        
        const evolucionChart = new ApexCharts(document.querySelector("#evolucionChart"), evolucionOptions);
        evolucionChart.render();
        
        // Actualizar información del período
        const periodoTexto = {
            'semanal': 'Última semana',
            'mensual': 'Último mes',
            'trimestral': 'Último trimestre',
            'personalizado': 'Período personalizado'
        }[$('#periodoReporte').val()] || 'Período seleccionado';
        
        $('.card-header h6').each(function() {
            const titulo = $(this).text().split('(')[0];
            $(this).text(titulo + ` (${periodoTexto}: ${data.filtros.fecha_inicio} a ${data.filtros.fecha_fin})`);
        });
    }
});
</script>
