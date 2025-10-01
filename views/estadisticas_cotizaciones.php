<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">

<?php
include('../layout/header_clientes.php');
?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title">Estadísticas de Cotizaciones</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="#" class="f-s-14 f-w-500">
            <span>
              <i class="ph-duotone ph-chart-line f-s-16"></i> Estadísticas
            </span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Reportes de Cotizaciones</a>
        </li>
      </ul>
    </div>
  </div>
  <!-- Breadcrumb end -->

  <!-- Stats Cards -->
  <div class="row">
    <div class="col-md-3">
      <div class="card stat-card">
        <div class="card-body">
          <h5 class="card-title">Total Cotizaciones</h5>
          <h2 class="stat-value" id="total-quotes">0</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card">
        <div class="card-body">
          <h5 class="card-title">Productos Únicos</h5>
          <h2 class="stat-value" id="unique-products">0</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card">
        <div class="card-body">
          <h5 class="card-title">Promedio por Cotización</h5>
          <h2 class="stat-value" id="avg-products">0</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card">
        <div class="card-body">
          <h5 class="card-title">Cotización Más Grande</h5>
          <h2 class="stat-value" id="max-products">0</h2>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row 1 -->
  <!-- <div class="row mt-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Estado de Cotizaciones</h5>
          <div id="statusChart"></div>
        </div>
      </div>
    </div> -->
    <div class="col-md-">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Productos Más Cotizados</h5>
          <div id="topProductsChart"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row 2 -->
  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Cotizaciones por Mes</h5>
          <div id="monthlyChart"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recommendations Section -->
  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Combinaciones Frecuentes de Productos</h5>
          <div class="table-responsive">
            <table class="table table-hover" id="combinationsTable">
              <thead>
                <tr>
                  <th>Producto 1</th>
                  <th>Producto 2</th>
                  <th>Frecuencia</th>
                </tr>
              </thead>
              <tbody id="combinationsBody">
                <tr><td colspan="3">Cargando datos...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recommendation Modal -->
<div class="modal fade" id="recommendationModal" tabindex="-1" role="dialog" aria-labelledby="recommendationModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="recommendationModalLabel">Recomendaciones</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Clientes que cotizaron <strong id="currentProduct"></strong> también cotizaron:</p>
        <ul id="recommendationList"></ul>
        <div id="brandRecommendation" class="mt-3"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<?php
include('../layout/footer.php');
?>

<!-- JavaScript Libraries -->
<script src="../assets/vendor/datatable/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    // Load statistics data
    loadStatistics();
    
    // Initialize DataTable
    $('#combinationsTable').DataTable();
});

function loadStatistics() {
    $.ajax({
        url: '../controllers/stats_controller.php',
        type: 'GET',
        dataType: "json",
        success: function(data) {
            // Update stats cards
            updateStatsCards(data);
            
            // Render charts
            renderStatusChart(data.status);
            renderTopProductsChart(data.top_products);
            renderMonthlyChart(data.monthly);
            
            // Populate combinations table
            populateCombinationsTable(data.combo_products);
            
            // Store data for recommendations
            localStorage.setItem('quoteStats', JSON.stringify(data));
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error loading statistics:", errorThrown);
            Swal.fire('Error', 'No se pudieron cargar las estadísticas', 'error');
        }
    });
}

function updateStatsCards(data) {
    // Calculate totals
    let totalQuotes = 0;
    data.status.forEach(item => totalQuotes += parseInt(item.total));
    
    let uniqueProducts = data.top_products.length;
    let avgProducts = (data.top_products.reduce((sum, item) => sum + parseInt(item.total), 0) / totalQuotes);
    let maxProducts = Math.max(...data.top_products.map(item => parseInt(item.total)));
    
    // Update cards
    $('#total-quotes').text(totalQuotes);
    $('#unique-products').text(uniqueProducts);
    $('#avg-products').text(avgProducts.toFixed(1));
    $('#max-products').text(maxProducts);
}

