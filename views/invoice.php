
<!-- header -->
<?php
include('../layout/header.php');
?>
            <!-- <div class="container-fluid ">
                <div class="row m-1">
                    <div class="col-12 ">
                        <h4 class="main-title">Invoice</h4>
                        <ul class="app-line-breadcrumbs mb-3">
                            <li class="">
                                <a href="#" class="f-s-14 f-w-500">
                      <span>
                        <i class="ph-duotone  ph-stack f-s-16"></i> Apps
                      </span>
                                </a>
                            </li>
                            <li class="active">
                                <a href="#" class="f-s-14 f-w-500">Invoice</a>
                            </li>
                        </ul>
                    </div>
                </div>
               
            </div> -->

            <!-- Invoice start -->
            <div class="container invoice-container">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <td>
                                                <table class="table table-lg w-100 invoice-header">
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class=" mb-3">
                                                                <div class="mb-3">
                                                                    <img src="../assets/images/logo/1.png" class="w-200" alt="">
                                                                </div>
                                                                <div>
                                                                    <address>
                                                                        Av. 20 de Octubre Nro 2332, Edif. Guadalquivir<BR>
                                                                        Tel/Fax: 2417461 <br>
                                                                        Email: info@aplitec.com<br>
                                                                        La Paz - Bolivia
                                                                    </address>
                            
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="mb-3">
                                                                <div class="mb-3">
                                                                    <h3 class="text-primary"></h3>
                                                                </div>
                                                                <address>
                                                                    
                                                                    <br>
                                                                    <br>
                                                                     <strong>REF.: ANALIZADOR DE CALIDAD DE ENERGÍA ELÉCTRICA</strong>
                                                                </address>
                                                               
                                                               
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <div class="mb-1">
                                                                    <h3 class="text-primary">COTIZACIÓN</h3>
                                                                </div>
                                                                <p>Nro. <strong>#900123</strong></p>
                                                                <p>Para: <strong>Hospital San Juan de Dios</strong></p>
                                                                <p>Fecha:<strong>20/11/2024</strong></p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                   
                                                    <tr>
                                                        <td>
                                                            <div class="mb-3 ">
                                                                <h6 class="f-w-600">Cliente</h6>
                                                                Nicolas Valdivieso
                                                                <address>
                                                                    2399, Villa Fátima,
                                                                    <br>
                                                                    77566666 <br>
                                                                    NicolasCKarl@gmail.com
                                                                </address>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="mb-3">
                                                                <h6 class="f-w-600">Datos para la Factura</h6>
                                                                
                                                                <address>
                                                                Razon Social: Valdivieso<BR>
                                                                    Nit: 4555464014
                                                                    
                                                                </address>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class=" text-end mb-3">
                                                                
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <table class="table table-lg table-bottom-border invoice-table w-100">

                                                    <thead>
                                                    <tr>
                                                        <th scope="col">Item</th>
                                                        <th scope="col">Cod.</th>
                                                        <th scope="col">Descripcion</th>
                                                        <th scope="col">Cantidad</th>
                                                        <th scope="col">P.U.</th>
                                                        <th scope="col">TOTAL</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>34534</td>
                                                        <td class="f-s-14">
                                                            <p class="text-elips mb-0">
                                                                Fluke 345
                                                            </p>
                                                        </td>
                                                        <td>3</td>
                                                        <td>Bs. 200</td>
                                                        <td>Bs. 600</td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>67856</td>
                                                        <td class="f-s-14">
                                                            <p class="text-elips mb-0">
                                                                Fluke 123
                                                            </p>
                                                        </td>
                                                        <td>2</td>
                                                        <td>Bs. 200</td>
                                                        <td>Bs. 400</td>
                                                    </tr>

                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="f-s-14">
                                                            <p class="text-elips mb-0">
                                                               
                                                            </p>
                                                        </td>
                                                        <td></td>
                                                        <td><b>TOTAL</b></td>
                                                        <td><b>Bs. 67899</b></td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td colspan="3"></td>
                                                        <td colspan="3">
                                                            <table class="table mb-0">
                                                                <tbody>
                                                                <tr>
                                                                    <td><b>Garantía:</b></td>
                                                                    <td>1 año por defectos de fábrica</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Validez de la oferta:</b></td>
                                                                    <td>30 días Calendario</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Forma de pago:</b></td>
                                                                    <td>A convenir</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Costo de envío</b></td>
                                                                    <td>Bs. 50</td>
                                                                </tr>

                                                                <tr>
                                                                    <th class="border-0 pb-0">Tienpo de entrega</th>
                                                                    <th class="border-0 pb-0">Aproximadamente 40 días calendario; computables desde el siguiente día hábil de recibida su Orden de Compra, firma de contrato o Pago por adelantado</th>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="invoice-footer float-end mb-3">
                            <button type="button" class="btn btn-primary m-1" onclick="window.print()"><i
                                        class="ti ti-printer"></i> Imprimir</button>
                            <!-- <button type="button" class="btn btn-secondary m-1"><i class="ti ti-send"></i> Send Invoice</button>
                            <button type="button" class="btn btn-success m-1"><i class="ti ti-download"></i> Download</button> -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Invoice end -->
       

    <!-- footer -->
    <?php
    include('../layout/footer.php');
    ?>
