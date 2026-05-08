@include('default.common.header')
<div class="page-content">
    <div class="container-fluid">
    <h2>Gestione Magazzini</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST">
        <div class="mb-3">
            <label for="codice_magazzino" class="form-label">Codice Magazzino</label>
            <input type="text" class="form-control" id="codice_magazzino" name="codice_magazzino" required>
        </div>
        <div class="mb-3">
            <label for="descrizione" class="form-label">Descrizione</label>
            <input type="text" class="form-control" id="descrizione" name="descrizione" required>
        </div>
        <input type="submit" name="crea_magazzino" class="btn btn-primary" value="Crea Magazzino">
    </form>

    <table class="table mt-4">
        <thead>
        <tr>
            <th>ID</th>
            <th>Codice Magazzino</th>
            <th>Descrizione</th>
            <th>Azioni</th>
        </tr>
        </thead>
        <tbody>
        @foreach($magazzini as $magazzino)
            <tr>
                <td>{{ $magazzino->id }}</td>
                <td>{{ $magazzino->codice_magazzino }}</td>
                <td>{{ $magazzino->descrizione }}</td>
                <td>
                    <a href="#" class="btn btn-sm btn-danger" onclick="setOrderIdToDelete({{ $magazzino->id }})">Delete</a>

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

    <!-- Modal for delete confirmation -->
    <div class="modal fade" id="deleteMg" tabindex="-1" aria-labelledby="deleteOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    @csrf
                    <input type="hidden" name="id_mg" value="" id="id_mg">
                    <div class="modal-body p-5 text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px"></lord-icon>
                        <div class="mt-4 text-center">
                            <h4>Sei sicuro di eliminare questo Magazzino? Tutti gli Articoli associati ad esso verranno dissociati!</h4>
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
</div>
@include('default.common.footer')

<script>
    function setOrderIdToDelete(idMg) {
        $('#deleteMg').modal('show');
        document.getElementById('id_mg').value = idMg;
    }
</script>
