@include('utente.common.header')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Articoli</h4>

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
        <!-- end page title -->

        @if(session('messaggio'))
            <div class="alert alert-{{ session('tipo_messaggio') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show">
                {{ session('messaggio') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($tipo === 'prodotto_finito')
            <div class="row">
                <!-- Inizio sezione Prodotti Finiti (tipologia == 0) -->
                <div class="col-lg-12">
                    <div class="card">

                        <div class="card-header">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-0">Prodotto Finito</h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="hstack text-nowrap gap-2">
                                        <!-- Barra di ricerca -->
                                        <form method="GET" class="search-form d-flex align-items-center">
                                            <input type="hidden" name="tipo" value="{{ $tipo }}">
                                            <div class="position-relative">
                                                <input type="text" name="search" value="{{ $pagination['search'] }}"
                                                       class="form-control" placeholder="Cerca articoli..."
                                                       style="width: 250px; padding-right: 35px;">
                                                <button type="submit" class="btn btn-link position-absolute"
                                                        style="right: 0; top: 0; border: none; background: none; padding: 8px 10px;">
                                                    <i class="ri-search-line"></i>
                                                </button>
                                            </div>
                                            @if($pagination['search'])
                                                <a href="{{ url()->current() }}?tipo={{ $tipo }}" class="btn btn-outline-secondary ms-2">
                                                    <i class="ri-close-line"></i>
                                                </a>
                                            @endif
                                        </form>

                                        <button class="btn btn-info add-btn" onclick="aggiungi();">
                                            <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi
                                        </button>
                                        <button class="btn btn-soft-success" onclick="$('#modal_importa_articoli').modal('show')">Importa</button>
                                        <button class="btn btn-soft-warning">Esporta</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Info risultati ricerca -->
                            @if($pagination['search'])
                                <div class="alert alert-info mb-3">
                                    <i class="ri-search-line me-2"></i>
                                    Risultati per "<strong>{{ $pagination['search'] }}</strong>":
                                    {{ $pagination['total_items'] }} articoli trovati
                                </div>
                            @endif

                            <table class="table table-bordered table-hover" style="width:100%">
                                <thead>
                                <tr>
                                    <th style="width:60px;">Img</th>
                                    <th>Codice</th>
                                    <th>Descrizione</th>
                                    <th>Cliente</th>
                                    <th>Listino</th>
                                    <th>Giacenza</th>
                                    <th>Data primo carico</th>
                                    <th style="width:120px;">Azioni</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($articoli as $a){ ?>
                                <tr>
                                    <td class="text-center" style="vertical-align:middle;">
                                        <div style="position:relative;display:inline-block;">
                                            @if($a->immagine)
                                                <img src="/{{ $a->immagine }}" alt="" style="width:45px;height:45px;object-fit:cover;border-radius:6px;cursor:pointer;" onclick="window.open('/{{ $a->immagine }}', '_blank')">
                                            @else
                                                <div style="width:45px;height:45px;border-radius:6px;background:#f3f3f9;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                                                    <i class="ri-image-line text-muted"></i>
                                                </div>
                                            @endif
                                            <a href="javascript:void(0)" onclick="document.getElementById('img_upload_{{ $a->id }}').click()" style="position:absolute;bottom:-5px;right:-5px;width:20px;height:20px;border-radius:50%;background:#405189;color:#fff;display:flex;align-items:center;justify-content:center;font-size:10px;">
                                                <i class="ri-camera-line"></i>
                                            </a>
                                        </div>
                                        <form method="post" enctype="multipart/form-data" id="img_form_{{ $a->id }}" style="display:none;">
                                            @csrf
                                            <input type="hidden" name="id_articolo" value="{{ $a->id }}">
                                            <input type="file" id="img_upload_{{ $a->id }}" name="immagine_articolo" accept="image/*" onchange="document.getElementById('img_form_{{ $a->id }}').submit();">
                                            <input type="hidden" name="upload_immagine_articolo" value="1">
                                        </form>
                                    </td>
                                    <td><?php echo $a->codice_articolo ?></td>
                                    <td>
                                        <?php echo $a->titolo ?>
                                        @if(isset($fasi_associate[$a->id]) && count($fasi_associate[$a->id]) > 0)
                                            <br>
                                            <button class="btn btn-sm btn-info mt-1" onclick="showDistintaBaseTree(<?php echo $a->id ?>)">
                                                <i class="ri-git-branch-line"></i> Distinta Base
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        @if($a->id_cliente && isset($clienti_map[$a->id_cliente]))
                                            <span class="badge bg-soft-primary text-primary">{{ $clienti_map[$a->id_cliente]->ragione_sociale }}</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>&euro;<?php echo number_format($a->prezzo, 2) ?>/<?php echo $a->um ?></td>
                                    <td><?php echo number_format($a->giacenza ?? 0, 2) ?></td>
                                    <td><?php echo $a->data_primo_carico ? date('d/m/Y', strtotime($a->data_primo_carico)) : '—' ?></td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a href="{{ url('utente/dettaglio_articolo/'.$a->id) }}" class="btn btn-sm btn-info" title="Dettaglio">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <button class="btn btn-sm btn-success" title="Associa cliente" onclick="apriAssociaCliente(<?php echo $a->id ?>, <?php echo $a->id_cliente ?? 'null' ?>)">
                                                <i class="ri-user-line"></i>
                                            </button>
                                            @if(isset($fasi_associate[$a->id]) && count($fasi_associate[$a->id]) > 0)
                                                <a onclick="distinta_base(<?php echo $a->id ?>,<?php echo $a->prezzo ?>)" class="btn btn-sm btn-primary">DB</a>
                                            @endif
                                            <a onclick="modifica(<?php echo $a->id ?>)" class="btn btn-sm btn-primary"><i class="ri-edit-2-line"></i></a>
                                            <form method="post" onsubmit="return confirm('Vuoi Eliminare questo articolo ?')">
                                                <input type="hidden" name="id" value="<?php echo $a->id ?>">
                                                <button name="elimina" value="Elimina" type="submit" class="btn btn-sm btn-danger"><i class="ri-delete-bin-2-line"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                                </tbody>
                            </table>

                            <!-- Paginazione -->
                            @if($pagination['total_pages'] > 1)
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div class="text-muted">
                                        Visualizzati {{ count($articoli) }} di {{ $pagination['total_items'] }} articoli
                                    </div>

                                    <nav>
                                        <ul class="pagination mb-0">
                                            @if($pagination['has_prev'])
                                                <li class="page-item">
                                                    <a class="page-link" href="?page={{ $pagination['prev_page'] }}&tipo={{ $tipo }}&search={{ $pagination['search'] }}">
                                                        <i class="ri-arrow-left-line"></i>
                                                    </a>
                                                </li>
                                            @else
                                                <li class="page-item disabled">
                                                    <span class="page-link"><i class="ri-arrow-left-line"></i></span>
                                                </li>
                                            @endif

                                            @for($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++)
                                                <li class="page-item {{ $i == $pagination['current_page'] ? 'active' : '' }}">
                                                    <a class="page-link" href="?page={{ $i }}&tipo={{ $tipo }}&search={{ $pagination['search'] }}">{{ $i }}</a>
                                                </li>
                                            @endfor

                                            @if($pagination['has_next'])
                                                <li class="page-item">
                                                    <a class="page-link" href="?page={{ $pagination['next_page'] }}&tipo={{ $tipo }}&search={{ $pagination['search'] }}">
                                                        <i class="ri-arrow-right-line"></i>
                                                    </a>
                                                </li>
                                            @else
                                                <li class="page-item disabled">
                                                    <span class="page-link"><i class="ri-arrow-right-line"></i></span>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($tipo === 'materia_prima')
            <!-- Inizio sezione Materie Prime (tipologia == 1) -->
            <div class="col-lg-12">
                <div class="card">

                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-0">Materie Prime</h5>
                            </div>

                            <div class="flex-shrink-0">
                                <div class="hstack text-nowrap gap-2">
                                    <!-- Barra di ricerca -->
                                    <form method="GET" class="search-form d-flex align-items-center">
                                        <input type="hidden" name="tipo" value="{{ $tipo }}">
                                        <div class="position-relative">
                                            <input type="text" name="search" value="{{ $pagination['search'] }}"
                                                   class="form-control" placeholder="Cerca articoli..."
                                                   style="width: 250px; padding-right: 35px;">
                                            <button type="submit" class="btn btn-link position-absolute"
                                                    style="right: 0; top: 0; border: none; background: none; padding: 8px 10px;">
                                                <i class="ri-search-line"></i>
                                            </button>
                                        </div>
                                        @if($pagination['search'])
                                            <a href="{{ url()->current() }}?tipo={{ $tipo }}" class="btn btn-outline-secondary ms-2">
                                                <i class="ri-close-line"></i>
                                            </a>
                                        @endif
                                    </form>

                                    <button class="btn btn-info add-btn" onclick="aggiungi();">
                                        <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi
                                    </button>

                                    <button class="btn btn-soft-success">Importa</button>
                                    <button class="btn btn-soft-warning">Esporta</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Info risultati ricerca -->
                        @if($pagination['search'])
                            <div class="alert alert-info mb-3">
                                <i class="ri-search-line me-2"></i>
                                Risultati per "<strong>{{ $pagination['search'] }}</strong>":
                                {{ $pagination['total_items'] }} articoli trovati
                            </div>
                        @endif

                        <table class="table table-bordered table-hover" style="width:100%">
                            <thead>
                            <tr>
                                <th>Codice</th>
                                <th>Titolo</th>
                                <th>Descrizione</th>
                                <th>Prezzo Acquisto</th>
                                <th style="width:100px;">Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php foreach($articoli as $a){ ?>
                            <tr>
                                <td><?php echo $a->codice_articolo ?></td>
                                <td><?php echo $a->titolo ?><br><small>Materiale</small></td>
                                <td><small>Descrizione: <?php echo nl2br($a->descrizione) ?></small>
                                        <?php foreach($a->distinta_base as $db){ ?>
                                    <small><?php echo '<br>'.$db->materiale.' ('.$db->qta.' '.$db->um.')' ?></small>
                                    <?php } ?>
                                </td>
                                <td>&euro;<?php echo $a->prezzo ?>/<?php echo $a->um ?></td>
                                <td>
                                    <div style="display: flex">
                                        <a href="{{ url('utente/dettaglio_articolo/'.$a->id) }}" class="btn btn-sm btn-info" title="Dettaglio">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        <a style="margin-left:5px;" onclick="modifica(<?php echo $a->id ?>)" class="btn btn-sm btn-primary"><i class="ri-edit-2-line"></i></a>
                                        <form method="post" onsubmit="return confirm('Vuoi Eliminare questo articolo ?')">
                                            <input type="hidden" name="id" value="<?php echo $a->id ?>">
                                            <button style="margin-left:5px;" name="elimina" value="Elimina" type="submit" class="btn btn-sm btn-danger"><i class="ri-delete-bin-2-line"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>

                        <!-- Paginazione -->
                        @if($pagination['total_pages'] > 1)
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted">
                                    Visualizzati {{ count($articoli) }} di {{ $pagination['total_items'] }} articoli
                                </div>

                                <nav>
                                    <ul class="pagination mb-0">
                                        @if($pagination['has_prev'])
                                            <li class="page-item">
                                                <a class="page-link" href="?page={{ $pagination['prev_page'] }}&tipo={{ $tipo }}&search={{ $pagination['search'] }}">
                                                    <i class="ri-arrow-left-line"></i>
                                                </a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="ri-arrow-left-line"></i></span>
                                            </li>
                                        @endif

                                        @for($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++)
                                            <li class="page-item {{ $i == $pagination['current_page'] ? 'active' : '' }}">
                                                <a class="page-link" href="?page={{ $i }}&tipo={{ $tipo }}&search={{ $pagination['search'] }}">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        @if($pagination['has_next'])
                                            <li class="page-item">
                                                <a class="page-link" href="?page={{ $pagination['next_page'] }}&tipo={{ $tipo }}&search={{ $pagination['search'] }}">
                                                    <i class="ri-arrow-right-line"></i>
                                                </a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="ri-arrow-right-line"></i></span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if($tipo === 'commerciale')
            <!-- Inizio sezione Articoli Commerciali (tipologia == 2) -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-0">Articoli Commerciali</h5>
                            </div>

                            <div class="flex-shrink-0">
                                <div class="hstack text-nowrap gap-2">
                                    <!-- Barra di ricerca -->
                                    <form method="GET" class="search-form d-flex align-items-center">
                                        <input type="hidden" name="tipo" value="{{ $tipo }}">
                                        <div class="position-relative">
                                            <input type="text" name="search" value="{{ $pagination['search'] }}"
                                                   class="form-control" placeholder="Cerca articoli..."
                                                   style="width: 250px; padding-right: 35px;">
                                            <button type="submit" class="btn btn-link position-absolute"
                                                    style="right: 0; top: 0; border: none; background: none; padding: 8px 10px;">
                                                <i class="ri-search-line"></i>
                                            </button>
                                        </div>
                                        @if($pagination['search'])
                                            <a href="{{ url()->current() }}?tipo={{ $tipo }}" class="btn btn-outline-secondary ms-2">
                                                <i class="ri-close-line"></i>
                                            </a>
                                        @endif
                                    </form>

                                    <button class="btn btn-info add-btn" onclick="aggiungi();">
                                        <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi
                                    </button>
                                    <button class="btn btn-soft-success">Importa</button>
                                    <button class="btn btn-soft-warning">Esporta</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Info risultati ricerca -->
                        @if($pagination['search'])
                            <div class="alert alert-info mb-3">
                                <i class="ri-search-line me-2"></i>
                                Risultati per "<strong>{{ $pagination['search'] }}</strong>":
                                {{ $pagination['total_items'] }} articoli trovati
                            </div>
                        @endif

                        <table class="table table-bordered table-hover" style="width:100%">
                            <thead>
                            <tr>
                                <th>Codice</th>
                                <th>Titolo</th>
                                <th>Descrizione</th>
                                <th>Prezzo Vendita</th>
                                <th style="width:100px;">Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php foreach($articoli as $a){ ?>
                            <tr>
                                <td><?php echo $a->codice_articolo ?></td>
                                <td><?php echo $a->titolo ?><br><small>Commerciale</small></td>
                                <td><small>Descrizione: <?php echo nl2br($a->descrizione) ?></small></td>
                                <td>&euro;<?php echo $a->prezzo ?>/<?php echo $a->um ?></td>
                                <td>
                                    <div style="display: flex">
                                        <a href="{{ url('utente/dettaglio_articolo/'.$a->id) }}" class="btn btn-sm btn-info" title="Dettaglio">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        <a style="margin-left:5px;" onclick="modifica(<?php echo $a->id ?>)" class="btn btn-sm btn-primary"><i class="ri-edit-2-line"></i></a>
                                        <form method="post" onsubmit="return confirm('Vuoi Eliminare questo articolo ?')">
                                            <input type="hidden" name="id" value="<?php echo $a->id ?>">
                                            <button style="margin-left:5px;" name="elimina" value="Elimina" type="submit" class="btn btn-sm btn-danger"><i class="ri-delete-bin-2-line"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>

                        <!-- Paginazione -->
                        @if($pagination['total_pages'] > 1)
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted">
                                    Visualizzati {{ count($articoli) }} di {{ $pagination['total_items'] }} articoli
                                </div>

                                <nav>
                                    <ul class="pagination mb-0">
                                        @if($pagination['has_prev'])
                                            <li class="page-item">
                                                <a class="page-link" href="?page={{ $pagination['prev_page'] }}&tipo={{ $tipo }}&search={{ $pagination['search'] }}">
                                                    <i class="ri-arrow-left-line"></i>
                                                </a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="ri-arrow-left-line"></i></span>
                                            </li>
                                        @endif

                                        @for($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++)
                                            <li class="page-item {{ $i == $pagination['current_page'] ? 'active' : '' }}">
                                                <a class="page-link" href="?page={{ $i }}&tipo={{ $tipo }}&search={{ $pagination['search'] }}">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        @if($pagination['has_next'])
                                            <li class="page-item">
                                                <a class="page-link" href="?page={{ $pagination['next_page'] }}&tipo={{ $tipo }}&search={{ $pagination['search'] }}">
                                                    <i class="ri-arrow-right-line"></i>
                                                </a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="ri-arrow-right-line"></i></span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if($tipo === 'semilavorato')
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-0">
                                    <i class="ri-settings-3-line me-2"></i>Semilavorati
                                </h5>
                                <p class="text-muted mb-0 small mt-1">Componenti prodotti internamente, usati nella distinta base dei prodotti finiti</p>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="hstack text-nowrap gap-2">
                                    <form method="GET" class="search-form d-flex align-items-center">
                                        <input type="hidden" name="tipo" value="{{ $tipo }}">
                                        <div class="position-relative">
                                            <input type="text" name="search" value="{{ $pagination['search'] }}"
                                                   class="form-control" placeholder="Cerca semilavorati..."
                                                   style="width: 250px; padding-right: 35px;">
                                            <button type="submit" class="btn btn-link position-absolute"
                                                    style="right: 0; top: 0; border: none; background: none; padding: 8px 10px;">
                                                <i class="ri-search-line"></i>
                                            </button>
                                        </div>
                                        @if($pagination['search'])
                                            <a href="{{ url()->current() }}?tipo={{ $tipo }}" class="btn btn-outline-secondary ms-2">
                                                <i class="ri-close-line"></i>
                                            </a>
                                        @endif
                                    </form>
                                    <button class="btn btn-info add-btn" onclick="aggiungi();">
                                        <i class="ri-add-fill me-1 align-bottom"></i>Aggiungi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($pagination['search'])
                            <div class="alert alert-info mb-3">
                                Risultati per "<strong>{{ $pagination['search'] }}</strong>": {{ $pagination['total_items'] }} trovati
                            </div>
                        @endif
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Codice</th>
                                <th>Descrizione</th>
                                <th>Fasi di lavorazione</th>
                                <th>Costo</th>
                                <th style="width:100px;">Azioni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($articoli as $a)
                            @php
                                $fasi_semi = isset($fasi_associate[$a->id]) ? $fasi_associate[$a->id] : collect();
                            @endphp
                            <tr>
                                <td>{{ $a->codice_articolo }}</td>
                                <td>{{ $a->titolo }}</td>
                                <td>
                                    @foreach($fasi_semi as $fs)
                                        <span class="badge bg-soft-info text-info me-1">{{ $fs->descrizione }}</span>
                                    @endforeach
                                    @if(count($fasi_semi) == 0)
                                        <span class="text-muted small">Nessuna fase</span>
                                    @endif
                                </td>
                                <td>&euro;{{ number_format($a->prezzo, 2) }}/{{ $a->um }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ url('utente/dettaglio_articolo/'.$a->id) }}" class="btn btn-sm btn-info" title="Dettaglio">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        @if(isset($fasi_associate[$a->id]) && count($fasi_associate[$a->id]) > 0)
                                            <a onclick="distinta_base({{ $a->id }}, {{ $a->prezzo }})" class="btn btn-sm btn-primary" title="Distinta Base">DB</a>
                                        @endif
                                        <a onclick="modifica({{ $a->id }})" class="btn btn-sm btn-primary"><i class="ri-edit-2-line"></i></a>
                                        <form method="post" onsubmit="return confirm('Vuoi eliminare questo semilavorato?')">
                                            <input type="hidden" name="id" value="{{ $a->id }}">
                                            <button name="elimina" value="Elimina" type="submit" class="btn btn-sm btn-danger"><i class="ri-delete-bin-2-line"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>

                        @if($pagination['total_pages'] > 1)
                            <div class="d-flex justify-content-end mt-3">
                                <nav><ul class="pagination mb-0">
                                    @if($pagination['has_prev'])
                                        <li class="page-item"><a class="page-link" href="?page={{ $pagination['prev_page'] }}&tipo={{ $tipo }}&search={{ $pagination['search'] }}"><i class="ri-arrow-left-line"></i></a></li>
                                    @endif
                                    @if($pagination['has_next'])
                                        <li class="page-item"><a class="page-link" href="?page={{ $pagination['next_page'] }}&tipo={{ $tipo }}&search={{ $pagination['search'] }}"><i class="ri-arrow-right-line"></i></a></li>
                                    @endif
                                </ul></nav>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>
    <!--end row-->

</div>
<!-- container-fluid -->
</div>


<!-- Modal Alberatura Distinta Base -->
<div class="modal fade" id="modal_alberatura_db" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title">Alberatura Distinta Base</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="distinta-tree"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_aggiungi" >
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Aggiungi Articolo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-lg-12">
                            <div class="text-center">
                                <div class="position-relative d-inline-block">
                                    <div class="avatar-lg p-1">
                                        <div class="avatar-title bg-light rounded-circle">
                                            <img src="/placehold_immagine.png" id="customer-img" class="avatar-md rounded-circle object-cover" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <input class="form-control" type="file" name="immagine" accept="image/png, image/gif, image/jpeg">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Codice<b style="color:red">*</b></label>
                            <input type="text" id="codice_articolo" name="codice_articolo" class="form-control" placeholder="Codice Articolo" value="{{ $prossimo_codice_articolo ?? '' }}" required/>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Descrizione<b style="color:red">*</b></label>
                            <input type="text" id="titolo" name="titolo" class="form-control" placeholder="Titolo" required/>
                        </div>

                        <div class="col-lg-12" >
                            <label for="fasi" class="form-label">Fasi Di Lavorazione</label>
                            <select data-choices data-choices-removeItem multiple name="fasi[]" style="width: 100%;" >
                                @foreach ($fasi as $fase)
                                    <option value="{{ $fase->id }}"
                                            @if(isset($a->fasi_associate) && in_array($fase->id, $a->fasi_associate)) selected @endif>
                                        {{ $fase->descrizione }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Tipologia<b style="color:red">*</b></label>
                            <select name="tipologia" class="form-control">
                                <option value="0" <?php echo (($tipo ?? 'prodotto_finito') == 'prodotto_finito')?'selected':'' ?>>Prodotto Finito</option>
                                <option value="1" <?php echo (($tipo ?? 'prodotto_finito') == 'materia_prima')?'selected':'' ?>>Materia Prima</option>
                                <option value="2" <?php echo (($tipo ?? 'prodotto_finito') == 'commerciale')?'selected':'' ?>>Commerciale</option>
                                <option value="3" <?php echo (($tipo ?? 'prodotto_finito') == 'semilavorato')?'selected':'' ?>>Semilavorato</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Descrizione</label>
                            <textarea id="descrizione" name="descrizione" class="form-control" style="height:150px"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Prezzo <?php echo (($tipo ?? 'prodotto_finito') == 'materia_prima')?'Acquisto':'Vendita' ?><b style="color:red">*</b></label>
                            <input type="text" id="prezzo" name="prezzo" class="form-control" placeholder="Prezzo" required/>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">UM<b style="color:red">*</b></label>
                            <input type="text" id="um" name="um" class="form-control" placeholder="Unità di Misura" required/>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="submit" class="btn btn-success" id="add-btn" name="aggiungi" value="Aggiungi" >
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



<?php foreach($articoli as $a){ ?>


<div class="modal fade" id="modal_modifica_<?php echo $a->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel">Modifica Articolo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-lg-12">
                            <div class="text-center">
                                <div class="position-relative d-inline-block">
                                    <div class="avatar-lg p-1">
                                        <div class="avatar-title bg-light rounded-circle">
                                            <img src="<?php echo $a->immagine ?>" id="customer-img" class="avatar-md rounded-circle object-cover" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <input class="form-control" type="file" name="immagine" accept="image/png, image/gif, image/jpeg">
                        </div>


                        <div class="col-md-12">
                            <label class="form-label">Titolo<b style="color:red">*</b></label>
                            <input type="text" id="titolo" name="titolo" value="<?php echo $a->titolo ?>" class="form-control" placeholder="Titolo" required/>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Codice Articolo<b style="color:red">*</b></label>
                            <input type="text" id="codice_articolo" value="<?php echo $a->codice_articolo ?>" name="codice_articolo" class="form-control" placeholder="Codice Articolo" required/>
                        </div>

                        <div class="col-md-12">
                            <label for="fasi" class="form-label">Fasi<b style="color:red">*</b></label>
                            <select data-choices data-choices-removeItem multiple name="fasi[]" id="fasi_{{ $a->id }}"  style="width: 100%;" >
                                @foreach ($fasi as $fase)
                                    <option value="{{ $fase->id }}"
                                            @if(isset($a->fasi_associate) && in_array($fase->id, $a->fasi_associate)) selected @endif>
                                        {{ $fase->descrizione }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Tipologia<b style="color:red">*</b></label>
                            <select name="tipologia" class="form-control">
                                <option value="0" <?php echo ($a->tipologia == 0)?'selected':'' ?>>Prodotto Finito</option>
                                <option value="1" <?php echo ($a->tipologia == 1)?'selected':'' ?>>Materia Prima</option>
                                <option value="2" <?php echo ($a->tipologia == 2)?'selected':'' ?>>Commerciale</option>
                                <option value="3" <?php echo ($a->tipologia == 3)?'selected':'' ?>>Semilavorato</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Descrizione</label>
                            <textarea id="descrizione" name="descrizione" class="form-control" style="height:150px"><?php echo $a->descrizione ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Prezzo<b style="color:red">*</b></label>
                            <input type="text" id="prezzo" name="prezzo" class="form-control" value="<?php echo $a->prezzo ?>" placeholder="Prezzo" required/>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">UM<b style="color:red">*</b></label>
                            <input type="text" id="um" name="um" class="form-control" value="<?php echo $a->um ?>" placeholder="Unità di Misura" required/>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="<?php echo $a->id ?>">
                        <input type="submit" class="btn btn-success" id="add-btn" name="modifica" value="Modifica" >
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($articoli as $articolo)
    <div class="modal fade modal-xl" id="modal_db_{{ $articolo->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Modifica Distinta Base - Articolo: {{ $articolo->titolo }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>
                <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post">
                    <div class="modal-body">
                        <!-- Cicla solo sulle fasi associate a questo articolo -->
                        @if(isset($fasi_associate[$articolo->id]) && count($fasi_associate[$articolo->id]) > 0)
                            @foreach($fasi_associate[$articolo->id] as $fase)
                                <div class="card mb-3">
                                    <div class="card-header" id="heading_{{ $fase->id }}">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#fase_{{ $articolo->id }}_{{ $fase->id }}" aria-expanded="true" aria-controls="fase_{{ $articolo->id }}_{{ $fase->id }}">
                                                Fase: {{ $fase->descrizione }}
                                            </button>
                                        </h5>
                                    </div>

                                    <div id="fase_{{ $articolo->id }}_{{ $fase->id }}" class="collapse" aria-labelledby="heading_{{ $fase->id }}" data-bs-parent="#accordion">
                                        <div class="card-body">
                                            @php
                                                $num_db = isset($fase->distinta_base) ? count($fase->distinta_base) : 0;
                                                $num_slots = max(3, $num_db + 1);
                                            @endphp
                                            <div id="slots_{{ $articolo->id }}_{{ $fase->id }}">
                                            @for($i=0; $i<$num_slots; $i++)
                                                <div class="row mb-1" id="slot_{{ $articolo->id }}_{{ $fase->id }}_{{ $i }}">
                                                    <div class="col-md-6">
                                                        @if($i == 0)<label>Materiale</label>@endif
                                                        <select id="db_{{ $articolo->id }}_{{ $fase->id }}_{{ $i }}" name="materiale[{{ $fase->id }}][{{ $i }}]" class="form-control select2" style="width:100%;" onchange="calcolaCostoTotale({{ $articolo->id }})">
                                                            <option value="">Nessun Materiale</option>
                                                            @php $last_tipo = null; @endphp
                                                            @foreach($materiali as $m)
                                                                @if($last_tipo !== $m->tipologia)
                                                                    @if($m->tipologia == 1)
                                                                        <option disabled>── Materie Prime ──</option>
                                                                    @elseif($m->tipologia == 2)
                                                                        <option disabled>── Commerciali / Ricambi ──</option>
                                                                    @elseif($m->tipologia == 3)
                                                                        <option disabled>── Semilavorati ──</option>
                                                                    @endif
                                                                    @php $last_tipo = $m->tipologia; @endphp
                                                                @endif
                                                                @php
                                                                    $prefisso = '';
                                                                    if($m->tipologia == 3) $prefisso = '[SEMI] ' . $m->codice_articolo . ' - ';
                                                                    if($m->tipologia == 2) $prefisso = '[COMM] ';
                                                                @endphp
                                                                <option value="{{ $m->id }}" costo="{{ $m->prezzo }}" data-tipologia="{{ $m->tipologia }}" {{ isset($fase->distinta_base[$i]) && $fase->distinta_base[$i]->id_materiale == $m->id ? 'selected' : '' }}>
                                                                    {{ $prefisso }}{{ $m->titolo }} ({{ $m->um }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        @if($i == 0)<label>Fornitore (lav. esterna - solo per semilavorati)</label>@endif
                                                        <select name="fornitore_db[{{ $fase->id }}][{{ $i }}]" class="form-control" style="width:100%;">
                                                            <option value="">Nessuno</option>
                                                            @foreach($fornitori_db as $f)
                                                                <option value="{{ $f->id }}" {{ isset($fase->distinta_base[$i]) && $fase->distinta_base[$i]->id_fornitore == $f->id ? 'selected' : '' }}>{{ $f->ragione_sociale }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        @if($i == 0)<label>Qta</label>@endif
                                                        <input id="qta_db_{{ $articolo->id }}_{{ $fase->id }}_{{ $i }}" type="number" min="0" step="0.0001" name="quantita[{{ $fase->id }}][{{ $i }}]" value="{{ isset($fase->distinta_base[$i]) ? $fase->distinta_base[$i]->qta : '' }}" class="form-control" onkeyup="calcolaCostoTotale({{ $articolo->id }})" onchange="calcolaCostoTotale({{ $articolo->id }})">
                                                    </div>
                                                </div>
                                            @endfor
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="aggiungiSlotDB({{ $articolo->id }}, {{ $fase->id }})">
                                                <i class="ri-add-line me-1"></i> Aggiungi materiale
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>Nessuna fase associata a questo articolo.</p>
                        @endif

                        <!-- Sezione di calcolo complessiva sotto tutte le fasi per l'articolo -->
                        <div class="row">

                            <div class="col-md-6" style="text-align:right">
                                <b>Costo Materie Prime Totale:</b><br><br>
                                <b>Prezzo di Vendita Totale:</b><br><br>
                                <b>Percentuale Costo Totale:</b><br>
                            </div>

                            <div class="col-md-3" style="text-align:left">
                                <b id="costo_materia_prima_totale_{{ $articolo->id }}"></b><br>
                                <input name="prezzo_totale" id="prezzo_totale_{{ $articolo->id }}" class="form-control" value="{{ $articolo->prezzo }}" onkeyup="ricalcoloPercentualeTotale({{ $articolo->id }})" onchange="ricalcoloPercentualeTotale({{ $articolo->id }})"><br>
                                <b id="incidenza_totale_{{ $articolo->id }}"></b><br>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                        <input type="hidden" name="id" value="{{ $articolo->id }}">
                        <input type="submit" class="btn btn-success" name="modifica_db" value="Salva Distinta Base" >
                    </div>
                </form>

            </div>
        </div>
    </div>
@endforeach

<?php } ?>


<!-- Modal Associa Cliente -->
<div class="modal fade" id="modal_associa_cliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-success p-3">
                <h5 class="modal-title">Associa Cliente al Prodotto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="{{ url('utente/articoli') }}">
                @csrf
                <input type="hidden" name="tipo" value="prodotto_finito">
                <input type="hidden" name="id_articolo" id="associa_id_articolo">
                <div class="modal-body">
                    <label class="form-label">Seleziona cliente</label>
                    <select class="form-select" name="id_cliente" id="associa_id_cliente">
                        <option value="">-- Nessun cliente --</option>
                        @foreach($clienti as $cl)
                            <option value="{{ $cl->id }}">{{ $cl->ragione_sociale }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" name="associa_cliente" value="1" class="btn btn-success">
                        <i class="ri-save-line me-1"></i> Salva
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Importa Prodotti -->
<div class="modal fade" id="modal_importa_articoli" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-success p-3">
                <h5 class="modal-title">Importa Prodotti Finiti da Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="tablelist-form" autocomplete="off" enctype="multipart/form-data" method="post" action="{{ url('utente/import_articoli_excel') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info small">
                        <p class="mb-1">Colonne supportate (prima riga = intestazioni):</p>
                        <ul class="mb-1">
                            <li><strong>Cod.</strong> — Codice articolo</li>
                            <li><strong>Descrizione</strong> — Descrizione prodotto</li>
                            <li><strong>Categoria</strong> — Nome cliente da associare (fuzzy match)</li>
                            <li><strong>Listino 1</strong> — Prezzo di listino</li>
                            <li><strong>Q.tà in giacenza</strong> — Giacenza iniziale</li>
                            <li><strong>Data primo carico</strong> — Data (gg/mm/aaaa)</li>
                        </ul>
                        <p class="mb-0">Campo obbligatorio: <strong>Cod.</strong> o <strong>Descrizione</strong></p>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">File Excel</label>
                        <input class="form-control" type="file" name="file_excel" accept=".xlsx,.xls" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-upload-cloud-2-line me-1"></i> Importa Prodotti
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="ajax_loader"></div>


@include('utente.common.footer')

<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3c8dbc!important;
        border-color: #367fa9!important;
        padding: 1px 10px!important;
        color: #fff!important;
    }

    .search-form .position-relative {
        position: relative;
    }

    .search-form .btn-link {
        text-decoration: none;
        color: #6c757d;
    }

    .search-form .btn-link:hover {
        color: #495057;
    }

    .pagination .page-link {
        border-radius: 6px;
        margin: 0 2px;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }
</style>

<script type="text/javascript">

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

    // Template delle opzioni materiali (generato una volta server-side)
    var opzioniMateriali = `<option value="">Nessun Materiale</option>@php
        $last_tipo = null;
        $htmlOpzioni = '';
        foreach($materiali as $m) {
            if($last_tipo !== $m->tipologia) {
                if($m->tipologia == 1) $htmlOpzioni .= '<option disabled>── Materie Prime ──</option>';
                elseif($m->tipologia == 2) $htmlOpzioni .= '<option disabled>── Commerciali / Ricambi ──</option>';
                elseif($m->tipologia == 3) $htmlOpzioni .= '<option disabled>── Semilavorati ──</option>';
                $last_tipo = $m->tipologia;
            }
            $prefisso = '';
            if($m->tipologia == 3) $prefisso = '[SEMI] ' . $m->codice_articolo . ' - ';
            if($m->tipologia == 2) $prefisso = '[COMM] ';
            $htmlOpzioni .= '<option value="'.$m->id.'" costo="'.$m->prezzo.'">'.$prefisso.$m->titolo.' ('.$m->um.')</option>';
        }
        echo $htmlOpzioni;
    @endphp`;

    // Template opzioni fornitori
    var opzioniFornitori = `<option value="">Nessuno</option>@php
        $htmlForn = '';
        foreach($fornitori_db as $f) {
            $htmlForn .= '<option value="'.$f->id.'">'.htmlspecialchars($f->ragione_sociale).'</option>';
        }
        echo $htmlForn;
    @endphp`;

    // Contatori slot per fase
    var slotCounters = {};

    function aggiungiSlotDB(articoloId, faseId) {
        var key = articoloId + '_' + faseId;
        if (!slotCounters[key]) {
            slotCounters[key] = document.querySelectorAll(`#slots_${articoloId}_${faseId} .row`).length;
        }
        var i = slotCounters[key];
        slotCounters[key]++;

        var html = `
        <div class="row mb-1" id="slot_${articoloId}_${faseId}_${i}">
            <div class="col-md-6">
                <select id="db_${articoloId}_${faseId}_${i}" name="materiale[${faseId}][${i}]" class="form-control" style="width:100%;" onchange="calcolaCostoTotale(${articoloId})">
                    ${opzioniMateriali}
                </select>
            </div>
            <div class="col-md-4">
                <select name="fornitore_db[${faseId}][${i}]" class="form-control" style="width:100%;">
                    ${opzioniFornitori}
                </select>
            </div>
            <div class="col-md-2">
                <input id="qta_db_${articoloId}_${faseId}_${i}" type="number" min="0" step="0.0001" name="quantita[${faseId}][${i}]" class="form-control" onkeyup="calcolaCostoTotale(${articoloId})" onchange="calcolaCostoTotale(${articoloId})">
            </div>
        </div>`;

        document.getElementById(`slots_${articoloId}_${faseId}`).insertAdjacentHTML('beforeend', html);
    }

    function calcolaCostoTotale(articoloId) {
        let costoTotale = 0;

        // Cerca tutti i select materiale per questo articolo (qualsiasi fase, qualsiasi slot)
        document.querySelectorAll(`[id^="db_${articoloId}_"]`).forEach(function(materialeSelect) {
            if (materialeSelect.tagName !== 'SELECT') return;
            var idParts = materialeSelect.id.split('_');
            var faseId = idParts[2];
            var slotIdx = idParts[3];
            var quantitaInput = document.getElementById(`qta_db_${articoloId}_${faseId}_${slotIdx}`);

            if (quantitaInput) {
                var costoMateriale = parseFloat(materialeSelect.selectedOptions[0]?.getAttribute('costo')) || 0;
                var quantita = parseFloat(quantitaInput.value) || 0;
                costoTotale += costoMateriale * quantita;
            }
        });

        // Aggiorna il costo totale per l'articolo specifico
        document.getElementById(`costo_materia_prima_totale_${articoloId}`).innerText = costoTotale.toFixed(4);
        ricalcoloPercentualeTotale(articoloId);
    }

    function ricalcoloPercentualeTotale(articoloId) {
        const costoTotale = parseFloat(document.getElementById(`costo_materia_prima_totale_${articoloId}`).innerText) || 0;
        const prezzoVenditaInput = document.getElementById(`prezzo_totale_${articoloId}`).value;
        const prezzoVenditaFinale = parseFloat(prezzoVenditaInput) || 0;

        if (prezzoVenditaFinale > 0) {
            const incidenza = (costoTotale / prezzoVenditaFinale) * 100;
            document.getElementById(`incidenza_totale_${articoloId}`).innerText = incidenza.toFixed(2) + '%';
        } else {
            document.getElementById(`incidenza_totale_${articoloId}`).innerText = '0%';
        }
    }

    function apriAssociaCliente(idArticolo, idCliente) {
        $('#associa_id_articolo').val(idArticolo);
        $('#associa_id_cliente').val(idCliente || '');
        $('#modal_associa_cliente').modal('show');
    }

    function aggiungi(){
        // Mostra la modal
        $('#modal_aggiungi').modal('show');

        // Inizializza select2 all'interno della modal
        $('.js-example-basic-multiple').select2({
            width: '100%' // Aggiungi l'opzione per usare il 100% della larghezza
        });
    }

    function modifica(id){
        $('#modal_modifica_'+id).modal('show');
    }

    function distinta_base(id,prezzo_vendita){
        $('#modal_db_'+id).modal('show');
        calcolaCostoTotale(id);
    }

    function showDistintaBaseTree(articleId) {
        // Recupera i dati della distinta base
        $.ajax({
            url: "<?php echo URL::asset('ajax/get_distinta_base_tree') ?>/" + articleId,
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    let html = buildTreeHtml(response.articolo, response.tree);
                    $('#distinta-tree').html(html);
                    $('#modal_alberatura_db').modal('show');
                }
            }
        });
    }

    function buildTreeHtml(articolo, tree) {
        let html = `
        <div class="mb-4">
            <h4>${articolo.titolo} (${articolo.codice_articolo})</h4>
        </div>
        <div class="tree-container">
    `;

        // Per ogni fase
        for(let fase in tree) {
            html += `
            <div class="tree-branch mb-3">
                <div class="fase-header bg-light p-2 rounded mb-2">
                    <i class="ri-git-branch-line me-2"></i>
                    <strong>${fase}</strong>
                </div>
                <div class="materials-container ps-4">
        `;

            // Per ogni materiale nella fase
            tree[fase].forEach(mat => {
                html += `
                <div class="material-item border-start ps-3 mb-2 position-relative">
                    <div class="connector"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="material-name">${mat.materiale}</span>
                            <small class="text-muted d-block">Cod: ${mat.codice_articolo}</small>
                        </div>
                        <div class="badge bg-primary">
                            ${mat.qta} ${mat.um}
                        </div>
                    </div>
                </div>
            `;
            });

            html += `
                </div>
            </div>
        `;
        }

        html += `</div>`;
        return html;
    }

    window.onload = function() {
        // Per ogni articolo
        <?php foreach($articoli as $a): ?>
        // Per ogni fase dell'articolo che ha una distinta base
            <?php foreach($a->distinta_base as $id_fase => $materiali): ?>
        // Per ogni slot materiale (0-4)
            <?php for($i = 0; $i < 5; $i++): ?>
        // Se esiste un materiale per questo slot
            <?php if(isset($materiali[$i])): ?>
        // Imposta il valore della select
        $('#db_<?php echo $a->id ?>_<?php echo $id_fase ?>_<?php echo $i ?>').val('<?php echo $materiali[$i]->id_materiale ?>');
        // Imposta la quantità
        $('#qta_db_<?php echo $a->id ?>_<?php echo $id_fase ?>_<?php echo $i ?>').val('<?php echo $materiali[$i]->qta ?>');
        <?php endif; ?>
        <?php endfor; ?>
        <?php endforeach; ?>
        // Calcola il costo totale per l'articolo
        calcolaCostoTotale(<?php echo $a->id ?>);
        <?php endforeach; ?>
    };

</script>

<style>
    .tree-container {
        padding: 15px;
    }

    .tree-branch {
        border-left: 2px solid #e9ecef;
        margin-left: 15px;
    }

    .fase-header {
        position: relative;
    }

    .fase-header:before {
        content: '';
        position: absolute;
        left: -17px;
        top: 50%;
        width: 15px;
        height: 2px;
        background-color: #e9ecef;
    }

    .material-item {
        position: relative;
    }

    .material-item:before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        width: 10px;
        height: 2px;
        background-color: #e9ecef;
    }

    .material-name {
        font-weight: 500;
    }

    .connector {
        position: absolute;
        left: -2px;
        top: 50%;
        width: 10px;
        height: 2px;
        background-color: #e9ecef;
    }
</style>