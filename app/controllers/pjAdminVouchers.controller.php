<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminVouchers extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['voucher_create']))
			{
				$data = array();
				$data['code'] = $_POST['code'];
				$data['discount'] = $_POST['discount'];
				$data['type'] = $_POST['type'];
				$data['valid'] = $_POST['valid'];
				switch ($_POST['valid'])
				{
					case 'fixed':
						$data['date_from'] = pjUtil::formatDate($_POST['f_date'], $this->option_arr['o_date_format']);
						$data['date_to'] = $data['date_from'];
						$data['time_from'] = $_POST['f_hour_from'] . ":" . $_POST['f_minute_from'] . ":00";
						$data['time_to'] = $_POST['f_hour_to'] . ":" . $_POST['f_minute_to'] . ":00";
						break;
					case 'period':
						$data['date_from'] = pjUtil::formatDate($_POST['p_date_from'], $this->option_arr['o_date_format']);
						$data['date_to'] = pjUtil::formatDate($_POST['p_date_to'], $this->option_arr['o_date_format']);
						$data['time_from'] = $_POST['p_hour_from'] . ":" . $_POST['p_minute_from'] . ":00";
						$data['time_to'] = $_POST['p_hour_to'] . ":" . $_POST['p_minute_to'] . ":00";
						break;
					case 'recurring':
						$data['every'] = $_POST['r_every'];
						$data['time_from'] = $_POST['r_hour_from'] . ":" . $_POST['r_minute_from'] . ":00";
						$data['time_to'] = $_POST['r_hour_to'] . ":" . $_POST['r_minute_to'] . ":00";
						break;
				}
				
				$id = pjVoucherModel::factory()->setAttributes($data)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$err = 'AV03';
				} else {
					$err = 'AV04';
				}
				
				pjUtil::redirect(sprintf("%s?controller=pjAdminVouchers&action=pjActionIndex&err=%s", $_SERVER['PHP_SELF'], $err));
			} else {
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
				$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminVouchers.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteVoucher()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				if (pjVoucherModel::factory()->set('id', $_GET['id'])->erase()->getAffectedRows() == 1)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Voucher has been deleted.'));
				} else {
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Voucher has not been deleted.'));
				}
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing or empty params.'));
		}
		exit;
	}
	
	public function pjActionDeleteVoucherBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['record']) && !empty($_POST['record']))
			{
				pjVoucherModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Voucher(s) has been deleted.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing or empty params.'));
		}
		exit;
	}

	public function pjActionGetVoucher()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			$pjVoucherModel = pjVoucherModel::factory();
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = trim($_GET['q']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
				$pjVoucherModel->where('t1.code LIKE', "%$q%");
			}
			if (isset($_GET['valid']) && !empty($_GET['valid']))
			{
				$pjVoucherModel->where('t1.valid', $_GET['valid']);
			}

			$column = 'code';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjVoucherModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjVoucherModel->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
				
			$daynames = __('daynames', true);
			foreach ($data as $k => $v)
			{
				$data[$k]['discount_f'] = $v['type'] == 'amount' ?
					pjUtil::formatCurrencySign(number_format($v['discount'], 2), $this->option_arr['o_currency']) :
					$v['discount'] . '%';
					
				switch ($v['valid'])
				{
					case 'fixed':
						$data[$k]['valid_f'] = sprintf('%s, %s - %s', pjUtil::formatDate($v['date_from'], 'Y-m-d', $this->option_arr['o_date_format']), substr($v['time_from'], 0, 5), substr($v['time_to'], 0, 5));
						break;
					case 'period':
						$data[$k]['valid_f'] = sprintf('%s, %s &divide; %s, %s', pjUtil::formatDate($v['date_from'], 'Y-m-d', $this->option_arr['o_date_format']), substr($v['time_from'], 0, 5), pjUtil::formatDate($v['date_to'], 'Y-m-d', $this->option_arr['o_date_format']), substr($v['time_to'], 0, 5));
						break;
					case 'recurring':
						$data[$k]['valid_f'] = sprintf('%s, %s - %s', @$daynames[$v['every']], substr($v['time_from'], 0, 5), substr($v['time_to'], 0, 5));
						break;
				}
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
			$this->appendJs('pjAdminVouchers.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveVoucher()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_GET['id']) && (int) $_GET['id'] > 0 &&
				isset($_POST['column']) && isset($_POST['value']) &&
				!empty($_POST['column']))
			{
				$pjVoucherModel = pjVoucherModel::factory();
				if (!in_array($_POST['column'], $pjVoucherModel->getI18n()))
				{
					$pjVoucherModel->set('id', $_GET['id'])->modify(array($_POST['column'] => $_POST['value'], 'modified' => date('Y-m-d H:i:s')));
				} else {
					$pjVoucherModel->set('id', $_GET['id'])->modify(array('modified' => date('Y-m-d H:i:s')));
					pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjVoucher');
				}
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Voucher has been saved.'));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing or empty params.'));
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['voucher_update']))
			{
				$data = array();
				$data['id'] = $_POST['id'];
				$data['code'] = $_POST['code'];
				$data['discount'] = $_POST['discount'];
				$data['type'] = $_POST['type'];
				$data['valid'] = $_POST['valid'];
				$data['modified'] = date('Y-m-d H:i:s');
				switch ($_POST['valid'])
				{
					case 'fixed':
						$data['date_from'] = pjUtil::formatDate($_POST['f_date'], $this->option_arr['o_date_format']);
						$data['date_to'] = $data['date_from'];
						$data['time_from'] = $_POST['f_hour_from'] . ":" . $_POST['f_minute_from'] . ":00";
						$data['time_to'] = $_POST['f_hour_to'] . ":" . $_POST['f_minute_to'] . ":00";
						$data['every'] = array('NULL');
						break;
					case 'period':
						$data['date_from'] = pjUtil::formatDate($_POST['p_date_from'], $this->option_arr['o_date_format']);
						$data['date_to'] = pjUtil::formatDate($_POST['p_date_to'], $this->option_arr['o_date_format']);
						$data['time_from'] = $_POST['p_hour_from'] . ":" . $_POST['p_minute_from'] . ":00";
						$data['time_to'] = $_POST['p_hour_to'] . ":" . $_POST['p_minute_to'] . ":00";
						$data['every'] = array('NULL');
						break;
					case 'recurring':
						$data['date_from'] = array('NULL');
						$data['date_to'] = array('NULL');
						$data['every'] = $_POST['r_every'];
						$data['time_from'] = $_POST['r_hour_from'] . ":" . $_POST['r_minute_from'] . ":00";
						$data['time_to'] = $_POST['r_hour_to'] . ":" . $_POST['r_minute_to'] . ":00";
						break;
				}

                pjVoucherModel::factory()->set('id', $data['id'])->modify($data);

                pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminVouchers&action=pjActionIndex&err=AV01");
			} else {
				$arr = pjVoucherModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
                    pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminVouchers&action=pjActionIndex&err=AV02");
				}
				$this->set('arr', $arr);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminVouchers.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>