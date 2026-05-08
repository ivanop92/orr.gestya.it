@include('default.common.header')
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Create Invoice</h4>

                    <div class="page-title-right">
                        <a href="{{ url('documenti/' . ($documento->attivo == 1 ? 'ca' : 'cp') . '/' . $dotes->cd_do) }}" class="btn btn-primary">Torna a Documenti</a>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <form enctype="multipart/form-data" method="post">
            <div class="row justify-content-center">
                <div class="col-xxl-9">
                    <div class="card">
                        <form class="needs-validation" novalidate id="invoice_form">
                            <div class="card-body border-bottom border-bottom-dashed p-4">
                                <div class="row">

                                    <select class="form-control mb-2"  name="cd_cf" id="selectCliente" onchange="compilaCampi(this.value)">
                                        <option value="0" disabled selected>-- Seleziona un Cliente --</option>

                                        <?php foreach($clienti as $c) {?>
                                        <option @if($clienteDotes->cd_cf === $c->cd_cf) selected @endif value="{{ $c->cd_cf }}">{{ $c->ragione_sociale }}</option>
                                        <?php } ?>
                                    </select>


                                    <div class="col-lg-4">
                                        <div class="profile-user mx-auto  mb-3">
                                            <input id="profile-img-file-input" type="file" class="profile-img-file-input" />
                                            <label for="profile-img-file-input" class="d-block" tabindex="0">
                                                            <span class="overflow-hidden border border-dashed d-flex align-items-center justify-content-center rounded" style=" width: 256px;">
                                                                <img src="{{ $c->immagine }}" class="card-logo card-logo-dark user-profile-image img-fluid" alt="logo dark">
                                                                <img src="{{ $c->immagine }}" class="card-logo card-logo-light user-profile-image img-fluid" alt="logo light">
                                                            </span>
                                            </label>
                                        </div>

                                        <div>
                                            <div>
                                                <label for="companyAddress">Indirizzo</label>
                                            </div>
                                            <div class="mb-2">
                                                <textarea class="form-control bg-light border-0" id="companyAddress"  name="indirizzo" rows="3" placeholder="Indirizzo" required> {{ $clienteDotes->indirizzo }}</textarea>
                                                <div class="invalid-feedback">
                                                    Please enter a address
                                                </div>
                                            </div>
                                            <div>
                                                <label for="companyAddress">Comune</label>
                                            </div>
                                            <div class="mb-2">
                                                <input type="text" class="form-control bg-light border-0" value="{{ $clienteDotes->comune }}" id="comune" name="comune" placeholder="Comune" required />
                                                <div class="invalid-feedback">
                                                    Inserisci Comune
                                                </div>
                                            </div>

                                            <div>
                                                <label for="companyAddress">Cap</label>
                                            </div>
                                            <div>
                                                <input type="text" class="form-control bg-light border-0" id="cap" name="cap" value="{{ $clienteDotes->cap }}" minlength="5" maxlength="6" placeholder="Codice Postale" required />
                                                <div class="invalid-feedback">
                                                    The US zip code must contain 5 digits, Ex. 45678
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-4 ms-auto">
                                        {{--<div class="mb-2">
                                            <input type="text" class="form-control bg-light border-0" id="registrationNumber" maxlength="12" placeholder="Legal Registration No" required />
                                            <div class="invalid-feedback">
                                                Please enter a registration no, Ex., 012345678912
                                            </div>
                                        </div>--}}

                                        <div>
                                            <label for="companyAddress">Ragione Sociale</label>
                                        </div>
                                        <div class="mb-2">
                                            <input type="text" class="form-control bg-light border-0" value="{{ $clienteDotes->ragione_sociale }}" id="ragioneSociale" name="ragione_sociale" placeholder="Ragione Sociale" required />
                                            <div class="invalid-feedback">
                                                Inserire Ragione Sociale
                                            </div>
                                        </div>
                                        <div>
                                            <label for="companyAddress">Partita Iva</label>
                                        </div>
                                        <div class="mb-2">
                                            <input type="text" class="form-control bg-light border-0" value="{{ $clienteDotes->piva }}" name="partita_iva" id="partitaIva" placeholder="P.Iva" required />
                                            <div class="invalid-feedback">
                                                Inserisci la partita Iva
                                            </div>
                                        </div>
                                        <div>
                                            <label for="companyAddress">Pec</label>
                                        </div>
                                        <div class="mb-2">
                                            <input type="email" class="form-control bg-light border-0" name="pec" value="{{ $clienteDotes->pec }}" id="pec" placeholder="PEC" required />
                                            <div class="invalid-feedback">
                                                Please enter a valid email, Ex., example@gamil.com
                                            </div>
                                        </div>
                                        {{--<div class="mb-2">
                                            <input type="text" class="form-control bg-light border-0" data-plugin="cleave-phone" id="compnayContactno" placeholder="Tel" required />
                                            <div class="invalid-feedback">
                                                Please enter a contact number
                                            </div>
                                        </div>--}}

                                    </div>

                                </div>

                                <!--end row-->
                            </div>
                            <div class="card-body p-4 border-top border-top-dashed">
                                <div class="row">

                                    <div class="col-lg-4 col-sm-6">

                                        <div>
                                            <label for="date-field">Data_consegna</label>
                                            <input type="text" class="form-control bg-light border-0" id="date-field" value="{{ $dotes->data_consegna }}" name="data_consegna" data-provider="flatpickr" data-time="true" placeholder="Select Date-time">
                                        </div>

                                        <div>
                                            <label for="billingName" class="text-muted text-uppercase fw-semibold">Indirizzo di fatturazione</label>
                                        </div>
                                        <div class="mb-2">
                                            <input type="text" class="form-control bg-light border-0" value="{{ $dotes->ragione_sociale_fatturazione }}" id="ragioneSocialeFatturazione" name="ragione_sociale_fatturazione" placeholder="Ragione Sociale" required />
                                            <div class="invalid-feedback">
                                                Please enter a full name
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <input type="text" class="form-control bg-light border-0" name="comune_fatturazione" value="{{ $dotes->comune_fatturazione }}" id="comune_fatturazione" placeholder="Comune" required />
                                            <div class="invalid-feedback">
                                                Please enter a full name
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <textarea class="form-control bg-light border-0" id="indirizzoFatturazione" name="indirizzo_fatturazione"  rows="3" placeholder="Indirizzo" required> {{ $dotes->indirizzo_fatturazione }}</textarea>
                                            <div class="invalid-feedback">
                                                Please enter a address
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <input type="text" class="form-control bg-light border-0" id="partitaIvaFatturazione" value="{{ $dotes->partita_iva_fatturazione }}" name="partita_iva_fatturazione" placeholder="Partita Iva" required />
                                            <div class="invalid-feedback">
                                                Please enter a tax number
                                            </div>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="same" name="same" onchange="billingFunction()" />
                                            <label class="form-check-label" for="same">
                                                Will your Billing and Shipping address same?
                                            </label>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-sm-6 ms-auto">
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <div>
                                                    <label for="shippingName" class="text-muted text-uppercase fw-semibold">Indirizzo di Consegna</label>
                                                </div>
                                                <div class="mb-2">
                                                    <input type="text" class="form-control bg-light border-0" name="comune_consegna" value="{{ $dotes->comune_consegna }}" id="comune_consegna" placeholder="Comune" required />
                                                    <div class="invalid-feedback">
                                                        Please enter a full name
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <textarea class="form-control bg-light border-0" id="indirizzoConsegna" name="indirizzo_consegna"  rows="3" placeholder="Address" required> {{$dotes->indirizzo_consegna}}</textarea>
                                                    <div class="invalid-feedback">
                                                        Please enter a address
                                                    </div>
                                                </div>
                                                {{--<div class="mb-2">
                                                    <input type="text" class="form-control bg-light border-0" data-plugin="cleave-phone" id="shippingPhoneno" placeholder="(123)456-7890" required />
                                                    <div class="invalid-feedback">
                                                        Please enter a phone number
                                                    </div>
                                                </div>--}}
                                                {{--<div>
                                                    <input type="text" class="form-control bg-light border-0" id="shippingTaxno" placeholder="Tax Number" required />
                                                    <div class="invalid-feedback">
                                                        Please enter a tax number
                                                    </div>
                                                </div>--}}
                                            </div>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="invoice-table table table-borderless table-nowrap mb-0">
                                        <thead class="align-middle">
                                        <tr class="table-active">
                                            <th scope="col" style="width: 50px;">#</th>
                                            <th scope="col">
                                                Product Details
                                            </th>
                                            <th scope="col" style="width: 120px;">
                                                <div class="d-flex currency-select input-light align-items-center">
                                                    Rate
                                                    <select class="form-selectborder-0 bg-light" data-choices data-choices-search-false id="choices-payment-currency" onchange="otherPayment()">
                                                        <option value="$">($)</option>
                                                        <option value="£">(£)</option>
                                                        <option value="₹">(₹)</option>
                                                        <option value="€">(€)</option>
                                                    </select>
                                                </div>
                                            </th>
                                            <th scope="col" style="width: 120px;">Quantity</th>
                                            <th scope="col" class="text-end" style="width: 150px;">Amount</th>
                                            <th scope="col" class="text-end" style="width: 105px;"></th>
                                        </tr>
                                        </thead>
                                        <tbody id="newlink">
                                        @foreach($dorig as $index => $d)
                                        <tr id="{{ $index + 1 }}" class="product">
                                            <th scope="row" class="product-id">{{ $index + 1 }}</th>

                                            <td class="text-start">
                                                <div class="mb-2">
                                                    <input type="text" class="form-control bg-light border-0" value="{{ $d->nome_prodotto }}" name="products[{{ $index }}][nome_prodotto]" id="productName-{{ $index + 1 }}" placeholder="Product Name" required />
                                                    <div class="invalid-feedback">
                                                        Please enter a product name
                                                    </div>
                                                </div>
                                                <textarea class="form-control bg-light border-0"  name="products[{{ $index }}][dettagli_prodotto]" id="productDetails-{{ $index + 1 }}" rows="2" placeholder="Product Details"> {{ $d->dettagli_prodotto }} </textarea>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control product-price bg-light border-0" value="{{ $d->prezzo_unitario }}" name="products[{{ $index }}][prezzo_unitario]" id="productRate-{{ $index + 1 }}" step="0.01" placeholder="0.00" required />
                                                <div class="invalid-feedback">
                                                    Please enter a rate
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-step">
                                                    <button type="button" class="minus">–</button>
                                                    <input type="number" class="form-control product-quantity" value="{{ $d->qta }}" name="products[{{ $index }}][qta]" id="product-qty-{{ $index + 1 }}"  readonly />
                                                    <button type="button" class="plus">+</button>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div>
                                                    <input type="text" class="form-control bg-light border-0 product-line-price" value="{{ $d->prezzo_totale }}" name="products[{{ $index }}][prezzo_totale]" id="productPrice-{{ $index + 1 }}" placeholder="$0.00" readonly />
                                                </div>
                                            </td>
                                            <td class="product-removal">
                                                <a href="javascript:void(0)" class="btn btn-success" onclick="removeProduct({{ $index + 1 }})">Delete</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                        </tbody>

                                        <tbody>
                                        <tr id="newForm" style="display: none;"><td class="d-none" colspan="5"><p>Add New Form</p></td></tr>
                                        <tr>
                                            <td colspan="5">
                                                <a href="javascript:new_link()" id="add-item" class="btn btn-soft-secondary fw-medium"><i class="ri-add-fill me-1 align-bottom"></i> Add Item</a>
                                            </td>
                                        </tr>
                                        <tr class="border-top border-top-dashed mt-2">
                                            <td colspan="3" style="width: 70%"></td>
                                            <td colspan="2" class="p-0">
                                                <table class="table table-borderless table-sm table-nowrap align-middle mb-0">
                                                    <tbody>
                                                    <tr>
                                                        <th scope="row">Imponibile</th>
                                                        <td style="width:150px;">
                                                            <input type="text" class="form-control bg-light border-0" id="cart-subtotal" name="imponibile" value="{{$dotes->imponibile}}" placeholder="$0.00" readonly />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Iva</th>
                                                        <td>
                                                            <div class="d-flex align-items-center" style="width: 200px">
                                                                <input type="text" class="form-control bg-light border-0" name="iva" id="cart-tax"  placeholder="€0.00" readonly />
                                                                <select  class="form-select ms-2" id="tax-rate-select" name="iva_percentuale"  onchange="updateTotals()">
                                                                    <!-- Genera dinamicamente le opzioni da 0% a 30% -->
                                                                    <script>
                                                                        let percentuale = parseInt('<?php echo $dotes->iva_percentuale; ?>', 10); // Converte in numero ed elimina spazi extra
                                                                        // Trova l'elemento select nel DOM
                                                                        const select = document.getElementById('tax-rate-select');

                                                                        // Crea le opzioni dinamicamente da 0% a 30%
                                                                        for (let i = 0; i <= 30; i++) {
                                                                            // Crea l'elemento option
                                                                            let option = document.createElement('option');
                                                                            option.value = i / 100;
                                                                            option.text = i + '%';

                                                                            // Imposta l'attributo selected se la percentuale corrisponde
                                                                            if (percentuale === i) {
                                                                                console.log(i)
                                                                                option.selected = true;
                                                                            }

                                                                            // Aggiungi l'opzione alla select
                                                                            select.appendChild(option);
                                                                        }
                                                                    </script>


                                                                </select>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th scope="row">Sconto</th>
                                                        <td>
                                                            <div class="d-flex align-items-center" style="width: 200px">
                                                                <input type="text" class="form-control bg-light border-0" id="cart-discount" name="sconto" placeholder="€0.00" readonly />
                                                                <select name="sconto_percentuale" class="form-select ms-2" id="discount-rate-select" onchange="updateTotals()">
                                                                    <!-- Genera dinamicamente le opzioni da 0% a 100% -->
                                                                    <script>

                                                                            let scPercentuale = parseInt('<?php echo $dotes->sconto_percentuale; ?>', 10); // Converte in numero ed elimina spazi extra
                                                                            // Trova l'elemento select nel DOM
                                                                            const scSelect = document.getElementById('discount-rate-select');

                                                                            // Crea le opzioni dinamicamente da 0% a 30%
                                                                            for (let i = 0; i <= 30; i++) {
                                                                            // Crea l'elemento option
                                                                            let option = document.createElement('option');
                                                                            option.value = i / 100;
                                                                            option.text = i + '%';

                                                                            // Imposta l'attributo selected se la percentuale corrisponde
                                                                            if (scPercentuale === i) {
                                                                            console.log(i)
                                                                            option.selected = true;
                                                                        }

                                                                            // Aggiungi l'opzione alla select
                                                                            scSelect.appendChild(option);
                                                                        }
                                                                    </script>
                                                                </select>

                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th scope="row">Costi di Trasporto</th>
                                                        <td>
                                                            <div class="d-flex align-items-center" style="width: 200px">
                                                                <input type="text" class="form-control bg-light border-0" id="cart-shipping" name="costo_trasporto"  placeholder="€0.00" readonly />
                                                                <select name="costo_trasporto_percentuale" class="form-select ms-2" id="shipping-rate-select" onchange="updateTotals()">
                                                                    <!-- Genera dinamicamente le opzioni da 0% a 50% -->
                                                                    <script>
                                                                        let trPercentuale = parseInt('<?php echo $dotes->costo_trasporto_percentuale; ?>', 10); // Converte in numero ed elimina spazi extra
                                                                        // Trova l'elemento select nel DOM
                                                                        const trSelect = document.getElementById('shipping-rate-select');

                                                                        // Crea le opzioni dinamicamente da 0% a 30%
                                                                        for (let i = 0; i <= 30; i++) {
                                                                            // Crea l'elemento option
                                                                            let option = document.createElement('option');
                                                                            option.value = i / 100;
                                                                            option.text = i + '%';

                                                                            // Imposta l'attributo selected se la percentuale corrisponde
                                                                            if (trPercentuale === i) {
                                                                                console.log(i)
                                                                                option.selected = true;
                                                                            }

                                                                            // Aggiungi l'opzione alla select
                                                                            trSelect.appendChild(option);
                                                                        }
                                                                    </script>
                                                                </select>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <tr class="border-top border-top-dashed">
                                                        <th scope="row">Totale</th>
                                                        <td>
                                                            <input type="text" class="form-control bg-light border-0" name="costo_totale" id="cart-total" value="{{ $dotes->costo_totale }}" placeholder="$0.00" readonly />
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <!--end table-->
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <!--end table-->
                                </div>

                                <!--end row-->
                                <div class="mt-4">
                                    <label for="exampleFormControlTextarea1" class="form-label text-muted text-uppercase fw-semibold">NOTES</label>
                                    <textarea class="form-control alert alert-info" id="exampleFormControlTextarea1" placeholder="Notes" rows="2" required>All accounts are to be paid within 7 days from receipt of invoice. To be paid by cheque or credit card or direct payment online. If account is not paid within 7 days the credits details supplied as confirmation of work undertaken will be charged the agreed quoted fee noted above.</textarea>
                                </div>
                                <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                                    <input type="submit" name="modifica_dotes" class="btn btn-success" value="Modifica">
                                   {{-- <a href="javascript:void(0);" class="btn btn-primary"><i class="ri-download-2-line align-bottom me-1"></i> Download Invoice</a>
                                    <a href="javascript:void(0);" class="btn btn-danger"><i class="ri-send-plane-fill align-bottom me-1"></i> Send Invoice</a>--}}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!--end col-->
            </div>
        </form> <!--end row-->

    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->
