<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminAreas extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['area_create']))
			{
				$data = array();
				$id = pjAreaModel::factory($_POST)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					if (isset($_POST['data']))
					{
						$pjAreaCoordModel = pjAreaCoordModel::factory();
						foreach ($_POST['data'] as $type => $coords)
						{
							foreach ($coords as $hash => $d)
							{
								$arr = $pjAreaCoordModel->where('t1.tmp_hash', $hash)->limit(1)->findAll()->getDataIndex(0);
								if ($arr) {
									$pjAreaCoordModel->reset()->set('id', $arr['id'])->modify(array(
										'area_id' => $id,
										'type' => $type,
										'tmp_hash' => ':NULL',
										'data' => $d
									));
								} else {
									$pjAreaCoordModel->reset()->setAttributes(array(
										'area_id' => $id,
										'type' => $type,
										'tmp_hash' => ':NULL',
										'data' => $d
									))->insert();
								}
							}
						}
					}
					
					$err = 'AAREA03';
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjArea', 'data');
					}
				} else {
					$err = 'AAREA04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminAreas&action=pjActionIndex&err=$err");
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
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$default_country_arr = pjCountryModel::factory()
						->select('t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`country_title` ASC')
						->find((int)$this->option_arr['o_default_country'])
						->getData();
				$default_country_name = $default_country_arr ? $default_country_arr['country_title'] : 'United States';
				$latlng = $this->getGeocode($default_country_name);
				$default_lat = $latlng['lat'] != '' ? $latlng['lat'] : '40.65';
				$default_lng = $latlng['lng'] != '' ? $latlng['lng'] : '-73.95';
				$this->set('default_lat', $default_lat)
					->set('default_lng', $default_lng);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('js?key='.$this->option_arr['o_google_api_key'].'&sensor=false&libraries=drawing', 'https://maps.googleapis.com/maps/api/', TRUE);
				$this->appendJs('pjAdminAreas.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
		
	public function pjActionDeleteArea()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjAreaModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjArea')->where('foreign_id', $_GET['id'])->eraseAll();
				$area_coors = pjAreaCoordModel::factory()->where('area_id', $_GET['id'])->findAll()->getDataPair(null, 'id');
				if ($area_coors) {
					pjMultiLangModel::factory()->reset()->where('model', 'pjAreaCoord')->whereIn('foreign_id', $area_coors)->eraseAll();
				}
				pjAreaCoordModel::factory()->reset()->where('area_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteAreaBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjAreaModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjArea')->whereIn('foreign_id', $_POST['record'])->eraseAll();
								
				$area_coors = pjAreaCoordModel::factory()->whereIn('area_id', $_POST['record'])->findAll()->getDataPair(null, 'id');
				if ($area_coors) {
					pjMultiLangModel::factory()->reset()->where('model', 'pjAreaCoord')->whereIn('foreign_id', $area_coors)->eraseAll();
				}
				pjAreaCoordModel::factory()->reset()->whereIn('area_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionGetArea()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjAreaModel = pjAreaModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjArea' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjAreaModel->where('(t2.content LIKE "%'.$q.'%")');
			}

			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjAreaModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 20;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$tblMultiLang = pjMultiLangModel::factory()->getTable();
			$tblAreaCoord = pjAreaCoordModel::factory()->getTable();
			$data = $pjAreaModel->select('t1.*, t2.content AS name,
			(SELECT GROUP_CONCAT(ml.content SEPARATOR "<br/>") FROM `'.$tblMultiLang.'` as ml LEFT OUTER JOIN `'.$tblAreaCoord.'` AS ac ON ac.id=ml.foreign_id AND ml.model="pjAreaCoord" AND ml.field="place_name" AND ml.locale = "'.$this->getLocaleId().'" WHERE ac.area_id=t1.id) AS places')
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
				
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
			$this->appendJs('pjAdminAreas.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveArea()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjAreaModel = pjAreaModel::factory();
			if (!in_array($_POST['column'], $pjAreaModel->getI18n()))
			{
				$pjAreaModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjArea', 'data');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			if (isset($_POST['area_update']))
			{
				pjAreaModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll($_POST);
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjArea', 'data');
				}
				$pjAreaCoordModel = pjAreaCoordModel::factory();
				if (isset($_POST['data']))
				{
					foreach ($_POST['data'] as $type => $coords)
					{
						foreach ($coords as $hash => $d)
						{
							if (strpos($hash, 'new_') !== false) {
								$arr = $pjAreaCoordModel->where('t1.tmp_hash', $hash)->limit(1)->findAll()->getDataIndex(0);
							} else {
								$arr = $pjAreaCoordModel->find($hash)->getData();
							}
							if ($arr) {
								$pjAreaCoordModel->reset()->set('id', $arr['id'])->modify(array(
									'area_id' => $_POST['id'],
									'type' => $type,
									'tmp_hash' => ':NULL',
									'data' => $d
								));
							} else {
								$pjAreaCoordModel->reset()->setAttributes(array(
									'area_id' => $_POST['id'],
									'type' => $type,
									'tmp_hash' => ':NULL',
									'data' => $d
								))->insert();
							}
						}
					}
				}
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminAreas&action=pjActionIndex&err=AAREA01");
				
			} else {
				$arr = pjAreaModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminAreas&action=pjActionIndex&err=AAREA08");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjArea');
				$this->set('arr', $arr);
				$this->set('coord_arr', pjAreaCoordModel::factory()->where('area_id', $_GET['id'])->findAll()->getData());
				
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
				
				$default_country_arr = pjCountryModel::factory()
						->select('t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`country_title` ASC')
						->find((int)$this->option_arr['o_default_country'])
						->getData();
				$default_country_name = $default_country_arr ? $default_country_arr['country_title'] : 'United States';
				$latlng = $this->getGeocode($default_country_name);
				$default_lat = $latlng['lat'] != '' ? $latlng['lat'] : '40.65';
				$default_lng = $latlng['lng'] != '' ? $latlng['lng'] : '-73.95';
				$this->set('default_lat', $default_lat)
					->set('default_lng', $default_lng);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('js?key='.$this->option_arr['o_google_api_key'].'&sensor=false&libraries=drawing', 'https://maps.googleapis.com/maps/api/', TRUE);
				$this->appendJs('pjAdminAreas.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSetPlaceName() {
		$this->setAjax(true);
		if ($this->isXHR()) {
			$pjAreaCoordModel = pjAreaCoordModel::factory();
			$pjMultiLangModel = pjMultiLangModel::factory();

			$data = array(
				'icon' => $_POST['location_icon'],
				'is_airport' => $_POST['is_airport'],
			    'price_level' => $_POST['price_level']
			);
			$data['is_disabled'] = isset($_POST['is_disabled']) ? 1 : 0;
			if (strpos($_POST['coord_id'], 'new_') !== false) {
				$arr = $pjAreaCoordModel->where('t1.tmp_hash', $_POST['coord_id'])->limit(1)->findAll()->getDataIndex(0);
				if ($arr) {
					$pjAreaCoordModel->reset()->set('id', $arr['id'])->modify($data);
					$pjMultiLangModel->updateMultiLang($_POST['i18n'], $arr['id'], 'pjAreaCoord', 'data');
				} else {
					$data['tmp_hash'] = $_POST['coord_id'];
					$id = $pjAreaCoordModel->setAttributes($data)->insert()->getInsertId();
					if ($id !== false && (int)$id > 0) {
						$pjMultiLangModel->saveMultiLang($_POST['i18n'], $id, 'pjAreaCoord', 'data');	
					}
				}
			} else {
				$pjAreaCoordModel->reset()->set('id', $_POST['coord_id'])->modify($data);
				$pjMultiLangModel->updateMultiLang($_POST['i18n'], $_POST['coord_id'], 'pjAreaCoord', 'data');
			}
		}
		pjAppController::jsonResponse(array('status' => 'OK'));
	}
	
	public function pjActionGetPlaceName() {
		$this->setAjax(true);
		if ($this->isXHR()) {
			if (strpos($_GET['coord_id'], 'new_') !== false) {
				$arr = pjAreaCoordModel::factory()->where('t1.tmp_hash', $_GET['coord_id'])->limit(1)->findAll()->getDataIndex(0);
			} else {
				$arr = pjAreaCoordModel::factory()->find($_GET['coord_id'])->getData();
			}
			if ($arr) {
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjAreaCoord');
				pjAppController::jsonResponse(array('status' => 'OK', 'data' => $arr));
			}
			pjAppController::jsonResponse(array('status' => 'ERR'));
		}
		pjAppController::jsonResponse(array('status' => 'ERR'));
	}
	
	public function pjActionDeletePlace() {
		$this->setAjax(true);
		if ($this->isXHR()) {
			$pjAreaCoordModel = pjAreaCoordModel::factory();
			if (strpos($_POST['coord_id'], 'new_') !== false) {
				$arr = $pjAreaCoordModel->where('t1.tmp_hash', $_POST['coord_id'])->limit(1)->findAll()->getDataIndex(0);
			} else {
				$arr = $pjAreaCoordModel->find($_POST['coord_id'])->getData();
			}
			if ($arr) {
				if ($pjAreaCoordModel->reset()->setAttributes(array('id' => $arr['id']))->erase()->getAffectedRows() == 1)
				{
					pjMultiLangModel::factory()->where('model', 'pjAreaCoord')->where('foreign_id', $arr['id'])->eraseAll();
				}
			}
		}
		pjAppController::jsonResponse(array('status' => 'OK'));
	}
}
?>