<tr>
    <td class="agenda-date" rowspan="1">
        <div class="dayofmonth"><?php echo date('d', strtotime($a["date"]))?></div>
        <div class="dayofweek"><?php echo date('D', strtotime($a["date"]))?></div>
        <div class="shortdate text-muted"><?php echo date('M', strtotime($a["date"]))?></div>
    </td>
    <td class="agenda-time"><?php echo $a["time"]?></td>
    <td class="agenda-events">
        <div class="agenda-event"><?php echo $a["title"]?>
            <br>Ort: <?php echo $a["location"]?></div>
    </td>
    <td class="agenda-date agenda-end">
        <div class="dayofmonth"><?php echo date('d', strtotime($a["end"]))?></div>
        <div class="dayofweek"><?php echo date('D', strtotime($a["end"]))?></div>
        <div class="shortdate text-muted"><?php echo date('M', strtotime($a["end"]))?></div>
    </td>

    <td class="Optionen">
        <div class="iconleiste">
            <div class="col-sm-4 nopadleft">
                <form action="admin.php" method="get">
                    <button type="submit" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Bearbeiten"><span class="glyphicon glyphicon-edit"></span></button>
                    <input name="action" value="edit_appointment" type="hidden">
                    <input name="command" value="edit" type="hidden">
                    <input name="id" value="<?php echo $a["id"]?>" type="hidden">
                </form>
            </div>
            <div class="col-sm-4 nopadleft">
                <form action="admin.php" method="get" 
                    onsubmit="return confirm("Wollen Sie diesen Termin wirklich löschen?");">
                    <button type="submit" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Löschen"><span class="glyphicon glyphicon-remove"></span></button>
                    <input name="action" value="list_appointments" type="hidden">
                    <input name="command" value="delete" type="hidden">
                    <input name="id" value="<?php echo $a["id"]?>" type="hidden">
                </form>
            </div>
        </div>
    </td>
</tr>
