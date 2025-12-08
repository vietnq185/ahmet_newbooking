<?php
if (isset($tpl['status']))
{
    $status = __('status', true);
    switch ($tpl['status'])
    {
        case 2:
            pjUtil::printNotice(NULL, $status[2]);
            break;
    }
} else {
    if (isset($_GET['err']))
    {
        $titles = __('error_titles', true);
        $bodies = __('error_bodies', true);
        pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
    }
    include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
    $week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
    $jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
    $vt = __('voucher_types', true);
    $vv = __('voucher_valids', true);

    pjUtil::printNotice(__('infoVouchersTitle', true, false), __('infoVouchersBody', true, false));
    ?>
    <div class="b10">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
            <input type="hidden" name="controller" value="pjAdminVouchers" />
            <input type="hidden" name="action" value="pjActionCreate" />
            <input type="submit" class="pj-button" value="<?php __('btnAddVoucher'); ?>" />
        </form>
        <form action="" method="get" class="float_left pj-form frm-filter">
            <input type="text" name="q" class="pj-form-field pj-form-field-search w300" placeholder="<?php __('btnSearch', false, true); ?>" />
        </form>
        <div class="float_right t5">
            <a href="#" class="pj-button btn-all"><?php __('lblAll'); ?></a>
            <a href="#" class="pj-button btn-filter btn-status" data-column="valid" data-value="fixed"><?php echo $vv['fixed']; ?></a>
            <a href="#" class="pj-button btn-filter btn-status" data-column="valid" data-value="period"><?php echo $vv['period']; ?></a>
            <a href="#" class="pj-button btn-filter btn-status" data-column="valid" data-value="recurring"><?php echo $vv['recurring']; ?></a>
        </div>
        <br class="clear_both" />
    </div>

    <div id="grid"></div>
    <script type="text/javascript">
        var pjGrid = pjGrid || {};
        pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
        var myLabel = myLabel || {};
        myLabel.code = "<?php __('voucher_code'); ?>";
        myLabel.discount= "<?php __('voucher_discount'); ?>";
        myLabel.type = "<?php __('voucher_type'); ?>";
        myLabel.valid = "<?php __('voucher_valid'); ?>";
        myLabel.amount = "<?php echo $vt['amount']; ?>";
        myLabel.percent = "<?php echo $vt['percent']; ?>";
        myLabel.fixed = "<?php echo $vv['fixed']; ?>";
        myLabel.period = "<?php echo $vv['period']; ?>";
        myLabel.recurring = "<?php echo $vv['recurring']; ?>";
        myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
        myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";
        myLabel.currency = "<?php echo $tpl['option_arr']['o_currency']; ?>";
    </script>
    <?php
}
?>

