<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjApiSync extends pjAppController
{	
	public function __construct()
	{
		$this->setLayout('pjActionEmpty');
	}
	
	public function beforeFilter()
	{
		$OptionModel = pjOptionModel::factory();
		$this->option_arr = $OptionModel->getPairs($this->getForeignId());
		$this->set('option_arr', $this->option_arr);
		$this->setTime();

		$is_forced = false;
		if (!isset($_SESSION[$this->defaultLocale]))
		{
			if($_GET['action'] != 'pjActionLoadCss' && $_GET['action'] != 'pjActionLoadCssNew')
			{
				if(isset($_GET['locale']) && (int) $_GET['locale'] > 0)
				{
					$this->setLocaleId($_GET['locale']);
					$this->loadSetFields(true);
					$is_forced = true;
				}else{
					$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
					if (count($locale_arr) === 1)
					{
						$this->setLocaleId($locale_arr[0]['id']);
					}
				}
			}
		}else{
			if(isset($_GET['locale']) && (int) $_GET['locale'] > 0 && $_SESSION[$this->defaultLocale] != $_GET['locale'])
			{
				$this->setLocaleId($_GET['locale']);
				$this->loadSetFields(true);
				$is_forced = true;
			}
		}
		if($is_forced == false)
		{
			$this->loadSetFields(true);
		}
	}
	
	public function pjActionPushGeneralData()
    {
    	$pjClientModel = pjClientModel::factory();
    	$pjDriverModel = pjDriverModel::factory();
    	$pjVoucherModel = pjVoucherModel::factory();
    	$pjExtraModel = pjExtraModel::factory();
    	$pjFleetModel = pjFleetModel::factory();
    	$pjLocationModel = pjLocationModel::factory();
    	$pjPriceModel = pjPriceModel::factory();
    	$pjStationModel = pjStationModel::factory();
    	$pjAreaModel = pjAreaModel::factory();
    	
    	$data = array();
    	$data['domain'] = PJ_INSTALL_URL;
    	if (isset($_REQUEST['last_update_time'])) {
    		$last_update_time = date('Y-m-d H:i:s', $_REQUEST['last_update_time']);
    		$pjClientModel->where('t1.modified > "'.$last_update_time.'"');
    		$pjDriverModel->where('t1.modified > "'.$last_update_time.'"');
    		$pjVoucherModel->where('t1.modified > "'.$last_update_time.'"');
    		$pjExtraModel->where('t1.modified > "'.$last_update_time.'"');
    		$pjFleetModel->where('t1.modified > "'.$last_update_time.'"');
    		$pjLocationModel->where('t1.modified > "'.$last_update_time.'"');
    		$pjPriceModel->where('t1.modified > "'.$last_update_time.'"');
    		$pjStationModel->where('t1.modified > "'.$last_update_time.'"');
    		$pjAreaModel->where('t1.modified > "'.$last_update_time.'"');
    	}
    	/* Clients */
       	$data['client_arr'] = $pjClientModel->findAll()->getData();
       	
       	/* Drivers */
       	$data['driver_arr'] = $pjDriverModel->findAll()->getData();
       	
       	/* Vouchers */
       	$data['voucher_arr'] = $pjVoucherModel->findAll()->getData();
       	
       	/* Extras */
       	$extra_arr = $pjExtraModel->findAll()->getData();
       	foreach ($extra_arr as $i => $ex) {
       		$extra_arr[$i]['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($ex['id'], 'pjExtra');
       		$extra_arr[$i]['limitations'] = pjExtraLimitationModel::factory()->reset()->where('t1.extra_id', $ex['id'])->findAll()->getData();
       	}
       	$data['extra_arr'] = $extra_arr;
       	
       	/* Fleets */
       	$fleet_ids = $fleet_discount_ids = array();
       	$fleet_discount_arr = $fleet_discount_period_arr = $fleet_fee_arr = array();
       	$fleet_arr = $pjFleetModel->findAll()->getData();
       	foreach ($fleet_arr as $i => $fleet) {
       		$fleet_arr[$i]['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($fleet['id'], 'pjFleet');
       		$fleet_ids[] = $fleet['id'];
       	}
       	if (count($fleet_ids) > 0) {
       		$_fleet_discount_arr = pjFleetDiscountModel::factory()->whereIn('t1.fleet_id', $fleet_ids)->findAll()->getData();
       		foreach ($_fleet_discount_arr as $fd) {
       			$fleet_discount_ids[] = $fd['id'];
       			$fleet_discount_arr[$fd['fleet_id']][] = $fd;
       		}
       		if ($fleet_discount_ids) {
       			$_fleet_discount_perio_arr = pjFleetDiscountPeriodModel::factory()->whereIn('t1.fleet_discount_id', $fleet_discount_ids)->findAll()->getData();
       			foreach ($_fleet_discount_perio_arr as $val) {
       				$fleet_discount_period_arr[$val['fleet_discount_id']][] = $val;
       			}
       		}
       		
       		$_fleet_fee_arr = pjFleetFeeModel::factory()->whereIn('t1.fleet_id', $fleet_ids)->findAll()->getData();
       		foreach ($_fleet_fee_arr as $fee) {
       			$fleet_fee_arr[$fee['fleet_id']][] = $fee;
       		}
       	}
		foreach ($fleet_arr as $i => $fleet) {
       		$discount_arr = isset($fleet_discount_arr[$fleet['id']]) ? $fleet_discount_arr[$fleet['id']] : array();
       		foreach ($discount_arr as $k => $val) {
       			if (isset($fleet_discount_period_arr[$val['id']])) {
       				$discount_arr[$k]['period'] = $fleet_discount_period_arr[$val['id']]; 
       			}
       		}
       		$fleet_arr[$i]['discount_arr'] = $discount_arr;
       		$fleet_arr[$i]['fee_arr'] = isset($fleet_fee_arr[$fleet['id']]) ? $fleet_fee_arr[$fleet['id']] : array();
       	}
       	$data['fleet_arr'] = $fleet_arr;
       	
       	/* Locations */
       	$location_ids = array();
       	$location_arr = $pjLocationModel->findAll()->getData();
       	foreach ($location_arr as $i => $loc) {
       		$location_arr[$i]['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($loc['id'], 'pjLocation');
       		$location_ids[] = $loc['id'];
       	}
       	$dropoff_arr = array();
       	if (count($location_ids) > 0) {
       		$_dropoff_arr = pjDropoffModel::factory()->whereIn('t1.location_id', $location_ids)->findAll()->getData();
       		foreach ($_dropoff_arr as $i => $drop) {
       			$drop['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($drop['id'], 'pjDropoff');
       			$drop['areas'] = pjDropoffAreaModel::factory()->reset()->where('t1.dropoff_id', $drop['id'])->findAll()->getData();
       			$dropoff_arr[$drop['location_id']][] = $drop;
       		}
       	}
       	foreach ($location_arr as $i => $loc) {
       		$location_arr[$i]['dropoff_arr'] = isset($dropoff_arr[$loc['id']]) ? $dropoff_arr[$loc['id']] : array();
       	}
       	$data['location_arr'] = $location_arr;
       	
       	/* Prices */
       	$data['price_arr'] = $pjPriceModel->findAll()->getData();
       	
       	/* Area */
    	$area_ids = array();
       	$area_arr = $pjAreaModel->findAll()->getData();
       	foreach ($area_arr as $i => $area) {
       		$area_arr[$i]['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($area['id'], 'pjArea');
       		$area_ids[] = $area['id'];
       	}
    	$area_coord_arr = array();
       	if (count($area_ids) > 0) {
       		$_area_coord_arr = pjAreaCoordModel::factory()->whereIn('t1.area_id', $area_ids)->findAll()->getData();
       		foreach ($_area_coord_arr as $i => $coord) {
       			$coord['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($coord['id'], 'pjAreaCoord');
       			$area_coord_arr[$coord['area_id']][] = $coord;
       		}
       	}
       	foreach ($area_arr as $i => $area) {
       		$area_arr[$i]['coords'] = isset($area_coord_arr[$area['id']]) ? $area_coord_arr[$area['id']] : array();
       	}
       	$data['area_arr'] = $area_arr;
       	
    	/* Station */
       	$station_ids = array();
       	$station_arr = $pjStationModel->findAll()->getData();
       	foreach ($station_arr as $i => $station) {
       		$station_arr[$i]['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($station['id'], 'pjStation');
       		$station_ids[] = $station['id'];
       	}
       	$station_fee_arr = array();
       	if (count($station_ids) > 0) {
       		$_station_fee_arr = pjStationFeeModel::factory()->reset()->whereIn('t1.station_id', $station_ids)->findAll()->getData();
       		foreach ($_station_fee_arr as $i => $sf) {
       			$station_fee_arr[$sf['station_id']][] = $sf;
       		}
       	}
       	foreach ($station_arr as $i => $station) {
       		$station_arr[$i]['fee_arr'] = isset($station_fee_arr[$station['id']]) ? $station_fee_arr[$station['id']] : array();
       	}
       	$data['station_arr'] = $station_arr;
    	       	       	
    	return pjAppController::jsonResponse($data);
    }
    
    public function pjActionPushBookingData()
    {
    	$data = array();
    	$data['domain'] = PJ_INSTALL_URL;
    	
    	/* Bookings */
       	$booking_ids = array();
       	$booking_arr = pjBookingModel::factory()->where('t1.status', 'confirmed')->findAll()->getData();
       	foreach ($booking_arr as $i => $booking) {
       		foreach ($booking as $k => $v) {
				if (empty($v)) {
					$booking[$k] = ':NULL';
				}
			}
			$booking_arr[$i] = $booking;
       		$booking_ids[] = $booking['id'];
       	}
       	if ($booking_ids) {
       		$be_arr = pjBookingExtraModel::factory()->whereIn('t1.booking_id', $booking_ids)->findAll()->getData();
       		$bp_arr = pjBookingPaymentModel::factory()->whereIn('t1.booking_id', $booking_ids)->findAll()->getData();
       		$booking_extra_arr = $booking_payment_arr = array();
       		foreach ($be_arr as $be) {
       			$booking_extra_arr[$be['booking_id']][] = $be;
       		}
       		foreach ($bp_arr as $bp) {
       			$booking_payment_arr[$bp['booking_id']][] = $bp;
       		}
       		foreach ($booking_arr as $i => $booking) {
       			$booking_arr[$i]['booking_extra_arr'] = isset($booking_extra_arr[$booking['id']]) ? $booking_extra_arr[$booking['id']] : array();
       			$booking_arr[$i]['booking_payment_arr'] = isset($booking_payment_arr[$booking['id']]) ? $booking_payment_arr[$booking['id']] : array();
       		}
       	}
       	$data['booking_arr'] = $booking_arr;
       	
    	return pjAppController::jsonResponse($data);
    }
    
	static public function syncBooking($id, $action, $option_arr) {
		$pjHttp = new pjHttp();
		switch ($action){
			case 'create':
			case 'update':
				$data = pjBookingModel::factory()->find($id)->getData();
				foreach ($data as $k => $v) {
					if (empty($v)) {
						$data[$k] = ':NULL';
					}
				}
				if ($action == 'create' && $data['payment_method'] == 'saferpay') {
				    $data['driver_payment_status'] = 8;
				}
				$be_arr = pjBookingExtraModel::factory()->where('t1.booking_id', $id)->findAll()->getData();
	       		$bp_arr = pjBookingPaymentModel::factory()->where('t1.booking_id', $id)->findAll()->getData();
	       		$data['booking_extra_arr'] = $be_arr;
       			$data['booking_payment_arr'] = $bp_arr;
       			$data['sync_action'] = $action;	       	
       			$data['domain'] = PJ_INSTALL_URL;
       			$data['platform'] = 'newsystem';
				$pjHttp->setData($data);
		        $pjHttp->setMethod('POST');
				break;
			case 'cancel':
				$data = array(
					'sync_action' => $action,
					'domain' => PJ_INSTALL_URL,
					'booking_ids' => $id
				);
				$pjHttp->setData($data);
		        $pjHttp->setMethod('POST');
				break;
			case 'delete':
				$data = array(
					'sync_action' => $action,
					'domain' => PJ_INSTALL_URL,
					'booking_ids' => $id
				);
				$pjHttp->setData($data);
		        $pjHttp->setMethod('POST');
				break;
		}
		$pjHttp->curlRequest($option_arr['o_driver_script_path'].'/index.php?controller=pjApiSync&action=syncBooking');
		$response = $pjHttp->getResponse();
	    $resp = json_decode($response, true);
	    return $resp;
    }
    
	public function pjActionInitData()
    {
    	$type = 'booking';
    	if (isset($_REQUEST['type']) && !empty($_REQUEST['type'])) {
    		$type = $_REQUEST['type'];
    	}
    	$rowCount = isset($_REQUEST['row_count']) && (int)$_REQUEST['row_count'] > 0 ? (int)$_REQUEST['row_count'] : 1000;
    	$data = array();
    	$data['domain'] = PJ_INSTALL_URL;
    	$data['platform'] = 'newsystem';
    	switch ($type) {
    		case 'client':
    			$pjClientModel = pjClientModel::factory();
    			$total = $pjClientModel->findCount()->getData();
				$pages = ceil($total / $rowCount);
				if (!isset($_REQUEST['is_count_page']) || (isset($_REQUEST['is_count_page']) && (int)$_REQUEST['is_count_page'] == 0)) {
    				$page = isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
    				$offset = ((int) $page - 1) * $rowCount;
        			$data['data'] = $pjClientModel->limit($rowCount, $offset)->findAll()->getData();
				}
    			$data['total_records'] = $total;
    			$data['total_pages'] = $pages;
    			$data['status'] = 'OK';
    		break; 
    		case 'driver':
    			$pjDriverModel = pjDriverModel::factory();
    			$total = $pjDriverModel->findCount()->getData();
				$pages = ceil($total / $rowCount);
				if (!isset($_REQUEST['is_count_page']) || (isset($_REQUEST['is_count_page']) && (int)$_REQUEST['is_count_page'] == 0)) {
    				$page = isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
    				$offset = ((int) $page - 1) * $rowCount;
        			$data['data'] = $pjDriverModel->limit($rowCount, $offset)->findAll()->getData();
				}
    			$data['total_records'] = $total;
    			$data['total_pages'] = $pages;
    			$data['status'] = 'OK';
    		break;
    		case 'voucher':
    			$pjVoucherModel = pjVoucherModel::factory();
    			$total = $pjVoucherModel->findCount()->getData();
				$pages = ceil($total / $rowCount);
				if (!isset($_REQUEST['is_count_page']) || (isset($_REQUEST['is_count_page']) && (int)$_REQUEST['is_count_page'] == 0)) {
    				$page = isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
    				$offset = ((int) $page - 1) * $rowCount;
        			$data['data'] = $pjVoucherModel->limit($rowCount, $offset)->findAll()->getData();
				}
    			$data['total_records'] = $total;
    			$data['total_pages'] = $pages;
    			$data['status'] = 'OK';
    		break;  
    		case 'extra':
    			$pjExtraModel = pjExtraModel::factory();
    			$total = $pjExtraModel->findCount()->getData();
				$pages = ceil($total / $rowCount);
				if (!isset($_REQUEST['is_count_page']) || (isset($_REQUEST['is_count_page']) && (int)$_REQUEST['is_count_page'] == 0)) {
    				$page = isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
    				$offset = ((int) $page - 1) * $rowCount;
    				$extra_arr = $pjExtraModel->limit($rowCount, $offset)->findAll()->getData();
    		    	foreach ($extra_arr as $i => $ex) {
    		       		$extra_arr[$i]['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($ex['id'], 'pjExtra');
    		       		$extra_arr[$i]['limitations'] = pjExtraLimitationModel::factory()->reset()->where('t1.extra_id', $ex['id'])->findAll()->getData();
    		       	}
        			$data['data'] = $extra_arr;
				}
    			$data['total_records'] = $total;
    			$data['total_pages'] = $pages;
    			$data['status'] = 'OK';
    		break;  
    		case 'fleet':
    			$pjFleetModel = pjFleetModel::factory();
    			$total = $pjFleetModel->findCount()->getData();
				$pages = ceil($total / $rowCount);
				if (!isset($_REQUEST['is_count_page']) || (isset($_REQUEST['is_count_page']) && (int)$_REQUEST['is_count_page'] == 0)) {
    				$page = isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
    				$offset = ((int) $page - 1) * $rowCount;
    				$fleet_arr = $pjFleetModel->limit($rowCount, $offset)->findAll()->getData();
    		    	$fleet_ids = $fleet_discount_ids = array();
    		       	$fleet_discount_arr = $fleet_discount_period_arr = $fleet_fee_arr = array();
    		       	foreach ($fleet_arr as $i => $fleet) {
    		       		$fleet_arr[$i]['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($fleet['id'], 'pjFleet');
    		       		$fleet_ids[] = $fleet['id'];
    		       	}
    		       	if (count($fleet_ids) > 0) {
    		       		$_fleet_discount_arr = pjFleetDiscountModel::factory()->whereIn('t1.fleet_id', $fleet_ids)->findAll()->getData();
    		       		foreach ($_fleet_discount_arr as $fd) {
    		       			$fleet_discount_ids[] = $fd['id'];
    		       			$fleet_discount_arr[$fd['fleet_id']][] = $fd;
    		       		}
    		       		if ($fleet_discount_ids) {
    		       			$_fleet_discount_perio_arr = pjFleetDiscountPeriodModel::factory()->whereIn('t1.fleet_discount_id', $fleet_discount_ids)->findAll()->getData();
    		       			foreach ($_fleet_discount_perio_arr as $val) {
    		       				$fleet_discount_period_arr[$val['fleet_discount_id']][] = $val;
    		       			}
    		       		}
    		       		
    		       		$_fleet_fee_arr = pjFleetFeeModel::factory()->whereIn('t1.fleet_id', $fleet_ids)->findAll()->getData();
    		       		foreach ($_fleet_fee_arr as $fee) {
    		       			$fleet_fee_arr[$fee['fleet_id']][] = $fee;
    		       		}
    		       	}
    				foreach ($fleet_arr as $i => $fleet) {
    		       		$discount_arr = isset($fleet_discount_arr[$fleet['id']]) ? $fleet_discount_arr[$fleet['id']] : array();
    		       		foreach ($discount_arr as $k => $val) {
    		       			if (isset($fleet_discount_period_arr[$val['id']])) {
    		       				$discount_arr[$k]['period'] = $fleet_discount_period_arr[$val['id']]; 
    		       			}
    		       		}
    		       		$fleet_arr[$i]['discount_arr'] = $discount_arr;
    		       		$fleet_arr[$i]['fee_arr'] = isset($fleet_fee_arr[$fleet['id']]) ? $fleet_fee_arr[$fleet['id']] : array();
    		       	}
        			$data['data'] = $fleet_arr;
				}
    			$data['total_records'] = $total;
    			$data['total_pages'] = $pages;
    			$data['status'] = 'OK';
    		break;  
    		case 'location':
    			$pjLocationModel = pjLocationModel::factory();
    			$total = $pjLocationModel->findCount()->getData();
				$pages = ceil($total / $rowCount);
				if (!isset($_REQUEST['is_count_page']) || (isset($_REQUEST['is_count_page']) && (int)$_REQUEST['is_count_page'] == 0)) {
    				$page = isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
    				$offset = ((int) $page - 1) * $rowCount;
    				$location_arr = $pjLocationModel->limit($rowCount, $offset)->findAll()->getData();
    		    	$location_ids = array();
    		       	foreach ($location_arr as $i => $loc) {
    		       		$location_arr[$i]['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($loc['id'], 'pjLocation');
    		       		$location_ids[] = $loc['id'];
    		       	}
    		       	$dropoff_arr = array();
    		       	if (count($location_ids) > 0) {
    		       		$_dropoff_arr = pjDropoffModel::factory()->whereIn('t1.location_id', $location_ids)->findAll()->getData();
    		       		foreach ($_dropoff_arr as $i => $drop) {
    		       			$drop['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($drop['id'], 'pjDropoff');
    		       			$drop['areas'] = pjDropoffAreaModel::factory()->reset()->where('t1.dropoff_id', $drop['id'])->findAll()->getData();
    		       			$dropoff_arr[$drop['location_id']][] = $drop;
    		       		}
    		       	}
    		       	foreach ($location_arr as $i => $loc) {
    		       		$location_arr[$i]['dropoff_arr'] = isset($dropoff_arr[$loc['id']]) ? $dropoff_arr[$loc['id']] : array();
    		       	}
        			$data['data'] = $location_arr;
				}
    			$data['total_records'] = $total;
    			$data['total_pages'] = $pages;
    			$data['status'] = 'OK';
    		break;	
    		case 'area':
    		    $pjAreaModel = pjAreaModel::factory();
    		    $total = $pjAreaModel->findCount()->getData();
    		    $pages = ceil($total / $rowCount);
    		    if (!isset($_REQUEST['is_count_page']) || (isset($_REQUEST['is_count_page']) && (int)$_REQUEST['is_count_page'] == 0)) {
    		        $page = isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
    		        $offset = ((int) $page - 1) * $rowCount;
    		        $area_ids = array();
    		        $area_arr = $pjAreaModel->limit($rowCount, $offset)->findAll()->getData();
    		        foreach ($area_arr as $i => $area) {
    		            $area_arr[$i]['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($area['id'], 'pjArea');
    		            $area_ids[] = $area['id'];
    		        }
    		        $area_coord_arr = array();
    		        if (count($area_ids) > 0) {
    		            $_area_coord_arr = pjAreaCoordModel::factory()->whereIn('t1.area_id', $area_ids)->findAll()->getData();
    		            foreach ($_area_coord_arr as $i => $coord) {
    		                $coord['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($coord['id'], 'pjAreaCoord');
    		                $area_coord_arr[$coord['area_id']][] = $coord;
    		            }
    		        }
    		        foreach ($area_arr as $i => $area) {
    		            $area_arr[$i]['coords'] = isset($area_coord_arr[$area['id']]) ? $area_coord_arr[$area['id']] : array();
    		        }
    		        $data['data'] = $area_arr;
    		    }
    		    $data['total_records'] = $total;
    		    $data['total_pages'] = $pages;
    		    $data['status'] = 'OK';
    		    break;
    		case 'station':
    		    $pjStationModel = pjStationModel::factory();
    		    $total = $pjStationModel->findCount()->getData();
    		    $pages = ceil($total / $rowCount);
    		    if (!isset($_REQUEST['is_count_page']) || (isset($_REQUEST['is_count_page']) && (int)$_REQUEST['is_count_page'] == 0)) {
    		        $page = isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
    		        $offset = ((int) $page - 1) * $rowCount;    		        
    		        $station_ids = array();
    		        $station_arr = $pjStationModel->limit($rowCount, $offset)->findAll()->getData();
    		        foreach ($station_arr as $i => $station) {
    		            $station_arr[$i]['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($station['id'], 'pjStation');
    		            $station_ids[] = $station['id'];
    		        }
    		        $station_fee_arr = array();
    		        if (count($station_ids) > 0) {
    		            $_station_fee_arr = pjStationFeeModel::factory()->reset()->whereIn('t1.station_id', $station_ids)->findAll()->getData();
    		            foreach ($_station_fee_arr as $i => $sf) {
    		                $station_fee_arr[$sf['station_id']][] = $sf;
    		            }
    		        }
    		        foreach ($station_arr as $i => $station) {
    		            $station_arr[$i]['fee_arr'] = isset($station_fee_arr[$station['id']]) ? $station_fee_arr[$station['id']] : array();
    		        }
    		        
    		        $data['data'] = $station_arr;
    		    }
    		    $data['total_records'] = $total;
    		    $data['total_pages'] = $pages;
    		    $data['status'] = 'OK';
    		    break;
    		case 'price':
    			$pjPriceModel = pjPriceModel::factory();
    			$total = $pjPriceModel->findCount()->getData();
				$pages = ceil($total / $rowCount);
				if (!isset($_REQUEST['is_count_page']) || (isset($_REQUEST['is_count_page']) && (int)$_REQUEST['is_count_page'] == 0)) {
    				$page = isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
    				$offset = ((int) $page - 1) * $rowCount;
        			$data['data'] = $pjPriceModel->limit($rowCount, $offset)->findAll()->getData();
				}
    			$data['total_records'] = $total;
    			$data['total_pages'] = $pages;
    			$data['status'] = 'OK';
    		break;	
    		case 'booking':
    			$pjBookingModel = pjBookingModel::factory();
    			$pjBookingExtraModel = pjBookingExtraModel::factory();
    			$pjBookingPaymentModel = pjBookingPaymentModel::factory();    			
    			
    			$total = $pjBookingModel->where('t1.status', 'confirmed')->findCount()->getData();
				$pages = ceil($total / $rowCount);
				if (!isset($_REQUEST['is_count_page']) || (isset($_REQUEST['is_count_page']) && (int)$_REQUEST['is_count_page'] == 0)) {
    				$page = isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
    				$offset = ((int) $page - 1) * $rowCount;
    				$booking_arr = $pjBookingModel->limit($rowCount, $offset)->findAll()->getData();
    		    	foreach ($booking_arr as $i => $booking) {
    		       		foreach ($booking as $k => $v) {
    						if (empty($v)) {
    							$booking[$k] = ':NULL';
    						}
    					}
    					$booking['platform'] = 'newsystem';
    					$booking_arr[$i] = $booking;
    		       		$booking_ids[] = $booking['id'];
    		       	}
    		       	if ($booking_ids) {
    		       		$be_arr = pjBookingExtraModel::factory()->whereIn('t1.booking_id', $booking_ids)->findAll()->getData();
    		       		$bp_arr = pjBookingPaymentModel::factory()->whereIn('t1.booking_id', $booking_ids)->findAll()->getData();
    		       		$booking_extra_arr = $booking_payment_arr = array();
    		       		foreach ($be_arr as $be) {
    		       			$booking_extra_arr[$be['booking_id']][] = $be;
    		       		}
    		       		foreach ($bp_arr as $bp) {
    		       			$booking_payment_arr[$bp['booking_id']][] = $bp;
    		       		}
    		       		foreach ($booking_arr as $i => $booking) {
    		       			$booking_arr[$i]['booking_extra_arr'] = isset($booking_extra_arr[$booking['id']]) ? $booking_extra_arr[$booking['id']] : array();
    		       			$booking_arr[$i]['booking_payment_arr'] = isset($booking_payment_arr[$booking['id']]) ? $booking_payment_arr[$booking['id']] : array();
    		       		}
    		       	}
        			$data['data'] = $booking_arr;
				}
    			$data['total_records'] = $total;
    			$data['total_pages'] = $pages;
    			$data['status'] = 'OK';
    		break;	
    		case 'area':
    			$pjAreaModel = pjAreaModel::factory();
    			$total = $pjAreaModel->findCount()->getData();
				$pages = ceil($total / $rowCount);
				if (!isset($_REQUEST['is_count_page']) || (isset($_REQUEST['is_count_page']) && (int)$_REQUEST['is_count_page'] == 0)) {
    				$page = isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
    				$offset = ((int) $page - 1) * $rowCount;
    				$area_arr = $pjAreaModel->limit($rowCount, $offset)->findAll()->getData();
    		    	$area_coord_arr = $area_ids = array();
    		       	foreach ($area_arr as $i => $area) {
    		       		$area_arr[$i]['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($area['id'], 'pjArea');
    		       		$area_ids[] = $area['id'];
    		       	}
    		       	if (count($area_ids) > 0) {
    		       		$_area_coord_arr = pjAreaCoordModel::factory()->whereIn('t1.area_id', $area_ids)->findAll()->getData();
    		       		foreach ($_area_coord_arr as $ac) {
    		       			$area_coord_arr[$ac['area_id']][] = $ac;
    		       		}
    		       	}
    				foreach ($area_arr as $i => $area) {
    		       		$area_arr[$i]['coords'] = isset($area_coord_arr[$area['id']]) ? $area_coord_arr[$area['id']] : array();
    		       	}
        			$data['data'] = $area_arr;
				}
    			$data['total_records'] = $total;
    			$data['total_pages'] = $pages;
    			$data['status'] = 'OK';
    		break; 	
    		case 'station':
    			$pjStationModel = pjStationModel::factory();
    			$total = $pjStationModel->findCount()->getData();
				$pages = ceil($total / $rowCount);
				if (!isset($_REQUEST['is_count_page']) || (isset($_REQUEST['is_count_page']) && (int)$_REQUEST['is_count_page'] == 0)) {
    				$page = isset($_REQUEST['page']) && (int) $_REQUEST['page'] > 0 ? intval($_REQUEST['page']) : 1;
    				$offset = ((int) $page - 1) * $rowCount;
    				$station_arr = $pjStationModel->limit($rowCount, $offset)->findAll()->getData();
    		    	$station_fee_arr = $station_ids = array();
    		       	foreach ($station_arr as $i => $station) {
    		       		$station_arr[$i]['i18n'] = pjMultiLangModel::factory()->getIsoMultiLang($station['id'], 'pjStation');
    		       		$station_ids[] = $station['id'];
    		       	}
    		       	if (count($station_ids) > 0) {
    		       		$_station_fee_arr = pjStationFeeModel::factory()->whereIn('t1.station_id', $station_ids)->findAll()->getData();
    		       		foreach ($_station_fee_arr as $sf) {
    		       			$station_fee_arr[$sf['station_id']][] = $sf;
    		       		}
    		       	}
    				foreach ($station_arr as $i => $station) {
    		       		$station_arr[$i]['fee_arr'] = isset($station_fee_arr[$station['id']]) ? $station_fee_arr[$station['id']] : array();
    		       	}
        			$data['data'] = $station_arr;
				}
    			$data['total_records'] = $total;
    			$data['total_pages'] = $pages;
    			$data['status'] = 'OK';
    		break; 	
    		default:
    			$data['status'] = 'ERROR';
    			$data['error_msg'] = 'Unknow type';
    		break;
    	}
    	return pjAppController::jsonResponse($data);
    }
}
?>