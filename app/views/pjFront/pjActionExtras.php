<?php
$index = pjObject::escapeString($_GET['index']);
$STORE = @$tpl['store'];
?>
<div class="row">
    <!--- Content -->
    <div class="full-width content">
        <header class="f-title color"><?= str_replace('{X}', 3, __('front_step_x', true, false)) ?><?php __('front_step_extras'); ?></header>

        <p><?php __('front_extra_description'); ?></p>
    </div>

    <div class="three-fourth">
        <form id="trExtrasForm_<?php echo $index;?>" action="" method="post">
            <input type="hidden" name="index" value="<?= $index ?>">

            <table>
                <tr>
                    <th><?php __('front_extra'); ?></th>
                    <th></th>
                    <th><?php __('front_count'); ?></th>
                </tr>
                <?php foreach($tpl['extra_arr'] as $extra): ?>
                    <?php
                    $max_qty = isset($tpl['el_arr'][$extra['id']])? $tpl['el_arr'][$extra['id']]: $tpl['option_arr']['o_extras_max_qty'];
                    if($max_qty < 1)
                    {
                        continue;
                    }
                    ?>
                    <tr>
                        <td>
                            <?php
                            if ((float)$extra['price'] > 0) {
                            	echo $extra['name'].' ('.number_format((float)$extra['price'], 2, ',', ' ') . ' ' . $tpl['option_arr']['o_currency'].')';
                            } else {
                            	echo $extra['name'].' ('.__('front_label_free', true).')';
                            }
                            ?>                            
                        </td>
                        <td> <?php if(!empty($extra['info'])): ?>
                                <i><?= $extra['info'] ?></i><?php endif; ?></td>
                        <td>
                            <select name="extras[<?= $extra['id'] ?>]">
                                <?php for($i = 0; $i <= $max_qty; $i++): ?>
                                    <option value="<?= $i ?>"<?= @$STORE['extras'][$extra['id']] == $i? ' selected="selected"': ''; ?>><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <div class="actions">
                <a href="" id="trBtnExtras_<?= $index ?>" class="btn medium color right"><?php __('front_btn_continue'); ?></a>
            </div>
        </form>
    </div>

    <?php include_once PJ_VIEWS_PATH . 'pjFront/elements/cart.php'; ?>
</div>