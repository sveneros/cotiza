<link rel="stylesheet" type="text/css" href="../assets/vendor/apexcharts/apexcharts.css">
<link rel="stylesheet" type="text/css" href="../assets/vendor/datatable/jquery.dataTables.min.css">

<?php include('../layout/header.php'); ?>

<div class="container-fluid">
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title">Costos y Precios de Productos</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="#" class="f-s-14 f-w-500">
            <span><i class="ph-duotone ph-table f-s-16"></i> Operaciones</span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Costos de Productos</a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Filtros y controles -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5>Filtros del Reporte</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Productos a mostrar</label>
              <select class="form-select" id="productFilter">
                <option value="all">Todos los productos</option>
                <option value="selected">Seleccionar productos específicos</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Cantidad por página</label>
              <select class="form-select" id="limitFilter">
                <option value="10">10 productos</option>
                <option value="30" selected>30 productos</option>
                <option value="50">50 productos</option>
                <option value="100">100 productos</option>
                <option value="0">Todos</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Productos específicos</label>
              <select class="form-select" id="productSelect" multiple style="height: 100px;" disabled>
                <option value="">Cargando productos...</option>
              </select>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-12">
              <button type="button" class="btn btn-primary" id="applyFilters">
                <i class="fa fa-filter"></i> Aplicar Filtros
              </button>
              <button type="button" class="btn btn-secondary" id="resetFilters">
                <i class="fa fa-refresh"></i> Restablecer
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5>Resumen de Precios y Costos</h5>
        </div>
        <div class="card-body">
          <div id="priceSummaryChart" style="height: 350px;"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5>Distribución de Costos</h5>
        </div>
        <div class="card-body">
          <div id="costDistributionChart" style="height: 300px;"></div>
        </div>
      </div>
    </div>
  </div> 

  <!-- Paginación -->
  <div class="row mt-3">
    <div class="col-md-12">
      <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center" id="pagination">
        </ul>
      </nav>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5>Factores de Costo por Producto</h5>
          <div class="card-header-right">
            <div class="btn-group card-option">
              <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="feather icon-more-vertical"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="#" id="refreshData">Actualizar Datos</a>
              </div>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table display" id="costFactorsTable">
              <thead>
                <tr>
                  <th>Código</th>
                  <th>Nombre</th>
                  <th>Costo Base</th>
                  <th>Moneda</th>
                  <th>Tipo Cambio</th>
                  <th>Precio Actual</th>
                  <th>Precio Sugerido</th>
                  <th>Diferencia</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para edición de costos -->
  <div class="modal fade" id="costModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Factores de Costo</h5>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="costForm">
            <input type="hidden" id="cost_product_id">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Costo Base (USD)<span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="base_cost" step="0.01" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Impuesto Importación (%)<span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="import_tax" step="0.01" required>
              </div>
            </div>
            <div class="row g-3 mt-2">
              <div class="col-md-4">
                <label class="form-label">Envío Internacional</label>
                <input type="number" class="form-control" id="shipping_cost" step="0.01">
              </div>
              <div class="col-md-4">
                <label class="form-label">Transporte Local</label>
                <input type="number" class="form-control" id="local_transport" step="0.01">
              </div>
              <div class="col-md-4">
                <label class="form-label">Almacenamiento</label>
                <input type="number" class="form-control" id="storage_cost" step="0.01">
              </div>
            </div>
            <div class="row g-3 mt-2">
              <div class="col-md-4">
                <label class="form-label">Seguro</label>
                <input type="number" class="form-control" id="insurance" step="0.01">
              </div>
              <div class="col-md-4">
                <label class="form-label">Gastos Aduaneros</label>
                <input type="number" class="form-control" id="customs_fees" step="0.01">
              </div>
              <div class="col-md-4">
                <label class="form-label">Tipo de Cambio</label>
                <input type="number" class="form-control" id="currency_exchange_rate" step="0.0001" required>
              </div>
            </div>
            <div class="row g-3 mt-2">
              <div class="col-md-4">
                <label class="form-label">Margen Ganancia (%)<span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="profit_margin" step="0.01" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Factor Demanda (1.0 = normal)</label>
                <input type="number" class="form-control" id="market_demand_factor" step="0.01" min="0.5" max="2.0">
              </div>
              <div class="col-md-4">
                <label class="form-label">Factor Competencia (1.0 = normal)</label>
                <input type="number" class="form-control" id="competition_factor" step="0.01" min="0.5" max="2.0">
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-12">
                <div class="alert alert-info">
                  <strong>Precio Sugerido:</strong> <span id="suggested_price_display">$0.00</span>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="saveCostFactors">Guardar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('../layout/footer.php'); ?>

