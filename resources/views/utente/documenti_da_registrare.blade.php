@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Documenti Da Registrare</h4>
                    <div class="page-title-right">
                        <button type="button" class="btn btn-primary waves-effect waves-light" onclick="importaXML()">
                            <i class="ri-upload-cloud-2-line align-bottom me-1"></i> Importa XML
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- XML Viewer Column -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body" id="vista_fattura">

                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <h5 class="card-title flex-grow-1 mb-0">Seleziona una Riga per vedere l'XML</h5>
                                    <div class="flex-shrink-0">
                                        <button type="button" class="btn btn-ghost-primary btn-icon btn-sm" onclick="expandXML()">
                                            <i class="ri-external-link-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <iframe id="xml-viewer" style="width: 100%; height: 800px; border: none;"></iframe>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Documents List Column -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Elenco Documenti</h5>
                    </div>
                    <div class="card-body">
                        <table id="tabella-documenti" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Numero</th>
                                <th>Data</th>
                                <th>Fornitore</th>
                                <th>Totale</th>
                                <th>Stato</th>
                                <th style="width:150px;">Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($documenti as $doc)
                                <tr onclick="visualizzaXML(<?php echo $doc->id ?>)">
                                    <td>{{ $doc->cd_do }}</td>
                                    <td>{{ $doc->numero_doc }}</td>
                                    <td>{{ date('d/m/Y', strtotime($doc->data_doc)) }}</td>
                                    <td>{{ $doc->cliente_ragione_sociale ?? ($doc->cliente_nome . ' ' . $doc->cliente_cognome) }}</td>
                                    <td class="text-end">{{ number_format($doc->totale, 2, ',', '.') }} €</td>
                                    <td>
                                        @if($doc->da_registrare)
                                            <span class="badge badge-soft-warning">Da Registrare</span>
                                        @else
                                            <span class="badge badge-soft-success">Da Registrare</span>
                                        @endif
                                    </td>
                                    <td>

                                        <form method="post" onsubmit="return confirm('Vuoi Registrare questa fattura ?')">
                                            <input type="hidden" name="id" value="<?php echo $doc->id ?>">
                                            <button style="margin-left:5px;float:left;" type="submit" name="registra" value="Registra" class="btn btn-sm btn-success">Registra</button>
                                        </form>

                                        <a style="float:left;margin-left:5px;" onclick="visualizzaXML(<?php echo $doc->id ?>)" target="_blank" class="btn btn-sm btn-primary">XML</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('utente.common.footer')

<script type="text/javascript">

    $(document).ready(function() {
        // Manteniamo una variabile per l'ID della riga selezionata
        let selectedRowId = null;

        // Inizializza DataTable
        const table = $('#tabella-documenti').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json'
            },
            order: [[2, 'desc']], // Ordina per data decrescente
            responsive: true,
            columnDefs: [
                { orderable: false, targets: -1 } // Disabilita ordinamento ultima colonna
            ],
            // Dopo ogni redraw della tabella, riapplica la selezione
            drawCallback: function() {
                if (selectedRowId) {
                    const row = table.rows().nodes().toArray()
                        .find(tr => $(tr).find('[onclick*="visualizzaXML"]')
                            .attr('onclick')
                            .match(/visualizzaXML\((\d+)\)/)[1] === selectedRowId);

                    if (row) {
                        $(row).addClass('selected');
                    }
                }
            }
        });

        // Click handler per le righe
        $('#tabella-documenti tbody').on('click', 'tr', function(e) {
            // Ignora il click se è stato fatto sui pulsanti o sul dropdown
            if (!$(e.target).closest('button, .dropdown-menu').length) {
                // Ottieni l'ID dalla riga
                const id = $(this).find('[onclick*="visualizzaXML"]')
                    .attr('onclick')
                    .match(/visualizzaXML\((\d+)\)/)[1];

                if (selectedRowId === id) {
                    // Se la riga è già selezionata, deselezionala
                    selectedRowId = null;
                    $(this).removeClass('selected');
                    // Resetta la visualizzazione XML
                    resetXMLViewer();
                } else {
                    // Seleziona la nuova riga
                    selectedRowId = id;
                    table.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                    // Visualizza l'XML
                    visualizzaXML(id);
                }
            }
        });
    });

    // Funzione per resettare il visualizzatore XML
    function resetXMLViewer() {
        $('#vista_fattura').html(`
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Anteprima XML</h4>
            </div>
            <div class="card-body">
                <div class="text-center text-muted">
                    <i class="ri-file-text-line" style="font-size: 48px;"></i>
                    <p class="mt-2">Seleziona un documento dalla lista per visualizzare l'XML</p>
                </div>
            </div>
        </div>
    `);
    }

    // Stili CSS
    const style = `
    <style>
        #tabella-documenti tbody tr.selected {
            background-color: #e2e6ea !important;
        }
        #tabella-documenti tbody tr {
            cursor: pointer;
        }
        #tabella-documenti tbody tr:hover:not(.selected) {
            background-color: #f5f5f5;
        }
    </style>
`;
    $('head').append(style);

    function registraDocumento(id) {

        $('#modalRegistrazione').modal('show');
    }

    function visualizzaXML(id) {
        // Se il div vista_fattura è vuoto, creiamo la struttura della card
            $('#vista_fattura').html(`
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title flex-grow-1 mb-0">Visualizzazione Fattura</h5>
                        <div class="flex-shrink-0">
                            <button type="button" class="btn btn-ghost-primary btn-icon btn-sm" onclick="expandXML()">
                                <i class="ri-external-link-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <iframe id="xml-viewer" style="width: 100%; height: 800px; border: none;"></iframe>
                </div>
            </div>
        `);

        // Aggiorniamo la src dell'iframe esistente
        $('#xml-viewer').attr('src', '/utente/visualizza_xml_da_file/' + id);
    }

    function expandXML() {
        const iframeSrc = $('#xml-viewer').attr('src');
        if (iframeSrc) {
            window.open(iframeSrc, '_blank');
        }
    }

    /*
    function visualizzaXML(id) {

        $.ajax({
            url: "<?php echo URL::asset('utente/visualizza_xml_da_file') ?>/"+id,
            type:'GET',
            success: function(result){
                $('#vista_fattura').html(result);
            }
        });

    }

    */

</script>

@push('css')
    <!-- Highlight.js CSS -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/default.min.css">
    <style>
        .xml-content {
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 13px;
            line-height: 1.5;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            max-height: calc(100vh - 250px);
            overflow-y: auto;
        }

        .xml-content .hljs-tag { color: #000080; }
        .xml-content .hljs-name { color: #0000FF; }
        .xml-content .hljs-string { color: #008000; }
        .xml-content .hljs-attr { color: #FF0000; }

        #tabella-documenti tbody tr.selected {
            background-color: #e2e6ea !important;
        }

        .table-hover tbody tr:hover {
            cursor: pointer;
            background-color: rgba(0,0,0,.075);
        }
    </style>
@endpush

@push('scripts')
    <!-- Highlight.js -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/languages/xml.min.js"></script>

