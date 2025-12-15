<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFront extends pjAppController
{	
	public $defaultCaptcha = 'pjTransferReservation_Captcha';
	
	public $defaultLocale = 'pjTransferReservation_LocaleId';
	
	public $defaultIndex = 'pjTransferReservation_Index';
	
	public $defaultStore = 'pjTransferReservation_Store';
	
	public $defaultForm = 'pjTransferReservation_Form';
	
	public $defaultClient = 'pjTransferReservation_Client';

	public $defaultVoucher = 'pjTransferReservation_Voucher';
	
	public $defaultPickupLocations = 'pjTransferReservation_PickupLocations';
	public $defaultDropoffLocations = 'pjTransferReservation_DropoffLocations';
	
	public $defaultPaySafePaymentMethod = 'iframe';

	public function __construct()
	{
		$this->setLayout('pjActionFront');
		self::allowCORS();
	}
	
	public function isXHR()
	{
		return parent::isXHR() || isset($_SERVER['HTTP_ORIGIN']);
	}
	
	static protected function allowCORS()
	{
	    $install_url = parse_url(PJ_INSTALL_URL);
	    if($install_url['scheme'] == 'https'){
	        header('Set-Cookie: '.session_name().'='.session_id().'; SameSite=None; Secure');
	    }
	    if (!isset($_SERVER['HTTP_ORIGIN']))
	    {
	        return;
	    }
	    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
	    header("Access-Control-Allow-Credentials: true");
	    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
	    header("Access-Control-Allow-Headers: Origin, X-Requested-With");
	    header('P3P: CP="ALL DSP COR CUR ADM TAI OUR IND COM NAV INT"');
	    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
	    {
	        exit;
	    }
	    
		/* $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
		header('P3P: CP="ALL DSP COR CUR ADM TAI OUR IND COM NAV INT"');
		header("Access-Control-Allow-Origin: $origin");
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With"); */
	}
	
	private function _get($key)
	{
		if ($this->_is($key))
		{
			return $_SESSION[$this->defaultStore][$this->defaultIndex][$key];
		}
		return false;
	}
	
	private function _is($key)
	{
		return isset($_SESSION[$this->defaultStore][$this->defaultIndex]) && isset($_SESSION[$this->defaultStore][$this->defaultIndex][$key]);
	}
	
	private function _set($key, $value)
	{
		$_SESSION[$this->defaultStore][$this->defaultIndex][$key] = $value;
		return $this;
	}

	private function _unset($key)
	{
		$_SESSION[$this->defaultStore][$this->defaultIndex][$key] = null;
		unset($_SESSION[$this->defaultStore][$this->defaultIndex][$key]);
	}
	
	private function checkDate($post, $option_arr)
	{
		$valid = true;
		if(isset($post['return_on']))
		{
			$start_ts = $end_ts = 0;
			$time_ampm = false;
			if(strpos($option_arr['o_time_format'], 'a') > -1 || strpos($option_arr['o_time_format'], 'A') > -1)
			{
				$time_ampm = true;
			}
			$_date = pjUtil::formatDate($post['date'], $option_arr['o_date_format']);
			if($time_ampm == false)
			{
				$_time = $post['hour'] . ':' . $post['minute'] . ':00';
			}else{
				$_time = date("H:i:s", strtotime($post['hour'] . ':' . $post['minute'] . ' ' . strtoupper($post['ampm'])));
			}
			$start_ts = strtotime($_date . ' ' . $_time);

			$_return_date = pjUtil::formatDate($post['return_date'], $option_arr['o_date_format']);
			if($time_ampm == false)
			{
				$_return_time = $post['return_hour'] . ':' . $post['return_minute'] . ':00';
			}else{
				$_return_time = date("H:i:s", strtotime($post['return_hour'] . ':' . $post['return_minute'] . ' ' . strtoupper($post['return_ampm'])));
			}
			$end_ts = strtotime($_return_date . ' ' . $_return_time);
			
			if($end_ts < $start_ts)
			{
				$valid = false;
			}
		}
		return $valid;
	}

	private function checkDateNew($post, $option_arr)
	{
		$valid = true;
		if(isset($post['return_on']))
		{
			$start_ts = $end_ts = 0;
			$time_ampm = false;
			if(strpos($option_arr['o_time_format'], 'a') > -1 || strpos($option_arr['o_time_format'], 'A') > -1)
			{
				$time_ampm = true;
			}
			$_date = pjUtil::formatDate($post['search_date'], $option_arr['o_date_format']);
			if($time_ampm == false)
			{
				$_time = $post['hour'] . ':' . $post['minute'] . ':00';
			}else{
				$_time = date("H:i:s", strtotime($post['hour'] . ':' . $post['minute'] . ' ' . strtoupper($post['ampm'])));
			}
			$start_ts = strtotime($_date . ' ' . $_time);

			$_return_date = pjUtil::formatDate($post['return_date'], $option_arr['o_date_format']);
			if($time_ampm == false)
			{
				$_return_time = $post['return_hour'] . ':' . $post['return_minute'] . ':00';
			}else{
				$_return_time = date("H:i:s", strtotime($post['return_hour'] . ':' . $post['return_minute'] . ' ' . strtoupper($post['return_ampm'])));
			}
			$end_ts = strtotime($_return_date . ' ' . $_return_time);

			if($end_ts < $start_ts)
			{
				$valid = false;
			}
		}
		return $valid;
	}

	public function afterFilter()
	{		
	    $_GET['hide'] = 1;
		if (!isset($_GET['hide']) || (isset($_GET['hide']) && (int) $_GET['hide'] !== 1) &&
			in_array($_GET['action'], array('pjActionSearch', 'pjActionSearchNew', 'pjActionServices', 'pjActionExtras', 'pjActionDeparture', 'pjActionReturn', 'pjActionPassenger', 'pjActionCheckout', 'pjActionSummary', 'pjActionPayment')))
		{
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file, t2.title')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
			
			$this->set('locale_arr', $locale_arr);
		}
	}
	
	public function beforeFilter()
	{
		$OptionModel = pjOptionModel::factory();
		$this->option_arr = $OptionModel->getPairs($this->getForeignId());
		$this->option_arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjOption');
		$this->set('option_arr', $this->option_arr);
		$this->setTime();
        if($_GET['action'] == 'pjActionLoad' || $_GET['action'] == 'pjActionLoadNew')
        {
            $this->defaultIndex = mt_rand(1, 9999);
        }
        elseif($_GET['action'] != 'pjActionLoadCss' && $_GET['action'] != 'pjActionLoadCssNew')
        {
            $this->defaultIndex = @$_REQUEST['index'];
        }

        $is_forced = false;
        if ($_GET['controller'] == 'pjFront' && $_GET['action'] == 'pjActionCancel') {
            $booking_arr = pjBookingModel::factory()->find($_GET['id'])->getData();
            if ($booking_arr && (int)$booking_arr['locale_id'] > 0) {
                $this->setLocaleId((int)$booking_arr['locale_id']);
                $is_forced = true;
            }
        }
        
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
	
	public function beforeRender()
	{
		if (isset($_GET['iframe']))
		{
			$this->setLayout('pjActionIframe');
		}
	}
	
	public function pjActionCaptcha()
	{
		$this->setAjax(true);
		$Captcha = new pjCaptcha('app/web/obj/Anorexia.ttf', $this->defaultCaptcha, 6);
		$Captcha->setImage('app/web/img/button.png')->init(isset($_GET['rand']) ? $_GET['rand'] : null);
	}

	public function pjActionCheckCaptcha()
	{
		$this->setAjax(true);
		if (!isset($_GET['captcha']) || empty($_GET['captcha']) || strtoupper($_GET['captcha']) != $_SESSION[$this->defaultCaptcha]){
			echo 'false';
		}else{
			echo 'true';
		}
	}
	public function isFrontLogged()
	{
		if (isset($_SESSION[$this->defaultClient]) && count($_SESSION[$this->defaultClient]) > 0)
		{
			return true;
		}
		return false;
	}
	public function pjActionLocale()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['locale_id']))
			{
				$this->pjActionSetLocale($_GET['locale_id']);
				
				pjAppController::setFields($this->getLocaleId());
				
				$day_names = __('day_names', true);
				ksort($day_names, SORT_NUMERIC);
				
				$months = __('months', true);
				ksort($months, SORT_NUMERIC);
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Locale have been changed.', 'opts' => array(
					'day_names' => array_values($day_names),
					'month_names' => array_values($months)
				)));
			}
		}
		exit;
	}
	private function pjActionSetLocale($locale)
	{
		if ((int) $locale > 0)
		{
			$_SESSION[$this->defaultLocale] = (int) $locale;
			$this->loadSetFields(true);
		}
		return $this;
	}
	
	public function pjActionGetLocale()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : FALSE;
	}
	
	public function pjActionLoadCss()
	{
        $isRtl = pjLocaleModel::factory()->where('id', $this->getLocaleId())->where('dir', 'rtl')->limit(1)->findCount()->getData() == 1;
        $theme = isset($_GET['theme']) && in_array($_GET['theme'], array('beige', 'dblue', 'dgreen', 'grey', 'lblue', 'lgreen', 'lime', 'orange', 'peach', 'pink', 'purple', 'red', 'teal', 'turquoise', 'yellow'))? $_GET['theme']: 'pink';

        $baseDir = defined("PJ_INSTALL_PATH") ? PJ_INSTALL_PATH : null;
        $dm = new pjDependencyManager($baseDir, PJ_THIRD_PARTY_PATH);		
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		$arr = array(
			array('file' => 'bootstrap-datetimepicker.min.css', 'path' => $dm->getPath('pj_bootstrap_datetimepicker')),
			array('file' => "theme-$theme.css", 'path' => PJ_CSS_PATH),
            array('file' => 'select2.min.css', 'path' => $dm->getPath('pj_select2')),
            array('file' => 'tooltipster.bundle.min.css', 'path' => PJ_LIBS_PATH.'pjQ/tooltipster/css/'),
            //array('file' => 'pjTransferResNewLayout.css', 'path' => PJ_CSS_PATH),
		    array('file' => 'https://fonts.googleapis.com/css?family=Raleway:400,500,600,700|Montserrat:400,700', 'path' => '')
		);
		if ($_SERVER['HTTP_HOST'] == 'localhost' || isset($_GET['original'])) {
		    $arr[] = array('file' => 'pjTransferResNewLayout_local.css', 'path' => PJ_CSS_PATH);
		} else {
		    $arr[] = array('file' => 'pjTransferResNewLayout.css', 'path' => PJ_CSS_PATH);
		}
		header("Content-Type: text/css; charset=utf-8");
		foreach ($arr as $item)
		{
			$string = FALSE;
			if ($stream = fopen($item['path'] . $item['file'], 'rb'))
			{
				$string = stream_get_contents($stream);
				fclose($stream);
			}
			
			if ($string !== FALSE)
			{
                echo str_replace(
					array('[WEB_URL]', '../fonts/glyphicons', 'pjWrapper'),
					array(
						PJ_INSTALL_URL . 'app/web/',
						PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/fonts/glyphicons',
						'pjWrapperShuttleBooking'
					), $string
				) . "\n";
			}
		}
		exit;
	}

	public function pjActionLoadCssNew()
	{
		$isRtl = pjLocaleModel::factory()->where('id', $this->getLocaleId())->where('dir', 'rtl')->limit(1)->findCount()->getData() == 1;
        $theme = isset($_GET['theme']) && in_array($_GET['theme'], array('beige', 'dblue', 'dgreen', 'grey', 'lblue', 'lgreen', 'lime', 'orange', 'peach', 'pink', 'purple', 'red', 'teal', 'turquoise', 'yellow'))? $_GET['theme']: 'pink';

        $baseDir = defined("PJ_INSTALL_PATH") ? PJ_INSTALL_PATH : null;
        $dm = new pjDependencyManager($baseDir, PJ_THIRD_PARTY_PATH);		
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		$arr = array(
			array('file' => 'bootstrap-datetimepicker.min.css', 'path' => $dm->getPath('pj_bootstrap_datetimepicker')),
			array('file' => "theme-$theme.css", 'path' => PJ_CSS_PATH),
            array('file' => 'select2.min.css', 'path' => $dm->getPath('pj_select2')),
            array('file' => 'pjTransferResNewLayout.css', 'path' => PJ_CSS_PATH),
			array('file' => 'https://fonts.googleapis.com/css?family=Raleway:400,500,600,700|Montserrat:400,700')
		);
		header("Content-Type: text/css; charset=utf-8");
		foreach ($arr as $item)
		{
			$string = FALSE;
			if ($stream = fopen($item['path'] . $item['file'], 'rb'))
			{
				$string = stream_get_contents($stream);
				fclose($stream);
			}
			
			if ($string !== FALSE)
			{
                echo str_replace(
					array('[WEB_URL]', '../fonts/glyphicons', 'pjWrapper'),
					array(
						PJ_INSTALL_URL . 'app/web/',
						PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/fonts/glyphicons',
						'pjWrapperShuttleBookingSearch'
					), $string
				) . "\n";
			}
		}
		exit;
	}
	
	public function pjActionLoad()
	{
		ob_start();
		header("Content-Type: text/javascript; charset=utf-8");
		if (isset($_GET['locale']))
		{
			$this->pjActionSetLocale($_GET['locale']);
		}

        if(!$this->_is('search') && isset($_GET['location']) && !empty($_GET['location']))
        {
        	list($pickup_type, $pickup_id) = explode('~::~', $_GET['location']);
        	if ($pickup_type == 'server') {
	            $isPickupAvailable = pjLocationModel::factory()
	                ->where('t1.id', $pickup_id)
	                ->where('t1.status', 'T')
	                ->where("t1.id IN (SELECT TD.location_id FROM `".pjDropoffModel::factory()->getTable()."` TD INNER JOIN `".pjPriceModel::factory()->getTable()."` TP ON TP.dropoff_id=TD.id)")
	                ->findCount()->getData();
        	} else {
        		$isPickupAvailable = true;
        	}
            if($isPickupAvailable)
            {
                $search = array('location_id' => $_GET['location']);
                if ($pickup_type == 'google') {
	            	$pickup_place_arr = $this->getGooglePlaceDetails($pickup_id, $this->option_arr);
					if ($pickup_place_arr['status'] == 'OK') {
						$search['custom_pickup_address'] = strip_tags($pickup_place_arr['result']['adr_address']);
					}
                }
	            if (isset($_GET['load_prices']) && (int)$_GET['load_prices'] == 1) {
	        		$search['date'] = date($this->option_arr['o_date_format']);
	        	}
                if(isset($_GET['dropoff']) && !empty($_GET['dropoff']))
                {
                	list($dropoff_type, $dropoff_place_id, $dropoff_id) = explode('~::~', $_GET['dropoff']);
                	if ($dropoff_type == 'server') {
	                    $isDropoffAvailable = pjDropoffModel::factory()
	                            ->where('t1.location_id', $pickup_id)
	                            ->where('t1.id', $dropoff_id)
	                            ->where("t1.id IN (SELECT TP.dropoff_id FROM `".pjPriceModel::factory()->getTable()."` TP)")
	                            ->findCount()->getData();
                	} else {
                		$isDropoffAvailable = true;
                	}
                    if($isDropoffAvailable)
                    {
                    	if ($dropoff_type == 'google') {
			            	$dropoff_place_arr = $this->getGooglePlaceDetails($dropoff_place_id, $this->option_arr);
							if ($dropoff_place_arr['status'] == 'OK') {
								$search['custom_dropoff_address'] = strip_tags($dropoff_place_arr['result']['adr_address']);
							}
		                }
		                
                    	$pjFleetModel = pjFleetModel::factory();
                        $passenger_range_arr = $pjFleetModel
                            ->select('DISTINCT CONCAT_WS("-", min_passengers, passengers) as from_to')
                            ->where('status', 'T')
                            //->orderBy('min_passengers ASC, passengers ASC')
							->orderBy('from_to ASC')
                            ->limit(1)
                            ->findAll()
                            ->getDataIndex(0);
                        if(!empty($passenger_range_arr))
                        {
                            $search['dropoff_id'] = $_GET['dropoff'];
                            #$search['date'] = date($this->option_arr['o_date_format'], strtotime('tomorrow'));
                            $search['passengers_from_to'] = $passenger_range_arr['from_to'];
                            $search['autoload_next_step'] = 1;
                        }
                    }
                }
                $this->_set('search', $search);
            }
        }

		if (!empty($_GET['skip_first_step'])) {
			$skip_first_step = array(
				'location_id' => @$_GET['search_location_id'],
				'custom_pickup_id' => @$_GET['search_pickup_id'],
				'dropoff_id' => @$_GET['search_dropoff_id'],
				'passengers_from_to' => @$_GET['search_passengers_from_to'],
				'date' => @$_GET['search_date'],
				'is_return' => @$_GET['search_is_return'],
				'return_date' => @$_GET['search_return_date'],
				'autoload_next_step' => 1
			);
			if (!empty($_GET['search_location_id'])) {
				list($pickup_type, $pickup_id) = explode('~::~', $_GET['search_location_id']);
				if ($pickup_type == 'google') {
	            	$pickup_place_arr = $this->getGooglePlaceDetails($pickup_id, $this->option_arr);
					if ($pickup_place_arr['status'] == 'OK') {
						$skip_first_step['custom_pickup_address'] = strip_tags($pickup_place_arr['result']['adr_address']);
					}
                }
			}
			if (!empty($_GET['search_dropoff_id'])) {
				list($dropoff_type, $dropoff_place_id, $dropoff_id) = explode('~::~', $_GET['search_dropoff_id']);
				if ($dropoff_type == 'google') {
	            	$dropoff_place_arr = $this->getGooglePlaceDetails($dropoff_place_id, $this->option_arr);
					if ($dropoff_place_arr['status'] == 'OK') {
						$skip_first_step['custom_dropoff_address'] = strip_tags($dropoff_place_arr['result']['adr_address']);
					}
                }
			}
			$this->_set('skip_first_step', $skip_first_step);
		}
		else {
			$this->_unset('skip_first_step');
		}

		$this->option_arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjOption');
		$this->set('option_arr', $this->option_arr);
	}

	public function pjActionLoadNew()
	{
		ob_start();
		header("Content-Type: text/javascript; charset=utf-8");
		if (isset($_GET['locale']))
		{
			$this->pjActionSetLocale($_GET['locale']);
		}

		$options = pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjOption');
		$siteUrl = '';
		foreach ($options as $locale => $option) {
			foreach ($option as $key => $value) {
				if ($locale == $this->getLocaleId() && $key == 'o_site_url') {
					$siteUrl = $value;
					break 2;
				}
			}
		}
		$this->set('siteUrl', $siteUrl);

		if(!$this->_is('search') && isset($_GET['location']) && !empty($_GET['location']))
		{
			list($pickup_type, $pickup_id) = explode('~::~', $_GET['location']);
			if ($pickup_type == 'server') {
				$isPickupAvailable = pjLocationModel::factory()
					->where('t1.id', $pickup_id)
					->where('t1.status', 'T')
					->where("t1.id IN (SELECT TD.location_id FROM `".pjDropoffModel::factory()->getTable()."` TD INNER JOIN `".pjPriceModel::factory()->getTable()."` TP ON TP.dropoff_id=TD.id)")
					->findCount()->getData();
			} else {
				$isPickupAvailable = true;
			}
			if($isPickupAvailable)
			{
				$search = array('location_id' => $_GET['location']);
				if ($pickup_type == 'google') {
	            	$pickup_place_arr = $this->getGooglePlaceDetails($pickup_id, $this->option_arr);
					if ($pickup_place_arr['status'] == 'OK') {
						$search['custom_pickup_address'] = strip_tags($pickup_place_arr['result']['adr_address']);
					}
                }
				if (isset($_GET['load_prices']) && (int)$_GET['load_prices'] == 1) {
					$search['date'] = date($this->option_arr['o_date_format']);
				}
				if(isset($_GET['dropoff']) && !empty($_GET['dropoff']))
				{
					list($dropoff_type, $dropoff_place_id, $dropoff_id) = explode('~::~', $_GET['dropoff']);
					if ($dropoff_type == 'server') {
						$isDropoffAvailable = pjDropoffModel::factory()
							->where('t1.location_id', $pickup_id)
							->where('t1.id', $dropoff_id)
							->where("t1.id IN (SELECT TP.dropoff_id FROM `".pjPriceModel::factory()->getTable()."` TP)")
							->findCount()->getData();
					} else {
						$isDropoffAvailable = true;
					}
					if($isDropoffAvailable)
					{
						if ($dropoff_type == 'google') {
			            	$dropoff_place_arr = $this->getGooglePlaceDetails($dropoff_place_id, $this->option_arr);
							if ($dropoff_place_arr['status'] == 'OK') {
								$search['custom_dropoff_address'] = strip_tags($dropoff_place_arr['result']['adr_address']);
							}
		                }
		                
						$passenger_range_arr = pjFleetModel::factory()
							->select('DISTINCT CONCAT_WS("-", min_passengers, passengers) as from_to')
							->where('status', 'T')
							//->orderBy('min_passengers ASC, passengers ASC')
							->orderBy('from_to ASC')
							->limit(1)
							->findAll()
							->getDataIndex(0);
						if(!empty($passenger_range_arr))
						{
							$search['dropoff_id'] = $_GET['dropoff'];
							#$search['date'] = date($this->option_arr['o_date_format'], strtotime('tomorrow'));
							$search['passengers_from_to'] = $passenger_range_arr['from_to'];
							$search['autoload_next_step'] = 1;
						}
					}
				}
				$this->_set('search', $search);
			}
		}
	}

    public function updateCart()
    {
        $cart = $extras = $extras_return = array();
        $search_post = $this->_get('search');
        $FORM = @$_SESSION[$this->defaultForm][$this->defaultIndex];

        $cart['passengers'] = isset($FORM['departure']['passengers'])? $FORM['departure']['passengers']: 0;
        $cart['passengers_return'] = isset($FORM['return']['passengers_return'])? $FORM['return']['passengers_return']: 0;
        $cart['date'] = $search_post['date'];
        $cart['time'] = '';
        if(isset($FORM['departure']['time_h'], $FORM['departure']['time_m']))
        {
            $cart['time'] = str_pad($FORM['departure']['time_h'], 2, 0, STR_PAD_LEFT) . ':' . str_pad($FORM['departure']['time_m'], 2, 0, STR_PAD_LEFT);
        } elseif (isset($FORM['departure']['arrival_time'])) {
        	$cart['time'] = $FORM['departure']['arrival_time'];
        } elseif (isset($FORM['departure']['pickup_time'])) {
        	$cart['time'] = $FORM['departure']['pickup_time'];
        } elseif (isset($FORM['departure']['c_departure_flight_time'])) {
        	$cart['time'] = $FORM['departure']['c_departure_flight_time'];
        }
        $cart['is_return'] = (int) $this->_get('is_return');
        if($cart['is_return'])
        {
            $cart['return_date'] = isset($FORM['return']['return_date']) && !empty($FORM['return']['return_date'])? $FORM['return']['return_date'] : null;
            if(isset($FORM['return']['return_pickup_time']))
            {
                $cart['return_time'] = $FORM['return']['return_pickup_time'];
            } elseif(isset($FORM['return']['return_time']))
            {
                $cart['return_time'] = $FORM['return']['return_time'];
            }
        }

        if($search_post['pickup_type'] == 'server')
        {
        	$pickup_arr = pjLocationModel::factory()->select('t1.*, t2.content AS pickup_location')
                ->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->find((int)$search_post['pickup_id'])->getData();
            $cart['pickup_address'] = $pickup_arr['address'];
            $cart['pickup_location_name'] = $pickup_arr['pickup_location'];
        } else {
        	$cart['pickup_location_name'] = strip_tags($search_post['custom_pickup_data']['adr_address']);
        }

        if($search_post['dropoff_type'] == 'server')
        {
        	$dropoff_place_arr = pjAreaCoordModel::factory()->select('t1.*, t2.content AS dropoff_place_name, t3.content AS dropoff_area_name')
                ->join('pjMultiLang', "t2.model='pjAreaCoord' AND t2.foreign_id=t1.id AND t2.field='place_name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjMultiLang', "t3.model='pjArea' AND t3.foreign_id=t1.area_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                ->find((int)$search_post['dropoff_place_id'])->getData();
            $cart['dropoff_location_name'] = $dropoff_place_arr['dropoff_place_name'];
        } else {
        	$cart['dropoff_location_name'] = strip_tags($search_post['custom_dropoff_data']['adr_address']);
        }
		$total_extra_price = 0;
        $dropoff_id = ($search_post['dropoff_type'] == 'google' && (int)$search_post['custom_dropoff_id'] > 0) ? (int)$search_post['custom_dropoff_id'] : (int)$search_post['dropoff_id'];
        if($this->_get('fleet_id'))
        {
        	$pjFleetModel = pjFleetModel::factory();
            $dayIndex = date('N', strtotime(pjUtil::formatDate($cart['date'], $this->option_arr['o_date_format'])));
            $fleet = $pjFleetModel
                ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjPrice', "t1.id=t3.fleet_id AND t3.dropoff_id = " . $dropoff_id, 'left outer')
                ->select("t1.*, t2.content as fleet, t3.price_{$dayIndex} as price")
                ->where('t1.status', 'T')
                ->find($this->_get('fleet_id'))
                ->getData();
            $cart['fleet'] = $fleet['fleet'];            
            if(($search_post['pickup_type'] == 'google' && (int)$search_post['custom_pickup_id'] <= 0) || ($search_post['dropoff_type'] == 'google' && (int)$search_post['custom_dropoff_id'] <= 0)) {
            	$params = array(
					'pickup_lat' => $search_post['pickup_lat'],
					'pickup_lng' => $search_post['pickup_lng'],
					'dropoff_lat' => $search_post['dropoff_lat'],
					'dropoff_lng' => $search_post['dropoff_lng'],
					'distance' => $search_post['distance'],
					'vehicle_arr' => $fleet
				);
            	$price_arr = $this->getPricesBasedOnDistance($params, $this->option_arr);
            	$one_way_price = $price_arr['rental_price'];
            	
            	$data_latlng = array(
            	    'dropoff_lat' => $search_post['dropoff_lat'],
            	    'dropoff_lng' => $search_post['dropoff_lng'],
            	    'pickup_lat' => $search_post['pickup_lat'],
            	    'pickup_lng' => $search_post['pickup_lng']
            	);
            	$dropoff_area = $this->check_area_to_get_price_level($data_latlng);
            	if ($dropoff_area) {
            	    $price_level = $dropoff_area['price_level'];
            	} else {
            	    $price_level = 0;
            	}
            	$price_by_distance = 1;
            } else {
            	$one_way_price = $fleet['price'];
            	$drop_arr = pjDropoffModel::factory()->find((int)$dropoff_id)->getData();
            	$price_level = $drop_arr ? $drop_arr['price_level'] : 1;
            	$price_by_distance = 0;
            }
            
            if (!empty($cart['date'])) {
           		$date = pjUtil::formatDate($cart['date'], $this->option_arr['o_date_format']);
           		$fleet_discount_arr = $this->getFleetDiscount($date, $this->_get('fleet_id'), $price_level);
				if ($fleet_discount_arr) {
					if ($fleet_discount_arr['is_subtract'] == 'T') {
						if ($fleet_discount_arr['type'] == 'amount') {
							$one_way_price = $one_way_price - $fleet_discount_arr['discount'];
						} else {
							$one_way_price = $one_way_price - (($one_way_price * $fleet_discount_arr['discount']) / 100);
						}
					} else {
					    if ($price_by_distance == 0) {
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
            }
            
            if ($price_by_distance == 1 && $price_level == 2) {
                $distance = round($search_post['distance']/1000);
                $price_level2_arr = $this->getPriceLevel2ByDistance($date, $this->_get('fleet_id'), $distance);
                $one_way_price = $one_way_price + ((float)$price_level2_arr['price'] * $distance);
            }
            
            $one_way_price = round($one_way_price);
            $return_price = $one_way_price;
            $cart['one_way_price'] = $one_way_price;
            $cart['return_price'] = $return_price;
            $extra_price = $return_extra_price = 0;
	        if($this->_get('extras'))
	        {
	            $_extras = $this->_get('extras');
	            if(!empty($_extras))
	            {
	            	$extra_arr = pjExtraModel::factory()
	                    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	                    ->select("t1.*, t2.content as name")
	                    ->whereIn('t1.id', array_keys($_extras))
	                    ->orderBy("t1.id ASC")
	                    ->findAll()
	                    ->getData();
	                foreach($extra_arr as $ex)
	                {
	                	if (!empty($ex['image_path'])) {
	                		$extras[] = '<img src="'.PJ_INSTALL_URL . $ex['image_path'].'" class="img-responsive" /> '.$_extras[$ex['id']] . ' x ' . $ex['name'];
	                	} else {
	                    	$extras[] = $_extras[$ex['id']] . ' x ' . $ex['name'];
	                	}
	                    if ((float)$ex['price'] > 0) {
	                    	$extra_price += $_extras[$ex['id']] * $ex['price'];
	                    }
	                }
	            }
	        }
	        
        	if($this->_get('extras_return') && $cart['is_return'] == 1)
	        {
	            $_extras = $this->_get('extras_return');
	            if(!empty($_extras))
	            {
	            	$extra_arr = pjExtraModel::factory()->reset()
	                    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	                    ->select("t1.*, t2.content as name")
	                    ->whereIn('t1.id', array_keys($_extras))
	                    ->orderBy("t1.id ASC")
	                    ->findAll()
	                    ->getData();
	                foreach($extra_arr as $ex)
	                {
	                	if (!empty($ex['image_path'])) {
	                		$extras_return[] = '<img src="'.PJ_INSTALL_URL . $ex['image_path'].'" class="img-responsive" /> '.$_extras[$ex['id']] . ' x ' . $ex['name'];
	                	} else {
	                    	$extras_return[] = $_extras[$ex['id']] . ' x ' . $ex['name'];
	                	}
	                    if ((float)$ex['price'] > 0) {
	                    	$return_extra_price += $_extras[$ex['id']] * $ex['price'];
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
            $cart = array_merge($cart, pjUtil::calPrice($one_way_price, $return_price, $total_extra_price, $cart['is_return'], $return_discount, $this->option_arr, '', ''));
        }
        if ($extras) {
        	$cart['extras'] = implode('<br/>', $extras);
        }
    	if ($extras_return) {
        	$cart['extras_return'] = implode('<br/>', $extras_return);
        }
		$cart['extra_price'] = $extra_price;
		$cart['return_extra_price'] = $return_extra_price;
		$cart['total_extra_price'] = $total_extra_price;
        $this->_set('cart', $cart);

        return $cart;
    }

    public function updateExtras($extras = array(), $type = 'pickup')
    {
        $pjExtraLimitationModel = pjExtraLimitationModel::factory();
        $extras = array_filter($extras, function ($v) {
            return $v > 0;
        });
        foreach($extras as $extra_id => $qty)
        {
            $limit = $pjExtraLimitationModel
                ->reset()
                ->select('max_qty')
                ->where('extra_id', $extra_id)
                ->where('fleet_id', $this->_get('fleet_id'))
                ->findAll()
                ->getDataIndex(0);
            $max_qty = isset($limit['max_qty'])? $limit['max_qty']: $this->option_arr['o_extras_max_qty'];
            if($max_qty < 1)
            {
                unset($extras[$extra_id]);
            }
            elseif($qty > $max_qty)
            {
                $extras[$extra_id] = $max_qty;
            }
        }
        if ($type == 'return') {
        	$this->_set('extras_return', $extras);
        } else {
        	$this->_set('extras', $extras);
        }
        $this->updateCart();

        return $this;
    }

	public function pjActionSearch()
	{
		$this->setAjax(true);
			    
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
		    $priceNotNullQuery = '';
		    $dropoff_place_arr = array();
            if($this->_is('search'))
			{
				$search_post = $this->_get('search');
				if (isset($search_post['date']) && !empty($search_post['date'])) {
					$date = pjUtil::formatDate($search_post['date'], $this->option_arr['o_date_format']);
					$dayIndex = date('N', strtotime($date));
                    $priceNotNullQuery = "WHERE TP.price_{$dayIndex} IS NOT NULL";
				}

				list($pickup_type, $pickup_id) = explode('~::~', $search_post['location_id']);
				$dropoff_arr = array();
				if ($pickup_type == 'server' || (isset($search_post['custom_pickup_id']) && (int)$search_post['custom_pickup_id'] > 0)) {
					$pjDropoffModel = pjDropoffModel::factory();
					if (isset($search_post['custom_pickup_id']) && (int)$search_post['custom_pickup_id'] > 0) {
						$pjDropoffModel->where('t1.location_id', (int)$search_post['custom_pickup_id']);
					} else {
						$pjDropoffModel->where('t1.location_id', $pickup_id);
					}
					$dropoff_arr = $pjDropoffModel
						->select("t1.*, t2.content as location")
						->join('pjMultiLang', "t2.model='pjDropoff' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')						
						//->where("t1.id IN (SELECT TP.dropoff_id FROM `".pjPriceModel::factory()->getTable()."` TP {$priceNotNullQuery})")
						->orderBy("t1.is_airport DESC, t1.order_index ASC, location ASC")
						->findAll()->getData();			
				}
				$this->set('dropoff_arr', $dropoff_arr);
				$this->set('search_post', $search_post);
			}
			
			if ($this->_is('skip_first_step')) {
				$search = $this->_get('skip_first_step');
				if (isset($search['date']) && !empty($search['date'])) {
					$date = pjUtil::formatDate($search['date'], $this->option_arr['o_date_format']);
					$dayIndex = date('N', strtotime($date));
					$priceNotNullQuery = "WHERE TP.price_{$dayIndex} IS NOT NULL";
				}

				list($pickup_type, $pickup_id) = explode('~::~', $search['location_id']);
				$dropoff_arr = array();
				if ($pickup_type == 'server' || (isset($search['custom_pickup_id']) && (int)$search['custom_pickup_id'] > 0)) {
					$pjDropoffModel = pjDropoffModel::factory();
					if (isset($search['custom_pickup_id']) && (int)$search['custom_pickup_id'] > 0) {
						$pjDropoffModel->where('t1.location_id', (int)$search['custom_pickup_id']);
					} else {
						$pjDropoffModel->where('t1.location_id', $pickup_id);
					}
					$dropoff_arr = $pjDropoffModel
						->select("t1.*, t2.content as location")
						->join('pjMultiLang', "t2.model='pjDropoff' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						//->where("t1.id IN (SELECT TP.dropoff_id FROM `".pjPriceModel::factory()->getTable()."` TP {$priceNotNullQuery})")
						->orderBy("t1.is_airport DESC, t1.order_index ASC, location ASC")
						->findAll()
						->getData();
					$this->set('dropoff_arr', $dropoff_arr);
				}
				$this->set('search_post', $search);
			}

			if (isset($dropoff_arr) && count($dropoff_arr) > 0) {
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
			    			$icon = 'fad fa-plane-departure';
			    		} elseif ($v['icon'] == 'train') {
			    			$icon = 'fad fa-subway';
			    		} else {
			    			$icon = 'fad fa-map-marker';
			    		}	
			    		$dropoff_place_arr[$k]['icon'] = $icon;
			    		$dropoff_place_arr[$k]['text'] = $v['place_name'];
			    		$dropoff_place_arr[$k]['id_formated'] = 'server~::~'.$v['id'].'~::~'.$v['dropoff_id'];
					}
				}
			}
			$this->set('dropoff_place_arr', $dropoff_place_arr);
			$_SESSION[$this->defaultDropoffLocations] = $dropoff_place_arr;
			
			$pickup_arr = pjLocationModel::factory()
				->select("t1.*, t2.content as pickup_location")
				->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where('t1.status', 'T')
				//->where("t1.id IN (SELECT TD.location_id FROM `".pjDropoffModel::factory()->getTable()."` TD INNER JOIN `".pjPriceModel::factory()->getTable()."` TP ON TP.dropoff_id=TD.id {$priceNotNullQuery})")
				->orderBy("t1.is_airport DESC, t1.order_index ASC, pickup_location ASC")
				->findAll()->getData();
			foreach ($pickup_arr as $k => $v) {
				if ($v['icon'] == 'airport') {
	    			$icon = 'fad fa-plane-departure';
	    		} elseif ($v['icon'] == 'train') {
	    			$icon = 'fad fa-subway';
	    		} else {
	    			$icon = 'fad fa-map-marker';
	    		}	
	    		$pickup_arr[$k]['icon'] = $icon;
	    		$pickup_arr[$k]['text'] = $v['pickup_location'];
	    		$pickup_arr[$k]['id_formated'] = 'server~::~'.$v['id'];
			}
			$this->set('pickup_arr', $pickup_arr);
			$_SESSION[$this->defaultPickupLocations] = $pickup_arr;

			$passenger_range_arr = pjFleetModel::factory()
                ->select('DISTINCT CONCAT_WS("-", IFNULL(min_passengers, 0), IFNULL(passengers, 0)) as from_to')
                ->where('status', 'T')
			    ->orderBy('from_to ASC')
                ->findAll()
                ->getDataPair(null, 'from_to');
            $this->set('passenger_range_arr', $passenger_range_arr);
            $min_passenger = $max_passenger = 1;
            foreach ($passenger_range_arr as $k => $val) {
            	list($min, $max) = explode('-', $val);
            	if ($k == 0 || $min < $min_passenger) {
            		$min_passenger = $min;
            	}
            	if ($k == 0 || $max > $max_passenger) {
            		$max_passenger = $max;
            	}
            }
            $this->set('min_passenger', $min_passenger);
            $this->set('max_passenger', $max_passenger);
		}
	}

	public function pjActionSearchNew()
	{
		$this->setAjax(true);

		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			$priceNotNullQuery = '';
			if($this->_is('search'))
			{
				$search_post = $this->_get('search');
				$dropoff_place_arr = array();
				if (isset($search_post['date']) && !empty($search_post['date'])) {
					$date = pjUtil::formatDate($search_post['date'], $this->option_arr['o_date_format']);
					$dayIndex = date('N', strtotime($date));
					$priceNotNullQuery = "WHERE TP.price_{$dayIndex} IS NOT NULL";
				}

				$dropoff_arr = pjDropoffModel::factory()
					->join('pjMultiLang', "t2.model='pjDropoff' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->select("t1.*, t2.content as location")
					->where('t1.location_id', $search_post['location_id'])
					//->where("t1.id IN (SELECT TP.dropoff_id FROM `".pjPriceModel::factory()->getTable()."` TP {$priceNotNullQuery})")
					->orderBy("location ASC")
					->findAll()->getData();
				$this->set('dropoff_arr', $dropoff_arr);
				$this->set('search_post', $search_post);
			}
			$dropoff_place_arr = array();
			if (isset($dropoff_arr) && count($dropoff_arr) > 0) {
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
			    			$icon = 'fad fa-plane-departure';
			    		} elseif ($v['icon'] == 'train') {
			    			$icon = 'fad fa-subway';
			    		} else {
			    			$icon = 'fad fa-map-marker';
			    		}	
			    		$dropoff_place_arr[$k]['icon'] = $icon;
			    		$dropoff_place_arr[$k]['text'] = $v['place_name'];
			    		$dropoff_place_arr[$k]['id_formated'] = 'server~::~'.$v['id'].'~::~'.$v['dropoff_id'];
					}
				}
			}
			$this->set('dropoff_place_arr', $dropoff_place_arr);
			$_SESSION[$this->defaultDropoffLocations] = $dropoff_place_arr;
			
			$pickup_arr = pjLocationModel::factory()
				->select("t1.*, t2.content as pickup_location")
				->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where('t1.status', 'T')
				//->where("t1.id IN (SELECT TD.location_id FROM `".pjDropoffModel::factory()->getTable()."` TD INNER JOIN `".pjPriceModel::factory()->getTable()."` TP ON TP.dropoff_id=TD.id {$priceNotNullQuery})")
				->orderBy("t1.is_airport DESC, t1.order_index ASC, pickup_location ASC")
				->findAll()->getData();
			foreach ($pickup_arr as $k => $v) {
				if ($v['icon'] == 'airport') {
	    			$icon = 'fad fa-plane-departure';
	    		} elseif ($v['icon'] == 'train') {
	    			$icon = 'fad fa-subway';
	    		} else {
	    			$icon = 'fad fa-map-marker';
	    		}	
	    		$pickup_arr[$k]['icon'] = $icon;
	    		$pickup_arr[$k]['text'] = $v['pickup_location'];
	    		$pickup_arr[$k]['id_formated'] = 'server~::~'.$v['id'];
			}
			$this->set('pickup_arr', $pickup_arr);
			$_SESSION[$this->defaultPickupLocations] = $pickup_arr;

			$passenger_range_arr = pjFleetModel::factory()
			->select('DISTINCT CONCAT_WS("-", IFNULL(min_passengers, 0), IFNULL(passengers, 0)) as from_to')
			->where('status', 'T')
			->orderBy('from_to ASC')
			->findAll()
			->getDataPair(null, 'from_to');
			$this->set('passenger_range_arr', $passenger_range_arr);
			$min_passenger = $max_passenger = 1;
			foreach ($passenger_range_arr as $k => $val) {
			    list($min, $max) = explode('-', $val);
			    if ($k == 0 || $min < $min_passenger) {
			        $min_passenger = $min;
			    }
			    if ($k == 0 || $max > $max_passenger) {
			        $max_passenger = $max;
			    }
			}
			$this->set('min_passenger', $min_passenger);
			$this->set('max_passenger', $max_passenger);
		}
	}
	
	public function pjActionServices()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if (isset($_SESSION[$this->defaultStore][$this->defaultIndex]) && count($_SESSION[$this->defaultStore][$this->defaultIndex]) > 0)
			{
				if($this->_is('search'))
				{
					$pjFleetModel = pjFleetModel::factory();
					
					$STORE = @$_SESSION[$this->defaultStore][$this->defaultIndex];
					$search_post = $this->_get('search');
					$date = pjUtil::formatDate($search_post['date'], $this->option_arr['o_date_format']);
					$dayIndex = date('N', strtotime($date));
					
					$dropoff_id = 0;
					$passengers_from_to = (int)$search_post['passengers_from_to'];
					if ($search_post['dropoff_type'] == 'server') {
						$pjFleetModel->where('t4.dropoff_id', $search_post['dropoff_id']);
						$dropoff_id = (int)$search_post['dropoff_id'];
					} elseif ($search_post['dropoff_type'] == 'google' && (int)$search_post['custom_dropoff_id'] > 0) {
						$pjFleetModel->where('t4.dropoff_id', (int)$search_post['custom_dropoff_id']);
						$dropoff_id = (int)$search_post['custom_dropoff_id'];
					}
					
					$distance = round($search_post['distance']/1000);
					$duration = round($search_post['duration']/60);
					$tblFleetFee = pjFleetFeeModel::factory()->getTable();
					
					$station_fee_arr = $this->getStationFee($STORE['search']['pickup_lat'], $STORE['search']['pickup_lng'], @$STORE['search']['dropoff_lat'], @$STORE['search']['dropoff_lng']);
                	$pjFleetModel->where('t1.station_id', (int)$station_fee_arr['station_id']);
                	
					if (($search_post['pickup_type'] == 'google' && (int)$search_post['custom_pickup_id'] <= 0) || ($search_post['dropoff_type'] == 'google' && (int)$search_post['custom_dropoff_id'] <= 0)) {
					    $data_latlng = array(
					        'dropoff_lat' => $search_post['dropoff_lat'],
					        'dropoff_lng' => $search_post['dropoff_lng'],
					        'pickup_lat' => $search_post['pickup_lat'],
					        'pickup_lng' => $search_post['pickup_lng']
					    );
					    $dropoff_area = $this->check_area_to_get_price_level($data_latlng);
					    if ($dropoff_area) {
					        $price_level = $dropoff_area['price_level'];
					    } else {
					        $price_level = 0;
					    }
						$pjFleetModel
							->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
							->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
							->join('pjMultiLang', "t4.model='pjFleet' AND t4.foreign_id=t1.id AND t4.field='badget' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
							->join('pjMultiLang', "t5.model='pjFleet' AND t5.foreign_id=t1.id AND t5.field='model' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
							->select("t1.*, t2.content as fleet, t3.content as description, t4.content AS badget, t5.content AS `model`, 
                            (SELECT `price` FROM `".$tblFleetFee."` WHERE `fleet_id`=t1.id AND '".$distance."' BETWEEN `start` AND `end` LIMIT 1) as price, 
                            IF (".$price_level."=2, t1.return_discount_{$dayIndex}_2, IF (".$price_level."=1, t1.return_discount_{$dayIndex}, 0)) as return_discount, 
                            '".$distance."' AS distance, '".$duration."' AS duration")
							->where('t1.status', 'T')
	                        ->where("({$passengers_from_to} BETWEEN t1.min_passengers AND t1.passengers)");
					} else {
						$pjFleetModel
							->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
							->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
							->join('pjPrice', "t1.id=t4.fleet_id", 'left')
							->join('pjDropoff', "t5.id=t4.dropoff_id", 'left')
							->join('pjMultiLang', "t6.model='pjFleet' AND t6.foreign_id=t1.id AND t6.field='badget' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
							->join('pjMultiLang', "t7.model='pjFleet' AND t7.foreign_id=t1.id AND t7.field='model' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
							->select("t1.*, t2.content as fleet, t3.content as description, t6.content AS badget, t7.content AS `model`, t4.price_{$dayIndex} as price, IF (t5.price_level=2, t1.return_discount_{$dayIndex}_2, t1.return_discount_{$dayIndex}) as return_discount, '".$distance."' AS distance, '".$duration."' AS duration")
							->where('t1.status', 'T')
	                        ->where("({$passengers_from_to} BETWEEN t1.min_passengers AND t1.passengers)");
                        $drop_arr = pjDropoffModel::factory()->find((int)$dropoff_id)->getData();
                        $price_level = $drop_arr ? $drop_arr['price_level'] : 1;
					}		
					$total = $pjFleetModel->findCount()->getData();
					$rowCount = (int) $this->option_arr['o_vehicle_per_page'] > 0 ? $this->option_arr['o_vehicle_per_page'] : 5;
					$pages = ceil($total / $rowCount);
					$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
					$offset = ((int) $page - 1) * $rowCount;
					if ($page > $pages)
					{
						$page = $pages;
					}
					//$pjFleetModel->limit($rowCount, $offset);
						
					$fleet_arr = $pjFleetModel
						->orderBy("t1.order_index ASC, fleet ASC")
						->findAll()
						->getData();

					$this->set('fleet_arr', $fleet_arr);
					$this->set('paginator', array('pages' => $pages, 'page' => $page, 'offset' => $offset, 'total' => $total));
					$this->set('no_date_selected', $date === false);
					$this->set('store', $STORE);
					
					$op_arr = pjMultiLangModel::factory()->select('t1.*')
                        ->where('t1.model','pjOption')
                        ->where('t1.locale', $this->getLocaleId())
                        ->where('t1.field', 'o_no_credit_card_fees_info')
                        ->limit(0, 1)
                        ->findAll()->getDataIndex(0);
                        $this->set('o_no_credit_card_fees_info', $op_arr ? $op_arr['content'] : '');

                    $op_arr = pjMultiLangModel::factory()->reset()->select('t1.*')
                        ->where('t1.model','pjOption')
                        ->where('t1.locale', $this->getLocaleId())
                        ->where('t1.field', 'o_free_waiting_time_info')
                        ->limit(0, 1)
                        ->findAll()->getDataIndex(0);
                        $this->set('o_free_waiting_time_info', $op_arr ? $op_arr['content'] : '');
                    
                    $op_arr = pjMultiLangModel::factory()->reset()->select('t1.*')
                        ->where('t1.model','pjOption')
                        ->where('t1.locale', $this->getLocaleId())
                        ->where('t1.field', 'o_meet_greet_service_info')
                        ->limit(0, 1)
                        ->findAll()->getDataIndex(0);
                    $this->set('o_meet_greet_service_info', $op_arr ? $op_arr['content'] : '');
                    $_SESSION[$this->defaultStore][$this->defaultIndex]['price_level'] = $price_level;
                    $this->set('price_level', $price_level);
				}
				$this->set('status', 'OK');
			}else{
				$this->set('status', 'ERR');
			}
		}
	}

    public function pjActionTransferType()
    {
        $this->setAjax(true);

        if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
        {
            if($this->_is('fleet_id'))
            {
                $search_post = $this->_get('search');
                $store = @$_SESSION[$this->defaultStore][$this->defaultIndex];
                $price_level = isset($store['price_level']) ? $store['price_level'] : 0;
                $dayIndex = date('N', strtotime(pjUtil::formatDate($search_post['date'], $this->option_arr['o_date_format'])));
				$dropoff_id = ($search_post['dropoff_type'] == 'google' && (int)$search_post['custom_dropoff_id'] > 0) ? (int)$search_post['custom_dropoff_id'] : (int)$search_post['dropoff_id'];
                $fleet = pjFleetModel::factory()
                    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjPrice', "t1.id=t3.fleet_id AND t3.dropoff_id = " . $dropoff_id, 'left')
                    ->join('pjDropoff', "t4.id=t3.dropoff_id", 'left outer')
                    ->select("t1.*, t2.content as fleet, t3.price_{$dayIndex} as price, 
                        IF (".$price_level."=2, return_discount_{$dayIndex}_2, IF (".$price_level."=1, return_discount_{$dayIndex}, 0)) as return_discount_{$dayIndex}")
                    ->where('t1.status', 'T')
                    ->find($this->_get('fleet_id'))
                    ->getData();
                $this->set('fleet', $fleet);
                $this->set('store', $store);
                
            	$cart = $this->_get('cart');
            	$this->set('cart', $cart);

                $this->set('status', 'OK');
            }else{
                $this->set('status', 'ERR');
            }
        }
    }

    public function pjActionExtras()
    {
        $this->setAjax(true);

        if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
        {
            if (isset($_SESSION[$this->defaultStore][$this->defaultIndex]) && count($_SESSION[$this->defaultStore][$this->defaultIndex]) > 0 && $this->_get('fleet_id'))
            {
                $this->set('extra_arr', pjExtraModel::factory()
                    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.field='info' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->select("t1.*, t2.content as name, t3.content as info")
                    ->where('t1.status', 'T')
                    ->orderBy("t1.id ASC")
                    ->findAll()
                    ->getData());
                $this->set('el_arr', pjExtraLimitationModel::factory()
                    ->where('fleet_id', $this->_get('fleet_id'))
                    ->findAll()
                    ->getDataPair('extra_id', 'max_qty'));

                if($this->_is('extras'))
                {
                    $this->updateExtras($this->_get('extras'));
                }

                $this->set('store', @$_SESSION[$this->defaultStore][$this->defaultIndex]);
                $this->set('cart', $this->_get('cart'));

                $this->set('status', 'OK');
            }else{
                $this->set('status', 'ERR');
            }
        }
    }

    public function pjActionDeparture()
    {
        $this->setAjax(true);

        if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
        {
            if (isset($_SESSION[$this->defaultStore][$this->defaultIndex]) && count($_SESSION[$this->defaultStore][$this->defaultIndex]) > 0)
            {
                if($this->_is('fleet_id'))
                {
                    $search_post = $this->_get('search');
                    $dropoff_id = (int)$search_post['dropoff_id'];
                    $store = @$_SESSION[$this->defaultStore][$this->defaultIndex];
                    $price_level = isset($store['price_level']) ? $store['price_level'] : 0;
                    $dayIndex = date('N', strtotime(pjUtil::formatDate($search_post['date'], $this->option_arr['o_date_format'])));

                    $fleet = pjFleetModel::factory()
                        ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                        ->join('pjPrice', "t1.id=t3.fleet_id AND t3.dropoff_id = " . $dropoff_id, 'left')
                        ->join('pjDropoff', "t4.id=t3.dropoff_id", 'left')
                        ->select("t1.*, t4.price_level, t2.content as fleet, t3.price_{$dayIndex} as price, 
                            IF (".$price_level."=2, return_discount_{$dayIndex}_2, IF (".$price_level."=1, return_discount_{$dayIndex}, 0)) as return_discount_{$dayIndex}")
                        ->where('t1.status', 'T')
                        ->find($this->_get('fleet_id'))
                        ->getData();
                    $this->set('fleet', $fleet);
                    
	                $this->set('extra_arr', pjExtraModel::factory()
	                    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	                    ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.field='info' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
	                    ->select("t1.*, t2.content as name, t3.content as info")
	                    ->where('t1.status', 'T')
	                    ->orderBy("t1.id ASC")
	                    ->findAll()
	                    ->getData());
	                $this->set('el_arr', pjExtraLimitationModel::factory()
	                    ->where('fleet_id', $this->_get('fleet_id'))
	                    ->findAll()
	                    ->getDataPair('extra_id', 'max_qty'));
	
	                if($this->_is('extras'))
	                {
	                    $this->updateExtras($this->_get('extras'), 'pickup');
	                }
	                
                	if($this->_is('extras_return'))
	                {
	                    $this->updateExtras($this->_get('extras_return'), 'return');
	                }

                    $this->set('store', $store);
                    $this->set('form', @$_SESSION[$this->defaultForm][$this->defaultIndex]);
                    $this->set('cart', $this->_get('cart'));
                }
                $this->set('status', 'OK');
            }else{
                $this->set('status', 'ERR');
            }
        }
    }

    public function pjActionSaveDeparture()
    {
        $this->setAjax(true);

        if ($this->isXHR())
        {
            $resp = array();
            if (!isset($_SESSION[$this->defaultForm][$this->defaultIndex]) || count($_SESSION[$this->defaultForm][$this->defaultIndex]) === 0)
            {
                $_SESSION[$this->defaultForm][$this->defaultIndex] = array();
            }
			$is_return = (int) $this->_get('is_return');
            $search_post = $this->_get('search');
            if($search_post['date'] != $_POST['date_confirm'])
            {
                $search_post['date'] = $_POST['date_confirm'];
                $this->_set('search', $search_post);
            }
            if (isset($_POST['has_return']) && $_POST['has_return'] == 1) {
            	$data = array();
	        	if (isset($_POST['return_date'])) {
	        		$date_confirm = pjUtil::formatDate($_POST['date_confirm'], $this->option_arr['o_date_format']);
	        		$return_date = pjUtil::formatDate($_POST['return_date'], $this->option_arr['o_date_format']);
	        		if (strtotime($return_date) < strtotime($date_confirm)) {
	        			$data['return_date'] = $_POST['date_confirm'];
	        		} else {
	            		$data['return_date'] = $_POST['return_date'];
	        		}
	            }
		        if (isset($_POST['passengers_return'])) {
	            	$data['passengers_return'] = $_POST['passengers_return'];
	            }
		        if (isset($_POST['return_pickup_time'])) {
	            	$data['return_pickup_time'] = $_POST['return_pickup_time'];
	            }
		        if (isset($_POST['return_c_address'])) {
	            	$data['return_c_address'] = $_POST['return_c_address'];
	            }
		        if (isset($_POST['return_c_destination_address'])) {
	            	$data['return_c_destination_address'] = $_POST['return_c_destination_address'];
	            }
		        if (isset($_POST['return_c_notes'])) {
	            	$data['return_c_notes'] = $_POST['return_c_notes'];
	            }
		        if (isset($_POST['return_c_departure_flight_time'])) {
	            	$data['return_c_departure_flight_time'] = $_POST['return_c_departure_flight_time'];
	            }
		        if (isset($_POST['return_time'])) {
	            	$data['return_time'] = $_POST['return_time'];
	            }
		        if (isset($_POST['return_c_flight_number'])) {
	            	$data['return_c_flight_number'] = $_POST['return_c_flight_number'];
	            }
		        if (isset($_POST['return_c_airline_company'])) {
	            	$data['return_c_airline_company'] = $_POST['return_c_airline_company'];
	            }
	            $_SESSION[$this->defaultForm][$this->defaultIndex]['return'] = $data;
            }
            unset($_POST['date_confirm']);
            $departure_arr = isset($_SESSION[$this->defaultForm][$this->defaultIndex]['departure']) ? $_SESSION[$this->defaultForm][$this->defaultIndex]['departure'] : array();
            $_SESSION[$this->defaultForm][$this->defaultIndex]['departure'] = array_merge($departure_arr, $_POST);
            $this->updateCart();
            
            if (isset($_GET['submit'])) {
                $error_arr = array();
                $departureData = @$_SESSION[$this->defaultForm][$this->defaultIndex]['departure'];
                $search_post = $this->_get('search');
                
                $_date = pjUtil::formatDate($search_post['date'], $this->option_arr['o_date_format']);
                $time = '';
                if (isset($departureData['pickup_time'])) {
                    $time = date('H:i:s', strtotime($departureData['pickup_time']));
                } elseif (isset($departureData['arrival_time'])) {
                    $time = date('H:i:s', strtotime($departureData['arrival_time']));
                }
                $booking_date = $_date . ' ' . $time;
                if (strtotime($booking_date) < time()) {
                    $error_arr[] = __('front_label_first_transfer', true).': '.__('front_invalid_time', true);
                }
                if (isset($_POST['has_return']) && $_POST['has_return'] == 1) {
                    $returnData = @$_SESSION[$this->defaultForm][$this->defaultIndex]['return'];
                    $_return_date = pjUtil::formatDate($returnData['return_date'], $this->option_arr['o_date_format']);
                    if (isset($returnData['return_pickup_time'])) {
                        $_return_time = date('H:i:s', strtotime($returnData['return_pickup_time']));
                    } elseif (isset($returnData['return_time'])) {
                        $_return_time = date('H:i:s', strtotime($returnData['return_time']));
                    }
                    $return_date = $_return_date . ' ' . $_return_time;
                    if (strtotime($return_date) < time()) {
                        $error_arr[] = __('front_label_return_transfer', true).': '.__('front_invalid_time', true);
                    }
                }
                
                if ($error_arr) {
                    $resp['code'] = 201;
                    $resp['text'] = implode("<br/>", $error_arr);
                }
                else {
                    $resp['code'] = 200;
                }
            } else {
                $resp['code'] = 200;
            }
            
            $resp['is_return'] = $is_return;

            pjAppController::jsonResponse($resp);
        }
    }

    public function pjActionReturn()
    {
        $this->setAjax(true);

        if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
        {
            if (isset($_SESSION[$this->defaultStore][$this->defaultIndex]) && count($_SESSION[$this->defaultStore][$this->defaultIndex]) > 0)
            {
                if($this->_is('fleet_id'))
                {
                	$search_post = $this->_get('search');
                	$dropoff_id = (int)$search_post['dropoff_id'];
                    $dayIndex = date('N', strtotime(pjUtil::formatDate($search_post['date'], $this->option_arr['o_date_format'])));
                    
                	$fleet = pjFleetModel::factory()
                        ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                        ->join('pjPrice', "t1.id=t3.fleet_id AND t3.dropoff_id = " . $dropoff_id, 'left')
                        ->select("t1.*, t2.content as fleet, t3.price_{$dayIndex} as price")
                        ->where('t1.status', 'T')
                        ->find($this->_get('fleet_id'))
                        ->getData();
                    $this->set('fleet', $fleet);
                    
                	$this->set('extra_arr', pjExtraModel::factory()
	                    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	                    ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.field='info' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
	                    ->select("t1.*, t2.content as name, t3.content as info")
	                    ->where('t1.status', 'T')
	                    ->orderBy("t1.id ASC")
	                    ->findAll()
	                    ->getData());
	                $this->set('el_arr', pjExtraLimitationModel::factory()
	                    ->where('fleet_id', $this->_get('fleet_id'))
	                    ->findAll()
	                    ->getDataPair('extra_id', 'max_qty'));
	
	                if($this->_is('extras_return'))
	                {
	                    $this->updateExtras($this->_get('extras_return'));
	                }
                                        
                    $distance_arr = $this->calcDistanceBetweenTwoLocations($search_post['dropoff_lat'], $search_post['dropoff_lng'], $search_post['pickup_lat'], $search_post['pickup_lng'], $this->option_arr);
					if (isset($distance_arr['rows'][0]['elements'][0]['status']) && $distance_arr['rows'][0]['elements'][0]['status'] == 'OK') {
						$search_post['return_distance'] = $distance_arr['rows'][0]['elements'][0]['distance']['value'];
						$search_post['return_duration'] = $distance_arr['rows'][0]['elements'][0]['duration']['value'];
					}
					$this->_set('search', $search_post);
					
					$this->set('store', @$_SESSION[$this->defaultStore][$this->defaultIndex]);
                    $this->set('form', @$_SESSION[$this->defaultForm][$this->defaultIndex]);
                    $this->set('cart', $this->_get('cart'));
                }
                $this->set('status', 'OK');
            }else{
                $this->set('status', 'ERR');
            }
        }
    }

    public function pjActionSaveReturn()
    {
        $this->setAjax(true);

        if ($this->isXHR())
        {
            $resp = array();
            if (!isset($_SESSION[$this->defaultForm][$this->defaultIndex]) || count($_SESSION[$this->defaultForm][$this->defaultIndex]) === 0)
            {
                $_SESSION[$this->defaultForm][$this->defaultIndex] = array();
            }
            $data = array();
        	if (isset($_POST['return_date'])) {
        		$date_confirm = pjUtil::formatDate($_POST['date_confirm'], $this->option_arr['o_date_format']);
        		$return_date = pjUtil::formatDate($_POST['return_date'], $this->option_arr['o_date_format']);
        		if (strtotime($return_date) < strtotime($date_confirm)) {
        			$data['return_date'] = $_POST['date_confirm'];
        		} else {
            		$data['return_date'] = $_POST['return_date'];
        		}
            }
	        if (isset($_POST['passengers_return'])) {
            	$data['passengers_return'] = $_POST['passengers_return'];
            }
	        if (isset($_POST['return_pickup_time'])) {
            	$data['return_pickup_time'] = $_POST['return_pickup_time'];
            }
	        if (isset($_POST['return_c_address'])) {
            	$data['return_c_address'] = $_POST['return_c_address'];
            }
	        if (isset($_POST['return_c_destination_address'])) {
            	$data['return_c_destination_address'] = $_POST['return_c_destination_address'];
            }
	        if (isset($_POST['return_c_notes'])) {
            	$data['return_c_notes'] = $_POST['return_c_notes'];
            }
	        if (isset($_POST['return_c_departure_flight_time'])) {
            	$data['return_c_departure_flight_time'] = $_POST['return_c_departure_flight_time'];
            }
	        if (isset($_POST['return_time'])) {
            	$data['return_time'] = $_POST['return_time'];
            }
	        if (isset($_POST['return_c_flight_number'])) {
            	$data['return_c_flight_number'] = $_POST['return_c_flight_number'];
            }
	        if (isset($_POST['return_c_airline_company'])) {
            	$data['return_c_airline_company'] = $_POST['return_c_airline_company'];
            }
            $return_arr = isset($_SESSION[$this->defaultForm][$this->defaultIndex]['return']) ? $_SESSION[$this->defaultForm][$this->defaultIndex]['return'] : array();
            $_SESSION[$this->defaultForm][$this->defaultIndex]['return'] = array_merge($return_arr, $data);
            $this->updateCart();
            $resp['code'] = 200;

            pjAppController::jsonResponse($resp);
        }
    }

    public function pjActionPassenger()
    {
        $this->setAjax(true);

        if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
        {
            if (isset($_SESSION[$this->defaultStore][$this->defaultIndex]) && count($_SESSION[$this->defaultStore][$this->defaultIndex]) > 0)
            {
                if($this->_is('fleet_id'))
                {
                    $this->set('country_arr', pjCountryModel::factory()
                        ->select('t1.id, t1.alpha_2, t2.content AS country_title, t3.code')
                        ->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                        ->join('pjDialingCode', "t3.country_id=t1.id", 'left outer')
                        ->orderBy('`country_title` ASC')
                        ->findAll()
                        ->getData()
                    );

                    $this->set('store', @$_SESSION[$this->defaultStore][$this->defaultIndex]);
                    $this->set('form', @$_SESSION[$this->defaultForm][$this->defaultIndex]);
                    $this->set('cart', $this->_get('cart'));
                    $ip = pjUtil::getClientIp();
                    //$ip = '115.79.208.13';
                    $get_location_by_ip = $this->getLocationByIp($ip);
                    if ($get_location_by_ip['success']) {
                    	$this->set('default_country_code', $get_location_by_ip['country_code']);
                    	$this->set('default_country_phone', $get_location_by_ip['country_phone']);
                    }
                    
                    $arr = pjMultiLangModel::factory()->select('t1.*')
                        ->where('t1.model','pjOption')
                        ->where('t1.locale', $this->getLocaleId())
                        ->where('t1.field', 'o_terms')
                        ->limit(0, 1)
                        ->findAll()->getDataIndex(0);
					$this->set('terms_conditions', $arr['content']);
                }			    
                $this->set('status', 'OK');
            }else{
                $this->set('status', 'ERR');
            }
        }
    }
    
	protected function doPaySafeURL($option_arr, $payload, $url, $opt, $booking_uuid)
	{
	    $pjBookingModel = pjBookingModel::factory();
	    
	    if($opt == 'initialize')
	    {
	        $pjBookingModel->reset()->where('uuid', $booking_uuid)->limit(1)->modifyAll(array('saferpay_request_id' => $payload['RequestHeader']['RequestId']));
	    }
	    $curl = curl_init($url);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json","Accept: application/json; charset=utf-8"));
	    curl_setopt($curl, CURLOPT_POST, true);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
	    curl_setopt($curl, CURLOPT_USERPWD, $option_arr['o_saferpay_username'] . ":" . $option_arr['o_saferpay_password']);
	    $jsonResponse = curl_exec($curl);
	    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	    if ($status != 200) {
	        $body = json_decode(curl_multi_getcontent($curl), true);
	        $response = array(
	            "status" => $status . " <|> " . curl_error($curl),
	            "body" => $body
	        );
	    }else {
	        $body = json_decode($jsonResponse, true);
	        $response = array(
	            "status" => $status,
	            "body" => $body
	        );
	        if($opt == 'initialize')
	        {
	            $pjBookingModel->reset()->where('uuid', $booking_uuid)->limit(1)->modifyAll(array('saferpay_token' => $body['Token']));
	        }
	    }
	    curl_close($curl);
	    return $response;
	}

    public function pjActionSavePassenger()
    {
        $this->setAjax(true);

        if ($this->isXHR())
        {
            $resp = array();
            if (!isset($_SESSION[$this->defaultForm][$this->defaultIndex]) || count($_SESSION[$this->defaultForm][$this->defaultIndex]) === 0)
            {
                $_SESSION[$this->defaultForm][$this->defaultIndex] = array();
            }
            $_SESSION[$this->defaultForm][$this->defaultIndex]['passenger'] = $_POST;
            //$this->updateCart();
            $resp['code'] = 200;

            pjAppController::jsonResponse($resp);
        }
    }
	
	public function pjActionCheckout()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if (isset($_SESSION[$this->defaultStore][$this->defaultIndex]) && count($_SESSION[$this->defaultStore][$this->defaultIndex]) > 0)
			{
				if($this->_is('fleet_id'))
				{
					$arr = pjMultiLangModel::factory()->select('t1.*')
                        ->where('t1.model','pjOption')
                        ->where('t1.locale', $this->getLocaleId())
                        ->where('t1.field', 'o_terms')
                        ->limit(0, 1)
                        ->findAll()->getDataIndex(0);
					$this->set('terms_conditions', $arr['content']);

                    $arr = pjMultiLangModel::factory()->select('t1.*')
                        ->where('t1.model','pjOption')
                        ->where('t1.locale', $this->getLocaleId())
                        ->where('t1.field', 'o_shared_trip_info')
                        ->limit(0, 1)
                        ->findAll()->getDataIndex(0);
                    $this->set('shared_trip_info', $arr['content']);
					
					$this->set('store', @$_SESSION[$this->defaultStore][$this->defaultIndex]);
					$this->set('form', @$_SESSION[$this->defaultForm][$this->defaultIndex]);
                    $this->set('cart', $this->_get('cart'));
				}
				$this->set('status', 'OK');
			}else{
				$this->set('status', 'ERR');
			}
		}
	}
	public function pjActionSummary()
	{
		$this->setAjax(true);

		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if (isset($_GET['booking_id']) && !empty($_GET['booking_id']))
			{
                $pjBookingModel = pjBookingModel::factory();

                $booking_arr = $pjBookingModel->reset()
                    ->select("t1.*,t2.content as fleet, t3.content as location, t4.content as dropoff, t5.content as dropoff_place_name")
                    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t5.model='pjAreaCoord' AND t5.foreign_id=t1.dropoff_place_id AND t5.field='place_name' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
                    ->find($_GET['booking_id'])
                    ->getData();
                $this->set('arr', $booking_arr);

                $return_arr = array();
                if(!empty($booking_arr['return_date']))
                {
                    $return_arr = $pjBookingModel->reset()
                        ->select("t1.*,t2.content as fleet, t3.content as location, t4.content as dropoff, t5.content as dropoff_place_name")
                        ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                        ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                        ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
                        ->join('pjMultiLang', "t5.model='pjAreaCoord' AND t5.foreign_id=t1.dropoff_place_id AND t5.field='place_name' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
                        ->where('t1.return_id', $booking_arr['id'])
                        ->findAll()
                        ->getDataIndex(0);
                }
                $this->set('return_arr', $return_arr);

                $pickup_arr = pjLocationModel::factory()
                    ->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->select("t1.*, t2.content as pickup_location")
                    ->where('t1.status', 'T')
                    ->orderBy("pickup_location ASC")
                    ->find($booking_arr['location_id'])
                    ->getData();
                $this->set('pickup_arr', $pickup_arr);

                $dropoff_arr = pjAreaCoordModel::factory()
                    ->join('pjMultiLang', "t2.model='pjArea' AND t2.foreign_id=t1.area_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjAreaCoord' AND t3.foreign_id=t1.id AND t3.field='place_name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->select("t1.*, t2.content as area_name, t3.content AS place_name")
                    ->find($booking_arr['dropoff_place_id'])
                    ->getData();
                $this->set('dropoff_arr', $dropoff_arr);

                $fleet = pjFleetModel::factory()
                    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->select("t1.id, t2.content as fleet")
                    ->find($booking_arr['fleet_id'])
                    ->getData();
                $this->set('fleet', $fleet);

                $extra_arr = pjBookingExtraModel::factory()
                    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.extra_id AND t3.field='info' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->select("t1.quantity, t1.price, t2.content as name, t3.content as info")
                    ->where('booking_id', $booking_arr['id'])
                    ->orderBy('t1.extra_id ASC')
                    ->findAll()
                    ->getData();
                $this->set('extra_arr', $extra_arr);
                if ($return_arr) {
                	$extra_return_arr = pjBookingExtraModel::factory()->reset()
	                    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	                    ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.extra_id AND t3.field='info' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
	                    ->select("t1.quantity, t1.price, t2.content as name, t3.content as info")
	                    ->where('booking_id', $return_arr['id'])
	                    ->orderBy('t1.extra_id ASC')
	                    ->findAll()
	                    ->getData();
	                $this->set('extra_return_arr', $extra_return_arr);
                }

				$bookingDate = new DateTime($booking_arr['booking_date']);
				$arrivalNotice = pjArrivalNoticeModel::factory()
					->reset()
					->where('t1.date_from <=', $bookingDate->format('Y-m-d'))
					->where('t1.date_to >=', $bookingDate->format('Y-m-d'))
					->findCount()
					->getData();
				$this->set('arrivalNotice', $arrivalNotice);

                $country_arr = array();
                if(!empty($booking_arr['c_country']))
                {
                    $country_arr = pjCountryModel::factory()
                                ->select('t1.id, t2.content AS country_title')
                                ->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                                ->find($booking_arr['c_country'])->getData();
                }
                $this->set('country_arr', $country_arr);

				switch ($booking_arr['payment_method'])
				{
					case 'paypal':
						$this->set('params', array(
							'name' => 'trPaypal',
							'id' => 'trPaypal',
							'business' => $this->option_arr['o_paypal_address'],
							'item_name' => __('front_transfer_reservation', true, false),
							'custom' => $booking_arr['id'],
							'amount' => number_format($booking_arr['deposit'], 2, '.', ''),
							'currency_code' => $this->option_arr['o_currency'],
							'return' => $this->option_arr['o_thankyou_page'],
							'notify_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmPaypal',
							'target' => '_self'
						));
						break;
					case 'authorize':
						$this->set('params', array(
							'name' => 'trAuthorize',
							'id' => 'trAuthorize',
							'target' => '_self',
							'timezone' => $this->option_arr['o_timezone'],
							'transkey' => $this->option_arr['o_authorize_transkey'],
							'x_login' => $this->option_arr['o_authorize_merchant_id'],
							'x_description' => __('front_transfer_reservation', true, false),
							'x_amount' => number_format($booking_arr['deposit'], 2, '.', ''),
							'x_invoice_num' => $booking_arr['id'],
							'x_receipt_link_url' => $this->option_arr['o_thankyou_page'],
							'x_relay_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmAuthorize'
						));
						break;
					case 'saferpay':
						if (empty($booking_arr['txn_id'])) {
						    $locale_arr = pjLocaleModel::factory()->find($booking_arr['locale_id'])->getData();
						    list($iso, ) = explode('-', $locale_arr['language_iso']);
			                $url = PJ_TEST_MODE ? 'https://test.saferpay.com/api/Payment/v1/PaymentPage/Initialize' : 'https://www.saferpay.com/api/Payment/v1/PaymentPage/Initialize';
						    $payload = array(
						        'RequestHeader' => array(
						            'SpecVersion' => "1.10",
						            'CustomerId' => $this->option_arr['o_saferpay_customer_id'],
						            'RequestId' => md5($booking_arr['uuid'] . PJ_SALT),
						            'RetryIndicator' => 0,
						            'ClientInfo' => array(
						                'ShopInfo' => "My Shop",
						                'OsInfo' => "Windows Server 2013"
						            )
						        ),
						        'TerminalId' => $this->option_arr['o_saferpay_terminal_id'],
						        'Payment' => array(
						            'Amount' => array(
						                'Value' => $booking_arr['deposit'] * 100,
						                'CurrencyCode' => $this->option_arr['o_currency']
						            ),
						            'OrderId' => $booking_arr['uuid'],
						            'Description' => __('front_transfer_reservation', true, false)
						        ),
						        'Payer' => array(
						            //'IpAddress' => pjUtil::getClientIp(),
						            'IpAddress' => "192.168.178.1",
						            'LanguageCode' => $iso
						        ),
						        'ReturnUrls' => array(
						            'Success' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmPaySafe&locale='.$booking_arr['locale_id'].'&uuid='.$booking_arr['uuid'],
						            'Fail' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionPaySafeReturn&locale='.$booking_arr['locale_id'].'&type=fail&uuid='.$booking_arr['uuid'],
						            'Abort' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionPaySafeReturn&locale='.$booking_arr['locale_id'].'&type=abort&uuid='.$booking_arr['uuid']
						        ),
						        'Notification' => array(
						            'NotifyUrl' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionPaySafeReturn&locale='.$booking_arr['locale_id'].'&type=notify&uuid='.$booking_arr['uuid']
						        )
						    );
						    $paysafe_data = $this->doPaySafeURL($this->option_arr, $payload, $url, 'initialize', $booking_arr['uuid']);
						    $this->set('paysafe_data', $paysafe_data);
						}
						break;
				}
			
				$this->set('status', 'OK');
			}else{
				$this->set('status', 'ERR');
			}
		}
	}
	
	public function pjActionGetLocations()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$dropoff_arr = $dropoff_place_arr = array();
			if (isset($_GET['location_id']) && !empty($_GET['location_id'])) {
				if (isset($_GET['custom_pickup_id']) && (int)$_GET['custom_pickup_id'] > 0) {
					$type = 'server';
					$location_id = (int)$_GET['custom_pickup_id'];
				} else {
					list($type, $location_id) = explode('~::~', $_GET['location_id']);
				}
				if ($type == 'server') {
					$priceNotNullQuery = '';
					$date = pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']);
					if($date)
		            {
		                $dayIndex = date('N', strtotime($date));
		                $priceNotNullQuery = "WHERE TP.price_{$dayIndex} IS NOT NULL";
		            }
		
					$dropoff_arr = pjDropoffModel::factory()
						->select("t1.*, t2.content as location")
						->join('pjMultiLang', "t2.model='pjDropoff' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->where('t1.location_id', $location_id)
						//->where("t1.id IN (SELECT TP.dropoff_id FROM `".pjPriceModel::factory()->getTable()."` TP {$priceNotNullQuery})")
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
			    			$icon = 'fad fa-plane-departure';
			    		} elseif ($v['icon'] == 'train') {
			    			$icon = 'fad fa-subway';
			    		} else {
			    			$icon = 'fad fa-map-marker';
			    		}
			    		$dropoff_place_arr[$k]['icon'] = $icon;
			    		$dropoff_place_arr[$k]['text'] = $v['place_name'];
			    		$dropoff_place_arr[$k]['id_formated'] = 'server~::~'.$v['id'].'~::~'.$v['dropoff_id'];
					}
				}
			}
			$_SESSION[$this->defaultDropoffLocations] = $dropoff_place_arr;
			$this->set('dropoff_arr', $dropoff_arr);
			$this->set('dropoff_place_arr', $dropoff_place_arr);
		}
	}
	
	public function pjActionGetLocationsNew()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$dropoff_arr = $dropoff_place_arr = array();
			if (isset($_GET['location_id']) && !empty($_GET['location_id'])) {
				if (isset($_GET['custom_pickup_id']) && (int)$_GET['custom_pickup_id'] > 0) {
					$type = 'server';
					$location_id = (int)$_GET['custom_pickup_id'];
				} else {
					list($type, $location_id) = explode('~::~', $_GET['location_id']);
				}
				if ($type == 'server') {
					$priceNotNullQuery = '';
					$date = pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']);
					if($date)
		            {
		                $dayIndex = date('N', strtotime($date));
		                $priceNotNullQuery = "WHERE TP.price_{$dayIndex} IS NOT NULL";
		            }
		
					$dropoff_arr = pjDropoffModel::factory()
						->select("t1.*, t2.content as location")
						->join('pjMultiLang', "t2.model='pjDropoff' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->where('t1.location_id', $location_id)
						//->where("t1.id IN (SELECT TP.dropoff_id FROM `".pjPriceModel::factory()->getTable()."` TP {$priceNotNullQuery})")
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
			    			$icon = 'fad fa-plane-departure';
			    		} elseif ($v['icon'] == 'train') {
			    			$icon = 'fad fa-subway';
			    		} else {
			    			$icon = 'fad fa-map-marker';
			    		}
			    		$dropoff_place_arr[$k]['icon'] = $icon;
			    		$dropoff_place_arr[$k]['text'] = $v['place_name'];
			    		$dropoff_place_arr[$k]['id_formated'] = 'server~::~'.$v['id'].'~::~'.$v['dropoff_id'];
					}
				}
			}
			$_SESSION[$this->defaultDropoffLocations] = $dropoff_place_arr;
			$this->set('dropoff_arr', $dropoff_arr);
			$this->set('dropoff_place_arr', $dropoff_place_arr);
		}
	}
	public function pjActionCheck()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$resp = array();
			if($this->checkDate($_POST, $this->option_arr) == true)
			{
				$pjFleetModel = pjFleetModel::factory();
				
				$data = array();
				$text = '';
				$front_check_transfer_msg = __('front_check_transfer_msg', true);
				
				list($pickup_type, $pickup_id) = explode('~::~', $_POST['location_id']);
				list($dropoff_type, $dropoff_place_id, $dropoff_id) = explode('~::~', $_POST['dropoff_id']);
				$_POST['pickup_type'] = $pickup_type;
				$_POST['pickup_id'] = $pickup_id;
                $_POST['dropoff_type'] = $dropoff_type;
                $_POST['dropoff_id'] = $dropoff_id;
                $_POST['dropoff_place_id'] = $dropoff_place_id;
                	
				if (($pickup_type == 'server' || (int)$_POST['custom_pickup_id'] > 0) && $dropoff_type == 'server') {
					$pjFleetModel->where('t2.dropoff_id', $dropoff_id);
				} elseif ($dropoff_type == 'google' && (int)$_POST['custom_dropoff_id'] > 0) {
					$pjFleetModel->where('t2.dropoff_id', (int)$_POST['custom_dropoff_id']);
				}
				$passengers_from_to = (int)$_POST['passengers_from_to'];
				$is_check_station_fee = $is_check_max_base_station_distance = false;
				$is_airport = 0;
				if ($pickup_type == 'server' || (int)$_POST['custom_pickup_id'] > 0) {
					if ((int)$_POST['custom_pickup_id'] > 0) {
						$pickup_place_arr = pjLocationModel::factory()->find((int)$_POST['custom_pickup_id'])->getData();
					} else {
						$pickup_place_arr = pjLocationModel::factory()->find($pickup_id)->getData();
					}
					$is_airport = $pickup_place_arr['is_airport'];
                	$_POST['pickup_lat'] = $pickup_place_arr['lat'];
                	$_POST['pickup_lng'] = $pickup_place_arr['lng'];
                	$_POST['pickup_is_airport'] = $pickup_place_arr['is_airport'];
                	if ((int)$_POST['custom_pickup_id'] > 0) {
                		$pickup_place_arr = $this->getGooglePlaceDetails($pickup_id, $this->option_arr);
                		if ($pickup_place_arr['status'] == 'OK') {
                			$_POST['custom_pickup_data'] = $pickup_place_arr['result'];
                			$_POST['pickup_lat'] = $pickup_place_arr['result']['geometry']['location']['lat'];
                			$_POST['pickup_lng'] = $pickup_place_arr['result']['geometry']['location']['lng'];
                		}
                	}
                	$is_check_station_fee = true;
				} else {
					$_POST['pickup_is_airport'] = 0;
					$pickup_place_arr = $this->getGooglePlaceDetails($pickup_id, $this->option_arr);
			    	if ($pickup_place_arr['status'] == 'OK') {
			    		$_POST['custom_pickup_data'] = $pickup_place_arr['result'];
			    		$_POST['pickup_lat'] = $pickup_place_arr['result']['geometry']['location']['lat'];
                		$_POST['pickup_lng'] = $pickup_place_arr['result']['geometry']['location']['lng'];
				    	if (isset($pickup_place_arr['result']['types']) && in_array('airport', $pickup_place_arr['result']['types'])) {
				    		$is_airport = 1;
				    		$_POST['pickup_is_airport'] = 1;
				    	}
				    	
			    		$is_check_station_fee = true;
			    		$is_check_max_base_station_distance = true;
			    	}
				}

				if ($dropoff_type == 'google') {
					$_POST['dropoff_is_airport'] = 0;
					$dropoff_place_arr = $this->getGooglePlaceDetails($dropoff_place_id, $this->option_arr);
			    	if ($dropoff_place_arr['status'] == 'OK') {
			    		$_POST['custom_dropoff_data'] = $dropoff_place_arr['result'];
			    		$data['dropoff_lat'] = $dropoff_place_arr['result']['geometry']['location']['lat'];
                		$data['dropoff_lng'] = $dropoff_place_arr['result']['geometry']['location']['lng'];
				    	if (isset($dropoff_place_arr['result']['types']) && in_array('airport', $dropoff_place_arr['result']['types'])) {
				    		$_POST['dropoff_is_airport'] = 1;
				    	}
			    	}
				} else {
					$dropoff_place_arr = pjAreaCoordModel::factory()->find($dropoff_place_id)->getData();
					$_POST['dropoff_is_airport'] = $dropoff_place_arr ? $dropoff_place_arr['is_airport'] : 0;
				}
				$data = array_merge($_POST, $data, array('is_airport' => $is_airport));
				if ($is_check_station_fee) {
					$station_arr = $this->getStationFee($data['pickup_lat'], $data['pickup_lng'], $data['dropoff_lat'], $data['dropoff_lng']);
					if ($is_check_max_base_station_distance && (float)$station_arr['station_distance'] > (float)$station_arr['max_base_station_distance']) {
						pjAppController::jsonResponse(array('code' => 102, 'text' => sprintf($front_check_transfer_msg[1], $station_arr['max_base_station_distance'].'km')));
					}
					$pjFleetModel->where('t1.station_id', (int)$station_arr['station_id']);
				}
				
                if($this->_is('search'))
                {
                    $oldSearch = $this->_get('search');
                    if(isset($oldSearch['is_airport']) && $oldSearch['is_airport'] != $is_airport)
                    {
                        $_SESSION[$this->defaultForm][$this->defaultIndex] = array();
                    }
                }
				
				$distance_arr = $this->calcDistanceBetweenTwoLocations($data['pickup_lat'], $data['pickup_lng'], $data['dropoff_lat'], $data['dropoff_lng'], $this->option_arr);
				if (isset($distance_arr['rows'][0]['elements'][0]['status']) && $distance_arr['rows'][0]['elements'][0]['status'] == 'OK') {
					$data['distance'] = $distance_arr['rows'][0]['elements'][0]['distance']['value'];
					$data['duration'] = $distance_arr['rows'][0]['elements'][0]['duration']['value'];
				}
				$this->_set('search', $data);
				$this->_set('is_return', $_POST['is_return']);
				$cnt_fleets = $pjFleetModel
					->join('pjPrice', "t1.id=t2.fleet_id", 'left')
					->where('t1.status', 'T')
                    ->where("({$passengers_from_to} BETWEEN t1.min_passengers AND t1.passengers)")					
					->findCount()
					->getData();
				if($cnt_fleets > 0)
				{
					$resp['code'] = 200;
				}else{
					$resp['code'] = 100;
				}
				$departure_arr = isset($_SESSION[$this->defaultForm][$this->defaultIndex]['departure']) ? $_SESSION[$this->defaultForm][$this->defaultIndex]['departure'] : array();
				if (isset($_POST['date']) && !empty($_POST['date'])) {
					$departure_arr['date_confirm'] = $_POST['date'];
				}
				$departure_arr['passengers'] = $_POST['passengers_from_to'];
				$_SESSION[$this->defaultForm][$this->defaultIndex]['departure'] = $departure_arr;
				
				if (isset($_POST['is_return']) && $_POST['is_return'] == 1) {
					$return_arr = isset($_SESSION[$this->defaultForm][$this->defaultIndex]['return']) ? $_SESSION[$this->defaultForm][$this->defaultIndex]['return'] : array();
					if (isset($_POST['return_date']) && !empty($_POST['return_date'])) {
						$return_arr['return_date'] = $_POST['return_date'];
					}
					$return_arr['passengers_return'] = $_POST['passengers_from_to'];
					$_SESSION[$this->defaultForm][$this->defaultIndex]['return'] = $return_arr;
				}
			}else{
				$resp['code'] = 101;
			}
			$resp['text'] = $text;
			pjAppController::jsonResponse($resp);
		}
	}
	public function pjActionCheckNew()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$resp = array();
			$data = array();
			$text = '';
			$front_check_transfer_msg = __('front_check_transfer_msg', true);
			if($this->checkDateNew($_POST, $this->option_arr) == true)
			{
				$pjFleetModel = pjFleetModel::factory();
				
				$passengers_from_to = (int)$_POST['search_passengers_from_to'];
				list($pickup_type, $pickup_id) = explode('~::~', $_POST['search_location_id']);
				list($dropoff_type, $dropoff_place_id, $dropoff_id) = explode('~::~', $_POST['search_dropoff_id']);
				
				$_POST['pickup_type'] = $pickup_type;
				$_POST['pickup_id'] = $pickup_id;
                $_POST['dropoff_type'] = $dropoff_type;
                $_POST['dropoff_id'] = $dropoff_id;
                $_POST['dropoff_place_id'] = $dropoff_place_id;
                
				if (($pickup_type == 'server' || (int)$_POST['custom_pickup_id'] > 0) && $dropoff_type == 'server') {
					$pjFleetModel->where('t2.dropoff_id', $dropoff_id);
				} elseif ($dropoff_type == 'google' && (int)$_POST['custom_dropoff_id'] > 0) {
					$pjFleetModel->where('t2.dropoff_id', (int)$_POST['custom_dropoff_id']);
				}
				$is_check_station_fee = $is_check_max_base_station_distance = false;
				$is_airport = 0;
				if ($pickup_type == 'server' || (int)$_POST['custom_pickup_id'] > 0) {
					if ((int)$_POST['custom_pickup_id'] > 0) {
						$pickup_place_arr = pjLocationModel::factory()->find((int)$_POST['custom_pickup_id'])->getData();
					} else {
						$pickup_place_arr = pjLocationModel::factory()->find($pickup_id)->getData();
					}
					$is_airport = $pickup_place_arr['is_airport'];
                	$_POST['pickup_lat'] = $pickup_place_arr['lat'];
                	$_POST['pickup_lng'] = $pickup_place_arr['lng'];
                	$_POST['pickup_is_airport'] = $pickup_place_arr['is_airport'];
                	if ((int)$_POST['custom_pickup_id'] > 0) {
                		$pickup_place_arr = $this->getGooglePlaceDetails($pickup_id, $this->option_arr);
	                	if ($pickup_place_arr['status'] == 'OK') {
				    		$_POST['custom_pickup_data'] = $pickup_place_arr['result'];
				    		$_POST['pickup_lat'] = $pickup_place_arr['result']['geometry']['location']['lat'];
	                		$_POST['pickup_lng'] = $pickup_place_arr['result']['geometry']['location']['lng'];
				    	}
                	}
                	$is_check_station_fee = true;
				} else {
					$_POST['pickup_is_airport'] = 0;
					$pickup_place_arr = $this->getGooglePlaceDetails($pickup_id, $this->option_arr);
			    	if ($pickup_place_arr['status'] == 'OK') {
			    		$_POST['custom_pickup_data'] = $pickup_place_arr['result'];
			    		$_POST['pickup_lat'] = $pickup_place_arr['result']['geometry']['location']['lat'];
                		$_POST['pickup_lng'] = $pickup_place_arr['result']['geometry']['location']['lng'];
				    	if (isset($pickup_place_arr['result']['types']) && in_array('airport', $pickup_place_arr['result']['types'])) {
				    		$is_airport = 1;
				    		$_POST['pickup_is_airport'] = 1;
				    	}
				    	$is_check_station_fee = true;
				    	$is_check_max_base_station_distance = true;
			    	}
				}

				if ($dropoff_type == 'google') {
					$_POST['dropoff_is_airport'] = 0;
					$dropoff_place_arr = $this->getGooglePlaceDetails($dropoff_place_id, $this->option_arr);
			    	if ($dropoff_place_arr['status'] == 'OK') {
			    		$_POST['custom_dropoff_data'] = $dropoff_place_arr['result'];
			    		$data['dropoff_lat'] = $dropoff_place_arr['result']['geometry']['location']['lat'];
                		$data['dropoff_lng'] = $dropoff_place_arr['result']['geometry']['location']['lng'];
				    	if (isset($dropoff_place_arr['result']['types']) && in_array('airport', $dropoff_place_arr['result']['types'])) {
				    		$_POST['dropoff_is_airport'] = 1;
				    	}
			    	}
				} else {
					$dropoff_place_arr = pjAreaCoordModel::factory()->find($dropoff_place_id)->getData();
					$_POST['dropoff_is_airport'] = $dropoff_place_arr ? $dropoff_place_arr['is_airport'] : 0;
				}
				$data = array_merge($_POST, $data, array('is_airport' => $is_airport));
				if ($is_check_station_fee) {
					$station_arr = $this->getStationFee($data['pickup_lat'], $data['pickup_lng'], $data['dropoff_lat'], $data['dropoff_lng']);
					if ($is_check_max_base_station_distance && (float)$station_arr['station_distance'] > (float)$station_arr['max_base_station_distance']) {
						pjAppController::jsonResponse(array('code' => 102, 'text' => sprintf($front_check_transfer_msg[1], $station_arr['max_base_station_distance'].'km')));
					}
					$pjFleetModel->where('t1.station_id', (int)$station_arr['station_id']);
				}

                if($this->_is('search'))
                {
                    $oldSearch = $this->_get('search');
                    if(isset($oldSearch['is_airport']) && $oldSearch['is_airport'] != $is_airport)
                    {
                        $_SESSION[$this->defaultForm][$this->defaultIndex] = array();
                    }
                }

				$distance_arr = $this->calcDistanceBetweenTwoLocations($data['pickup_lat'], $data['pickup_lng'], $data['dropoff_lat'], $data['dropoff_lng'], $this->option_arr);
				if (isset($distance_arr['rows'][0]['elements'][0]['status']) && $distance_arr['rows'][0]['elements'][0]['status'] == 'OK') {
					$data['distance'] = $distance_arr['rows'][0]['elements'][0]['distance']['value'];
					$data['duration'] = $distance_arr['rows'][0]['elements'][0]['duration']['value'];
				}
				
				$this->_set('search', $data);
				$cnt_fleets = $pjFleetModel
					->join('pjPrice', "t1.id=t2.fleet_id", 'left')
					->where('t1.status', 'T')
                    ->where("({$passengers_from_to} BETWEEN t1.min_passengers AND t1.passengers)")
					->findCount()
					->getData();
				if($cnt_fleets > 0)
				{
					$resp['code'] = 200;
					$resp['params'] = array(
						'autoload_next_step' => $_POST['autoload_next_step'],
						'skip_first_step' => $_POST['skip_first_step'],
						'search_pickup_id' => $_POST['custom_pickup_id'],
						'search_location_id' => $_POST['search_location_id'],
						'search_dropoff_id' => $_POST['search_dropoff_id'],
						'search_passengers_from_to' => $_POST['search_passengers_from_to'],
						'search_date' => $_POST['search_date'],
						'search_is_return' => $_POST['is_return'],
						'search_return_date' => $_POST['search_return_date']
					);
				}else{
					$resp['code'] = 100;
				}
			}else{
				$resp['code'] = 101;
			}
			$resp['text'] = $text;
			pjAppController::jsonResponse($resp);
		}
	}
	public function pjActionAddFleet()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if(isset($_GET['fleet_id']) && (int) $_GET['fleet_id'] > 0)
			{
				$this->_set('fleet_id', $_GET['fleet_id']);
			}
            $this->updateCart();
			$resp = array();
			$resp['code'] = 200;
			pjAppController::jsonResponse($resp);
		}
	}
    public function pjActionSetTransferType()
    {
        $this->setAjax(true);

        if ($this->isXHR())
        {
            if(isset($_GET['is_return']) && (int) $_GET['is_return'] == 1)
            {
                $this->_set('is_return', 1);
            }
            else
            {
                $this->_set('is_return', 0);
                unset($_SESSION[$this->defaultForm][$this->defaultIndex]['return']);
            }

            $this->updateCart();
            $resp = array();
            $resp['code'] = 200;
            pjAppController::jsonResponse($resp);
        }
    }
    public function pjActionUpdateExtras()
    {
        $this->setAjax(true);

        if ($this->isXHR())
        {
            if ($_GET['type'] == 'pickup') {
            	$this->updateExtras($_POST['extras'], 'pickup');
            	if (isset($_POST['has_return']) && $_POST['has_return'] == 1) {
            		$this->updateExtras($_POST['extras'], 'return');
            	} else {
            		$this->_set('extras_return', array());
            	}
            } else {
            	$this->updateExtras($_POST['extras_return'], 'return');
            }
            
            $cart = $this->_get('cart');

            $resp = array('code' => 200, 'extras' => $cart['extras'], 'extras_return' => @$cart['extras_return']);
            pjAppController::jsonResponse($resp);
        }
    }
	public function pjActionCheckLogin()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$resp = array();
			if (!isset($_SESSION[$this->defaultForm][$this->defaultIndex]) || count($_SESSION[$this->defaultForm][$this->defaultIndex]) === 0)
			{
				$_SESSION[$this->defaultForm][$this->defaultIndex] = array();
			}
			if(isset($_POST['step_checkout']))
			{
				if($_POST['client_type'] == 'existing')
				{
					$pjClientModel = pjClientModel::factory();
	
					$client = $pjClientModel
						->where('t1.email', $_POST['login_email'])
						->where(sprintf("t1.password = AES_ENCRYPT('%s', '%s')", pjObject::escapeString($_POST['login_password']), PJ_SALT))
						->limit(1)
						->findAll()
						->getData();
						
					if (count($client) != 1)
					{
						$client = $pjClientModel
						->reset()
						->where('t1.email', $_POST['login_email'])
						->limit(1)
						->findAll()
						->getData();
						if (count($client) != 1)
						{
							$resp['code'] = 100;
							$resp['text'] = __('front_email_does_not_exist', true);
						}else{
							$resp['code'] = 102;
							$resp['text'] = __('front_incorrect_password', true);
						}
					}else{
						if ($client[0]['status'] != 'T')
						{
							$resp['code'] = 101;
							$resp['text'] = __('front_your_account_disabled', true);
						}else{
							$last_login = date("Y-m-d H:i:s");
							
							$client = $pjClientModel->reset()->find($client[0]['id'])->getData();
							$_SESSION[$this->defaultClient] = $client;
								
							$_SESSION[$this->defaultForm][$this->defaultIndex]['c_email'] = $client['email'];
							$_SESSION[$this->defaultForm][$this->defaultIndex]['c_password'] = $client['password'];
							$_SESSION[$this->defaultForm][$this->defaultIndex]['c_fname'] = $client['fname'];
							$_SESSION[$this->defaultForm][$this->defaultIndex]['c_lname'] = $client['lname'];
							$_SESSION[$this->defaultForm][$this->defaultIndex]['c_phone'] = $client['phone'];
							
							$data = array();
							$data['last_login'] = $last_login;
							$pjClientModel->reset()->setAttributes(array('id' => $client[0]['id']))->modify($data);
							$resp['code'] = 200;
						}
					}
				}else{
					$resp['code'] = 200;
				}
			}
				
			pjAppController::jsonResponse($resp);
		}
	}
	public function pjActionSaveForm()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$resp = array();
			if (!isset($_SESSION[$this->defaultForm][$this->defaultIndex]) || count($_SESSION[$this->defaultForm][$this->defaultIndex]) === 0)
			{
				$_SESSION[$this->defaultForm][$this->defaultIndex] = array();
			}
			if ((int) $this->option_arr['o_bf_include_captcha'] === 3 && (!isset($_POST['captcha']) ||
					!pjCaptcha::validate($_POST['captcha'], $_SESSION[$this->defaultCaptcha]) ))
			{
				pjAppController::jsonResponse(array('code' => '101', 'text' => pjSanitize::clean(__('front_incorrect_captcha', true, false))));
			}
			if(isset($_POST['step_checkout']))
			{
				$_SESSION[$this->defaultForm][$this->defaultIndex] = $_POST;
				$resp['code'] = 200;
			}
			
			pjAppController::jsonResponse($resp);
		}
	}
	
	public function pjActionSaveBooking()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{		
			$search_post = $this->_get('search');
			$STORE = @$_SESSION[$this->defaultStore][$this->defaultIndex];
			$departureData = @$_SESSION[$this->defaultForm][$this->defaultIndex]['departure'];
			$returnData = @$_SESSION[$this->defaultForm][$this->defaultIndex]['return'];
			
			if (!$search_post || !$STORE || !isset($_SESSION[$this->defaultForm])) {
			    pjAppController::jsonResponse(array('code' => 102, 'text' => ''));
			}
			
            $passengerData = $_POST;
			$pjBookingModel = pjBookingModel::factory();
			
			$data = array();
			$uuid = pjAppController::createRandomBookingId();
			$data['uuid'] = $uuid;
			$data['ip'] = pjUtil::getClientIp();
			$data['status'] = $this->option_arr['o_booking_status'];
            $data['locale_id'] = $this->getLocaleId();
			$dropoff_id = ($search_post['dropoff_type'] == 'google' && (int)$search_post['custom_dropoff_id'] > 0) ? (int)$search_post['custom_dropoff_id'] : (int)$search_post['dropoff_id'];
            // STEP 1
            $data['location_id'] = $search_post['pickup_id'];
            $data['pickup_type'] = $search_post['pickup_type'];
            $data['pickup_id'] = $search_post['custom_pickup_id'];
            $data['dropoff_id'] = $dropoff_id;
            $data['dropoff_place_id'] = $search_post['dropoff_place_id'];
            $data['dropoff_type'] = $search_post['dropoff_type'];
            $data['fleet_id'] = $STORE['fleet_id'];
            $data['duration'] = round($search_post['duration']/60);
            $data['distance'] = round($search_post['distance']/1000);
			$_date = pjUtil::formatDate($search_post['date'], $this->option_arr['o_date_format']);
			
            // STEP 3 -- DEPARTURE INFORMATION
            $data['passengers'] = @$departureData['passengers'];
            $data['c_hotel'] = @$departureData['c_hotel'];
            $data['c_notes'] = @$departureData['c_notes'];
            $time = '';
            if (isset($departureData['pickup_time'])) {
            	$time = date('H:i:s', strtotime($departureData['pickup_time']));
            } elseif (isset($departureData['arrival_time'])) {
            	$time = date('H:i:s', strtotime($departureData['arrival_time']));
            }
            $data['booking_date'] = $_date . ' ' . $time;
            
            $region = $dropoff_region = '';
            if ($search_post['pickup_type'] == 'google') {
            	$data['pickup_address'] = strip_tags($search_post['custom_pickup_data']['adr_address']);
            } else {
            	$data['pickup_address'] = ':NULL';
            	$pickup_arr = pjLocationModel::factory()->find($search_post['pickup_id'])->getData();
            	$region = $pickup_arr['region'];
            }
            if ($search_post['dropoff_type'] == 'server') {
                $dropoff_arr = pjDropoffModel::factory()->find($dropoff_id)->getData();
                $dropoff_region = $dropoff_arr['region'];
            }
            
            $data['region'] = $region;
            $data['dropoff_region'] = $dropoff_region;
            
            $data['pickup_lat'] = $search_post['pickup_lat'];
            $data['pickup_lng'] = $search_post['pickup_lng'];
            $data['pickup_is_airport'] = $search_post['pickup_is_airport'];
            $dropoff_is_airport = isset($search_post['dropoff_is_airport']) ? $search_post['dropoff_is_airport'] : 0;
            if ($search_post['dropoff_type'] == 'server') {
            	$data['dropoff_address'] = ':NULL';
            } else {
            	$data['dropoff_address'] = strip_tags($search_post['custom_dropoff_data']['adr_address']);
            }
            $data['dropoff_lat'] = $search_post['dropoff_lat'];
            $data['dropoff_lng'] = $search_post['dropoff_lng'];
            $data['dropoff_is_airport'] = $search_post['dropoff_is_airport'];
            
            if (!$search_post['is_airport'] && $dropoff_is_airport == 0) {
            	$data['c_address'] = @$departureData['c_address'];
            	$data['c_destination_address'] = @$departureData['c_destination_address'];
            } else {
				if($search_post['is_airport'])
	            {
	                $data['c_flight_time'] = $time;
	                $data['c_flight_number'] = @$departureData['c_flight_number'];
	                $data['c_airline_company'] = @$departureData['c_airline_company'];
	                $data['c_destination_address'] = @$departureData['c_destination_address'];
	            }
	            else
	            {
	                $data['c_departure_flight_time'] = date('H:i:s', strtotime($departureData['c_departure_flight_time']));
	                $data['c_address'] = @$departureData['c_address'];
	            }
            }
            // STEP 4 -- RETURN INFORMATION
			$data['return_id'] = ':NULL';
			$_return_time = '';
			if($STORE['is_return'])
			{
				$_return_date = pjUtil::formatDate($returnData['return_date'], $this->option_arr['o_date_format']);
                if (isset($returnData['return_pickup_time'])) {
                	$_return_time = date('H:i:s', strtotime($returnData['return_pickup_time']));
                } elseif (isset($returnData['return_time'])) {
                	$_return_time = date('H:i:s', strtotime($returnData['return_time']));
                }
				$data['return_date'] = $_return_date . ' ' . $_return_time;
			}

            // STEP 5 -- PASSENGER DETAILS
            $c_data = array();
            $c_data['title'] = $data['c_title'] = isset($passengerData['title']) ? $passengerData['title'] : ':NULL';
            $c_data['fname'] = $data['c_fname'] = isset($passengerData['fname']) ? $passengerData['fname'] : ':NULL';
            $c_data['lname'] = $data['c_lname'] = isset($passengerData['lname']) ? $passengerData['lname'] : ':NULL';
            $c_data['email'] = $data['c_email'] = isset($passengerData['email']) ? $passengerData['email'] : ':NULL';
            $c_data['country_id'] = $data['c_country'] = isset($passengerData['country_id']) ? $passengerData['country_id'] : ':NULL';
            $c_data['dialing_code'] = $data['c_dialing_code'] = isset($passengerData['dialing_code']) ? $passengerData['dialing_code'] : ':NULL';
            $c_data['phone'] = $data['c_phone'] = isset($passengerData['phone']) ? $passengerData['phone'] : ':NULL';
            $c_data['password'] = pjUtil::getRandomPassword(6);
            $c_data['status'] = 'T';
            $client_id = pjClientModel::factory()->setAttributes($c_data)->insert()->getInsertId();
            if ($client_id !== false && (int) $client_id > 0)
            {
                $data['client_id'] = $client_id;
            }

            // STEP 6 -- PAYMENT DETAILS
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
            if ($payment_method == 'saferpay') {
                $data['status'] = 'in_progress';
            }
            // Prices
            $dayIndex = date('N', strtotime($_date));
            $fleet = pjFleetModel::factory()
				->select("
					t1.*,
					t2.price_{$dayIndex} as price
				")
                ->join('pjPrice', "t1.id=t2.fleet_id AND t2.dropoff_id = " . (int)$data['dropoff_id'], 'left')
                ->find($data['fleet_id'])
                ->getData();
            $price_calculated_by_distance = false;
			if (($search_post['pickup_type'] == 'google' && (int)$search_post['custom_pickup_id'] <= 0) || ($search_post['dropoff_type'] == 'google' && (int)$search_post['custom_dropoff_id'] <= 0)) {            
				$params = array(
					'pickup_lat' => $search_post['pickup_lat'],
					'pickup_lng' => $search_post['pickup_lng'],
					'dropoff_lat' => $search_post['dropoff_lat'],
					'dropoff_lng' => $search_post['dropoff_lng'],
					'distance' => $search_post['distance'],
					'vehicle_arr' => $fleet
				);
				$price_arr = $this->getPricesBasedOnDistance($params, $this->option_arr);
				$one_way_price = $price_arr['rental_price'];
				$data['station_fee'] = $price_arr['station_fee'];
            	$data['station_id'] = $price_arr['station_id'];
            	$price_calculated_by_distance = true;
			} else {
            	$one_way_price = $fleet['price'];
			}
			$price_level = isset($STORE['price_level']) ? $STORE['price_level'] : 0;
			$fleet_discount_arr = $this->getFleetDiscount($_date, $data['fleet_id'], $price_level);

			if ($fleet_discount_arr) {
				if ($fleet_discount_arr['is_subtract'] == 'T') {
					if ($fleet_discount_arr['type'] == 'amount') {
						$one_way_price = $one_way_price - $fleet_discount_arr['discount'];
					} else {
						$one_way_price = $one_way_price - (($one_way_price * $fleet_discount_arr['discount']) / 100);
					}
				} else {
				    if (!$price_calculated_by_distance) { 
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
			
			if ($price_calculated_by_distance == 1 && $price_level == 2) {
			    $price_level2_arr = $this->getPriceLevel2ByDistance($_date, $data['fleet_id'], $data['distance']);
			    $one_way_price = $one_way_price + ((float)$price_level2_arr['price'] * $data['distance']);
			}
			
            $return_price = $one_way_price;
			$extra_price = $return_extra_price = 0;
	        if($this->_get('extras'))
	        {
	            $_extras = $this->_get('extras');
	            if(!empty($_extras))
	            {
	            	$extra_arr = pjExtraModel::factory()->whereIn('t1.id', array_keys($_extras))->findAll()->getData();
	                foreach($extra_arr as $ex)
	                {
	                    if ((float)$ex['price'] > 0) {
	                    	$extra_price += $_extras[$ex['id']] * $ex['price'];
	                    }
	                }
	            }
	        }
			if($this->_get('extras_return') && $STORE['is_return'])
	        {
	            $_extras = $this->_get('extras_return');
	            if(!empty($_extras))
	            {
	            	$extra_arr = pjExtraModel::factory()->reset()->whereIn('t1.id', array_keys($_extras))->findAll()->getData();
	                foreach($extra_arr as $ex)
	                {
	                    if ((float)$ex['price'] > 0) {
	                    	$return_extra_price += $_extras[$ex['id']] * $ex['price'];
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
	        $price_arr = pjUtil::calPrice($one_way_price, $return_price, $total_extra_price, $STORE['is_return'], $return_discount, $this->option_arr, $payment_method, $data['voucher_code']);
            $data['sub_total'] = $price_arr['sub_total'];
            $data['discount'] = $price_arr['discount'];
            $data['tax'] = $price_arr['tax'];
            $data['total'] = $price_arr['total'];
            $data['credit_card_fee'] = $price_arr['credit_card_fee'];
            if (in_array($payment_method, array('creditcard_later', 'cash'))) {
                $data['deposit'] = ':NULL';
            } else {
                $data['deposit'] = $price_arr['deposit'];
            }
            $data['extra_price'] = $extra_price;
			if($STORE['is_return']) {
				$data['price'] = $price_arr['total']/2;
			} else {
				$data['price'] = $price_arr['total'];
			}
			$data['price_by_distance'] = $price_calculated_by_distance ? 'T' : 'F';
			//$data['pickup_google_map_link'] = 'http://www.google.com/maps/place/'.$search_post['pickup_lat'].','.$search_post['pickup_lng'];
			//$data['dropoff_google_map_link'] = 'http://www.google.com/maps/place/'.$search_post['dropoff_lat'].','.$search_post['dropoff_lng'];
			$saferpay_cature_id = '';
			if ($this->defaultPaySafePaymentMethod == 'direct' && $data['payment_method'] == 'saferpay') {
				$url = PJ_TEST_MODE ? 'https://test.saferpay.com/api/Payment/v1/Transaction/AuthorizeDirect' : 'https://www.saferpay.com/api/Payment/v1/Transaction/AuthorizeDirect';
				list($exp_month, $exp_year) = explode('/', $_POST['cc_exp']);
			    $payload = array(
			        'RequestHeader' => array(
			            'SpecVersion' => "1.16",
			            'CustomerId' => $this->option_arr['o_saferpay_customer_id'],
			            'RequestId' => md5($uuid . PJ_SALT),
			            'RetryIndicator' => 0
			        ),
			        'TerminalId' => $this->option_arr['o_saferpay_terminal_id'],
			        'Payment' => array(
			            'Amount' => array(
			                'Value' => $data['deposit'] * 100,
			                'CurrencyCode' => $this->option_arr['o_currency']
			            ),
			            'OrderId' => $uuid,
			            'Description' => __('front_transfer_reservation', true, false)
			        ),
			        'PaymentMeans' => array(
			        	'Card' => array(
			        		"Number" => $_POST['cc_num'],
					      	"ExpYear" => $exp_year,
					      	"ExpMonth" => $exp_month,
					      	"HolderName" => $_POST['cc_owner'],
					      	"VerificationCode" => $_POST['cc_code']							    
			        	)
			        ),
			        'RegisterAlias' => array(
			        	'IdGenerator' => 'RANDOM'
			        )
			    );
			    
			    $curl = curl_init($url);
			    curl_setopt($curl, CURLOPT_HEADER, false);
			    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json","Accept: application/json; charset=utf-8"));
			    curl_setopt($curl, CURLOPT_POST, true);
			    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
			    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
			    curl_setopt($curl, CURLOPT_USERPWD, $this->option_arr['o_saferpay_username'] . ":" . $this->option_arr['o_saferpay_password']);
			    $jsonResponse = curl_exec($curl);
			    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			    if ($status != 200) {
			        $body = json_decode(curl_multi_getcontent($curl), true);
			        curl_close($curl);
			    	if (isset($body['ErrorDetail'])) {
			        	$json = array('code' => 101, 'text' => implode('<br/>', $body['ErrorDetail']));
			        } else {
			        	$json = array('code' => 101, 'text' => $body['ErrorMessage']);
			        }
			        pjAppController::jsonResponse($json);
			    }else {
			        $body = json_decode($jsonResponse, true);
			        curl_close($curl);
			        $transaction_id = $body['Transaction']['Id'];
			        $url = PJ_TEST_MODE ? 'https://test.saferpay.com/api/Payment/v1/Transaction/Capture' : 'https://www.saferpay.com/api/Payment/v1/Transaction/Capture';
				    $payload = array(
				        'RequestHeader' => array(
				            'SpecVersion' => "1.16",
				            'CustomerId' => $this->option_arr['o_saferpay_customer_id'],
				            'RequestId' => md5($uuid . PJ_SALT),
				            'RetryIndicator' => 0
				        ),
				        'TransactionReference' => array(
				        	'TransactionId' => $transaction_id
				        )
				    );
				    
				    $curl = curl_init($url);
				    curl_setopt($curl, CURLOPT_HEADER, false);
				    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				    curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json","Accept: application/json; charset=utf-8"));
				    curl_setopt($curl, CURLOPT_POST, true);
				    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
				    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
				    curl_setopt($curl, CURLOPT_USERPWD, $this->option_arr['o_saferpay_username'] . ":" . $this->option_arr['o_saferpay_password']);
				    $jsonResponse = curl_exec($curl);
				    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				    if ($status != 200) {
				        $body = json_decode(curl_multi_getcontent($curl), true);
				        curl_close($curl);
					    if (isset($body['ErrorDetail'])) {
				        	$json = array('code' => 101, 'text' => implode('<br/>', $body['ErrorDetail']));
				        } else {
				        	$json = array('code' => 101, 'text' => $body['ErrorMessage']);
				        }
		        		pjAppController::jsonResponse($json);
				    }else {
				        $body = json_decode($jsonResponse, true);
				        curl_close($curl);
				        if ($body['Status'] == 'CAPTURED') {
					        $data['txn_id'] = $body['CaptureId'];
				        	$data['status'] = $this->option_arr['o_payment_status'];
				        	$saferpay_cature_id = $body['CaptureId'];
				        } else {
				        	$json = array('code' => 101, 'text' => __('front_messages_ARRAY_9', true));
			        		pjAppController::jsonResponse($json);
				        }
				    }
			    }
			}
            // Save booking
			$id = $pjBookingModel->setAttributes($data)->insert()->getInsertId();
			if ($id !== false && (int) $id > 0)
			{
			    $data_history = array(
			        'booking_id' => $id,
			        'action' => 'Booking created'
			    );
			    pjBookingHistoryModel::factory()->setAttributes($data_history)->insert();
			    
				$arr = $pjBookingModel->reset()
					->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
					->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
					->find($id)
					->getData();

                $return_id = null;
				if($STORE['is_return'])
				{
				    $return_uuid = pjAppController::createRandomBookingId();
                    $data['return_id'] = $id;
                    $data['booking_date'] = $data['return_date'];
                    $data['return_date'] = ':NULL';
                    $data['uuid'] = $return_uuid;
                    $data['created'] = $arr['created'];
                    $data['c_notes'] = $returnData['return_c_notes'];
                    $data['c_flight_time'] = ':NULL';
                    $data['c_flight_number'] = ':NULL';
                    $data['c_airline_company'] = ':NULL';
                    $data['c_destination_address'] = ':NULL';
                    $data['c_departure_flight_time'] = ':NULL';
                    $data['c_address'] = ':NULL';
                    $data['c_hotel'] = ':NULL';
                    $data['passengers'] = $returnData['passengers_return'];
					$data['price'] = $price_arr['total']/2;
					$data['extra_price'] = $return_extra_price;
                    if (!$search_post['is_airport'] && $dropoff_is_airport == 0) {
                     	$data['c_address'] = $returnData['return_c_address'];
                     	$data['c_destination_address'] = $returnData['return_c_destination_address'];
                    } else {
						if($search_post['is_airport'])
	                    {
	                        $data['c_departure_flight_time'] = date('H:i:s', strtotime($returnData['return_c_departure_flight_time']));
	                        $data['c_address'] = $returnData['return_c_address'];
	                    }
	                    else
	                    {
	                        $data['c_flight_time'] = $_return_time;
	                        $data['c_flight_number'] = $returnData['return_c_flight_number'];
	                        $data['c_airline_company'] = $returnData['return_c_airline_company'];
	                    }
                    }
                    $data['pickup_is_airport'] = $search_post['dropoff_is_airport'];
					$data['dropoff_is_airport'] = $search_post['pickup_is_airport'];
					$data['region'] = $dropoff_region;
					$data['dropoff_region'] = $region;
					
					$data['pickup_lat'] = $search_post['dropoff_lat'];
					$data['pickup_lng'] = $search_post['dropoff_lng'];
					
					$data['dropoff_lat'] = $search_post['pickup_lat'];
					$data['dropoff_lng'] = $search_post['pickup_lng'];
					
					$return_id = $pjBookingModel->reset()->setAttributes($data)->insert()->getInsertId();
				}

                // STEP 2 -- BAGGAGE AND EXTRAS
                pjBookingExtraModel::factory()->saveExtras($this->_get('extras'), $this->_get('extras_return'), $id, $return_id);

				pjBookingPaymentModel::factory()
                    ->setAttributes(array(
                        'booking_id'     => $arr['id'],
                        'payment_method' => $payment_method,
                        'payment_type'   => 'online',
                        'amount'         => $arr['deposit'],
                        'status'         => 'notpaid',
                    ))
                    ->insert();

                //$invoice_arr = $this->pjActionGenerateInvoice($id);
                    
				$bookingDate = new DateTime($arr['booking_date']);
				$arrivalNotice = pjArrivalNoticeModel::factory()
					->reset()
					->where('t1.date_from <=', $bookingDate->format('Y-m-d'))
					->where('t1.date_to >=', $bookingDate->format('Y-m-d'))
					->findCount()
					->getData();

                $now = date('Y-m-d H:i:s');
				$diff = strtotime($arr['booking_date']) - strtotime($now);
				$hours = $diff / (60 * 60);
				if ($arr['payment_method'] != 'saferpay' || ($arr['payment_method'] == 'saferpay' && $this->defaultPaySafePaymentMethod == 'direct')) {
					if ($hours < 24 || $arrivalNotice > 0 || $price_calculated_by_distance) {
						pjAppController::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'arrival', $this->getLocaleId());
					} else {
						pjAppController::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'confirm', $this->getLocaleId());
					}
				}
				
				//If the customer pays online during the booking process, they should not receive a payment confirmation — only when we send them a payment link.
				/* if ($arr['payment_method'] == 'saferpay' && !empty($saferpay_cature_id)) {
				    pjAppController::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'payment', $this->getLocaleId());
				} */
				
				if ($arr['status'] == 'confirmed') {
					$resp = pjApiSync::syncBooking($id, 'create', $this->option_arr);
					if (isset($return_id) && (int)$return_id > 0) {
						$resp = pjApiSync::syncBooking($return_id, 'create', $this->option_arr);
					}
				}
				unset($_SESSION[$this->defaultStore][$this->defaultIndex]);
				unset($_SESSION[$this->defaultForm][$this->defaultIndex]);
				unset($_SESSION[$this->defaultVoucher][$this->defaultIndex]);
				unset($_SESSION[$this->defaultClient]);
				
				$json = array('code' => 200, 'text' => '', 'booking_id' => $id, 'booking_uuid' => $arr['uuid'], 'payment_method' => $arr['payment_method']);
			}else{
				if ($this->defaultPaySafePaymentMethod == 'direct' && $data['payment_method'] == 'saferpay' && !empty($saferpay_cature_id)) {
					$url = PJ_TEST_MODE ? 'https://test.saferpay.com/api/Payment/v1/Transaction/Refund' : 'https://www.saferpay.com/api/Payment/v1/Transaction/Refund';
				    $payload = array(
				        'RequestHeader' => array(
				            'SpecVersion' => "1.16",
				            'CustomerId' => $this->option_arr['o_saferpay_customer_id'],
				            'RequestId' => md5($uuid . PJ_SALT),
				            'RetryIndicator' => 0
				        ),
				        'Refund' => array(
				            'Amount' => array(
				                'Value' => $data['deposit'] * 100,
				                'CurrencyCode' => $this->option_arr['o_currency']
				            )
				        ),
				        'CaptureReference' => array(
				        	'CaptureId' => $saferpay_cature_id
				        )
				    );
				    
				    $curl = curl_init($url);
				    curl_setopt($curl, CURLOPT_HEADER, false);
				    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				    curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json","Accept: application/json; charset=utf-8"));
				    curl_setopt($curl, CURLOPT_POST, true);
				    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
				    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
				    curl_setopt($curl, CURLOPT_USERPWD, $this->option_arr['o_saferpay_username'] . ":" . $this->option_arr['o_saferpay_password']);
				    $jsonResponse = curl_exec($curl);
				    curl_close($curl);
				}
				$json = array('code' => 100, 'text' => '');
			}
			pjAppController::jsonResponse($json);
		}
	}
	
	public function pjActionGetPaymentForm()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$arr = pjBookingModel::factory()
				->select('t1.*')
				->find($_GET['booking_id'])->getData();
			
			switch ($arr['payment_method'])
			{
				case 'paypal':
					$this->set('params', array(
						'name' => 'trPaypal',
						'id' => 'trPaypal',
						'business' => $this->option_arr['o_paypal_address'],
						'item_name' => __('front_transfer_reservation', true, false),
						'custom' => $arr['id'],
						'amount' => number_format($arr['deposit'], 2, '.', ''),
						'currency_code' => $this->option_arr['o_currency'],
						'return' => $this->option_arr['o_thankyou_page'],
						'notify_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmPaypal',
						'target' => '_self'
					));
					break;
				case 'authorize':
					$this->set('params', array(
						'name' => 'trAuthorize',
						'id' => 'trAuthorize',
						'target' => '_self',
						'timezone' => $this->option_arr['o_timezone'],
						'transkey' => $this->option_arr['o_authorize_transkey'],
						'x_login' => $this->option_arr['o_authorize_merchant_id'],
						'x_description' => __('front_transfer_reservation', true, false),
						'x_amount' => number_format($arr['deposit'], 2, '.', ''),
						'x_invoice_num' => $arr['id'],
						'x_receipt_link_url' => $this->option_arr['o_thankyou_page'],
						'x_relay_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmAuthorize'
					));
					break;
				case 'saferpay':
				    $locale_arr = pjLocaleModel::factory()->find($arr['locale_id'])->getData();
				    list($iso, ) = explode('-', $locale_arr['language_iso']);
	                $url = PJ_TEST_MODE ? 'https://test.saferpay.com/api/Payment/v1/PaymentPage/Initialize' : 'https://www.saferpay.com/api/Payment/v1/PaymentPage/Initialize';
				    $payload = array(
				        'RequestHeader' => array(
				            'SpecVersion' => "1.10",
				            'CustomerId' => $this->option_arr['o_saferpay_customer_id'],
				            'RequestId' => md5($arr['uuid'] . PJ_SALT),
				            'RetryIndicator' => 0,
				            'ClientInfo' => array(
				                'ShopInfo' => "My Shop",
				                'OsInfo' => "Windows Server 2013"
				            )
				        ),
				        'TerminalId' => $this->option_arr['o_saferpay_terminal_id'],
				        'Payment' => array(
				            'Amount' => array(
				                'Value' => number_format($arr['deposit'], 2, '.', '') * 100,
				                'CurrencyCode' => $this->option_arr['o_currency']
				            ),
				            'OrderId' => $arr['uuid'],
				            'Description' => __('front_transfer_reservation', true, false)
				        ),
				        'Payer' => array(
				            //'IpAddress' => pjUtil::getClientIp(),
				            'IpAddress' => "192.168.178.1",
				            'LanguageCode' => $iso
				        ),
				        'ReturnUrls' => array(
				            'Success' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmPaySafe&locale='.$arr['locale_id'].'&uuid=' . $arr['uuid'],
				            'Fail' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionPaySafeReturn&locale='.$arr['locale_id'].'&type=fail&uuid='.$arr['uuid'],
				        	'Abort' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionPaySafeReturn&locale='.$arr['locale_id'].'&type=abort&uuid='.$arr['uuid']
				        ),
				        'Notification' => array(
				            'NotifyUrl' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionPaySafeReturn&locale='.$arr['locale_id'].'&type=notify&uuid='.$arr['uuid']
				        )
				    );
				    $paysafe_data = $this->doPaySafeURL($this->option_arr, $payload, $url, 'initialize', $arr['uuid']);
				    $this->set('paysafe_data', $paysafe_data);
					break;
			}
			
			$this->set('arr', $arr);
			$this->set('get', $_GET);
		}
	}
	
	public function pjActionConfirmAuthorize()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjAuthorize') === NULL)
		{
			$this->log('Authorize.NET plugin not installed');
			exit;
		}
		$pjBookingModel = pjBookingModel::factory();
		
		$booking_arr = $pjBookingModel->reset()
			->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
			->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
			->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
			->find($_POST['x_invoice_num'])
			->getData();
									
		if (count($booking_arr) == 0)
		{
			$this->log('No such booking');
			pjUtil::redirect($this->option_arr['o_thankyou_page']);
		}					
		
		if (count($booking_arr) > 0)
		{
			$params = array(
				'transkey' => $this->option_arr['o_authorize_transkey'],
				'x_login' => $this->option_arr['o_authorize_merchant_id'],
				'md5_setting' => $this->option_arr['o_authorize_md5_hash'],
				'key' => md5($this->option_arr['private_key'] . PJ_SALT)
			);
			
			$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
			if ($response !== FALSE && $response['status'] === 'OK')
			{
				$pjBookingModel->reset()
					->setAttributes(array('id' => $response['transaction_id']))
					->modify(array('status' => $this->option_arr['o_payment_status'], 'processed_on' => ':NOW()'));

				pjBookingPaymentModel::factory()->setAttributes(array('booking_id' => $response['transaction_id'], 'payment_type' => 'online'))
												->modify(array('status' => 'paid'));

				$return_arr = $pjBookingModel->reset()->where('t1.return_id', $response['transaction_id'])->limit(1)->findAll()->getDataIndex(0);	
				if ($return_arr) {
					$return_id = $return_arr['id'];
					$pjBookingModel->reset()
						->setAttributes(array('id' => $return_id))
						->modify(array('status' => $this->option_arr['o_payment_status'], 'processed_on' => ':NOW()'));
	
					pjBookingPaymentModel::factory()->reset()->setAttributes(array('booking_id' => $return_id, 'payment_type' => 'online'))
													->modify(array('status' => 'paid'));
				}						
				
				pjAppController::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'payment', $booking_arr['locale_id']);
				
				$arr = $pjBookingModel->reset()->find($response['transaction_id'])->getData();
				if ($arr['status'] == 'confirmed') {
    				$resp = pjApiSync::syncBooking($response['transaction_id'], 'create', $this->option_arr);
    				if (isset($return_id) && (int)$return_id > 0) {
    					$resp = pjApiSync::syncBooking($return_id, 'create', $this->option_arr);
    				}
				}
			} elseif (!$response) {
				$this->log('Authorization failed');
			} else {
				$this->log('Booking not confirmed. ' . $response['response_reason_text']);
			}
			?>
			<script type="text/javascript">window.location.href="<?php echo $this->option_arr['o_thankyou_page']; ?>";</script>
			<?php
			return;
		}
	}

	public function pjActionConfirmPaypal()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			exit;
		}
		$pjBookingModel = pjBookingModel::factory();
		
		$booking_arr = $pjBookingModel->reset()
			->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
			->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
			->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
			->find($_POST['custom'])
			->getData();
		
		if (count($booking_arr) == 0)
		{
			$this->log('No such booking');
			pjUtil::redirect($this->option_arr['o_thankyou_page']);
		}					
		
		$params = array(
			'txn_id' => @$booking_arr['txn_id'],
			'paypal_address' => $this->option_arr['o_paypal_address'],
			'deposit' => @$booking_arr['deposit'],
			'currency' => $this->option_arr['o_currency'],
			'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);
		$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
		
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$this->log('Booking confirmed');
			$pjBookingModel->reset()->setAttributes(array('id' => $booking_arr['id']))->modify(array(
				'status' => $this->option_arr['o_payment_status'],
				'txn_id' => $response['transaction_id'],
				'processed_on' => ':NOW()'
			));
			
			pjBookingPaymentModel::factory()->setAttributes(array('booking_id' => $booking_arr['id'], 'payment_type' => 'online'))
											->modify(array('status' => 'paid'));
			
			$return_arr = $pjBookingModel->reset()->where('t1.return_id', $booking_arr['id'])->limit(1)->findAll()->getDataIndex(0);	
			if ($return_arr) {
				$return_id = $return_arr['id'];
				$pjBookingModel->reset()->setAttributes(array('id' => $return_id))->modify(array(
					'status' => $this->option_arr['o_payment_status'],
					'txn_id' => $response['transaction_id'],
					'processed_on' => ':NOW()'
				));

				pjBookingPaymentModel::factory()->reset()->setAttributes(array('booking_id' => $return_id, 'payment_type' => 'online'))
											->modify(array('status' => 'paid'));
			}
				
			pjAppController::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'payment', $booking_arr['locale_id']);
			
			$arr = $pjBookingModel->reset()->find($booking_arr['id'])->getData();
			if ($arr['status'] == 'confirmed') {
    			$resp = pjApiSync::syncBooking($booking_arr['id'], 'create', $this->option_arr);
    			if (isset($return_id) && (int)$return_id > 0) {
    				$resp = pjApiSync::syncBooking($return_id, 'create', $this->option_arr);
    			}
			}
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Booking not confirmed');
		}
		pjUtil::redirect($this->option_arr['o_thankyou_page']);
	}
	
	public function pjActionCancel()
	{
		$this->setLayout('pjActionCancel');
		
		$pjBookingModel = pjBookingModel::factory();
		
		if (isset($_POST['booking_cancel']))
		{
			
			$booking_arr = $pjBookingModel->reset()
				->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
				->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
				->find($_POST['id'])
				->getData();

			if (count($booking_arr) > 0)
			{
				$sql = "UPDATE `".$pjBookingModel->getTable()."` SET status = 'cancelled' WHERE SHA1(CONCAT(`id`, `created`, '".PJ_SALT."')) = '" . $_POST['hash'] . "'";
				
				$pjBookingModel->reset()->execute($sql);

				pjAppController::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'cancel', $booking_arr['locale_id']);
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . '?controller=pjFront&action=pjActionCancel&err=200');
			}
		}else{
			if (isset($_GET['hash']) && isset($_GET['id']))
			{
				$arr = $pjBookingModel	
					->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff, t7.content as country_title")
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
					->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t7.model='pjCountry' AND t7.foreign_id=t1.c_country AND t7.field='name' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
					->find($_GET['id'])->getData();
										
				if (count($arr) == 0)
				{
					$this->set('status', 2);
				}else{
					if ($arr['status'] == 'cancelled')
					{
						$this->set('status', 4);
					}else{
						$hash = sha1($arr['id'] . $arr['created'] . PJ_SALT);
						if ($_GET['hash'] != $hash)
						{
							$this->set('status', 3);
						}else{

							$this->set('arr', $arr);
						}
					}
				}
			}elseif (!isset($_GET['err'])) {
				$this->set('status', 1);
			}
		}
	}

    public function pjActionApplyCode()
    {
        $this->setAjax(true);

        if ($this->isXHR())
        {
            $cart = $this->_get('cart');
            if (!isset($_GET['voucher_code']) || !pjValidation::pjActionNotEmpty($_GET['voucher_code']))
            {
                $total = $cart['total'];
                $deposit = ($total * $this->option_arr['o_deposit_payment']) / 100;
                $rest = $total - $deposit;

                $total = number_format($total, 2, ',', ' ') . ' ' . $this->option_arr['o_currency'];
                $deposit = number_format($deposit, 2, ',', ' ') . ' ' . $this->option_arr['o_currency'];
                $rest = number_format($rest, 2, ',', ' ') . ' ' . $this->option_arr['o_currency'];
                pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => __('system_104', true), 'total' => $total, 'deposit' => $deposit, 'rest' => $rest));
            }

            $_SESSION[$this->defaultVoucher][$this->defaultIndex] = pjAppController::getDiscount($cart['sub_total'] + $cart['tax'], $_GET['voucher_code'], $this->option_arr['o_currency']);
            $total = $cart['total'];
            if($_SESSION[$this->defaultVoucher][$this->defaultIndex]['code'] == 200)
            {
                $total -= $_SESSION[$this->defaultVoucher][$this->defaultIndex]['discount'];
            }
            $deposit = ($total * $this->option_arr['o_deposit_payment']) / 100;
            $rest = $total - $deposit;

            $total = number_format($total, 2, ',', ' ') . ' ' . $this->option_arr['o_currency'];
            $deposit = number_format($deposit, 2, ',', ' ') . ' ' . $this->option_arr['o_currency'];
            $rest = number_format($rest, 2, ',', ' ') . ' ' . $this->option_arr['o_currency'];

            pjAppController::jsonResponse(array_merge($_SESSION[$this->defaultVoucher][$this->defaultIndex], array('total' => $total, 'deposit' => $deposit, 'rest' => $rest)));
        }
        exit;
    }

    public function pjActionEmailTemplate()
    {
        $this->setLayout('pjActionEmpty');
    }

    public function pjActionDownload()
    {
        if(isset($_GET['id']) && !empty($_GET['id']))
        {
            $arr = pjBookingModel::factory()
            	->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
                ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
				->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
                ->where('t1.uuid', $_GET['id'])
                ->findAll()
                ->getDataIndex(0);

            if($arr)
            {
                $html = pjUtil::fileGetContents(PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionPrint&id=' . $arr['id']);

                $name = "booking_{$arr['uuid']}.pdf";
                $pjPdf = new pjPdf();
                $pjPdf->downloadPdf($name, $html);
                exit;
            }
        }

        exit;
    }

    public function pjActionPrint()
    {
        $this->setLayout('pjActionEmpty');
        $transfer_arr = array();

        if (isset($_GET['id']))
        {
            $transfer_arr = pjBookingModel::factory()
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
                ->where("t1.id", $_GET['id'])
                ->orWhere("t1.return_id", $_GET['id'])
                ->orderBy("t1.id ASC")
                ->findAll()
                ->getData();

            $extra_arr = pjBookingExtraModel::factory()
                ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.extra_id AND t3.field='info' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                ->select("t1.quantity, t2.content as name, t3.content as info")
                ->where('booking_id', $_GET['id'])
                ->orderBy('t1.extra_id ASC')
                ->findAll()
                ->getData();
            $this->set('extra_arr', $extra_arr);
        }
        $this->set('transfer_arr', $transfer_arr);
    }

    public function pjActionDriverPDF()
    {
        $this->setLayout('pjActionEmpty');
        $transfer_arr = array();

        if (isset($_GET['id']))
        {
            $transfer_arr = pjBookingModel::factory()
                ->where("t1.id", $_GET['id'])
                ->orWhere("t1.return_id", $_GET['id'])
                ->orderBy("t1.id ASC")
                ->findAll()
                ->getDataIndex(0);
        }
        $this->set('transfer_arr', $transfer_arr);
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
			    		$icon = 'fad fa-map-marker';
			    		if (in_array('airport', $v['types'])) {
			    			$icon = 'fad fa-plane-departure';
			    		} elseif (in_array('restaurant', $v['types'])) {
			    			$icon = 'fad fa-utensils';
			    		} elseif (in_array('store', $v['types'])) {
			    			$icon = 'fad fa-shopping-bag';
			    		} elseif (in_array('train_station', $v['types'])) {
			    			$icon = 'fad fa-subway';
			    		} elseif (in_array('university', $v['types']) || in_array('school', $v['types'])) {
			    			$icon = 'fad fa-graduation-cap';
			    		}
			    		$data_google[] = array(
			    			'id' => 'google~::~'.$v['place_id'].'~::~',
			    			'icon' => $icon,
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
    
	public function pjActionGetDropoff()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			list($type, $location_id) = explode('~::~', $_POST['location_id']);
			if (isset($_POST['custom_pickup_id']) && (int)$_POST['custom_pickup_id'] > 0) {
				$location_id = (int)$_POST['custom_pickup_id'];
			}
			
			$priceNotNullQuery = '';
			$date = pjUtil::formatDate($_POST['date'], $this->option_arr['o_date_format']);
			if($date)
            {
                $dayIndex = date('N', strtotime($date));
                $priceNotNullQuery = "WHERE TP.price_{$dayIndex} IS NOT NULL";
            }

			$dropoff_arr = pjDropoffModel::factory()
				->select("t1.*, t2.content as location")
				->join('pjMultiLang', "t2.model='pjDropoff' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where('t1.location_id', $location_id)
				//->where("t1.id IN (SELECT TP.dropoff_id FROM `".pjPriceModel::factory()->getTable()."` TP {$priceNotNullQuery})")
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
					->where('t1.is_disabled', 0)
					->orderBy('t3.is_airport DESC, t3.order_index ASC')
					->findAll()
					->getData();
				pjAppController::jsonResponse(array('status' => 'OK', 'pickup_arr' => $pickup_arr, 'lat' => $lat, 'lng' => $lng));
			}
			pjAppController::jsonResponse(array('status' => 'ERR'));
		}
	}
	
	public function pjActionConfirmPaySafe()
	{
	    $this->setAjax(true);
	    
	    if(isset($_GET['uuid']) && !empty($_GET['uuid']))
	    {
	        $pjBookingModel = pjBookingModel::factory();
	        $booking_arr = $pjBookingModel
	        ->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
	        ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	        ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
	        ->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
	        ->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
	        ->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
	        ->where('t1.uuid', $_GET['uuid'])
	        ->limit(1)
	        ->findAll()
	        ->getDataIndex(0);
	        if($booking_arr)
	        {
	            $payload = array(
	                'RequestHeader' => array(
	                    'SpecVersion' => "1.10",
	                    'CustomerId' => $this->option_arr['o_saferpay_customer_id'],
	                    'RequestId' => $booking_arr['saferpay_request_id'],
	                    'RetryIndicator' => 0,
	                    'ClientInfo' => array(
	                        'ShopInfo' => "My Shop",
	                        'OsInfo' => "Windows Server 2013"
	                    )
	                ),
	                'Token' => $booking_arr['saferpay_token']
	            );
	            $url = PJ_TEST_MODE ? 'https://test.saferpay.com/api/Payment/v1/PaymentPage/Assert' : 'https://www.saferpay.com/api/Payment/v1/PaymentPage/Assert';
	            $paysafe_data = $this->doPaySafeURL($this->option_arr, $payload, $url, null, $booking_arr['uuid']);
	            if($paysafe_data['status'] == 200)
	            {
	                $transaction_status = $paysafe_data['body']['Transaction']['Status'];
	                $transaction_id = $paysafe_data['body']['Transaction']['Id'];
	                $uuid = $paysafe_data['body']['Transaction']['OrderId'];	                
	                if($transaction_status == 'AUTHORIZED')
	                {
	                    /*$payload = array(
	                     'RequestHeader' => array(
	                     'SpecVersion' => "1.10",
	                     'CustomerId' => $this->option_arr['o_saferpay_customer_id'],
	                     'RequestId' => $booking_arr['saferpay_request_id'],
	                     'RetryIndicator' => 0
	                     ),
	                     'TransactionReference' => array(
	                     'TransactionId' => $transaction_id
	                     )
	                     );
	                     $url = PJ_TEST_MODE ? 'https://test.saferpay.com/api/Payment/v1/Transaction/Capture' : 'https://www.saferpay.com/api/Payment/v1/Transaction/Capture';
	                     $paysafe_data = $this->doPaySafeURL($this->option_arr, $payload, $url, null, $uuid);
	                     
	                     $transaction_status = $paysafe_data['body']['Status'];*/
	                    if(/*$transaction_status == 'CAPTURED'*/ true)
	                    {
	                        $this->log('The transaction was authorized.');
	                        
	                        $data_update_arr = array();
	                        if (in_array($booking_arr['payment_method'], array('cash','creditcard_later'))) {
	                            $cc_fee = 0;
	                            $total = round((float)$booking_arr['total'] - (float)$booking_arr['credit_card_fee']);
	                            if ((float)$this->option_arr['o_saferpay_fee'] > 0) {
	                                $cc_fee = round(($total * (float)$this->option_arr['o_saferpay_fee'])/100);
	                            }
	                            $total += $cc_fee;
	                            $deposit = ($total * (float)$this->option_arr['o_deposit_payment']) / 100;
	                            $deposit = round($deposit);
	                            
	                            $data_update_arr['deposit'] = $deposit;
	                            $data_update_arr['total'] = $total;
	                            $data_update_arr['payment_method'] = 'saferpay';
	                            $data_update_arr['credit_card_fee'] = $cc_fee;
	                            $data_update_arr['paid_via_payment_link'] = 1;
	                        } else { 
	                           $data_update_arr['status'] = $this->option_arr['o_payment_status'];
	                        }
	                        $data_update_arr['txn_id'] = $transaction_id;
	                        $data_update_arr['processed_on'] = date('Y-m-d H:i:s');
	                        
	                        $pjBookingModel->reset()
	                        ->set('id', $booking_arr['id'])
	                        ->modify($data_update_arr);
	                        
	                        pjBookingPaymentModel::factory()
	                        ->where('booking_id', $booking_arr['id'])
	                        ->where('payment_type', 'online')
	                        ->limit(1)
	                        ->modifyAll(array('status' => 'paid'));
	                        
	                        $return_arr = $pjBookingModel->reset()->where('t1.return_id', $booking_arr['id'])->limit(1)->findAll()->getDataIndex(0);
	                        if ($return_arr) {
	                            $return_id = $return_arr['id'];
	                            $pjBookingModel->reset()
	                            ->set('id', $return_id)
	                            ->modify(array('status' => $this->option_arr['o_payment_status'], 'txn_id' => $transaction_id, 'processed_on' => ':NOW()'));
	                            
	                            pjBookingPaymentModel::factory()->reset()
	                            ->where('booking_id', $return_id)
	                            ->where('payment_type', 'online')
	                            ->limit(1)
	                            ->modifyAll(array('status' => 'paid'));
	                        }
	                        
	                        $arr = $pjBookingModel->reset()
	                        ->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT_WS(' - ', t6.content, t4.content), t1.dropoff_address) AS dropoff")
	                        ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	                        ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
	                        ->join('pjMultiLang', "t4.model='pjAreaCoord' AND t4.foreign_id=t1.dropoff_place_id AND t4.field='place_name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
	                        ->join('pjAreaCoord', "t5.id=t1.dropoff_place_id", 'left')
	                        ->join('pjMultiLang', "t6.model='pjArea' AND t6.foreign_id=t5.area_id AND t6.field='name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
	                        ->find($booking_arr['id'])
	                        ->getData();
	                        
	                        if (!in_array($booking_arr['payment_method'], array('cash','creditcard_later'))) {
    	                        $bookingDate = new DateTime($arr['booking_date']);
    	                        $arrivalNotice = pjArrivalNoticeModel::factory()
    	                        ->reset()
    	                        ->where('t1.date_from <=', $bookingDate->format('Y-m-d'))
    	                        ->where('t1.date_to >=', $bookingDate->format('Y-m-d'))
    	                        ->findCount()
    	                        ->getData();
    	                        
    	                        $now = date('Y-m-d H:i:s');
    	                        $diff = strtotime($arr['booking_date']) - strtotime($now);
    	                        $hours = $diff / (60 * 60);
    	                        if ($hours < 24 || $arrivalNotice > 0 || $arr['price_by_distance'] == 'T') {
    	                            pjAppController::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'arrival', $arr['locale_id']);
    	                        } else {
    	                            pjAppController::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'confirm', $arr['locale_id']);
    	                        }
	                        } else { 
	                           pjAppController::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'payment', $arr['locale_id']);
	                        }
	                        if ($arr['status'] == 'confirmed') {
	                            $resp = pjApiSync::syncBooking($arr['id'], 'create', $this->option_arr);
    	                        if (isset($return_id) && (int)$return_id > 0) {
    	                            $resp = pjApiSync::syncBooking($return_id, 'create', $this->option_arr);
    	                        }
	                        }
	                        ?>
    						<script type="text/javascript">window.location.href="<?php echo PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionPaySafeReturn&locale='.@$arr['locale_id'].'&uuid='.$arr['uuid'].'&type=notify'; ?>";</script>
    						<?php
    						exit;
    					}else{
    						$this->log('The transaction was authorized. Status is ' . $transaction_status);
    					}
    				} else {
    					$this->log('The transaction was not authorized. Status is ' . $transaction_status);
    				}
	            }else{
	                $this->log('Payment was not authorized.');
	            }
	        } else {
	            $this->log('Booking not found.');
	        }
	    }else{
	        $this->log('Missing parameters.');
	    }
	    ?>
	    <script type="text/javascript">window.location.href="<?php echo PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionPaySafeReturn&locale='.@$booking_arr['locale_id'].'&uuid='.@$booking_arr['uuid'].'&type=fail'; ?>";</script>
	    <?php 
	    exit;
	}
	
	public function pjActionSwitchLocations()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['is_seperate_search_form'])) {
				$pickup_id = $_POST['search_location_id'];
				$dropoff_id = $_POST['search_dropoff_id'];
			} else {
				$pickup_id = $_POST['location_id'];
				$dropoff_id = $_POST['dropoff_id'];
			}
			if (!empty($pickup_id)) {
				if (isset($_POST['custom_pickup_id']) && (int)$_POST['custom_pickup_id'] > 0) {
					$pickup_type = 'server';
					$location_id = (int)$_POST['custom_pickup_id'];
				} else {
					list($pickup_type, $location_id) = explode('~::~', $pickup_id);
				}
				if ($pickup_type == 'server') {
					$pickup_location_arr = pjLocationModel::factory()->select('t1.*, t2.content AS pickup_location')
		                ->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
		                ->find($location_id)->getData();
				}
			}
			if (!empty($dropoff_id)) {
				if (isset($_POST['custom_dropoff_id']) && (int)$_POST['custom_dropoff_id'] > 0) {
					$dropoff_type = 'server';
					$dropoff_id = (int)$dropoff_id;
				} else {
					list($dropoff_type, $dropoff_place_id, $dropoff_id) = explode('~::~', $dropoff_id);
				}
				if ($dropoff_type == 'server') {
					$dropoff_place_arr = pjDropoffAreaModel::factory()->select('t1.dropoff_id, t4.*, t5.content AS area_name, t6.content AS place_name')
							->join('pjDropoff', 't2.id=t1.dropoff_id', 'inner')
							->join('pjArea', 't3.id=t1.area_id', 'inner')
							->join('pjAreaCoord', 't4.area_id=t3.id', 'inner')
							->join('pjMultiLang', "t5.model='pjArea' AND t5.foreign_id=t1.area_id AND t5.field='name' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
							->join('pjMultiLang', "t6.model='pjAreaCoord' AND t6.foreign_id=t4.id AND t6.field='place_name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
							->where('t1.dropoff_id', $dropoff_id)
							->where('t4.id', $dropoff_place_id)
							->orderBy('t1.dropoff_id ASC, t3.order_index ASC, t5.content ASC')
							->limit(1)
							->findAll()
							->getDataIndex(0);
					if ($dropoff_place_arr) {
						$pjLocationModel = pjLocationModel::factory()->reset();
						if ($pickup_type == 'server') {
								$pjLocationModel->where('t1.id <>', (int)$location_id);
						}
						$pickup_arr = $pjLocationModel
							->select("t1.*, t2.content as pickup_location")
							->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
							->where('t1.status', 'T')
							->where('t2.content LIKE "'.$dropoff_place_arr['place_name'].'%"')
							->orderBy("t1.is_airport DESC, t1.order_index ASC, pickup_location ASC")
							->limit(1)
							->findAll()->getDataIndex(0);
						if ($pickup_arr) {
							if ($pickup_type == 'server') {
								/* Get Dropff based on original pickup */
								$dropoff_arr = pjDropoffModel::factory()->reset()
									->select("t1.*, t2.content as location")
									->join('pjMultiLang', "t2.model='pjDropoff' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
									->where('t1.location_id', $pickup_arr['id'])
									->orderBy("t1.is_airport DESC, t1.order_index ASC, location ASC")
									->findAll()->getData();
								$dropoff_ids_arr = array();
								foreach ($dropoff_arr as $v) {
									$dropoff_ids_arr[] = $v['id'];
								}
								$dropoff_value = '';
								if ($dropoff_ids_arr) {
									$dropoff_place_arr = pjDropoffAreaModel::factory()->reset()->select('t1.dropoff_id, t4.*, t5.content AS area_name, t6.content AS place_name')
										->join('pjDropoff', 't2.id=t1.dropoff_id', 'inner')
										->join('pjArea', 't3.id=t1.area_id', 'inner')
										->join('pjAreaCoord', 't4.area_id=t3.id', 'inner')
										->join('pjMultiLang', "t5.model='pjArea' AND t5.foreign_id=t1.area_id AND t5.field='name' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
										->join('pjMultiLang', "t6.model='pjAreaCoord' AND t6.foreign_id=t4.id AND t6.field='place_name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
										->whereIn('t1.dropoff_id', $dropoff_ids_arr)
										->where('t6.content LIKE "'.$pickup_location_arr['pickup_location'].'%"')
										->where('t4.is_disabled', 0)
										->orderBy('t1.dropoff_id ASC, t3.order_index ASC, t5.content ASC')
										->limit(1)
										->findAll()
										->getDataIndex(0);
									if ($dropoff_place_arr) {
										$dropoff_value = 'server~::~'.$dropoff_place_arr['id'].'~::~'.$dropoff_place_arr['dropoff_id'];
									}
								}
							}
							pjAppController::jsonResponse(array('status' => 'OK', 'pickup_value' => 'server~::~'.$pickup_arr['id'], 'dropoff_value' => $dropoff_value));
						}
					}
				} else if ($dropoff_type == 'google') {
					pjAppController::jsonResponse(array('status' => 'ERR', 'pickup_value' => $dropoff_id));
				}
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'value' => ''));
		}
	}
	
	public function pjActionPaySafeReturn()
	{
		$this->setLayout('pjActionEmpty');
		if (isset($_GET['uuid']) && !empty($_GET['uuid'])) {
			$pjBookingModel = pjBookingModel::factory();
				
			$uuid = pjObject::escapeString($_GET['uuid']);
			$arr = $pjBookingModel->where('t1.uuid', $uuid)->limit(1)->findAll()->getDataIndex(0);
			/*if ($arr && isset($_GET['type']) && in_array($_GET['type'], array('fail','abort'))) {
				$pjBookingModel->set('id', $arr['id'])->erase();
				pjBookingExtraModel::factory()->where('booking_id', $arr['id'])->eraseAll();
				pjBookingPaymentModel::factory()->where('booking_id', $arr['id'])->eraseAll();
				
				$return_arr = $pjBookingModel->reset()->where('t1.return_id', $arr['id'])->limit(1)->findAll()->getDataIndex(0);
				if ($return_arr) {
					$pjBookingModel->reset()->set('id', $return_arr['id'])->erase();					
					pjBookingExtraModel::factory()->reset()->where('booking_id', $return_arr['id'])->eraseAll();
					pjBookingPaymentModel::factory()->reset()->where('booking_id', $return_arr['id'])->eraseAll();
				}
			}*/
			$this->set('arr', $arr);
		}
		
		$options = pjMultiLangModel::factory()->getMultiLang($this->getForeignId(), 'pjOption');
		$siteUrl = '';
		foreach ($options as $locale => $option) {
		    foreach ($option as $key => $value) {
		        if ($locale == $this->getLocaleId() && $key == 'o_site_url') {
		            $siteUrl = $value;
		            break 2;
		        }
		    }
		}
		$this->set('siteUrl', $siteUrl);
	}
	
	public function pjActionCart()
    {
        $this->setAjax(true);

        if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
        {
            $this->set('store', @$_SESSION[$this->defaultStore][$this->defaultIndex]);
			$this->set('form', @$_SESSION[$this->defaultForm][$this->defaultIndex]);
            $this->set('cart', $this->_get('cart'));
        }
    }
    
    public function pjActionGetExtras()
    {
        $this->setAjax(true);
        
        $this->set('extra_arr', pjExtraModel::factory()
			->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.field='info' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
			->select("t1.*, t2.content as name, t3.content as info")
			->where('t1.status', 'T')
			->orderBy("t1.id ASC")
			->findAll()
			->getData());
		$this->set('el_arr', pjExtraLimitationModel::factory()
			->where('fleet_id', $this->_get('fleet_id'))
			->findAll()
			->getDataPair('extra_id', 'max_qty'));
			
    	if($this->_is('extras'))
		{
			$this->updateExtras($this->_get('extras'), 'pickup');
		}
		
		if($this->_is('extras_return'))
		{
			$this->updateExtras($this->_get('extras_return'), 'return');
		}
		$this->set('store', @$_SESSION[$this->defaultStore][$this->defaultIndex]);
    }
    
	public function pjActionPayment()
    {
        $this->setAjax(true);

    	if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if (isset($_GET['booking_uuid']) && !empty($_GET['booking_uuid']))
			{
                $pjBookingModel = pjBookingModel::factory();

                $booking_arr = $pjBookingModel->reset()
                    ->select("t1.*,t2.content as fleet, t3.content as location, t4.content as dropoff, t5.content as dropoff_place_name")
                    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t5.model='pjAreaCoord' AND t5.foreign_id=t1.dropoff_place_id AND t5.field='place_name' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
                    ->where('t1.uuid', $_GET['booking_uuid'])
                    ->limit(1)
                    ->findAll()
                    ->getDataIndex(0);
                if ($booking_arr) {
	                $return_arr = array();
	                if(!empty($booking_arr['return_date']))
	                {
	                    $return_arr = $pjBookingModel->reset()
	                        ->select("t1.*,t2.content as fleet, t3.content as location, t4.content as dropoff, t5.content as dropoff_place_name")
	                        ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	                        ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
	                        ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
	                        ->join('pjMultiLang', "t5.model='pjAreaCoord' AND t5.foreign_id=t1.dropoff_place_id AND t5.field='place_name' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
	                        ->where('t1.return_id', $booking_arr['id'])
	                        ->findAll()
	                        ->getDataIndex(0);
	                }
	                $this->set('return_arr', $return_arr);
	
	                $pickup_arr = pjLocationModel::factory()
	                    ->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	                    ->select("t1.*, t2.content as pickup_location")
	                    ->where('t1.status', 'T')
	                    ->orderBy("pickup_location ASC")
	                    ->find($booking_arr['location_id'])
	                    ->getData();
	                $this->set('pickup_arr', $pickup_arr);
	
	                $dropoff_arr = pjAreaCoordModel::factory()
	                    ->join('pjMultiLang', "t2.model='pjArea' AND t2.foreign_id=t1.area_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	                    ->join('pjMultiLang', "t3.model='pjAreaCoord' AND t3.foreign_id=t1.id AND t3.field='place_name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
	                    ->select("t1.*, t2.content as area_name, t3.content AS place_name")
	                    ->find($booking_arr['dropoff_place_id'])
	                    ->getData();
	                $this->set('dropoff_arr', $dropoff_arr);
	
	                $fleet = pjFleetModel::factory()
	                    ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	                    ->select("t1.id, t2.content as fleet")
	                    ->find($booking_arr['fleet_id'])
	                    ->getData();
	                $this->set('fleet', $fleet);
	
	                $extra_arr = pjBookingExtraModel::factory()
	                    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	                    ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.extra_id AND t3.field='info' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
	                    ->join('pjExtra', 't4.id=t1.extra_id', 'left outer')
	                    ->select("t1.quantity, t1.price, t2.content as name, t3.content as info, t4.image_path")
	                    ->where('booking_id', $booking_arr['id'])
	                    ->orderBy('t1.extra_id ASC')
	                    ->findAll()
	                    ->getData();
	                $this->set('extra_arr', $extra_arr);
	                if ($return_arr) {
	                	$extra_return_arr = pjBookingExtraModel::factory()->reset()
		                    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
		                    ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.extra_id AND t3.field='info' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
		                    ->join('pjExtra', 't4.id=t1.extra_id', 'left outer')
		                    ->select("t1.quantity, t1.price, t2.content as name, t3.content as info, t4.image_path")
		                    ->where('booking_id', $return_arr['id'])
		                    ->orderBy('t1.extra_id ASC')
		                    ->findAll()
		                    ->getData();
		                $this->set('extra_return_arr', $extra_return_arr);
	                }
	
	                $bookingDate = new DateTime($booking_arr['booking_date']);
					$arrivalNotice = pjArrivalNoticeModel::factory()
						->reset()
						->where('t1.date_from <=', $bookingDate->format('Y-m-d'))
						->where('t1.date_to >=', $bookingDate->format('Y-m-d'))
						->findCount()
						->getData();
					$this->set('arrivalNotice', $arrivalNotice);
					
					$country_arr = array();
	                if(!empty($booking_arr['c_country']))
	                {
	                    $country_arr = pjCountryModel::factory()
	                                ->select('t1.id, t2.content AS country_title')
	                                ->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	                                ->find($booking_arr['c_country'])->getData();
	                }
	                $this->set('country_arr', $country_arr);
	
	                if (in_array($booking_arr['payment_method'], array('cash','creditcard_later')) && in_array($booking_arr['status'], array('pending','confirmed'))) {
	                    $cc_fee = 0;
	                    $total = round((float)$booking_arr['total'] - (float)$booking_arr['credit_card_fee']);
	                    if ((float)$this->option_arr['o_saferpay_fee'] > 0) {
	                        $cc_fee = round(($total * (float)$this->option_arr['o_saferpay_fee'])/100);
	                    }
	                    $total += $cc_fee;
	                    $deposit = ($total * (float)$this->option_arr['o_deposit_payment']) / 100;
	                    $deposit = round($deposit);
	                    
	                    $booking_arr['deposit'] = $deposit;
	                    $booking_arr['total'] = $total;
	                    $booking_arr['payment_method'] = 'saferpay';
	                    $booking_arr['credit_card_fee'] = $cc_fee;
	                    $booking_arr['allow_saferpay_only'] = 1;
	                }
	                $locale_arr = pjLocaleModel::factory()->find($booking_arr['locale_id'])->getData();
	                list($iso, ) = explode('-', $locale_arr['language_iso']);
					if (empty($booking_arr['txn_id'])) {
		                $url = PJ_TEST_MODE ? 'https://test.saferpay.com/api/Payment/v1/PaymentPage/Initialize' : 'https://www.saferpay.com/api/Payment/v1/PaymentPage/Initialize';
					    $payload = array(
					        'RequestHeader' => array(
					            'SpecVersion' => "1.10",
					            'CustomerId' => $this->option_arr['o_saferpay_customer_id'],
					            'RequestId' => md5($booking_arr['uuid'] . PJ_SALT),
					            'RetryIndicator' => 0,
					            'ClientInfo' => array(
					                'ShopInfo' => "My Shop",
					                'OsInfo' => "Windows Server 2013"
					            )
					        ),
					        'TerminalId' => $this->option_arr['o_saferpay_terminal_id'],
					        'Payment' => array(
					            'Amount' => array(
					                'Value' => $booking_arr['deposit'] * 100,
					                'CurrencyCode' => $this->option_arr['o_currency']
					            ),
					            'OrderId' => $booking_arr['uuid'],
					            'Description' => __('front_transfer_reservation', true, false)
					        ),
					        'Payer' => array(
					            //'IpAddress' => pjUtil::getClientIp(),
					            'IpAddress' => "192.168.178.1",
					            'LanguageCode' => $iso
					        ),
					        'ReturnUrls' => array(
					            'Success' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmPaySafe&locale='.$booking_arr['locale_id'].'&uuid='.$booking_arr['uuid'],
					            'Fail' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionPaySafeReturn&type=fail&locale='.$booking_arr['locale_id'].'&uuid='.$booking_arr['uuid'],
					        	'Abort' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionPaySafeReturn&type=abort&locale='.$booking_arr['locale_id'].'&uuid='.$booking_arr['uuid']
					        ),
					        'Notification' => array(
					            'NotifyUrl' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionPaySafeReturn&type=notify&locale='.$booking_arr['locale_id'].'&uuid='.$booking_arr['uuid']
					        )
					    );
					    $paysafe_data = $this->doPaySafeURL($this->option_arr, $payload, $url, 'initialize', $booking_arr['uuid']);
					    $this->set('paysafe_data', $paysafe_data);
					}
					$this->set('arr', $booking_arr);
					$this->set('status', 'OK');
                } else {
                	$this->set('status', 'ERR');
                }	
			}else{
				$this->set('status', 'ERR');
			}
		}
    }
    
    public function pjActionFinishBooking() {
    	$this->setAjax(true);
    	if ($this->isXHR() || isset($_GET['_escaped_fragment_'])) {
    		if (isset($_POST['step_payment'])) {
    			$pjBookingModel = pjBookingModel::factory();
				if (isset($_POST['payment_method'])) {
				    $arr = $pjBookingModel->find($_POST['booking_id'])->getData();
				    $return_arr = $pjBookingModel->reset()->where('t1.return_id', $_POST['booking_id'])->limit(1)->findAll()->getDataIndex(0);
				    
				    $cc_fee = 0;
				    $total = round($arr['total'] - $arr['credit_card_fee']);
				    if ($_POST['payment_method'] == 'creditcard_later' && (float)$this->option_arr['o_creditcard_later_fee'] > 0) {
				        $cc_fee = round(($total * (float)$this->option_arr['o_creditcard_later_fee'])/100);
				    } elseif ($_POST['payment_method'] == 'saferpay' && (float)$this->option_arr['o_saferpay_fee'] > 0) {
				        $cc_fee = round(($total * (float)$this->option_arr['o_saferpay_fee'])/100);
				    }
				    
				    $total += $cc_fee;
				    $deposit = in_array($_POST['payment_method'], array('creditcard', 'paypal', 'authorize', 'saferpay')) ? (($total * (float)$this->option_arr['o_deposit_payment']) / 100): 0;
				    $deposit = round($deposit);
				    
				    $data = array();
				    $data['payment_method'] = $_POST['payment_method'];
				    $data['total'] = $total;
				    $data['deposit'] = $deposit;
				    $data['credit_card_fee'] = $cc_fee;
				    $data['status'] = 'pending';
				    if($return_arr) {
				        $data['price'] = $data['total']/2;
				    } else {
				        $data['price'] = $data['total'];
				    }
				    $pjBookingModel->reset()->set('id', $_POST['booking_id'])->modify($data);
				    
				    if ($return_arr) {
				        $pjBookingModel->set('id', $return_arr['id'])->modify($data);
				    }
				}
				$arr = $pjBookingModel->reset()
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->select("t1.*, t2.content as fleet, t3.content as location, t4.content as dropoff")
					->find($_POST['booking_id'])
					->getData();
					
    			$bookingDate = new DateTime($arr['booking_date']);
				$arrivalNotice = pjArrivalNoticeModel::factory()
					->reset()
					->where('t1.date_from <=', $bookingDate->format('Y-m-d'))
					->where('t1.date_to >=', $bookingDate->format('Y-m-d'))
					->findCount()
					->getData();

                $now = date('Y-m-d H:i:s');
				$diff = strtotime($arr['booking_date']) - strtotime($now);
				$hours = $diff / (60 * 60);
				if ($arr['payment_method'] != 'saferpay' || ($arr['payment_method'] == 'saferpay' && $this->defaultPaySafePaymentMethod == 'direct')) {
					if ($hours < 24 || $arrivalNotice > 0 || $arr['price_by_distance'] == 'T') {
					    pjAppController::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'arrival', $arr['locale_id']);
					} else {
					    pjAppController::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'confirm', $arr['locale_id']);
					}
				}
				
				pjAppController::jsonResponse(array('status' => 'OK', 'text' => '', 'booking_id' => $_POST['booking_id']));
    		} else {
    			pjAppController::jsonResponse(array('status' => 'ERR', 'text' => ''));
    		}
    	}
    }
}
?>