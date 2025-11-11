<?php
$pickup_arr = $tpl['transfer_arr'];
$client_name = __('personal_titles_ARRAY_' . $pickup_arr['c_title'], true, false) . ' ' .  $pickup_arr['c_fname'] . ' ' . $pickup_arr['c_lname'];
?>
<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
<table style="width: 100%;">
    <tr>
        <td style="text-align: center; font-size: 144px;"><?= $client_name ?></td>
    </tr>
</table>
