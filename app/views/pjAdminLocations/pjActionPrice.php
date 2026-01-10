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
	$days = __('days', true);
	$days[7] = $days[0];
	unset($days[0]);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top<?php echo $_GET['action'] == 'pjActionUpdate' ? ' ui-tabs-active ui-state-active' : null;?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblDetails'); ?></a></li>
			<li class="ui-state-default ui-corner-top<?php echo $_GET['action'] == 'pjActionPrice' ? ' ui-tabs-active ui-state-active' : null;?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionPrice&amp;id=<?php echo $tpl['arr']['id']?>"><?php __('lblPrices'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoUpdatePriceTitle', true, false), __('infoUpdatePriceDesc', true, false));
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionPrice&amp;id=<?php echo $tpl['arr']['id']?>" method="post" id="frmUpdatePrice" class="pj-form form">
		<input type="hidden" name="location_update" value="1" />
		<input type="hidden" id="id" name="id" value="<?php echo $tpl['arr']['id']?>" />
		<div class="trFromLocation"><?php __('lblFromPickupLocation');?>: <?php echo $tpl['arr']['pickup_location']?></div>

        <?php
        $col_width = 160;
        $number_of_vehicles = count($tpl['fleet_arr']);
        $number_of_locations = count($tpl['dropoff_arr']);
        ?>
        <?php foreach($days as $dayIndex => $dayName): ?>
            <div class="trFromLocation" style="display: none;"><?= $dayName ?></div>
            <div class="pj-location-grid" style="display: <?php echo $dayIndex > 1 ? 'none' : NULL;?>">
                <?php
                if($number_of_locations > 0 && $number_of_vehicles > 0)
                {
                    ?>
                    <div class="pj-first-column">
                        <table cellpadding="0" cellspacing="0" border="0" class="display">
                            <thead>
                                <tr class="title-head-row">
                                    <th><?php __('lblToDropoffLocation');?>:</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach($tpl['dropoff_arr'] as $k => $v)
                                {
                                    ?>
                                    <tr class="title-row" lang="<?php echo $v['id']; ?>">
                                        <td><?php echo pjSanitize::clean($v['location'])?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pj-location-column">
                        <div class="wrapper1">
                            <div class="div1-compare" style="width: <?php echo $col_width * $number_of_vehicles; ?>px;"></div>
                        </div>
                        <div class="wrapper2">
                            <div class="div2-compare" style="width: <?php echo $col_width * $number_of_vehicles; ?>px;">
                                <table cellpadding="0" cellspacing="0" border="0" class="display" id="compare_table" width="<?php echo $col_width * $number_of_vehicles; ?>px">
                                    <thead>
                                        <tr class="content-head-row">
                                            <?php
                                            $j = 1;
                                            foreach($tpl['fleet_arr'] as $v)
                                            {
                                                ?>
                                                <th class="<?php echo $j == 1 ? 'first-col' : null;?>" width="<?php echo $col_width;?>px">
                                                    <?php echo pjSanitize::clean($v['fleet'])?>
                                                    <div><?php echo pjSanitize::clean($v['station']);?></div>
                                                </th>
                                                <?php
                                                $j++;
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach($tpl['dropoff_arr'] as $k => $row)
                                        {
                                            ?>
                                            <tr class="content_row_<?php echo $row['id']; ?>" class="">
                                                <?php
                                                $j = 1;
                                                foreach($tpl['fleet_arr'] as $col)
                                                {
                                                    $pair_id = $row['id'] . '_' . $col['id'];
                                                    ?>
                                                    <td class="<?php echo $j == 1 ? 'first-col' : null;?>" >
                                                        <?php
                                                        if (isset($tpl['dropoff_station_ids_arr'][$row['id']]) && in_array($col['station_id'], $tpl['dropoff_station_ids_arr'][$row['id']])) { 
                                                            ?>
                                                                <span class="pj-form-field-custom pj-form-field-custom-before">
                                                                    <span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
                                                                    <input type="text" name="price_<?php echo $pair_id;?>[<?= $dayIndex ?>]" class="pj-form-field number pj-grid-field w50" value="<?php echo isset($tpl['price_arr'][$pair_id][$dayIndex]) ? $tpl['price_arr'][$pair_id][$dayIndex] : null;?>" />
                                                                </span>
                                                            <?php
                                                        } else {
                                                            echo '&nbsp;';   
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    $j++;
                                                }
                                                ?>
                                            </tr>
                                            <?php

                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
		<?php endforeach; ?>
        <p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminLocations&action=pjActionIndex';" />
		</p>
		<div id="priceErrorMessage" style="height: 0 !important; display: none; overflow: hidden"></div>
	</form>
	
	<div id="dialogPricesStatus" title="<?php echo pjSanitize::html(__('lblStatusTitle', true)); ?>" style="display: none">
		<span class="bxPriceStatus bxPriceStatusStart" style="display: none"><?php __('lblPriceStatusStart'); ?></span>
		<span class="bxPriceStatus bxPriceStatusEnd" style="display: none"><?php __('lblPriceStatusEnd'); ?></span>
		<span class="bxPriceStatus bxPriceStatusFail" style="display: none"><?php __('lblPriceStatusFail'); ?></span>
	</div>
	<script type="text/javascript">
	
	var myLabel = myLabel || {};
	
	</script>
	<?php
}
?>