function renderStatusChart(statusData) {
    const labels = statusData.map(item => {
        switch(item.estado) {
            case 'CLI': return 'Registrado por Cliente';
            case 'RECH': return 'Rechazado';
            case 'APRO': return 'Aprobado';
            case 'REV': return 'En Revisión';
            default: return item.estado;
        }
    });
    const series = statusData.map(item => item.total);
    
    const options = {
        series: series,
        chart: {
            type: 'donut',
            height: 350
        },
        labels: labels,
        colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560'],
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
        }]
    };
    
    const chart = new ApexCharts(document.querySelector("#statusChart"), options);
    chart.render();
}

function renderTopProductsChart(productsData) {
    const labels = productsData.map(item => item.producto);
    const series = productsData.map(item => item.total);
    
    const options = {
        series: [{
            name: 'Cotizaciones',
            data: series
        }],
        chart: {
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: labels,
        },
        colors: ['#3F51B5']
    };
    
    const chart = new ApexCharts(document.querySelector("#topProductsChart"), options);
    chart.render();
}

function renderMonthlyChart(monthlyData) {
    const labels = monthlyData.map(item => {
        const [year, month] = item.mes.split('-');
        const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        return `${monthNames[parseInt(month)-1]} ${year}`;
    }).reverse();
    
    const series = monthlyData.map(item => item.total).reverse();
    
    const options = {
        series: [{
            name: 'Cotizaciones',
            data: series
        }],
        chart: {
            type: 'line',
            height: 350,
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        markers: {
            size: 5
        },
        xaxis: {
            categories: labels,
        },
        yaxis: {
            min: 0,
            forceNiceScale: true
        },
        colors: ['#4CAF50']
    };
    
    const chart = new ApexCharts(document.querySelector("#monthlyChart"), options);
    chart.render();
}

function populateCombinationsTable(combinations) {
    const tbody = $('#combinationsBody');
    tbody.empty();
    
    combinations.forEach(combo => {
        const row = `
            <tr class="combo-row" data-product1="${combo.producto1}" data-product2="${combo.producto2}">
                <td>${combo.producto1}</td>
                <td>${combo.producto2}</td>
                <td>${combo.frecuencia}</td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Add click handlers for recommendations
    $('.combo-row').click(function() {
        const product1 = $(this).data('product1');
        showRecommendations(product1);
    });
}

function showRecommendations(productName) {
    $('#currentProduct').text(productName);
    $('#recommendationList').empty();
    $('#brandRecommendation').empty();
    
    $.ajax({
        url: '../controllers/stats_controller.php?product=' + encodeURIComponent(productName),
        type: 'GET',
        dataType: "json",
        success: function(data) {
            // Show product recommendations
            data.forEach(item => {
                if (item.recommended_product) {
                    $('#recommendationList').append(`
                        <li>${item.recommended_product} (${item.probability}% de coincidencia)</li>
                    `);
                }
            });
            
            // Show brand recommendations if available
            if (data.brands && data.brands.length > 0) {
                let brandHtml = '<p>También podrían interesarte productos de estas marcas:</p><ul>';
                data.brands.forEach(brand => {
                    brandHtml += `<li>${brand.marca}</li>`;
                });
                brandHtml += '</ul>';
                $('#brandRecommendation').html(brandHtml);
            }
            
            // Show modal
            $('#recommendationModal').modal('show');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error loading recommendations:", errorThrown);
            Swal.fire('Error', 'No se pudieron cargar las recomendaciones', 'error');
        }
    });
}

// Genetic Algorithm for Recommendations (simplified version)
function generateRecommendations(productName, stats) {
    // Fitness function: combination frequency
    const combinations = stats.combo_products.filter(c => 
        c.producto1 === productName || c.producto2 === productName
    );
    
    // Sort by frequency
    combinations.sort((a, b) => b.frecuencia - a.frecuencia);
    
    // Get top 3 recommendations
    const recommendations = combinations.slice(0, 3).map(combo => {
        return {
            product: combo.producto1 === productName ? combo.producto2 : combo.producto1,
            frequency: combo.frecuencia
        };
    });
    
    return recommendations;
}
</script>

<style>
.stat-card {
    text-align: center;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-value {
    font-size: 2.5rem;
    color: #3F51B5;
    margin: 10px 0;
}

.combo-row {
    cursor: pointer;
    transition: background-color 0.2s;
}

.combo-row:hover {
    background-color: #f5f5f5;
}

.card {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.card-title {
    font-weight: 600;
    color: #333;
}
</style>