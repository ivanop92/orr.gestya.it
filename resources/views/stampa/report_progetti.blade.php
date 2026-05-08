<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Report Tracciabilita <?php echo $odl->numero ?></title>


    <style>

        body{
            font-size:12px;
        }

        tr.header th {
            border:1px solid black;
            text-align:center;
            font-size:16px;
        }

        tbody tr.border_bottom td {
            border:1px solid black;
            height:30px;
            font-size:15px;
            text-align: left;
            padding-left:10px;
        }

        table {
            border-collapse: collapse;
        }
    </style>


</head>

<body>
<h1 style="margin-bottom:0;">Report Tracciabilita <?php echo $odl->numero ?> </h1>

<table  style="width:100%">

    <thead>
        <tr>
            <th style="width:200px;">Data</th>
            <th>Causale</th>
            <th>Articolo</th>
            <th style="width:150px">Qta</th>
            <th style="width:50px">Lotto</th>
            <th style="width:150px">Scadenza</th>
        </tr>
    </thead>

    <tbody>
    <?php foreach($mgmov as $mgm){ ?>
    <tr class="border_bottom" >
        <td><?php echo date('d/m/Y H:i:s',strtotime($mgm->datamov)) ?></td>
        <td><?php echo $mgm->causale ?></td>
        <td><?php echo $mgm->titolo ?></td> <!-- Nome dell'articolo -->
        <td><?php echo $mgm->qta ?></td>
        <td><?php echo $mgm->lotto ?></td>
        <td><?php echo ($mgm->scadenza_lotto != '') ? date('d/m/Y',strtotime($mgm->scadenza_lotto)) : '' ?></td>
    </tr>
    <?php } ?>

    </tbody>

</table>


</body>
</html>