@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Invoice Details</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Invoices</a></li>
                            <li class="breadcrumb-item active">Invoice Details</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="page-title-right">
            <a href="{{ url('documenti/{ciclo}' . $dotes->cd_do) }}" class="btn btn-primary">Torna a Documenti</a>
        </div>
        <div class="row justify-content-center">
            <div class="col-xxl-9">
                <div class="card" id="demo">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-header border-bottom-dashed p-4">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <img src="assets/images/logo-dark.png" class="card-logo card-logo-dark" alt="logo dark" height="17">
                                        <img src="assets/images/logo-light.png" class="card-logo card-logo-light" alt="logo light" height="17">
                                        <div class="mt-sm-5 mt-4">
                                            <h6 class="text-muted text-uppercase fw-semibold">Address</h6>
                                            <p class="text-muted mb-1" id="address-details">{{ $dotes->indirizzo }}</p>
                                            <p class="text-muted mb-0" id="zip-code"><span>Comune:</span> {{ $dotes->comune }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end card-header-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Numero Documento</p>
                                        <h5 class="fs-14 mb-0">{{ $dotes->numero_doc }}</h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Data Documento</p>
                                        <h5 class="fs-14 mb-0">{{ $dotes->data_doc }}</h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Totale</p>
                                        <h5 class="fs-14 mb-0">€ {{ number_format($dotes->costo_totale, 2) }}</h5>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4 border-top border-top-dashed">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Indirizzo di Fatturazione</h6>
                                        <p class="text-muted mb-1">{{ $dotes->indirizzo_fatturazione }}</p>
                                        <p class="text-muted mb-0">{{ $dotes->comune_fatturazione }}</p>
                                    </div>
                                    <!--end col-->
                                    <div class="col-6">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Indirizzo di Consegna</h6>
                                        <p class="text-muted mb-1">{{ $dotes->indirizzo_consegna }}</p>
                                        <p class="text-muted mb-0">{{ $dotes->comune_consegna }}</p>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                        <thead>
                                        <tr class="table-active">
                                            <th scope="col" style="width: 50px;">#</th>
                                            <th scope="col">Nome del Prodotto</th>
                                            <th scope="col">Quantità</th>
                                            <th scope="col">Prezzo</th>
                                            <th scope="col">Barcode</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($prodotti as $index => $prodotto)
                                            <tr>
                                                <th scope="row">{{ $index + 1 }}</th>
                                                <td>{{ $prodotto->nome_prodotto }}</td>
                                                <td>{{ $prodotto->qta }}</td>
                                                <td>€ {{ number_format($prodotto->prezzo_totale, 2) }}</td>
                                                <td>
                                                    <!-- Visualizza il barcode e cliccandoci apre l'immagine in una nuova scheda -->
                                                    <a href="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($prodotto->barcode) }}&code=Code128&translate-esc=on" target="_blank">
                                                        <img style="width: 70%; height: 80%" alt="Barcode" src="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($prodotto->barcode) }}&code=Code128&translate-esc=on"/>
                                                    </a>
                                                    <!-- Aggiungi il pulsante per stampare solo il barcode -->
                                                    <button onclick="printBarcode('{{ urlencode($prodotto->barcode) }}')" class="btn btn-success print-hidden">
                                                        <i class="ri-printer-line"></i>
                                                    </button>
                                                </td>

                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>




                                </div>
                                <div class="border-top border-top-dashed mt-2">
                                    <table class="table table-borderless table-nowrap align-middle mb-0 ms-auto" style="width:250px">
                                        <tbody>
                                        <tr>
                                            <td>Sub Total</td>
                                            <td class="text-end">€ {{ number_format($dotes->costo_totale, 2) }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <!--end table-->
                                </div>
                                <div class="mt-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Dettagli di Pagamento:</h6>
                                    <p class="text-muted">Prezzo Totale: <span class="fw-medium">€ {{ number_format($dotes->costo_totale, 2) }}</span></p>
                                </div>
                                <div class="mt-4">
                                    <div class="alert alert-info">
                                        <p class="mb-0"><span class="fw-semibold">NOTES:</span>
                                            <span id="note">All accounts are to be paid within 7 days from receipt of invoice. To be paid by cheque or
                                                credit card or direct payment online. If account is not paid within 7
                                                days the credits details supplied as confirmation of work undertaken
                                                will be charged the agreed quoted fee noted above.
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                                    <a href="javascript:window.print()" class="btn btn-success"><i class="ri-printer-line align-bottom me-1"></i> Print</a>
                                    <button name="scarica_pdf_ordine" class="btn btn-primary"><i class="ri-download-2-line align-bottom me-1"></i> Download</button>
                                </div>
                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                    </div><!--end row-->
                </div>
                <!--end card-->
            </div>
            <!--end col-->
        </div>
        <!--end row-->

    </div><!-- container-fluid -->
</div><!-- End Page-content -->
@include('default.common.footer')

<style>
    /* Nascondi gli elementi con la classe print-hidden durante la stampa */
    @media print {
        .print-hidden {
            display: none !important; /* Nasconde gli elementi */
        }
    }
</style>


<script>
    function printBarcode(barcodeData) {
        // URL per il barcode
        const url = `https://barcode.tec-it.com/barcode.ashx?data=${barcodeData}&code=Code128&translate-esc=on`;

        // Apri una nuova finestra con il barcode
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Stampa Barcode</title>
                </head>
                <body>
                    <img src="${url}" alt="Barcode" />
                </body>
            </html>
        `);

        // Attendi che l'immagine venga caricata e poi avvia la stampa
        printWindow.document.close(); // Chiude il documento per il rendering
        printWindow.focus(); // Imposta la finestra come attiva

        // Stampa il contenuto della finestra
        printWindow.onload = function() {
            printWindow.print();
            printWindow.onafterprint = function() {
                printWindow.close(); // Chiudi la finestra dopo la stampa
            };
        };
    }


</script>