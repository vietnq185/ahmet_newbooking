<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminDrivers extends pjAdmin
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
			$pjDriverModel = pjDriverModel::factory()->where('t1.email', $_GET['email']);
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjDriverModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjDriverModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['driver_create']))
			{
				$data = array();
				
				$id = pjDriverModel::factory(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					pjAppController::pjActionAccountSend($this->option_arr, $id, PJ_SALT, $this->getLocaleId());
					$err = 'AD03';
				} else {
					$err = 'AD04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminDrivers&action=pjActionIndex&err=$err");
			} else {
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminDrivers.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDelete()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjDriverModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjDriverModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExport()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjDriverModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Drivers-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGet()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjDriverModel = pjDriverModel::factory();
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjDriverModel->where('t1.email LIKE', "%$q%");
				$pjDriverModel->orWhere('t1.fname LIKE', "%$q%");
				$pjDriverModel->orWhere('t1.lname LIKE', "%$q%");
			}

			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjDriverModel->where('t1.status', $_GET['status']);
			}
				
			$column = 'fname';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjDriverModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjDriverModel
				->select("t1.*, (SELECT COUNT(`TB`.id) FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.driver_id=t1.id) cnt_bookings")
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
			$this->appendJs('pjAdminDrivers.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSave()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjDriverModel = pjDriverModel::factory();
			
			$pjDriverModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value'], 'modified' => date('Y-m-d H:i:s')));
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['driver_update']))
			{
				$data = array();
				$data['modified'] = date('Y-m-d H:i:s');
				pjDriverModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminDrivers&action=pjActionIndex&err=AD01");
				
			} else {
				$arr = pjDriverModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminDrivers&action=pjActionIndex&err=AD02");
				}
				$this->set('arr', $arr);

				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminDrivers.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>