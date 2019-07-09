<tr>
    <td style="vertical-align:middle" rowspan="1">
        <div class="dayofmonth" id="startday"><?php echo date('d.m.Y', strtotime($msg["start"])) ?></div>
    </td>
    <td style="vertical-align:middle">
        <div class="dayofmonth" id="endday"><?php echo date('d.m.Y', strtotime($msg["end"])) ?></div>
    </td>
    <td style="vertical-align:middle">
        <div class="agenda-event"><?php echo $msg["message"] ?></div>
    </td>
    <td class="Optionen">
        <div class="iconleiste">
            <div class="col-sm-4 nopadleft">
                <form action="admin.php" method="get" onsubmit="return confirm( 'Wollen Sie diese Ankündigung wirklich löschen?');">
                    <button type="submit" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="Löschen">
                        <span class="glyphicon glyphicon-remove"></span>
                    </button>
                    <input name="action" value="list_messages" type="hidden">
                    <input name="command" value="delete" type="hidden">
                    <input name="id" value="<?php echo $msg["id"] ?>" type="hidden">
                </form>
            </div>
        </div>
    </td>
</tr>