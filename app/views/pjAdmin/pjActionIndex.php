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
}else{
    ?>
    <style type="text/css">
        #abWrapper_<?php echo $controller->getForeignId(); ?> {
            float: left;
        }
        #abWrapper_<?php echo $controller->getForeignId(); ?> table.abCalendarTable{
            margin: 0 0 10px;
            height: 285px !important;
            width: 100% !important;
        }
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarMonth{
            height: 35px !important;
        }
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarMonthPrev a,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarMonthNext a{
            height: 35px !important;
            width: 40px !important;
        }
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarWeekDay,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarWeekNum,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarToday,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarReserved,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarPending,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarPast,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarEmpty,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarDate,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarPendingNightsStart,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarPendingNightsEnd,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarReservedNightsStart,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarReservedNightsEnd,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsReservedReserved,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsReservedPending,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsPendingReserved,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsPendingPending{
            height: 35px !important;
            width: 40px !important;
        }
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarReserved,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarPending,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarPast,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsReservedReserved,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsReservedPending,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsPendingReserved,
        #abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsPendingPending{
            cursor: pointer !important;
        }
        #abWrapper_<?php echo $controller->getForeignId(); ?> .abCalendarLinkDate{
            width: 100% !important;
            height: 100% !important;
            position: relative !important;
            padding: 0px !important;
        }

        #abWrapper_<?php echo $controller->getForeignId(); ?> .abCalendarLinkDateInner{
            width: 100% !important;
            padding: 22% 0 !important;
            top: 0px !important;
            left: 0px !important;
            position: absolute !important;
        }
    	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarCell{
        	cursor: pointer;
		}
    </style>

	<div class="float_left r20">
		<div class="dashboard_header">
			<div class="item">
				<div class="stat today-reservations">
					<div class="info">
						<abbr><?php echo $tpl['cnt_new_reservations'];?></abbr>
						<?php
						if($tpl['cnt_new_reservations'] != 1)
						{
							__('lblNewReservationsToday');
						}else{
							__('lblNewReservationToday');
						} 
						?>
					</div>
				</div>
			</div>
			<div class="item">
				<div class="stat today-transfers">
					<div class="info">
						<abbr><?php echo $tpl['cnt_today_transfers'];?></abbr>
						<?php
						if($tpl['cnt_today_transfers'] != 1)
						{
							__('lblTransfersToday');
						}else{
							echo strtolower(__('lblTransferToday', true, false));
						} 
						?>
					</div>
				</div>
			</div>
		</div>

	    <div class="dashboard_box">
	        <div class="dashboard_top">
	            <div class="dashboard_column_top"><?php __('lblLatestReservations');?> (<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex"><?php __('lblDashViewAll'); ?></a>)</div>
	            <div class="dashboard_column_top"><?php __('lblTransfersToday');?> (<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex&amp;date=<?php echo pjUtil::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format']);?>"><?php __('lblDashViewAll'); ?></a>)&nbsp;&nbsp;&nbsp;<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionPrint&amp;today=yes" target="_blank"><?php __('lblPrint'); ?></a></div>
	        </div>
	        <div class="dashboard_middle">
	            <div class="dashboard_column">
	                <div class="dashboard_list">
	                    <?php
	                    $statuses = __('booking_statuses', true, false);
	                    if(!empty($tpl['latest_arr']))
	                    {
	                        foreach($tpl['latest_arr'] as $v)
	                        {
	                            $client_name_arr = array();
	                            if(!empty($v['c_fname']) || !empty($v['fname']))
	                            {
	                                $client_name_arr[] = !empty($v['client_id']) ? pjSanitize::clean($v['fname']) : pjSanitize::clean($v['c_fname']);
	                            }
	                            if(!empty($v['c_lname']) || !empty($v['lname']))
	                            {
	                                $client_name_arr[] = !empty($v['client_id']) ? pjSanitize::clean($v['lname']) : pjSanitize::clean($v['c_lname']);
	                            }
	                            ?>
	                            <div class="dashboard_row">
	                                <label><span><?php __('lblID')?>: </span><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $v['id'];?>"><?php echo $v['uuid'];?></a></label>
	                                <label><span><?php __('lblCustomer')?>: </span><?php echo join(' ', $client_name_arr);?></label>
	                                <label><span><?php __('lblPickup')?>: </span><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['booking_date'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['booking_date']));?></label>
	                                <?php
	                                if(!empty($v['return_date']))
	                                {
	                                    ?>
	                                    <label><span><?php __('lblReturn')?>: </span><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['return_date'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['return_date']));?></label>
	                                    <?php
	                                }
	                                ?>
	                                <label><span><?php __('lblFrom')?>: </span><?php echo $v['location'];?></label>
	                                <label><span><?php __('lblTo')?>: </span><?php echo $v['dropoff_location'];?></label>
	                                <label><span><?php __('lblDuration')?>: </span><?php echo $v['duration'];?> <?php echo strtolower(__('lblMinutes',true, false));?></label>
	                                <label><span><?php __('lblVehicle')?>: </span><?php echo $v['vehicle'];?></label>
	                            </div>
	                            <?php
	                        }
	                    }else{
	                        ?>
	                        <div class="dashboard_row">
	                            <label><span><?php __('lblReservationsNotFound');?></span></label>
	                        </div>
	                        <?php
	                    }
	                    ?>
	                </div>
	            </div>
	            <div class="dashboard_column">
	                <div class="dashboard_list">
	                    <?php
	                    if(!empty($tpl['today_arr']))
	                    {
	                        foreach($tpl['today_arr'] as $v)
	                        {
	                            $client_name_arr = array();
	                            if(!empty($v['c_fname']) || !empty($v['fname']))
	                            {
	                                $client_name_arr[] = !empty($v['client_id']) ? pjSanitize::clean($v['fname']) : pjSanitize::clean($v['c_fname']);
	                            }
	                            if(!empty($v['c_lname']) || !empty($v['lname']))
	                            {
	                                $client_name_arr[] = !empty($v['client_id']) ? pjSanitize::clean($v['lname']) : pjSanitize::clean($v['c_lname']);
	                            }
	                            ?>
	                            <div class="dashboard_row">
	                                <label><span><?php __('lblID')?>: </span><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo empty($v['return_id']) ? $v['id'] : $v['id2'];?>"><?php echo empty($v['return_id']) ? $v['uuid'] : $v['uuid2'];?></a></label>
	                                <label><span><?php __('lblCustomer')?>: </span><?php echo join(' ', $client_name_arr);?></label>
	                                <label><span><?php empty($v['return_id']) ? __('lblPickup') : __('lblReturn');?>: </span><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['booking_date'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['booking_date']));?></label>
	                                <label><span><?php __('lblFrom')?>: </span><?php echo empty($v['return_id']) ? $v['location'] : $v['location2']; ;?></label>
	                                <label><span><?php __('lblTo')?>: </span><?php echo empty($v['return_id']) ? $v['dropoff_location'] : $v['dropoff_location2'];?></label>
	                                <label><span><?php __('lblDuration')?>: </span><?php echo empty($v['return_id']) ? $v['duration'] : $v['duration2'];?> <?php echo strtolower(__('lblMinutes',true, false));?></label>
	                                <label><span><?php __('lblVehicle')?>: </span><?php echo $v['vehicle'];?></label>
	                                <label class="status <?php echo $v['status'];?>"><?php echo $statuses[$v['status']];?></label>
	                                <div style="clear:both;"></div>
	                            </div>
	                            <?php
	                        }
	                    }else{
	                        ?>
	                        <div class="dashboard_row">
	                            <label><span><?php __('lblReservationsNotFound');?></span></label>
	                        </div>
	                        <?php
	                    }
	                    ?>
	                </div>
	            </div>
	        </div>
	        <div class="dashboard_bottom"></div>
	    </div>
    </div>
	<div class="float_left w300">
	    <div id="abWrapper_<?php echo $controller->getForeignId(); ?>" class="pjDashboardCalendar">
	        <div id="abCalendar_<?php echo $controller->getForeignId(); ?>">
	            <?php include dirname(__FILE__) . '/pjActionGetCal.php'; ?>
	        </div>
	    </div>
	</div>
	<div class="float_right">
		<div class="pjDashboardTotalAmounts"></div>
	</div>
	<br class="clear_both">
	<div class="clear_left t20 overflow">
		<div class="float_left black t30 t20"><span class="gray"><?php echo ucfirst(__('lblDashLastLogin', true)); ?>:</span> <?php echo pjUtil::formatDate(date('Y-m-d', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'H:i:s', $tpl['option_arr']['o_time_format']); ?></div>
		<div class="float_right overflow">
		<?php
		list($hour, $day, $other) = explode("_", date("H:i_l_F d, Y"));
		$days = __('days', true, false);
		?>
			<div class="dashboard_date">
				<abbr><?php echo $days[date('w')]; ?></abbr>
				<?php echo pjUtil::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>
			</div>
			<div class="dashboard_hour"><?php echo $hour; ?></div>
		</div>
	</div>
	<?php
}
?>