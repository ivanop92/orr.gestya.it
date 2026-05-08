@include('default.common.header')

<div class="page-content">
    <div class="container-fluid">
        <h2>Gestione Utenti</h2>

        <!-- Pulsante per aggiungere un nuovo utente -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">Aggiungi Utente</button>


            <table class="table table-bordered nowrap datatable w-100">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th>Email</th>
                    <th>Azioni</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($utenti as $utente)
                    <tr>
                        <td>{{ $utente->id }}</td>
                        <td>{{ $utente->nome }}</td>
                        <td>{{ $utente->cognome }}</td>
                        <td>{{ $utente->email }}</td>
                        <td>
                            <!-- Pulsante per aprire la modal di modifica -->
                            <button class="btn btn-sm btn-primary" onclick="openEditModalUser({{ $utente->id }}, '{{ $utente->id_azienda }}', '{{ $utente->nome }}', '{{ $utente->cognome }}', '{{ $utente->email }}')">Modifica</button>
                            <a href="#" class="btn btn-sm btn-danger" onclick="setUtenteIdToDelete({{ $utente->id }})">Elimina</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>


        <!-- Modal per aggiungere un nuovo utente -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST">
                        <input type="hidden" name="crea_utente" value="1">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserLabel">Aggiungi Nuovo Utente</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="cognome" class="form-label">Cognome</label>
                                <input type="text" class="form-control" id="cognome" name="cognome" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                            <input type="submit" class="btn btn-primary" value="Crea Utente" name="crea_utente">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal per la modifica dell'utente -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST">
                        <input type="hidden" name="modifica_utente" value="1">
                        <input type="hidden" name="id_utente" id="edit_id_utente">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserLabel">Modifica Utente</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="edit_nome" name="edit_nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_cognome" class="form-label">Cognome</label>
                                <input type="text" class="form-control" id="edit_cognome" name="edit_cognome" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="edit_email" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_password" class="form-label">Password (Lascia vuoto per non cambiare)</label>
                                <input type="password" class="form-control" id="edit_password" name="edit_password">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                            <input type="submit" class="btn btn-primary" name="modifica_utente" value="Modifica">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteUtenteModal" tabindex="-1" aria-labelledby="deleteOrderLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST">
                        @csrf
                        <input type="hidden" name="id_utente" value="" id="id_utente">
                        <div class="modal-body p-5 text-center">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
                            <div class="mt-4 text-center">
                                <h4>Sei sicuro di eliminare questa Azienda?</h4>
                                <p class="text-muted fs-15 mb-4">Cancellando questa azienda verranno rimossi tutti i relativi dati</p>
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

    </div>
</div>

@include('default.common.footer')
<script>
    function setUtenteIdToDelete(id) {
        $('#deleteUtenteModal').modal('show');
        document.getElementById('id_utente').value = id;
    }

    function openEditModalUser(id, id_azienda, nome, cognome, email) {
        $('#editUserModal').modal('show');
        $('#edit_id_utente').val(id);
        $('#edit_nome').val(nome);
        $('#edit_cognome').val(cognome);
        $('#edit_email').val(email);
    }
</script>
