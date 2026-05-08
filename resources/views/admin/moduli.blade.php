@include('admin.common.header')

<div class="page-content">
    <div class="container-fluid">


        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Moduli</h4>

                    <div class="page-title-right">
                        <!--
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">CRM</a></li>
                            <li class="breadcrumb-item active">Contacts</li>
                        </ol>-->
                    </div>

                </div>
            </div>
        </div>

        <!-- Pulsante per aggiungere un nuovo modulo -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modal_aggiungi_modulo">Aggiungi Modulo</button>

        <!-- Tabella dei moduli -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <table class="table datatable w-100">
                <thead>
                <tr>
                    <th>ID Modulo</th>
                    <th>Nome</th>
                    <th>Descrizione</th>
                    <th>Aggiungi/Rimuovi Aziende</th>
                    <th>Azioni</th>
                </tr>
                </thead>
                <tbody style="height: 100px">
                @foreach ($moduli as $modulo)
                    <tr>
                        <td>{{ $modulo->id }}</td>
                        <td>{{ $modulo->nome }}</td>
                        <td>{{ $modulo->descrizione }}</td>
                        <td>
                            <select data-choices data-choices-removeItem multiple class="form-control js-choices" id="select-aziende-{{ $modulo->id }}" data-modulo-id="{{ $modulo->id }}">
                                @foreach ($aziende as $azienda)
                                    <option value="{{ $azienda->id }}"
                                            @if (in_array($azienda->id, explode(',', $modulo->azienda_id))) selected @endif>
                                        {{ $azienda->ragione_sociale }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="d-flex justify-content-between">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" onclick="openEditModal({{ $modulo->id }}, '{{ $modulo->nome }}', '{{ $modulo->descrizione }}')">Modifica</button>
                            <form method="post" onsubmit="return confirm('Vuoi Eliminare questo Modulo? ?')">
                                <input type="hidden" name="id_modulo" value="<?php echo $modulo->id ?>">
                                <button style="margin-left:5px;" name="elimina" value="Elimina" type="submit" class="btn btn-sm btn-danger"><i class="ri-delete-bin-2-line"></i></button>
                            </form>
                        </td>
                    </tr>


                @endforeach
                </tbody>
            </table>
                </div>
            </div>
        </div>

        <!-- Modal per aggiungere un nuovo modulo -->
        <div class="modal fade" id="modal_aggiungi_modulo" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-soft-info p-3">
                        <h5 class="modal-title">Nuovo Modulo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nome_modulo" class="form-label">Nome Modulo <b style="color:red">*</b></label>
                                <input type="text" id="nome_modulo" name="nome" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="descrizione_modulo" class="form-label">Descrizione</label>
                                <textarea id="descrizione_modulo" name="descrizione" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                            <input  type="submit" class="btn btn-success" value="Crea Modulo" name="aggiungi">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @foreach ($moduli as $modulo)

        <!-- Modal per modificare un modulo -->

            <div class="modal fade" id="modal_modifica_modulo_{{ $modulo->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-soft-info p-3">
                            <h5 class="modal-title">Modifica Modulo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST">
                            @csrf
                            <input type="hidden" id="edit_modulo_id_{{ $modulo->id }}" name="id_modulo" value="{{ $modulo->id }}">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="edit_nome_modulo_{{ $modulo->id }}" class="form-label">Nome Modulo <b style="color:red">*</b></label>
                                    <input type="text" id="edit_nome_modulo_{{ $modulo->id }}" name="nome" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_descrizione_modulo_{{ $modulo->id }}" class="form-label">Descrizione</label>
                                    <textarea id="edit_descrizione_modulo_{{ $modulo->id }}" name="descrizione" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="id_modulo" value="{{ $modulo->id }}">

                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                                <input  name="modifica" value="Salva Modifiche" type="submit" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        @endforeach

    </div>
</div>
<style>
    .choices__list--dropdown {
        position: absolute !important;
        z-index: 1050; /* Assicura che stia sopra altri elementi */
        max-height: 200px; /* Limita l'altezza */
        overflow-y: auto; /* Aggiunge lo scroll */
        width: auto; /* Evita di espandere il td */
    }

</style>
<!-- CSS di Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<!-- JS di Choices.js -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const elements = document.querySelectorAll('.js-choices');
        elements.forEach(function (element) {
            const moduloId = element.getAttribute('data-modulo-id');
            const choices = new Choices(element, {
                removeItemButton: true,
                searchPlaceholderValue: 'Cerca aziende...',
                position: 'auto', // Consente alla dropdown di posizionarsi sopra o sotto automaticamente
                shouldSort: false, // Mantiene l'ordine originale delle opzioni
                searchEnabled: true, // Abilita la ricerca
                maxItemCount: -1, // Permette selezioni multiple senza limite
            });

            // Aggiungi evento per aggiornare il database al cambiamento
            element.addEventListener('change', () => aggiornaAziende(moduloId));
        });
    });


    function aggiornaAziende(moduloId) {
        const aziendeSelect = document.getElementById(`select-aziende-${moduloId}`);
        const aziendeIds = Array.from(aziendeSelect.selectedOptions).map(option => option.value);

        // Costruisci l'URL con i parametri per aggiornare le aziende
        const url = `{{ url('api/moduli/aggiungi-aziende') }}?modulo_id=${moduloId}&aziende=${aziendeIds.join(',')}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Aziende aggiornate con successo!');
                } else {
                    alert('Errore durante l\'aggiornamento delle aziende.');
                }
            })
            .catch(error => console.error('Errore:', error));
    }

    function openEditModal(id, nome, descrizione) {
        // Imposta i valori nei campi della modale con ID specifico
        document.getElementById(`edit_modulo_id_${id}`).value = id;
        document.getElementById(`edit_nome_modulo_${id}`).value = nome;
        document.getElementById(`edit_descrizione_modulo_${id}`).value = descrizione;

        // Mostra la modale specifica
        const modalId = `#modal_modifica_modulo_${id}`;
        $(modalId).modal('show');
    }

</script>

@include('admin.common.footer')
