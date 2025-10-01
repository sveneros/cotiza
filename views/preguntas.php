<?php
include('../layout/header_clientes.php');
?>

<div class="container-fluid">
  <!-- Breadcrumb start -->
  <div class="row m-1">
    <div class="col-12">
      <h4 class="main-title">Preguntas Frecuentes</h4>
      <ul class="app-line-breadcrumbs mb-3">
        <li class="">
          <a href="tienda.php" class="f-s-14 f-w-500">
            <span>
              <i class="ph-duotone ph-house f-s-16"></i> Inicio
            </span>
          </a>
        </li>
        <li class="active">
          <a href="#" class="f-s-14 f-w-500">Preguntas Frecuentes</a>
        </li>
      </ul>
    </div>
  </div>
  <!-- Breadcrumb end -->

  <!-- Content start -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="accordion accordion-flush" id="faqAccordion">
            <div class="accordion-item">
              <h2 class="accordion-header" id="flush-headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne">
                  ¿Cómo puedo realizar un pedido?
                </button>
              </h2>
              <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  <p>Para realizar un pedido, siga estos pasos:</p>
                  <ol>
                    <li>Navegue por nuestro catálogo de productos</li>
                    <li>Seleccione los productos que desea comprar</li>
                    <li>Agréguelos al carrito de compras</li>
                    <li>Proceda al checkout</li>
                    <li>Complete sus datos de envío y pago</li>
                    <li>Confirme su pedido</li>
                  </ol>
                  <p>Recibirá un correo electrónico con la confirmación de su pedido.</p>
                </div>
              </div>
            </div>
            
            <div class="accordion-item">
              <h2 class="accordion-header" id="flush-headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo">
                  ¿Qué métodos de pago aceptan?
                </button>
              </h2>
              <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  <p>Aceptamos los siguientes métodos de pago:</p>
                  <ul>
                    <li>Tarjetas de crédito/débito (Visa, MasterCard, American Express)</li>
                    <li>Transferencias bancarias</li>
                    <li>Pago contra entrega (solo en algunas zonas)</li>
                    <li>PayPal</li>
                  </ul>
                </div>
              </div>
            </div>
            
            <!-- Más preguntas frecuentes -->
            <div class="accordion-item">
              <h2 class="accordion-header" id="flush-headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree">
                  ¿Cuál es el tiempo de entrega?
                </button>
              </h2>
              <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  <p>El tiempo de entrega varía según la ubicación:</p>
                  <ul>
                    <li>Ciudad principal: 1-2 días hábiles</li>
                    <li>Otras ciudades: 3-5 días hábiles</li>
                    <li>Zonas rurales: 5-7 días hábiles</li>
                  </ul>
                  <p>Los tiempos pueden variar en temporadas de alta demanda.</p>
                </div>
              </div>
            </div>
            
            <div class="accordion-item">
              <h2 class="accordion-header" id="flush-headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour">
                  ¿Puedo devolver un producto?
                </button>
              </h2>
              <div id="flush-collapseFour" class="accordion-collapse collapse" aria-labelledby="flush-headingFour" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  <p>Aceptamos devoluciones dentro de los 15 días posteriores a la recepción del producto, siempre que:</p>
                  <ul>
                    <li>El producto esté en su estado original</li>
                    <li>Se presente la factura de compra</li>
                    <li>El producto no sea de aquellos marcados como "no retornables"</li>
                  </ul>
                  <p>Para iniciar una devolución, por favor contáctenos a través de nuestro formulario de contacto.</p>
                </div>
              </div>
            </div>
            
            <div class="accordion-item">
              <h2 class="accordion-header" id="flush-headingFive">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFive">
                  ¿Cómo puedo hacer seguimiento a mi pedido?
                </button>
              </h2>
              <div id="flush-collapseFive" class="accordion-collapse collapse" aria-labelledby="flush-headingFive" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  <p>Una vez que su pedido haya sido enviado, recibirá un correo electrónico con un número de seguimiento y un enlace para rastrear su paquete.</p>
                  <p>También puede ingresar a su cuenta en nuestro sitio web y ver el estado de sus pedidos en la sección "Mis Pedidos".</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="mt-5">
            <h4>¿No encontró respuesta a su pregunta?</h4>
            <p>Contáctenos a través de nuestro <a href="contacto.php" class="text-primary">formulario de contacto</a> y con gusto le ayudaremos.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Content end -->
</div>

<?php
include('../layout/footer.php');
?>