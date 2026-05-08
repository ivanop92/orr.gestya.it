@include('default.common.header')
<div class="page-content">
    <div class="container-fluid">
        <h2>Gestione Fasi</h2>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST">
            <div class="mb-3">
                <label for="descrizione" class="form-label">Descrizione</label>
                <input type="text" class="form-control" id="descrizione" name="descrizione" required>
            </div>
            <input type="submit" name="crea_fase" class="btn btn-primary" value="Crea Fase">
        </form>

        <table class="table mt-4">
            <thead>
            <tr>
                <th>ID</th>
                <th>Descrizione</th>
                <th>Ordinamento</th>
                <th>Azioni</th>
            </tr>
            </thead>
            <tbody>
            @foreach($fasi as $fase)
                <tr>
                    <td>{{ $fase->id }}</td>
                    <td>{{ $fase->descrizione }}</td>
                    <td style="width: 10%">
                        <input type="number" class="form-control"
                               value="{{ $fase->ordinamento }}"
                               onchange="updateOrdine({{ $fase->id }}, this.value)">
                    </td> <!-- Campo di input per l'ordine -->
                    <td>
                        <!-- Pulsante Modifica -->
                        <button type="button" class="btn btn-sm btn-primary" onclick="openEditModal({{ $fase->id }}, '{{ $fase->descrizione }}')">Modifica</button>

                        <!-- Pulsante Delete -->
                        <a href="#" class="btn btn-sm btn-danger" onclick="setFaseIdToDelete({{ $fase->id }})">Delete</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal for delete confirmation -->
    <div class="modal fade" id="deleteFase" tabindex="-1" aria-labelledby="deleteOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    @csrf
                    <input type="hidden" name="id_fase" value="" id="id_fase">
                    <div class="modal-body p-5 text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
                        <div class="mt-4 text-center">
                            <h4>Sei sicuro di eliminare questa Fase di Lavorazione?</h4>
                            <p class="text-muted fs-15 mb-4">Cancellando questa fase verranno cancellati tutti i suoi dati a database.</p>
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

    <!-- Modal for editing fase -->
    <div class="modal fade" id="editFaseModal" tabindex="-1" aria-labelledby="editFaseLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    @csrf
                    <input type="hidden" name="id_fase" value="" id="edit_id_fase">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFaseLabel">Modifica Fase di Lavorazione</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_descrizione" class="form-label">Descrizione</label>
                            <input type="text" class="form-control" id="edit_descrizione" name="edit_descrizione" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <button type="submit" class="btn btn-primary" name="modifica_fase" value="1">Salva Modifiche</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@include('default.common.footer')

<script>
    // Funzione AJAX per aggiornare l'ordine
    function updateOrdine(idFase, newOrdine) {
        $.ajax({
            url: '{{ url("update-fase") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id_fase: idFase,
                ordine: newOrdine
            },
            success: function(response) {
                console.log("Ordine aggiornato con successo");
            },
            error: function(xhr, status, error) {
                console.error("Errore durante l'aggiornamento dell'ordine:", error);
            }
        });
    }

    // Funzione per aprire la modale di modifica
    function openEditModal(idFase, descrizione) {
        $('#editFaseModal').modal('show');
        document.getElementById('edit_id_fase').value = idFase;
        document.getElementById('edit_descrizione').value = descrizione;
    }

    function setFaseIdToDelete(idFase) {
        $('#deleteFase').modal('show');
        document.getElementById('id_fase').value = idFase;
    }
</script>
