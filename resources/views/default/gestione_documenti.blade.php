@include('default.common.header')
<div class="page-content">
    <div class="container-fluid">
        <h2>Gestione Documenti</h2>
        <form method="POST">
            @csrf
            <div class="mb-3">
                <label for="cd_do" class="form-label">Codice Documento</label>
                <input type="text" class="form-control" id="cd_do" name="cd_do" required>
            </div>
            <div class="mb-3">
                <label for="descrizione" class="form-label">Descrizione</label>
                <input type="text" class="form-control" id="descrizione" name="descrizione" required>
            </div>
            <div class="d-flex justify-content-around">
                <!-- Checkbox per ogni campo booleano -->
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="attivo" name="attivo" onclick="toggleCheckbox('attivo', 'passivo')">
                    <label class="form-check-label" for="attivo">Attivo</label>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="passivo" name="passivo" onclick="toggleCheckbox('passivo', 'attivo')">
                    <label class="form-check-label" for="passivo">Passivo</label>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="scarico" name="scarico" onclick="toggleCheckbox('scarico', 'carico')">
                    <label class="form-check-label" for="scarico">Scarico</label>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="carico" name="carico" onclick="toggleCheckbox('carico', 'scarico')">
                    <label class="form-check-label" for="carico">Carico</label>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="fatturazione" name="fatturazione">
                    <label class="form-check-label" for="fatturazione">Fatturazione</label>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="ordine" name="ordine">
                    <label class="form-check-label" for="fatturazione">Ordine</label>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="scan_code" name="scan_code">
                    <label class="form-check-label" for="scan_code">Scan Code</label>
                </div>
            </div>
            <!-- Continua per gli altri campi simili -->
            <input type="submit" name="crea_documento" class="btn btn-primary" value="Crea Documento">
        </form>

        <script>
            function toggleCheckbox(selectedId, relatedId) {
                var selectedCheckbox = document.getElementById(selectedId);
                var relatedCheckbox = document.getElementById(relatedId);

                if (selectedCheckbox.checked) {
                    relatedCheckbox.disabled = true;
                } else {
                    relatedCheckbox.disabled = false;
                }
            }
        </script>

    </div>

    <div class="container mt-4">
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Codice Documento</th>
                <th>Descrizione</th>
                <th>Attivo</th>
                <!-- Altri campi -->
                <th>Azioni</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($documenti as $documento)
                <tr>
                    <td>{{ $documento->id }}</td>
                    <td>{{ $documento->cd_do }}</td>
                    <td>{{ $documento->descrizione }}</td>
                    <td>{{ $documento->attivo ? 'Sì' : 'No' }}</td>
                    <!-- Altri campi -->
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="openEditModal({{ $documento->id }}, '{{ $documento->cd_do }}', '{{ $documento->descrizione }}', {{ $documento->attivo }}, {{ $documento->passivo }}, {{ $documento->scarico }}, {{ $documento->carico }}, {{ $documento->fatturazione }},{{ $documento->ordine }}, {{ $documento->scan_code }})">Modifica</button>
                        <a href="#" class="btn btn-sm btn-danger" onclick="setOrderIdToDelete({{ $documento->id }})">Delete</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal for delete confirmation -->
    <div class="modal fade" id="deleteDoc" tabindex="-1" aria-labelledby="deleteOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    @csrf
                    <input type="hidden" name="id_documento" value="" id="id_documento">
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

    <!-- Modal for editing document -->
    <div class="modal fade modal-xl" id="editDocModal" tabindex="-1" aria-labelledby="editDocLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    @csrf
                    <input type="hidden" name="id_documento" value="" id="edit_id_documento">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDocLabel">Modifica Documento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_cd_do" class="form-label">Codice Documento</label>
                            <input type="text" class="form-control" id="edit_cd_do" name="edit_cd_do" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_descrizione" class="form-label">Descrizione</label>
                            <input type="text" class="form-control" id="edit_descrizione" name="edit_descrizione" required>
                        </div>
                        <div class="d-flex justify-content-around">
                            <!-- Checkbox per ogni campo booleano -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_attivo" name="edit_attivo">
                                <label class="form-check-label" for="edit_attivo">Attivo</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_passivo" name="edit_passivo">
                                <label class="form-check-label" for="edit_passivo">Passivo</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_scarico" name="edit_scarico">
                                <label class="form-check-label" for="edit_scarico">Scarico</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_carico" name="edit_carico">
                                <label class="form-check-label" for="edit_carico">Carico</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_fatturazione" name="edit_fatturazione">
                                <label class="form-check-label" for="edit_fatturazione">Fatturazione</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_ordine" name="edit_ordine">
                                <label class="form-check-label" for="edit_ordine">Ordine</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_scan_code" name="edit_scan_code">
                                <label class="form-check-label" for="edit_scan_code">Scan Code</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <button type="submit" class="btn btn-primary" name="modifica_documento" value="1">Salva Modifiche</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function setOrderIdToDelete(docId) {
            $('#deleteDoc').modal('show');
            document.getElementById('id_documento').value = docId;
        }

        function openEditModal(id, cd_do, descrizione, attivo, passivo, scarico, carico, fatturazione, ordine, scan_code) {
            $('#editDocModal').modal('show');
            document.getElementById('edit_id_documento').value = id;
            document.getElementById('edit_cd_do').value = cd_do;
            document.getElementById('edit_descrizione').value = descrizione;
            document.getElementById('edit_attivo').checked = attivo;
            document.getElementById('edit_passivo').checked = passivo;
            document.getElementById('edit_scarico').checked = scarico;
            document.getElementById('edit_carico').checked = carico;
            document.getElementById('edit_fatturazione').checked = fatturazione;
            document.getElementById('edit_ordine').checked = ordine;
            document.getElementById('edit_scan_code').checked = scan_code;
        }
    </script>
</div>
@include('default.common.footer')
