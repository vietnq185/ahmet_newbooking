<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjInvoice extends pjInvoiceAppController
{
	public $invoiceErrors = 'InvoiceErrors';
	
	private function sortTimezones(Array $array)
	{
		$ordered = array();
		$orderArray = array('-43200', '-39600', '-36000', '-32400', '-28800', '-25200', '-21600', '-18000', '-14400', '-10800', '-7200', '-3600', '0', '3600', '7200', '10800', '14400', '18000', '21600', '25200', '28800', '32400', '36000', '39600', '43200', '46800');
		foreach($orderArray as $key) {
			if(array_key_exists($key,$array)) {
				$ordered[$key] = $array[$key];
				unset($array[$key]);
			}
		}
		return $ordered + $array;
	}
	
	public function pjActionCheckUniqueId()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && isset($_GET['uuid']))
		{
			$pjInvoiceModel = pjInvoiceModel::factory();
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjInvoiceModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjInvoiceModel->where('t1.uuid', $_GET['uuid'])->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionAddItem()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			if (isset($_POST['invoice_add']))
			{
				$insert_id = pjInvoiceItemModel::factory($_POST)->insert()->getInsertId();
				if ($insert_id !== false && (int) $insert_id > 0)
				{
					$pjInvoiceModel = pjInvoiceModel::factory();
					$invoice = $pjInvoiceModel->find($_POST['invoice_id'])->getData();
					if (!empty($invoice))
					{					    $data_invoice_arr = $this->recalPrice($invoice['id'], $this->option_arr);
					    $pjInvoiceModel->reset()->set('id', $invoice['id'])->modify($data_invoice_arr);					    
					    pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => '', 'data' => $data_invoice_arr));
					}
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
			
			if (isset($_GET['invoice_id']) && (int) $_GET['invoice_id'] > 0)
			{
				$this->set('arr', pjInvoiceModel::factory()->find($_GET['invoice_id'])->getData());
			}
			$this->set('config_arr', pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId()));			$this->set('invoice_tax_arr', pjInvoiceTaxModel::factory()			    ->join('pjMultiLang', "t2.model='pjInvoiceTax' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')			    ->select("t1.*, t2.content as name")			    ->orderBy("`tax` ASC")			    ->findAll()->getData());
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
		
		if (!isset($_POST['x_invoice_num']))
		{
			$this->log('Missing arguments');
			exit;
		}
		
		$pjInvoiceModel = pjInvoiceModel::factory();

		$invoice_arr = $pjInvoiceModel->where('t1.uuid', $_POST['x_invoice_num'])->limit(1)->findAll()->getDataIndex(0);
		if ($invoice_arr === FALSE || empty($invoice_arr))
		{
			$this->log('Invoice not found');
			exit;
		}
		$config_arr = pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId());
		
		$params = array(
			'transkey' => @$config_arr['p_authorize_key'],
			'x_login' => @$config_arr['p_authorize_mid'],
			'md5_setting' => @$config_arr['p_authorize_hash'],
			'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);
		
		$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$this->log('Invoice confirmed as paid');
			$pjInvoiceModel->reset()->set('id', $invoice_arr['id'])->modify(array('status' => 'paid', 'modified' => ':NOW()'));
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Invoice not confirmed as paid. ' . $response['response_reason_text']);
		}
		exit;
	}

	public function pjActionConfirmPaypal()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			exit;
		}
		
		if (!isset($_POST['custom']))
		{
			$this->log('Missing arguments');
			exit;
		}
		
		$pjInvoiceModel = pjInvoiceModel::factory();
		
		$invoice_arr = $pjInvoiceModel->where('t1.uuid', $_POST['custom'])->limit(1)->findAll()->getDataIndex(0);
		if ($invoice_arr === FALSE || empty($invoice_arr))
		{
			$this->log('Invoice not found');
			exit;
		}
		$config_arr = pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId());

		$params = array(
			'txn_id' => @$invoice_arr['txn_id'],
			'paypal_address' => $config_arr['p_paypal_address'],
			'deposit' => @$invoice_arr['total'],
			'currency' => @$invoice_arr['currency'],
			'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);

		$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$this->log('Invoice confirmed as paid');
			$pjInvoiceModel->reset()->set('id', $invoice_arr['id'])->modify(array(
				'status' => 'paid',
				'txn_id' => $response['transaction_id'],
				'processed_on' => ':NOW()'
			));
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Invoice not confirmed as paid');
		}
		exit;
	}

	public function pjActionConfirmBambora()
	{
	    $redirect_url = PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionView&id=' . $_GET['trnOrderNumber'];
	    if (!isset($_GET['hashValue']) || !isset($_GET['trnAmount']) || !isset($_GET['trnOrderNumber']) || !isset($_GET['ref1']) || !isset($_GET['ref2']))
        {
            $this->log('Bambora | Missing arguments');
            pjUtil::redirect($redirect_url);
        }

        $config_arr = pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId());

	    $pjInvoiceModel = pjInvoiceModel::factory();

		$invoice_arr = $pjInvoiceModel->where('t1.uuid', $_GET['ref2'])->limit(1)->findAll()->getDataIndex(0);
		if ($invoice_arr === FALSE || empty($invoice_arr))
		{
			$this->log('Invoice not found');
			pjUtil::redirect($redirect_url);
		}
		$redirect_url .= '&uuid=' . $invoice_arr['order_id'];

	    $hashValue = 'merchant_id=' . $config_arr['p_bambora_merchant_id'] . '&trnAmount=' . $_GET['trnAmount'] . '&trnOrderNumber=' . $_GET['trnOrderNumber'];
        if($config_arr['p_bambora_hash_algorithm'] == 'SHA-1')
        {
            $hashValue = sha1($hashValue . $config_arr['p_bambora_hash']);
        }
        elseif($config_arr['p_bambora_hash_algorithm'] == 'MD5')
        {
            $hashValue = md5($hashValue . $config_arr['p_bambora_hash']);
        }
        if($hashValue != $_GET['ref1'])
        {
            $this->log("Bambora | {$config_arr['p_bambora_hash_algorithm']} Hash failed.");
            pjUtil::redirect($redirect_url);
        }

        if ((int) $_GET['trnApproved'] === 1)
        {
            $this->log('Bambora | Invoice confirmed as paid');
            $pjInvoiceModel->reset()->set('id', $invoice_arr['id'])->modify(array(
                'status' => 'paid',
                'txn_id' => @$_GET['trnId'],
                'processed_on' => ':NOW()'
            ));
        } else {
            $this->log('Bambora | Invoice not confirmed as paid. Message: ' . @$_GET['messageText']);
        }

        pjUtil::redirect($redirect_url);
	}
	
	public function pjActionCreate()
	{
		$params = $this->getParams();
		if (!isset($params['key']) || $params['key'] != md5($this->option_arr['private_key'] . PJ_SALT))
		{
			return array('status' => 'ERR', 'code' => '101', 'text' => 'Key is not set or invalid');
		}
		
		$locale_id = isset($params['locale_id']) && !empty($params['locale_id']) ? $params['locale_id'] : $this->getLocaleId();
		$config = pjInvoiceConfigModel::factory()->getConfigData($locale_id);
		$config['id'] = NULL;
		unset($config['id']);
		
		$data = array_merge($config, $params);
		
		$invoice_id = pjInvoiceModel::factory($data)->insert()->getInsertId();
		if ($invoice_id !== FALSE && (int) $invoice_id > 0)
		{
			if (isset($params['items']) && is_array($params['items']) && !empty($params['items']))
			{
				$pjInvoiceItemModel = pjInvoiceItemModel::factory();
				foreach ($params['items'] as $item)
				{
					$item['invoice_id'] = $invoice_id;
					$pjInvoiceItemModel->reset()->setAttributes($item)->insert();
				}
			}
			return array('status' => 'OK', 'code' => '200', 'text' => 'Invoice has been created.', 'data' => array_merge($data, array('id' => $invoice_id)));
		} else {
			return array('status' => 'ERR', 'code' => '100', 'text' => 'Invoice has not been created.');
		}
	}
	
	public function pjActionCreateInvoice()
	{
		$this->checkLogin();
		
		if (!$this->isInvoiceReady())
		{
			$this->set('status', 2);
			return;
		}
		
		$pjInvoiceModel = pjInvoiceModel::factory();
		$pjInvoiceItemModel = pjInvoiceItemModel::factory();
			
		if (isset($_POST['invoice_create']))
		{
			$data = array();
			$data['foreign_id'] = $this->getForeignId();
			$data['issue_date'] = !empty($_POST['issue_date']) ? pjUtil::formatDate($_POST['issue_date'], $this->option_arr['o_date_format']) : NULL;
			$data['due_date'] = !empty($_POST['due_date']) ? pjUtil::formatDate($_POST['due_date'], $this->option_arr['o_date_format']) : NULL;
			$data['s_date'] = !empty($_POST['s_date']) ? pjUtil::formatDate($_POST['s_date'], $this->option_arr['o_date_format']) : NULL;
			$data = array_merge($_POST, $data);
			if (!$pjInvoiceModel->validates($data))
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionInvoices&err=PIN06");
			}
			$invoice_id = $pjInvoiceModel->setAttributes($data)->insert()->getInsertId();
			if ($invoice_id !== false && (int) $invoice_id > 0)
			{
				$pjInvoiceItemModel
					->where('tmp', $_POST['tmp'])
					->modifyAll(array(
						'invoice_id' => $invoice_id,
						'tmp' => ":NULL"
					));
				$err = "PIN07";
			} else {
				$err = "PIN08";
			}
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionInvoices&err=$err");
		} else {
			
			if (isset($_REQUEST['items']) && !empty($_REQUEST['items']))
			{
				$pjInvoiceItemModel->where('tmp', $_REQUEST['tmp'])->eraseAll();
				foreach ($_REQUEST['items'] as $item)
				{
					$item['tmp'] = $_REQUEST['tmp'];
					$pjInvoiceItemModel->reset()->setAttributes($item)->insert();
				}
			}
			$this->set('uuid', pjInvoiceModel::factory()->getInvoiceID());
			$this->set('config_arr', pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId()));
			
			$this
				->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/')
				->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
				->appendJs('pjInvoice.js', $this->getConst('PLUGIN_JS_PATH'))
				->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true)
			;
		}
	}
	
	public function pjActionDelete()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			$response = array();
			if (pjInvoiceModel::factory()->set('id', $_GET['id'])->erase()->getAffectedRows() == 1)
			{
				pjInvoiceItemModel::factory()->where('invoice_id', $_GET['id'])->eraseAll();
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
	
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjInvoiceModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjInvoiceItemModel::factory()->whereIn('invoice_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionDeleteItem()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			$pjInvoiceItemModel = pjInvoiceItemModel::factory();
			$invoice_item = $pjInvoiceItemModel->find($_GET['id'])->getData();
			
			if (!empty($invoice_item) && $pjInvoiceItemModel->erase()->getAffectedRows() == 1)
			{
				$pjInvoiceModel = pjInvoiceModel::factory();
				$invoice = $pjInvoiceModel->find($invoice_item['invoice_id'])->getData();
				if (!empty($invoice))
				{
				    $data_invoice_arr = $this->recalPrice($invoice['id'], $this->option_arr);				    $pjInvoiceModel->reset()->set('id', $invoice['id'])->modify($data_invoice_arr);
				    pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => '', 'data' => $data_invoice_arr));
				}
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}
			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
		}
		exit;
	}
	
	public function pjActionDeleteLogo()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			$pjInvoiceConfigModel = pjInvoiceConfigModel::factory();
			
			$arr = $pjInvoiceConfigModel->find(1)->getData();
			if (!empty($arr) && !empty($arr['y_logo']))
			{
				@clearstatcache();
				if (is_file($arr['y_logo']))
				{
					@unlink($arr['y_logo']);
				}
				$pjInvoiceConfigModel->set('id', 1)->modify(array('y_logo' => ':NULL'));
			}
		}
		exit;
	}

	public function pjActionEditItem()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			if (isset($_POST['invoice_edit']))
			{			    $arr = pjInvoiceItemModel::factory()->find($_POST['id'])->getData();			    pjInvoiceItemModel::factory()->reset()->set('id', $_POST['id'])->modify($_POST);			    $data_invoice_arr = $this->recalPrice($arr['invoice_id'], $this->option_arr);			    pjInvoiceModel::factory()->reset()->set('id', $arr['invoice_id'])->modify($data_invoice_arr);			    			    pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => '', 'data' => $data_invoice_arr));
			}
			
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$this->set('arr', pjInvoiceItemModel::factory()
					->select('t1.*, t2.currency')
					->join('pjInvoice', 't2.id=t1.invoice_id', 'left outer')
					->find($_GET['id'])->getData());
				$this->set('config_arr', pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId()));								$this->set('invoice_tax_arr', pjInvoiceTaxModel::factory()				    ->join('pjMultiLang', "t2.model='pjInvoiceTax' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')				    ->select("t1.*, t2.content as name")				    ->orderBy("`tax` ASC")				    ->findAll()->getData());
			}
		}
	}
	
	public function pjActionGetInvoices()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			$pjInvoiceModel = pjInvoiceModel::factory();
			
			if (isset($_GET['foreign_id']))
			{
				$foreign_arr = $this->get('foreign_arr');
				if ((int) $_GET['foreign_id'] > 0 && $foreign_arr !== FALSE && !empty($foreign_arr))
				{
					$pjInvoiceModel->where('t1.foreign_id', $_GET['foreign_id']);
				}
			}
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = $pjInvoiceModel->escapeStr($_GET['q']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
				$pjInvoiceModel
					->where('t1.uuid LIKE', "%$q%")
					->orWhere('t1.order_id LIKE', "%$q%")
					->orWhere('t1.b_company LIKE', "%$q%")
					->orWhere('t1.b_name LIKE', "%$q%")
					->orWhere('t1.b_email LIKE', "%$q%")
					->orWhere('t1.s_company LIKE', "%$q%")
					->orWhere('t1.s_name LIKE', "%$q%")
					->orWhere('t1.s_email LIKE', "%$q%")
				;
			}
				
			$column = 'created';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjInvoiceModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjInvoiceModel->orderBy("`$column` $direction")->limit($rowCount, $offset)->findAll()->getData();
			foreach ($data as $k => $v)
			{
				$data[$k]['total_formated'] = pjUtil::formatCurrencySign(number_format($v['total'], 2), !empty($v['currency']) ? $v['currency'] : $this->option_arr['o_currency']);
			}
						
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionGetItems()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			$pjInvoiceItemModel = pjInvoiceItemModel::factory();
			
			$column = 'id';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}
			$pjInvoiceItemModel->where('t1.id', -1);
			
			if (isset($_GET['invoice_id']) && (int) $_GET['invoice_id'] > 0)
			{
				$pjInvoiceItemModel->reset()->where('t1.invoice_id', $_GET['invoice_id']);
			}
			if (isset($_GET['tmp']) && !empty($_GET['tmp']))
			{
				$pjInvoiceItemModel->reset()->where('t1.tmp', $_GET['tmp']);
			}
			
			$data = $pjInvoiceItemModel
				->select('t1.*, t2.currency')
				->join('pjInvoice', 't2.id=t1.invoice_id', 'left outer')
				->orderBy("`$column` $direction")->findAll()->getData();
			foreach ($data as $k => $v)
			{			    $data[$k]['description'] = !empty($v['description']) ? nl2br($v['description']) : '';
				$data[$k]['unit_price_formated'] = pjUtil::formatCurrencySign(number_format((float)$v['unit_price'], 2), !empty($v['currency']) ? $v['currency'] : $this->option_arr['o_currency']);
				$data[$k]['amount_formated'] = pjUtil::formatCurrencySign(number_format((float)$v['amount'], 2), !empty($v['currency']) ? $v['currency'] : $this->option_arr['o_currency']);
			}
			
			pjAppController::jsonResponse(compact('data', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!$this->isInvoiceReady() || !$this->isAdmin())
		{
			$this->set('status', 2);
			return;
		}
		
		if (isset($_POST['invoice_post']))
		{
			if (isset($_FILES['y_logo']) && !empty($_FILES['y_logo']['tmp_name']))
			{
				$pjImage = new pjImage();
				$pjImage
					->setAllowedExt(array('png', 'gif', 'jpg', 'jpeg', 'jpe', 'jfif', 'jif', 'jfi'))
					->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
				if ($pjImage->load($_FILES['y_logo']))
				{
					$hash = md5(uniqid(rand(), true));
					$original = 'app/web/invoices/' . $hash . '.' . $pjImage->getExtension();
					$thumb = 'app/web/invoices/' . $hash . '_thumb.png';
					if ($pjImage->save($original))
					{
						$pjImage->loadImage($original)						//->resizeSmart(120, 60)						->saveImage($thumb);
						
						$_POST['y_logo'] = $thumb;
						@unlink($original);
					}
				} else {
					$time = time();
					$_SESSION[$this->invoiceErrors][$time] = $pjImage->getError();
				
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionIndex&err=PIN03&errTime=" . $time);
				}
			}
			$data = array();
			$data['p_accept_payments'] = isset($_POST['p_accept_payments']) ? 1 : 0;
			$data['p_accept_paypal'] = isset($_POST['p_accept_paypal']) ? 1 : 0;
			$data['p_accept_bambora'] = isset($_POST['p_accept_bambora']) ? 1 : 0;
			$data['p_accept_authorize'] = isset($_POST['p_accept_authorize']) ? 1 : 0;
			$data['p_accept_creditcard'] = isset($_POST['p_accept_creditcard']) ? 1 : 0;
			$data['p_accept_cash'] = isset($_POST['p_accept_cash']) ? 1 : 0;
			$data['p_accept_bank'] = isset($_POST['p_accept_bank']) ? 1 : 0;
			$data['si_include'] = isset($_POST['si_include']) ? 1 : 0;
			$data['si_shipping_address'] = isset($_POST['si_shipping_address']) ? 1 : 0;
			$data['si_company'] = isset($_POST['si_company']) ? 1 : 0;
			$data['si_name'] = isset($_POST['si_name']) ? 1 : 0;
			$data['si_address'] = isset($_POST['si_address']) ? 1 : 0;
			$data['si_street_address'] = isset($_POST['si_street_address']) ? 1 : 0;
			$data['si_city'] = isset($_POST['si_city']) ? 1 : 0;
			$data['si_state'] = isset($_POST['si_state']) ? 1 : 0;
			$data['si_zip'] = isset($_POST['si_zip']) ? 1 : 0;
			$data['si_phone'] = isset($_POST['si_phone']) ? 1 : 0;
			$data['si_fax'] = isset($_POST['si_fax']) ? 1 : 0;
			$data['si_email'] = isset($_POST['si_email']) ? 1 : 0;
			$data['si_url'] = isset($_POST['si_url']) ? 1 : 0;
			$data['si_date'] = isset($_POST['si_date']) ? 1 : 0;
			$data['si_terms'] = isset($_POST['si_terms']) ? 1 : 0;
			$data['si_is_shipped'] = isset($_POST['si_is_shipped']) ? 1 : 0;
			$data['si_shipping'] = isset($_POST['si_shipping']) ? 1 : 0;
			$data['o_qty_is_int'] = isset($_POST['o_qty_is_int']) ? 1 : 0;
			$data['o_use_qty_unit_price'] = isset($_POST['o_use_qty_unit_price']) ? 1 : 0;
			
			pjInvoiceConfigModel::factory()
				->set('id', 1)
				->modify(array_merge($_POST, $data));
			
			if (isset($_POST['i18n']))
			{
			    $data_i18n = array();			    foreach ($_POST['i18n'] as $locale_id => $val) {			        foreach ($val as $k => $v) {			            if ($k == 'tax_name') {			                continue;			            }			            $data_i18n[$locale_id][$k] = $v;			        }			    }			    pjMultiLangModel::factory()->updateMultiLang($data_i18n, 1, 'pjInvoiceConfig');
			}			$pjMultiLangModel = pjMultiLangModel::factory();			$pjInvoiceTaxModel = pjInvoiceTaxModel::factory();			if(isset($_POST['tax']) && !empty($_POST['tax']))			{			    foreach ($_POST['tax'] as $k => $v) {			        $data_tax = array();			        $data_tax['tax'] = $v;			        $data_tax['is_default'] = $_POST['is_default'][$k] ? 1 : 0;			        if(strpos($k, 'cr') !== false) {   			            $tax_id = $pjInvoiceTaxModel->reset()->setAttributes($data_tax)->insert()->getInsertId();			        } else{			            $tax_id = $k;			            $pjInvoiceTaxModel->reset()->where('id', $k)->limit(1)->modifyAll($data_tax);			        }			        if ($tax_id !== false && (int)$tax_id > 0) {			            foreach ($_POST['i18n'] as $locale => $locale_arr)			            {			                foreach ($locale_arr as $tfield => $content)			                {			                    if ($tfield != 'tax_name') continue;			                    $field = 'name';			                    if(is_array($content))			                    {			                        $sql = sprintf("INSERT INTO `%1\$s` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)																VALUES (NULL, :foreign_id, :model, :locale, :field, :update_content, :source)																ON DUPLICATE KEY UPDATE `content` = :update_content, `source` = :source;",			                            $pjMultiLangModel->getTable()			                            );			                        $foreign_id = $tax_id;			                        $model = 'pjInvoiceTax';			                        $source = 'data';			                        $update_content = @$content[$k];			                        $modelObj = $pjMultiLangModel->reset()->prepare($sql)->exec(compact('foreign_id', 'model', 'locale', 'field', 'update_content', 'source'));			                        if ($modelObj->getAffectedRows() > 0 || $modelObj->getInsertId() > 0)			                        {			                            			                        }			                    }			                }			            }			        }			    }			}						if(isset($_POST['remove_tax_arr']) && $_POST['remove_tax_arr'] != '')			{			    $remove_tax_arr = explode("|", $_POST['remove_tax_arr']);			    if ($remove_tax_arr) {			        $pjMultiLangModel->reset()->where('model', 'pjInvoiceTax')->whereIn('foreign_id', $remove_tax_arr)->eraseAll();			        $pjInvoiceTaxModel->reset()->whereIn('id', $remove_tax_arr)->eraseAll();			    }			}
			
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionIndex&err=PIN02&tab_id=" . $_POST['tab_id']);
		}
		
		$this->set('timezones', $this->sortTimezones(__('timezones', true)));
		$this->set('country_arr', pjCountryModel::factory()
			->select('t1.*, t2.content AS `name`')
			->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->orderBy('`name` ASC')
			->findAll()->getData());
		
		$arr = pjInvoiceConfigModel::factory()->find(1)->getData();
		if (!empty($arr))
		{
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjInvoiceConfig');
		}
		$invoice_tax_arr = pjInvoiceTaxModel::factory()->orderBy('t1.tax ASC')->findAll()->getData();		foreach($invoice_tax_arr as $k => $v)		{		    $invoice_tax_arr[$k]['i18n'] = pjMultiLangModel::factory()->getMultiLang($v['id'], 'pjInvoiceTax');		}		$this->set('invoice_tax_arr', $invoice_tax_arr);
		$locale_arr = pjLocaleModel::factory()
			->select('t1.*, t2.file')
			->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left outer')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')
			->findAll()
			->getData();
		
		$lp_arr = array();
		foreach ($locale_arr as $item)
		{
			$lp_arr[$item['id']."_"] = $item['file']; //Hack for jquery $.extend, to prevent (re)order of numeric keys in object
		}
		$this->set('lp_arr', $locale_arr);
		$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
		
		$this->set('is_flag_ready', $this->requestAction(array('controller' => 'pjLocale', 'action' => 'pjActionIsFlagReady'), array('return')));
		
		$this
			->set('arr', $arr)
			->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/')
			->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
			->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/')
			->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/')
			->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/')
			->appendJs('pjInvoice.js', $this->getConst('PLUGIN_JS_PATH'))
			->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true)
		;
	}
	
	public function pjActionInvoices()
	{
		$this->checkLogin();
		
		if (!$this->isInvoiceReady())
		{
			$this->set('status', 2);
			return;
		}
		
		$this
			->set('invoice_config_arr', pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId()))
			->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/')
			->appendJs('pjInvoice.js', $this->getConst('PLUGIN_JS_PATH'))
			->appendCss('plugin_invoice.css', $this->getConst('PLUGIN_CSS_PATH'))
			->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true)
		;
	}
	
	public function pjActionPayment()
	{
		$this->setLayout('pjActionEmpty');
		
		$arr = pjInvoiceModel::factory()->where('t1.uuid', $_POST['uuid'])->limit(1)->findAll()->getDataIndex(0);
		if ($arr === FALSE || empty($arr))
		{
			return;
		}
		
		$config_arr = pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId());
		
		$data = array();
		if($_POST['payment_method'] == 'creditcard')
		{
			$data['cc_type'] = $_POST['cc_type'];
			$data['cc_num'] = $_POST['cc_num'];
			$data['cc_code'] = $_POST['cc_code'];
			$data['cc_exp_month'] = $_POST['cc_exp_month'];
			$data['cc_exp_year'] = $_POST['cc_exp_year'];
		}
		$data['payment_method'] = $_POST['payment_method'];
		pjInvoiceModel::factory()->set('id', $arr['id'])->modify($data);
		
		switch ($_POST['payment_method'])
		{
			case 'paypal':
				$this->set('params', array(
					'target' => '_self',
					'name' => 'pinPaypal',
					'id' => 'pinPaypal',
					'business' => $config_arr['p_paypal_address'],
					'item_name' => $arr['uuid'],
					'custom' => $arr['uuid'],
					'amount' => $arr['paid_deposit'],
					'currency_code' => $arr['currency'],
					'return' => PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionView&uuid=' . $arr['uuid'],
					'notify_url' => PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionConfirmPaypal&cid=' . $arr['foreign_id']
				));
				break;
			case 'authorize':
				$this->set('params', array(
					'name' => 'pinAuthorize',
					'id' => 'pinAuthorize',
					'timezone' => $config_arr['p_authorize_tz'],
					'transkey' => $config_arr['p_authorize_key'],
					'x_login' => $config_arr['p_authorize_mid'],
					'x_description' => $arr['uuid'],
					'x_amount' => $arr['paid_deposit'],
					'x_invoice_num' => $arr['uuid'],
					'x_receipt_link_url' => PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionView&uuid=' . $arr['uuid'],
					'x_relay_url' => PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionConfirmAuthorize&cid=' . $arr['foreign_id']
				));
				break;
            case 'bambora':
                if(!empty($arr['b_country']))
                {
                    $country = pjCountryModel::factory()->find($arr['b_country'])->getData();
                    $arr['b_country_alpha_2'] = $country? $country['alpha_2']: 'CA';
                }
                else
                {
                    $arr['b_country_alpha_2'] = 'CA';
                }
                $this->set('arr', $arr);
				$this->set('params', array(
					'target' => '_self',
					'name' => 'pinBambora',
					'id' => 'pinBambora',
					'p_bambora_hash_algorithm' => $config_arr['p_bambora_hash_algorithm'],
					'p_bambora_hash' => $config_arr['p_bambora_hash'],
					'item_name' => $arr['uuid'],
					'custom' => $arr['uuid'],
                    'amount' => $arr['paid_deposit'],
                    'return' => PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionView&uuid=' . $arr['uuid'],
                    'notify_url' => PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionConfirmBambora&cid=' . $arr['foreign_id']
				));
				break;
		}
		
		$this
			->set('config_arr', $config_arr)
			->resetCss()
			->resetJs()
			->appendCss('invoice.css', $this->getConst('PLUGIN_CSS_PATH'))
		;
	}
	
	public function pjActionPrint()
	{
		$this->pjActionView();
	}
	
	public function pjActionSaveItem()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			$pjInvoiceItemModel = pjInvoiceItemModel::factory();
			if (!in_array($_POST['column'], $pjInvoiceItemModel->getI18n()))
			{
				$pjInvoiceItemModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjInvoiceItem');
			}
		}
		exit;
	}
	
	public function pjActionSend()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && $this->isLoged() && $this->isInvoiceReady())
		{
			if (isset($_GET['uuid']) && !empty($_GET['uuid']))
			{
				$arr = pjInvoiceModel::factory()
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.y_country AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.b_country AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t4.model='pjCountry' AND t4.foreign_id=t1.s_country AND t4.field='name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->select("t1.*, t2.content as y_country_title, t3.content as b_country_title, t3.content as s_country_title, 
						AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS cc_type, 
						AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS cc_num, 
						AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS cc_exp_month, 
						AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS cc_exp_year,
						AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS cc_code")
					->where('t1.uuid', $_GET['id'])
					->where('t1.order_id', $_GET['uuid'])
					->limit(1)->findAll()->getDataIndex(0);
				$this->set('arr', $arr);
				$this->set('config_arr', pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId()));
			}
			
			if (isset($_POST['uuid']) && !empty($_POST['uuid']))
			{
				// Validate data
				$b_send = (isset($_POST['b_send']) && isset($_POST['b_email']) && !empty($_POST['b_email']) && pjValidation::pjActionEmail($_POST['b_email']));
				$s_send = (isset($_POST['s_send']) && isset($_POST['s_email']) && !empty($_POST['s_email']) && pjValidation::pjActionEmail($_POST['s_email']));
				if (!$b_send && !$s_send)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Email(s) not selected.'));
				}
				
				// Build message
				$arr = pjInvoiceModel::factory()				->join('pjMultiLang', sprintf("t2.model='pjCountry' AND t2.foreign_id=t1.y_country AND t2.field='name' AND t2.locale='%u'", $this->getLocaleId()), 'left outer')								->join('pjMultiLang', sprintf("t3.model='pjCountry' AND t3.foreign_id=t1.b_country AND t3.field='name' AND t3.locale='%u'", $this->getLocaleId()), 'left outer')								->join('pjMultiLang', sprintf("t4.model='pjCountry' AND t4.foreign_id=t1.s_country AND t4.field='name' AND t4.locale='%u'", $this->getLocaleId()), 'left outer')								->select("t1.*, t2.content as y_country_title, t3.content as b_country_title, t3.content as s_country_title,				    						AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS cc_type,				    						AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS cc_num,				    						AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS cc_exp_month,				    						AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS cc_exp_year,				    						AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS cc_code")				->where('t1.uuid', $_POST['id'])->where('t1.order_id', $_POST['uuid'])->limit(1)->findAll()->getDataIndex(0);
				if ($arr === FALSE || empty($arr))
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Invoice not found.'));
				}
				$arr['items'] = pjInvoiceItemModel::factory()				->select('t1.*, t2.tax')				->join('pjInvoiceTax', 't2.id=t1.tax_id', 'left outer')				->where('t1.invoice_id', $arr['id'])->findAll()->getData();
				$confi_arr = pjInvoiceConfigModel::factory()->find(1)->getData();
				$arr['y_logo'] = '<img src="'.PJ_INSTALL_URL.$confi_arr['y_logo'].'" />';
				$arr['o_use_qty_unit_price'] = $confi_arr['o_use_qty_unit_price'];
				
				$view_url = PJ_INSTALL_URL . 'index.php?controller=pjInvoice&action=pjActionView&id=' . $_POST['id'] . '&uuid=' . $_POST['uuid'];
				$view_url = '<a href="'.$view_url.'">'.$view_url.'</a>';
				// Send message
				$pjEmail = new pjEmail();				if ($this->option_arr['o_send_email'] == 'smtp')				{				    $pjEmail				    ->setTransport('smtp')				    ->setSmtpHost($this->option_arr['o_smtp_host'])				    ->setSmtpPort($this->option_arr['o_smtp_port'])				    ->setSmtpUser($this->option_arr['o_smtp_user'])				    ->setSmtpPass($this->option_arr['o_smtp_pass'])				    ;				}				$pjEmail->setContentType('text/html');
				$message = '';
				if($arr['status'] == 'not_paid')
				{
					$message .= '<p>'.__('plugin_invoice_i_send_invoice_link', true).'</p>';
					$message .= $view_url . '<br/><br/><br/><br/>';
				}
				$message .= $this->pjActionTokenizer($arr);				$admin_email = $this->getAdminEmail();
				$result = $pjEmail					->setTo($_POST['b_email'])					->setFrom($admin_email, $this->option_arr['o_email_sender'])					->setSubject(__('plugin_invoice_send_subject', true))					->send($message);
				if ($result)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent.'));
				} else {
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Email has not been sent.'));
				}
			}
		}
	}
	
	private function pjActionTokenizer($a, $locale_id=NULL)
	{
		if (is_null($locale_id))
		{
			$locale_id = !empty($a['locale_id']) ? $a['locale_id'] : $this->getLocaleID();
		}
		
		$config = pjInvoiceConfigModel::factory()->getConfigData($locale_id);				$payment_methods = __('payment_methods', true);		$pm = isset($config[$a['payment_method']]) && !empty($config[$a['payment_method']]) ? $config[$a['payment_method']] : $payment_methods[$a['payment_method']];
		$tax_rate = 0;		$tax_rate_arr = pjInvoiceTaxModel::factory()->where('t1.is_default', 1)->limit(1)->findAll()->getDataIndex(0);		if ($tax_rate_arr) {		    $tax_rate = $tax_rate_arr['tax'];		}
		$items = "";		$items_tax = array();
		if (isset($a['items']) && is_array($a['items']) && !empty($a['items']))
		{
			$items .= '<table style="width: 100%; border-collapse: collapse">';
			$items .= '<tr>';
			$items .= '<td style="border-bottom: solid 1px #000; font-weight: 600;">'.__('plugin_invoice_i_description', true).'</td>';
			if($a['o_use_qty_unit_price'] == 1)
			{
				$items .= '<td style="border-bottom: solid 1px #000; text-align: right; font-weight: 600;">'.__('plugin_invoice_i_qty', true).'</td>';
				$items .= '<td style="border-bottom: solid 1px #000; text-align: right; font-weight: 600;">'.__('plugin_invoice_i_unit', true).'</td>';
			}			$items .= '<td></td>';
			$items .= '<td style="border-bottom: solid 1px #000; text-align: right; font-weight: 600;">'.__('plugin_invoice_i_amount', true).'</td>';
			$items .= '</tr>';
			foreach ($a['items'] as $item)
			{
				$items .= '<tr>';				$description = !empty($item['description']) ? nl2br($item['description']) : '';
				$items .= sprintf('<td style="border-top: solid 1px #000; ">%s<br>%s</td>', $item['name'], $description);
				if($a['o_use_qty_unit_price'] == 1)
				{
					$items .= sprintf('<td style="text-align: right; border-top: solid 1px #000; ">%s</td>', number_format($item['qty'], (int) $config['o_qty_is_int'] === 0 ? 2 : 0));
					$items .= sprintf('<td style="text-align: right; border-top: solid 1px #000; ">%s</td>', number_format($item['unit_price'], 2));
				}				if ((float)$item['amount'] > 0) {				    $items .= sprintf('<td style="text-align: right; border-top: solid 1px #000; ">%s</td>', $item['tax'].'%');				} else {				    $items .= sprintf('<td style="text-align: right; border-top: solid 1px #000; ">%s</td>', '');				}
				$items .= sprintf('<td style="text-align: right; border-top: solid 1px #000; ">%s</td>', number_format($item['amount'], 2));
				$items .= '</tr>';				if ((float)$item['tax'] > 0) {				    if (!isset($items_tax[$item['tax']])) {				        $items_tax[$item['tax']] = 0;				    }				    $price = pjAppController::getPriceBeforeTax($item['amount'], $item['tax']);				    $items_tax[$item['tax']] += $item['amount'] - $price;				}
			}
			$items .= '</table>';
		}		$tax_rates = '';		foreach ($items_tax as $k => $v) {		    $tax_rates .= '<tr><td class="label">'.__('lblInvoiceTax', true).' '.$k.'%:</td><td class="value">'.round($v, 2, PHP_ROUND_HALF_UP).'</td></tr>';		}		
		$statuses = __('plugin_invoice_statuses', true);
		$_yesno = __('plugin_invoice_yesno', true);		$y_logo = '';		if (!empty($config['y_logo'])) {		    $y_logo = '<img src="'.PJ_INSTALL_URL.$config['y_logo'].'" style="max-height: 100%;" />';		}
		return str_replace(
			array(
				'{uuid}',
				'{order_id}',
				'{issue_date}',
				'{due_date}',
				'{created}',
				'{modified}',
				'{status}',
				'{subtotal}',
				'{discount}',
				'{tax}',
				'{shipping}',
				'{total}',
				'{paid_deposit}',
				'{amount_due}',
				'{currency}',
				'{notes}',
				'{y_logo}',
				'{y_company}',
				'{y_name}',
				'{y_street_address}',
				'{y_country}',
				'{y_city}',
				'{y_state}',
				'{y_zip}',
				'{y_phone}',
				'{y_fax}',
				'{y_email}',
				'{y_url}',
				'{b_billing_address}',
				'{b_company}',
				'{b_name}',
				'{b_address}',
				'{b_street_address}',
				'{b_country}',
				'{b_city}',
				'{b_state}',
				'{b_zip}',
				'{b_phone}',
				'{b_fax}',
				'{b_email}',
				'{b_url}',
				'{s_shipping_address}',
				'{s_company}',
				'{s_name}',
				'{s_address}',
				'{s_street_address}',
				'{s_country}',
				'{s_city}',
				'{s_state}',
				'{s_zip}',
				'{s_phone}',
				'{s_fax}',
				'{s_email}',
				'{s_url}',
				'{s_date}',
				'{s_terms}',
				'{s_is_shipped}',
				'{items}',                			    '{y_tax_number}',			    '{y_bank_name}',			    '{y_iban}',			    '{y_bic}',			    '{b_company_name}',			    '{b_tax_number}',			    '{payment_method}',			    '{y_company_reg_no}',			    '{tax_rate}',			    '{tax_rates}'
			),
			array(
				$a['uuid'],
				$a['order_id'],
				pjUtil::formatDate($a['issue_date'], 'Y-m-d', $this->option_arr['o_date_format']),
				pjUtil::formatDate($a['due_date'], 'Y-m-d', $this->option_arr['o_date_format']),
				!empty($a['created']) ? date($this->option_arr['o_date_format'] . " H:i:s", strtotime($a['created'])) : NULL,
				!empty($a['modified']) ? date($this->option_arr['o_date_format'] . " H:i:s", strtotime($a['modified'])) : NULL,
				$statuses[$a['status']],
			    number_format($a['subtotal'], 2),
			    number_format($a['discount'], 2),
			    number_format($a['tax'], 2),
			    number_format($a['shipping'], 2),
			    number_format($a['total'], 2),
			    number_format($a['paid_deposit'], 2),
			    number_format($a['amount_due'], 2),
				$a['currency'],
				$a['notes'],
			    $y_logo,
			    $config['y_company'],
			    $config['y_name'],
			    $config['y_street_address'],
			    $a['y_country_title'],
			    $config['y_city'],
			    $config['y_state'],
			    $config['y_zip'],
			    $config['y_phone'],
			    $config['y_fax'],
			    $config['y_email'],
			    $config['y_url'],
				$a['b_billing_address'],
				$a['b_company'],
				$a['b_name'],
				$a['b_address'],
				$a['b_street_address'],
				$a['b_country_title'],
				$a['b_city'],
				$a['b_state'],
				$a['b_zip'],
				$a['b_phone'],
				$a['b_fax'],
				$a['b_email'],
				$a['b_url'],
				$a['s_shipping_address'],
				$a['s_company'],
				$a['s_name'],
				$a['s_address'],
				$a['s_street_address'],
				$a['s_country_title'],
				$a['s_city'],
				$a['s_state'],
				$a['s_zip'],
				$a['s_phone'],
				$a['s_fax'],
				$a['s_email'],
				$a['s_url'],
				pjUtil::formatDate($a['s_date'], 'Y-m-d', $this->option_arr['o_date_format']),
				$a['s_terms'],
				$_yesno[$a['s_is_shipped']],
				$items,			    $config['y_tax_number'],			    $config['y_bank_name'],			    $config['y_iban'],			    $config['y_bic'],			    $a['b_company'],			    $a['b_tax_number'],			    $pm,			    $config['y_company_reg_no'],			    $tax_rate,			    $tax_rates
			),
			$config['y_template']
		);
	}
	
	public function pjActionSaveInvoice()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$value = $_POST['value'];				
			pjInvoiceModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $value));
		}
		exit;
	}
	
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if (!$this->isInvoiceReady())
		{
			$this->set('status', 2);
			return;
		}
		
		$pjInvoiceModel = pjInvoiceModel::factory();
		
		if (isset($_POST['invoice_update']))
		{
			$arr = $pjInvoiceModel->find($_POST['id'])->getData();
			if (empty($arr))
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionInvoices&err=PIN04");
			}
			
			$data = array();
			$data['foreign_id'] = $arr['foreign_id'];
			$data['modified'] = ':NOW()';
			$data['issue_date'] = !empty($_POST['issue_date']) ? pjUtil::formatDate($_POST['issue_date'], $this->option_arr['o_date_format']) : NULL;
			$data['due_date'] = !empty($_POST['due_date']) ? pjUtil::formatDate($_POST['due_date'], $this->option_arr['o_date_format']) : NULL;
			$data['s_date'] = !empty($_POST['s_date']) ? pjUtil::formatDate($_POST['s_date'], $this->option_arr['o_date_format']) : NULL;
			$data = array_merge($_POST, $data);
			if (!$pjInvoiceModel->validates($data))
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionInvoices&err=PIN06");
			}
			$pjInvoiceModel->set('id', $_POST['id'])->modify($data);
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionUpdate&id=".$_POST['id']."&err=PIN05");
		}
		
		$arr = $pjInvoiceModel->find($_GET['id'])->getData();
		if (empty($arr))
		{
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionInvoices&err=PIN04");
		}
		$this->set('country_arr', pjCountryModel::factory()
			->select('t1.*, t2.content AS `name`')
			->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->orderBy('`name` ASC')
			->findAll()->getData());
		$this
			->set('arr', $arr)
			->set('config_arr', pjInvoiceConfigModel::factory()->getConfigData($this->getLocaleId()))
			->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/')
			->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
			->appendJs('pjInvoice.js', $this->getConst('PLUGIN_JS_PATH'))
			->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true)
		;
	}

	public function pjActionView()
	{
		$this->setLayout('pjActionEmpty');
		
		$arr = pjInvoiceModel::factory()
			->join('pjMultiLang', sprintf("t2.model='pjCountry' AND t2.foreign_id=t1.y_country AND t2.field='name' AND t2.locale='%u'", $this->getLocaleId()), 'left outer')
			->join('pjMultiLang', sprintf("t3.model='pjCountry' AND t3.foreign_id=t1.b_country AND t3.field='name' AND t3.locale='%u'", $this->getLocaleId()), 'left outer')
			->join('pjMultiLang', sprintf("t4.model='pjCountry' AND t4.foreign_id=t1.s_country AND t4.field='name' AND t4.locale='%u'", $this->getLocaleId()), 'left outer')
			->select("t1.*, t2.content as y_country_title, t3.content as b_country_title, t3.content as s_country_title, 
						AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS cc_type, 
						AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS cc_num, 
						AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS cc_exp_month, 
						AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS cc_exp_year,
						AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS cc_code")
			->where('t1.uuid', @$_GET['id'])
			->where('t1.order_id', @$_GET['uuid'])
			->limit(1)
			->findAll()
			->getDataIndex(0);
		
		if ($arr === FALSE || empty($arr))
		{
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjInvoice&action=pjActionInvoices&err=PIN04");
		}

		$arr['items'] = pjInvoiceItemModel::factory()		->select('t1.*, t2.tax')		->join('pjInvoiceTax', 't2.id=t1.tax_id', 'left outer')		->where('t1.invoice_id', $arr['id'])->findAll()->getData();
		$locale_id = !empty($arr['locale_id']) ? $arr['locale_id'] : $this->getLocaleId();
		$confi_arr = pjInvoiceConfigModel::factory()->getConfigData($locale_id);
		$arr['y_logo'] = '<img src="'.PJ_INSTALL_URL.$confi_arr['y_logo'].'" />';
		$arr['o_use_qty_unit_price'] = $confi_arr['o_use_qty_unit_price'];
		$this
			->set('arr', $arr)
			->set('config_arr', $confi_arr)
			->set('template', $this->pjActionTokenizer($arr, $locale_id))
			->resetCss()
			->resetJs()
			->appendJs('jquery.min.js', PJ_THIRD_PARTY_PATH . 'jquery/')
			->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
			->appendJs('pjInvoice.js', $this->getConst('PLUGIN_JS_PATH'))
			->appendCss('invoice.css', $this->getConst('PLUGIN_CSS_PATH'))
		;
	}	protected function recalPrice($invoice_id, $option_arr) {	    $invoice_arr = pjInvoiceModel::factory()->reset()->find($invoice_id)->getData();	    $invoice_item_arr = pjInvoiceItemModel::factory()->reset()->select('t1.*, t3.tax AS tax_rate')	    ->join('pjInvoice', 't2.id=t1.invoice_id', 'inner')	    ->join('pjInvoiceTax', 't3.id=t1.tax_id', 'left outer')	    ->where('t1.invoice_id', $invoice_id)	    ->findAll()->getData();	    	    $voucher_code = $invoice_arr['voucher_code'];	    $sub_total_before_tax = $tax = $sub_total = 0;	    foreach ($invoice_item_arr as $ii) {	        $sub_total += $ii['amount'];	        if ($ii['tax_rate'] > 0) {	            $amount_before_tax = pjAppController::getPriceBeforeTax($ii['amount'], $ii['tax_rate']);	            $sub_total_before_tax += $amount_before_tax;	            $tax += $ii['amount'] - $amount_before_tax;	        } else {	            $sub_total_before_tax += $ii['amount'];	            $tax += 0;	        }	    }	    	    $discount_arr = pjAppController::getDiscount($sub_total, $voucher_code, $option_arr['o_currency']);	    $discount = $discount_arr['status'] == 'OK'? $discount_arr['discount']: 0;	    $discount = round($discount);	    $total = round($sub_total_before_tax + $tax - $discount);	    $amount_due = $total - $invoice_arr['paid_deposit'];	    return array(	        'subtotal' => round($sub_total_before_tax, 2, PHP_ROUND_HALF_UP), 	        'discount' => round($discount, 2, PHP_ROUND_HALF_UP), 	        'tax' => round($tax, 2, PHP_ROUND_HALF_UP), 	        'total' => round($total, 2, PHP_ROUND_HALF_UP), 	        'amount_due' => round($amount_due, 2, PHP_ROUND_HALF_UP)
	    );	}
}
?>