<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminExtras extends pjAdmin
{
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['action_create']))
			{
				$pjExtraModel = pjExtraModel::factory();
				if (!$pjExtraModel->validates($_POST))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminExtras&action=pjActionIndex&err=AE04");
				}
				
				$id = $pjExtraModel->setAttributes($_POST)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
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
											$image_path = PJ_UPLOAD_PATH . 'extras/' . $id . '_' . $hash . '.' . $Image->getExtension();
											
											$Image->loadImage($_FILES['image']["tmp_name"]);
											//$Image->resize(45, 45);
											$Image->saveImage($image_path);
											
											$pjExtraModel->reset()->where('id', $id)->limit(1)->modifyAll(array('image_path' => $image_path));
										}
									}
								}
							}else{
								
							}
						}else if($_FILES['image']['error'] != 4){
							
						}
					}
					
					$err = 'AE03';
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjExtra', 'data');
					}
				} else {
					$err = 'AE04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminExtras&action=pjActionIndex&err=$err");
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
		
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminExtras.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGet()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjExtraModel = pjExtraModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjExtra' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'info'", 'left');

			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjExtraModel->where('t2.content LIKE', "%$q%");
				$pjExtraModel->orWhere('t3.content LIKE', "%$q%");
			}
				
			$column = 't2.content';
			$direction = 'ASC';
			
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjExtraModel->where('t1.status', $_GET['status']);
			}
			
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjExtraModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjExtraModel->select('t1.*, t2.content as name, t3.content as info')
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
			foreach ($data as $k => $v) {
				if ((float)$v['price'] > 0) {
					$data[$k]['price_format'] = pjUtil::formatCurrencySign(number_format((float)$v['price'], 2), $this->option_arr['o_currency']);
				} else {
					$data[$k]['price_format'] = __('lblFree', true);
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
			$this->appendJs('pjAdminExtras.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{	
			if (isset($_POST['action_update']))
			{
				$pjExtraModel = pjExtraModel::factory();
				
				$arr = $pjExtraModel->find($_POST['id'])->getData();
				$data = array();
				$data['modified'] = date('Y-m-d H:i:s');
				if (isset($_FILES['image']))
				{
					if($_FILES['image']['error'] == 0)
					{
						$image_size = getimagesize($_FILES['image']['tmp_name']);
						if(!empty($image_size))
						{
							if(!empty($arr['image_path']))
							{
								$image_path = PJ_INSTALL_PATH . $arr['image_path'];
								@unlink($image_path);
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
										$image_path = PJ_UPLOAD_PATH . 'extras/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
										
										$Image->loadImage($_FILES['image']["tmp_name"]);
										//$Image->resize(45, 45);
										$Image->saveImage($image_path);
										$data['image_path'] = $image_path;
									}
								}
							}
						}else{
							
						}
					}else if($_FILES['image']['error'] != 4){
						
					}	
				}
				$pjExtraModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjExtra', 'data');
				}
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminExtras&action=pjActionIndex&err=AE01");
				
			} else {
				$arr = pjExtraModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminExtras&action=pjActionIndex&err=AE08");
				}
				
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjExtra');
			
				$this->set('arr', $arr);
				
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
				$this->appendJs('pjAdminExtras.js');
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
			$pjExtraModel = pjExtraModel::factory();
			
			$arr = $pjExtraModel->find($_GET['id'])->getData();
			if ($pjExtraModel->reset()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjExtra')->where('foreign_id', $_GET['id'])->eraseAll();
				if(file_exists(PJ_INSTALL_PATH . $arr['image_path']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['image_path']);
				}
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
				$pjExtraModel = pjExtraModel::factory();
				
				$arr = $pjExtraModel
					->reset()
					->whereIn('id', $_POST['record'])
					->findAll()
					->getData();
					
				$pjExtraModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjExtra')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				
				foreach($arr as $v)
				{
					if(file_exists(PJ_INSTALL_PATH . $v['image_path']))
					{
						@unlink(PJ_INSTALL_PATH . $v['image_path']);
					}
				}
			}
		}
		exit;
	}
	
	public function pjActionSave()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjExtraModel = pjExtraModel::factory();
			if (!in_array($_POST['column'], $pjExtraModel->getI18n()))
			{
				$pjExtraModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value'], 'modified' => date('Y-m-d H:i:s')));
			} else {
				$pjExtraModel->where('id', $_GET['id'])->limit(1)->modifyAll(array('modified' => date('Y-m-d H:i:s')));
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjExtra');
			}
		}
		exit;
	}
	
	public function pjActionDeleteImage()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			
			$pjExtraModel = pjExtraModel::factory();
			$arr = $pjExtraModel->find($_GET['id'])->getData(); 
			
			if(!empty($arr))
			{
				if(!empty($arr['image_path']))
				{
					$image_path = PJ_INSTALL_PATH . $arr['image_path'];
					@unlink($image_path);
				}
				
				$data = array();
				$data['image_path'] = ':NULL';
				$pjExtraModel->reset()->where(array('id' => $_GET['id']))->limit(1)->modifyAll($data);
				
				$response['code'] = 200;
			}else{
				$response['code'] = 100;
			}
			
			pjAppController::jsonResponse($response);
		}
	}
}
?>