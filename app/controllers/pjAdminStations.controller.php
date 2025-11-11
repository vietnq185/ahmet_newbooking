<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminStations extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['station_create']))
			{
				$data = array();
				$latlng = $this->getGeocode($_POST['address']);
				$data['lat'] = $latlng['lat'];
				$data['lng'] = $latlng['lng'];
				$id = pjStationModel::factory(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjStation', 'data');
					}
					
					if(isset($_POST['index_arr']) && $_POST['index_arr'] != '')
					{
						$pjStationFeeModel = pjStationFeeModel::factory();
						$index_arr = explode("|", $_POST['index_arr']);
						foreach($index_arr as $k => $v)
						{
							$p_data = array();
							$p_data['station_id'] = $id;
							$p_data['start'] = $_POST['start'][$v];
							$p_data['end'] = $_POST['end'][$v];
							$p_data['price'] = $_POST['price'][$v];
							$pjStationFeeModel->reset()->setAttributes($p_data)->insert();
						}
					}					
					$err = 'ASTA03';
				} else {
					$err = 'ASTA04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminStations&action=pjActionIndex&err=$err");
			} else {
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
				}

                $this->set('extra_arr', pjExtraModel::factory()
                    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.field='info' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->select("t1.*, t2.content as name, t3.content as info")
                    ->where('t1.status', 'T')
                    ->orderBy("t1.id ASC")
                    ->findAll()
                    ->getData());
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminStations.js');
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
			if (pjStationModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjStationFeeModel::factory()->where('station_id', $_GET['id'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjStation')->where('foreign_id', $_GET['id'])->eraseAll();
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
				pjStationModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjStationFeeModel::factory()->whereIn('station_id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjStation')->whereIn('foreign_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExport()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjStationModel::factory()->select('t1.id, t2.content AS name, t1.address, t1.start_fee')
				->join('pjMultiLang', "t2.model='pjStation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->whereIn('t1.id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Stations-".time().".csv")
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
			$pjStationModel = pjStationModel::factory()
				->join('pjMultiLang', "t2.model='pjStation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjStationModel->where('t1.address LIKE', "%$q%");
				$pjStationModel->orWhere('t2.content LIKE', "%$q%");
			}

			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjStationModel->where('t1.status', $_GET['status']);
			}
				
			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjStationModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjStationModel
				->select("t1.*, t2.content AS name")
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
			foreach($data as $k => $v)
			{
				$v['name'] = pjSanitize::clean($v['name']);
				$v['start_fee_formated'] = pjUtil::formatCurrencySign(number_format($v['start_fee'], 2), $this->option_arr['o_currency']);
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
			$this->appendJs('pjAdminStations.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSave()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjStationModel = pjStationModel::factory();
			
			if (!in_array($_POST['column'], $pjStationModel->i18n))
			{
				$value = $_POST['value'];
				$pjStationModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjStation', 'data');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['station_update']))
			{
				$data = array();
				$latlng = $this->getGeocode($_POST['address']);
				$data['lat'] = $latlng['lat'];
				$data['lng'] = $latlng['lng'];
				pjStationModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjStation', 'data');
				}
				
				$pjStationFeeModel = pjStationFeeModel::factory();				
				if(isset($_POST['index_arr']) && $_POST['index_arr'] != '')
				{
					$index_arr = explode("|", $_POST['index_arr']);			
					foreach($index_arr as $k => $v)
					{
						if(strpos($v, 'new') !== false)
						{
							$p_data = array();
							$p_data['station_id'] = $_POST['id'];
							$p_data['start'] = $_POST['start'][$v];
							$p_data['end'] = $_POST['end'][$v];
							$p_data['price'] = $_POST['price'][$v];
							$pjStationFeeModel->reset()->setAttributes($p_data)->insert();
							
						}else{
							$p_data = array();
							$p_data['station_id'] = $_POST['id'];
							$p_data['start'] = $_POST['start'][$v];
							$p_data['end'] = $_POST['end'][$v];
							$p_data['price'] = $_POST['price'][$v];
							$pjStationFeeModel->reset()->where('id', $v)->limit(1)->modifyAll($p_data);
						}
					}
				}
				
				if(isset($_POST['remove_arr']) && $_POST['remove_arr'] != '')
				{
					$remove_arr = explode("|", $_POST['remove_arr']);
					$pjStationFeeModel->reset()->whereIn('id', $remove_arr)->eraseAll();
				}
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminStations&action=pjActionIndex&err=ASTA01");
				
			} else {
				$arr = pjStationModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminStations&action=pjActionIndex&err=ASTA02");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjStation');
				$this->set('arr', $arr);

				$price_arr = pjStationFeeModel::factory()->where('station_id', $_GET['id'])->findAll()->getData();
				$this->set('price_arr', $price_arr);
				
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
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminStations.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>