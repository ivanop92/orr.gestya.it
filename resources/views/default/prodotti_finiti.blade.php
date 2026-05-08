@include('default.common.header')

    <div class="container-fluid mt-5">
        <h4>Prodotti Finiti</h4>
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>Titolo</th>
                <th>Descrizione</th>
                <th>Giacenza</th>
                <th>Prezzo</th>
                <th>Azioni</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($articoli as $a)
                <tr>
                    <td>{{ $a->titolo }}</td>
                    <td><small>Descrizione: <?php echo nl2br($a->descrizione) ?></small>
                            <?php foreach($a->distinta_base as $db){ ?>
                        <small><?php echo '<br>'.$db->materiale.' ('.$db->qta.' '.$db->um.')' ?></small>
                        <?php } ?>
                    </td>                    <td>{{ $a->giacenza }} {{ $a->um }}</td>
                    <td>&euro;{{ $a->prezzo }}/{{ $a->um }}</td>
                    <td>
                        <a href="{{ route('distinta_base', $a->id) }}" class="btn btn-sm btn-primary">
                            Distinta Base
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@include('default.common.footer')