<script src="../assets/vendor/datatable/jquery.dataTables.min.js"></script>
<script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="../assets/js/jquery.validate.min.js"></script>

<script>
$(document).ready(function() {
    let pricingData = [];
    let currentPage = 1;
    let currentLimit = 30;
    let currentProductIds = [];
    let priceSummaryChart = null;
    let costDistributionChart = null;
    
    // Cargar productos disponibles para el filtro
    function loadAvailableProducts() {
        $.get('../controllers/product_price_controller.php?action=available_products', function(products) {
            const $productSelect = $('#productSelect');
            $productSelect.empty();
            
            // Usar un objeto para evitar duplicados
            const uniqueProducts = {};
            products.forEach(function(product) {
                uniqueProducts[product.id] = product;
            });
            
            // Agregar productos únicos al select
            Object.values(uniqueProducts).forEach(function(product) {
                $productSelect.append(
                    $('<option>', {
                        value: product.id,
                        text: product.codigo + ' - ' + product.nombre
                    })
                );
            });
        }).fail(function() {
            console.error("Error al cargar productos disponibles");
            $('#productSelect').html('<option value="">Error al cargar productos</option>');
        });
    }
    
    // Manejar cambio en el filtro de productos
    $('#productFilter').change(function() {
        if ($(this).val() === 'selected') {
            $('#productSelect').prop('disabled', false);
        } else {
            $('#productSelect').prop('disabled', true);
            $('#productSelect').val([]);
        }
    });
    
    // Aplicar filtros
    $('#applyFilters').click(function() {
        currentPage = 1;
        currentLimit = parseInt($('#limitFilter').val());
        
        if ($('#productFilter').val() === 'selected') {
            currentProductIds = $('#productSelect').val();
            if (currentProductIds.length === 0) {
                Swal.fire('Advertencia', 'Por favor seleccione al menos un producto', 'warning');
                return;
            }
        } else {
            currentProductIds = [];
        }
        
        loadData();
    });
    
    // Restablecer filtros
    $('#resetFilters').click(function() {
        $('#productFilter').val('all');
        $('#limitFilter').val('30');
        $('#productSelect').prop('disabled', true).val([]);
        currentPage = 1;
        currentLimit = 30;
        currentProductIds = [];
        loadData();
    });
    
    // Cargar datos iniciales
    function loadData() {
        const params = {
            limit: currentLimit,
            page: currentPage
        };
        
        if (currentProductIds.length > 0) {
            params.product_ids = currentProductIds.join(',');
        }
        
        const queryString = new URLSearchParams(params).toString();
        
        $.get(`../controllers/product_price_controller.php?action=pricing_analysis&${queryString}`, function(response) {
            pricingData = response.data;
            initCharts(response.data);
            initDataTable(response.data);
            initPagination(response.pagination);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Error al cargar datos:", textStatus, errorThrown);
            Swal.fire('Error', 'No se pudieron cargar los datos de precios', 'error');
        });
    }
    
    // Inicializar paginación
    function initPagination(pagination) {
        const $pagination = $('#pagination');
        $pagination.empty();
        
        const totalPages = pagination.pages;
        const current = pagination.page;
        
        if (totalPages <= 1) {
            return; // No mostrar paginación si solo hay una página
        }
        
        // Botón anterior
        const prevDisabled = current <= 1 ? 'disabled' : '';
        $pagination.append(`
            <li class="page-item ${prevDisabled}">
                <a class="page-link" href="#" data-page="${current - 1}">Anterior</a>
            </li>
        `);
        
        // Números de página
        const maxPagesToShow = 5;
        let startPage = Math.max(1, current - Math.floor(maxPagesToShow / 2));
        let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);
        
        if (endPage - startPage + 1 < maxPagesToShow) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const active = i === current ? 'active' : '';
            $pagination.append(`
                <li class="page-item ${active}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }
        
        // Botón siguiente
        const nextDisabled = current >= totalPages ? 'disabled' : '';
        $pagination.append(`
            <li class="page-item ${nextDisabled}">
                <a class="page-link" href="#" data-page="${current + 1}">Siguiente</a>
            </li>
        `);
    }
    
    // Manejar clic en paginación
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page && page !== currentPage) {
            currentPage = page;
            loadData();
        }
    });
    
    // Inicializar gráficos
    function initCharts(data) {
        if (!data || data.length === 0) {
            console.error("No se recibieron datos para los gráficos");
            $('#priceSummaryChart').html('<div class="alert alert-warning text-center">No hay datos para mostrar</div>');
            $('#costDistributionChart').html('<div class="alert alert-warning text-center">No hay datos para mostrar</div>');
            return;
        }
        
        // Limpiar contenedores de gráficos
        $('#priceSummaryChart').html('');
        $('#costDistributionChart').html('');
        
        // Preparar datos para gráficos
        const labels = data.map(item => item.codigo);
        const currentPrices = data.map(item => parseFloat(item.precio_actual));
        const suggestedPrices = data.map(item => parseFloat(item.precio_sugerido));
        
        // Gráfico de resumen de precios
        const priceSummaryOptions = {
            series: [{
                name: 'Precio Actual',
                data: currentPrices
            }, {
                name: 'Precio Sugerido',
                data: suggestedPrices
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: true
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
            colors: ['#3a86ff', '#8338ec'],
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: labels,
                labels: {
                    rotate: -45
                }
            },
            yaxis: {
                title: {
                    text: 'Precio (Bs.)'
                },
                labels: {
                    formatter: function(val) {
                        return "Bs. " + val.toFixed(2);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "Bs. " + val.toFixed(2);
                    }
                }
            }
        };
        
        // Destruir gráfico anterior si existe
        if (priceSummaryChart) {
            try {
                priceSummaryChart.destroy();
            } catch (e) {
                console.log('No se pudo destruir el gráfico anterior:', e);
            }
        }
        
        priceSummaryChart = new ApexCharts(document.querySelector("#priceSummaryChart"), priceSummaryOptions);
        priceSummaryChart.render();
        
        // Gráfico de distribución de costos (usando el primer producto como ejemplo)
        if (data.length > 0) {
            const sampleProduct = data[0];
            const costData = [
                parseFloat(sampleProduct.base_cost_local),
                parseFloat(sampleProduct.base_cost_local * sampleProduct.import_tax / 100),
                parseFloat(sampleProduct.shipping_cost),
                parseFloat(sampleProduct.local_transport),
                parseFloat(sampleProduct.storage_cost),
                parseFloat(sampleProduct.insurance),
                parseFloat(sampleProduct.customs_fees)
            ];
            
            const costDistributionOptions = {
                series: costData,
                chart: {
                    type: 'pie',
                    height: 300
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
                    text: 'Distribución de Costos - ' + sampleProduct.codigo,
                    align: 'center'
                },
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return "Bs. " + value.toFixed(2);
                        }
                    }
                }
            };
            
            // Destruir gráfico anterior si existe
            if (costDistributionChart) {
                try {
                    costDistributionChart.destroy();
                } catch (e) {
                    console.log('No se pudo destruir el gráfico anterior:', e);
                }
            }
            
            costDistributionChart = new ApexCharts(document.querySelector("#costDistributionChart"), costDistributionOptions);
            costDistributionChart.render();
        }
    }
    
    // Inicializar tabla de datos
    function initDataTable(data) {
        $('#costFactorsTable').DataTable({
            data: data,
            destroy: true,
            columns: [
                { data: 'codigo' },
                { data: 'nombre' },
                { 
                    data: 'base_cost_local',
                    render: function(data, type, row) {
                        return '$' + parseFloat(data).toFixed(2) + ' ' + (row.currency_code || 'USD');
                    }
                },
                { 
                    data: 'currency_code',
                    render: function(data) {
                        return data || 'USD';
                    }
                },
                { 
                    data: 'currency_exchange_rate',
                    render: function(data) {
                        return parseFloat(data).toFixed(4);
                    }
                },
                { 
                    data: 'precio_actual',
                    render: function(data) {
                        return '$' + parseFloat(data).toFixed(2);
                    }
                },
                { 
                    data: 'precio_sugerido',
                    render: function(data) {
                        return '$' + parseFloat(data).toFixed(2);
                    }
                },
                { 
                    data: 'porcentaje_diferencia',
                    render: function(data, type, row) {
                        const diff = parseFloat(data);
                        const diffClass = diff > 0 ? 'text-success' : diff < 0 ? 'text-danger' : 'text-warning';
                        const icon = diff > 0 ? '↑' : diff < 0 ? '↓' : '→';
                        const sign = diff > 0 ? '+' : '';
                        return `<span class="${diffClass}">${icon} ${sign}${diff.toFixed(2)}%</span>`;
                    }
                },
                {
                    data: 'id',
                    render: function(data) {
                        return `<button class="btn btn-sm btn-primary edit-cost" data-id="${data}">
                                    <i class="fa fa-edit"></i> Editar
                                </button>`;
                    }
                }
            ],
            order: [[0, 'asc']],
            pageLength: currentLimit > 0 ? currentLimit : data.length,
            responsive: true,
           
        });
    }
    
    // Manejar clic en botón editar
    $(document).on('click', '.edit-cost', function() {
        const productId = $(this).data('id');
        loadProductCostData(productId);
    });

    // Cargar datos de costos para edición
    function loadProductCostData(productId) {
        $.get(`../controllers/product_price_controller.php?action=pricing_data&id=${productId}`, function(data) {
            $('#cost_product_id').val(productId);
            $('#base_cost').val(data.base_cost || '0');
            $('#import_tax').val(data.import_tax || '0');
            $('#shipping_cost').val(data.shipping_cost || '0');
            $('#local_transport').val(data.local_transport || '0');
            $('#storage_cost').val(data.storage_cost || '0');
            $('#insurance').val(data.insurance || '0');
            $('#customs_fees').val(data.customs_fees || '0');
            $('#currency_exchange_rate').val(data.currency_exchange_rate || '6.96');
            $('#profit_margin').val(data.profit_margin || '25');
            $('#market_demand_factor').val(data.market_demand_factor || '1.0');
            $('#competition_factor').val(data.competition_factor || '1.0');
            
            calculateAndDisplaySuggestedPrice();
            $('#costModal').modal('show');
        }).fail(function() {
            Swal.fire('Error', 'No se pudieron cargar los datos del producto', 'error');
        });
    }

    // Calcular precio sugerido en tiempo real
    $('.modal-body input').on('change keyup', function() {
        calculateAndDisplaySuggestedPrice();
    });

    function calculateAndDisplaySuggestedPrice() {
        const productId = $('#cost_product_id').val();
        if (!productId) return;

        const costData = {
            base_cost: $('#base_cost').val(),
            import_tax: $('#import_tax').val(),
            shipping_cost: $('#shipping_cost').val(),
            local_transport: $('#local_transport').val(),
            storage_cost: $('#storage_cost').val(),
            insurance: $('#insurance').val(),
            customs_fees: $('#customs_fees').val(),
            currency_exchange_rate: $('#currency_exchange_rate').val(),
            profit_margin: $('#profit_margin').val(),
            market_demand_factor: $('#market_demand_factor').val(),
            competition_factor: $('#competition_factor').val()
        };

        $.get(`../controllers/product_price_controller.php?action=suggested_price&id=${productId}`, costData, function(response) {
            $('#suggested_price_display').text('$' + parseFloat(response.suggested_price).toFixed(2));
        }).fail(function() {
            $('#suggested_price_display').text('$0.00 (Error)');
        });
    }

    // Guardar factores de costo
    $('#saveCostFactors').click(function() {
        const productId = $('#cost_product_id').val();
        const costData = {
            base_cost: $('#base_cost').val(),
            import_tax: $('#import_tax').val(),
            shipping_cost: $('#shipping_cost').val(),
            local_transport: $('#local_transport').val(),
            storage_cost: $('#storage_cost').val(),
            insurance: $('#insurance').val(),
            customs_fees: $('#customs_fees').val(),
            currency_exchange_rate: $('#currency_exchange_rate').val(),
            currency_code: 'USD',
            profit_margin: $('#profit_margin').val(),
            market_demand_factor: $('#market_demand_factor').val(),
            competition_factor: $('#competition_factor').val()
        };

        $.ajax({
            url: '../controllers/product_price_controller.php?action=update_cost_factors',
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({
                product_id: productId,
                ...costData
            }),
            success: function(response) {
                if (response.success) {
                    $('#costModal').modal('hide');
                    loadData(); // Recargar todos los datos
                    Swal.fire('Éxito', 'Factores de costo actualizados correctamente', 'success');
                } else {
                    Swal.fire('Error', response.error || 'Error al guardar los datos', 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Error en la comunicación con el servidor', 'error');
            }
        });
    });
    
    // Botón para actualizar datos
    $('#refreshData').click(function() {
        loadData();
    });
    
    // Cargar datos iniciales
    loadAvailableProducts();
    loadData();
});
</script>