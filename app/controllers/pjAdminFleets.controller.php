<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminFleets extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminFleets&action=pjActionIndex&err=AF05");
			}
			if (isset($_POST['fleet_create']))
			{
				$data = array();
				
				$pjFleetModel = pjFleetModel::factory();
				$_POST['passengers'] = empty($_POST['passengers']) ? 0 : $_POST['passengers'];
				$_POST['luggage'] = empty($_POST['luggage']) ? 0 : $_POST['luggage'];
				$id = $pjFleetModel->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjFleet', 'data');
					}

                    pjExtraLimitationModel::factory()->saveLimitations($_POST['extras'], $id);
					
					if (isset($_POST['is_subtract'])) {
                        $pjFleetDiscountModel = pjFleetDiscountModel::factory();
                        $pjFleetDiscountModel->begin();
                        foreach ($_POST['is_subtract'] as $level => $pdata) {
                            foreach ($pdata as $day => $val) {
                                $data_discount = array(
                                    'fleet_id' => $id,
                                    'day' => $day,
                                    'type' => ($_POST['valid'][$level][$day] == 'always') ? $_POST['type'][$level][$day] : ':NULL',
                                    'discount' => ($_POST['valid'][$level][$day] == 'always') ? (float)$_POST['discount'][$level][$day] : ':NULL',
                                    'valid' => $_POST['valid'][$level][$day],
                                    'date_from' => ':NULL',
                                    'time_from' => ":NULL",
                                    'date_to' => ':NULL',
                                    'time_to' => ":NULL",
                                    'is_subtract' => ($_POST['valid'][$level][$day] == 'always') ? $val : ':NULL',
                                    'price_level' => $level
                                );
                                $fleetDiscountId = $pjFleetDiscountModel->setAttributes($data_discount)->insert()->getInsertId();
                                
                                if ($_POST['valid'][$level][$day] == 'period') {
                                    foreach ($_POST['periods'][$level][$day] as $period) {
                                        $entity = array(
                                            'fleet_discount_id' => $fleetDiscountId,
                                            'date_from'         => pjUtil::formatDate($period['date_from'], $this->option_arr['o_date_format']),
                                            'date_to'           => pjUtil::formatDate($period['date_to'], $this->option_arr['o_date_format']),
                                            'is_subtract'      	=> $period['is_subtract'],
                                            'discount'          => $period['discount'],
                                            'type'              => $period['type']
                                        );
                                        
                                        if (pjFleetDiscountPeriodModel::factory()->validates($entity)) {
                                            pjFleetDiscountPeriodModel::factory()->reset()->setAttributes($entity)->insert();
                                        }
                                    }
                                }
                            }
                        }
                        $pjFleetDiscountModel->commit();
                    }
	                
	                if(isset($_POST['index_arr']) && $_POST['index_arr'] != '')
					{
						$pjFleetFeeModel = pjFleetFeeModel::factory();
						$index_arr = explode("|", $_POST['index_arr']);
						foreach($index_arr as $k => $v)
						{
							$p_data = array();
							$p_data['fleet_id'] = $id;
							$p_data['start'] = $_POST['start'][$v];
							$p_data['end'] = $_POST['end'][$v];
							$p_data['price'] = $_POST['price'][$v];
							$pjFleetFeeModel->reset()->setAttributes($p_data)->insert();
						}
					}		
					
					$pjFleetFeeDayModel = pjFleetFeeDayModel::factory();
					if(isset($_POST['index_day_arr']) && $_POST['index_day_arr'] != '')
					{
					    $index_day_arr = explode("|", $_POST['index_day_arr']);
					    foreach($index_day_arr as $k => $v)
					    {
					        list($day, $idx) = explode("~:~", $v);
					        $pday_data = array();
					        $pday_data['fleet_id'] = $id;
					        $pday_data['day'] = $day;
					        $pday_data['start'] = $_POST['start_day'][$day][$idx];
					        $pday_data['end'] = $_POST['end_day'][$day][$idx];
					        $pday_data['price'] = $_POST['price_day'][$day][$idx];
					        $pjFleetFeeDayModel->reset()->setAttributes($pday_data)->insert();
					    }
					}
	                
					if (isset($_FILES['image']))
					{
						if($_FILES['image']['error'] == 0)
						{
							$image_size = getimagesize($_FILES['image']['tmp_name']);
							if(!empty($image_size))
							{
								$Image = new pjImage();
								if ($Image->getErrorCode() !== 200)
								{
									$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
									if ($Image->load($_FILES['image']))
									{
										$resp = $Image->isConvertPossible();
										if ($resp['status'] === true)
										{
											$hash = md5(uniqid(rand(), true));
											$source_path = PJ_UPLOAD_PATH . 'fleets/source/' . $id . '_' . $hash . '.' . $Image->getExtension();
											$thumb_path = PJ_UPLOAD_PATH . 'fleets/thumb/' . $id . '_' . $hash . '.' . $Image->getExtension();
											if ($Image->save($source_path))
											{
												$Image->loadImage($source_path);
												$Image->resizeSmart(220, 130);
												$Image->saveImage($thumb_path);
												
												$data['source_path'] = $source_path;
												$data['thumb_path'] = $thumb_path;
												$data['image_name'] = $_FILES['image']['name'];
												$pjFleetModel->reset()->where('id', $id)->limit(1)->modifyAll($data);
											}
										}
									}
								}
							}else{
								pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionUpdate&id=$id&err=AF11");
							}
						}else if($_FILES['image']['error'] != 4){
							pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionUpdate&id=$id&err=AF09");
						}
					}
					
					$err = 'AF03';
				} else {
					$err = 'AF04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionIndex&err=$err");
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
				
				$station_arr = pjStationModel::factory()
					->select("t1.*, t2.content AS name")
					->join('pjMultiLang', "t2.model='pjStation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy("t2.content ASC")->findAll()->getData();
				$this->set('station_arr', $station_arr);

				$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminFleets.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteFleet()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			
			$pjFleetModel = pjFleetModel::factory();
			$arr = $pjFleetModel->find($_GET['id'])->getData();
			
			if (pjFleetModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				if(file_exists(PJ_INSTALL_PATH . $arr['source_path']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['source_path']);
				}
				if(file_exists(PJ_INSTALL_PATH . $arr['thumb_path']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['thumb_path']);
				}
					
				pjMultiLangModel::factory()->where('model', 'pjFleet')->where('foreign_id', $_GET['id'])->eraseAll();
				pjPriceModel::factory()->where('fleet_id', $_GET['id'])->eraseAll();

				$fleetDiscountIds = pjFleetDiscountModel::factory()->reset()->where('fleet_id', $_GET['id'])->findAll()->getDataPair('id', 'id');
				if (!empty($fleetDiscountIds)) {
					pjFleetDiscountPeriodModel::factory()->reset()->whereIn('fleet_discount_id', $fleetDiscountIds)->eraseAll();
				}
				pjFleetDiscountModel::factory()->where('fleet_id', $_GET['id'])->eraseAll();
				
				pjFleetFeeModel::factory()->where('fleet_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteFleetBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjFleetModel = pjFleetModel::factory();
				$arr = $pjFleetModel
					->reset()
					->whereIn('id', $_POST['record'])
					->findAll()
					->getData();
			
				foreach($arr as $v)
				{
					if(file_exists(PJ_INSTALL_PATH . $v['source_path']))
					{
						@unlink(PJ_INSTALL_PATH . $v['source_path']);
					}
					if(file_exists(PJ_INSTALL_PATH . $v['thumb_path']))
					{
						@unlink(PJ_INSTALL_PATH . $v['thumb_path']);
					}
				}
				$pjFleetModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
				pjPriceModel::factory()->whereIn('fleet_id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjFleet')->whereIn('foreign_id', $_POST['record'])->eraseAll();

				$fleetDiscountIds = pjFleetDiscountModel::factory()->reset()->whereIn('fleet_id', $_POST['record'])->findAll()->getDataPair('id', 'id');
				if (!empty($fleetDiscountIds)) {
					pjFleetDiscountPeriodModel::factory()->reset()->whereIn('fleet_discount_id', $fleetDiscountIds)->eraseAll();
				}
				pjFleetDiscountModel::factory()->whereIn('fleet_id', $_POST['record'])->eraseAll();
				pjFleetFeeModel::factory()->whereIn('fleet_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExportFleet()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjFleetModel::factory()
						->select("t1.*, t2.content as fleet, t3.content as description")
						->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
						->whereIn('t1.id', $_POST['record'])
						->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Fleets-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetFleet()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjFleetModel = pjFleetModel::factory();
			
			$pjFleetModel->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						 ->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
						 ->join('pjMultiLang', "t4.model='pjStation' AND t4.foreign_id=t1.station_id AND t4.field='name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
						 ->join('pjMultiLang', "t5.model='pjFleet' AND t5.foreign_id=t1.id AND t5.field='model' AND t5.locale='".$this->getLocaleId()."'", 'left outer');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjFleetModel->where("(t2.content LIKE '%$q%' OR t3.content LIKE '%$q%' OR t5.content LIKE '%$q%')");
			}

			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjFleetModel->where('t1.status', $_GET['status']);
			}
				
			$column = 'fleet';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjFleetModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			$data = $pjFleetModel
				->select('t1.id, t1.thumb_path, t2.content as fleet, t1.min_passengers, t1.passengers, t1.status, t1.luggage, t4.content as base_station, t5.content as `model`')
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()->getData();
			foreach($data as $k => $v)
			{
				$_arr = array();
				if(!empty($v['min_passengers']))
				{
					$_arr[] = $v['min_passengers'];
				}
				if(!empty($v['passengers']))
				{
					$_arr[] = $v['passengers'];
				}
				$v['passengers'] = join(" - ", $_arr);
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
			$this->appendJs('pjAdminFleets.js');
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveFleet()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjFleetModel = pjFleetModel::factory();
			
			if (!in_array($_POST['column'], $pjFleetModel->i18n))
			{
				$value = $_POST['value'];
				
				$pjFleetModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $value, 'modified' => date('Y-m-d H:i:s')));
			} else {
				$pjFleetModel->where('id', $_GET['id'])->limit(1)->modifyAll(array('modified' => date('Y-m-d H:i:s')));
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjFleet', 'data');
			}
		}
		exit;
	}
	
	public function pjActionStatusFleet()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjFleetModel::factory()->whereIn('id', $_POST['record'])->modifyAll(array(
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
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminFleets&action=pjActionIndex&err=AF06");
			}	
			if (isset($_POST['fleet_update']))
			{
				$pjFleetModel = pjFleetModel::factory();
				$pjFleetDiscountModel = pjFleetDiscountModel::factory();
				
				$arr = $pjFleetModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionIndex&err=AF08");
				}
				
				$data = array();
				if (isset($_FILES['image']))
				{
					if($_FILES['image']['error'] == 0)
					{
						$image_size = getimagesize($_FILES['image']['tmp_name']);
						if(!empty($image_size))
						{
							if(!empty($arr['source_path']))
							{
								$source_path = PJ_INSTALL_PATH . $arr['source_path'];
								$thumb_path = PJ_INSTALL_PATH . $arr['thumb_path'];
								@unlink($source_path);
								@unlink($thumb_path);
							}
								
							$Image = new pjImage();
							if ($Image->getErrorCode() !== 200)
							{
								$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
								if ($Image->load($_FILES['image']))
								{
									$resp = $Image->isConvertPossible();
									if ($resp['status'] === true)
									{
										$hash = md5(uniqid(rand(), true));
										$source_path = PJ_UPLOAD_PATH . 'fleets/source/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
										$thumb_path = PJ_UPLOAD_PATH . 'fleets/thumb/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
										if ($Image->save($source_path))
										{
											$Image->loadImage($source_path);
											$Image->resizeSmart(220, 130);
											$Image->saveImage($thumb_path);
											
											$data['source_path'] = $source_path;
											$data['thumb_path'] = $thumb_path;
											$data['image_name'] = $_FILES['image']['name'];
										}
									}
								}
							}
						}else{
							pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionUpdate&id=".$_POST['id']."&err=AF11");
						}
					}else if($_FILES['image']['error'] != 4){
						pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionUpdate&id=".$_POST['id']."&err=AF10");
					}	
				}
				$_POST['passengers'] = empty($_POST['passengers']) ? 0 : $_POST['passengers'];
				$_POST['luggage'] = empty($_POST['luggage']) ? 0 : $_POST['luggage'];
				$data['modified'] = date('Y-m-d H:i:s');
				$pjFleetModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjFleet', 'data');
				}

                pjExtraLimitationModel::factory()->saveLimitations($_POST['extras'], $_POST['id']);

				$fleetDiscountIds = pjFleetDiscountModel::factory()->reset()->where('fleet_id', $_POST['id'])->findAll()->getDataPair('id', 'id');
				if (!empty($fleetDiscountIds)) {
					pjFleetDiscountPeriodModel::factory()->reset()->whereIn('fleet_discount_id', $fleetDiscountIds)->eraseAll();
				}

				pjFleetDiscountModel::factory()->reset()->where('fleet_id', $_POST['id'])->eraseAll();
				if (isset($_POST['is_subtract'])) {
				    $pjFleetDiscountModel->begin();
				    foreach ($_POST['is_subtract'] as $level => $pdata) {
				        foreach ($pdata as $day => $val) {
				            $data_discount = array(
				                'fleet_id' => $_POST['id'],
				                'day' => $day,
				                'type' => ($_POST['valid'][$level][$day] == 'always') ? $_POST['type'][$level][$day] : ':NULL',
				                'discount' => ($_POST['valid'][$level][$day] == 'always') ? (float)$_POST['discount'][$level][$day] : ':NULL',
				                'valid' => $_POST['valid'][$level][$day],
				                'date_from' => ':NULL',
				                'time_from' => ":NULL",
				                'date_to' => ':NULL',
				                'time_to' => ":NULL",
				                'is_subtract' => ($_POST['valid'][$level][$day] == 'always') ? $val : ':NULL',
				                'price_level' => $level
				            );
				            $fleetDiscountId = $pjFleetDiscountModel->setAttributes($data_discount)->insert()->getInsertId();
				            
				            if ($_POST['valid'][$level][$day] == 'period') {
				                foreach ($_POST['periods'][$level][$day] as $period) {
				                    $entity = array(
				                        'fleet_discount_id' => $fleetDiscountId,
				                        'date_from'         => pjUtil::formatDate($period['date_from'], $this->option_arr['o_date_format']),
				                        'date_to'           => pjUtil::formatDate($period['date_to'], $this->option_arr['o_date_format']),
				                        'is_subtract'      	=> $period['is_subtract'],
				                        'discount'          => $period['discount'],
				                        'type'              => $period['type']
				                    );
				                    
				                    if (pjFleetDiscountPeriodModel::factory()->validates($entity)) {
				                        pjFleetDiscountPeriodModel::factory()->reset()->setAttributes($entity)->insert();
				                    }
				                }
				            }
				        }
				    }
				    $pjFleetDiscountModel->commit();
				}
                
				$pjFleetFeeModel = pjFleetFeeModel::factory();				
				if(isset($_POST['index_arr']) && $_POST['index_arr'] != '')
				{
					$index_arr = explode("|", $_POST['index_arr']);			
					foreach($index_arr as $k => $v)
					{
						if(strpos($v, 'new') !== false)
						{
							$p_data = array();
							$p_data['fleet_id'] = $_POST['id'];
							$p_data['start'] = $_POST['start'][$v];
							$p_data['end'] = $_POST['end'][$v];
							$p_data['price'] = $_POST['price'][$v];
							$pjFleetFeeModel->reset()->setAttributes($p_data)->insert();
							
						}else{
							$p_data = array();
							$p_data['fleet_id'] = $_POST['id'];
							$p_data['start'] = $_POST['start'][$v];
							$p_data['end'] = $_POST['end'][$v];
							$p_data['price'] = $_POST['price'][$v];
							$pjFleetFeeModel->reset()->where('id', $v)->limit(1)->modifyAll($p_data);
						}
					}
				}
				
				if(isset($_POST['remove_arr']) && $_POST['remove_arr'] != '')
				{
				    $remove_arr = explode("|", $_POST['remove_arr']);
				    $pjFleetFeeModel->reset()->whereIn('id', $remove_arr)->eraseAll();
				}
				
				$pjFleetFeeDayModel = pjFleetFeeDayModel::factory();
				if(isset($_POST['index_day_arr']) && $_POST['index_day_arr'] != '')
				{
				    $index_day_arr = explode("|", $_POST['index_day_arr']);
				    foreach($index_day_arr as $k => $v)
				    {
				        list($day, $idx) = explode("~:~", $v);
				        if(strpos($idx, 'new') !== false)
				        {
				            $pday_data = array();
				            $pday_data['fleet_id'] = $_POST['id'];
				            $pday_data['day'] = $day;
				            $pday_data['start'] = $_POST['start_day'][$day][$idx];
				            $pday_data['end'] = $_POST['end_day'][$day][$idx];
				            $pday_data['price'] = $_POST['price_day'][$day][$idx];
				            $pjFleetFeeDayModel->reset()->setAttributes($pday_data)->insert();
				            
				        }else{
				            $pday_data = array();
				            $pday_data['fleet_id'] = $_POST['id'];
				            $pday_data['day'] = $day;
				            $pday_data['start'] = $_POST['start_day'][$day][$idx];
				            $pday_data['end'] = $_POST['end_day'][$day][$idx];
				            $pday_data['price'] = $_POST['price_day'][$day][$idx];
				            $pjFleetFeeDayModel->reset()->where('id', $idx)->limit(1)->modifyAll($pday_data);
				        }
				    }
				}
				
				if(isset($_POST['remove_day_arr']) && $_POST['remove_day_arr'] != '')
				{
				    $remove_day_arr = explode("|", $_POST['remove_day_arr']);
				    $pjFleetFeeDayModel->reset()->whereIn('id', $remove_day_arr)->eraseAll();
				}
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminFleets&action=pjActionIndex&err=AF01");
				
			} else {
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				$arr = pjFleetModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminFleets&action=pjActionIndex&err=AF08");
				}
				$arr['i18n'] = $pjMultiLangModel->getMultiLang($arr['id'], 'pjFleet');
				
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
				}
				
				$this->set('arr', $arr);
                $this->set('extra_arr', pjExtraModel::factory()
                    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.id AND t3.field='info' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                    ->select("t1.*, t2.content as name, t3.content as info")
                    ->where('t1.status', 'T')
                    ->orderBy("t1.id ASC")
                    ->findAll()
                    ->getData());
                $this->set('el_arr', pjExtraLimitationModel::factory()
                    ->where('fleet_id', $arr['id'])
                    ->findAll()
                    ->getDataPair('extra_id', 'max_qty'));
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$fleet_discount_arr = pjFleetDiscountModel::factory()->where('t1.fleet_id', $arr['id'])->where('t1.price_level', 1)->findAll()->getDataPair('day');
				if (!empty($fleet_discount_arr)) {
				    foreach ($fleet_discount_arr as $k => $fda) {
				        $fleet_discount_arr[$k]['periods'] = pjFleetDiscountPeriodModel::factory()->reset()->where('fleet_discount_id', $fda['id'])->findAll()->getData();
				    }
				}
				$this->set('fleet_discount_arr', $fleet_discount_arr);
				
				$fleet_discount_2_arr = pjFleetDiscountModel::factory()->reset()->where('t1.fleet_id', $arr['id'])->where('t1.price_level', 2)->findAll()->getDataPair('day');
				if (!empty($fleet_discount_2_arr)) {
				    foreach ($fleet_discount_2_arr as $k => $fda) {
				        $fleet_discount_2_arr[$k]['periods'] = pjFleetDiscountPeriodModel::factory()->reset()->where('fleet_discount_id', $fda['id'])->findAll()->getData();
				    }
				}
				$this->set('fleet_discount_2_arr', $fleet_discount_2_arr);
				
				$price_arr = pjFleetFeeModel::factory()->where('fleet_id', $_GET['id'])->findAll()->getData();
				$this->set('price_arr', $price_arr);
				
				$price_day_arr = array();
				$_price_day_arr = pjFleetFeeDayModel::factory()->where('fleet_id', $_GET['id'])->findAll()->getData();
				foreach ($_price_day_arr as $pd) {
				    $price_day_arr[$pd['day']][] = $pd;
				}
				$this->set('price_day_arr', $price_day_arr);

				$station_arr = pjStationModel::factory()
					->select("t1.*, t2.content AS name")
					->join('pjMultiLang', "t2.model='pjStation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy("t2.content ASC")->findAll()->getData();
				$this->set('station_arr', $station_arr);
					
				$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminFleets.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteImage()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			
			$pjFleetModel = pjFleetModel::factory();
			$arr = $pjFleetModel->find($_GET['id'])->getData(); 
			
			if(!empty($arr))
			{
				if(!empty($arr['source_path']))
				{
					$source_path = PJ_INSTALL_PATH . $arr['source_path'];
					$thumb_path = PJ_INSTALL_PATH . $arr['thumb_path'];
					@unlink($source_path);
					@unlink($thumb_path);
				}
				
				$data = array();
				$data['source_path'] = ':NULL';
				$data['thumb_path'] = ':NULL';
				$data['image_name'] = ':NULL';
				$pjFleetModel->reset()->where(array('id' => $_GET['id']))->limit(1)->modifyAll($data);
				
				$response['code'] = 200;
			}else{
				$response['code'] = 100;
			}
			
			pjAppController::jsonResponse($response);
		}
	}
}
?>