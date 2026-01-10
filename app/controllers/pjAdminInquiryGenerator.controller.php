<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminInquiryGenerator extends pjAdmin
{
    public $defaultPickupLocations = 'pjTransferReservation_PickupLocations';
    public $defaultDropoffLocations = 'pjTransferReservation_DropoffLocations';
    
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
		    if (isset($_POST['send_inquiry']))
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
		        $body = pjAppController::getEmailBody($_POST['i18n'][$locale_id]['message']);
		        if ($pjEmail->send($body))
		        {
		            $err = 'AB19';
		        } else {
		            $err = 'AB18';
		        }
		        pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminInquiryGenerator&action=pjActionIndex&err=$err");
		    } else {
			    $this->set('inquiry_template_arr', pjEmailThemeModel::factory()
			        ->join('pjMultiLang', "t2.model='pjEmailTheme' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			        ->select("t1.*, t2.content as name")
			        ->where('t1.status', 'T')
			        ->where('t1.type', 'inquiry')
			        ->orderBy("name ASC")
			        ->findAll()->getData());
			    
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
			    
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/js/');
				$this->appendCss('select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/css/');
				$this->appendJs('jquery-ui-sliderAccess.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery-ui-timepicker-addon.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendCss('jquery-ui-timepicker-addon.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('https://maps.googleapis.com/maps/api/js?key='.$this->option_arr['o_google_api_key'].'&sensor=false&libraries=places,geometry,drawing', NULL, true);
				
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				$this->appendJs('pjAdminInquiryGenerator.js');
			}
		} else {
			$this->set('status', 2);
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
	        if($fleet['price_per'] == 'distance' || (($_POST['pickup_type'] == 'google' && (int)$_POST['pickup_id'] <= 0) || ($_POST['dropoff_type'] == 'google' && (int)$_POST['custom_dropoff_id'] <= 0))) {
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
	                if ($fleet_discount_arr['type'] == 'amount') {
	                    $one_way_price = $one_way_price + $fleet_discount_arr['discount'];
	                } else {
	                    $one_way_price = $one_way_price + (($one_way_price * $fleet_discount_arr['discount']) / 100);
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
	        
	        $result = pjUtil::calPrice($one_way_price, $return_price, $total_extra_price, isset($_POST['has_return']) && !empty($_POST['return_date']) ? true : false, $return_discount, $this->option_arr, '', '');
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
	
	public function pjActionGenerateInquiry() {
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
    	    $i18n = pjMultiLangModel::factory()->getMultiLang((int)$_POST['inquiry_template'], 'pjEmailTheme');
    	    $locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
    	    ->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
    	    ->where('t2.file IS NOT NULL')
    	    ->orderBy('t1.sort ASC')->findAll()->getData();
    	 
    	    list($pickup_type, $pickup_id) = explode('~::~', $_POST['location_id']);
    	    list($dropoff_type, $dropoff_place_id, $dropoff_id) = explode('~::~', $_POST['dropoff_id']);
    	    if ($_POST['dropoff_type'] == 'google' && (int)$_POST['custom_dropoff_id'] > 0) {
    	        $dropoff_id = (int)$_POST['custom_dropoff_id'];
    	    }
    	    if ($pickup_type == 'server') {
    	        $pickup_i18n = pjMultiLangModel::factory()->getMultiLang($_POST['location_id'], 'pjLocation');
    	    }
    	    if ($dropoff_type == 'server') {
    	        $area_coord_arr = pjAreaCoordModel::factory()->find($_POST['dropoff_place_id'])->getData();
    	        $area_coord_i18n = pjMultiLangModel::factory()->getMultiLang($_POST['location_id'], 'pjAreaCoord');
    	        $area_i18n = pjMultiLangModel::factory()->getMultiLang($area_coord_arr['area_id'], 'pjArea');
    	    }
    	    
    	    $fleet_i18n = pjMultiLangModel::factory()->getMultiLang($_POST['fleet_id'], 'pjFleet');
    	    
    	    $lp_arr = $i18n_arr = array();
    	    foreach ($locale_arr as $item)
    	    {
    	        $lp_arr[$item['id']."_"] = $item['file'];
    	        $_POST['fleet'] = $fleet_i18n[$item['id']]['fleet'];
    	        if ($pickup_type == 'server') {
    	            $_POST['location'] = $pickup_i18n[$item['id']]['pickup_location'];
    	        } else {
    	            $_POST['location'] = $_POST['pickup_address'];
    	        }
    	        if ($dropoff_type == 'server') {
    	            $dropoff_arr = array();
    	            if (!empty($dropoff_i18n[$item['id']]['name'])) {
    	                $dropoff_arr[] = $dropoff_i18n[$item['id']]['name'];
    	            }
    	            if (!empty($area_coord_i18n[$item['id']]['place_name'])) {
    	                $dropoff_arr[] = $area_coord_i18n[$item['id']]['place_name'];
    	            }
    	            $_POST['dropoff'] = implode(' - ', $dropoff_arr);
    	        } else {
    	            $_POST['dropoff'] = $_POST['dropoff_address'];
    	        }
    	        
    	        $lang_subject = pjAppController::replaceTokens($_POST, pjAppController::getInquiryTokens($this->option_arr, $_POST, $item['id']), $i18n[$item['id']]['subject']);
    	        $i18n_arr[$item['id']]['subject'] = $lang_subject;
    	        
    	        $lang_message = pjAppController::replaceTokens($_POST, pjAppController::getInquiryTokens($this->option_arr, $_POST, $item['id']), $i18n[$item['id']]['body']);
    	        $i18n_arr[$item['id']]['message'] = $lang_message;
    	    }
    	    $this->set('i18n_arr', $i18n_arr);
    	    $this->set('lp_arr', $locale_arr);
	    }
	}
}
?>