<tr>
    <td style="vertical-align:middle" rowspan="1">
        <div class="dayofmonth" id="date"><?php echo $d["date"] ?></div>
    </td>
    <td style="vertical-align:middle">
        <div class="dayofmonth" id="name"><?php echo $d["name"] ?></div>
    </td>
    <td class="Optionen">
        <div class="iconleiste">
            <div class="col-sm-5 nopadleft">
                <form action="admin.php" method="get" onsubmit="return confirm( 'Wollen Sie diese Ankündigung wirklich löschen?');">
                    <button type="submit" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="Löschen">
                        <span class="glyphicon glyphicon-remove"></span>
                    </button>
                    <input name="action" value="list_birthdays" type="hidden">
                    <input name="command" value="delete" type="hidden">
                    <input name="id" value="<?php echo $d["id"] ?>" type="hidden">
                </form>
            </div>
        </div>
    </td>
</tr>