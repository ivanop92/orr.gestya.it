@include('default.common.header')
<div class="page-content">
    <div class="container-fluid">
        @php
            // Esegui una query per ottenere le somme delle colonne qta_evadibile_prod e qta_evasa_prod per ogni dotes con tipo_documento 'ord'
            $sommeDorig = DB::table('dorig')
                ->select('id_dotes', DB::raw('SUM(qta_evadibile_prod) as totale_evadibile'), DB::raw('SUM(qta_evasa_prod) as totale_evasa'))
                ->whereIn('id_dotes', $dotes->pluck('id'))
                ->where('id_azienda', $utente->id_azienda)
                ->groupBy('id_dotes')
                ->get()
                ->keyBy('id_dotes'); // Utilizza l'id_dotes come chiave per un accesso rapido
        @endphp
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ $cd_do }}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Documenti</a></li>
                            <li class="breadcrumb-item active">{{ $cd_do }}</li>
                        </ol>
                    </div>
                    {{--<div class="d-flex gap-2">
                        <!-- Pulsante Evadi Tutto -->
                        <button class="btn btn-sm btn-warning" onclick="evadiTuttoInFattura()">Evadi Tutto in Fattura</button>
                    </div>--}}

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card" id="invoiceList">
                    <div class="card-header border-0">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">{{ $cd_do }} Documenti</h5>
                            <div class="flex-shrink-0">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-primary" id="remove-actions" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
                                    <a href="/crea_documento/{{ $cd_do }}" class="btn btn-danger"><i class="ri-add-line align-bottom me-1"></i>Crea Documento</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div>
                            <div class="table-responsive table-card">
                                <table class="table align-middle table-nowrap" id="invoiceTable">
                                    <thead class="text-muted">
                                    <tr>
                                        <th scope="col">CD_CF</th>
                                        <th scope="col">CD_DO</th>
                                        <th scope="col">Cliente</th>
                                        <th scope="col">Data Documento</th>
                                        <th scope="col">Quantità</th>
                                        <th scope="col">Costo Totale</th>
                                        @if($dotes->contains('tipo_documento', 'ord'))
                                            <th scope="col">Quantità Evadibile in Produzione</th>
                                            <th scope="col">Quantità Evasa in Produzione</th>
                                        @endif
                                        <th scope="col">Azioni</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($dotes as $d)
                                        <tr>
                                            <td>{{ $d->cd_cf }}</td>
                                            <td>{{ $d->cd_do }}</td>
                                            <td>{{ $d->ragione_sociale }}</td>
                                            <td>{{ $d->data_doc }}</td>
                                            <td>{{ $d->qta_totale }}</td>
                                            <td>{{ number_format($d->costo_totale, 2, ',', '.') }} €</td>
                                            @if($d->tipo_documento === 'ord')
                                                <td>{{ $sommeDorig[$d->id]->totale_evadibile ?? 0 }}</td>
                                                <td>{{ $sommeDorig[$d->id]->totale_evasa ?? 0 }}</td>
                                            @endif
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="/dettaglio_documento/{{ $d->id }}" class="btn btn-sm btn-success"><i class="ri-information-line"></i></a>
                                                    <a href="/modifica_documento/{{ $d->id }}" class="btn btn-sm btn-primary">Modifica</a>
                                                    <a href="#" class="btn btn-sm btn-danger" onclick="setOrderIdToDelete({{ $d->id }})">Delete</a>
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="evadiDocumento({{ $d->id }})">Evadi</button>
                                                    <button class="btn btn-sm btn-info" onclick="toggleCorrelati({{ $d->id }})">Documenti Correlati</button>
                                                </div>
                                                <div class="documenti-correlati mt-2" id="documenti_correlati_{{ $d->id }}" style="display:none;">
                                                    @php
                                                        $correlati = $documenti_correlati->filter(function($doc) use ($d) {
                                                            return $doc->id_dotes_evade == $d->id;
                                                        });
                                                    @endphp

                                                    @if($correlati->isNotEmpty())
                                                        <select class="form-select" onchange="apriDettaglioDocumento(this.value)">
                                                            <option>Seleziona documento correlato</option>
                                                            @foreach($correlati as $correlato)
                                                                <option value="{{ $correlato->id }}">{{ $correlato->cd_do }} - {{ $correlato->numero_doc }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <p class="text-muted">Nessun documento correlato trovato</p>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="noresult" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sorry! No Result Found</h5>
                                        <p class="text-muted mb-0">We've searched more than 150+ documents and did not find any results for your search.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal for delete confirmation -->
                        <div class="modal fade" id="deleteOrder" tabindex="-1" aria-labelledby="deleteOrderLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST">
                                        @csrf
                                        <input type="hidden" name="id_ordine" value="" id="id_ordine">
                                        <div class="modal-body p-5 text-center">
                                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
                                            <div class="mt-4 text-center">
                                                <h4>Sei sicuro di eliminare questo documento?</h4>
                                                <p class="text-muted fs-15 mb-4">Cancellando questo documento verranno cancellati tutti i suoi dati a database.</p>
                                                <div class="hstack gap-2 justify-content-center remove">
                                                    <button type="button" class="btn btn-link link-success fw-medium text-decoration-none" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Chiudi</button>
                                                    <button type="submit" class="btn btn-danger" name="elimina" value="1">Sì, Elimina</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!--end modal -->
                    </div>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->

    </div><!-- container-fluid -->
    <div id="ajax_loader"></div>
</div>
@include('default.common.footer')

<script>
    function setOrderIdToDelete(orderId) {
        $('#deleteOrder').modal('show');
        document.getElementById('id_ordine').value = orderId;
    }


    function evadiDocumento(id) {
        let url = `{{ url('evadi_documento') }}/${id}`;
        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                $('#ajax_loader').html(data); // Popola il loader con il contenuto della risposta
                $('#modal_evadi_' + id).modal('show'); // Mostra la modale
                console.log('Modale aperta');
            },
            error: function (xhr, status, error) {
                console.error('Errore durante la richiesta AJAX:', error); // Mostra l'errore in console
            }
        });
    }
    function toggleCorrelati(id) {
        const correlatiDiv = document.getElementById('documenti_correlati_' + id);

        if (correlatiDiv.style.display === "none" || correlatiDiv.style.display === "") {
            correlatiDiv.style.display = "block";
        } else {
            correlatiDiv.style.display = "none";
        }
    }

    function apriDettaglioDocumento(id) {
        window.location.href = '/dettaglio_documento/' + id;
        console.log(id)
    }

    


</script>

<style>
    .documenti-correlati {
        transition: all 0.3s ease-in-out;
    }

    .documenti-correlati select {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border-radius: 5px;
    }

    .documenti-correlati p {
        margin: 0;
        padding: 10px;
        color: #6c757d;
    }

</style>

