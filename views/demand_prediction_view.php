<?php
include('../layout/header.php');
?>

<div class="container-fluid">
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title">Predicción de Demanda de Productos</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="productos.php" class="f-s-14 f-w-500">
            <span>
              <i class="ph-duotone ph-package f-s-16"></i> Productos
            </span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Predicción de Demanda</a>
        </li>
      </ul>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <h5>Selección de Producto</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label for="product-select" class="form-label">Producto</label>
            <select class="form-select" id="product-select">
              <option value="">Cargando productos...</option>
            </select>
          </div>
          
          <div class="mb-3">
            <label for="price-range" class="form-label">Rango de Precios a Evaluar</label>
            <div class="row">
              <div class="col-6">
                <input type="number" class="form-control" id="min-price" placeholder="Mínimo" step="0.01">
              </div>
              <div class="col-6">
                <input type="number" class="form-control" id="max-price" placeholder="Máximo" step="0.01">
              </div>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="price-points" class="form-label">Número de puntos de precio</label>
            <input type="number" class="form-control" id="price-points" value="10" min="3" max="20">
          </div>
          
          <div class="mb-3">
            <label class="form-label">Métodos de Predicción</label>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="linear_regression" id="linear-regression" checked>
              <label class="form-check-label" for="linear-regression">
                Regresión Lineal
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="logistic_regression" id="logistic-regression" checked>
              <label class="form-check-label" for="logistic-regression">
                Regresión Logística
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="genetic_algorithm" id="genetic-algorithm" checked>
              <label class="form-check-label" for="genetic-algorithm">
                Algoritmo Genético
              </label>
            </div>
          </div>
          
          <button id="analyze-btn" class="btn btn-primary w-100">
            <i class="ti ti-chart-line me-1"></i> Analizar Demanda
          </button>
        </div>
      </div>
      
      <div class="card mt-3">
        <div class="card-header">
          <h5>Datos Históricos</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm" id="historical-data-table">
              <thead>
                <tr>
                  <th>Precio</th>
                  <th>Cantidad</th>
                  <th>Fecha</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="3" class="text-center py-3">Seleccione un producto</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5>Resultados de Predicción</h5>
        </div>
        <div class="card-body">
          <div id="prediction-chart"></div>
        </div>
      </div>
      
      <div class="card mt-3">
        <div class="card-header">
          <h5>Comparación de Métodos</h5>
        </div>
        <div class="card-body">
          <div id="comparison-chart"></div>
        </div>
      </div>
      
      <div class="card mt-3">
        <div class="card-header">
          <h5>Recomendación Óptima</h5>
        </div>
        <div class="card-body">
          <div id="recommendation-loading" class="text-center py-3">
            <div class="spinner-border text-primary"></div>
            <p>Calculando recomendación óptima...</p>
          </div>
          <div id="recommendation-results" class="d-none">
            <div class="row">
              <div class="col-md-6">
                <div class="card bg-light">
                  <div class="card-body">
                    <h6 class="mb-3">Mejor Precio Según:</h6>
                    <table class="table table-sm">
                      <tbody id="optimal-prices-table">
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card bg-light">
                  <div class="card-body">
                    <h6 class="mb-3">Ganancias Estimadas:</h6>
                    <table class="table table-sm">
                      <tbody id="profit-estimates-table">
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="no-recommendation" class="text-center py-3 d-none">
            <i class="ti ti-info-circle f-s-48 text-muted mb-3"></i>
            <p>No hay suficiente información para generar una recomendación</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal de detalles -->
<div class="modal fade" id="methodDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="method-details-title">Detalles del Método</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="method-details-content">
          <div class="text-center py-3">
            <div class="spinner-border text-primary"></div>
            <p>Cargando detalles...</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>


<script>
$(document).ready(function() {
  // Cargar lista de productos
  loadProducts();
  
  // Inicializar gráficos
  initCharts();
  
  // Evento para analizar demanda
  $('#analyze-btn').click(analyzeDemand);
});

