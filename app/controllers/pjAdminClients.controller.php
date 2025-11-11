<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminClients extends pjAdmin
{
	public function pjActionCheckEmail()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (!isset($_GET['email']) || empty($_GET['email']))
			{
				echo 'false';
				exit;
			}
			$pjClientModel = pjClientModel::factory()->where('t1.email', $_GET['email']);
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjClientModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjClientModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['client_create']))
			{
				$data = array();
				
				$id = pjClientModel::factory(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					pjAppController::pjActionAccountSend($this->option_arr, $id, PJ_SALT, $this->getLocaleId());
					$err = 'AC03';
				} else {
					$err = 'AC04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminClients&action=pjActionIndex&err=$err");
			} else {
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`country_title` ASC')
					->findAll()
					->getData();
				
				$this->set('country_arr', $country_arr);
				
				$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminClients.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteClient()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjClientModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				$booking_id_arr = pjBookingModel::factory()->where('t1.client_id', $_GET['id'])->findAll()->getDataPair(null, 'id');
				if(!empty($booking_id_arr))
				{
					pjBookingModel::factory()->whereIn('id', $booking_id_arr)->eraseAll();
					pjBookingPaymentModel::factory()->whereIn('booking_id', $booking_id_arr)->eraseAll();
				}
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteClientBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjClientModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				$booking_id_arr = pjBookingModel::factory()->whereIn('client_id', $_POST['record'])->findAll()->getDataPair(null, 'id');
				if(!empty($booking_id_arr))
				{
					pjBookingModel::factory()->whereIn('id', $booking_id_arr)->eraseAll();
					pjBookingPaymentModel::factory()->whereIn('booking_id', $booking_id_arr)->eraseAll();
				}
			}
		}
		exit;
	}
	
	public function pjActionExportClient()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjClientModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Clients-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetClient()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjClientModel = pjClientModel::factory();
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjClientModel->where('t1.email LIKE', "%$q%");
				$pjClientModel->orWhere('t1.fname LIKE', "%$q%");
				$pjClientModel->orWhere('t1.lname LIKE', "%$q%");
			}

			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjClientModel->where('t1.status', $_GET['status']);
			}
				
			$column = 'fname';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjClientModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjClientModel
				->select("t1.*, (SELECT COUNT(`TB`.id) FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.client_id=t1.id) cnt_bookings")
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
			foreach($data as $k => $v)
			{
				$name_arr = array();
				if(!empty($v['fname']))
				{
					$name_arr[] = pjSanitize::clean($v['fname']);
				}
				if(!empty($v['lname']))
				{
					$name_arr[] = pjSanitize::clean($v['lname']);
				}
				$v['name'] = join(" ", $name_arr);
				$v['email'] = pjSanitize::clean($v['email']);
				$data[$k] = $v;
			}
				
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminClients.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveClient()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjClientModel = pjClientModel::factory();
			
			$pjClientModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value'], 'modified' => date('Y-m-d H:i:s')));
		}
		exit;
	}
	
	public function pjActionStatusClient()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjClientModel::factory()->whereIn('id', $_POST['record'])->modifyAll(array(
					'status' => ":IF(`status`='F','T','F')",
					'modified' => date('Y-m-d H:i:s')
				));
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['client_update']))
			{
				$data = array();
				$data['modified'] = date('Y-m-d H:i:s');
				pjClientModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminClients&action=pjActionIndex&err=AC01");
				
			} else {
				$arr = pjClientModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminClients&action=pjActionIndex&err=AC08");
				}
				$this->set('arr', $arr);
				
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`country_title` ASC')
					->findAll()
					->getData();
				
				$this->set('country_arr', $country_arr);
				
				$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminClients.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>