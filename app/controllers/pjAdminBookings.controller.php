<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminBookings extends pjAdmin
{
	public $defaultPickupLocations = 'pjTransferReservation_PickupLocations';
	public $defaultDropoffLocations = 'pjTransferReservation_DropoffLocations';
	
	public function pjActionCheckID()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (!isset($_GET['uuid']) || empty($_GET['uuid']))
			{
				echo 'false';
				exit;
			}
			$pjBookingModel = pjBookingModel::factory()->where('t1.uuid', $_GET['uuid']);
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjBookingModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjBookingModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$pickup_arr = pjLocationModel::factory()
				->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->select("t1.*, t2.content as pickup_location")
				->where('t1.status', 'T')
				->orderBy("is_airport DESC, pickup_location ASC")
				->findAll()->getData();
			$this->set('pickup_arr', $pickup_arr);
			
			$dropoff_arr = pjDropoffModel::factory()
				->select("t1.*, t2.content as location")
				->join('pjMultiLang', "t2.model='pjDropoff' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->orderBy("t1.is_airport DESC, t1.order_index ASC, location ASC")
				->findAll()->getData();
			$dropoff_place_arr = $dropoff_ids_arr = array();
			foreach ($dropoff_arr as $v) {
				$dropoff_ids_arr[] = $v['id'];
			}
			
			if ($dropoff_ids_arr) {
				$dropoff_place_arr = pjDropoffAreaModel::factory()->select('t1.dropoff_id, t4.*, t5.content AS area_name, t6.content AS place_name')
					->join('pjDropoff', 't2.id=t1.dropoff_id', 'inner')
					->join('pjArea', 't3.id=t1.area_id', 'inner')
					->join('pjAreaCoord', 't4.area_id=t3.id', 'inner')
					->join('pjMultiLang', "t5.model='pjArea' AND t5.foreign_id=t1.area_id AND t5.field='name' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t6.model='pjAreaCoord' AND t6.foreign_id=t4.id AND t6.field='place_name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
					->whereIn('t1.dropoff_id', $dropoff_ids_arr)
					->where('t4.is_disabled', 0)
					->orderBy('t1.dropoff_id ASC, t3.order_index ASC, t5.content ASC')
					->findAll()
					->getData();
			}
			$this->set('dropoff_place_arr', $dropoff_place_arr);
			
			$this->appendJs('select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/js/');
			$this->appendCss('select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/css/');
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminBookings.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjBookingModel = pjBookingModel::factory()
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjBooking', "t5.id=t1.return_id", 'left outer')
				->join('pjClient', "t6.id=t1.client_id", 'left')
				->join('pjMultiLang', "t7.model='pjAreaCoord' AND t7.foreign_id=t1.dropoff_place_id AND t7.field='place_name' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjAreaCoord', "t8.id=t1.dropoff_place_id", 'left')
				->join('pjMultiLang', "t9.model='pjArea' AND t9.foreign_id=t8.area_id AND t9.field='name' AND t9.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjLocation', "t10.id=t1.location_id", 'left');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjBookingModel->where("(t1.uuid LIKE '%$q%' OR t2.content LIKE '%$q%' OR t6.fname LIKE '%$q%' OR t6.lname LIKE '%$q%' OR t6.email LIKE '%$q%')");
			}
			
			if (isset($_GET['fleet_id']) && !empty($_GET['fleet_id']))
			{
				$fleet_id = pjObject::escapeString($_GET['fleet_id']);
				$pjBookingModel->where("(t1.fleet_id='".$fleet_id."')");
			}
			if (isset($_GET['client_id']) && (int) $_GET['client_id'] > 0)
			{
				$client_id = pjObject::escapeString($_GET['client_id']);
				$pjBookingModel->where("(t1.client_id='".$client_id."')");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('confirmed','cancelled','pending','in_progress','passed_on')))
			{
				$pjBookingModel->where('t1.status', $_GET['status']);
			}
            if (isset($_GET['driver_id']) && !empty($_GET['driver_id']))
            {
                $pjBookingModel->where('t1.driver_id', $_GET['driver_id']);
            }

			if (isset($_GET['name']) && !empty($_GET['name']))
			{
				$q = pjObject::escapeString($_GET['name']);
				$pjBookingModel->where('t6.fname LIKE', "%$q%");
				$pjBookingModel->orWhere('t6.lname LIKE', "%$q%");
			}
			if (isset($_GET['email']) && !empty($_GET['email']))
			{
				$q = pjObject::escapeString($_GET['email']);
				$pjBookingModel->where('t6.email LIKE', "%$q%");
			}
			if (isset($_GET['phone']) && !empty($_GET['phone']))
			{
				$q = pjObject::escapeString($_GET['phone']);
				$pjBookingModel->where('t6.phone LIKE', "%$q%");
			}
			if (isset($_GET['date']) && !empty($_GET['date']))
			{
				$date = pjUtil::formatDate(pjObject::escapeString($_GET['date']), $this->option_arr['o_date_format']);
				$pjBookingModel->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')='$date')");
			}
			if (isset($_GET['location_id']) && !empty($_GET['location_id']))
			{
				$location_id = pjObject::escapeString($_GET['location_id']);
				$pjBookingModel->where("(t1.location_id='".$location_id."')");
			}
			if (isset($_GET['dropoff_id']) && !empty($_GET['dropoff_id']))
			{
				$dropoff_id = pjObject::escapeString($_GET['dropoff_id']);
				$pjBookingModel->where("(t1.dropoff_id='".$dropoff_id."')");
			}
			
			if (isset($_GET['dropoff_place_id']) && !empty($_GET['dropoff_place_id']))
			{
				$dropoff_place_id = pjObject::escapeString($_GET['dropoff_place_id']);
				$pjBookingModel->where("(t1.dropoff_place_id='".$dropoff_place_id."')");
			}
			if (isset($_GET['notes_for_support']) && (int)$_GET['notes_for_support'] == 1) {
			    $pjBookingModel->where("(t1.notes_for_support IS NOT NULL && t1.notes_for_support != '')");
			}
			
			$column = 'created';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}
			
			$total = $pjBookingModel->findCount()->getData();
			
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			$tblBookingsTable = $pjBookingModel->getTable();
			if (isset($_GET['date']) && !empty($_GET['date'])) {
			    $subquery = "(
                        SELECT
                            COUNT(tb.id)
                        FROM
                            `".$tblBookingsTable."` AS tb
                        WHERE
                            t1.id != tb.id
                            AND LOWER(t1.status) != 'cancelled'
                            AND LOWER(tb.status) != 'cancelled'
                            AND NOT (
                                (tb.id = t1.return_id AND COALESCE(t1.return_id, 0) > 0) 
                                OR 
                                (t1.id = tb.return_id AND COALESCE(tb.return_id, 0) > 0)
                            )
                            AND 
                            (
                                (
                                    LOWER(TRIM(t1.c_email)) = LOWER(TRIM(tb.c_email)) 
                                    AND t1.c_email IS NOT NULL AND t1.c_email != ''
                                )
                                OR
                                (
                                    TRIM(CONCAT_WS(' ', t1.c_title, t1.c_fname, t1.c_lname)) = TRIM(CONCAT_WS(' ', tb.c_title, tb.c_fname, tb.c_lname))
                                    AND TRIM(CONCAT_WS(' ', t1.c_title, t1.c_fname, t1.c_lname)) != ''
                                )
                            )
                            AND DATE(t1.booking_date) <= DATE(COALESCE(tb.return_date, tb.booking_date))
                            AND DATE(COALESCE(t1.return_date, t1.booking_date)) >= DATE(tb.booking_date)
                    ) AS double_bookings";
			} else {
			    $subquery = " 0 AS double_bookings";
			}
			$data = $pjBookingModel
				->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', t7.content, t1.dropoff_address) as dropoff, 
						t5.uuid as uuid2, t5.id as id2, 
						t6.fname, t6.lname, t6.email, t6.phone, t10.color AS location_color, $subquery")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
				
			$booking_ids_arr = $booking_extras_arr = array();
			foreach ($data as $val) {
				$booking_ids_arr[] = $val['id'];
			}
			if ($booking_ids_arr) {
				$be_arr = pjBookingExtraModel::factory()->select('t1.*, t2.image_path, t3.content AS name')
				    ->join('pjExtra', 't2.id=t1.extra_id', 'left outer')
					->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.extra_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->whereIn('t1.booking_id', $booking_ids_arr)
					->orderBy('t3.content ASC')
					->findAll()
					->getData();
				foreach ($be_arr as $val) {
					$booking_extras_arr[$val['booking_id']][] = $val;
				}
			}

			$paymentMethodsShort = __('payment_methods_short', true);
			$_yesno = __('_yesno', true);
			foreach($data as $k => $v)
			{
				$client_arr = array();
				if(!empty($v['c_fname']) || !empty($v['fname']))
				{
					$client_arr[] = !empty($v['client_id']) ? pjSanitize::clean($v['fname']) : pjSanitize::clean($v['c_fname']) ;
				}
				if(!empty($v['c_lname']) || !empty($v['lname']))
				{
					$client_arr[] = !empty($v['client_id']) ? pjSanitize::clean($v['lname']) : pjSanitize::clean($v['c_lname']) ;
				}
				$v['client'] = join(" ", $client_arr) . "<br/>" . (!empty($v['client_id']) ? pjSanitize::clean($v['email']) : pjSanitize::clean($v['lname']) );
				$v['pickup_dropoff'] = __('lblTransferFrom', true, false).': '. $v['location'] . "<br/>" . __('lblTransferTo', true, false).': ' . $v['dropoff'];
				if(!empty($v['return_id']))
				{
					$v['bid'] = $v['return_id'];		
					$v['pickup_dropoff'] = __('lblTransferFrom', true, false).': '. $v['dropoff'] . "<br/>" . __('lblTransferTo', true, false).': ' . $v['location'];
					$v['booking_date']  = strtolower(__('lblReturn', true, false)) . ': ' . pjUtil::formatTime(date('H:i:s', strtotime($v['booking_date'])), 'H:i:s', $this->option_arr['o_time_format']) . ',<br/>' . pjUtil::formatDate(date('Y-m-d', strtotime($v['booking_date'])), 'Y-m-d', $this->option_arr['o_date_format']);
				}else{
					$v['bid'] = $v['id'];
					$v['booking_date']  = pjUtil::formatTime(date('H:i:s', strtotime($v['booking_date'])), 'H:i:s', $this->option_arr['o_time_format']) . ',<br/>' . pjUtil::formatDate(date('Y-m-d', strtotime($v['booking_date'])), 'Y-m-d', $this->option_arr['o_date_format']);
				}

				$v['payment_method'] = $paymentMethodsShort[$v['payment_method']];
				
				$extra_arr = array();
				if (isset($booking_extras_arr[$v['id']])) {
					foreach ($booking_extras_arr[$v['id']] as $ex) {
					    if (!empty($ex['image_path'])) {
					        $extra_arr[] = $ex['quantity'].' x '.$ex['name'].' <img src="'.$ex['image_path'].'" />';
					    } else {
                            $extra_arr[] = $ex['quantity'].' x '.$ex['name'];
					    }
					}
				}
				$v['extras'] = '<div class="orderExtras">'.implode('<br/>', $extra_arr).'</div>';
				
				/* if ((int)$v['return_id'] <= 0 && !empty($v['location_color'])) {
				    $booking_color = $v['location_color'];
				} else {
				    if ((int)$v['pickup_is_airport'] == 1 && (int)$v['dropoff_is_airport'] == 0) {
				        $booking_color = '#D1B336';
				    } else {
				        $booking_color = '#D14936';
				    }
				} */
				if (!empty($v['notes_for_support'])) {
				    $booking_color = '#D14936';
				} else {
				    $booking_color = '';
				}
				$v['booking_color'] = $booking_color;
				$v['is_synchronized'] = @$_yesno[$v['is_synchronized']];
				$data[$k] = $v;
			}
						
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionSaveBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjBookingModel = pjBookingModel::factory();
			if ($_POST['column'] == 'status') {
				$arr = $pjBookingModel->find($_GET['id'])->getData();
				$pjBookingModel->reset()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
				if ($arr['status'] == 'confirmed' && $_POST['value'] == 'cancelled') {
					$resp = pjApiSync::syncBooking(array($_GET['id']), 'cancel', $this->option_arr);
				} elseif ($arr['status'] != 'confirmed' && $_POST['value'] == 'confirmed') {
					$resp = pjApiSync::syncBooking($_GET['id'], 'update', $this->option_arr);
				}
			} else {
				$arr = $pjBookingModel->find($_GET['id'])->getData();
				if(empty($arr['return_id']))
				{
					$pjBookingModel->reset()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
					$pjBookingModel->reset()->where('return_id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
				}else{
					$pjBookingModel->reset()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
					$pjBookingModel->reset()->where('id', $arr['return_id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
				}
			}
		}
		exit;
	}
	
	public function pjActionExportBooking()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjBookingModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Bookings-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionPrint()
	{
        $this->checkLogin();
		$this->setLayout('pjActionPrint');
		$transfer_arr = array();
		
		if ((isset($_GET['record']) && $_GET['record'] != '') || isset($_GET['today']) || isset($_GET['id']))
		{
			$pjBookingModel = pjBookingModel::factory()
				->reset()
				->select("t1.*, t2.content as vehicle, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', t8.content, t1.dropoff_address) AS dropoff_location,
						  t6.uuid as uuid2, t6.id as id2, t6.c_departure_airline_company as ariline_company_2, t6.c_departure_flight_number as flight_number_2, t6.c_departure_flight_time as flight_time_2,
						  IF(t1.dropoff_type='server', t8.content, t1.dropoff_address) AS location2, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS dropoff_location2, 
						  t1.duration as duration2, t7.content as c_country,						  
						  t1.pickup_is_airport AS is_airport, t1.pickup_is_airport as is_return_airport")
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjDropoff', "t5.id=t1.dropoff_id", 'left outer')
				->join('pjBooking', "t6.id=t1.return_id", 'left outer')
				->join('pjMultiLang', "t7.model='pjCountry' AND t7.foreign_id=t1.c_country AND t7.field='name' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t8.model='pjAreaCoord' AND t8.foreign_id=t1.dropoff_place_id AND t8.field='place_name' AND t8.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjAreaCoord', "t9.id=t1.dropoff_place_id", 'left')
				->join('pjMultiLang', "t10.model='pjArea' AND t10.foreign_id=t9.area_id AND t10.field='name' AND t10.locale='".$this->getLocaleId()."'", 'left outer');
						
			if(!isset($_GET['id']))
			{
				if (isset($_GET['record']) && $_GET['record'] != '')
				{
					$pjBookingModel->whereIn("t1.id", explode(",", $_GET['record']));	
					if (isset($_GET['details'])) {
						$pjBookingModel->orWhereIn("t1.return_id", explode(",", $_GET['record']));
						$returnIds = pjBookingModel::factory()->reset()->whereIn('t1.id', explode(',', $_GET['record']))->findAll()->getDataPair(null, 'return_id');
						if ($returnIds) {
							$pjBookingModel->orWhereIn('t1.id', $returnIds);
							$pjBookingModel->orWhereIn('t1.return_id', $returnIds);
						}
					}
				}else{
					$pjBookingModel->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')=DATE_FORMAT(NOW(), '%Y-%m-%d'))")	;
					$pjBookingModel->where("t1.status <> 'cancelled'");
				}
			}else{
				$pjBookingModel->where("t1.id", $_GET['id']);
				$pjBookingModel->orWhere("t1.return_id", $_GET['id']);
			}
			$transfer_arr = $pjBookingModel
				->orderBy("t1.id ASC")
				->findAll()
				->getData();

            foreach($transfer_arr as $k => $v)
            {
                $transfer_arr[$k]['extra_arr'] = pjBookingExtraModel::factory()
                    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.extra_id AND t3.field='info' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->select("t1.quantity, t2.content as name, t3.content as info")
                    ->where('booking_id', $v['id'])
                    ->orderBy('t1.extra_id ASC')
                    ->findAll()
                    ->getData();;
            }
		}		
		$this->set('transfer_arr', $transfer_arr);
	}

	public function pjActionPrintSingle()
	{
		$this->checkLogin();
		$this->setLayout('pjActionPrint');

		$records = explode(",", $_GET['record']);
		$transfer_arr = pjBookingModel::factory()
			->reset()
			->select("t1.*, t2.content as vehicle, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', t8.content, t1.dropoff_address) AS dropoff_location,
						  t6.uuid as uuid2, t6.id as id2, t6.c_departure_airline_company as ariline_company_2, t6.c_departure_flight_number as flight_number_2, t6.c_departure_flight_time as flight_time_2,
						  IF(t1.dropoff_type='server', t8.content, t1.dropoff_address) AS location2, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS dropoff_location2, 
						  t1.duration as duration2, t7.content as c_country,						  
						  t1.pickup_is_airport AS is_airport, t1.pickup_is_airport as is_return_airport")
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjDropoff', "t5.id=t1.dropoff_id", 'left outer')
				->join('pjBooking', "t6.id=t1.return_id", 'left outer')
				->join('pjMultiLang', "t7.model='pjCountry' AND t7.foreign_id=t1.c_country AND t7.field='name' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t8.model='pjAreaCoord' AND t8.foreign_id=t1.dropoff_place_id AND t8.field='place_name' AND t8.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjAreaCoord', "t9.id=t1.dropoff_place_id", 'left')
				->join('pjMultiLang', "t10.model='pjArea' AND t10.foreign_id=t9.area_id AND t10.field='name' AND t10.locale='".$this->getLocaleId()."'", 'left outer')
			->whereIn("t1.id", $records)
			->orderBy("t1.id ASC")
			->findAll()
			->getData();

		if (!empty($transfer_arr)) {
			foreach ($transfer_arr as $k => $transfer) {
				$transfer_arr[$k]['extra_arr'] = pjBookingExtraModel::factory()
					->reset()
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.extra_id AND t3.field='info' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->select("t1.quantity, t2.content as name, t3.content as info")
					->where('booking_id', $transfer['id'])
					->orderBy('t1.extra_id ASC')
					->findAll()
					->getData();
			}
		}
		
		$this->set('transfer_arr', $transfer_arr);
	}
	
	public function pjActionDeleteBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$ids_arr = array();
			$pjBookingModel = pjBookingModel::factory();
			if ($pjBookingModel->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjBookingExtraModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				pjBookingPaymentModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				pjBookingHistoryModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				
				$ids_arr[] = $_GET['id'];
				$return_arr = $pjBookingModel->reset()->where('t1.return_id', $_GET['id'])->limit(1)->findAll()->getDataIndex(0);
				if ($return_arr) {
					if ($pjBookingModel->reset()->setAttributes(array('id' => $return_arr['id']))->erase()->getAffectedRows() == 1) {
						pjBookingExtraModel::factory()->reset()->where('booking_id', $return_arr['id'])->eraseAll();
						pjBookingPaymentModel::factory()->reset()->where('booking_id', $return_arr['id'])->eraseAll();
						$ids_arr[] = $return_arr['id'];
					}
				}				
				$resp = pjApiSync::syncBooking($ids_arr, 'delete', $this->option_arr);	
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteBookingBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$ids_arr = $_POST['record'];
				$pjBookingModel = pjBookingModel::factory();
				
				$pjBookingModel->whereIn('id', $_POST['record'])->eraseAll();				
				pjBookingExtraModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
				pjBookingPaymentModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
				pjBookingHistoryModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
				
				$return_ids_arr = $pjBookingModel->reset()->whereIn('t1.return_id', $_POST['record'])->findAll()->getDataPair(NULL, 'id');
				if ($return_ids_arr) {
					$pjBookingModel->reset()->whereIn('id', $return_ids_arr)->eraseAll();
					pjBookingExtraModel::factory()->reset()->whereIn('booking_id', $return_ids_arr)->eraseAll();
					pjBookingPaymentModel::factory()->reset()->whereIn('booking_id', $return_ids_arr)->eraseAll();				
					$ids_arr = array_merge($ids_arr, $return_ids_arr);
				}				
				$resp = pjApiSync::syncBooking(array_unique($ids_arr), 'delete', $this->option_arr);
			}
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['booking_create']))
			{
				$pjBookingModel = pjBookingModel::factory();
				
				$_date = $_POST['booking_date']; unset($_POST['booking_date']);
				if(count(explode(" ", $_date)) == 3)
				{
					list($date, $time, $period) = explode(" ", $_date);
					$time = pjUtil::formatTime($time . ' ' . $period, $this->option_arr['o_time_format']);
				}else{
					list($date, $time) = explode(" ", $_date);
					$time = pjUtil::formatTime($time, $this->option_arr['o_time_format']);
				}

				$region = $dropoff_region = '';
				list($pickup_type, $pickup_id) = explode('~::~', $_POST['location_id']);
				list($dropoff_type, $dropoff_place_id, $dropoff_id) = explode('~::~', $_POST['dropoff_id']);
				if ($_POST['dropoff_type'] == 'google' && (int)$_POST['custom_dropoff_id'] > 0) {
				    $dropoff_id = (int)$_POST['custom_dropoff_id'];
				}
				
				if ($dropoff_type == 'server') {
				    $dropoff_arr = pjDropoffModel::factory()->find($dropoff_id)->getData();
				    $dropoff_region = $dropoff_arr['region'];
				}
				
				$data = array();
				if ($pickup_type == 'server') {
				    $pickup_arr = pjLocationModel::factory()->find($pickup_id)->getData();
				    $region = $pickup_arr['region'];
				}
				$data['region'] = $region;
				$data['dropoff_region'] = $dropoff_region;
				
				$data['uuid'] = pjAppController::createRandomBookingId();
				$data['ip'] = pjUtil::getClientIp();
				$data['locale_id'] = $this->getLocaleId();
				$data['booking_date'] = pjUtil::formatDate($date, $this->option_arr['o_date_format']) . ' ' . $time;
                $data['driver_id'] = !empty($_POST['driver_id']) && $_POST['driver_id'] > 0 ? $_POST['driver_id'] : ':NULL';
                $data['status'] = $_POST['status'];
                
                $data['location_id'] = $pickup_id;
                $data['dropoff_id'] = $dropoff_id;
                $data['dropoff_place_id'] = $dropoff_place_id;
                
                $data['pickup_id'] = $_POST['pickup_id'];
                $data['pickup_address'] = $_POST['pickup_address'];
				$data['pickup_lat'] = $_POST['pickup_lat'];
				$data['pickup_lng'] = $_POST['pickup_lng'];
				$data['pickup_type'] = $pickup_type;
				
				$data['dropoff_address'] = $_POST['dropoff_address'];
				$data['dropoff_lat'] = $_POST['dropoff_lat'];
				$data['dropoff_lng'] = $_POST['dropoff_lng'];
				$data['dropoff_type'] = $dropoff_type;
				
				$data['pickup_is_airport'] = $_POST['pickup_is_airport'];
				$data['dropoff_is_airport'] = $_POST['dropoff_is_airport'];
				$data['duration'] = $_POST['duration'];
				$data['distance'] = $_POST['distance'];
				$data['station_id'] = $_POST['station_id'];
				$data['station_fee'] = $_POST['station_fee'];
                
                $data['fleet_id'] = $_POST['fleet_id'];
                $data['passengers'] = $_POST['passengers'];
                $data['c_hotel'] = $_POST['c_hotel'];
                $data['c_notes'] = $_POST['c_notes'];
                $data['internal_notes'] = $_POST['internal_notes'];
                $data['customized_name_plate'] = $_POST['customized_name_plate'];
				$data['google_map_link'] = $_POST['google_map_link'];
				$data['pickup_google_map_link'] = $_POST['pickup_google_map_link'];
				$data['dropoff_google_map_link'] = $_POST['dropoff_google_map_link'];
				$data['station_id'] = $_POST['station_id'];
				$data['notes_for_support'] = $_POST['notes_for_support'];
                if(isset($_POST['has_return']))
                {
                    if(count(explode(" ", $_POST['return_date'])) == 3)
                    {
                        list($return_date, $return_time, $return_period) = explode(" ", $_POST['return_date']);
                        $return_time = pjUtil::formatTime($return_time . ' ' . $return_period, $this->option_arr['o_time_format']);
                    }else{
                        list($return_date, $return_time) = explode(" ", $_POST['return_date']);
                        $return_time = pjUtil::formatTime($return_time, $this->option_arr['o_time_format']);
                    }
                    unset($_POST['return_date']);
                    $data['return_date'] = pjUtil::formatDate($return_date, $this->option_arr['o_date_format']) . ' ' . $return_time;
                }

                if ($_POST['pickup_is_airport'] == 0 && $_POST['dropoff_is_airport'] == 0) {
                	$data['c_address'] = $_POST['cl_address'];
                	$data['c_destination_address'] = $_POST['cl_destination_address'];
                } else {
					if($_POST['pickup_is_airport'] == 1)
	                {
	                    $data['c_flight_time'] = $time;
	                    $data['c_flight_number'] = $_POST['c_flight_number'];
	                    $data['c_airline_company'] = $_POST['c_airline_company'];
	                    $data['c_destination_address'] = $_POST['c_destination_address'];
	                }
	                else
	                {
	                    $data['c_departure_flight_time'] = str_pad($_POST['c_departure_flight_time_h'], 2, 0, STR_PAD_LEFT) . ':' . str_pad($_POST['c_departure_flight_time_m'], 2, 0, STR_PAD_LEFT);
	                    $data['c_address'] = $_POST['c_address'];
	                }
                }

                $data['c_title'] = isset($_POST['c_title']) ? $_POST['c_title'] : ':NULL';
                $data['c_fname'] = isset($_POST['c_fname']) ? $_POST['c_fname'] : ':NULL';
                $data['c_lname'] = isset($_POST['c_lname']) ? $_POST['c_lname'] : ':NULL';
                $data['c_email'] = isset($_POST['c_email']) ? $_POST['c_email'] : ':NULL';
                $data['c_country'] = isset($_POST['c_country']) ? $_POST['c_country'] : ':NULL';
                $data['c_dialing_code'] = isset($_POST['c_dialing_code']) ? $_POST['c_dialing_code'] : ':NULL';
                $data['c_phone'] = isset($_POST['c_phone']) ? $_POST['c_phone'] : ':NULL';

                $c_data = array();
                $c_data['title'] = $data['c_title'];
                $c_data['fname'] = $data['c_fname'];
                $c_data['lname'] = $data['c_lname'];
                $c_data['email'] = $data['c_email'];
                $c_data['country_id'] = $data['c_country'];
                $c_data['dialing_code'] = $data['c_dialing_code'];
                $c_data['phone'] = $data['c_phone'];
                $c_data['password'] = pjUtil::getRandomPassword(6);
                $c_data['status'] = 'T';
                if($c_data['email'] != ':NULL')
                {
                    $client_id = pjClientModel::factory()->setAttributes($c_data)->insert()->getInsertId();
                    if ($client_id !== false && (int) $client_id > 0)
                    {
                        $data['client_id'] = $client_id;
                    }
                }

                $payment_method = isset($_POST['payment_method']) && !empty($_POST['payment_method'])? $_POST['payment_method']: 'none';
                if ($payment_method == 'creditcard')
                {
                    $data['cc_owner'] = $_POST['cc_owner'];
                    $data['cc_num'] = $_POST['cc_num'];
                    $data['cc_exp'] = $_POST['cc_exp_year'] . '-' . $_POST['cc_exp_month'];
                    $data['cc_code'] = $_POST['cc_code'];
                }
                $data['payment_method'] = $payment_method;
                $data['voucher_code'] = $_POST['voucher_code'];
                $data['accept_shared_trip'] = isset($_POST['accept_shared_trip'])? 1: 0;
                $data['sub_total'] = $_POST['sub_total'];
                $data['discount'] = $_POST['discount'];
                $data['tax'] = $_POST['tax'];
                $data['total'] = $_POST['total'];
                $data['deposit'] = $_POST['deposit'];
                $data['extra_price'] = (float)$_POST['extra_price_first_transfe'];
				if(isset($_POST['has_return'])) {
					$data['price'] = $_POST['price_first_transfer'];
				} else {
					$data['price'] = $_POST['price'];
				}
				$id = pjBookingModel::factory($data)->insert()->getInsertId();

				if ($id !== false && (int) $id > 0)
				{
				    $data_history = array(
				        'booking_id' => $id,
				        'action' => 'Booking created',
				        'user_id' => $this->getUserId()
				    );
				    pjBookingHistoryModel::factory()->setAttributes($data_history)->insert();
				    
                    $arr = $pjBookingModel->reset()->find($id)->getData();

                    if(isset($_POST['has_return']))
                    {
                        $data['return_id'] = $id;
                        
                        $data['station_id'] = ':NULL';
						$data['station_fee'] = ':NULL';
                        
                        $data['booking_date'] = $data['return_date'];
                        $data['return_date'] = ':NULL';
                        $data['uuid'] = pjAppController::createRandomBookingId();
                        $data['created'] = $arr['created'];
                        $data['c_notes'] = $_POST['return_c_notes'];
                        $data['c_flight_time'] = ':NULL';
                        $data['c_flight_number'] = ':NULL';
                        $data['c_airline_company'] = ':NULL';
                        $data['c_destination_address'] = ':NULL';
                        $data['c_departure_flight_time'] = ':NULL';
                        $data['c_address'] = ':NULL';
                        $data['c_hotel'] = ':NULL';
                        $data['status'] = $_POST['status_return_trip'];
                        $data['passengers'] = $_POST['passengers_return'];
						$data['price'] = $_POST['price_return_transfer'];
						$data['google_map_link'] = $_POST['return_google_map_link'];
						$data['pickup_google_map_link'] = $_POST['return_pickup_google_map_link'];
						$data['dropoff_google_map_link'] = $_POST['return_dropoff_google_map_link'];
						$data['internal_notes'] = $_POST['return_internal_notes'];
                        if ($_POST['pickup_is_airport'] == 0 && $_POST['dropoff_is_airport'] == 0) {
                         	$data['c_address'] = $_POST['return_cl_address'];
                         	$data['c_destination_address'] = $_POST['return_cl_destination_address'];
                        } else {
	                        if($_POST['pickup_is_airport'] == 1)
	                        {
	                            $data['c_departure_flight_time'] = str_pad($_POST['return_c_departure_flight_time_h'], 2, 0, STR_PAD_LEFT) . ':' . str_pad($_POST['return_c_departure_flight_time_m'], 2, 0, STR_PAD_LEFT);
	                            $data['c_address'] = $_POST['return_c_address'];
	                        }
	                        else
	                        {
	                            $data['c_flight_time'] = $return_time;
	                            $data['c_flight_number'] = $_POST['return_c_flight_number'];
	                            $data['c_airline_company'] = $_POST['return_c_airline_company'];
	                        }
                        }
						$data['pickup_is_airport'] = $_POST['dropoff_is_airport'];
						$data['dropoff_is_airport'] = $_POST['pickup_is_airport'];
                        $data['ip'] = $arr['ip'];
                        $data['client_id'] = $arr['client_id'];
                        $data['extra_price'] = (float)$_POST['extra_price_return_transfe'];
                        $data['region'] = $dropoff_region;
                        $data['dropoff_region'] = $region;
                        
                        $data['pickup_lat'] = $_POST['dropoff_lat'];
                        $data['pickup_lng'] = $_POST['dropoff_lng'];
                        
                        $data['dropoff_lat'] = $_POST['pickup_lat'];
                        $data['dropoff_lng'] = $_POST['pickup_lng'];
                        
                        $return_id = $pjBookingModel->reset()->setAttributes($data)->insert()->getInsertId();
                    }

                    pjBookingExtraModel::factory()->saveExtras($_POST['extras'], @$_POST['extras_return'], $id, @$return_id);
                    
                    if (isset($_FILES['name_sign']) && !empty($_FILES['name_sign']['tmp_name']))
                    {
                        $file_path = PJ_UPLOAD_PATH.'files/'.$_FILES['name_sign']['name'];
                        if (@move_uploaded_file($_FILES['name_sign']['tmp_name'], $file_path)) {
                            $pjBookingModel->reset()->set('id', $id)->modify(array('name_sign' => $file_path));
                        }
                    }

                    //$invoice_arr = $this->pjActionGenerateInvoice($id);
                    
                    if($data['driver_id'] != ':NULL' && $this->option_arr['o_email_driver'] == 1)
                    {
                        $driver = pjDriverModel::factory()->select('email')->find($data['driver_id'])->getData();
                        if($driver && !empty($driver['email']))
                        {
                            $arr = $pjBookingModel->reset()
                                ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                                ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                                ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
                                ->join('pjMultiLang', "t5.model='pjDropoff' AND t5.foreign_id=t1.dropoff_id AND t5.field='address' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
                                ->select("t1.*, t2.content as fleet, t3.content as location, CONCAT_WS(' - ', t4.content, t5.content) as dropoff")
                                ->find($arr['id'])
                                ->getData();
                            $tokens = pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $this->getLocaleId());

                            $lang_message = pjMultiLangModel::factory()
                                ->select('t1.content')
                                ->where('t1.model','pjOption')
                                ->where('t1.locale', $this->getLocaleId())
                                ->where('t1.field', 'o_email_driver_message')
                                ->limit(1)
                                ->findAll()
                                ->getDataIndex(0);
                            $lang_subject = pjMultiLangModel::factory()
                                ->select('t1.content')
                                ->where('t1.model','pjOption')
                                ->where('t1.locale', $this->getLocaleId())
                                ->where('t1.field', 'o_email_driver_subject')
                                ->limit(1)
                                ->findAll()
                                ->getDataIndex(0);
                            if(!empty($lang_message['content']) && !empty($lang_subject['content']))
                            {
                                $html = pjUtil::fileGetContents(PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionDriverPDF&id='.$arr['id']);
                                $name = "name_plate_{$arr['uuid']}.pdf";
                                $pjPdf = new pjPdf();
                                $filePath = $pjPdf->generatePdf($name, $html);

                                $pjEmail = new pjEmail();
                                if ($this->option_arr['o_send_email'] == 'smtp')
                                {
                                    $pjEmail
                                        ->setTransport('smtp')
                                        ->setSmtpHost($this->option_arr['o_smtp_host'])
                                        ->setSmtpPort($this->option_arr['o_smtp_port'])
                                        ->setSmtpUser($this->option_arr['o_smtp_user'])
                                        ->setSmtpPass($this->option_arr['o_smtp_pass'])
                                    ;
                                }
                                $pjEmail->setContentType('text/html');

                                $subject = pjAppController::replaceTokens($arr, $tokens, $lang_subject['content']);
                                $message = pjAppController::replaceTokens($arr, $tokens, $lang_message['content']);

                                $pjEmail
                                    ->setTo($driver['email'])
                                    ->setFrom($this->getAdminEmail(), $this->option_arr['o_email_sender'])
                                    ->setSubject($subject)
                                    ->attach($filePath, $name, 'application/pdf')
                                    ->send(pjAppController::getEmailBody($message));
                            }
                        }
                    }
                    if ($_POST['status'] == 'confirmed') {
						$resp = pjApiSync::syncBooking($id, 'create', $this->option_arr);
						if ($_POST['status_return_trip'] == 'confirmed' && isset($return_id) && (int)$return_id > 0) {
							$resp = pjApiSync::syncBooking($return_id, 'create', $this->option_arr);
						}
                    }
					$err = 'ABB03';
				}else{
					$err = 'ABB04';
				}

				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=$err");
			}else{
				$this->set('country_arr', pjCountryModel::factory()
                    ->select('t1.id, t2.content AS country_title, t3.code')
                    ->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjDialingCode', "t3.country_id=t1.id", 'left outer')
                    ->orderBy('`country_title` ASC')->findAll()->getData());

                $this->set('extra_arr', pjExtraModel::factory()
                    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.field='info' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->select("t1.*, t2.content as name, t3.content as info")
                    ->where('t1.status', 'T')
                    ->orderBy("t1.id ASC")
                    ->findAll()
                    ->getData());

                $this->set('driver_arr', pjDriverModel::factory()
                    ->where('status', 'T')
                    ->orderBy("fname ASC, lname ASC")
                    ->findAll()
                    ->getData());

                $pickup_arr = pjLocationModel::factory()
                    ->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->select("t1.*, t2.content as pickup_location")
                    ->where('t1.status', 'T')
                    ->orderBy("is_airport DESC, pickup_location ASC")
                    ->findAll()->getDataPair('id');
				foreach ($pickup_arr as $k => $v) {
					if ($v['icon'] == 'airport') {
						$materialIcon = 'local_airport';
					} elseif ($v['icon'] == 'train') {
						$materialIcon = 'train_station';
					} else {
						$materialIcon = 'place';
					}	
					$pickup_arr[$k]['icon'] = $materialIcon;
					$pickup_arr[$k]['text'] = $v['pickup_location'];
					$pickup_arr[$k]['id_formated'] = 'server~::~'.$v['id'];
				}
				$this->set('pickup_arr', $pickup_arr);
				$_SESSION[$this->defaultPickupLocations] = $pickup_arr;

				$this->set('fleet_arr', pjFleetModel::factory()
                    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjStation' AND t3.foreign_id=t1.station_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->select("t1.*, t2.content as fleet, t3.content as station_name")
                    ->where('t1.status', 'T')
                    ->orderBy("fleet ASC")
                    ->findAll()->getData());
					
				$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/js/');
				$this->appendCss('select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/css/');
				$this->appendJs('jquery-ui-sliderAccess.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery-ui-timepicker-addon.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendCss('jquery-ui-timepicker-addon.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('https://maps.googleapis.com/maps/api/js?key='.$this->option_arr['o_google_api_key'].'&sensor=false&libraries=places,geometry,drawing', NULL, true);
				$this->appendJs('pjAdminBookings.js');
			}
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['booking_update']))
			{
				$pjBookingModel = pjBookingModel::factory();

				$data = array(
                    'return_date' => ':NULL',
                    'c_address' => ':NULL',
                    'c_airline_company' => ':NULL',
                    'c_departure_airline_company' => ':NULL',
                    'c_flight_number' => ':NULL',
                    'c_flight_time' => ':NULL',
                    'c_departure_flight_number' => ':NULL',
                    'c_departure_flight_time' => ':NULL',
                    'c_destination_address' => ':NULL',
                );
				
				$arr = $pjBookingModel
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t5.model='pjDropoff' AND t5.foreign_id=t1.dropoff_id AND t5.field='address' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
				->select("t1.*, t2.content as fleet, t3.content as location, CONCAT_WS(' - ', t4.content, t5.content) as dropoff")
				->find($_POST['id'])
				->getData();
				
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=ABB08");
				}
				
				if(!empty($arr['return_date']))
				{
				    $return_arr = $pjBookingModel->reset()
				    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				    ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				    ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
				    ->join('pjMultiLang', "t5.model='pjDropoff' AND t5.foreign_id=t1.dropoff_id AND t5.field='address' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
				    ->select("t1.*, t2.content as fleet, t3.content as location, CONCAT_WS(' - ', t4.content, t5.content) as dropoff")
				    ->where('t1.return_id', $arr['id'])
				    ->limit(1)
				    ->findAll()
				    ->getDataIndex(0);
				}
				
				$original_booking_arr = $arr;
				if(count(explode(" ", $_POST['booking_date'])) == 3)
				{
					list($date, $time, $period) = explode(" ", $_POST['booking_date']);
					$time = pjUtil::formatTime($time . ' ' . $period, $this->option_arr['o_time_format']);
				}else{
					list($date, $time) = explode(" ", $_POST['booking_date']);
					$time = pjUtil::formatTime($time, $this->option_arr['o_time_format']);
				}
                unset($_POST['booking_date']);
                $data['booking_date'] = pjUtil::formatDate($date, $this->option_arr['o_date_format']) . ' ' . $time;

				if(isset($_POST['has_return']))
				{
					if(count(explode(" ", $_POST['return_date'])) == 3)
					{
						list($return_date, $return_time, $return_period) = explode(" ", $_POST['return_date']);
						$return_time = pjUtil::formatTime($return_time . ' ' . $return_period, $this->option_arr['o_time_format']);
					}else{
						list($return_date, $return_time) = explode(" ", $_POST['return_date']);
						$return_time = pjUtil::formatTime($return_time, $this->option_arr['o_time_format']);
					}
					unset($_POST['return_date']);
					$data['return_date'] = pjUtil::formatDate($return_date, $this->option_arr['o_date_format']) . ' ' . $return_time;
				}
				
				list($pickup_type, $pickup_id) = explode('~::~', $_POST['location_id']);
				list($dropoff_type, $dropoff_place_id, $dropoff_id) = explode('~::~', $_POST['dropoff_id']);
				if ($_POST['dropoff_type'] == 'google' && (int)$_POST['custom_dropoff_id'] > 0) {
					$dropoff_id = (int)$_POST['custom_dropoff_id'];
				}
				
				$region = $dropoff_region = '';
				if ($dropoff_type == 'server') {
				    $dropoff_arr = pjDropoffModel::factory()->find($dropoff_id)->getData();
				    $dropoff_region = $dropoff_arr['region'];
				}
				
				if ($pickup_type == 'server') {
				    $pickup_arr = pjLocationModel::factory()->find($pickup_id)->getData();
				    $region = $pickup_arr['region'];
				}
				$data['region'] = $region;
				$data['dropoff_region'] = $dropoff_region;
				
                $data['location_id'] = $pickup_id;
                $data['dropoff_id'] = $dropoff_id;
                $data['dropoff_place_id'] = $dropoff_place_id;
                
                $data['pickup_id'] = $_POST['pickup_id'];
                $data['pickup_address'] = $_POST['pickup_address'];
				$data['pickup_lat'] = $_POST['pickup_lat'];
				$data['pickup_lng'] = $_POST['pickup_lng'];
				$data['pickup_type'] = $pickup_type;
				
				$data['dropoff_address'] = $_POST['dropoff_address'];
				$data['dropoff_lat'] = $_POST['dropoff_lat'];
				$data['dropoff_lng'] = $_POST['dropoff_lng'];
				$data['dropoff_type'] = $dropoff_type;
				
				$data['pickup_is_airport'] = $_POST['pickup_is_airport'];
				$data['dropoff_is_airport'] = $_POST['dropoff_is_airport'];
				$data['duration'] = $_POST['duration'];
				$data['distance'] = $_POST['distance'];
				$data['station_id'] = $_POST['station_id'];
				$data['station_fee'] = $_POST['station_fee'];
				
				$data['driver_id'] = !empty($_POST['driver_id']) && $_POST['driver_id'] > 0 ? $_POST['driver_id'] : ':NULL';
                $data['status'] = $_POST['status'];
                $data['fleet_id'] = $_POST['fleet_id'];
                $data['passengers'] = $_POST['passengers'];
                $data['c_hotel'] = $_POST['c_hotel'];
                $data['c_notes'] = $_POST['c_notes'];
                $data['internal_notes'] = $_POST['internal_notes'];
				$data['customized_name_plate'] = $_POST['customized_name_plate'];
				$data['notes_for_support'] = $_POST['notes_for_support'];
                
				if ($_POST['pickup_is_airport'] == 0 && $_POST['dropoff_is_airport'] == 0) {
					$data['c_address'] = $_POST['cl_address'];
					$data['c_destination_address'] = $_POST['cl_destination_address'];
				} else {
	                if($_POST['pickup_is_airport'] == 1)
	                {
	                    $data['c_flight_time'] = $time;
	                    $data['c_flight_number'] = $_POST['c_flight_number'];
	                    $data['c_airline_company'] = $_POST['c_airline_company'];
	                    $data['c_destination_address'] = $_POST['c_destination_address'];
	                }
	                else
	                {
	                    $data['c_departure_flight_time'] = str_pad($_POST['c_departure_flight_time_h'], 2, 0, STR_PAD_LEFT) . ':' . str_pad($_POST['c_departure_flight_time_m'], 2, 0, STR_PAD_LEFT);
	                    $data['c_address'] = $_POST['c_address'];
	                }
				}

                $data['c_title'] = isset($_POST['c_title']) ? $_POST['c_title'] : ':NULL';
                $data['c_fname'] = isset($_POST['c_fname']) ? $_POST['c_fname'] : ':NULL';
                $data['c_lname'] = isset($_POST['c_lname']) ? $_POST['c_lname'] : ':NULL';
                $data['c_email'] = isset($_POST['c_email']) ? $_POST['c_email'] : ':NULL';
                $data['c_country'] = isset($_POST['c_country']) ? $_POST['c_country'] : ':NULL';
                $data['c_dialing_code'] = isset($_POST['c_dialing_code']) ? $_POST['c_dialing_code'] : ':NULL';
                $data['c_phone'] = isset($_POST['c_phone']) ? $_POST['c_phone'] : ':NULL';
                
				$c_data = array();
                $c_data['title'] = $data['c_title'];
                $c_data['fname'] = $data['c_fname'];
                $c_data['lname'] = $data['c_lname'];
                $c_data['email'] = $data['c_email'];
                $c_data['country_id'] = $data['c_country'];
                $c_data['dialing_code'] = $data['c_dialing_code'];
                $c_data['phone'] = $data['c_phone'];
                $c_data['status'] = 'T';
                if($c_data['email'] != ':NULL')
                {
                	$client = pjClientModel::factory()->find($arr['client_id'])->getData();
                	if ($client) {
                		pjClientModel::factory()->reset()->set('id', $client['id'])->modify($c_data);
                	} else {
                		$c_data['password'] = pjUtil::getRandomPassword(6);
	                    $client_id = pjClientModel::factory()->reset()->setAttributes($c_data)->insert()->getInsertId();
	                    if ($client_id !== false && (int) $client_id > 0)
	                    {
	                        $data['client_id'] = $client_id;
	                    }
                	}
                }

                $payment_method = isset($_POST['payment_method']) && !empty($_POST['payment_method'])? $_POST['payment_method']: 'none';
                if ($payment_method == 'creditcard')
                {
                    $data['cc_owner'] = $_POST['cc_owner'];
                    $data['cc_num'] = $_POST['cc_num'];
                    $data['cc_exp'] = $_POST['cc_exp_year'] . '-' . $_POST['cc_exp_month'];
                    $data['cc_code'] = $_POST['cc_code'];
                }
                $data['payment_method'] = $payment_method;
                $data['voucher_code'] = $_POST['voucher_code'];
                $data['accept_shared_trip'] = isset($_POST['accept_shared_trip'])? 1: 0;
                $data['sub_total'] = $_POST['sub_total'];
                $data['discount'] = $_POST['discount'];
                $data['tax'] = $_POST['tax'];
                $data['total'] = $_POST['total'];
                $data['deposit'] = $_POST['deposit'];
                $data['extra_price'] = (float)$_POST['extra_price_first_transfe'];
                $data['google_map_link'] = $_POST['google_map_link'];
                $data['pickup_google_map_link'] = $_POST['pickup_google_map_link'];
				$data['dropoff_google_map_link'] = $_POST['dropoff_google_map_link'];
				if(isset($_POST['has_return'])) {
					$data['price'] = $_POST['price_first_transfer'];
				} else {
					$data['price'] = $_POST['price'];
				}
				$pjBookingModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll($data);
				
				if (isset($_FILES['name_sign']) && !empty($_FILES['name_sign']['tmp_name']))
				{
				    @unlink($arr['name_sign']);
				    $file_path = PJ_UPLOAD_PATH.'files/'.$_FILES['name_sign']['name'];
				    if (@move_uploaded_file($_FILES['name_sign']['tmp_name'], $file_path)) {
				        $pjBookingModel->reset()->set('id', $arr['id'])->modify(array('name_sign' => $file_path));
				    }
				}

				$returns = $pjBookingModel->reset()->where('return_id', $_POST['id'])->limit(1)->findAll()->getDataIndex(0);
				if(isset($_POST['has_return']))
				{
				    $return_uuid = pjAppController::createRandomBookingId();
                    $data['return_id'] = $_POST['id'];
                    
                    $data['station_id'] = ':NULL';
					$data['station_fee'] = ':NULL';
                    
                    $data['booking_date'] = $data['return_date'];
                    $data['return_date'] = ':NULL';
                    $data['uuid'] = $returns && !empty($returns['uuid']) ? $returns['uuid'] : $return_uuid;
                    $data['created'] = $arr['created'];
                    $data['c_notes'] = $_POST['return_c_notes'];
                    $data['c_flight_time'] = ':NULL';
                    $data['c_flight_number'] = ':NULL';
                    $data['c_airline_company'] = ':NULL';
                    $data['c_destination_address'] = ':NULL';
                    $data['c_departure_flight_time'] = ':NULL';
                    $data['c_address'] = ':NULL';
                    $data['c_hotel'] = ':NULL';
                    $data['status'] = $_POST['status_return_trip'];
                    $data['passengers'] = $_POST['passengers_return'];
					$data['price'] = $_POST['price_return_transfer'];
					$data['google_map_link'] = $_POST['return_google_map_link'];
					$data['pickup_google_map_link'] = $_POST['return_pickup_google_map_link'];
					$data['dropoff_google_map_link'] = $_POST['return_dropoff_google_map_link'];
					$data['internal_notes'] = $_POST['return_internal_notes'];
					$data['pickup_is_airport'] = $_POST['dropoff_is_airport'];
					$data['dropoff_is_airport'] = $_POST['pickup_is_airport'];
                    if ($_POST['pickup_is_airport'] == 0 && $_POST['dropoff_is_airport'] == 0) {
                    	 $data['c_address'] = $_POST['return_cl_address'];
                    	 $data['c_destination_address'] = $_POST['return_cl_destination_address'];
                    } else {
						if($_POST['pickup_is_airport'] == 1)
	                    {
	                        $data['c_departure_flight_time'] = str_pad($_POST['return_c_departure_flight_time_h'], 2, 0, STR_PAD_LEFT) . ':' . str_pad($_POST['return_c_departure_flight_time_m'], 2, 0, STR_PAD_LEFT);
	                        $data['c_address'] = $_POST['return_c_address'];
	                    }
	                    else
	                    {
	                        $data['c_flight_time'] = $return_time;
	                        $data['c_flight_number'] = $_POST['return_c_flight_number'];
	                        $data['c_airline_company'] = $_POST['return_c_airline_company'];
	                    }
                    }
                    $data['extra_price'] = (float)$_POST['extra_price_return_transfe'];
                    $data['region'] = $dropoff_region;
                    $data['dropoff_region'] = $region;
                    
                    $data['pickup_lat'] = $_POST['dropoff_lat'];
                    $data['pickup_lng'] = $_POST['dropoff_lng'];
                    
                    $data['dropoff_lat'] = $_POST['pickup_lat'];
                    $data['dropoff_lng'] = $_POST['pickup_lng'];
                    
					if($returns)
					{
                        $return_id = $returns['id'];
						$pjBookingModel->reset()->where('id', $returns['id'])->limit(1)->modifyAll($data);
					}else{
                        $data['ip'] = $arr['ip'];
                        $data['client_id'] = $arr['client_id'];
                        $return_id = $pjBookingModel->reset()->setAttributes($data)->insert()->getInsertId();
					}
				}else{
					if($returns)
					{
						$pjBookingModel->reset()->setAttributes(array('id' => $returns['id']))->erase();
                        pjBookingExtraModel::factory()->where('booking_id', $returns['id'])->eraseAll();
                        pjApiSync::syncBooking(array($returns['id']), 'delete', $this->option_arr);	
					}
				}

                pjBookingExtraModel::factory()->saveExtras($_POST['extras'], @$_POST['extras_return'], $arr['id'], @$return_id);

                $new_arr = $pjBookingModel->reset()
                ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjMultiLang', "t5.model='pjDropoff' AND t5.foreign_id=t1.dropoff_id AND t5.field='address' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
                ->select("t1.*, t2.content as fleet, t3.content as location, CONCAT_WS(' - ', t4.content, t5.content) as dropoff")
                ->find($arr['id'])
                ->getData();
                
                if($data['driver_id'] != ':NULL' && $data['driver_id'] != $arr['driver_id'] && $this->option_arr['o_email_driver'] == 1)
                {
                    $driver = pjDriverModel::factory()->select('email')->find($data['driver_id'])->getData();
                    if($driver && !empty($driver['email']))
                    {
                        $tokens = pjAppController::getTokens($this->option_arr, $new_arr, PJ_SALT, $this->getLocaleId());

                        $lang_message = pjMultiLangModel::factory()
                            ->select('t1.content')
                            ->where('t1.model','pjOption')
                            ->where('t1.locale', $this->getLocaleId())
                            ->where('t1.field', 'o_email_driver_message')
                            ->limit(1)
                            ->findAll()
                            ->getDataIndex(0);
                        $lang_subject = pjMultiLangModel::factory()
                            ->select('t1.content')
                            ->where('t1.model','pjOption')
                            ->where('t1.locale', $this->getLocaleId())
                            ->where('t1.field', 'o_email_driver_subject')
                            ->limit(1)
                            ->findAll()
                            ->getDataIndex(0);
                        if(!empty($lang_message['content']) && !empty($lang_subject['content']))
                        {
                            $html = pjUtil::fileGetContents(PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionDriverPDF&id='.$new_arr['id']);
                            $name = "name_plate_{$new_arr['uuid']}.pdf";
                            $pjPdf = new pjPdf();
                            $filePath = $pjPdf->generatePdf($name, $html);

                            $pjEmail = new pjEmail();
                            if ($this->option_arr['o_send_email'] == 'smtp')
                            {
                                $pjEmail
                                    ->setTransport('smtp')
                                    ->setSmtpHost($this->option_arr['o_smtp_host'])
                                    ->setSmtpPort($this->option_arr['o_smtp_port'])
                                    ->setSmtpUser($this->option_arr['o_smtp_user'])
                                    ->setSmtpPass($this->option_arr['o_smtp_pass'])
                                ;
                            }
                            $pjEmail->setContentType('text/html');

                            $subject = pjAppController::replaceTokens($new_arr, $tokens, $lang_subject['content']);
                            $message = pjAppController::replaceTokens($new_arr, $tokens, $lang_message['content']);

                            $pjEmail
                                ->setTo($driver['email'])
                                ->setFrom($this->getAdminEmail(), $this->option_arr['o_email_sender'])
                                ->setSubject($subject)
                                ->attach($filePath, $name, 'application/pdf')
                                ->send(pjAppController::getEmailBody($message));
                        }
                    }
                }
                if (($_POST['status'] == 'confirmed' || ($arr['status'] == 'confirmed' && $_POST['status'] == 'cancelled')) || 
                	(($_POST['status_return_trip'] == 'confirmed' || (@$returns['status'] == 'confirmed' && $_POST['status_return_trip'] == 'cancelled')) && isset($return_id) && (int)$return_id > 0)
                ) {
					$resp = pjApiSync::syncBooking($_POST['id'], 'update', $this->option_arr);
					if (($_POST['status_return_trip'] == 'confirmed' || ($returns['status'] == 'confirmed' && $_POST['status_return_trip'] == 'cancelled')) && isset($return_id) && (int)$return_id > 0) {
						$resp = pjApiSync::syncBooking($return_id, 'update', $this->option_arr);
					}
                } else {
                	$ids_arr = array();
                	if ($original_booking_arr['status'] == 'confirmed' && $_POST['status'] != 'confirmed') {
                		$ids_arr[] = $_POST['id'];	
                	}
                	if (isset($return_id) && (int)$return_id > 0 && @$returns['status'] == 'confirmed' && $_POST['status_return_trip'] != 'confirmed') {
                		$ids_arr[] = $return_id;
                	}
                	if ($ids_arr) {
                		$resp = pjApiSync::syncBooking($ids_arr, 'delete', $this->option_arr);	
                	}
                }
				$err = 'ABB01';
				
				if(isset($_POST['has_return'])) {
				    $return_new_arr = $pjBookingModel->reset()
				    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				    ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				    ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
				    ->join('pjMultiLang', "t5.model='pjDropoff' AND t5.foreign_id=t1.dropoff_id AND t5.field='address' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
				    ->select("t1.*, t2.content as fleet, t3.content as location, CONCAT_WS(' - ', t4.content, t5.content) as dropoff")
				    ->find($return_id)
				    ->getData();
				}
				
				/* check changes to save into history */
				$booking_statuses = __('booking_statuses', true);
				$payment_methods = __('payment_methods', true);
				$yesno = __('_yesno', true);
				$name_titles = __('personal_titles', true, false);
				$labels = array_merge($booking_statuses, $payment_methods, $yesno, $name_titles);
				$map_columns = pjBookingModel::factory()->getMapColumns;
				
				$data_history = array();
				$result_assoc = array_diff_assoc($original_booking_arr, $new_arr);
				if ($result_assoc) {
				    foreach ($result_assoc as $k => $v) {
				        if (isset($map_columns[$k]) && isset($original_booking_arr[$k]) && isset($new_arr[$k])) {
				            if (isset($map_columns[$k]['value'])) {
				                $old_value = $original_booking_arr[$map_columns[$k]['value']];
				                $new_value = $new_arr[$map_columns[$k]['value']];
				            } else {
				                $old_value = $original_booking_arr[$k];
				                $new_value = $new_arr[$k];
				            }
				            $old_value = empty($old_value) ? 'empty' : $old_value;
				            $new_value = empty($new_value) ? 'empty' : $new_value;
				            
				            if ($map_columns[$k]['type'] == 'bolean') {
				                $data_history[] = $map_columns[$k]['label'].'['.@$labels[$old_value].'] -> ['.@$labels[$new_value].']';
				            } elseif ($map_columns[$k]['type'] == 'datetime') {
				                $data_history[] = $map_columns[$k]['label'].'['.date($this->option_arr['o_date_format'].' '.$this->option_arr['o_time_format'], strtotime($old_value)).'] -> ['.date($this->option_arr['o_date_format'].' '.$this->option_arr['o_time_format'], strtotime($new_value)).']';
				            } elseif (in_array($k, array('c_dialing_code','c_phone'))) {
				                $old_value = $original_booking_arr['c_dialing_code'].''.$original_booking_arr['c_phone'];
				                $new_value = $new_arr['c_dialing_code'].''.$new_arr['c_phone'];
				                $data_history[] = $map_columns['c_phone']['label'].'['.$old_value.'] -> ['.$new_value.']';
				            } elseif ($map_columns[$k]['type'] == 'currency') {
				                $old_value = pjUtil::formatCurrencySign(number_format($old_value, 2), $this->option_arr['o_currency']);
				                $new_value = pjUtil::formatCurrencySign(number_format($new_value, 2), $this->option_arr['o_currency']);
				                $data_history[] = $map_columns[$k]['label'].'['.$old_value.'] -> ['.$new_value.']';
				            } elseif ($map_columns[$k]['type'] == 'image') {
				                $data_history[] = $map_columns[$k]['label'];
				            } else {
				                $data_history[] = $map_columns[$k]['label'].'['.$old_value.'] -> ['.$new_value.']';
				            }
				        }
				    }
				}
				
				$return_data_history = array();
				if (!empty($original_booking_arr['return_date']) && empty($new_arr['return_date'])) {
				    $data_history[] = 'Removed return trip';
				} elseif (empty($original_booking_arr['return_date']) && !empty($new_arr['return_date'])) {
				    $data_history[] = 'Added return trip';
				} elseif (!empty($original_booking_arr['return_date']) && !empty($new_arr['return_date'])) {
				    $return_result_assoc = array_diff_assoc($return_arr, $return_new_arr);
				    if ($return_result_assoc) {
				        $return_map_columns = $pjBookingModel->returnGetMapColumns;
				        foreach ($return_result_assoc as $k => $v) {
				            if (!in_array($k, $return_map_columns)) {
				                continue;
				            }
				            if (isset($map_columns[$k]) && isset($return_arr[$k]) && isset($return_new_arr[$k])) {
				                if (isset($map_columns[$k]['value'])) {
				                    $old_value = $return_arr[$map_columns[$k]['value']];
				                    $new_value = $return_new_arr[$map_columns[$k]['value']];
				                } else {
				                    $old_value = $return_arr[$k];
				                    $new_value = $return_new_arr[$k];
				                }
				                $old_value = empty($old_value) ? 'empty' : $old_value;
				                $new_value = empty($new_value) ? 'empty' : $new_value;
				                
				                if ($map_columns[$k]['type'] == 'bolean') {
				                    $return_data_history[] = $map_columns[$k]['label'].'['.@$labels[$old_value].'] -> ['.@$labels[$new_value].']';
				                } elseif ($map_columns[$k]['type'] == 'datetime') {
				                    $return_data_history[] = $map_columns[$k]['label'].'['.date($this->option_arr['o_date_format'].' '.$this->option_arr['o_time_format'], strtotime($old_value)).'] -> ['.date($this->option_arr['o_date_format'].' '.$this->option_arr['o_time_format'], strtotime($new_value)).']';
				                } elseif (in_array($k, array('c_dialing_code','c_phone'))) {
				                    $old_value = $return_arr['c_dialing_code'].''.$return_arr['c_phone'];
				                    $new_value = $return_new_arr['c_dialing_code'].''.$return_new_arr['c_phone'];
				                    $return_data_history[] = $map_columns['c_phone']['label'].'['.$old_value.'] -> ['.$new_value.']';
				                } elseif ($map_columns[$k]['type'] == 'currency') {
				                    $old_value = pjUtil::formatCurrencySign(number_format($old_value, 2), $this->option_arr['o_currency']);
				                    $new_value = pjUtil::formatCurrencySign(number_format($new_value, 2), $this->option_arr['o_currency']);
				                    $return_data_history[] = $map_columns[$k]['label'].''.$old_value.' -> '.$new_value;
				                } elseif ($map_columns[$k]['type'] == 'image') {
				                    $return_data_history[] = $map_columns[$k]['label'];
				                } else {
				                    $return_data_history[] = $map_columns[$k]['label'].'['.$old_value.'] -> ['.$new_value.']';
				                }
				            }
				        }
				    }
				}
				if ($return_data_history) {
				    $data_history[] = '<br/>RETURN TRIP:';
				    $data_history = array_merge($data_history, $return_data_history);
				}
				
				if ($data_history) {
				    $data_log = array(
				        'booking_id' => $original_booking_arr['id'],
				        'action' => implode('<br/>', $data_history),
				        'user_id' => $this->getUserId()
				    );
				    pjBookingHistoryModel::factory()->setAttributes($data_log)->insert();
				}
				
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=$err");
			}else{
			    $pjBookingModel = pjBookingModel::factory();
			    // TODO: If it comes from the calendar in Dashboard and if the ID is for a return booking (without UUID etc) we should redirect to the first booking
			    if (isset($_REQUEST['id']) && (int) $_REQUEST['id'] > 0)
			    {
			        $pjBookingModel->where('t1.id', $_REQUEST['id']);
			    } elseif (isset($_GET['uuid']) && !empty($_GET['uuid'])) {
			        $pjBookingModel->where('t1.uuid', $_GET['uuid']);
			    }
			    
			    $arr = $pjBookingModel->limit(1)->findAll()->getDataIndex(0);

			    if(!$arr)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=ABB08");
				}

                $arr['extra_arr'] = pjBookingExtraModel::factory()
                    ->where('booking_id', $arr['id'])
                    ->findAll()
                    ->getDataPair('extra_id', 'quantity');

                $this->set('country_arr', pjCountryModel::factory()
                    ->select('t1.id, t2.content AS country_title, t3.code')
                    ->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjDialingCode', "t3.country_id=t1.id", 'left outer')
                    ->orderBy('`country_title` ASC')->findAll()->getData());

				$this->set('arr', $arr);

                $return_arr = array();
                if(!empty($arr['return_date']))
                {
                    $return_arr = pjBookingModel::factory()
                        ->where('return_id', $arr['id'])
                        ->findAll()
                        ->getDataIndex(0);
                        
                	if ($return_arr) {
                		$return_arr['extra_arr'] = pjBookingExtraModel::factory()->reset()
		                    ->where('booking_id', $return_arr['id'])
		                    ->findAll()
		                    ->getDataPair('extra_id', 'quantity');
                	}
                }
                $this->set('return_arr', $return_arr);

                $this->set('extra_arr', pjExtraModel::factory()
                    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.field='info' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->select("t1.*, t2.content as name, t3.content as info")
                    ->where('t1.status', 'T')
                    ->orderBy("t1.id ASC")
                    ->findAll()
                    ->getData());

                $this->set('driver_arr', pjDriverModel::factory()
                    ->where('status', 'T')
                    ->orderBy("fname ASC, lname ASC")
                    ->findAll()
                    ->getData());

                $pickup_arr = pjLocationModel::factory()
					->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->select("t1.*, t2.content as pickup_location")
					->where('t1.status', 'T')
					->orderBy("is_airport DESC, pickup_location ASC")
					->findAll()->getData();
				foreach ($pickup_arr as $k => $v) {
					if ($v['icon'] == 'airport') {
						$materialIcon = 'local_airport';
					} elseif ($v['icon'] == 'train') {
						$materialIcon = 'train_station';
					} else {
						$materialIcon = 'place';
					}	
					$pickup_arr[$k]['icon'] = $materialIcon;
					$pickup_arr[$k]['text'] = $v['pickup_location'];
					$pickup_arr[$k]['id_formated'] = 'server~::~'.$v['id'];
				}
				$this->set('pickup_arr', $pickup_arr);
				$_SESSION[$this->defaultPickupLocations] = $pickup_arr;

				$this->set('fleet_arr', pjFleetModel::factory()
                    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjStation' AND t3.foreign_id=t1.station_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->select("t1.*, t2.content as fleet, t3.content as station_name")
                    ->where('t1.status', 'T')
                    ->orderBy("fleet ASC")
                    ->findAll()->getData());

				$dropoff_place_arr = array();
				if ($arr['pickup_type'] == 'server' || (int)$arr['pickup_id'] > 0) {
					$location_id = (int)$arr['pickup_id'] > 0 ? (int)$arr['pickup_id'] : $arr['location_id'];
					$dropoff_arr = pjDropoffModel::factory()
						->select("t1.*, t2.content as location")
						->join('pjMultiLang', "t2.model='pjDropoff' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->where('t1.location_id', $location_id)
						->orderBy("t1.is_airport DESC, t1.order_index ASC, location ASC")
						->findAll()->getData();
					$dropoff_ids_arr = array();
					foreach ($dropoff_arr as $v) {
						$dropoff_ids_arr[] = $v['id'];
					}
					
					if ($dropoff_ids_arr) {
						$dropoff_place_arr = pjDropoffAreaModel::factory()->select('t1.dropoff_id, t4.*, t5.content AS area_name, t6.content AS place_name')
							->join('pjDropoff', 't2.id=t1.dropoff_id', 'inner')
							->join('pjArea', 't3.id=t1.area_id', 'inner')
							->join('pjAreaCoord', 't4.area_id=t3.id', 'inner')
							->join('pjMultiLang', "t5.model='pjArea' AND t5.foreign_id=t1.area_id AND t5.field='name' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
							->join('pjMultiLang', "t6.model='pjAreaCoord' AND t6.foreign_id=t4.id AND t6.field='place_name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
							->whereIn('t1.dropoff_id', $dropoff_ids_arr)
							->where('t4.is_disabled', 0)
							->orderBy('t1.dropoff_id ASC, t3.order_index ASC, t5.content ASC')
							->findAll()
							->getData();
					}
					foreach ($dropoff_place_arr as $k => $v) {
						if ($v['icon'] == 'airport') {
			    			$materialIcon = 'local_airport';
			    		} elseif ($v['icon'] == 'train') {
			    			$materialIcon = 'train_station';
			    		} else {
			    			$materialIcon = 'place';
			    		}	
			    		$dropoff_place_arr[$k]['icon'] = $materialIcon;
			    		$dropoff_place_arr[$k]['text'] = $v['place_name'];
			    		$dropoff_place_arr[$k]['id_formated'] = 'server~::~'.$v['id'].'~::~'.$v['dropoff_id'];
					}
				}
				$this->set('dropoff_place_arr', $dropoff_place_arr);	
				$_SESSION[$this->defaultDropoffLocations] = $dropoff_place_arr;		
				
				$this->set('email_theme_arr', pjEmailThemeModel::factory()
				    ->join('pjMultiLang', "t2.model='pjEmailTheme' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				    ->select("t1.*, t2.content as name")
				    ->where('t1.status', 'T')
				    ->orderBy("name ASC")
				    ->findAll()->getData());
				
				//$invoice_arr = $this->pjActionGenerateInvoice($arr['id']);
				
				$booking_arr = pjBookingModel::factory()->reset()
				->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale=t1.locale_id", 'left outer')
				->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale=t1.locale_id", 'left outer')
				->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale=t1.locale_id", 'left outer')
				->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
				->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale=t1.locale_id", 'left outer')
				->find($arr['id'])
				->getData();
				
				$booking_extra_arr = pjBookingExtraModel::factory()->reset()
				->select('t1.*, t3.content as name, t4.content as info, t5.price')
				->join('pjBooking', 't2.id=t1.booking_id', 'inner')
				->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.extra_id AND t3.field='name' AND t3.locale=t2.locale_id", 'left outer')
				->join('pjMultiLang', "t4.model='pjExtra' AND t4.foreign_id=t1.extra_id AND t4.field='info' AND t4.locale=t2.locale_id", 'left outer')
				->join('pjExtra', 't5.id=t1.extra_id', 'inner')
				->where('t1.booking_id', $arr['id'])
				->findAll()
				->getData();
				$this->set('booking_arr', $booking_arr);
				$this->set('booking_extra_arr', $booking_extra_arr);
				
				$return_booking_extra_arr = array();
				if ($return_arr) {
				    $return_booking_extra_arr = pjBookingExtraModel::factory()->reset()
				    ->select('t1.*, t3.content as name, t4.content as info, t5.price')
				    ->join('pjBooking', 't2.id=t1.booking_id', 'inner')
				    ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.extra_id AND t3.field='name' AND t3.locale=t2.locale_id", 'left outer')
				    ->join('pjMultiLang', "t4.model='pjExtra' AND t4.foreign_id=t1.extra_id AND t4.field='info' AND t4.locale=t2.locale_id", 'left outer')
				    ->join('pjExtra', 't5.id=t1.extra_id', 'inner')
				    ->where('t1.booking_id', $return_arr['id'])
				    ->findAll()
				    ->getData();
				}
				$this->set('return_booking_extra_arr', $return_booking_extra_arr);
				
				$invoice_tax_arr = pjInvoiceTaxModel::factory()->where('t1.is_default', 1)->limit(1)->findAll()->getDataIndex(0);
				$tax = $tax_percentage = 0;
				$tax_id = ':NULL';
				if ($invoice_tax_arr) {
				    $tax_percentage = $invoice_tax_arr['tax'];
				    $tax_id = $invoice_tax_arr['id'];
				}
				$this->set('tax_percentage', $tax_percentage);
				$this->set('tax_id', $tax_id);

				$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/js/');
				$this->appendCss('select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/css/');
				$this->appendJs('jquery-ui-sliderAccess.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery-ui-timepicker-addon.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendCss('jquery-ui-timepicker-addon.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('https://maps.googleapis.com/maps/api/js?key='.$this->option_arr['o_google_api_key'].'&sensor=false&libraries=places,geometry,drawing', NULL, true);
				$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('pjAdminBookings.js');
			}
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionGetDropoff()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$type = 'server';
			if (isset($_GET['is_search'])) {
				$location_id = $_GET['location_id'];
			} elseif (!empty($_GET['location_id'])) {
				list($type, $location_id) = explode('~::~', $_GET['location_id']);
			}
			$dropoff_arr = $dropoff_place_arr = array();
			if ($type == 'server' || (isset($_GET['pickup_id']) && (int)$_GET['pickup_id'] > 0)) {
				$pjDropoffModel = pjDropoffModel::factory();
				if (isset($_GET['pickup_id']) && (int)$_GET['pickup_id'] > 0) {
					$pjDropoffModel->where('t1.location_id', (int)$_GET['pickup_id']);
				} else if ((int)$location_id > 0) {
					$pjDropoffModel->where('t1.location_id', $location_id);
				}
				$dropoff_arr = $pjDropoffModel
					->select("t1.*, t2.content as location")
					->join('pjMultiLang', "t2.model='pjDropoff' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')					
					->orderBy("t1.is_airport DESC, t1.order_index ASC, location ASC")
					->findAll()->getData();
				$dropoff_ids_arr = array();
				foreach ($dropoff_arr as $v) {
					$dropoff_ids_arr[] = $v['id'];
				}
				
				if ($dropoff_ids_arr) {
					$dropoff_place_arr = pjDropoffAreaModel::factory()->select('t1.dropoff_id, t4.*, t5.content AS area_name, t6.content AS place_name')
						->join('pjDropoff', 't2.id=t1.dropoff_id', 'inner')
						->join('pjArea', 't3.id=t1.area_id', 'inner')
						->join('pjAreaCoord', 't4.area_id=t3.id', 'inner')
						->join('pjMultiLang', "t5.model='pjArea' AND t5.foreign_id=t1.area_id AND t5.field='name' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
						->join('pjMultiLang', "t6.model='pjAreaCoord' AND t6.foreign_id=t4.id AND t6.field='place_name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
						->whereIn('t1.dropoff_id', $dropoff_ids_arr)
						->where('t4.is_disabled', 0)
						->orderBy('t1.dropoff_id ASC, t3.order_index ASC, t5.content ASC')
						->findAll()
						->getData();
					foreach ($dropoff_place_arr as $k => $v) {
						if ($v['icon'] == 'airport') {
			    			$materialIcon = 'local_airport';
			    		} elseif ($v['icon'] == 'train') {
			    			$materialIcon = 'train_station';
			    		} else {
			    			$materialIcon = 'place';
			    		}	
			    		$dropoff_place_arr[$k]['icon'] = $materialIcon;
			    		$dropoff_place_arr[$k]['text'] = $v['place_name'];
			    		$dropoff_place_arr[$k]['id_formated'] = 'server~::~'.$v['id'].'~::~'.$v['dropoff_id'];
					}
				}
			}
			$this->set('dropoff_arr', $dropoff_arr);
			$this->set('dropoff_place_arr', $dropoff_place_arr);
			$_SESSION[$this->defaultDropoffLocations] = $dropoff_place_arr;
		}
	}

	public function pjActionResend()
	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['reminder']))
			{
				$pjEmail = new pjEmail();
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$pjEmail
						->setTransport('smtp')
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpPort($this->option_arr['o_smtp_port'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass'])
					;
				}
				$locale_id = isset($_POST['locale_id']) && (int)$_POST['locale_id'] > 0 ? (int)$_POST['locale_id'] : $this->getLocaleId();
                $pjEmail->setContentType('text/html');
				$pjEmail
					->setTo($_POST['to'])
					->setFrom($this->getAdminEmail(), $this->option_arr['o_email_sender'])
					->setSubject($_POST['i18n'][$locale_id]['subject']);
				if ($pjEmail->send(pjAppController::getEmailBody($_POST['i18n'][$locale_id]['message'])))
				{
				    $data_history = array(
				        'booking_id' => $_POST['id'],
				        'action' => $_POST['i18n'][$locale_id]['subject'].' -> sent to ['.$_POST['to'].']',
				        'user_id' => $this->getUserId()
				    );
				    pjBookingHistoryModel::factory()->setAttributes($data_history)->insert();
				    
					$err = 'AB09';
				} else {
					$err = 'AB10';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=$err");
			} else {
			    $arr = pjBookingModel::factory()
					->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
					->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
					->find($_GET['id'])
					->getData();

				if (count($arr) === 0)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AB08");
				}				
				$this->set('arr', $arr);
				
				$i18n = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = $i18n_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
					
					$lang_subject = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_confirmation_subject']);
					$i18n_arr[$item['id']]['subject'] = $lang_subject;
					
					$lang_message = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_confirmation_message']);
					$i18n_arr[$item['id']]['message'] = $lang_message;
				}
				$this->set('i18n_arr', $i18n_arr);
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
                $this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminBookings.js');
			}
		} else {
			$this->set('status', 2);
		}
	}

	public function pjActionSendSms()
	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['send_sms']) && isset($_POST['to']) && !empty($_POST['to']) && !empty($_POST['id']))
			{
				$locale_id = isset($_POST['locale_id']) && (int)$_POST['locale_id'] > 0 ? (int)$_POST['locale_id'] : $this->getLocaleId();
				/*$params = array(
					'text' => $_POST['i18n'][$locale_id]['message'],
					'type' => 'unicode',
					'key' => md5($this->option_arr['private_key'] . PJ_SALT)
				);

				$params['number'] = $_POST['to'];
				$result = $this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));*/
				
				$result = $this->messagebirdSendSMS(array($_POST['to']), $_POST['i18n'][$locale_id]['message'], $this->option_arr);

				if ($result) {
    				$data_log = array(
    				    'booking_id' => $_POST['id'],
    				    'action' => $_POST['i18n'][$locale_id]['message'],
    				    'user_id' => $this->getUserId()
    				);
    				pjBookingHistoryModel::factory()->setAttributes($data_log)->insert();
				}
				
				if (isset($result) && (int) $result === 1)
				{
					$err = 'AB11';
				}else{
					$err = 'AB12';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionUpdate&id=".$_POST['id']."&err=$err");
			} else {

				$arr = pjBookingModel::factory()
					->select("t1.*, t2.content as fleet, t3.content as location, CONCAT_WS(' - ', t4.content, t5.content) as dropoff")
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t5.model='pjDropoff' AND t5.foreign_id=t1.dropoff_id AND t5.field='address' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
					->find($_GET['id'])
					->getData();

				if (count($arr) === 0)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AB08");
				}

				$this->set('arr', $arr);
                
                $i18n = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = $i18n_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
					$lang_message = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_sms_confirmation_message']);
					$i18n_arr[$item['id']]['message'] = $lang_message;
				}
				$this->set('i18n_arr', $i18n_arr);
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminBookings.js');
			}
		} else {
			$this->set('status', 2);
		}
	}

    public function pjActionEmailPaymentConfirmation()
    {
        $this->checkLogin();

        if ($this->isAdmin() || $this->isEditor())
        {
            if (isset($_POST['email_payment_confirmation']))
            {
                $pjEmail = new pjEmail();
                if ($this->option_arr['o_send_email'] == 'smtp')
                {
                    $pjEmail
                        ->setTransport('smtp')
                        ->setSmtpHost($this->option_arr['o_smtp_host'])
                        ->setSmtpPort($this->option_arr['o_smtp_port'])
                        ->setSmtpUser($this->option_arr['o_smtp_user'])
                        ->setSmtpPass($this->option_arr['o_smtp_pass'])
                    ;
                }
                $locale_id = isset($_POST['locale_id']) && (int)$_POST['locale_id'] > 0 ? (int)$_POST['locale_id'] : $this->getLocaleId();
                $pjEmail->setContentType('text/html');
                $pjEmail
                    ->setTo($_POST['to'])
                    ->setFrom($this->getAdminEmail(), $this->option_arr['o_email_sender'])
                    ->setSubject($_POST['i18n'][$locale_id]['subject']);
                if ($pjEmail->send(pjAppController::getEmailBody($_POST['i18n'][$locale_id]['message'])))
                {
                    $data_history = array(
                        'booking_id' => $_POST['id'],
                        'action' => $_POST['i18n'][$locale_id]['subject'].' -> sent to ['.$_POST['to'].']',
                        'user_id' => $this->getUserId()
                    );
                    pjBookingHistoryModel::factory()->setAttributes($data_history)->insert();
                    
                    $err = 'AB09';
                } else {
                    $err = 'AB10';
                }
                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=$err");
            } else {

                $arr = pjBookingModel::factory()
                    ->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
                    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
					->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
                    ->find($_GET['id'])
                    ->getData();

                if (count($arr) === 0)
                {
                    pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AB08");
                }

                $this->set('arr', $arr);
                
                $i18n = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = $i18n_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
					
					$lang_subject = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_payment_subject']);
					$i18n_arr[$item['id']]['subject'] = $lang_subject;
					
					$lang_message = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_payment_message']);
					$i18n_arr[$item['id']]['message'] = $lang_message;
				}
				$this->set('i18n_arr', $i18n_arr);
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
                $this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
                $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
                $this->appendJs('pjAdminBookings.js');
            }
        } else {
            $this->set('status', 2);
        }
    }

    public function pjActionEmailCancellation()
    {
        $this->checkLogin();

        if ($this->isAdmin() || $this->isEditor())
        {
            if (isset($_POST['email_cancellation']))
            {
                $pjEmail = new pjEmail();
                if ($this->option_arr['o_send_email'] == 'smtp')
                {
                    $pjEmail
                        ->setTransport('smtp')
                        ->setSmtpHost($this->option_arr['o_smtp_host'])
                        ->setSmtpPort($this->option_arr['o_smtp_port'])
                        ->setSmtpUser($this->option_arr['o_smtp_user'])
                        ->setSmtpPass($this->option_arr['o_smtp_pass'])
                    ;
                }
                $locale_id = isset($_POST['locale_id']) && (int)$_POST['locale_id'] > 0 ? (int)$_POST['locale_id'] : $this->getLocaleId();
                $pjEmail->setContentType('text/html');
                $pjEmail
                    ->setTo($_POST['to'])
                    ->setFrom($this->getAdminEmail(), $this->option_arr['o_email_sender'])
                    ->setSubject($_POST['i18n'][$locale_id]['subject']);
                if ($pjEmail->send(pjAppController::getEmailBody($_POST['i18n'][$locale_id]['message'])))
                {
                    $data_history = array(
                        'booking_id' => $_POST['id'],
                        'action' => $_POST['i18n'][$locale_id]['subject'].' -> sent to ['.$_POST['to'].']',
                        'user_id' => $this->getUserId()
                    );
                    pjBookingHistoryModel::factory()->setAttributes($data_history)->insert();
                    
                    $err = 'AB09';
                } else {
                    $err = 'AB10';
                }
                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=$err");
            } else {

                $arr = pjBookingModel::factory()
                    ->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
                    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
					->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
                    ->find($_GET['id'])
                    ->getData();

                if (count($arr) === 0)
                {
                    pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AB08");
                }

                $this->set('arr', $arr);
                
                $i18n = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = $i18n_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
					
					$lang_subject = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_cancel_subject']);
					$i18n_arr[$item['id']]['subject'] = $lang_subject;
					
					$lang_message = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_cancel_message']);
					$i18n_arr[$item['id']]['message'] = $lang_message;
				}
				$this->set('i18n_arr', $i18n_arr);
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
                $this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
                $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
                $this->appendJs('pjAdminBookings.js');
            }
        } else {
            $this->set('status', 2);
        }
    }

    public function pjActionEmailReminder()
    {
        $this->checkLogin();

        if ($this->isAdmin() || $this->isEditor())
        {
            if (isset($_POST['email_reminder']))
            {
                $pjEmail = new pjEmail();
                if ($this->option_arr['o_send_email'] == 'smtp')
                {
                    $pjEmail
                        ->setTransport('smtp')
                        ->setSmtpHost($this->option_arr['o_smtp_host'])
                        ->setSmtpPort($this->option_arr['o_smtp_port'])
                        ->setSmtpUser($this->option_arr['o_smtp_user'])
                        ->setSmtpPass($this->option_arr['o_smtp_pass'])
                    ;
                }
                $locale_id = isset($_POST['locale_id']) && (int)$_POST['locale_id'] > 0 ? (int)$_POST['locale_id'] : $this->getLocaleId();
                $pjEmail->setContentType('text/html');
                $pjEmail
                    ->setTo($_POST['to'])
                    ->setFrom($this->getAdminEmail(), $this->option_arr['o_email_sender'])
                    ->setSubject($_POST['i18n'][$locale_id]['subject']);
                if ($pjEmail->send(pjAppController::getEmailBody($_POST['i18n'][$locale_id]['message'])))
                {
                    $data_history = array(
                        'booking_id' => $_POST['id'],
                        'action' => $_POST['i18n'][$locale_id]['subject'].' -> sent to ['.$_POST['to'].']',
                        'user_id' => $this->getUserId()
                    );
                    pjBookingHistoryModel::factory()->setAttributes($data_history)->insert();
                    
                    $err = 'AB09';
                } else {
                    $err = 'AB10';
                }
                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=$err");
            } else {

                $arr = pjBookingModel::factory()
                    ->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
                    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
					->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
                    ->find($_GET['id'])
                    ->getData();

                if (count($arr) === 0)
                {
                    pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AB08");
                }

                $this->set('arr', $arr);
                
                $i18n = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = $i18n_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
					
					$lang_subject = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_reminder_subject']);
					$i18n_arr[$item['id']]['subject'] = $lang_subject;
					
					$lang_message = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_reminder_message']);
					$i18n_arr[$item['id']]['message'] = $lang_message;
				}
				$this->set('i18n_arr', $i18n_arr);
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
                $this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
                $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
                $this->appendJs('pjAdminBookings.js');
            }
        } else {
            $this->set('status', 2);
        }
    }

    public function pjActionSmsReminder()
    {
        $this->checkLogin();

        if ($this->isAdmin() || $this->isEditor())
        {
            if (isset($_POST['sms_reminder']) && isset($_POST['to']) && !empty($_POST['to']) && !empty($_POST['id']))
            {
            	$locale_id = isset($_POST['locale_id']) && (int)$_POST['locale_id'] > 0 ? (int)$_POST['locale_id'] : $this->getLocaleId();
                /*$params = array(
                    'text' => $_POST['i18n'][$locale_id]['message'],
                    'type' => 'unicode',
                    'key' => md5($this->option_arr['private_key'] . PJ_SALT)
                );

                $params['number'] = $_POST['to'];
                $result = $this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));*/

                $result = $this->messagebirdSendSMS(array($_POST['to']), $_POST['i18n'][$locale_id]['message'], $this->option_arr);
                if ($result) {
                    $data_log = array(
                        'booking_id' => $_POST['id'],
                        'action' => $_POST['i18n'][$locale_id]['message'],
                        'user_id' => $this->getUserId()
                    );
                    pjBookingHistoryModel::factory()->setAttributes($data_log)->insert();
                    $err = 'AB11';
                } else {
                    $err = 'AB12';
                }
                
                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionUpdate&id=".$_POST['id']."&err=$err");
            } else {

                $arr = pjBookingModel::factory()
                    ->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
                    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
					->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
                    ->find($_GET['id'])
                    ->getData();

                if (count($arr) === 0)
                {
                    pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AB08");
                }

                $this->set('arr', $arr);
                
                $i18n = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = $i18n_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
					
					$lang_message = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_sms_reminder_message']);
					$i18n_arr[$item['id']]['message'] = $lang_message;
				}
				$this->set('i18n_arr', $i18n_arr);
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
                $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
                $this->appendJs('pjAdminBookings.js');
            }
        } else {
            $this->set('status', 2);
        }
    }

    public function pjActionEmailReturnReminder()
    {
        $this->checkLogin();

        if ($this->isAdmin() || $this->isEditor())
        {
            if (isset($_POST['email_return_reminder']))
            {
                $pjEmail = new pjEmail();
                if ($this->option_arr['o_send_email'] == 'smtp')
                {
                    $pjEmail
                        ->setTransport('smtp')
                        ->setSmtpHost($this->option_arr['o_smtp_host'])
                        ->setSmtpPort($this->option_arr['o_smtp_port'])
                        ->setSmtpUser($this->option_arr['o_smtp_user'])
                        ->setSmtpPass($this->option_arr['o_smtp_pass'])
                    ;
                }
                $locale_id = isset($_POST['locale_id']) && (int)$_POST['locale_id'] > 0 ? (int)$_POST['locale_id'] : $this->getLocaleId();
                $pjEmail->setContentType('text/html');
                $pjEmail
                    ->setTo($_POST['to'])
                    ->setFrom($this->getAdminEmail(), $this->option_arr['o_email_sender'])
                    ->setSubject($_POST['i18n'][$locale_id]['subject']);
                if ($pjEmail->send(pjAppController::getEmailBody($_POST['i18n'][$locale_id]['message'])))
                {
                    $data_history = array(
                        'booking_id' => $_POST['id'],
                        'action' => $_POST['i18n'][$locale_id]['subject'].' -> sent to ['.$_POST['to'].']',
                        'user_id' => $this->getUserId()
                    );
                    pjBookingHistoryModel::factory()->setAttributes($data_history)->insert();
                    
                    $err = 'AB09';
                } else {
                    $err = 'AB10';
                }
                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=$err");
            } else {

                $arr = pjBookingModel::factory()
                    ->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
                    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
					->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
                    ->find($_GET['id'])
                    ->getData();

                if (count($arr) === 0)
                {
                    pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AB08");
                }

                $this->set('arr', $arr);
                
                $i18n = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = $i18n_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
					
					$lang_subject = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_return_reminder_subject']);
					$i18n_arr[$item['id']]['subject'] = $lang_subject;
					
					$lang_message = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_return_reminder_message']);
					$i18n_arr[$item['id']]['message'] = $lang_message;
				}
				$this->set('i18n_arr', $i18n_arr);
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
                $this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
                $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
                $this->appendJs('pjAdminBookings.js');
            }
        } else {
            $this->set('status', 2);
        }
    }

	public function pjActionEmailReturnReminderBulk()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {

			$bookings = pjBookingModel::factory()
				->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
				->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')		
				->whereIn('t1.id', $_POST['records'])
				->findAll()
				->getData();

			if (!empty($bookings)) {
				$i18n = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
				$locale_arr = pjLocaleModel::factory()
					->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')
					->findAll()
					->getData();

				foreach ($bookings as $booking) {
					if (!empty($booking['return_id'])) {
						$booking = pjBookingModel::factory()
							->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
							->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
							->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
							->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
							->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
							->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')	
							->find($booking['return_id'])
							->getData();
					}

					$lp_arr = $i18n_arr = array();
					foreach ($locale_arr as $item) {
						$lp_arr[$item['id']."_"] = $item['file'];
						
						$lang_subject = pjAppController::replaceTokens($booking, pjAppController::getTokens($this->option_arr, $booking, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_return_reminder_subject']);
						$i18n_arr[$item['id']]['subject'] = $lang_subject;

						$lang_message = pjAppController::replaceTokens($booking, pjAppController::getTokens($this->option_arr, $booking, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_return_reminder_message']);
						$i18n_arr[$item['id']]['message'] = $lang_message;
					}

					$pjEmail = new pjEmail();
					if ($this->option_arr['o_send_email'] == 'smtp') {
						$pjEmail
							->setTransport('smtp')
							->setSmtpHost($this->option_arr['o_smtp_host'])
							->setSmtpPort($this->option_arr['o_smtp_port'])
							->setSmtpUser($this->option_arr['o_smtp_user'])
							->setSmtpPass($this->option_arr['o_smtp_pass']);
					}

					$pjEmail
						->setContentType('text/html')
						->setTo($booking['c_email'])
						->setFrom($this->getAdminEmail(), $this->option_arr['o_email_sender'])
						->setSubject($i18n_arr[$this->getLocaleId()]['subject'])
						->send(pjAppController::getEmailBody($i18n_arr[$this->getLocaleId()]['message']));
					
					$data_history = array(
					    'booking_id' => $booking['id'],
					    'action' => $i18n_arr[$this->getLocaleId()]['subject'].' -> sent to ['.$booking['c_email'].']',
					    'user_id' => $this->getUserId()
					);
					pjBookingHistoryModel::factory()->reset()->setAttributes($data_history)->insert();
						
				}
			}
		}

		$this->jsonResponse(array('status' => 200));
	}

	public function pjActionEmailReminderBulk()
	{
		$this->setAjax(true);

		if ($this->isXHR()) {
			$bookings = pjBookingModel::factory()
				->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
				->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')								
				->whereIn('t1.id', $_POST['records'])
				->findAll()
				->getData();

			if (!empty($bookings)) {
				$i18n = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
				$locale_arr = pjLocaleModel::factory()
					->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')
					->findAll()
					->getData();
				foreach ($bookings as $booking) {
					if (!empty($booking['return_id'])) {
						$booking = pjBookingModel::factory()
							->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
							->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
							->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
							->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
							->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
							->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
							->find($booking['return_id'])
							->getData();
					}

					$lp_arr = $i18n_arr = array();
					foreach ($locale_arr as $item)
					{
						$lp_arr[$item['id']."_"] = $item['file'];
						
						$lang_subject = pjAppController::replaceTokens($booking, pjAppController::getTokens($this->option_arr, $booking, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_reminder_subject']);
						$i18n_arr[$item['id']]['subject'] = $lang_subject;

						$lang_message = pjAppController::replaceTokens($booking, pjAppController::getTokens($this->option_arr, $booking, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_reminder_message']);
						$i18n_arr[$item['id']]['message'] = $lang_message;
					}

					$pjEmail = new pjEmail();
					if ($this->option_arr['o_send_email'] == 'smtp') {
						$pjEmail
							->setTransport('smtp')
							->setSmtpHost($this->option_arr['o_smtp_host'])
							->setSmtpPort($this->option_arr['o_smtp_port'])
							->setSmtpUser($this->option_arr['o_smtp_user'])
							->setSmtpPass($this->option_arr['o_smtp_pass']);
					}
					$pjEmail
						->setContentType('text/html')
						->setTo($booking['c_email'])
						->setFrom($this->getAdminEmail(), $this->option_arr['o_email_sender'])
						->setSubject($i18n_arr[$this->getLocaleId()]['subject'])
						->send(pjAppController::getEmailBody($i18n_arr[$this->getLocaleId()]['message']));
					
					$data_history = array(
					    'booking_id' => $booking['id'],
					    'action' => $i18n_arr[$this->getLocaleId()]['subject'].' -> sent to ['.$booking['c_email'].']',
					    'user_id' => $this->getUserId()
					);
					pjBookingHistoryModel::factory()->reset()->setAttributes($data_history)->insert();
				}
			}


		}

		$this->jsonResponse(array('status' => 200));
	}

    public function pjActionSmsReturnReminder()
    {
        $this->checkLogin();

        if ($this->isAdmin() || $this->isEditor())
        {
            if (isset($_POST['sms_return_reminder']) && isset($_POST['to']) && !empty($_POST['to']) && !empty($_POST['id']))
            {
            	$locale_id = isset($_POST['locale_id']) && (int)$_POST['locale_id'] > 0 ? (int)$_POST['locale_id'] : $this->getLocaleId();
                /*$params = array(
                    'text' => $_POST['i18n'][$locale_id]['message'],
                    'type' => 'unicode',
                    'key' => md5($this->option_arr['private_key'] . PJ_SALT)
                );

                $params['number'] = $_POST['to'];
                $result = $this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));*/
                
                $result = $this->messagebirdSendSMS(array($_POST['to']), $_POST['i18n'][$locale_id]['message'], $this->option_arr);
                if ($result) {
                    $data_log = array(
                        'booking_id' => $_POST['id'],
                        'action' => $_POST['i18n'][$locale_id]['message'],
                        'user_id' => $this->getUserId()
                    );
                    pjBookingHistoryModel::factory()->setAttributes($data_log)->insert();
                    $err = 'AB11';
                } else {
                    $err = 'AB12';
                }
                
                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionUpdate&id=".$_POST['id']."&err=$err");
            } else {

                $arr = pjBookingModel::factory()
                    ->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
                    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
					->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
                    ->find($_GET['id'])
                    ->getData();

                if (count($arr) === 0)
                {
                    pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AB08");
                }

                $this->set('arr', $arr);
                
                $i18n = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = $i18n_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
					
					$lang_message = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_sms_return_reminder_message']);
					$i18n_arr[$item['id']]['message'] = $lang_message;
				}
				$this->set('i18n_arr', $i18n_arr);
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
                $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
                $this->appendJs('pjAdminBookings.js');
            }
        } else {
            $this->set('status', 2);
        }
    }

	public function pjActionCalPrice()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
		    if(count(explode(" ", $_POST['booking_date'])) == 3)
            {
                list($date, $time, $period) = explode(" ", $_POST['booking_date']);
            }else{
                list($date, $time) = explode(" ", $_POST['booking_date']);
            }
            $date = pjUtil::formatDate(@$date, $this->option_arr['o_date_format']);
            $dayIndex = $date? date('N', strtotime($date)): null;

            list($pickup_type, $pickup_id) = explode('~::~', $_POST['location_id']);
			list($dropoff_type, $dropoff_place_id, $dropoff_id) = explode('~::~', $_POST['dropoff_id']);
			$_POST['pickup_type'] = $pickup_type;
			$_POST['custom_pickup_id'] = $pickup_id;
			$_POST['dropoff_type'] = $dropoff_type;
			
			$is_airport = 0;
			if ($pickup_type == 'server' || (int)$_POST['pickup_id'] > 0) {
				if ((int)$_POST['pickup_id'] > 0) {
					$pickup_place_arr = pjLocationModel::factory()->find((int)$_POST['pickup_id'])->getData();
				} else { 
					$pickup_place_arr = pjLocationModel::factory()->find($pickup_id)->getData();
				}
				$is_airport = $pickup_place_arr['is_airport'];
				$_POST['pickup_lat'] = $pickup_place_arr['lat'];
				$_POST['pickup_lng'] = $pickup_place_arr['lng'];
				$_POST['pickup_is_airport'] = $pickup_place_arr['is_airport'];
				$_POST['pickup_address'] = '';
				if ((int)$_POST['pickup_id'] > 0) {
					$pickup_place_arr = $this->getGooglePlaceDetails($pickup_id, $this->option_arr);
					if ($pickup_place_arr['status'] == 'OK') {
						$_POST['pickup_address'] = strip_tags($pickup_place_arr['result']['adr_address']);
						$_POST['pickup_lat'] = $pickup_place_arr['result']['geometry']['location']['lat'];
						$_POST['pickup_lng'] = $pickup_place_arr['result']['geometry']['location']['lng'];
					}
				}
			} else {
				$_POST['pickup_is_airport'] = 0;
				$pickup_place_arr = $this->getGooglePlaceDetails($pickup_id, $this->option_arr);
				if ($pickup_place_arr['status'] == 'OK') {
					$_POST['pickup_address'] = strip_tags($pickup_place_arr['result']['adr_address']);
					$_POST['pickup_lat'] = $pickup_place_arr['result']['geometry']['location']['lat'];
					$_POST['pickup_lng'] = $pickup_place_arr['result']['geometry']['location']['lng'];
					if (isset($pickup_place_arr['result']['types']) && in_array('airport', $pickup_place_arr['result']['types'])) {
						$is_airport = 1;
						$_POST['pickup_is_airport'] = 1;
					}
				}
			}
			
			if ($dropoff_type == 'google') {
				$_POST['dropoff_is_airport'] = 0;
				$dropoff_place_arr = $this->getGooglePlaceDetails($dropoff_place_id, $this->option_arr);
				if ($dropoff_place_arr['status'] == 'OK') {
					$_POST['dropoff_address'] = strip_tags($dropoff_place_arr['result']['adr_address']);
					$_POST['dropoff_lat'] = $dropoff_place_arr['result']['geometry']['location']['lat'];
					$_POST['dropoff_lng'] = $dropoff_place_arr['result']['geometry']['location']['lng'];
					if (isset($dropoff_place_arr['result']['types']) && in_array('airport', $dropoff_place_arr['result']['types'])) {
						$_POST['dropoff_is_airport'] = 1;
					}
				}
			} else {
				$dropoff_place_arr = pjAreaCoordModel::factory()->find($dropoff_place_id)->getData();
				$_POST['dropoff_is_airport'] = $dropoff_place_arr ? $dropoff_place_arr['is_airport'] : 0;
				$_POST['dropoff_address'] = '';
			}

			$distance = 0;
			$distance_arr = $this->calcDistanceBetweenTwoLocations($_POST['pickup_lat'], $_POST['pickup_lng'], $_POST['dropoff_lat'], $_POST['dropoff_lng'], $this->option_arr);
			if (isset($distance_arr['rows'][0]['elements'][0]['status']) && $distance_arr['rows'][0]['elements'][0]['status'] == 'OK') {
				$distance = $distance_arr['rows'][0]['elements'][0]['distance']['value'];
				$_POST['distance'] = round($distance/1000);
				$_POST['duration'] = round($distance_arr['rows'][0]['elements'][0]['duration']['value']/60);
			}
			$_POST['distance_formated'] = $_POST['distance'].' '.strtolower(__('lblKm', true, false));
			$_POST['duration_formated'] = $_POST['duration'].' '.strtolower(__('lblMinutes', true, false));
			$price_by_distance = 'F';
			$fleet = pjFleetModel::factory()->find($_POST['fleet_id'])->getData();
			if(($_POST['pickup_type'] == 'google' && (int)$_POST['pickup_id'] <= 0) || ($_POST['dropoff_type'] == 'google' && (int)$_POST['custom_dropoff_id'] <= 0)) {
				$params = array(
					'pickup_lat' => $_POST['pickup_lat'],
					'pickup_lng' => $_POST['pickup_lng'],
					'dropoff_lat' => $_POST['dropoff_lat'],
					'dropoff_lng' => $_POST['dropoff_lng'],
					'distance' => $distance,
					'vehicle_arr' => $fleet
				);
            	$price_arr = $this->getPricesBasedOnDistance($params, $this->option_arr);
            	$one_way_price = $price_arr['rental_price'];
            	$_POST['station_fee'] = $price_arr['station_fee'];
            	$_POST['station_id'] = $price_arr['station_id'];
            	$_POST['station_distance'] = $price_arr['station_distance'];
            	$price_by_distance = 'T';
            	
            	$data_latlng = array(
            	    'dropoff_lat' => $_POST['dropoff_lat'],
            	    'dropoff_lng' => $_POST['dropoff_lng'],
            	    'pickup_lat' => $_POST['pickup_lat'],
            	    'pickup_lng' => $_POST['pickup_lng']
            	);
            	$dropoff_area = $this->check_area_to_get_price_level($data_latlng);
            	if ($dropoff_area) {
            	    $price_level = $dropoff_area['price_level'];
            	} else {
            	    $price_level = 0;
            	}
			} else {
				if ($_POST['dropoff_type'] == 'google' && (int)$_POST['custom_dropoff_id'] > 0) {
					$dropoff_id = (int)$_POST['custom_dropoff_id'];
				}
				$price_arr = pjPriceModel::factory()
				    ->select("t1.price_{$dayIndex}, t2.return_discount_{$dayIndex}, t2.return_discount_{$dayIndex}_2")
	                ->join('pjFleet', 't2.id = t1.fleet_id', 'left')
					->where('t1.dropoff_id', $dropoff_id)
					->where('t1.fleet_id', $_POST['fleet_id'])
					->findAll()
					->getDataIndex(0);
				$one_way_price = $price_arr["price_{$dayIndex}"];
				
				$drop_arr = pjDropoffModel::factory()->find((int)$dropoff_id)->getData();
				$price_level = $drop_arr ? $drop_arr['price_level'] : 1;
			}
			
			$fleet_discount_arr = $this->getFleetDiscount($date, $_POST['fleet_id'], $price_level);
			if ($fleet_discount_arr) {
				if ($fleet_discount_arr['is_subtract'] == 'T') {
					if ($fleet_discount_arr['type'] == 'amount') {
						$one_way_price = $one_way_price - $fleet_discount_arr['discount'];
					} else {
						$one_way_price = $one_way_price - (($one_way_price * $fleet_discount_arr['discount']) / 100);
					}
				} else {
				    if ($price_by_distance == 'F') { 
    					if ($fleet_discount_arr['type'] == 'amount') {
    						$one_way_price = $one_way_price + $fleet_discount_arr['discount'];
    					} else {
    						$one_way_price = $one_way_price + (($one_way_price * $fleet_discount_arr['discount']) / 100);
    					}
				    }
				}
				if ($one_way_price < 0) {
					$one_way_price = 0;
				}
			}
			
			if ($price_by_distance == 'T' && $price_level == 2) {
			    $price_level2_arr = $this->getPriceLevel2ByDistance($date, $_POST['fleet_id'], round($distance/1000));
			    $one_way_price = $one_way_price + ((float)$price_level2_arr['price'] * round($distance/1000));
			}
			
			$return_price = $one_way_price;
			$extra_price = $return_extra_price = 0;
			if (isset($_POST['extras'])) {
				$extra_ids = array();
				foreach ($_POST['extras'] as $e_id => $e_cnt) {
					if ((int)$e_cnt > 0) {
						$extra_ids[$e_id] = $e_cnt;
					}
				}
				if ($extra_ids) {
					$extra_arr = pjExtraModel::factory()->whereIn('t1.id', array_keys($extra_ids))->findAll()->getDataPair('id', NULL);
					foreach ($extra_arr as $ex) {
						if ((float)$ex['price'] > 0) {
							$extra_price += $extra_ids[$ex['id']] * (float)$ex['price'];
						}
					}
				}
			}
			
			if (isset($_POST['extras_return']) && isset($_POST['has_return'])) {
				$extra_ids = array();
				foreach ($_POST['extras_return'] as $e_id => $e_cnt) {
					if ((int)$e_cnt > 0) {
						$extra_ids[$e_id] = $e_cnt;
					}
				}
				if ($extra_ids) {
					$extra_arr = pjExtraModel::factory()->reset()->whereIn('t1.id', array_keys($extra_ids))->findAll()->getDataPair('id', NULL);
					foreach ($extra_arr as $ex) {
						if ((float)$ex['price'] > 0) {
							$return_extra_price += $extra_ids[$ex['id']] * (float)$ex['price'];
						}
					}
				}
			}
			$total_extra_price = $extra_price + $return_extra_price;
			if ($price_level == 2) {
			    $return_discount = $fleet["return_discount_{$dayIndex}_2"];
			} elseif ($price_level == 1) {
			    $return_discount = $fleet["return_discount_{$dayIndex}"];
			} else {
			    $return_discount = 0;
			}
			
			$result = pjUtil::calPrice($one_way_price, $return_price, $total_extra_price, isset($_POST['has_return']) && !empty($_POST['return_date']) ? true : false, $return_discount, $this->option_arr, $_POST['payment_method'], $_POST['voucher_code']);
			$total = $result['total'];
			$result['price'] = 0;
			$result['price_first_transfer'] = 0;
			$result['price_return_transfer'] = 0;
			$result['extra_price_first_transfe'] = $extra_price;
			$result['extra_price_return_transfe'] = $return_extra_price;
			$result['total_extra_price'] = $total_extra_price;
			if (isset($_POST['has_return']) && !empty($_POST['return_date'])) {
				$result['price_first_transfer'] = $total/2;
				$result['price_return_transfer'] = $total/2;
			} else {
				$result['price'] = $total;
			}
			$result['price_by_distance'] = $price_by_distance;
			pjAppController::jsonResponse(array_merge($_POST, $result));
			exit;
		}
	}

	public function pjActionEmailRating()
    {
        $this->checkLogin();

        if ($this->isAdmin() || $this->isEditor())
        {
            if (isset($_POST['email_rating']))
            {
                $pjEmail = new pjEmail();
                if ($this->option_arr['o_send_email'] == 'smtp')
                {
                    $pjEmail
                        ->setTransport('smtp')
                        ->setSmtpHost($this->option_arr['o_smtp_host'])
                        ->setSmtpPort($this->option_arr['o_smtp_port'])
                        ->setSmtpUser($this->option_arr['o_smtp_user'])
                        ->setSmtpPass($this->option_arr['o_smtp_pass'])
                    ;
                }
                $locale_id = isset($_POST['locale_id']) && (int)$_POST['locale_id'] > 0 ? (int)$_POST['locale_id'] : $this->getLocaleId();
                $pjEmail->setContentType('text/html');
                $pjEmail
                    ->setTo($_POST['to'])
                    ->setFrom($this->getAdminEmail(), $this->option_arr['o_email_sender'])
                    ->setSubject($_POST['i18n'][$locale_id]['subject']);
                if ($pjEmail->send(pjAppController::getEmailBody($_POST['i18n'][$locale_id]['message'])))
                {
                    $data_history = array(
                        'booking_id' => $_POST['id'],
                        'action' => $_POST['i18n'][$locale_id]['subject'].' -> sent to ['.$_POST['to'].']',
                        'user_id' => $this->getUserId()
                    );
                    pjBookingHistoryModel::factory()->setAttributes($data_history)->insert();
                    
                    $err = 'AB13';
                } else {
                    $err = 'AB14';
                }
                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=$err");
            } else {

                $arr = pjBookingModel::factory()
                    ->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
                    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
					->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
                    ->find($_GET['id'])
                    ->getData();

                if (count($arr) === 0)
                {
                    pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AB08");
                }

                $this->set('arr', $arr);
                
                $i18n = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = $i18n_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
					
					$lang_subject = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_rating_subject']);
					$i18n_arr[$item['id']]['subject'] = $lang_subject;
					
					$lang_message = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_rating_message']);
					$i18n_arr[$item['id']]['message'] = $lang_message;
				}
				$this->set('i18n_arr', $i18n_arr);
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
                $this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
                $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
                $this->appendJs('pjAdminBookings.js');
            }
        } else {
            $this->set('status', 2);
        }
    }
    
    public function pjActionUpdateBookingPrice()
    {
        $this->checkLogin();

        if ($this->isAdmin())
        {
        	$sql = 'UPDATE `'.pjBookingModel::factory()->getTable().'` SET `price` = IF(return_date != "" OR return_id > 0, total/2, total);';
        	pjBookingModel::factory()->execute($sql);
        	echo 'Data updated!';
        } else {
        	echo 'Access deined!';
        }
        exit;
    }
    
	public function pjActionSearchLocations() {
    	$this->setAjax(true);

        if ($this->isXHR())
        {
	    	$arr = $data_server = $data_google = array();
	    	if (isset($_GET['dropoff'])) {
	    		if (isset($_SESSION[$this->defaultDropoffLocations]) && count($_SESSION[$this->defaultDropoffLocations]) > 0) {
		    		$arr = $_SESSION[$this->defaultDropoffLocations];
	    		}
	    	} else {
		    	if (isset($_SESSION[$this->defaultPickupLocations]) && count($_SESSION[$this->defaultPickupLocations]) > 0) {
		    		$arr = $_SESSION[$this->defaultPickupLocations];
	    		}
	    	}
	    	if ($arr) {
		    	foreach ($arr as $v) {
		    		if (strpos(strtolower($v['text']), strtolower($_GET['term'])) !== false) {
						$data_server[] = array('id' => $v['id_formated'], 'icon' => $v['icon'], 'text' => $v['text']);
		    		}
		    	}
	    	}
	    	
	    	if (count($data_server) <= 0) {
		    	$query = http_build_query(array('input' => $_GET['term']));
		    	$pjHttp = new pjHttp();
		    	$pjHttp->setMethod('GET');
		    	$pjHttp->curlRequest('https://maps.googleapis.com/maps/api/place/autocomplete/json?'.$query.'&libraries=places&components=country:at|country:it|country:de|country:ch&key='.$this->option_arr['o_google_api_key']);
		    	//$pjHttp->curlRequest('https://maps.googleapis.com/maps/api/place/autocomplete/json?'.$query.'&libraries=places&components=country:at|country:it|country:de|country:ch|country:vn&key='.$this->option_arr['o_google_api_key']);
		    	$arr = json_decode($pjHttp->getResponse(), true);
		    	if (isset($arr['predictions']) && count($arr['predictions']) > 0) {
			    	foreach ($arr['predictions'] as $v) {
			    		$materialIcon = 'place';
			    		if (in_array('airport', $v['types'])) {
			    			$materialIcon = 'local_airport';
			    		} elseif (in_array('restaurant', $v['types'])) {
			    			$materialIcon = 'restaurant';
			    		} elseif (in_array('store', $v['types'])) {
			    			$materialIcon = 'local_mall';
			    		} elseif (in_array('train_station', $v['types'])) {
			    			$materialIcon = 'train_station';
			    		} elseif (in_array('university', $v['types']) || in_array('school', $v['types'])) {
			    			$materialIcon = 'school';
			    		}
			    		$data_google[] = array(
			    			'id' => 'google~::~'.$v['place_id'].'~::~',
			    			'icon' => $materialIcon,
			    			'text' => $v['description']
			    		);
			    	}
		    	}
	    	}
	    	pjAppController::jsonResponse(array('results' => array_merge($data_server, $data_google)));
        }
        exit;
    }
    
    public function pjActionGetLatLngDropoff() {
    	$this->setAjax(true);

        if ($this->isXHR())
        {
        	$coordinate_arr = pjAreaCoordModel::factory()->find($_GET['place_id'])->getData();
        	pjAppController::jsonResponse($coordinate_arr);
        }
        exit;
    }
    
	public function pjActionGetLocationDropoff()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			list($type, $location_id) = explode('~::~', $_POST['location_id']);			
			if (isset($_POST['pickup_id']) && (int)$_POST['pickup_id'] > 0) {
				$location_id = (int)$_POST['pickup_id'];
			}
			$dropoff_arr = pjDropoffModel::factory()
				->select("t1.*, t2.content as location")
				->join('pjMultiLang', "t2.model='pjDropoff' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where('t1.location_id', $location_id)
				->orderBy("t1.is_airport DESC, t1.order_index ASC, location ASC")
				->findAll()->getData();
			$dropoff_ids_arr = array();
			foreach ($dropoff_arr as $v) {
				$dropoff_ids_arr[] = $v['id'];
			}
			
			$lat = $lng = '';
			$dropoff_arr = array();
			if ($dropoff_ids_arr) {
				$dropoff_arr = pjDropoffAreaModel::factory()->select('t1.dropoff_id, t4.*, t5.content AS area_name, t6.content AS place_name')
					->join('pjDropoff', 't2.id=t1.dropoff_id', 'inner')
					->join('pjArea', 't3.id=t1.area_id', 'inner')
					->join('pjAreaCoord', 't4.area_id=t3.id', 'inner')
					->join('pjMultiLang', "t5.model='pjArea' AND t5.foreign_id=t1.area_id AND t5.field='name' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t6.model='pjAreaCoord' AND t6.foreign_id=t4.id AND t6.field='place_name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
					->whereIn('t1.dropoff_id', $dropoff_ids_arr)
					->where('t4.is_disabled', 0)
					->orderBy('t1.dropoff_id ASC, t3.order_index ASC, t5.content ASC')
					->findAll()
					->getData();
				list($dropoff_type, $dropoff_place_id, $dropoff_id) = explode('~::~', $_POST['dropoff_id']);
				$place_arr = $this->getGooglePlaceDetails($dropoff_place_id, $this->option_arr);
				if ($place_arr['status'] == 'OK') {
					$lat = $place_arr['result']['geometry']['location']['lat'];
	                $lng = $place_arr['result']['geometry']['location']['lng'];
				}
			}
			pjAppController::jsonResponse(array('dropoff_arr' => $dropoff_arr, 'lat' => $lat, 'lng' => $lng));
		}
	}
	
	public function pjActionGetLatLngPickup()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$place_arr = $this->getGooglePlaceDetails($_GET['place_id'], $this->option_arr);
			if ($place_arr['status'] == 'OK') {
				$lat = $place_arr['result']['geometry']['location']['lat'];
                $lng = $place_arr['result']['geometry']['location']['lng'];
                
                $pickup_arr = pjAreaCoordModel::factory()->select('t1.*, t3.id AS location_id')
					->join('pjArea', 't2.id=t1.area_id', 'inner')
					->join('pjLocation', 't3.area_id=t2.id', 'inner')
					->orderBy('t3.is_airport DESC, t3.order_index ASC')
					->findAll()
					->getData();
				pjAppController::jsonResponse(array('status' => 'OK', 'pickup_arr' => $pickup_arr, 'lat' => $lat, 'lng' => $lng));
			}
			pjAppController::jsonResponse(array('status' => 'ERR'));
		}
	}
	
	public function pjActionUpdateBookingUUID()
	{
	    $this->checkLogin();
	    
	    if ($this->isAdmin())
	    {
	        $pjBookingModel = pjBookingModel::factory();
	        $arr = $pjBookingModel->where('t1.uuid IS NULL OR t1.uuid=""')->findAll()->getData();
	        foreach ($arr as $val) {
	            $uuid = pjAppController::createRandomBookingId();
	            $pjBookingModel->reset()->set('id', $val['id'])->modify(array('uuid' => $uuid));
	        }
	        echo 'Data updated!';
	    } else {
	        echo 'Access deined!';
	    }
	    exit;
	}
	
	public function pjActionEmailPaymentLink()
	{
	    $this->checkLogin();
	    
	    if ($this->isAdmin() || $this->isEditor())
	    {
	        if (isset($_POST['o_email_payment_link_confirmation']))
	        {
	            $pjEmail = new pjEmail();
	            if ($this->option_arr['o_send_email'] == 'smtp')
	            {
	                $pjEmail
	                ->setTransport('smtp')
	                ->setSmtpHost($this->option_arr['o_smtp_host'])
	                ->setSmtpPort($this->option_arr['o_smtp_port'])
	                ->setSmtpUser($this->option_arr['o_smtp_user'])
	                ->setSmtpPass($this->option_arr['o_smtp_pass'])
	                ;
	            }
	            $locale_id = isset($_POST['locale_id']) && (int)$_POST['locale_id'] > 0 ? (int)$_POST['locale_id'] : $this->getLocaleId();
	            $pjEmail->setContentType('text/html');
	            $pjEmail
	            ->setTo($_POST['to'])
	            ->setFrom($this->getAdminEmail(), $this->option_arr['o_email_sender'])
	            ->setSubject($_POST['i18n'][$locale_id]['subject']);
	            if ($pjEmail->send(pjAppController::getEmailBody($_POST['i18n'][$locale_id]['message'])))
	            {
	                $data_history = array(
	                    'booking_id' => $_POST['id'],
	                    'action' => $_POST['i18n'][$locale_id]['subject'].' -> sent to ['.$_POST['to'].']',
	                    'user_id' => $this->getUserId()
	                );
	                pjBookingHistoryModel::factory()->setAttributes($data_history)->insert();
	                
	                $err = 'AB09';
	            } else {
	                $err = 'AB10';
	            }
	            pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=$err");
	        } else {
	            
	            $arr = pjBookingModel::factory()
	            ->select("t1.*, t2.content as fleet, t3.content as location, CONCAT_WS(' - ', t4.content, t5.content) as dropoff")
	            ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	            ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
	            ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
	            ->join('pjMultiLang', "t5.model='pjDropoff' AND t5.foreign_id=t1.dropoff_id AND t5.field='address' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
	            ->find($_GET['id'])
	            ->getData();
	            
	            if (count($arr) === 0)
	            {
	                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AB08");
	            }
	            
	            $this->set('arr', $arr);
	            
	            $i18n = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
	            $locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
	            ->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
	            ->where('t2.file IS NOT NULL')
	            ->orderBy('t1.sort ASC')->findAll()->getData();
	            
	            $lp_arr = $i18n_arr = array();
	            foreach ($locale_arr as $item)
	            {
	                $lp_arr[$item['id']."_"] = $item['file'];
	                $arr['o_site_url'] = $i18n[$item['id']]['o_site_url'];
	                
	                $lang_subject = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_payment_link_subject']);
	                $i18n_arr[$item['id']]['subject'] = $lang_subject;
	                
	                $lang_message = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['o_email_payment_link_message']);
	                $i18n_arr[$item['id']]['message'] = $lang_message;
	            }
	            $this->set('i18n_arr', $i18n_arr);
	            $this->set('lp_arr', $locale_arr);
	            $this->set('locale_str', pjAppController::jsonEncode($lp_arr));
	            
	            $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
	            $this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
	            $this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
	            $this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
	            
	            $this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
	            $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
	            $this->appendJs('pjAdminBookings.js');
	        }
	    } else {
	        $this->set('status', 2);
	    }
	}	
	
	public function pjActionDownloadFile()
	{
	    $this->checkLogin();
	    
	    if ($this->isAdmin() || $this->isEditor())
	    {
	        $arr = pjBookingModel::factory()->find($_GET['id'])->getData();
	        $data = file_get_contents($arr['name_sign']);
	        pjUtil::download($data, basename($arr['name_sign']));
	    }
	    exit;
	}
	
	public function pjActionDeleteFile()
	{
	    $this->checkLogin();
	    
	    if ($this->isAdmin() || $this->isEditor())
	    {
	        $arr = pjBookingModel::factory()->find($_GET['id'])->getData();
	        @unlink($arr['name_sign']);
	        
	        pjBookingModel::factory()->reset()->set('id', $arr['id'])->modify(array('name_sign' => ':NULL'));
	    }
	    exit;
	}
	
	public function pjActionCustomEmail()
	{
	    $this->checkLogin();
	    
	    if ($this->isAdmin() || $this->isEditor())
	    {
	        if (isset($_POST['email_custom']))
	        {
	            $pjEmail = new pjEmail();
	            if ($this->option_arr['o_send_email'] == 'smtp')
	            {
	                $pjEmail
	                ->setTransport('smtp')
	                ->setSmtpHost($this->option_arr['o_smtp_host'])
	                ->setSmtpPort($this->option_arr['o_smtp_port'])
	                ->setSmtpUser($this->option_arr['o_smtp_user'])
	                ->setSmtpPass($this->option_arr['o_smtp_pass'])
	                ;
	            }
	            $locale_id = isset($_POST['locale_id']) && (int)$_POST['locale_id'] > 0 ? (int)$_POST['locale_id'] : $this->getLocaleId();
	            $pjEmail->setContentType('text/html');
	            $pjEmail
	            ->setTo($_POST['to'])
	            ->setFrom($this->getAdminEmail(), $this->option_arr['o_email_sender'])
	            ->setSubject($_POST['i18n'][$locale_id]['subject']);
	            if ($pjEmail->send(pjAppController::getEmailBody($_POST['i18n'][$locale_id]['message'])))
	            {
	                $data_history = array(
	                    'booking_id' => $_POST['id'],
	                    'action' => $_POST['i18n'][$locale_id]['subject'].' -> sent to ['.$_POST['to'].']',
	                    'user_id' => $this->getUserId()
	                );
	                pjBookingHistoryModel::factory()->setAttributes($data_history)->insert();
	                
	                $err = 'AB19';
	            } else {
	                $err = 'AB18';
	            }
	            pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=$err");
	        } else {
	            $email_theme_arr = pjEmailThemeModel::factory()->find($_GET['id'])->getData();
	            if (!$email_theme_arr) {
	                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionUpdate&id=".$_GET['booking_id']);
	            }
	            $arr = pjBookingModel::factory()
	            ->select("t1.*, t2.content as fleet, t3.content as location, CONCAT_WS(' - ', t4.content, t5.content) as dropoff")
	            ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	            ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
	            ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
	            ->join('pjMultiLang', "t5.model='pjDropoff' AND t5.foreign_id=t1.dropoff_id AND t5.field='address' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
	            ->find($_GET['booking_id'])
	            ->getData();
	            
	            if (count($arr) === 0)
	            {
	                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AB08");
	            }
	            
	            $this->set('arr', $arr);
	            
	            $i18n = pjMultiLangModel::factory()->getMultiLang($_GET['id'], 'pjEmailTheme');
	            $locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
	            ->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
	            ->where('t2.file IS NOT NULL')
	            ->orderBy('t1.sort ASC')->findAll()->getData();
	            
	            $lp_arr = $i18n_arr = array();
	            foreach ($locale_arr as $item)
	            {
	                $lp_arr[$item['id']."_"] = $item['file'];
	                
	                $lang_subject = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['subject']);
	                $i18n_arr[$item['id']]['subject'] = $lang_subject;
	                
	                $lang_message = pjAppController::replaceTokens($arr, pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $item['id']), $i18n[$item['id']]['body']);
	                $i18n_arr[$item['id']]['message'] = $lang_message;
	            }
	            $this->set('i18n_arr', $i18n_arr);
	            $this->set('lp_arr', $locale_arr);
	            $this->set('locale_str', pjAppController::jsonEncode($lp_arr));
	            
	            $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
	            $this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
	            $this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
	            $this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
	            
	            $this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
	            $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
	            $this->appendJs('pjAdminBookings.js');
	        }
	    } else {
	        $this->set('status', 2);
	    }
	}
	
	public function pjActionGetHistory()
	{
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
	        $pjBookingHistoryModel = pjBookingHistoryModel::factory()
	        ->join('pjUser', "t2.id=t1.user_id", 'left outer');
	        
	        $pjBookingHistoryModel->where('t1.booking_id', (int)$_GET['booking_id']);
	        
	        $column = 'created';
	        $direction = 'DESC';
	        if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
	        {
	            $column = $_GET['column'];
	            $direction = strtoupper($_GET['direction']);
	        }
	        
	        $total = $pjBookingHistoryModel->findCount()->getData();
	        $rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
	        $pages = ceil($total / $rowCount);
	        $page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
	        $offset = ((int) $page - 1) * $rowCount;
	        if ($page > $pages)
	        {
	            $page = $pages;
	        }
	        
	        $data = $pjBookingHistoryModel
	        ->select("t1.*, IF (t1.user_id>0, t2.name, '".__('lblBokingHistoryByOnline', true)."') AS created_by")
	        ->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
	        foreach ($data as $k => $v) {
	            $data[$k]['created'] = date($this->option_arr['o_date_format'].', '.$this->option_arr['o_time_format'], strtotime($v['created']));
	        }
	        
	        pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
	    }
	    exit;
	}
	
	public function pjActionDeleteHistory()
	{
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
	        $response = array();
	        if (pjBookingHistoryModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
	        {
	            $response['code'] = 200;
	        } else {
	            $response['code'] = 100;
	        }
	        pjAppController::jsonResponse($response);
	    }
	    exit;
	}
	
	public function pjActionDeleteHistoryBulk()
	{
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
	        if (isset($_POST['record']) && count($_POST['record']) > 0)
	        {
	            pjBookingHistoryModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
	        }
	    }
	    exit;
	}
	
	public function pjActionUpdateLatLng()
	{
	    $this->checkLogin();
	    
	    if ($this->isAdmin())
	    {
	        $pjBookingModel = pjBookingModel::factory();
	        
	        $today = date('Y-m-d');
	        $total = $pjBookingModel
	        ->where('t1.is_run_update', 0)
	        ->where('DATE(t1.booking_date)>="'.$today.'"')
	        ->where('t1.status', 'confirmed')
	        ->findCount()->getData();
	        $rowCount = 15;
	        $pages = ceil($total / $rowCount);
	        $this->set('pages', $pages);
	        $this->set('total', $total);
	        
	        $this->appendJs('pjAdminBookings.js');
	    } else {
	        $this->set('status', 2);
	    }
	}
	
	public function pjActionProcessUpdateLatLng() {
	    $this->setAjax(true);
	    $get = $_GET;
	    $pjBookingModel = pjBookingModel::factory();
	    $rowCount = 15;
	    $page = isset($get['page']) && (int) $get['page'] > 0 ? intval($get['page']) : 1;
	    $offset = ((int) $page - 1) * $rowCount;
	    $today = date('Y-m-d');
	    $data = $pjBookingModel
	    ->where('t1.is_run_update', 0)
        ->where('DATE(t1.booking_date)>="'.$today.'"')
        ->where('t1.status', 'confirmed')
        ->limit($rowCount, $offset)->findAll()->getData();
        foreach ($data as $val) {
            $data_update = array();
            if ((int)$val['return_id'] > 0) {
                $data_update['pickup_lat'] = $val['dropoff_lat'];
                $data_update['pickup_lng'] = $val['dropoff_lng'];
                
                $data_update['dropoff_lat'] = $val['pickup_lat'];
                $data_update['dropoff_lng'] = $val['pickup_lng'];
                
                $data_update['region'] = $val['dropoff_region'];
                $data_update['dropoff_region'] = $val['region'];
            } else {
                $data_update['region'] = $val['region'];
                $data_update['dropoff_region'] = $val['dropoff_region'];
            }
            
            $data_update['is_run_update'] = 1;
            $pjBookingModel->reset()->set('id', $val['id'])->modify($data_update);
            
            $resp = pjApiSync::syncBooking($val['id'], 'update_latlng', $this->option_arr);
        }
        
        pjAppController::jsonResponse(array('next_page' => (int)$get['page'] + 1));
	}
	
	public function getActualTravelTime($params) {
	    $lat1 = $params['pickup_lat'];
	    $lon1 = $params['pickup_lng'];
	    $origin = "{$lat1},{$lon1}";
	    
	    $lat2 = $params['dropoff_lat'];
	    $lon2 = $params['dropoff_lng'];
	    $destination = "{$lat2},{$lon2}";
	    
	    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?" .
	   	    "origins=" . urlencode($origin) .
	   	    "&destinations=" . urlencode($destination) .
	   	    "&key=" . $this->option_arr['o_google_api_key'] .
	   	    "&mode=driving" .
	   	    "&departure_time=now";
	    
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	    $response = curl_exec($ch);
	    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);
	    
	    if ($http_code !== 200 || $response === false) {
	        return [
	            'duration' => 0,
	            'distance' => 0
	        ];
	    }
	    
	    $data = json_decode($response, true);
	    $element = $data['rows'][0]['elements'][0];
	    
	    if ($element['status'] !== 'OK') {
	        return [
	            'duration' => 0,
	            'distance' => 0
	        ];
	    }
	    
	    $actualDuration = round((int)$element['duration']['value']/60);
	    $actualDistance = round((int)$element['distance']['value']/100);
	    
	    return [
	        'duration' => $actualDuration,
	        'distance' => $actualDistance
	    ];
	    
	}
	
	public function pjActionUpdateFlagSynchronized()
	{
	    $this->checkLogin();
	    
	    if ($this->isAdmin())
	    {
	        $pjBookingModel = pjBookingModel::factory();
	        
	        //$pjBookingModel->where('DATE(booking_date)>="'.$today.'"')->modifyAll(array('is_run_update' => 0));
	        
	        $total = $pjBookingModel->reset()
	        ->where('t1.is_synchronized', 0)
	        ->findCount()->getData();
	        $rowCount = 15;
	        $pages = ceil($total / $rowCount);
	        $this->set('pages', $pages);
	        $this->set('total', $total);
	        
	        $this->appendJs('pjAdminBookings.js');
	    } else {
	        $this->set('status', 2);
	    }
	}
	
	public function pjActionProcessUpdateFlagSynchronized() {
	    $this->setAjax(true);
	    $get = $_GET;
	    $pjBookingModel = pjBookingModel::factory();
	    $rowCount = 15;
	    $page = isset($get['page']) && (int) $get['page'] > 0 ? intval($get['page']) : 1;
	    $offset = ((int) $page - 1) * $rowCount;
	    $today = date('Y-m-d');
	    $data = $pjBookingModel
	    ->where('t1.is_synchronized', 0)
	    ->limit($rowCount, $offset)->findAll()->getData();
	    foreach ($data as $val) {
	        $resp = pjApiSync::syncBooking($val['id'], 'update_flag_synchronized', $this->option_arr);
	        if (isset($resp['status']) && $resp['status'] == 'OK') {
	            $pjBookingModel->reset()->set('id', $val['id'])->modify(array('is_synchronized' => 1));
	        }
	    }
	    
	    pjAppController::jsonResponse(array('next_page' => (int)$get['page'] + 1));
	}
}
?>