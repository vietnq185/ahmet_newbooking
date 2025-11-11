<?php
$index = pjObject::escapeString($_GET['index']);
$STORE = @$tpl['store'];

$date = pjUtil::formatDate($STORE['search']['date'], $tpl['option_arr']['o_date_format']);
$dayIndex = $date? date('N', strtotime($date)): null;
?>
<div class="row">
    <!--- Content -->
    <div class="full-width content">
        <header class="f-title color"><?= str_replace('{X}', 2, __('front_step_x', true, false)) ?><?php __('front_step_choose_transfer_type'); ?></header>

        <p><?php __('front_transfer_type_description'); ?></p>
    </div>

    <div class="full-width">
        <div class="results">
            <?php
            if($tpl['status'] == 'OK')
            {
                ?>
                <div class="f-row">
                    <div class="one-half">
                        <table>
                            <tr>
                                <th><?php __('front_transfer_type_one_way'); ?></th>
                                <th class="price"><?php __('front_price'); ?></th>
                            </tr>
                            <tr>
                                <td class="price"><?php __('front_one_way_transfer') ?>:</td>
                                <td class="price"><?php echo number_format(round($tpl['cart']['one_way_price']), 2, ',', ' ');?> <small><?= $tpl['option_arr']['o_currency'] ?></small></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td><a href="#" class="btn medium color trChooseTransferTypeButton" data-is-return="0"><i class="fas fa-check-circle"></i><span><?php __('front_btn_continue', false, false);?></span></a></td>
                            </tr>
                        </table>
                    </div>

                    <div class="one-half">
                        <?php $price_with_return = round($tpl['cart']['one_way_price'] + $tpl['cart']['return_price']); ?>
                        <table>
                            <tr>
                                <th class="price"><?php __('front_transfer_type_return'); ?></th>
                                <th class="price"><?php __('front_price'); ?></th>
                            </tr>
                            <tr>
                                <td class="price"><?php __('front_return_transfer') ?>:</td>
                                <td class="price"><?php echo number_format($price_with_return, 2, ',', ' ');?> <small><?= $tpl['option_arr']['o_currency'] ?></small></td>
                            </tr>
                            <?php if($tpl['fleet']["return_discount_{$dayIndex}"] > 0): ?>
                                <?php
                                $discount = round(($price_with_return * $tpl['fleet']["return_discount_{$dayIndex}"]) / 100);
                                $total = $price_with_return - $discount;
                                ?>
                                <tr>
                                    <td class="price"><?php echo str_replace('{X}', floatval($tpl['fleet']["return_discount_{$dayIndex}"]), __('front_return_transfer_discount', true)) ?>:</td>
                                    <td class="price"><?php echo number_format(-$discount, 2, ',', ' ');?> <small><?= $tpl['option_arr']['o_currency'] ?></small></td>
                                </tr>
                                <tr>
                                    <td class="price"><?php __('front_total_price'); ?>:</td>
                                    <td class="price"><?php echo number_format(round($total), 2, ',', ' ');?> <small><?= $tpl['option_arr']['o_currency'] ?></small></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td>&nbsp;</td>
                                <td><a href="#" class="btn medium color trChooseTransferTypeButton" data-is-return="1"><i class="fas fa-check-circle"></i><span><?php __('front_btn_continue', false, false);?></span></a></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php
            }else{
                ?>
                <div class="trSystemMessage"><?php __('front_error'); ?></div>
                <?php
            }
            ?>
        </div>
    </div>
</div>