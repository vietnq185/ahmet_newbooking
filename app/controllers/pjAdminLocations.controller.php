<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminLocations extends pjAdmin
{
	public $sessionLocation = 'pjLocation_session';
	
	public $sessionPrice = 'pjPrice_session';
	
	public function pjActionBeforeSave()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && ($this->isAdmin() || $this->isEditor()))
		{
			$response = array('status' => 'OK', 'code' => 200, 'text' => '');
			if (!isset($_SESSION[$this->sessionLocation]) || !is_array($_SESSION[$this->sessionLocation]))
			{
				$_SESSION[$this->sessionLocation] = array();
			}
				
			$_SESSION[$this->sessionLocation] = pjUtil::arrayMergeDistinct($_SESSION[$this->sessionLocation], $_POST);
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionSave()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && ($this->isAdmin() || $this->isEditor()))
		{
			if (!isset($_SESSION[$this->sessionLocation]) || empty($_SESSION[$this->sessionLocation]))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
			$STORE = $_SESSION[$this->sessionLocation];
			
			if(isset($STORE['location_create']))
			{
				$data = array();
				$data['is_airport'] = @$STORE['is_airport'];
				$data['icon'] = @$STORE['location_icon'];
				$data['order_index'] = @$STORE['order_index'];
				$data['address'] = @$STORE['address'];
				$data['area_id'] = @$STORE['pickup_area_id'];
				$latlng = $this->getGeocode(@$STORE['address']);
				$data['lat'] = $latlng['lat'];
				$data['lng'] = $latlng['lng'];
				$data['color'] = isset($STORE['color']) ? $STORE['color'] : ':NULL';
				$data['region'] = isset($STORE['location_region']) ? $STORE['location_region'] : ':NULL';
				$id = pjLocationModel::factory($data)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$dropoff_ids = array();
					if(isset($STORE['index_arr']) && $STORE['index_arr'] != '') {
						$index_arr = explode("|", $STORE['index_arr']);
						foreach($index_arr as $k => $v)
						{
							if (isset($STORE['dropoff_ids'][$v])) {
								$dropoff_ids[] = $STORE['dropoff_ids'][$v];
							}
						}
					}
										
					$pjPriceModel = pjPriceModel::factory();
					$pjMultiLangModel = pjMultiLangModel::factory();
					$pjDropoffModel = pjDropoffModel::factory();
					$pjDropoffAreaModel = pjDropoffAreaModel::factory();
					
					$price_arr = array();
					if ($dropoff_ids) {
						$_price_arr = $pjPriceModel->whereIn('t1.dropoff_id', array_unique($dropoff_ids))
							->findAll()
							->getData();
						foreach ($_price_arr as $price) {
							$price_arr[$price['dropoff_id']][] = $price;
						}
					}
					
					if (isset($STORE['i18n']))
					{
						if(isset($STORE['index_arr']) && $STORE['index_arr'] != '')
						{
							$index_arr = explode("|", $STORE['index_arr']);
								
							$pjPriceModel->reset()->begin();
							foreach($index_arr as $k => $v)
							{
								$d_data = array();
								$d_data['location_id'] = $id;
								$d_data['duration'] = @$STORE['duration'][$v];
								$d_data['distance'] = @$STORE['distance'][$v];
								$d_data['is_airport'] = @$STORE['airport'][$v];
								$d_data['icon'] = @$STORE['icon'][$v];
								$d_data['price_level'] = $STORE['price_level'][$v];
								$d_data['base_station_id'] = $STORE['base_station'][$v];
								$d_data['order_index'] = (!empty($STORE['order_index'][$v])) ? $STORE['order_index'][$v] : ':NULL';
								$d_data['region'] = isset($STORE['region'][$v]) ? $STORE['region'][$v] : ':NULL';
								$dropoff_id = $pjDropoffModel->reset()->setAttributes($d_data)->insert()->getInsertId();
								if ($dropoff_id !== false && (int) $dropoff_id > 0)
								{
									if (isset($STORE['area_id'][$v]) && count($STORE['area_id'][$v]) > 0) {
										$pjDropoffAreaModel->reset()->begin();
										foreach ($STORE['area_id'][$v] as $area_id) {
											$pjDropoffAreaModel->setAttributes(array('dropoff_id' => $dropoff_id, 'area_id' => $area_id))->insert();
										}
										$pjDropoffAreaModel->commit();
									}
									
									foreach ($STORE['i18n'] as $locale => $locale_arr)
									{
										foreach ($locale_arr as $field => $content)
										{
											if(is_array($content))
											{
												if (empty($content[$v])) {
													$content[$v] = $STORE['i18n'][$this->getLocaleId()][$field][$v];
												}
												$insert_id = $pjMultiLangModel->reset()->setAttributes(array(
														'foreign_id' => $dropoff_id,
														'model' => 'pjDropoff',
														'locale' => $locale,
														'field' => $field,
														'content' => $content[$v],
														'source' => 'data'
												))->insert()->getInsertId();
				
												if ($insert_id === FALSE || (int) $insert_id <= 0)
												{
													$pjMultiLangModel->reset()
														->where('foreign_id', $dropoff_id)
														->where('model', 'pjDropoff')
														->where('locale', $locale)
														->where('field', $field)
														->limit(1)
														->modifyAll(array('content' => $content[$v]));
												}
											}
										}
									}
									
									if (isset($STORE['dropoff_ids'][$v]) && isset($price_arr[$STORE['dropoff_ids'][$v]])) {
										foreach ($price_arr[$STORE['dropoff_ids'][$v]] as $val) {
											$pjPriceModel->setAttributes(array(
												'fleet_id' => $val['fleet_id'],
												'dropoff_id' => $dropoff_id,
												'price_1' => @$val['price_1'],
					                			'price_2' => @$val['price_2'],
												'price_3' => @$val['price_3'],
												'price_4' => @$val['price_4'],
												'price_5' => @$val['price_5'],
												'price_6' => @$val['price_6'],
												'price_7' => @$val['price_7']
											))->insert();
										}
									}
								}
							}
							$pjPriceModel->commit();
						}
						foreach ($STORE['i18n'] as $locale => $locale_arr)
						{
							foreach ($locale_arr as $field => $content)
							{
								if(!is_array($content))
								{
									if (empty($content)) {
										$content = $STORE['i18n'][$this->getLocaleId()][$field];
									}
									$insert_id = $pjMultiLangModel->reset()->setAttributes(array(
											'foreign_id' => $id,
											'model' => 'pjLocation',
											'locale' => $locale,
											'field' => $field,
											'content' => $content,
											'source' => 'data'
									))->insert()->getInsertId();
										
									if ($insert_id === FALSE || (int) $insert_id <= 0)
									{
										$pjMultiLangModel->reset()
											->where('foreign_id', $id)
											->where('model', 'pjLocation')
											->where('locale', $locale)
											->where('field', $field)
											->limit(1)
											->modifyAll(array('content' => $content));
									}
								}
							}
						}
					}
					
					$_SESSION[$this->sessionLocation] = NULL;
					unset($_SESSION[$this->sessionLocation]);
						
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'AL03', 'id' => $id));
				} else {
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'AL04'));
				}
			}
			else if(isset($STORE['location_update'])){
				
				$pjMultiLangModel = pjMultiLangModel::factory();
				$pjDropoffModel = pjDropoffModel::factory();
				$pjDropoffAreaModel = pjDropoffAreaModel::factory();

				$data = array();
				$data['is_airport'] = @$STORE['is_airport'];
				$data['icon'] = @$STORE['location_icon'];
				$data['order_index'] = @$STORE['order_index'];
				$data['address'] = @$STORE['address'];
				$data['area_id'] = @$STORE['pickup_area_id'];
				$latlng = $this->getGeocode(@$STORE['address']);
				$data['lat'] = $latlng['lat'];
				$data['lng'] = $latlng['lng'];
				$data['color'] = isset($STORE['color']) ? $STORE['color'] : ':NULL';
				$data['region'] = isset($STORE['location_region']) ? $STORE['location_region'] : ':NULL';
				$data['modified'] = date('Y-m-d H:i:s');
				pjLocationModel::factory()->where('id', $STORE['id'])->limit(1)->modifyAll($data);				
				if (isset($STORE['i18n']))
				{
					if(isset($STORE['index_arr']) && $STORE['index_arr'] != '')
					{
						$index_arr = explode("|", $STORE['index_arr']);
				
						foreach($index_arr as $k => $v)
						{
							if(strpos($v, 'tr') !== false)
							{
								$d_data = array();
								$d_data['location_id'] = $STORE['id'];
								$d_data['duration'] = @$STORE['duration'][$v];
								$d_data['distance'] = @$STORE['distance'][$v];
								$d_data['is_airport'] = @$STORE['airport'][$v];
								$d_data['icon'] = @$STORE['icon'][$v];
								$d_data['price_level'] = $STORE['price_level'][$v];
								$d_data['base_station_id'] = $STORE['base_station'][$v];
								$d_data['order_index'] = (!empty($STORE['order_index'][$v])) ? $STORE['order_index'][$v] : ':NULL';
								$d_data['region'] = isset($STORE['region'][$v]) ? $STORE['region'][$v] : ':NULL';
								$dropoff_id = $pjDropoffModel->reset()->setAttributes($d_data)->insert()->getInsertId();
								if ($dropoff_id !== false && (int) $dropoff_id > 0)
								{
									if (isset($STORE['area_id'][$v]) && count($STORE['area_id'][$v]) > 0) {
										$pjDropoffAreaModel->reset()->begin();
										foreach ($STORE['area_id'][$v] as $area_id) {
											$pjDropoffAreaModel->setAttributes(array('dropoff_id' => $dropoff_id, 'area_id' => $area_id))->insert();
										}
										$pjDropoffAreaModel->commit();
									}									
									
									foreach ($STORE['i18n'] as $locale => $locale_arr)
									{
										foreach ($locale_arr as $field => $content)
										{
											if(is_array($content))
											{
												if (empty($content[$v])) {
													$content[$v] = $STORE['i18n'][$this->getLocaleId()][$field][$v];
												}
												$insert_id = $pjMultiLangModel->reset()->setAttributes(array(
														'foreign_id' => $dropoff_id,
														'model' => 'pjDropoff',
														'locale' => $locale,
														'field' => $field,
														'content' => $content[$v],
														'source' => 'data'
												))->insert()->getInsertId();
											}
										}
									}
								}
							}else{
								$d_data = array();
								$d_data['location_id'] = @$STORE['id'];
								$d_data['duration'] = @$STORE['duration'][$v];
								$d_data['distance'] = @$STORE['distance'][$v];
								$d_data['is_airport'] = @$STORE['airport'][$v];
								$d_data['icon'] = @$STORE['icon'][$v];
								$d_data['price_level'] = $STORE['price_level'][$v];
								$d_data['base_station_id'] = $STORE['base_station'][$v];
								$d_data['order_index'] = (!empty($STORE['order_index'][$v])) ? $STORE['order_index'][$v] : ':NULL';
								$d_data['region'] = isset($STORE['region'][$v]) ? $STORE['region'][$v] : ':NULL';
								$pjDropoffModel->reset()->where('id', $v)->limit(1)->modifyAll($d_data);
				
								$pjDropoffAreaModel->reset()->where('dropoff_id', $v)->eraseAll();
								if (isset($STORE['area_id'][$v]) && count($STORE['area_id'][$v]) > 0) {
									$pjDropoffAreaModel->reset()->begin();
									foreach ($STORE['area_id'][$v] as $area_id) {
										$pjDropoffAreaModel->setAttributes(array('dropoff_id' => $v, 'area_id' => $area_id))->insert();
									}
									$pjDropoffAreaModel->commit();
								}
								
								foreach ($STORE['i18n'] as $locale => $locale_arr)
								{
									foreach ($locale_arr as $field => $content)
									{
										if(is_array($content))
										{
											if (empty($content[$v])) {
												$content[$v] = $STORE['i18n'][$this->getLocaleId()][$field][$v];
											}
											$sql = sprintf("INSERT INTO `%1\$s` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
												VALUES (NULL, :foreign_id, :model, :locale, :field, :update_content, :source)
												ON DUPLICATE KEY UPDATE `content` = :update_content, `source` = :source;",
													$pjMultiLangModel->getTable()
											);
											$foreign_id = $v;
											$model = 'pjDropoff';
											$source = 'data';
											$update_content = $content[$v];
											$modelObj = $pjMultiLangModel->reset()->prepare($sql)->exec(compact('foreign_id', 'model', 'locale', 'field', 'update_content', 'source'));
											if ($modelObj->getAffectedRows() > 0 || $modelObj->getInsertId() > 0)
											{
				
											}
										}
									}
								}
							}
						}
					}
						
					foreach ($STORE['i18n'] as $locale => $locale_arr)
					{
						foreach ($locale_arr as $field => $content)
						{
							if(!is_array($content))
							{
								if (empty($content)) {
									$content = $STORE['i18n'][$this->getLocaleId()][$field];
								}
								$sql = sprintf("INSERT INTO `%1\$s` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
									VALUES (NULL, :foreign_id, :model, :locale, :field, :content, :source)
									ON DUPLICATE KEY UPDATE `content` = :content, `source` = :source;",
										$pjMultiLangModel->getTable()
								);
								$foreign_id = $STORE['id'];
								$model = 'pjLocation';
								$source = 'data';
								$modelObj = $pjMultiLangModel->reset()->prepare($sql)->exec(compact('foreign_id', 'model', 'locale', 'field', 'content', 'source'));
								if ($modelObj->getAffectedRows() > 0 || $modelObj->getInsertId() > 0)
								{
										
								}
							}
						}
					}
				}
				
				if(isset($STORE['remove_arr']) && $STORE['remove_arr'] != '')
				{
					$remove_arr = explode("|", $STORE['remove_arr']);
						
					$pjMultiLangModel->reset()->where('model', 'pjDropoff')->whereIn('foreign_id', $remove_arr)->eraseAll();
					$pjDropoffModel->reset()->whereIn('id', $remove_arr)->eraseAll();
					pjPriceModel::factory()->whereIn('dropoff_id', $remove_arr)->eraseAll();
					$pjDropoffAreaModel->reset()->whereIn('dropoff_id', $remove_arr)->eraseAll();
				}
				$_SESSION[$this->sessionLocation] = NULL;
				unset($_SESSION[$this->sessionLocation]);
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'AL01', 'id' => $STORE['id']));
			}
		}
	}
	
	public function pjActionBeforeSavePrice()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && ($this->isAdmin() || $this->isEditor()))
		{
			$response = array('status' => 'OK', 'code' => 200, 'text' => '');
			if (!isset($_SESSION[$this->sessionPrice]) || !is_array($_SESSION[$this->sessionPrice]))
			{
				$_SESSION[$this->sessionPrice] = array();
			}
	
			$_SESSION[$this->sessionPrice] = pjUtil::arrayMergeDistinct($_SESSION[$this->sessionPrice], $_POST);
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionSavePrice()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && ($this->isAdmin() || $this->isEditor()))
		{
			if (!isset($_SESSION[$this->sessionPrice]) || empty($_SESSION[$this->sessionPrice]))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
			$STORE = $_SESSION[$this->sessionPrice];
			
			if(isset($STORE['location_update']))
			{
				$dropoff_arr = pjDropoffModel::factory()->where('location_id', $STORE['id'])->findAll()->getData();
				$fleet_arr = pjFleetModel::factory()->where('status', 'T')->findAll()->getData();
				$pjPriceModel = pjPriceModel::factory();

                foreach($dropoff_arr as $row)
                {
                    foreach($fleet_arr as $col)
                    {
                        $cnt = $pjPriceModel
                            ->reset()
                            ->where('dropoff_id', $row['id'])
                            ->where('fleet_id', $col['id'])
                            ->findCount()
                            ->getData();
                        $prices = array();
                        $prices['modified'] = date('Y-m-d H:i:s');
                        for($dayIndex = 1; $dayIndex <= 7; $dayIndex++)
                        {
                            //$prices["price_{$dayIndex}"] = !empty($STORE['price_' . $row['id'] . '_' . $col['id']][$dayIndex]) ? $STORE['price_' . $row['id'] . '_' . $col['id']][$dayIndex] : ':NULL';
                             /* price will be the same for all weekdays */
                            $prices["price_{$dayIndex}"] = !empty($STORE['price_' . $row['id'] . '_' . $col['id']][1]) ? $STORE['price_' . $row['id'] . '_' . $col['id']][1] : ':NULL';
                        }
                        if($cnt == 0)
                        {
                            $data = $prices;
                            $data['dropoff_id'] = $row['id'];
                            $data['fleet_id'] = $col['id'];
                            $pjPriceModel->reset()->setAttributes($data)->insert();
                        }else{
                            $pjPriceModel->reset()
                                ->where('dropoff_id', $row['id'])
                                ->where('fleet_id', $col['id'])
                                ->limit(1)
                                ->modifyAll($prices);
                        }
                    }
                }

				$_SESSION[$this->sessionPrice] = NULL;
				unset($_SESSION[$this->sessionPrice]);
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'AL09', 'id' => $STORE['id']));
			}
		}
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['location_create']))
			{
				
			} else {
				if(isset($_SESSION[$this->sessionLocation]))
				{
					$_SESSION[$this->sessionLocation] = NULL;
					unset($_SESSION[$this->sessionLocation]);
				}
				$pjMultiLangModel = pjMultiLangModel::factory();
				
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
				
				$fleet_arr = pjFleetModel::factory()
					->where('status', 'T')
					->findAll()
					->getData();
				foreach($fleet_arr as $k => $v)
				{
					$v['i18n'] = $pjMultiLangModel->reset()->getMultiLang($v['id'], 'pjFleet');
					$fleet_arr[$k] = $v;
				}
				$this->set('fleet_arr', $fleet_arr);
				
				$location_arr = pjLocationModel::factory()
					->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->select("t1.*, t2.content as pickup_location")
					->where('t1.status', 'T')
					->orderBy("pickup_location ASC")
					->findAll()->getData();
				$this->set('location_arr', $location_arr);
				
				$area_arr = pjAreaModel::factory()
					->join('pjMultiLang', "t2.model='pjArea' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->select("t1.*, t2.content as name")
					->where('t1.status', 'T')
					->orderBy("t1.order_index ASC, name ASC")
					->findAll()->getData();
				$this->set('area_arr', $area_arr);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'chosen/');
                $this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
                $this->appendJs('jquery.miniColors.min.js', PJ_THIRD_PARTY_PATH . 'mini_colors/');
                $this->appendCss('jquery.miniColors.css', PJ_THIRD_PARTY_PATH . 'mini_colors/');
				$this->appendJs('pjAdminLocations.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteLocation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			
			if (pjLocationModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				$pjDropoffModel = pjDropoffModel::factory();
				$pjMultiLangModel = pjMultiLangModel::factory();
				$pjMultiLangModel->where('model', 'pjLocation')->where('foreign_id', $_GET['id'])->eraseAll();
				$dropoff_id_arr = $pjDropoffModel->where('t1.location_id', $_GET['id'])->findAll()->getDataPair('id', 'id');
				if(!empty($dropoff_id_arr))
				{
					$pjMultiLangModel->reset()->where('model', 'pjDropoff')->whereIn('foreign_id', $dropoff_id_arr)->eraseAll();
				}
				$pjDropoffModel->reset()->where('location_id', $_GET['id'])->eraseAll();
				
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteLocationBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjDropoffModel = pjDropoffModel::factory();
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				$dropoff_id_arr = $pjDropoffModel->whereIn('t1.location_id', $_POST['record'])->findAll()->getDataPair('id', 'id');
				if(!empty($dropoff_id_arr))
				{
					$pjMultiLangModel->reset()->where('model', 'pjDropoff')->whereIn('foreign_id', $dropoff_id_arr)->eraseAll();
				}
				$pjDropoffModel->reset()->whereIn('location_id', $_POST['record'])->eraseAll();
				$pjMultiLangModel->where('model', 'pjLocation')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				pjLocationModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExportLocation()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjLocationModel::factory()->select('t1.id, t2.content as pickup_location')
											 ->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
											 ->whereIn('t1.id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Locations-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetLocation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjLocationModel = pjLocationModel::factory()
							->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
							->join('pjMultiLang', "t3.model='pjArea' AND t3.foreign_id=t1.area_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer');

			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjLocationModel->where('t2.content LIKE', "%$q%");
				$pjLocationModel->orWhere('t3.content LIKE', "%$q%");
			}
	
			$column = 'pickup_location';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjLocationModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjLocationModel->select(" t1.id, t1.address, t1.status, t2.content as pickup_location, t3.content as pickup_area")
								 ->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
				
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminLocations.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveLocation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjLocationModel = pjLocationModel::factory();
			if (!in_array($_POST['column'], $pjLocationModel->i18n))
			{
				$value = $_POST['value'];
				
				$pjLocationModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $value));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjLocation', 'data');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['location_update']))
			{
				$arr = pjLocationModel::factory()->find($_POST['id'])->getData();
				
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminLocations&action=pjActionIndex&err=AL08");
				}
			} else {
				if(isset($_SESSION[$this->sessionLocation]))
				{
					$_SESSION[$this->sessionLocation] = NULL;
					unset($_SESSION[$this->sessionLocation]);
				}
				$pjMultiLangModel = pjMultiLangModel::factory();
				$pjBookingModel = pjBookingModel::factory();
				$pjDropoffAreaModel = pjDropoffAreaModel::factory();
				
				$arr = pjLocationModel::factory()->find($_GET['id'])->getData();
				
				if (count($arr) === 0)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminLocations&action=pjActionIndex&err=AL08");
				}
				$arr['i18n'] = $pjMultiLangModel->getMultiLang($arr['id'], 'pjLocation');
				
				$dropoff_arr = pjDropoffModel::factory()->where('location_id', $_GET['id'])->findAll()->getData();
				foreach($dropoff_arr as $k => $v)
				{
					$dropoff_arr[$k]['i18n'] = $pjMultiLangModel->reset()->getMultiLang($v['id'], 'pjDropoff');
					$dropoff_arr[$k]['cnt'] = $pjBookingModel->reset()->where('dropoff_id', $v['id'])->findCount()->getData();
					$dropoff_arr[$k]['area_ids'] = $pjDropoffAreaModel->reset()->where('dropoff_id', $v['id'])->findAll()->getDataPair(null, 'area_id');
				}
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
				$this->set('arr', $arr);
				$this->set('dropoff_arr', $dropoff_arr);
				
				$fleet_arr = pjFleetModel::factory()
					->where('status', 'T')
					->findAll()
					->getData();
				foreach($fleet_arr as $k => $v)
				{
					$v['i18n'] = $pjMultiLangModel->reset()->getMultiLang($v['id'], 'pjFleet');
					$fleet_arr[$k] = $v;
				}
				$this->set('fleet_arr', $fleet_arr);
				
				$location_arr = pjLocationModel::factory()
					->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->select("t1.*, t2.content as pickup_location")
					->where('t1.status', 'T')
					->where('t1.id <>', $_GET['id'])
					->orderBy("pickup_location ASC")
					->findAll()->getData();
				$this->set('location_arr', $location_arr);
				
				$area_arr = pjAreaModel::factory()
					->join('pjMultiLang', "t2.model='pjArea' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->select("t1.*, t2.content as name")
					->where('t1.status', 'T')
					->orderBy("t1.order_index ASC, name ASC")
					->findAll()->getData();
				$this->set('area_arr', $area_arr);
				
				$station_arr = pjStationModel::factory()->select('t1.*, t2.content AS `name`')
				->join('pjMultiLang', "t2.model='pjStation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->orderBy('t2.content ASC')
				->findAll()->getData();
				$this->set('station_arr', $station_arr);
				
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('chosen.jquery.min.js', PJ_THIRD_PARTY_PATH . 'chosen/');
                $this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
                $this->appendJs('jquery.miniColors.min.js', PJ_THIRD_PARTY_PATH . 'mini_colors/');
                $this->appendCss('jquery.miniColors.css', PJ_THIRD_PARTY_PATH . 'mini_colors/');
				$this->appendJs('pjAdminLocations.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function getRepresentativeCoordinate($type, $data) {
	    $representative_point = null;
	    $clean_data = str_replace(['(', ')', ' '], '', $data);
	    
	    if ($type === 'circle') {
	        list($coord_str, ) = explode('|', $data);
	        $clean_coord = str_replace(['(', ')', ' '], '', $coord_str);
	        list($lat, $lng) = explode(',', $clean_coord);
	        
	        $representative_point = [
	            'lat' => (float)$lat,
	            'lng' => (float)$lng,
	            'source' => 'explicit_center'
	        ];
	        
	    } elseif ($type === 'rectangle' || $type === 'polygon') {
	        $parts = explode(',', $clean_data);
	        $num_coords = count($parts);
	        
	        if ($num_coords >= 4 && $num_coords % 2 === 0) {
	            $total_lat = 0;
	            $total_lng = 0;
	            $N = $num_coords / 2;
	            
	            for ($i = 0; $i < $num_coords; $i += 2) {
	                $total_lat += (float)$parts[$i]; 
	                $total_lng += (float)$parts[$i + 1];
	            }
	            
	            $center_lat = $total_lat / $N;
	            $center_lng = $total_lng / $N;
	            
	            $representative_point = [
	                'lat' => $center_lat,
	                'lng' => $center_lng,
	                'source' => 'calculated_centroid_N=' . $N
	            ];
	        }
	    }
	    
	    return $representative_point;
	}
	
	public function pjActionPrice()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
		    $pjFleetModel = pjFleetModel::factory();
		    
		    $pickup_arr = pjLocationModel::factory()->find($_GET['id'])->getData();
			$dropoff_arr = pjDropoffModel::factory()
				->join('pjMultiLang', "t2.model='pjDropoff' AND t2.foreign_id=t1.id AND t2.field='location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->select('t1.*, t2.content as location')
				->where('location_id', $_GET['id'])
				->findAll()->getData();	
			$dropoff_ids_arr = array();
			foreach ($dropoff_arr as $drop) {
			    $dropoff_ids_arr[] = $drop['id'];
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
			    $station_ids_arr = array();
			    foreach ($dropoff_place_arr as $place) {
			        $resp = $this->getRepresentativeCoordinate($place['type'], $place['data']);
			        $station_fee_arr = $this->getStationFee($pickup_arr['lat'], $pickup_arr['lng'], @$resp['lat'], @$resp['lng'], $place['dropoff_id']);
			        if (!in_array($station_fee_arr['station_id'], $station_ids_arr)) {
			            $station_ids_arr[] = $station_fee_arr['station_id'];
			        }
			    }
			    if ($station_ids_arr) {
			        $pjFleetModel->whereIn('t1.station_id', $station_ids_arr);
			    }
			}
				
			$fleet_arr = $pjFleetModel
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t3.model='pjStation' AND t3.foreign_id=t1.station_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->select('t1.*, t2.content as fleet, t3.content as station')
					->where('status', 'T')
					->findAll()
					->getData();	
			if (isset($_POST['location_update']))
			{
				
			} else {
				if(isset($_SESSION[$this->sessionPrice]))
				{
					$_SESSION[$this->sessionPrice] = NULL;
					unset($_SESSION[$this->sessionPrice]);
				}
				
				$pjMultiLangModel = pjMultiLangModel::factory();
				$pjPriceModel = pjPriceModel::factory();
				
				$arr = pjLocationModel::factory()
					->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='pickup_location' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->select('t1.*, t2.content as pickup_location')
					->find($_GET['id'])
					->getData();
				
				if (count($arr) === 0)
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminLocations&action=pjActionIndex&err=AL08");
				}
				
				$dropoff_id_arr = array();
				
				foreach($dropoff_arr as $k => $v)
				{
					$dropoff_id_arr[] = $v['id'];
				}
				
				$price_arr = array();
				if(!empty($dropoff_id_arr))
				{
					$_price_arr = $pjPriceModel
						->reset()
						->whereIn('t1.dropoff_id', $dropoff_id_arr)
						->findAll()
						->getData();
                    foreach($_price_arr as $v)
                    {
                        $price_arr[$v['dropoff_id'] . '_' . $v['fleet_id']] = array(
                            1 => $v['price_1'],
                            2 => $v['price_2'],
                            3 => $v['price_3'],
                            4 => $v['price_4'],
                            5 => $v['price_5'],
                            6 => $v['price_6'],
                            7 => $v['price_7'],
                        );
                    }
				}
				
				$this->set('arr', $arr);
				$this->set('dropoff_arr', $dropoff_arr);
				$this->set('price_arr', $price_arr);
				
				$this->set('fleet_arr', $fleet_arr);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminLocations.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionCopy()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$pjMultiLangModel = pjMultiLangModel::factory();
			$pjDropoffAreaModel = pjDropoffAreaModel::factory();

			$dropoff_arr = pjDropoffModel::factory()->where('location_id', $_GET['id'])->findAll()->getData();
			foreach($dropoff_arr as $k => $v)
			{
				$dropoff_arr[$k]['i18n'] = $pjMultiLangModel->reset()->getMultiLang($v['id'], 'pjDropoff');
				$dropoff_arr[$k]['area_ids'] = $pjDropoffAreaModel->reset()->where('t1.dropoff_id', $v['id'])->findAll()->getDataPair(NULL, 'area_id');
			}
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
			$this->set('dropoff_arr', $dropoff_arr);
			$area_arr = pjAreaModel::factory()
				->join('pjMultiLang', "t2.model='pjArea' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->select("t1.*, t2.content as name")
				->where('t1.status', 'T')
				->orderBy("t1.order_index ASC, name ASC")
				->findAll()->getData();
			$this->set('area_arr', $area_arr);
		}
	}
}
?>