// Cargar lista de productos
function loadProducts() {
  $.ajax({
    url: '../controllers/product_controller.php',
    type: 'GET',
    dataType: 'json',
    success: function(products) {
      const $select = $('#product-select');
      $select.empty();
      $select.append('<option value="">Seleccione un producto</option>');
      
      products.forEach(product => {
        $select.append(`<option value="${product.id}" data-name="${product.producto_nombre}">${product.producto_nombre} (${product.producto_codigo})</option>`);
      });
      
      // Evento al cambiar producto
      $select.change(function() {
        const productId = $(this).val();
        const productName = $(this).find('option:selected').data('name'); // Cambio clave aquí
        console.log("ID del producto: " + productId);
        console.log("Nombre del producto: " + productName);
        
        if (productId) {
          loadHistoricalData(productId, productName);
        } else {
          $('#historical-data-table tbody').html('<tr><td colspan="3" class="text-center py-3">Seleccione un producto</td></tr>');
        }
      });
    },
    error: function() {
      $('#product-select').html('<option value="">Error al cargar productos</option>');
    }
  });
}

// Cargar datos históricos
function loadHistoricalData(productId, productName) {
    $('#historical-data-table tbody').html('<tr><td colspan="3" class="text-center py-3"><div class="spinner-border spinner-border-sm"></div> Cargando datos...</td></tr>');
    
    // Codificar el nombre del producto para la URL
    const encodedProductName = encodeURIComponent(productName);
    
    $.ajax({
        url: '../controllers/demand_prediction_controller.php?action=historical_data&product_name=' + encodedProductName,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                console.error('Error en la consulta:', response.error);
                console.error('Consulta SQL:', response.query); // Solo para depuración
                $('#historical-data-table tbody').html('<tr><td colspan="3" class="text-center py-3 text-danger">Error al cargar datos</td></tr>');
                return;
            }

            const $tbody = $('#historical-data-table tbody');
            $tbody.empty();
            
            if (response.length === 0) {
                $tbody.html('<tr><td colspan="3" class="text-center py-3">No hay datos históricos para este producto</td></tr>');
                return;
            }

            // Ordenar por fecha descendente
            const sortedData = response.sort((a, b) => new Date(b.date) - new Date(a.date));
            
            // Mostrar solo los últimos 10 registros
            const recentData = sortedData.slice(0, 10);
            
            recentData.forEach(item => {
                $tbody.append(`
                    <tr>
                        <td>${parseFloat(item.price).toFixed(2)}</td>
                        <td>${item.quantity_sold}</td>
                        <td>${new Date(item.date).toLocaleDateString()}</td>
                    </tr>
                `);
            });
            
            // Calcular precios mínimos y máximos
            const prices = response.map(item => parseFloat(item.price));
            const minPrice = Math.min(...prices);
            const maxPrice = Math.max(...prices);
            
            // Establecer valores predeterminados
            $('#min-price').val(minPrice.toFixed(2));
            $('#max-price').val(maxPrice.toFixed(2));
        },
        error: function(xhr, status, error) {
            console.error('Error en la solicitud AJAX:', error);
            $('#historical-data-table tbody').html('<tr><td colspan="3" class="text-center py-3 text-danger">Error al cargar datos</td></tr>');
        }
    });
}