@include('default.common.footer')


<script>
    function compilaCampi(value) {
        jQuery.ajax({
            url: "<?php echo URL::asset('ajax/getClienteForOrdine') ?>/",
            type:'GET',
            data:{cd_cf:value},
            success: function(result){
                console.log(result)
                document.getElementById('companyAddress').innerHTML = result.indirizzo;
                document.getElementById('partitaIva').value = result.piva;
                document.getElementById('cap').value = result.cap;
                document.getElementById('pec').value = result.pec;
                document.getElementById('ragioneSociale').value = result.ragione_sociale;
                document.getElementById('comune').value = result.comune;
                document.getElementById('indirizzoFatturazione').innerHTML = result.indirizzo;
                document.getElementById('ragioneSocialeFatturazione').value = result.ragione_sociale;
                document.getElementById('partitaIvaFatturazione').value = result.piva;
                document.getElementById('comune_fatturazione').value = result.comune;


            }});


    }


    /*parte dell'aggiunta degli articoli*/

    // Variabile globale per contare gli articoli
    let productCount = document.querySelectorAll('.product').length; // Conta i prodotti già presenti

    // Funzione per aggiungere un nuovo articolo
    function new_link() {
        const index = productCount;
        productCount++;
        const newRow = `
        <tr id="${productCount}" class="product">
            <th scope="row" class="product-id">${productCount}</th>
            <td class="text-start">
                <div class="mb-2">
                    <input type="text" class="form-control bg-light border-0" name="products[${index}][nome_prodotto]" id="productName-${productCount}" placeholder="Product Name" required />
                    <div class="invalid-feedback">Please enter a product name</div>
                </div>
                <textarea class="form-control bg-light border-0" name="products[${index}][dettagli_prodotto]" id="productDetails-${productCount}" rows="2" placeholder="Product Details"></textarea>
            </td>
            <td>
                <input type="number" class="form-control product-price bg-light border-0" name="products[${index}][prezzo_unitario]" id="productRate-${productCount}" step="0.01" placeholder="0.00" required oninput="updatePrice(${productCount})" />
                <div class="invalid-feedback">Please enter a rate</div>
            </td>
            <td>
                <div class="input-step">
                    <button type="button" class="minus" onclick="changeQuantity(${productCount}, -1)">–</button>
                    <input type="number" class="product-quantity" name="products[${index}][qta]" id="product-qty-${productCount}" value="0" readonly />
                    <button type="button" class="plus" onclick="changeQuantity(${productCount}, 1)">+</button>
                </div>
            </td>
            <td class="text-end">
                <div>
                    <input type="text" class="form-control bg-light border-0 product-line-price" name="products[${index}][prezzo_totale]" id="productPrice-${productCount}" placeholder="$0.00" readonly />
                </div>
            </td>
            <td class="product-removal">
                <a href="javascript:void(0)" class="btn btn-success" onclick="removeProduct(${productCount})">Delete</a>
            </td>
        </tr>
    `;

        // Aggiungi la nuova riga al tbody
        document.getElementById('newlink').insertAdjacentHTML('beforeend', newRow);
    }


    // Funzione per cambiare la quantità
    function changeQuantity(id, change) {
        const qtyInput = document.getElementById(`product-qty-${id}`);
        let quantity = parseInt(qtyInput.value);
        quantity = Math.max(0, quantity + change); // Impedisce valori negativi
        qtyInput.value = quantity;
        updatePrice(id); // Aggiorna il prezzo della riga
    }

    // Funzione per aggiornare il prezzo dell'articolo
    function updatePrice(id) {
        const rate = parseFloat(document.getElementById(`productRate-${id}`).value) || 0;
        const quantity = parseInt(document.getElementById(`product-qty-${id}`).value) || 0;
        const linePrice = rate * quantity;
        document.getElementById(`productPrice-${id}`).value = `€${linePrice.toFixed(2)}`;

        updateTotals(); // Aggiorna i totali generali
    }

    function updateTotals() {
        let subtotal = 0;

        // Calcola il subtotale
        document.querySelectorAll('.product-line-price').forEach(priceField => {
            const price = parseFloat(priceField.value.replace('€', '')) || 0;
            subtotal += price;
        });

        // Recupera il valore della tassa selezionata
        const taxRate = parseFloat(document.getElementById('tax-rate-select').value) || 0;
        const tax = subtotal * taxRate;

        // Recupera il valore dello sconto selezionato
        const discountRate = parseFloat(document.getElementById('discount-rate-select').value) || 0;
        const discount = subtotal * discountRate;

        // Recupera il valore dello shipping selezionato
        const shippingRate = parseFloat(document.getElementById('shipping-rate-select').value) || 0;
        const shipping = subtotal * shippingRate;

        // Calcola il totale
        const total = subtotal + tax - discount + shipping;

        // Aggiorna i campi nella tabella
        document.getElementById('cart-subtotal').value = `€${subtotal.toFixed(2)}`;
        document.getElementById('cart-tax').value = `€${tax.toFixed(2)}`;
        document.getElementById('cart-discount').value = `€${discount.toFixed(2)}`;
        document.getElementById('cart-shipping').value = `€${shipping.toFixed(2)}`;
        document.getElementById('cart-total').value = `€${total.toFixed(2)}`;

        // Controllo per verificare se il totale è corretto
        console.log(`Subtotal: ${subtotal}, Tax: ${tax}, Discount: ${discount}, Shipping: ${shipping}, Total: ${total}`);
    }



    // Funzione per rimuovere un prodotto
    function removeProduct(id) {
        const row = document.getElementById(id);
        if (row) {
            row.remove();
            updateTotals(); // Aggiorna i totali dopo aver rimosso un prodotto
        }
    }

    // Inizializza gli eventi sugli elementi esistenti
    function initializeEvents() {
        // Associa l'evento oninput per aggiornare il prezzo sui campi esistenti
        document.querySelectorAll('.product-price').forEach((input, index) => {
            input.addEventListener('input', () => updatePrice(index + 1));
        });

        // Associa l'evento click per le quantità su campi esistenti
        document.querySelectorAll('.minus').forEach((button, index) => {
            button.addEventListener('click', () => changeQuantity(index + 1, -1));
        });

        document.querySelectorAll('.plus').forEach((button, index) => {
            button.addEventListener('click', () => changeQuantity(index + 1, 1));
        });
    }

    // Inizializza i calcoli e gli eventi quando la pagina viene caricata
    document.addEventListener('DOMContentLoaded', () => {
        initializeEvents();
        updateTotals();
    });







</script>

