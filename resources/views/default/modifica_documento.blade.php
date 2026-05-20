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
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri-check-line align-middle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ri-error-warning-line align-middle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <form enctype="multipart/form-data" method="post" id="form_modifica_documento" novalidate>
            @csrf
            <div class="row justify-content-center">
                <div class="col-xxl-9">
                    <div class="card">
                        <div class="needs-validation" novalidate id="invoice_form">
                            <div class="card-body border-bottom border-bottom-dashed p-4">
                                <div class="row">

                                    <select class="form-control mb-2" name="cd_cf" id="selectCliente" data-choices data-choices-search-true onchange="compilaCampi(this.value)">
                                        <option value="0" disabled selected>-- Seleziona un Cliente --</option>

                                        <?php foreach($clienti as $c) {?>
                                        <option @if($clienteDotes->cd_cf === $c->cd_cf) selected @endif value="{{ $c->cd_cf }}">{{ $c->ragione_sociale }}</option>
                                        <?php } ?>
                                    </select>

                                    <div class="mb-3">
                                        <label class="form-label">Commessa</label>
                                        <select name="id_commessa" id="id_commessa" class="form-control mb-2" data-choices data-choices-search-true>
                                            <option value="">Seleziona Commessa (opzionale)</option>
                                            @php
                                            $commesse = DB::table('commesse')
                                                ->where('id_azienda', $utente->id_azienda)
                                                ->whereIn('stato', ['aperta', 'in_corso'])
                                                ->orderBy('codice_commessa', 'asc')
                                                ->get();
                                            @endphp
                                            @foreach($commesse as $commessa)
                                                <option value="{{ $commessa->id }}" <?php if(isset($dotes) && $dotes->id_commessa == $commessa->id) echo 'selected'; ?>>{{ $commessa->codice_commessa }} - {{ $commessa->descrizione }}</option>
                                            @endforeach
                                        </select>
                                    </div>

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
                                        <div class="mb-2">
                                            <label class="form-label">CIG</label>
                                            <input type="text" class="form-control bg-light border-0" name="cig" value="{{ $dotes->cig ?? '' }}" placeholder="Codice CIG" />
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">CUP</label>
                                            <input type="text" class="form-control bg-light border-0" name="cup" value="{{ $dotes->cup ?? '' }}" placeholder="Codice CUP" />
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
                                                Dettaglio Prodotti
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
                                            <th scope="col">Iva</th>
                                            <th >Lotto</th>
                                            <th>Quantità</th>
                                            <th>UM</th>
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
                                                    <select class="form-control bg-light border-0" id="productSelect-{{ $index + 1 }}" data-choices data-choices-search-true onchange="updateProductNameField({{ $index + 1 }})" required>
                                                        <option value="">Seleziona un Prodotto</option>
                                                        @foreach($prodotti_finiti as $prodotto)
                                                            <option value="{{ $prodotto->id }},{{ $prodotto->titolo }}" {{ $d->id_articolo == $prodotto->id ? 'selected' : '' }}>
                                                                {{ $prodotto->titolo }}
                                                            </option>
                                                        @endforeach
                                                        <option value="custom" {{ !in_array($d->nome_prodotto, $prodotti_finiti->pluck('titolo')->toArray()) ? 'selected' : '' }}>Altro...</option>
                                                    </select>


                                                    <!-- Input per nome prodotto personalizzato -->
                                                    <input type="text" class="form-control bg-light border-0 mt-2" name="products[{{ $index }}][nome_prodotto]" id="customProductName-{{ $index + 1 }}" placeholder="Nome Prodotto" style="display: {{ !in_array($d->nome_prodotto, $prodotti_finiti->pluck('titolo')->toArray()) ? 'block' : 'none' }};" value="{{ !in_array($d->nome_prodotto, $prodotti_finiti->pluck('titolo')->toArray()) ? $d->nome_prodotto : '' }}" oninput="updateHiddenProductName({{ $index + 1 }})" />

                                                    <!-- Campo nascosto per l'id_articolo -->
                                                    <input type="hidden" name="products[{{ $index }}][id_articolo]" id="productArticoloHidden-{{ $index + 1 }}" value="{{ $d->id_articolo }}" />
                                                    <!-- Campo nascosto per inviare il nome prodotto finale -->
                                                    <input type="hidden" name="products[{{ $index }}][nome_prodotto]" id="productNameHidden-{{ $index + 1 }}" value="{{ $d->nome_prodotto }}" />


                                                </div>
                                                <textarea class="form-control bg-light border-0"  name="products[{{ $index }}][dettagli_prodotto]" id="productDetails-{{ $index + 1 }}" rows="2" placeholder="Product Details"> {{ $d->dettagli_prodotto }} </textarea>
                                            </td>
                                            <td>
                                                <input type="text" inputmode="decimal" class="form-control product-price bg-light border-0 num-decimal" value="{{ $d->prezzo_unitario }}" name="products[{{ $index }}][prezzo_unitario]" id="productRate-{{ $index + 1 }}" placeholder="0,00" required />
                                                <div class="invalid-feedback">
                                                    Please enter a rate
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" inputmode="decimal" style="width: 50px" class="form-control bg-light border-0 num-decimal" value="{{ $d->iva }}" name="products[{{ $index }}][iva]" id="productIva-1" placeholder="0" required />
                                            </td>
                                            <td>
                                                <input type="text" style="width: 60px" class="form-control bg-light border-0" value="{{ $d->lotto }}" name="products[{{ $index }}][lotto]" id="productLotto-1" placeholder="Lotto" />
                                            </td>
                                            <td>
                                                <div class="input-step">
                                                    <button type="button" class="minus">–</button>
                                                    <input type="text" inputmode="decimal" class="form-control product-quantity num-decimal" value="{{ $d->qta }}" name="products[{{ $index }}][qta]" id="product-qty-{{ $index + 1 }}" />
                                                    <button type="button" class="plus">+</button>
                                                </div>
                                            </td>
                                            <td>
                                                <select style="width: 50px" class="form-control" id="unitOfMeasure" name="products[{{ $index }}][um]" data-choices data-choices-search-true required>
                                                    <option value="" disabled {{ !$d->um ? 'selected' : '' }}>UM</option>
                                                    <option value="KG" {{ $d->um == 'KG' ? 'selected' : '' }}>KG</option>
                                                    <option value="M" {{ $d->um == 'M' ? 'selected' : '' }}>M</option>
                                                    <option value="L" {{ $d->um == 'L' ? 'selected' : '' }}>L</option>
                                                </select>
                                            </td>
                                            <td class="text-end">
                                                <div>
                                                    <input type="text" style="width: 120px" class="form-control bg-light border-0 product-line-price" value="{{ $d->prezzo_totale }}" name="products[{{ $index }}][prezzo_totale]" id="productPrice-{{ $index + 1 }}" placeholder="$0.00" readonly />
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
                                                            <input style="width: 120px" type="text" class="form-control bg-light border-0" id="cart-subtotal" name="imponibile" value="{{$dotes->imponibile}}" placeholder="$0.00" readonly />
                                                        </td>
                                                    </tr>
                                                    {{--<tr>
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
                                                    </tr>--}}

                                                    <tr class="border-top border-top-dashed">
                                                        <th scope="row">Totale</th>
                                                        <td>
                                                            <input style="width: 120px" type="text" class="form-control bg-light border-0" name="costo_totale" id="cart-total" value="{{ $dotes->costo_totale }}" placeholder="$0.00" readonly />
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
                                    <button type="submit" name="modifica_dotes" value="1" class="btn btn-success"><i class="ri-save-line me-1"></i> Salva</button>
                                   {{-- <a href="javascript:void(0);" class="btn btn-primary"><i class="ri-download-2-line align-bottom me-1"></i> Download Invoice</a>
                                    <a href="javascript:void(0);" class="btn btn-danger"><i class="ri-send-plane-fill align-bottom me-1"></i> Send Invoice</a>--}}
                                </div>
                            </div>
                        </div>
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

    // Sostituisce la virgola con il punto in tutti i campi decimali prima del submit.
    // Permette agli utenti italiani di scrivere "0,30" o "1,5" senza errori HTML5.
    document.addEventListener('DOMContentLoaded', function(){
        var f = document.getElementById('form_modifica_documento');
        if (!f) return;
        f.addEventListener('submit', function(){
            f.querySelectorAll('.num-decimal').forEach(function(el){
                if (typeof el.value === 'string') {
                    el.value = el.value.replace(',', '.').trim();
                }
            });
        });
    });


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
                    <select class="form-control bg-light border-0" id="productSelect-${productCount}" data-choices data-choices-search-true onchange="updateProductNameField(${productCount})" required>
                <option value="">Seleziona un Prodotto</option>
                 @foreach($prodotti_finiti as $prodotto)
        <option value="{{ $prodotto->id }},{{ $prodotto->titolo }}">{{ $prodotto->titolo }}</option>
                        @endforeach
        <option value="custom">Altro...</option>
    </select>
    <input type="text" class="form-control bg-light border-0 mt-2" name="products[${index}][nome_prodotto]" id="customProductName-${productCount}" placeholder="Nome Prodotto" style="display: none;" oninput="updateHiddenProductName(${productCount})" />
            <input type="hidden" name="products[${index}][nome_prodotto]" id="productNameHidden-${productCount}" />
            <!-- Campo nascosto per l'id_articolo -->
                    <input type="hidden" name="products[${index}][id_articolo]" id="productArticoloHidden-${productCount}" />
                </div>
                <textarea class="form-control bg-light border-0" name="products[${index}][dettagli_prodotto]" id="productDetails-${productCount}" rows="2" placeholder="Product Details"></textarea>
            </td>
            <td>
                <input type="text" inputmode="decimal" class="form-control product-price bg-light border-0 num-decimal" name="products[${index}][prezzo_unitario]" id="productRate-${productCount}" placeholder="0,00" required oninput="updatePrice(${productCount})" />
                <div class="invalid-feedback">Please enter a rate</div>
            </td>
 <td>
                      <input type="text" inputmode="decimal" style="width: 50px" class="form-control bg-light border-0 num-decimal" name="products[${index}][iva]" id="productIva-${productCount}" placeholder="0" required />
               </td>
                <td>
                       <input type="text" style="width: 60px" class="form-control bg-light border-0" name="products[${index}][lotto]" id="productLotto-${productCount}" placeholder="Lotto" />
                 </td>
            <td>
                <div class="input-step">
                    <button type="button" class="minus" onclick="changeQuantity(${productCount}, -1)">–</button>
                    <input type="text" inputmode="decimal" class="product-quantity num-decimal" name="products[${index}][qta]" id="product-qty-${productCount}" value="0" />
                    <button type="button" class="plus" onclick="changeQuantity(${productCount}, 1)">+</button>
                </div>
            </td>
            <td>
                 <select style="width: 50px" class="form-control" id="unitOfMeasure" name="products[${index}][um]" data-choices data-choices-search-true required>
                    <option value="" disabled {{ !$d->um ? 'selected' : '' }}>UM</option>
                    <option value="KG" {{ $d->um == 'KG' ? 'selected' : '' }}>KG</option>
                    <option value="M" {{ $d->um == 'M' ? 'selected' : '' }}>M</option>
                    <option value="L" {{ $d->um == 'L' ? 'selected' : '' }}>L</option>
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

    function updateProductNameField(rowId) {
        const select = document.getElementById(`productSelect-${rowId}`);
        const customInput = document.getElementById(`customProductName-${rowId}`);
        const hiddenFieldName = document.getElementById(`productNameHidden-${rowId}`);
        const hiddenFieldId = document.getElementById(`productArticoloHidden-${rowId}`);

        if (select.value === 'custom') {
            customInput.style.display = 'block';
            customInput.setAttribute('required', 'required');
            hiddenFieldName.value = customInput.value;
            hiddenFieldId.value = ''; // ID non disponibile per input personalizzati
        } else {
            customInput.style.display = 'none';
            customInput.removeAttribute('required');

            // Estrai l'ID e il nome del prodotto dalla select (formato "id,titolo")
            const [productId, productName] = select.value.split(',');

            // Imposta i valori nei campi nascosti
            hiddenFieldName.value = productName;
            hiddenFieldId.value = productId;
        }
    }

    function updateHiddenProductName(rowId) {
        const customInput = document.getElementById(`customProductName-${rowId}`);
        const hiddenFieldName = document.getElementById(`productNameHidden-${rowId}`);
        hiddenFieldName.value = customInput.value;
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