// Inicializar gráficos
function initCharts() {
  window.predictionChart = new ApexCharts(document.querySelector("#prediction-chart"), {
    series: [],
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
          selection: true,
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
      curve: 'smooth'
    },
    title: {
      text: 'Predicción de Demanda por Precio',
      align: 'left'
    },
    grid: {
      row: {
        colors: ['#f3f3f3', 'transparent'],
        opacity: 0.5
      },
    },
    xaxis: {
      title: {
        text: 'Precio'
      }
    },
    yaxis: {
      title: {
        text: 'Demanda Estimada'
      },
      min: 0
    },
    legend: {
      position: 'top'
    },
    tooltip: {
      y: {
        formatter: function(value) {
          return value.toFixed(0) + " unidades";
        }
      }
    }
  });
  
  window.predictionChart.render();
  
  window.comparisonChart = new ApexCharts(document.querySelector("#comparison-chart"), {
    series: [],
    chart: {
      height: 350,
      type: 'bar',
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
        endingShape: 'rounded'
      },
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      show: true,
      width: 2,
      colors: ['transparent']
    },
    title: {
      text: 'Comparación de Métodos de Predicción',
      align: 'left'
    },
    xaxis: {
      categories: [],
      title: {
        text: 'Precio'
      }
    },
    yaxis: {
      title: {
        text: 'Demanda Estimada'
      },
      min: 0
    },
    fill: {
      opacity: 1
    },
    tooltip: {
      y: {
        formatter: function(value) {
          return value.toFixed(0) + " unidades";
        }
      }
    },
    legend: {
      position: 'top'
    }
  });
  
  window.comparisonChart.render();
}

// Analizar demanda
function analyzeDemand() {
  const productId = $('#product-select').val();
  const productName = $('#product-select option:selected').data('name');
  if (!productId) {
    Swal.fire('Error', 'Seleccione un producto', 'error');
    return;
  }
  
  const minPrice = parseFloat($('#min-price').val());
  const maxPrice = parseFloat($('#max-price').val());
  const points = parseInt($('#price-points').val());
  
  if (isNaN(minPrice) || isNaN(maxPrice) || minPrice >= maxPrice) {
    Swal.fire('Error', 'Ingrese un rango de precios válido', 'error');
    return;
  }
  
  if (isNaN(points) || points < 3 || points > 20) {
    Swal.fire('Error', 'Ingrese un número válido de puntos (3-20)', 'error');
    return;
  }
  
  // Generar puntos de precio equidistantes
  const pricePoints = [];
  const step = (maxPrice - minPrice) / (points - 1);
  for (let i = 0; i < points; i++) {
    pricePoints.push(minPrice + i * step);
  }
  
  // Verificar qué métodos se seleccionaron
  const methods = [];
  if ($('#linear-regression').is(':checked')) methods.push('linear_regression');
  if ($('#logistic-regression').is(':checked')) methods.push('logistic_regression');
  if ($('#genetic-algorithm').is(':checked')) methods.push('genetic_algorithm');
  
  if (methods.length === 0) {
    Swal.fire('Error', 'Seleccione al menos un método de predicción', 'error');
    return;
  }
  
  // Mostrar loading
  Swal.fire({
    title: 'Analizando demanda',
    html: 'Calculando predicciones para los puntos de precio seleccionados...',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });
  
   if (methods.length === 1) {
    $.ajax({
      url: '../controllers/demand_prediction_controller.php?action=predict_demand&product_name=' + 
           encodeURIComponent(productName) + '&price_points=' + JSON.stringify(pricePoints) + '&method=' + methods[0],
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        Swal.close();
        if (response.error) {
          Swal.fire('Error', response.error, 'error');
        } else {
          updateCharts(response, methods[0]);
          generateOptimalRecommendation([response]);
        }
      },
      error: function(xhr, status, error) {
        Swal.close();
        Swal.fire('Error', 'Error al calcular predicciones: ' + error, 'error');
      }
    });
  } else {
    // Comparar múltiples métodos
    $.ajax({
      url: '../controllers/demand_prediction_controller.php?action=predict_demand&product_name=' + 
           encodeURIComponent(productName) + '&price_points=' + JSON.stringify(pricePoints) + '&method=compare',
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        Swal.close();
        if (response.error) {
          Swal.fire('Error', response.error, 'error');
        } else {
          updateComparisonChart(response);
          generateOptimalRecommendation([
            response.linear_regression,
            response.logistic_regression,
            response.genetic_algorithm
          ]);
        }
      },
      error: function(xhr, status, error) {
        Swal.close();
        Swal.fire('Error', 'Error al calcular comparación: ' + error, 'error');
      }
    });
  }
}

