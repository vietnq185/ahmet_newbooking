<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdmin extends pjAppController
{
	public $defaultUser = 'admin_user';
	
	public $requireLogin = true;
		
	public function __construct($requireLogin=null)
	{
		$this->setLayout('pjActionAdmin');
		
		if (!is_null($requireLogin) && is_bool($requireLogin))
		{
			$this->requireLogin = $requireLogin;
		}
		
		if ($this->requireLogin)
		{
			if (!$this->isLoged() && !in_array(@$_GET['action'], array('pjActionLogin', 'pjActionForgot', 'pjActionPreview')))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
			}
		}
	}
	
	public function afterFilter()
	{
		parent::afterFilter();
		if ($this->isLoged() && !in_array(@$_GET['action'], array('pjActionLogin')))
		{
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		}
	}
	
	public function beforeRender()
	{
		
	}
		
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$pjBookingModel = pjBookingModel::factory();
			
			$cnt_new_reservations = $pjBookingModel
				->where("(DATE_FORMAT(t1.created, '%Y-%m-%d')=DATE_FORMAT(NOW(), '%Y-%m-%d') AND (t1.return_id IS NULL))")
				->findCount()
				->getData();
			$cnt_today_transfers = $pjBookingModel
				->reset()
				->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')=DATE_FORMAT(NOW(), '%Y-%m-%d'))")
				->findCount()
				->getData();
				
			$latest_arr = $pjBookingModel
				->reset()
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjDropoff', "t4.id=t1.dropoff_id", 'left outer')
				->join('pjMultiLang', "t5.model='pjDropoff' AND t5.foreign_id=t1.dropoff_id AND t5.field='location' AND t5.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjClient', "t6.id=t1.client_id", 'left outer')				
				->join('pjMultiLang', "t7.model='pjAreaCoord' AND t7.foreign_id=t1.dropoff_place_id AND t7.field='place_name' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjAreaCoord', "t8.id=t1.dropoff_place_id", 'left')
				->join('pjMultiLang', "t9.model='pjArea' AND t9.foreign_id=t8.area_id AND t9.field='name' AND t9.locale='".$this->getLocaleId()."'", 'left outer')				
				->select("t1.*, t2.content as vehicle, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', t7.content, t1.dropoff_address) as dropoff_location, t6.fname, t6.lname, t6.email, t6.phone")
				->where("(t1.return_id IS NULL)")
				->orderBy("t1.created DESC")
				->limit(5)
				->findAll()
				->getData();
				
			$today_arr = $pjBookingModel
				->reset()
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjDropoff', "t5.id=t1.dropoff_id", 'left outer')
				->join('pjBooking', "t6.id=t1.return_id", 'left outer')
				->join('pjClient', "t7.id=t1.client_id", 'left')
				->join('pjMultiLang', "t8.model='pjAreaCoord' AND t8.foreign_id=t1.dropoff_place_id AND t8.field='place_name' AND t8.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjAreaCoord', "t9.id=t1.dropoff_place_id", 'left')
				->join('pjMultiLang', "t10.model='pjArea' AND t10.foreign_id=t9.area_id AND t10.field='name' AND t10.locale='".$this->getLocaleId()."'", 'left outer')	
				->select("t1.*, t2.content as vehicle, 
						IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', t8.content, t1.dropoff_address) AS dropoff_location, 
						t6.uuid as uuid2, t6.id as id2, 
						IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS dropoff_location2, IF(t1.dropoff_type='server', t8.content, t1.dropoff_address) AS location2,
						t1.duration as duration2,
						t7.fname, t7.lname, t7.email,t7.phone")
				->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')=DATE_FORMAT(NOW(), '%Y-%m-%d'))")
				->where("t1.status <> 'cancelled'")
				->orderBy("t1.created DESC")
				->limit(5)
				->findAll()
				->getData();
				
			$this->set('cnt_new_reservations', $cnt_new_reservations);
			$this->set('cnt_today_transfers', $cnt_today_transfers);
			$this->set('latest_arr', $latest_arr);
			$this->set('today_arr', $today_arr);

            // Calendar
            $year = date("Y");
            $month = date("n");
            if(isset($_GET['year']) && isset($_GET['month']))
            {
                $year = $_GET['year'];
                $month = $_GET['month'];
            }

            $this->__getCalendar($this->getForeignId(), $year, $month);

            $this->set('calendar_format', pjUtil::formatDate($year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01', 'Y-m-d', $this->option_arr['o_date_format']));

            $this->appendJs('pjAdmin.js');
            $this->appendCss('index.php?controller=pjAdmin&action=pjActionLoadCss&cid=' . $this->getForeignId() . '&' . rand(1,99999), PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}

    private function __getCalendar($cid, $year, $month, $view=1)
    {
        $option_arr = $this->option_arr;
        $option_arr['o_month_year_format'] = 'Month Year';

        $start = date("Y-m-d", mktime(0, 0, 0, $month, 1, $year));
        $end = date("Y-m-d", mktime(23, 59, 59, $month + $view, 0, $year));

        $ABCalendar = new pjABCalendar();
        $ABCalendar
            ->setShowNextLink((int) $view > 1 ? false : true)
            ->setShowPrevLink((int) $view > 1 ? false : true)
            ->setPrevLink("")
            ->setNextLink("")
            ->set('calendarId', $cid)
            ->set('reservationsInfo', pjBookingModel::factory()
                ->select('DATE(booking_date) as date')
                ->where("(DATE(booking_date) BETWEEN '$start' AND '$end')")
                ->where('status', 'confirmed')
                ->groupBy('date')
                ->findAll()
                ->getDataPair('date', 'date'))
            ->set('options', $option_arr)
            ->set('weekNumbers', true)
            ->setStartDay($this->option_arr['o_week_start'])
            ->setDayNames(__('day_names', true))
            ->setMonthNames(__('months', true))
        ;

        $this->set('ABCalendar', $ABCalendar);
    }

    public function pjActionLoadCss()
    {
        $arr = array(
            array('file' => 'ABCalendar.css', 'path' => PJ_CSS_PATH),
            array('file' => 'ABFonts.min.css', 'path' => PJ_CSS_PATH)
        );
        header("Content-Type: text/css; charset=utf-8");
        foreach ($arr as $item)
        {
            ob_start();
            @readfile($item['path'] . $item['file']);
            $string = ob_get_contents();
            ob_end_clean();

            if ($string !== FALSE)
            {
                echo str_replace(
                        array('../img/', '../fonts/'),
                        array(PJ_IMG_PATH, PJ_FONT_PATH),
                        $string) . "\n";
            }
        }

        ob_start();
        @readfile(PJ_CSS_PATH . 'ABCalendar.txt');
        $string = ob_get_contents();
        ob_end_clean();

        $option_arr = array(
            'o_background_available' => '#80b369',
            'o_background_booked' => '#da5350',
            'o_background_empty' => '#f8f6f0',
            'o_background_month' => '#248faf',
            'o_background_nav' => '#187c9a',
            'o_background_nav_hover' => '#116b86',
            'o_background_past' => '#f2f0ea',
            'o_background_pending' => '#f9ce67',
            'o_background_weekday' => '#ffffff',
            'o_border_inner' => '#e0dfde',
            'o_border_inner_size' => '1',
            'o_border_outer' => '#000000',
            'o_border_outer_size' => '0',
            'o_color_available' => '#ffffff',
            'o_color_booked' => '#ffffff',
            'o_color_legend' => '#676F71',
            'o_color_month' => '#ffffff',
            'o_color_past' => '#c5c6c7',
            'o_color_pending' => '#ffffff',
            'o_color_weekday' => '#737576',
            'o_font_family' => 'Arial',
            'o_font_family_legend' => 'Arial',
            'o_font_size_available' => '14',
            'o_font_size_booked' => '14',
            'o_font_size_legend' => '12',
            'o_font_size_month' => '20',
            'o_font_size_past' => '14',
            'o_font_size_pending' => '14',
            'o_font_size_weekday' => '12',
            'o_font_style_available' => 'font-weight: bold',
            'o_font_style_booked' => 'font-weight: bold',
            'o_font_style_legend' => 'font-weight: normal',
            'o_font_style_month' => 'font-weight: normal',
            'o_font_style_past' => 'font-weight: bold',
            'o_font_style_pending' => 'font-weight: bold',
            'o_font_style_weekday' => 'font-weight: normal',
            'o_month_year_format' => 'Month Year',
            'o_show_legend' => '1',
            'o_show_week_numbers' => '1',
        );

        if ($string !== FALSE && isset($option_arr['o_show_week_numbers']))
        {
            echo str_replace(
                array(
                    '[calendarContainer]',
                    '[URL]',
                    '[cell_width]',
                    '[cell_height]',
                    '[background_available]',
                    '[c_background_available]',
                    '[background_booked]',
                    '[c_background_booked]',
                    '[background_empty]',
                    '[background_month]',
                    '[background_nav]',
                    '[background_nav_hover]',
                    '[background_past]',
                    '[background_pending]',
                    '[c_background_pending]',
                    '[background_select]',
                    '[background_weekday]',
                    '[border_inner]',
                    '[border_inner_size]',
                    '[border_outer]',
                    '[border_outer_size]',
                    '[color_available]',
                    '[color_booked]',
                    '[color_legend]',
                    '[color_month]',
                    '[color_past]',
                    '[color_pending]',
                    '[color_weekday]',
                    '[font_family]',
                    '[font_family_legend]',
                    '[font_size_available]',
                    '[font_size_booked]',
                    '[font_size_legend]',
                    '[font_size_month]',
                    '[font_size_past]',
                    '[font_size_pending]',
                    '[font_size_weekday]',
                    '[font_style_available]',
                    '[font_style_booked]',
                    '[font_style_legend]',
                    '[font_style_month]',
                    '[font_style_past]',
                    '[font_style_pending]',
                    '[font_style_weekday]'
                ),
                array(
                    '#abWrapper_' . $_GET['cid'],
                    PJ_INSTALL_URL,
                    number_format((100 / ((int) $option_arr['o_show_week_numbers'] === 1 ? 8 : 7)), 2, '.', ''),
                    number_format(100 / 8, 2, '.', ''),
                    $option_arr['o_background_available'],
                    str_replace('#', '', $option_arr['o_background_available']),
                    $option_arr['o_background_booked'],
                    str_replace('#', '', $option_arr['o_background_booked']),
                    $option_arr['o_background_empty'],
                    $option_arr['o_background_month'],
                    $option_arr['o_background_nav'],
                    $option_arr['o_background_nav_hover'],
                    $option_arr['o_background_past'],
                    $option_arr['o_background_pending'],
                    str_replace('#', '', $option_arr['o_background_pending']),
                    $option_arr['o_background_select'],
                    $option_arr['o_background_weekday'],
                    $option_arr['o_border_inner'],
                    $option_arr['o_border_inner_size'],
                    $option_arr['o_border_outer'],
                    $option_arr['o_border_outer_size'],
                    $option_arr['o_color_available'],
                    $option_arr['o_color_booked'],
                    $option_arr['o_color_legend'],
                    $option_arr['o_color_month'],
                    $option_arr['o_color_past'],
                    $option_arr['o_color_pending'],
                    $option_arr['o_color_weekday'],
                    $option_arr['o_font_family'],
                    $option_arr['o_font_family_legend'],
                    $option_arr['o_font_size_available'],
                    $option_arr['o_font_size_booked'],
                    $option_arr['o_font_size_legend'],
                    $option_arr['o_font_size_month'],
                    $option_arr['o_font_size_past'],
                    $option_arr['o_font_size_pending'],
                    $option_arr['o_font_size_weekday'],
                    $option_arr['o_font_style_available'],
                    $option_arr['o_font_style_booked'],
                    $option_arr['o_font_style_legend'],
                    $option_arr['o_font_style_month'],
                    $option_arr['o_font_style_past'],
                    $option_arr['o_font_style_pending'],
                    $option_arr['o_font_style_weekday']
                ),
                $string
            );
        }
        exit;
    }

    public function pjActionGetCal()
    {
        $this->setAjax(true);

        if ($this->isXHR())
        {
            $this->__getCalendar($_GET['cid'], $_GET['year'], $_GET['month']);
        }
    }
	
	public function pjActionForgot()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['forgot_user']))
		{
			if (!isset($_POST['forgot_email']) || !pjValidation::pjActionNotEmpty($_POST['forgot_email']) || !pjValidation::pjActionEmail($_POST['forgot_email']))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			}
			$pjUserModel = pjUserModel::factory();
			$user = $pjUserModel
				->where('t1.email', $_POST['forgot_email'])
				->limit(1)
				->findAll()
				->getData();
				
			if (count($user) != 1)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			} else {
				$user = $user[0];
				
				$Email = new pjEmail();
				$Email
					->setTo($user['email'])
					->setFrom($user['email'], $this->option_arr['o_email_sender'])
					->setSubject(__('emailForgotSubject', true));
				
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$Email
						->setTransport('smtp')
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpPort($this->option_arr['o_smtp_port'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass'])
					;
				}
				
				$body = str_replace(
					array('{Name}', '{Password}'),
					array($user['name'], $user['password']),
					__('emailForgotBody', true)
				);

				if ($Email->send($body))
				{
					$err = "AA11";
				} else {
					$err = "AA12";
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=$err");
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	
	public function pjActionMessages()
	{
		$this->setAjax(true);
		header("Content-Type: text/javascript; charset=utf-8");
	}
	
	public function pjActionLogin()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['login_user']))
		{
			if (!isset($_POST['login_email']) || !isset($_POST['login_password']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_email']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_password']) ||
				!pjValidation::pjActionEmail($_POST['login_email']))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=4");
			}
			$pjUserModel = pjUserModel::factory();

			$user = $pjUserModel
				->where('t1.email', $_POST['login_email'])
				->where(sprintf("t1.password = AES_ENCRYPT('%s', '%s')", pjObject::escapeString($_POST['login_password']), PJ_SALT))
				->limit(1)
				->findAll()
				->getData();

			if (count($user) != 1)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=1");
			} else {
				$user = $user[0];
				unset($user['password']);
															
				if (!in_array($user['role_id'], array(1,2,3)))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['role_id'] == 3 && $user['is_active'] == 'F')
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['status'] != 'T')
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=3");
				}
				
				# Login succeed
				$last_login = date("Y-m-d H:i:s");
				if($user['last_login'] == $user['created'])
				{
					$user['last_login'] = date("Y-m-d H:i:s");
				}
    			$_SESSION[$this->defaultUser] = $user;
    			
    			$data = array();
    			$data['last_login'] = $last_login;
    			$pjUserModel->reset()->setAttributes(array('id' => $user['id']))->modify($data);

    			if ($this->isAdmin() || $this->isEditor())
    			{
	    			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionIndex");
    			}
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	
	public function pjActionLogout()
	{
		if ($this->isLoged())
        {
        	unset($_SESSION[$this->defaultUser]);
        }
       	pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
	}
	
	public function pjActionProfile()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin())
		{
			if (isset($_POST['profile_update']))
			{
				$pjUserModel = pjUserModel::factory();
				$arr = $pjUserModel->find($this->getUserId())->getData();
				$data = array();
				$data['role_id'] = $arr['role_id'];
				$data['status'] = $arr['status'];
				$post = array_merge($_POST, $data);
				if (!$pjUserModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA14");
				}
				$pjUserModel->set('id', $this->getUserId())->modify($post);
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA13");
			} else {
				$this->set('arr', pjUserModel::factory()->find($this->getUserId())->getData());
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdmin.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetTotalAmounts() {
		$this->setAjax(true);
		if ($this->isXHR()) {
			$pjBookingModel = pjBookingModel::factory();
			
			$date_from = isset($_GET['date']) && !empty($_GET['date']) ? pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']) : date('Y-m-d');
			$date_to = $date_from;
			$amount = array();
			
			$clause = " 1=1 ";
			if($date_from != null && $date_to != null)
			{
				$clause .= " AND (DATE_FORMAT(t1.booking_date, '%Y-%m-%d')>='$date_from' AND DATE_FORMAT(t1.booking_date, '%Y-%m-%d')<='$date_to')";
			}else if($date_from != null && $date_to == null){
				$clause .= " AND (DATE_FORMAT(t1.booking_date, '%Y-%m-%d')>='$date_from')";
			}else if($date_from == null && $date_to != null){
				$clause .= " AND DATE_FORMAT(t1.booking_date, '%Y-%m-%d')<='$date_to')";
			}
			
			$_arr = $pjBookingModel
				->reset()
				->where($clause)
				->orderBy('t1.id ASC')
				->findAll()
				->getData();
			$arr = array();
			foreach ($_arr as $val) {
				if ($val['return_id'] > 0) {
					$arr[$val['return_id']][] = $val;
				} else {
					$arr[$val['id']][] = $val;
				}
			}
			
			$amount['total'] = 0;
			$amount['confirmed'] = 0;
			$amount['in_progress'] = 0;
			$amount['passed_on'] = 0;
			$amount['cancelled'] = 0;
			foreach($arr as $rid => $v)
			{
				$is_half_price = false;
				if ((!empty($v[0]['return_date']) || (int)$v[0]['return_id'] > 0) && count($v) == 1) {
					$is_half_price = true;
				}
				$v = $v[0];
				if($v['status'] == 'confirmed')
				{
					if ($is_half_price) {
						$amount['confirmed'] += $v['total'] / 2;
					} else {
						$amount['confirmed'] += $v['total'];
					}
				}
	            if($v['status'] == 'in_progress')
	            {
	                if ($is_half_price) {
	                	$amount['in_progress'] += $v['total'] / 2;
	                } else {
	                	$amount['in_progress'] += $v['total'];
	                }
	            }
	            if($v['status'] == 'passed_on')
	            {
	                if ($is_half_price) {
	                    $amount['passed_on'] += $v['total'] / 2;
	                } else {
	                    $amount['passed_on'] += $v['total'];
	                }
	            }
				if($v['status'] == 'cancelled')
				{
					if ($is_half_price) {
						$amount['cancelled'] += $v['total'] / 2;	
					} else {
						$amount['cancelled'] += $v['total'];
					}
				}
				if ($is_half_price) {
					$amount['total'] += $v['total'] / 2;
				} else {
					$amount['total'] += $v['total'];
				}
			}
			$this->set('amount', $amount);
		}
	}
}
?>