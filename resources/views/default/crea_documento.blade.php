@include('default.common.header')
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Crea Documento</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <h4>Crea Documento di tipo: {{ $cd_do }}</h4>

                        </ol>
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

                                    <select class="form-control mb-2"  name="cd_cf" id="selectCliente" data-choices data-choices-search-true onchange="compilaCampi(this.value)">
                                        <option value="0" disabled selected>-- Seleziona un Cliente --</option>

                                        <?php foreach($clienti as $c) {?>
                                            <option value="{{ $c->cd_cf }}">{{ $c->ragione_sociale }}</option>
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
                                                <textarea class="form-control bg-light border-0" id="companyAddress" name="indirizzo" rows="3" placeholder="Indirizzo" required></textarea>
                                                <div class="invalid-feedback">
                                                    Please enter a address
                                                </div>
                                            </div>
                                            <div>
                                                <label for="companyAddress">Comune</label>
                                            </div>
                                            <div class="mb-2">
                                                <input type="text" class="form-control bg-light border-0" id="comune" name="comune" placeholder="Comune" required />
                                                <div class="invalid-feedback">
                                                    Inserisci Comune
                                                </div>
                                            </div>

                                            <div>
                                                <label for="companyAddress">Cap</label>
                                            </div>
                                            <div>
                                                <input type="text" class="form-control bg-light border-0" id="cap" name="cap" minlength="5" maxlength="6" placeholder="Codice Postale" required />
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
                                            <input type="text" class="form-control bg-light border-0" id="ragioneSociale" name="ragione_sociale" placeholder="Ragione Sociale" required />
                                            <div class="invalid-feedback">
                                               Inserire Ragione Sociale
                                            </div>
                                        </div>
                                        <div>
                                            <label for="companyAddress">Partita Iva</label>
                                        </div>
                                        <div class="mb-2">
                                            <input type="text" class="form-control bg-light border-0" name="partita_iva" id="partitaIva" placeholder="P.Iva" required />
                                            <div class="invalid-feedback">
                                                Inserisci la partita Iva
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <input type="text" class="form-control bg-light border-0" name="sdi" id="sdi" placeholder="SDI" required />
                                            <div class="invalid-feedback">
                                                Inserisci SDI
                                            </div>
                                        </div>
                                        <div>
                                            <label for="companyAddress">Pec</label>
                                        </div>
                                        <div class="mb-2">
                                            <input type="email" class="form-control bg-light border-0" name="pec" id="pec" placeholder="PEC" required />
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
                                            <input type="text" class="form-control bg-light border-0" id="date-field" name="data_consegna" data-provider="flatpickr" data-time="true" placeholder="Select Date-time">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">CIG</label>
                                            <input type="text" class="form-control bg-light border-0" name="cig" placeholder="Codice CIG" />
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">CUP</label>
                                            <input type="text" class="form-control bg-light border-0" name="cup" placeholder="Codice CUP" />
                                        </div>

                                    <div>
                                        <label for="billingName" class="text-muted text-uppercase fw-semibold">Indirizzo di fatturazione</label>
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" class="form-control bg-light border-0" id="ragioneSocialeFatturazione" name="ragione_sociale_fatturazione" placeholder="Ragione Sociale" required />
                                        <div class="invalid-feedback">
                                            Please enter a full name
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" class="form-control bg-light border-0" name="comune_fatturazione" id="comune_fatturazione" placeholder="Comune" required />
                                        <div class="invalid-feedback">
                                            Please enter a full name
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <textarea class="form-control bg-light border-0" id="indirizzoFatturazione" name="indirizzo_fatturazione" rows="3" placeholder="Indirizzo" required></textarea>
                                        <div class="invalid-feedback">
                                            Please enter a address
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <input type="text" class="form-control bg-light border-0" id="partitaIvaFatturazione" name="partita_iva_fatturazione" placeholder="Partita Iva" required />
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
                                                <input type="text" class="form-control bg-light border-0" name="comune_consegna" id="comune_consegna" placeholder="Comune" required />
                                                <div class="invalid-feedback">
                                                    Please enter a full name
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <textarea class="form-control bg-light border-0" id="indirizzoConsegna" name="indirizzo_consegna" rows="3" placeholder="Address" required></textarea>
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
                                <div class="row">
                                    @if ($scanBarcodeEnabled)
                                        <div class="row mb-3">
                                            <div class="col-lg-4">
                                                <label for="barcodeInput" class="form-label">Scan Barcode</label>
                                                <input type="text" class="form-control" id="barcodeInput" disabled placeholder="Scan a barcode" />
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <table class="invoice-table table table-borderless table-nowrap mb-0">
                                    <thead class="align-middle">
                                    <tr class="table-active">
                                        <th scope="col" style="width: 50px;">#</th>
                                        <th scope="col">
                                            Dettagli Prodotto
                                        </th>
                                        <th scope="col" style="width: 120px;">
                                            <div class="d-flex currency-select input-light align-items-center">
                                                Prezzo
                                                <select class="form-selectborder-0 bg-light" data-choices data-choices-search-false id="choices-payment-currency" onchange="otherPayment()">
                                                    <option value="$">($)</option>
                                                    <option value="£">(£)</option>
                                                    <option value="₹">(₹)</option>
                                                    <option value="€">(€)</option>
                                                </select>
                                            </div>
                                        </th>
                                        <th>Iva</th>
                                        <th >Lotto</th>
                                        <th>Quantità</th>
                                        <th>UM</th>
                                        <th scope="col" class="text-end" >Totale</th>
                                        <th scope="col" class="text-end" style="width: 105px;"></th>
                                    </tr>
                                    </thead>
                                    <tbody id="newlink">
                                    <tr id="1" class="product">
                                        <th scope="row" class="product-id">1</th>
                                        <td class="text-start">
                                            <div class="mb-2">
                                                <!-- Select per scegliere da elenco predefinito o selezionare "Altro" -->
                                                <select
                                                        class="form-control bg-light border-0"
                                                        id="productSelect-1"
                                                        data-choices data-choices-search-true
                                                        onchange="updateProductNameField()"
                                                        required>

                                                    <option value="">Seleziona i tuoi prodotti</option>

                                                    <!-- Opzioni predefinite dal database -->
                                                    <?php foreach($prodotti_finiti as $prodotto): ?>
                                                    <option value="<?= $prodotto->id ?>,<?= htmlspecialchars($prodotto->titolo) ?>">
                                                            <?= htmlspecialchars($prodotto->titolo) ?>
                                                    </option>
                                                    <?php endforeach; ?>

                                                            <!-- Opzione per inserire un valore personalizzato -->
                                                    <option value="custom">Altro...</option>
                                                </select>

                                                <!-- Campo input nascosto per inserire un valore personalizzato -->
                                                <input
                                                        type="text"
                                                        class="form-control bg-light border-0 mt-2"
                                                        id="customProductName-1"
                                                        placeholder="Enter custom product name"
                                                        style="display: none;"
                                                        oninput="updateProductNameField()"
                                                />

                                                <!-- Campo nascosto per memorizzare il valore effettivo da inviare -->
                                                <input
                                                        type="hidden"
                                                        name="products[0][nome_prodotto]"
                                                        id="productNameHidden-1"
                                                />
                                                <input
                                                        type="hidden"
                                                        name="products[0][id_articolo]"
                                                        id="productArticoloHidden-1"
                                                />

                                                <div class="invalid-feedback">
                                                    Please enter a product name
                                                </div>
                                            </div>

                                            <textarea class="form-control bg-light border-0" name="products[0][dettagli_prodotto]" id="productDetails-1" rows="2" placeholder="Product Details"></textarea>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control product-price bg-light border-0" name="products[0][prezzo_unitario]" id="productRate-1" step="0.01" placeholder="0.00" required />
                                            <div class="invalid-feedback">
                                                Please enter a rate
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" style="width: 50px" class="form-control  bg-light border-0" name="products[0][iva]" id="productIva-1" step="0.01" placeholder="0%" required />
                                        </td>
                                        <td>
                                            <input type="number" style="width: 60px" class="form-control   bg-light border-0" name="products[0][lotto]" id="productLotto-1"  placeholder="Lotto" required />
                                        </td>
                                        <td>
                                            <div class="input-step">
                                                <button type="button" class="minus">–</button>
                                                <input type="number" class="form-control product-quantity" name="products[0][qta]" id="product-qty-1" value="0" readonly />
                                                <button type="button" class="plus">+</button>
                                            </div>
                                        </td>
                                        <td>
                                            <select style="width: 50px" class="form-control" id="unitOfMeasure" name="products[0][um]" data-choices data-choices-search-true required>
                                                <option value="" disabled selected>UM</option>
                                                <option value="KG">Kg</option>
                                                <option value="M">m</option>
                                                <option value="L">l</option>
                                                <option value="PZ">Pz</option>
                                            </select>
                                        </td>
                                        <td class="text-end">
                                            <div>
                                                <input type="text" style="width: 100px" class="form-control bg-light border-0 product-line-price" name="products[0][prezzo_totale]" id="productPrice-1" placeholder="$0.00" readonly />
                                            </div>
                                        </td>
                                        <td class="product-removal">
                                            <a href="javascript:void(0)" class="btn btn-success" onclick="removeProduct(1)">Elimina</a>
                                        </td>
                                    </tr>
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
                                                        <input style="width: 120px" type="text" class="form-control bg-light border-0" name="imponibile" id="cart-subtotal" placeholder="$0.00" readonly />
                                                    </td>
                                                </tr>
                                                {{--<tr>
                                                    <th scope="row">Iva</th>
                                                    <td>
                                                        <div class="d-flex align-items-center" style="width: 200px">
                                                            <input type="text" class="form-control bg-light border-0" name="iva" id="cart-tax" placeholder="€0.00" readonly />
                                                            <select class="form-select ms-2" id="tax-rate-select" name="iva_percentuale" onchange="updateTotals()">
                                                                <!-- Genera dinamicamente le opzioni da 0% a 30% -->
                                                                <script>
                                                                    for (let i = 0; i <= 30; i++) {
                                                                        document.write(`<option value="${i / 100}">${i}%</option>`);
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
                                                            <input type="text" class="form-control bg-light border-0" name="sconto" id="cart-discount" placeholder="€0.00" readonly />
                                                            <select class="form-select ms-2" id="discount-rate-select" name="sconto_percentuale" onchange="updateTotals()">
                                                                <!-- Genera dinamicamente le opzioni da 0% a 100% -->
                                                                <script>
                                                                    for (let i = 0; i <= 100; i++) {
                                                                        document.write(`<option value="${i / 100}">${i}%</option>`);
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
                                                            <input type="text" class="form-control  bg-light border-0" name="costo_trasporto" id="cart-shipping" placeholder="€0.00" readonly />
                                                            <select class="form-select ms-2" id="shipping-rate-select" name="costo_trasporto_percentuale" onchange="updateTotals()">
                                                                <!-- Genera dinamicamente le opzioni da 0% a 50% -->
                                                                <script>
                                                                    for (let i = 0; i <= 50; i++) {
                                                                        document.write(`<option value="${i / 100}">${i}%</option>`);
                                                                    }
                                                                </script>
                                                            </select>
                                                        </div>
                                                    </td>
                                                </tr>--}}

                                                <tr class="border-top border-top-dashed">
                                                    <th scope="row">Totale</th>
                                                    <td>
                                                        <input type="text" style="width: 120px" class="form-control bg-light border-0" name="costo_totale" id="cart-total" placeholder="$0.00" readonly />
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
                                <input type="submit" name="aggiungi_dotes" class="btn btn-success" value="Salva">
                                <a href="javascript:void(0);" class="btn btn-primary"><i class="ri-download-2-line align-bottom me-1"></i> Download Invoice</a>
                                <a href="javascript:void(0);" class="btn btn-danger"><i class="ri-send-plane-fill align-bottom me-1"></i> Send Invoice</a>
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


<script src="https://unpkg.com/onscan.js/onscan.min.js"></script>
<script>

    /*scan del barcode*/


    function updateProductNameField() {
        const select = document.getElementById('productSelect-1');
        const customInput = document.getElementById('customProductName-1');
        const hiddenFieldName = document.getElementById('productNameHidden-1');
        const hiddenFieldId = document.getElementById('productArticoloHidden-1'); // Campo per l'ID articolo

        if (select.value === 'custom') {
            // Mostra l'input per il valore personalizzato
            customInput.style.display = 'block';
            customInput.setAttribute('required', 'required');
            hiddenFieldName.value = customInput.value; // Usa il valore dell'input personalizzato
            hiddenFieldId.value = ''; // Non c'è ID per un valore personalizzato
        } else {
            // Nasconde l'input personalizzato e usa il valore della select
            customInput.style.display = 'none';
            customInput.removeAttribute('required');

            // Estrai l'ID e il nome del prodotto dalla select
            const [productId, productName] = select.value.split(',');

            // Imposta i valori nei campi nascosti
            hiddenFieldName.value = productName; // Imposta il nome del prodotto
            hiddenFieldId.value = productId; // Imposta l'ID del prodotto
        }
    }



    onScan.attachTo(document, {
        onScan: function(sCode) {
            console.log('entra');
            // Attiva il campo input, inserisce il codice e lo disattiva
            var input = document.getElementById('barcodeInput');
            input.value = sCode; // Imposta il valore del codice a barre
            console.log(sCode);
            // Chiama la funzione per eseguire l'operazione AJAX
            handleBarcode(sCode);
        },
        onScanError: function(e) {
            console.error('Errore di scansione:', e);
        },

        keyCodeMapper: function(oEvent) {
            // Mappa correttamente il codice del tasto spazio
            if (oEvent.which === 32) { // Codice 32 rappresenta il tasto "Spazio"
                return ' ';
            }
            // Usa il decoder di default per tutti gli altri caratteri
            return onScan.decodeKeyEvent(oEvent);
        },

    });

    function handleBarcode(barcode) {
        fetch(`{{ url('ajax/getProductByBarcode') }}/${barcode}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateProductDetails(data.product);
                } else {
                    alert('Prodotto non trovato!');
                }
            })
            .catch(error => {
                console.error('Errore AJAX:', error);
            });
    }


    function populateProductDetails(product) {
        let existingEmptyProduct = findEmptyProductRow();

        if (!existingEmptyProduct) {
            new_link(); // Crea una nuova riga vuota se non ne esistono
            existingEmptyProduct = findEmptyProductRow(); // Trova la nuova riga creata
        }

        if (existingEmptyProduct) {
            fillProductRow(existingEmptyProduct, product);
            updateTotals(); // Aggiorna i totali dopo aver inserito i dati del prodotto
        }
    }

    function findEmptyProductRow() {
        // Trova la prima riga di prodotto vuota nel documento
        const products = document.querySelectorAll('.product');
        return Array.from(products).find(row => !row.querySelector('input[name*="[nome_prodotto]"]').value);
    }

    function fillProductRow(row, product) {
        row.querySelector('input[name*="[nome_prodotto]"]').value = product.nome_prodotto;
        row.querySelector('input[name*="[prezzo_unitario]"]').value = product.prezzo_unitario;
        row.querySelector('input[name*="[qta]"]').value = product.qta;
        row.querySelector('input[name*="[prezzo_totale]"]').value = product.prezzo_totale;

        // Chiama updatePrice per la riga specifica se necessario
        // Puoi aggiungere un ID o un altro identificatore unico alla riga per facilitare questo aggiornamento
    }



    /*scan del barcode*/


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
                document.getElementById('sdi').value = result.sdi;
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
                <!-- Select con opzione "Altro..." e input per nome prodotto personalizzato -->
                <select class="form-control bg-light border-0" id="productSelect-${productCount}" data-choices data-choices-search-true onchange="updateProductNameFieldTwo(${productCount})" required>
                    <option value="">Seleziona i tuoi prodotti</option>
                    <?php foreach($prodotti_finiti as $prodotto): ?>
        <option value="<?= $prodotto->id ?>,<?= htmlspecialchars($prodotto->titolo) ?>">
        <?= htmlspecialchars($prodotto->titolo) ?>
        </option>
<?php endforeach; ?>

        <option value="custom">Altro...</option>
    </select>

    <!-- Campo input per nome prodotto personalizzato -->
    <input type="text" class="form-control bg-light border-0 mt-2" id="customProductName-${productCount}" placeholder="Enter custom product name" style="display: none;" oninput="updateHiddenProductName(${productCount})" />

                <!-- Campo nascosto che invia il valore finale al database -->
                <input type="hidden" name="products[${index}][nome_prodotto]" id="productNameHidden-${productCount}" />
                <input type="hidden" name="products[${index}][id_articolo]" id="productArticoloHidden-${productCount}" />

                <div class="invalid-feedback">Please enter a product name</div>
            </div>
                <textarea class="form-control bg-light border-0" name="products[${index}][dettagli_prodotto]" id="productDetails-${productCount}" rows="2" placeholder="Product Details"></textarea>
            </td>
            <td>
                <input type="number" class="form-control product-price bg-light border-0" name="products[${index}][prezzo_unitario]" id="productRate-${productCount}" step="0.01" placeholder="0.00" required oninput="updatePrice(${productCount})" />
                <div class="invalid-feedback">Please enter a rate</div>
            </td>
               <td>
                      <input type="number" style="width: 50px" class="form-control  bg-light border-0" name="products[${index}][iva]" id="productIva-${productCount}" step="0.01" placeholder="0%" required />
               </td>
                <td>
                       <input type="number" style="width: 60px" class="form-control   bg-light border-0" name="products[${index}][lotto]" id="productLotto-${productCount}"  placeholder="Lotto" required />
                 </td>
            <td>
                <div class="input-step">
                    <button type="button" class="minus" onclick="changeQuantity(${productCount}, -1)">–</button>
                    <input type="number" class="product-quantity" name="products[${index}][qta]" id="product-qty-${productCount}" value="0" readonly />
                    <button type="button" class="plus" onclick="changeQuantity(${productCount}, 1)">+</button>
                </div>
            </td>
            <td>
                 <select style="width: 50px" class="form-control" id="unitOfMeasure" name="products[${index}][um]" id="productUm-${productCount}" data-choices data-choices-search-true required>
                     <option value="" disabled selected>UM</option>
                     <option value="KG">KG</option>
                     <option value="M">M</option>
                     <option value="L">L</option>
                 </select>
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

    // Funzione per mostrare/nascondere l'input per nome prodotto personalizzato e aggiornare il campo nascosto
    function updateProductNameFieldTwo(rowId) {
        const select = document.getElementById(`productSelect-${rowId}`);
        const customInput = document.getElementById(`customProductName-${rowId}`);
        const hiddenFieldName = document.getElementById(`productNameHidden-${rowId}`);
        const hiddenFieldId = document.getElementById(`productArticoloHidden-${rowId}`); // Campo per l'ID articolo

        if (select.value === 'custom') {
            customInput.style.display = 'block';
            customInput.setAttribute('required', 'required');
            hiddenFieldName.value = customInput.value; // Imposta il valore del campo personalizzato
            hiddenFieldId.value = ''; // Non c'è ID per un valore personalizzato
        } else {
            customInput.style.display = 'none';
            customInput.removeAttribute('required');

            // Estrai l'ID e il nome del prodotto dalla select
            const [productId, productName] = select.value.split(',');

            // Imposta i valori nei campi nascosti
            hiddenFieldName.value = productName; // Imposta il nome del prodotto
            hiddenFieldId.value = productId; // Imposta l'ID del prodotto
        }
    }


    // Funzione per aggiornare il campo nascosto quando si inserisce un valore personalizzato
    function updateHiddenProductName(rowId) {
        const customInput = document.getElementById(`customProductName-${rowId}`);
        const hiddenField = document.getElementById(`productNameHidden-${rowId}`);
        hiddenField.value = customInput.value;
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
        const iva = parseInt(document.getElementById(`productIva-${id}`).value) || 0;

        // Calcola il prezzo base
        const linePrice = rate * quantity;

        // Aggiungi IVA al prezzo base
        const linePriceWithIva = linePrice * (1 + iva / 100);

        // Visualizza il prezzo con IVA
        document.getElementById(`productPrice-${id}`).value = `€${linePriceWithIva.toFixed(2)}`;

        updateTotals(); // Aggiorna i totali generali
    }


    function updateTotals() {
        let subtotal = 0;

        // Calcola il subtotale
        document.querySelectorAll('.product-line-price').forEach(priceField => {
            const price = parseFloat(priceField.value.replace('€', '')) || 0;
            subtotal += price;
        });

      /*  // Recupera il valore della tassa selezionata
        const taxRate = parseFloat(document.getElementById('tax-rate-select').value) || 0;
        const tax = subtotal * taxRate;

        // Recupera il valore dello sconto selezionato
        const discountRate = parseFloat(document.getElementById('discount-rate-select').value) || 0;
        const discount = subtotal * discountRate;

        // Recupera il valore dello shipping selezionato
        const shippingRate = parseFloat(document.getElementById('shipping-rate-select').value) || 0;
        const shipping = subtotal * shippingRate;
*/
        // Calcola il totale
        /*const total = subtotal + tax - discount + shipping;*/

        const total = subtotal;

        // Aggiorna i campi nella tabella
        document.getElementById('cart-subtotal').value = `€${subtotal.toFixed(2)}`;
   /*     document.getElementById('cart-tax').value = `€${tax.toFixed(2)}`;
        document.getElementById('cart-discount').value = `€${discount.toFixed(2)}`;
        document.getElementById('cart-shipping').value = `€${shipping.toFixed(2)}`;*/
        document.getElementById('cart-total').value = `€${total.toFixed(2)}`;

        // Controllo per verificare se il totale è corretto

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

