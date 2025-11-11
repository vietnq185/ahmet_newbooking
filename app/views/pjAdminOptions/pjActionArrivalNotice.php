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
        $week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
        $jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
        $jqTimeFormat = pjUtil::jqTimeFormat($tpl['option_arr']['o_time_format']);

        if (isset($_GET['err']))
        {
            $titles = __('error_titles', true);
            $bodies = __('error_bodies', true);
            pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
        }
        include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
        ?>
        <div class="clear_both">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionArrivalNotice" method="post" class="form pj-form">
                <input type="hidden" name="update" value="1" />

                <table class="pj-table" data-paste-certain-date="1">
                    <thead>
                        <tr>
                            <th class="w300"><?php __('lblFrom'); ?></th>
                            <th class="w300"><?php __('lblTo'); ?></th>
                            <th class="w30"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tpl['certainDates'])): ?>
                            <?php foreach ($tpl['certainDates'] as $certainDate): ?>
                                <tr>
                                    <td>
                                        <span class="block overflow">
                                            <span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
                                                <input type="text" name="certain_dates[<?php echo $certainDate['id']; ?>][date_from]" class="pj-form-field pointer w120 datepick required" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>" value="<?php echo pjUtil::formatDate($certainDate['date_from'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>"/>
                                                <span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
                                            </span>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="block overflow">
                                            <span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
                                                <input type="text" name="certain_dates[<?php echo $certainDate['id']; ?>][date_to]" class="pj-form-field pointer w120 datepick required" data-datepicker="1" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>" value="<?php echo pjUtil::formatDate($certainDate['date_to'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>"/>
                                                <span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
                                            </span>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="javascript:;" class="pj-table-icon-delete" data-remove-certain-date="1"></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <p>
                    <button type="button" class="pj-button" data-add-certain-date="1"><?php __('btnAdd'); ?></button>
                    <button type="submit" class="pj-button"><?php __('btnSave'); ?></button>
                </p>
            </form>

            <table data-copy-certain-date="1" style="display: none;">
                <tbody>
                    <tr>
                        <td>
                            <span class="block overflow">
                                <span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
                                    <input type="text" name="certain_dates[{INDEX}][date_from]" class="pj-form-field pointer w120 required" data-datepicker="1" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>" value=""/>
                                    <span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
                                </span>
                            </span>
                        </td>
                        <td>
                            <span class="block overflow">
                                <span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
                                    <input type="text" name="certain_dates[{INDEX}][date_to]" class="pj-form-field pointer w120 required" data-datepicker="1" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>" value=""/>
                                    <span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
                                </span>
                            </span>
                        </td>
                        <td>
                            <a href="javascript:;" class="pj-table-icon-delete" data-remove-certain-date="1"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
?>