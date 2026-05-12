@php $p = $prefisso ?? ''; @endphp
<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Servizio</label>
        <input type="text" name="servizio" id="{{ $p }}servizio" class="form-control" placeholder="es. LA01" maxlength="10">
    </div>
    <div class="col-md-4">
        <label class="form-label">Codice</label>
        <input type="text" name="codice" id="{{ $p }}codice" class="form-control" placeholder="codice articolo / rif. specifica">
    </div>
    <div class="col-md-3">
        <label class="form-label">Attività</label>
        <input type="text" name="attivita" id="{{ $p }}attivita" class="form-control" placeholder="attività">
    </div>
    <div class="col-md-2">
        <label class="form-label">&nbsp;</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="setup_tank" id="{{ $p }}setup_tank" value="1">
            <label class="form-check-label" for="{{ $p }}setup_tank">Setup Task</label>
        </div>
    </div>

    <div class="col-md-12">
        <label class="form-label">Descrizione</label>
        <textarea name="descrizione" id="{{ $p }}descrizione" class="form-control" rows="2" placeholder="descrizione della lavorazione"></textarea>
    </div>

    <div class="col-md-3">
        <label class="form-label">Quantità</label>
        <input type="number" name="qta" id="{{ $p }}qta" class="form-control" step="0.01" min="0" value="0">
    </div>
    <div class="col-md-3">
        <label class="form-label">Minuti</label>
        <input type="number" name="minuti" id="{{ $p }}minuti" class="form-control" step="0.01" min="0" value="0">
        <small class="text-muted">Se &gt; 0 ignora la qta</small>
    </div>
    <div class="col-md-3">
        <label class="form-label">Prezzo Unitario (€)</label>
        <input type="number" name="pu" id="{{ $p }}pu" class="form-control" step="0.01" min="0" value="33.75">
    </div>
    <div class="col-md-3">
        <label class="form-label">Aliquota IVA (%)</label>
        <input type="number" name="aliquota" id="{{ $p }}aliquota" class="form-control" min="0" max="100" value="22">
    </div>

    <div class="col-md-4">
        <label class="form-label">Costo Materiale (€)</label>
        <input type="number" name="materiale" id="{{ $p }}materiale" class="form-control" step="0.01" min="0" value="0">
    </div>
    <div class="col-md-8">
        <label class="form-label">Descrizione Materiale</label>
        <input type="text" name="descrizione_materiale" id="{{ $p }}descrizione_materiale" class="form-control" placeholder="opzionale">
    </div>
</div>