// Actualizar gráfico de predicción para un solo método
function updateCharts(response, method) {
  const predictions = response.predictions;
  const historicalData = response.historical_data;
  
  // Actualizar gráfico de predicción
  const series = [{
    name: 'Predicción ' + method.replace('_', ' '),
    data: predictions.map(p => [p.price, p.predicted_demand])
  }];
  
  // Agregar datos históricos si existen
  /* if (historicalData && historicalData.length > 0) {
    series.push({
      name: 'Datos históricos',
      data: historicalData.map(d => [parseFloat(d.price), d.quantity_sold]),
      type: 'scatter',
      marker: {
        size: 6
      }
    });
  } */
  
  window.predictionChart.updateOptions({
    series: series,
    title: {
      text: 'Predicción de Demanda - ' + method.replace('_', ' ').toUpperCase()
    }
  });
  
  // Actualizar gráfico de comparación (solo un método)
  window.comparisonChart.updateOptions({
    series: [{
      name: method.replace('_', ' '),
      data: predictions.map(p => p.predicted_demand)
    }],
    xaxis: {
      categories: predictions.map(p => p.price.toFixed(2))
    }
  });
  
  // Mostrar detalles del método en un modal
  showMethodDetails(response, method);
}

// Actualizar gráfico de comparación para múltiples métodos
function updateComparisonChart(response) {
  const pricePoints = response.price_points;
  const linearPredictions = response.linear_regression.predictions;
  const logisticPredictions = response.logistic_regression.predictions;
  const geneticPredictions = response.genetic_algorithm.predictions;
  
  const series = [];
  const categories = [];
  
  if (response.linear_regression && !response.linear_regression.error) {
    series.push({
      name: 'Regresión Lineal',
      data: linearPredictions.map(p => p.predicted_demand)
    });
  }
  
  if (response.logistic_regression && !response.logistic_regression.error) {
    series.push({
      name: 'Regresión Logística',
      data: logisticPredictions.map(p => p.predicted_demand)
    });
  }
  
  if (response.genetic_algorithm && !response.genetic_algorithm.error) {
    series.push({
      name: 'Algoritmo Genético',
      data: geneticPredictions.map(p => p.predicted_demand)
    });
  }
  
  // Usar los mismos puntos de precio para todas las categorías
  pricePoints.forEach(price => {
    categories.push(price.toFixed(2));
  });
  
  window.comparisonChart.updateOptions({
    series: series,
    xaxis: {
      categories: categories
    }
  });
  
  // Actualizar gráfico de predicción con todos los métodos
  const predictionSeries = [];
  
  if (response.linear_regression && !response.linear_regression.error) {
    predictionSeries.push({
      name: 'Regresión Lineal',
      data: linearPredictions.map(p => [p.price, p.predicted_demand])
    });
  }
  
  if (response.logistic_regression && !response.logistic_regression.error) {
    predictionSeries.push({
      name: 'Regresión Logística',
      data: logisticPredictions.map(p => [p.price, p.predicted_demand])
    });
  }
  
  if (response.genetic_algorithm && !response.genetic_algorithm.error) {
    predictionSeries.push({
      name: 'Algoritmo Genético',
      data: geneticPredictions.map(p => [p.price, p.predicted_demand])
    });
  }
  
  // Agregar datos históricos si existen
  /* if (response.linear_regression.historical_data && response.linear_regression.historical_data.length > 0) {
    predictionSeries.push({
      name: 'Datos históricos',
      data: response.linear_regression.historical_data.map(d => [parseFloat(d.price), d.quantity_sold]),
      type: 'scatter',
      marker: {
        size: 6
      }
    });
  } */
  
  window.predictionChart.updateOptions({
    series: predictionSeries,
    title: {
      text: 'Comparación de Métodos de Predicción'
    }
  });
}

// Mostrar detalles del método en un modal
function showMethodDetails(response, method) {
  const $modal = $('#methodDetailsModal');
  const $title = $('#method-details-title');
  const $content = $('#method-details-content');
  
  $title.text('Detalles: ' + method.replace('_', ' ').toUpperCase());
  
  let detailsHtml = `
    <div class="mb-4">
      <h6>Ecuación del Modelo</h6>
      <div class="alert alert-light">
        <code>${response.equation}</code>
      </div>
    </div>
    
    <div class="mb-4">
      <h6>Coeficientes</h6>
      <table class="table table-sm">
        <tbody>
  `;
  
  for (const [key, value] of Object.entries(response.coefficients)) {
    detailsHtml += `
      <tr>
        <th>${key}</th>
        <td>${value.toFixed(4)}</td>
      </tr>
    `;
  }
  
  detailsHtml += `
        </tbody>
      </table>
    </div>
    
    <div class="mb-4">
      <h6>Predicciones</h6>
      <div class="table-responsive">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>Precio</th>
              <th>Demanda Estimada</th>
            </tr>
          </thead>
          <tbody>
  `;
  
  response.predictions.forEach(pred => {
    detailsHtml += `
      <tr>
        <td>${pred.price.toFixed(2)}</td>
        <td>${pred.predicted_demand.toFixed(0)} unidades</td>
      </tr>
    `;
  });
  
  detailsHtml += `
          </tbody>
        </table>
      </div>
    </div>
  `;
  
  $content.html(detailsHtml);
  $modal.modal('show');
}

// Generar recomendación óptima
function generateOptimalRecommendation(methodResults) {
  $('#recommendation-results').addClass('d-none');
  $('#no-recommendation').addClass('d-none');
  $('#recommendation-loading').removeClass('d-none');
  
  // Esperar un momento para que se muestre el loading (simulando procesamiento)
  setTimeout(() => {
    try {
      const optimalPrices = {};
      const profitEstimates = {};
      
      methodResults.forEach(result => {
        if (result.error) return;
        
        const method = result.method;
        const predictions = result.predictions;
        
        // Encontrar el punto que maximiza ganancias (precio * cantidad)
        let maxProfit = 0;
        let optimalPrice = 0;
        let optimalDemand = 0;
        
        predictions.forEach(pred => {
          const profit = pred.price * pred.predicted_demand;
          if (profit > maxProfit) {
            maxProfit = profit;
            optimalPrice = pred.price;
            optimalDemand = pred.predicted_demand;
          }
        });
        
        optimalPrices[method] = optimalPrice.toFixed(2);
        profitEstimates[method] = maxProfit.toFixed(2);
      });
      
      // Actualizar tabla de precios óptimos
      const $optimalTable = $('#optimal-prices-table');
      $optimalTable.empty();
      
      for (const [method, price] of Object.entries(optimalPrices)) {
        $optimalTable.append(`
          <tr>
            <th>${method.replace('_', ' ')}</th>
            <td>${price} Bs.</td>
          </tr>
        `);
      }
      
      // Actualizar tabla de ganancias estimadas
      const $profitTable = $('#profit-estimates-table');
      $profitTable.empty();
      
      for (const [method, profit] of Object.entries(profitEstimates)) {
        $profitTable.append(`
          <tr>
            <th>${method.replace('_', ' ')}</th>
            <td>${profit} Bs.</td>
          </tr>
        `);
      }
      
      $('#recommendation-loading').addClass('d-none');
      $('#recommendation-results').removeClass('d-none');
    } catch (error) {
      console.error(error);
      $('#recommendation-loading').addClass('d-none');
      $('#no-recommendation').removeClass('d-none');
    }
  }, 1000);
}
</script>

<style>
#prediction-chart, #comparison-chart {
  min-height: 350px;
}

.card-header {
  border-bottom: 1px solid rgba(0,0,0,.125);
}

.table-sm td, .table-sm th {
  padding: 0.3rem;
}

.method-detail-card {
  border-left: 4px solid #0d6efd;
}

.method-detail-card h6 {
  color: #0d6efd;
}

#optimal-prices-table th, #profit-estimates-table th {
  font-weight: 500;
}
</style>