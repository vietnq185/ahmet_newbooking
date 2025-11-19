/*!
 * Transfer Reservation v1.0
 * http://www.phpjabbers.com/transfer-reservation/
 * 
 * Copyright 2014, StivaSoft Ltd.
 * 
 */
(function (window, undefined){
	"use strict";

	pjQ.$.ajaxSetup({
		xhrFields: {
			withCredentials: true
		}
	});
	
	var document = window.document,
        isPageLoaded = false,
		validate = (pjQ.$.fn.validate !== undefined),
		datepicker = (pjQ.$.fn.datepicker !== undefined);
	
	function log() {
		if (window.console && window.console.log) {
			for (var x in arguments) {
				if (arguments.hasOwnProperty(x)) {
					window.console.log(arguments[x]);
				}
			}
		}
	}
	
	function assert() {
		if (window && window.console && window.console.assert) {
			window.console.assert.apply(window.console, arguments);
		}
	}
	
	function TransferRes(opts) {
		if (!(this instanceof TransferRes)) {
			return new TransferRes(opts);
		}
				
		this.reset.call(this);
		this.init.call(this, opts);
		
		return this;
	}
	
	TransferRes.inObject = function (val, obj) {
		var key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				if (obj[key] == val) {
					return true;
				}
			}
		}
		return false;
	};
	
	TransferRes.size = function(obj) {
		var key,
			size = 0;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				size += 1;
			}
		}
		return size;
	};
	
	TransferRes.prototype = {
		reset: function () {
			this.$container = null;
			this.container = null;
			this.page = null;
			this.opts = {};
			this.switchDropoffLocation = '';
			return this;
		},
        pageLoaded: function () {
            // Created this method instead of using window.onload event because sometimes the event is triggered before DOM is loaded.
            // Then jQuery can't find the preloader and fails to hide it, which causes infinite "loading" screen even if the page is loaded successfully.

            // UNIFY HEIGHT
            var maxHeight = 0;

            pjQ.$('.heightfix').each(function(){
                if (pjQ.$(this).height() > maxHeight) { maxHeight = pjQ.$(this).height(); }
            });
            pjQ.$('.heightfix').height(maxHeight);

            // PRELOADER
            pjQ.$('.preloader').fadeOut();

            isPageLoaded = true;
        },
		disableButtons: function () {
			var $el;
			this.$container.find(".btn").each(function (i, el) {
				$el = pjQ.$(el).attr("disabled", "disabled");
				if ($el.hasClass("btn")) {
					$el.addClass("trButtonDisable");
				}
			});
			
			this.$container.find(".pjSbBtnGoBack").each(function (i, el) {
				$el = pjQ.$(el).attr("disabled", "disabled");
				if ($el.hasClass("pjSbBtnGoBack")) {
					$el.addClass("trButtonDisable");
				}
			});
		},
		enableButtons: function () {
			this.$container.find(".btn").removeAttr("disabled").removeClass("trButtonDisable");
			this.$container.find(".pjSbBtnGoBack").removeAttr("disabled").removeClass("trButtonDisable");
		},
		init: function (opts) {
			var self = this;
			this.opts = opts;
			this.container = document.getElementById("pjSbContainer_" + this.opts.index);
			this.$container = pjQ.$(this.container);

            pjQ.$.validator.setDefaults({
                onkeyup: false,
                errorPlacement: function (error, element) {
                    if(element.is('select'))
                    {
                        if(element.hasClass('select2'))
                        {
                            error.insertAfter(element.next('.select2-container'));
                        }
                        else
                        {
                            error.insertAfter(element.parent());
                        }
                    }
                    else if(element.hasClass('hasDatepicker'))
                    {
                        error.insertAfter(element.parent());
                    }
                    else if(element.is(':checkbox'))
                    {
                        error.appendTo(element.closest('.form-check'));
                    }
                    else
                    {
                    	error.insertAfter(element.parent().next());
                    }
                },
                highlight: function(ele, errorClass, validClass) {
	            	var element = pjQ.$(ele);
	            	element.closest('.form-group').addClass('has-error');
	            },
	            unhighlight: function(ele, errorClass, validClass) {
	            	var element = pjQ.$(ele);
	            	element.closest('.form-group').removeClass('has-error').addClass('has-success');
	            },
            });

			this.$container.on("click.tr", ".btn", function (e) {
                if(pjQ.$(this).hasClass('trButtonDisable'))
                {
                    if (e && e.preventDefault) {
                        e.preventDefault();
                    }
                }
            }).on("click.tr", ".trSelectorLocale", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var locale = pjQ.$(this).data("id");
				self.opts.locale = locale;				
				pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionLocale", "&session_id=", self.opts.session_id].join(""), {
					"locale_id": locale
				}).done(function (data) {
					self.loadSearch.call(self);
				}).fail(function () {
					log("Deferred is rejected");
				});
				return false;
			}).on("change.tr", "#trLocationId_"+ self.opts.index, function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('#custom_pickup_id_' + self.opts.index).val('');
				var $location_id = pjQ.$(this).val();
				if ($location_id != '') {
					pjQ.$('.pjSbPickupLocation').addClass('hasSelected');
					var $location_id_arr = $location_id.split('~::~');
					if ($location_id_arr[0] == 'google') {
                		pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionGetLatLngPickup", "&session_id=", self.opts.session_id].join(""), {
        					"place_id": $location_id_arr[1]
        				}).done(function (data) {
        					if (data.status == 'OK') {
        						if (data.pickup_arr.length > 0 && data.lat != '' && data.lng != '') {
        	                    	var valid= false,
        	                    		pjLatLng = new google.maps.LatLng(parseFloat(data.lat), parseFloat(data.lng));
        	                    	for (var j = 0, jlen = data.pickup_arr.length; j < jlen; j++) 
        							{
        								switch (data.pickup_arr[j].type) {
        									case 'circle':
        										var str = data.pickup_arr[j].data.replace(/\(|\)|\s+/g, ""),
        											arr = str.split("|"),
        											center = new google.maps.LatLng(arr[0].split(",")[0], arr[0].split(",")[1]);
        										
        										var circle = new google.maps.Circle({
        											center: center,								
        								            radius: parseFloat(arr[1]),
        										});
        										valid = circle.getBounds().contains(pjLatLng) ? true : false;
        										if(valid == true) {
        											pjQ.$('#custom_pickup_id_' + self.opts.index).val(data.pickup_arr[j].location_id);
        											self.getLocations();
        											return true;
        										}
        										break;
        									case 'polygon':
        										var path,
        											str = data.pickup_arr[j].data.replace(/\(|\s+/g, ""),
        											arr = str.split("),"),
        											paths = [];
        										arr[arr.length-1] = arr[arr.length-1].replace(")", "");
        										for (var i = 0, len = arr.length; i < len; i++) {
        											path = new google.maps.LatLng(arr[i].split(",")[0], arr[i].split(",")[1]);
        											paths.push(path);
        										}
        										var polygon = new google.maps.Polygon({
        											paths: paths
        									    });
        										valid = google.maps.geometry.poly.containsLocation(pjLatLng, polygon);
        										if(valid == true) {
        											pjQ.$('#custom_pickup_id_' + self.opts.index).val(data.pickup_arr[j].location_id);
        											self.getLocations();
        											return true;
        										}
        										break;
        									case 'rectangle':
        										var bound,
        											str = data.pickup_arr[j].data.replace(/\(|\s+/g, ""),
        											arr = str.split("),"), 
        											bounds = [];
        										for (var i = 0, len = arr.length; i < len; i++) {
        											arr[i] = arr[i].replace(/\)/g, "");
        											bound = new google.maps.LatLng(arr[i].split(",")[0], arr[i].split(",")[1]);
        											bounds.push(bound);
        										}
        										var rectangle = new google.maps.Rectangle({
        								            bounds: new google.maps.LatLngBounds(bounds[0], bounds[1]),
        										});
        										valid = rectangle.getBounds().contains(pjLatLng) ? true : false;
        										if(valid == true) {
        											pjQ.$('#custom_pickup_id_' + self.opts.index).val(data.pickup_arr[j].location_id);
        											self.getLocations();
        											return true;
        										}
        										break;
        								}
        							}
        	                    }
        					}
							self.getLocations();
        				}).fail(function () {
        					log("Deferred is rejected");
        				});
                	} else {
                		self.getLocations();
                	}
				} else {
					pjQ.$('.pjSbPickupLocation').removeClass('hasSelected');
					self.getLocations();
				}
				var $container = pjQ.$('#trBookingStep_Services_' + self.opts.index);
				$container.empty();
	            $container.nextAll('[id^="trBookingStep_"]').empty();
			}).on("change.tr", "#trDropoffId_"+ self.opts.index, function (e) {
				pjQ.$('#custom_dropoff_id_' + self.opts.index).val('');
				pjQ.$('#custom_dropoff_place_id_' + self.opts.index).val('');
                var $dropoff_id = pjQ.$(this).val();
                if ($dropoff_id != '') {
                	pjQ.$('.pjSbDropoffLocation').addClass('hasSelected');
                	var $dropoff_id_arr = $dropoff_id.split('~::~');
                	if ($dropoff_id_arr[0] == 'server') {
                		pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionGetLatLngDropoff", "&session_id=", self.opts.session_id].join(""), {
        					"place_id": $dropoff_id_arr[1]
        				}).done(function (data) {
        					var $bounds = new google.maps.LatLngBounds();
							switch (data.type) {
								case 'circle':
									var $str = data.data.replace(/\(|\)|\s+/g, ""),
										$arr = $str.split("|"),
										$center = new google.maps.LatLng($arr[0].split(",")[0], $arr[0].split(",")[1]),									
										$circle = new google.maps.Circle({
											center: $center,								
								            radius: parseFloat($arr[1]),
										}),
										$lat = $circle.getCenter().lat(),
										$lng = $circle.getCenter().lng();
									break;
								case 'polygon':
									var $str = data.data.replace(/\(|\s+/g, ""),
										$arr = $str.split("),");
									$arr[$arr.length-1] = $arr[$arr.length-1].replace(")", "");
									for (var i = 0, len = $arr.length; i < len; i++) {
										$bounds.extend(new google.maps.LatLng($arr[i].split(",")[0], $arr[i].split(",")[1]));
									}
									var $center = $bounds.getCenter(),
										$lat = $center.lat(),
										$lng = $center.lng();
									break;
								case 'rectangle':
									var $str = data.data.replace(/\(|\s+/g, ""),
										$arr = $str.split("),");
									for (var i = 0, len = $arr.length; i < len; i++) {
										$arr[i] = $arr[i].replace(/\)/g, "");
										$bounds.extend(new google.maps.LatLng($arr[i].split(",")[0], $arr[i].split(",")[1]));
									}
									var $center = $bounds.getCenter(),
									$lat = $center.lat(),
									$lng = $center.lng();
									break;
							}
							pjQ.$('#dropoff_lat_' + self.opts.index).val($lat);
							pjQ.$('#dropoff_lng_' + self.opts.index).val($lng);
        				}).fail(function () {
        					log("Deferred is rejected");
        				});
                	} else {
                		var $location_id = pjQ.$('#trLocationId_' + self.opts.index).val();
	               		 if ($location_id != '') {
	               			 var $location_id_arr = $location_id.split('~::~'),
	               			 	$custom_pickup_id = parseInt(pjQ.$('#custom_pickup_id_' + self.opts.index).val(), 10);
	               			 if ($location_id_arr[0] == 'server' || $custom_pickup_id > 0) {
	               				 pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionGetDropoff", "&session_id=", self.opts.session_id].join(""), pjQ.$('#trSearchForm_' + self.opts.index).serialize()).done(function (data) {
	           	                    if (data.dropoff_arr.length > 0 && data.lat != '' && data.lng != '') {
	           	                    	var valid= false,
	           	                    		pjLatLng = new google.maps.LatLng(parseFloat(data.lat), parseFloat(data.lng));
	           	                    	for (var j = 0, jlen = data.dropoff_arr.length; j < jlen; j++) 
	           							{
	           								switch (data.dropoff_arr[j].type) {
	           									case 'circle':
	           										var str = data.dropoff_arr[j].data.replace(/\(|\)|\s+/g, ""),
	           											arr = str.split("|"),
	           											center = new google.maps.LatLng(arr[0].split(",")[0], arr[0].split(",")[1]);
	           										
	           										var circle = new google.maps.Circle({
	           											center: center,								
	           								            radius: parseFloat(arr[1]),
	           										});
	           										valid = circle.getBounds().contains(pjLatLng) ? true : false;
	           										if(valid == true) {
	           											pjQ.$('#custom_dropoff_id_' + self.opts.index).val(data.dropoff_arr[j].dropoff_id);
	           											pjQ.$('#custom_dropoff_place_id_' + self.opts.index).val(data.dropoff_arr[j].id);
	           											return true;
	           										}
	           										break;
	           									case 'polygon':
	           										var path,
	           											str = data.dropoff_arr[j].data.replace(/\(|\s+/g, ""),
	           											arr = str.split("),"),
	           											paths = [];
	           										arr[arr.length-1] = arr[arr.length-1].replace(")", "");
	           										for (var i = 0, len = arr.length; i < len; i++) {
	           											path = new google.maps.LatLng(arr[i].split(",")[0], arr[i].split(",")[1]);
	           											paths.push(path);
	           										}
	           										var polygon = new google.maps.Polygon({
	           											paths: paths
	           									    });
	           										valid = google.maps.geometry.poly.containsLocation(pjLatLng, polygon);
	           										if(valid == true) {
	           											pjQ.$('#custom_dropoff_id_' + self.opts.index).val(data.dropoff_arr[j].dropoff_id);
	           											pjQ.$('#custom_dropoff_place_id_' + self.opts.index).val(data.dropoff_arr[j].id);
	           											return true;
	           										}
	           										break;
	           									case 'rectangle':
	           										var bound,
	           											str = data.dropoff_arr[j].data.replace(/\(|\s+/g, ""),
	           											arr = str.split("),"), 
	           											bounds = [];
	           										for (var i = 0, len = arr.length; i < len; i++) {
	           											arr[i] = arr[i].replace(/\)/g, "");
	           											bound = new google.maps.LatLng(arr[i].split(",")[0], arr[i].split(",")[1]);
	           											bounds.push(bound);
	           										}
	           										var rectangle = new google.maps.Rectangle({
	           								            bounds: new google.maps.LatLngBounds(bounds[0], bounds[1]),
	           										});
	           										valid = rectangle.getBounds().contains(pjLatLng) ? true : false;
	           										if(valid == true) {
	           											pjQ.$('#custom_dropoff_id_' + self.opts.index).val(data.dropoff_arr[j].dropoff_id);
	           											pjQ.$('#custom_dropoff_place_id_' + self.opts.index).val(data.dropoff_arr[j].id);
	           											return true;
	           										}
	           										break;
	           								}
	           							}
	           	                    }            	                    
	           	                    self.enableButtons.call(self);
	           	                }).fail(function () {
	           	                    self.enableButtons.call(self);
	           	                });
	               			 }
	               		 }
                	}
                } else {
                	pjQ.$('.pjSbDropoffLocation').removeClass('hasSelected');
                }
                //self.loadServices.call(self);
            }).on("click.tr", ".trChooseDateButton", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('html, body').animate({
                    scrollTop: pjQ.$('#trDate_' + self.opts.index).offset().top
                }, 500);
                
                pjQ.$('#trDate_' + self.opts.index).trigger('click');
				return false;
			}).on("click.tr", ".trChooseVehicleButton", function (e) {
                var $allow_book = parseInt(pjQ.$(this).attr('data-allow_book'), 10),
                	$is_return = parseInt(pjQ.$(this).attr('data-is_return'), 10);
                if ($allow_book == 1) {
                	if (e && e.preventDefault) {
    					e.preventDefault();
    				}
                    if(pjQ.$(this).hasClass('trButtonDisable'))
                    {
                        return false;
                    }
	                pjQ.$('.trChooseVehicleButton').removeClass('active');
	                pjQ.$(this).addClass('active');
	                var fleet_id = pjQ.$(this).attr('data-id'),
	                    params = 	{
	                        "locale": self.opts.locale,
	                        "hide": self.opts.hide,
	                        "index": self.opts.index,
	                        "fleet_id": fleet_id
	                    };
	                self.disableButtons.call(self);
	                pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionAddFleet", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
	                	self.loadDeparture.call(self, 1,0);
	                }).fail(function () {
	                    self.enableButtons.call(self);
	                });
					return false;
                }
			}).on("click.tr", ".tr-page-clickable", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
                self.page = pjQ.$(this).attr('rev');
                self.loadServices.call(self);
				return false;
			}).on("click.tr", ".trSetTransferTypeButton", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                if(pjQ.$(this).hasClass('trButtonDisable'))
                {
                    return false;
                }
                pjQ.$('.trChooseTransferTypeButton').removeClass('active');
                pjQ.$(this).addClass('active');
                var $form = pjQ.$(this).closest('form'),
                	$is_return = pjQ.$(this).data('is-return'),
                	params = 	{
                        "locale": self.opts.locale,
                        "hide": self.opts.hide,
                        "index": self.opts.index,
                        "is_return": $is_return
                    };
                self.disableButtons.call(self);
                pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveDeparture", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                	pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionSetTransferType", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                        pjQ.$('.pjSbAlertAddReturnTransfer').hide();
                        pjQ.$('.trChooseVehicleButton').attr('data-is_return', $is_return);
                        if ($is_return == 1) {
    	                    pjQ.$('.pjSbVehicle.pjSbHasDiscount').removeClass('pjSbHasReturnDiscount');
    	                    pjQ.$('.pjSbVehicle').find('.return-discount-info').hide();
    	                    pjQ.$('.pjSbSwitchReturn').trigger('click');
                        } else {
                        	pjQ.$('.pjSbVehicle.pjSbHasDiscount').addClass('pjSbHasReturnDiscount');
    	                    pjQ.$('.pjSbVehicle').find('.return-discount-info').show();
    	                    pjQ.$('.pjSbSwitchOneWay').trigger('click');
                        }
                        self.loadDeparture.call(self, 0,1);
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
                	self.enableButtons.call(self);
                }).fail(function () {
                    self.enableButtons.call(self);
                });
                return false;
            }).on("change.tr", "#trExtrasForm_" + self.opts.index + ' select', function (e) {
                self.disableButtons.call(self);
                pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionUpdateExtras", "&session_id=", self.opts.session_id].join(""), pjQ.$('#trExtrasForm_' + self.opts.index).serialize()).done(function (data) {
                    pjQ.$('dd.trCartExtras').html(data.extras);
                    pjQ.$('.trCartExtras').toggle(data.extras !== undefined && data.extras !== null && data.extras.length > 0);
                    self.enableButtons.call(self);
                }).fail(function () {
                    self.enableButtons.call(self);
                });
            }).on("click.tr", "#trBtnExtras_" + self.opts.index, function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                self.loadDeparture.call(self, 0,0);
            }).on("click.tr", "#trBtnTerms_" + self.opts.index, function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
                pjQ.$("#trTermContainer_" + self.opts.index).slideToggle();
			}).on("click.tr", "#trBtnSharedTrip_" + self.opts.index, function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$("#trSharedTripInfoContainer_" + self.opts.index).slideToggle();
            }).on("click.bs", "#pjTrCaptchaImage_" + self.opts.index, function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $captcha = pjQ.$(this);
				$captcha.attr("src", $captcha.attr("src").replace(/(&rand=)\d+/g, '\$1' + Math.ceil(Math.random() * 99999)));
				pjQ.$('#trCaptcha_' + self.opts.index).val("").removeData('previousValue');
			}).on("click.tr", ".trPaymentMethodSelector", function (e) {
                var $pm = pjQ.$(this).val();
                pjQ.$("#trPaymentMethod_" + self.opts.index).val($pm);
                pjQ.$("#trPaymentMethod_" + self.opts.index).trigger('change');
            }).on("change.tr", "#trPaymentMethod_" + self.opts.index, function (e) {
            	var $attr_pm = pjQ.$('option:selected', pjQ.$(this)).attr('data-pm'),
            		$attr_deposit = pjQ.$('option:selected', pjQ.$(this)).attr('data-deposit'),
            		$attr_total = pjQ.$('option:selected', pjQ.$(this)).attr('data-total'),
            		$html_cc_fee = pjQ.$('option:selected', pjQ.$(this)).attr('data-html_cc_fee');
                if(pjQ.$(this).find('option:selected').length > 0)
                {
                	if ($html_cc_fee.length > 0) {
                		pjQ.$('.pjSbCartPaymentMethod').html(pjQ.$(this).find('option:selected').text() + '<br/>' + $html_cc_fee);
                	} else {
                		pjQ.$('.pjSbCartPaymentMethod').html(pjQ.$(this).find('option:selected').text());
                	}
                }
                if (pjQ.$(this).val() == 'creditcard' || (pjQ.$(this).val() == 'saferpay' && $attr_pm == 'direct')) {
                	pjQ.$("#trCCData_" + self.opts.index + ', .trCartDeposit_Checkout_' + self.opts.index + ', .trCartRest_Checkout_' + self.opts.index).show();
                	pjQ.$("#trCCData_" + self.opts.index).find('input.form-control').addClass('required');
                } else {
                	pjQ.$("#trCCData_" + self.opts.index + ', .trCartDeposit_Checkout_' + self.opts.index + ', .trCartRest_Checkout_' + self.opts.index).hide();
                	pjQ.$("#trCCData_" + self.opts.index).find('input.form-control').removeClass('required');
                }
                if (pjQ.$(this).val() == 'creditcard' || pjQ.$(this).val() == 'saferpay') {
                	pjQ.$('.pjSbFullPriceChargedDesc').show();
                } else {
                	pjQ.$('.pjSbFullPriceChargedDesc').hide();
                }
                
                pjQ.$('.pjSbCartDeposit').html($attr_deposit);
                pjQ.$('.pjSbCartTotal').html($attr_total);
                
                var $html_book = pjQ.$('.btnBook').attr('data-html_book'),
                	$html_book_pay = pjQ.$('.btnBook').attr('data-html_book_pay');
                if (pjQ.$(this).val() == 'saferpay') {
                	pjQ.$('.btnBook').html($html_book_pay);
                	pjQ.$('.btnFinishBooking').hide();
                } else {
                	pjQ.$('.btnBook').html($html_book);
                	pjQ.$('.btnFinishBooking').show();
                }
            }).on("change.tr", "#voucher_code", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                var voucher_code = pjQ.$(this).val(),
                    params = 	{
                        "locale": self.opts.locale,
                        "hide": self.opts.hide,
                        "index": self.opts.index,
                        "voucher_code": voucher_code
                    };
                self.disableButtons.call(self);

                pjQ.$.get([self.opts.folder, "index.php?controller=pjFront&action=pjActionApplyCode", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                    switch (parseInt(data.code, 10)) {
                        case 200:
                            pjQ.$('.pjSbCartDiscountPrint').html(data.discount_print);
                            pjQ.$('.pjSbCartDeposit').html(data.deposit);
                            pjQ.$('.pjSbCartRest').html(data.rest);
                            pjQ.$('.pjSbCartTotal').html(data.total);
                            pjQ.$('.pjSbCartDiscount').removeClass('hide').addClass('d-flex');
                            break;
                        default:
                        	pjQ.$('.pjSbCartDiscountPrint').html('');
	                        pjQ.$('.pjSbCartDeposit').html(data.deposit);
	                        pjQ.$('.pjSbCartRest').html(data.rest);
	                        pjQ.$('.pjSbCartTotal').html(data.total);
	                        pjQ.$('.pjSbCartDiscount').removeClass('d-flex').addClass('hide');
                            if(data.text !== undefined && data.text !== null && data.text.length > 0)
                            {
                                var validator = pjQ.$('#trPassengerForm_' + self.opts.index).validate(); // get instance
                                validator.showErrors({ voucher_code: data.text });
                            }
                    }
                    self.enableButtons.call(self);
                }).fail(function () {
                    self.enableButtons.call(self);
                });
                return false;
            }).on("change.tr", '#time_h, #time_m', function (e) {
                var $form = pjQ.$(this).closest('form'),
                	time = '',
                    hours = pjQ.$('#time_h').val(),
                    minutes = pjQ.$('#time_m').val();

                if(hours.length > 0 && minutes.length > 0)
                {
                    hours = ('0' + hours).substr(hours.length - 1);
                    minutes = ('0' + minutes).substr(minutes.length - 1);
                    time = hours + ':' + minutes;
                }

                pjQ.$('dd.trCartDepartureTime').html(time);
                pjQ.$('.trCartDepartureTime').toggle(time.length > 0);
                
                pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveDeparture", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                	self.loadDeparture.call(self, 0,0);
                	self.enableButtons.call(self);
                }).fail(function () {
                    self.enableButtons.call(self);
                });
            }).on("change.tr", '#return_time_h, #return_time_m', function (e) {
                var $form = pjQ.$(this).closest('form'),
                	time = '',
                    hours = pjQ.$('#return_time_h').val(),
                    minutes = pjQ.$('#return_time_m').val();

                if(hours.length > 0 && minutes.length > 0)
                {
                    hours = ('0' + hours).substr(hours.length - 1);
                    minutes = ('0' + minutes).substr(minutes.length - 1);
                    time = hours + ':' + minutes;
                }

                pjQ.$('dd.trCartReturnTime').html(time);
                pjQ.$('.trCartReturnTime').toggle(time.length > 0);
                
                pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveReturn", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                	self.loadReturn.call(self, 1);
                	self.enableButtons.call(self);
                }).fail(function () {
                    self.enableButtons.call(self);
                });
            }).on("change.tr", '#passengers', function (e) {
                var pax = pjQ.$(this).val();

                pjQ.$('dd.trCartPax').html(pax);
                pjQ.$('.trCartPax').toggle(pax.length > 0);
            }).on("click.tr", ".pjCrRestartBooking", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                self.loadSearch.call(self);
                pjQ.$('html, body').animate({
                    scrollTop: pjQ.$('#trSearchForm_' + self.opts.index).offset().top
                }, 500)
            });

            pjQ.$(document).on("click", ".pjSbVehicleMoreInfo", function (e) {
            	var $obj = pjQ.$(this).closest('.pjSbVehicle').find('.pjSbVehicleFullDesc');
            	$obj.slideToggle(500, 
            		function(){ 
	            		pjQ.$('html, body').animate({
	                        scrollTop: $obj.offset().top
	                    }, 500);
            		}
            	)
            }).on("click", ".pjSbVehicleLessInfo", function (e) {
            	pjQ.$(this).closest('.pjSbVehicle').find('.pjSbVehicleMoreInfo').trigger('click');
            }).on("select2:open", function() {
                pjQ.$(".select2-search--dropdown .select2-search__field").attr("placeholder", self.opts.search_placeholder);
            }).on("change.tr", ".trLoadPrices", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $form = pjQ.$(this).closest('form'), 
					$location_id = pjQ.$("#trLocationId_"+ self.opts.index).val(),
	        		$dropoff_id = pjQ.$("#trDropoffId_"+ self.opts.index).val(),
	        		$date = pjQ.$("#trDate_"+ self.opts.index).val();
	        	if ($location_id != '' && $dropoff_id != '') {
	        		setTimeout(function() {
	        			$form.trigger('submit');
	        		}, 1000);
	        	}
				return false;
			}).on("click.tr", ".pjSbVehicleTipInfo", function (e) {
				pjQ.$(this).tooltipster({
					functionBefore: function(instance, helper){
						pjQ.$.each(pjQ.$.tooltipster.instances(), function(i, instance){
						    instance.close();
						});
					},
					contentAsHTML: true,
					distance: 1,
					side: ['top', 'bottom', 'right', 'left'],
				    trigger: 'custom',
				    triggerOpen: {
				        mouseenter: true,
				        touchstart: true
				    },
				    triggerClose: {
				    	mouseleave: true,
				        originClick: true,
				        click: true,
				        touchleave: true,
				        tap: true
				    }
				});
				pjQ.$(this).tooltipster('open');
			}).on("mouseenter.tr", ".pjSbVehicleTipInfo", function (e) {
				pjQ.$(this).tooltipster({
					functionBefore: function(instance, helper){
						pjQ.$.each(pjQ.$.tooltipster.instances(), function(i, instance){
							instance.close();
						});
					},
					contentAsHTML: true,
					distance: 1,
					side: ['top', 'bottom', 'right', 'left'],
				    trigger: 'custom',
				    triggerOpen: {
				        mouseenter: true
				    },
				    triggerClose: {
				    	mouseleave: true,
				        scroll: true
				    }
				});
				pjQ.$(this).tooltipster('open');
			}).on("click.tr", ".pjSbSwitchOneWay", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('.pjSbSwitch').removeClass('btn-primary');
                pjQ.$(this).addClass('btn-primary');
                pjQ.$('.pjSbAddReturnTransfer').show();
                pjQ.$('.pjSbReturnTransferDateWrap').hide();
                pjQ.$('#is_return_' + self.opts.index).val(0);
            }).on("click.tr", ".pjSbSwitchReturn", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('.pjSbSwitch').removeClass('btn-primary');
                pjQ.$(this).addClass('btn-primary');
                pjQ.$('.pjSbAddReturnTransfer').hide();
                pjQ.$('.pjSbReturnTransferDateWrap').show();
                pjQ.$('#is_return_' + self.opts.index).val(1);
            }).on("click.tr", ".pjSbAddReturnTransfer", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('#trReturnDate_' + self.opts.index).trigger('click');
                pjQ.$('.pjSbSwitchReturn').trigger('click');
            }).on("click.tr", ".pjSbSpin", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                var $this = pjQ.$(this),
                	$form = $this.closest('form'),
                	$parent = $this.closest('.pjSbSpinWrap'),
                	$input = $parent.find('input.form-control'),
                	$val = parseInt($input.val(), 10),
                	$type = $this.attr('data-type');
                if ($type == 'plus') {
                	var $max = parseInt($this.attr('data-max'), 10);
                	if ($val + 1 > $max) {
                		$val = $max;
                	} else {
                		$val++;
                	}
                } else {
                	var $min = parseInt($this.attr('data-min'), 10);
                	if ($val - 1 < $min) {
                		$val = $min;
                	} else {
                		$val--;
                	}
                }
                $input.val($val);
                if ($input.attr('name') == 'passengers') {
                	pjQ.$('.trCartPax').html($val);
                } else if ($input.attr('name') == 'passengers_return') {
                	pjQ.$('.trCartReturnPax').html($val);
                } else if ($input.hasClass('pjSbExtraQty')) {
                	self.disableButtons.call(self);
                	var $type = $input.hasClass('pjSbExtraReturn') ? 'return' : 'pickup';
                	if ($type == 'pickup') {
                		var $eid = $input.attr('data-id');
    					pjQ.$('#trExtraReturn_' + $eid).val($input.val());
        			}
                	pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveDeparture", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                		pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionUpdateExtras", "&session_id=", self.opts.session_id, "&type=", $type].join(""), $form.serialize()).done(function (data) {
                			self.loadCart.call(self, pjQ.$('.pjSbCartWrap_pjActionDeparture'));
                            self.enableButtons.call(self);
                        }).fail(function () {
                            self.enableButtons.call(self);
                        });
                    	self.enableButtons.call(self);
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
                    
                }
            }).on("click.tr", ".pjSbLoadFleets", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                self.loadServices.call(self);
            }).on("click.tr", ".pjSbLoadDeparture", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                self.loadDeparture.call(self, 1,0);
            }).on("click.tr", ".pjSbSwitchLocation", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                var $form = pjQ.$(this).closest('form');
                self.disableButtons.call(self);
                pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSwitchLocations", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                	if (data.status == 'OK') {
                		pjQ.$('#trLocationId_' + self.opts.index).val(data.pickup_value).trigger('change');
                		var $dropoff_value = data.dropoff_value;
                		if ($dropoff_value.length > 0) {
                			self.switchDropoffLocation = $dropoff_value;
                		}
                		
                	}
                    self.enableButtons.call(self);
                }).fail(function () {
                    self.enableButtons.call(self);
                });
            }).on("click.tr", ".pjSbPersonalTitle", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('.pjSbPersonalTitle').removeClass('btn-primary');
                pjQ.$(this).addClass('btn-primary');
                var $title = pjQ.$(this).attr('data-value');
                pjQ.$('#trPersonalTitle_' + self.opts.index).val($title);
            }).on("change.tr", ".pjSbDialingCode, .pjSbPhone", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                var $dialing_code = pjQ.$('#trDialingCode_' + self.opts.index).val(),
                	$phone = pjQ.$('#trPhone_' + self.opts.index).val();
                if ($dialing_code.length > 0 && $phone.length > 0) {
                	pjQ.$('#trFullPhoneNumber_' + self.opts.index).val($dialing_code + $phone);
                } else {
                	pjQ.$('#trFullPhoneNumber_' + self.opts.index).val('');
                }
            }).on("click.tr", ".btnFinishBooking", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                var $form = pjQ.$('#trPaymentForm_' + self.opts.index);
                pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionFinishBooking", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                	self.loadSummary.call(self, data.booking_id);
                });
            }).on("change.tr", "#trCountryId_"+ self.opts.index, function (e) {
                pjQ.$('#trDialingCode_' + self.opts.index).val(pjQ.$(this).find('option:selected').data('code'));
            }).on("click.tr", ".pjSbTransferDateContainer", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('#pjSbTransferDateModal_' + self.opts.index).modal('show');
            }).on("click.tr", ".pjSbReturnTransferDateContainer", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('#pjSbReturnTransferDateModal_' + self.opts.index).modal('show');
            }).on("click.tr", ".pjSbBookingDetailsTransferDate", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('#pjSbBookingDetailsTransferDateModal_' + self.opts.index).modal('show');
            }).on("click.tr", ".pjSbBookingDetailsArrivalTime", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('#pjSbBookingDetailsArrivalTimeModal_' + self.opts.index).modal('show');
            }).on("click.tr", ".pjSbBookingDetailsPickupTime", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('#pjSbBookingDetailsPickupTimeModal_' + self.opts.index).modal('show');
            }).on("click.tr", ".pjSbBookingDetailsFlightDepartureTime", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('#pjSbBookingDetailsFlightDepartureTimeModal_' + self.opts.index).modal('show');
            }).on("click.tr", ".pjSbBookingDetailReturnDate", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('#pjSbBookingDetailsReturnDateModal_' + self.opts.index).modal('show');
            }).on("click.tr", ".pjSbBookingDetailsReturnPickupTime", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('#pjSbBookingDetailsReturnPickupTimeModal_' + self.opts.index).modal('show');
            }).on("click.tr", ".pjSbBookingDetailsReturnFlightDepartureTime", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('#pjSbBookingDetailsReturnFlightDepartureTimeModal_' + self.opts.index).modal('show');
            }).on("click.tr", ".pjSbBookingDetailsReturnTime", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('#pjSbBookingDetailsReturnTimeModal_' + self.opts.index).modal('show');
            });
                        
            
            if (self.opts.load_summary == 1) {
            	self.loadSummary.call(self, self.opts.booking_id);
            } else if (self.opts.load_payment == 1) {
            	self.loadPayment.call(self, self.opts.booking_uuid);
            } else {
            	self.loadSearch.call(self);
            }
		},
		loadSearch: function() {
			var self = this,
				index = this.opts.index,
				params = 	{
								"locale": this.opts.locale,
								"hide": this.opts.hide,
								"index": this.opts.index
							};
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionSearch", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
				self.$container.html(data);
				pjQ.$('.modal-dialog').css("z-index", "9999");
				
				if(pjQ.$('#trReturnOn_' + index).is(':checked'))
				{
					pjQ.$('.trReturnField').prop('disabled', false);
				}else{
					pjQ.$('.trReturnField').prop('disabled', 'disabled');
				}
				
                self.bindSearch.call(self);
                if(parseInt(pjQ.$('#autoloadNextStep_' + index).val(), 10) == 1)
                {
                    // pjQ.$('html, body').animate({
                    //     scrollTop: self.$container.offset().top
                    // }, 0);
                	
                    //self.loadServices.call(self);
                	setTimeout(function() {
                		pjQ.$('#trSearchForm_' + index).trigger('submit');
                	}, 1000);
                }
                else
                {
					/*
                    pjQ.$('html, body').animate({
                        scrollTop: self.$container.offset().top
                    }, 500);
					*/
                    self.pageLoaded.call(self);
                }
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		bindSearch: function(){
			var self = this;
            var $form = pjQ.$('#trSearchForm_' + self.opts.index);

            if(pjQ.$('#pjSbCalendarLocale').length > 0)
			{
				var fday = parseInt(pjQ.$('#pjSbCalendarLocale').data('fday'), 10);
				moment.updateLocale('en', {
					months : pjQ.$('#pjSbCalendarLocale').data('months').split("_"),
			        weekdaysMin : pjQ.$('#pjSbCalendarLocale').data('days').split("_"),
			        week: { dow: fday }
				});
			}
			if(pjQ.$('#trTransferDatePick_' + self.opts.index).length > 0)
			{
				var currentDate = new Date();
				if(pjQ.$('#trTransferDatePick_' + self.opts.index).length > 0)
				{
					pjQ.$('#trTransferDatePick_' + self.opts.index).datetimepicker({
						format: self.opts.momentDateFormat.toUpperCase(),
						locale: moment.locale('en'),
						allowInputToggle: true,
						minDate: new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate()),
						ignoreReadonly: true,
						useCurrent: false,
						inline: true,
						sideBySide: true
					});
					pjQ.$('#trTransferDatePick_' + self.opts.index).on('dp.change', function (e) {
						var $transfer_date = pjQ.$('#trTransferDatePick_' + self.opts.index).data('date');
						pjQ.$('#trDate_' + self.opts.index).val($transfer_date);
						pjQ.$('#pjSbTransferDateModal_' + self.opts.index).modal('hide');
						if(pjQ.$('#trDate_' + self.opts.index).val() != '')
						{
							var toDate = new Date(e.date);
							toDate.setDate(toDate.getDate());
							var momentDate = new moment(toDate);
							pjQ.$('#trReturnTransferDatePick_' + self.opts.index).datetimepicker().children('input').val(momentDate.format(self.opts.momentDateFormat.toUpperCase()));
							pjQ.$('#trReturnTransferDatePick_' + self.opts.index).data("DateTimePicker").minDate(e.date);
							
							if(parseInt(pjQ.$('#autoloadNextStep_' + self.opts.index).val(), 10) == 1)
			                {
								$form.trigger('submit');
			                }
						}
					});
				}
				if(pjQ.$('#trReturnTransferDatePick_' + self.opts.index).length > 0)
				{
					pjQ.$('#trReturnTransferDatePick_' + self.opts.index).datetimepicker({
						format: self.opts.momentDateFormat.toUpperCase(),
						locale: moment.locale('en'),
						allowInputToggle: true,
						ignoreReadonly: true,
						useCurrent: false,
						minDate: new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate()),
						inline: true,
						sideBySide: true
					});	
					pjQ.$('#trReturnTransferDatePick_' + self.opts.index).on('dp.change', function (e) {
						var $return_transfer_date = pjQ.$('#trReturnTransferDatePick_' + self.opts.index).data('date');
						pjQ.$('#trReturnDate_' + self.opts.index).val($return_transfer_date);
						pjQ.$('#pjSbReturnTransferDateModal_' + self.opts.index).modal('hide');
						if(parseInt(pjQ.$('#autoloadNextStep_' + self.opts.index).val(), 10) == 1)
		                {
							$form.trigger('submit');
		                }
					});
				}
			}
            
            $form.find('input[type=radio], input[type=checkbox],input[type=number], select:not(.select2)').uniform();
            
            pjQ.$.fn.select2.amd.define('select2/data/extended-ajax', ['./ajax', './tags', '../utils', 'module', 'jquery'], function(AjaxAdapter, Tags, Utils, module, $) {
                function ExtendedAjaxAdapter($element, options) {
                    this.minimumInputLength = options.get('minimumInputLength');
                    this.defaultResults = options.get('defaultResults');

                    ExtendedAjaxAdapter.__super__.constructor.call(this, $element, options);
                }
                Utils.Extend(ExtendedAjaxAdapter, AjaxAdapter);

                var originQuery = AjaxAdapter.prototype.query;

                ExtendedAjaxAdapter.prototype.query = function(params, callback) {
                    var defaultResults = (typeof this.defaultResults == 'function') ? this.defaultResults.call(this) : this.defaultResults;
                    if (defaultResults && defaultResults.length && (!params.term || params.term.length < this.minimumInputLength)) {
                        var data = {
                            results: defaultResults
                        };
                        var processedResults = this.processResults(data, params);
                        callback(processedResults);
                    } else {
                        originQuery.call(this, params, callback);
                    }
                };

                if (module.config().tags) {
                    return Utils.Decorate(ExtendedAjaxAdapter, Tags);
                } else {
                    return ExtendedAjaxAdapter;
                }
            });
            
            var $pickupSelect2 = pjQ.$('#trLocationId_' + self.opts.index),
            	$dropoffSelect2 = pjQ.$('#trDropoffId_' + self.opts.index);
            
            var $defaultResults = pjQ.$('option[value]', $pickupSelect2);
            var defaultResults = [];
            $defaultResults.each(function() {
            	var $option = pjQ.$(this);
            	defaultResults.push({
            		id: $option.attr('value'),
            		icon: $option.attr('data-icon'),
            		text: $option.text()
            	});
            });

            $pickupSelect2.select2({
            	minimumInputLength: 3,
            	ajax: {
            		delay: 250,
            		url: self.opts.folder + 'index.php?controller=pjFront&action=pjActionSearchLocations',
            		cache: true,
            		delay: 0,
            		templateResult: function(state) {
            			return pjQ.$('<span><i class="' + pjQ.$(state.element).data('icon') + '"></i><span>' + state.text + '</span></span>');
            		}
            	},
            	dataAdapter: pjQ.$.fn.select2.amd.require('select2/data/extended-ajax'),
            	defaultResults: defaultResults,
            	templateResult: formatRepo,
            	"language": {
            		"searching": function() {
                        return self.opts.i18n.searching;
                    },
                    "errorLoading": function () {
                    	return self.opts.i18n.searching;
                    },
                    "noResults": function(){
                        return self.opts.i18n.locations_empty;
                    }
                }
            });

            function formatRepo(repo) {
            	if (repo.icon !== undefined) {
            		return pjQ.$('<span><i class="' + repo.icon + '"></i><span>' + repo.text + '</span></span>');
            	} else {
            		return repo.text;
            	}
            }
            
            var $defaultResults = pjQ.$('option[value]', $dropoffSelect2);
            var defaultResults = [];
            $defaultResults.each(function() {
            	var $option = pjQ.$(this);
            	defaultResults.push({
            		id: $option.attr('value'),
            		icon: $option.attr('data-icon'),
            		text: $option.text()
            	});
            });

            $dropoffSelect2.select2({
            	minimumInputLength: 3,
            	ajax: {
            		delay: 250,
            		url: self.opts.folder + 'index.php?controller=pjFront&action=pjActionSearchLocations&dropoff=1',
            		cache: true,
            		delay: 0,
            		templateResult: function(state) {
            			return pjQ.$('<span><i class="' + pjQ.$(state.element).data('icon') + '"></i><span>' + state.text + '</span></span>');
            		}
            	},
            	dataAdapter: pjQ.$.fn.select2.amd.require('select2/data/extended-ajax'),
            	defaultResults: defaultResults,
            	templateResult: formatRepo,
            	"language": {
            		"searching": function() {
                        return self.opts.i18n.searching;
                    },
                    "errorLoading": function () {
                    	return self.opts.i18n.searching;
                    },
                    "noResults": function(){
                        return self.opts.i18n.locations_empty;
                    }
                }
            });

            var $custom_pickup_address = pjQ.$('#custom_pickup_address_' + self.opts.index);
            if ($custom_pickup_address.length > 0) {
            	var $newPickupOption = new Option($custom_pickup_address.val(), pjQ.$('#custom_pickup_address_id_' + self.opts.index).val(), true, true);
        		$pickupSelect2.append($newPickupOption);
            }
        	
            var $custom_dropoff_address = pjQ.$('#custom_dropoff_address_' + self.opts.index);
            if ($custom_dropoff_address.length > 0) {
            	var $newDropoffOption = new Option($custom_dropoff_address.val(), pjQ.$('#custom_dropoff_address_id_' + self.opts.index).val(), true, true);
            	$dropoffSelect2.append($newDropoffOption);
            }
			
			var $dropoff = pjQ.$('#trDropoffId_' + self.opts.index).val();
			if ($dropoff.length > 0) {
				pjQ.$('#trDropoffId_' + self.opts.index).trigger('change');
			}
			
            $form.validate({
            	submitHandler: function (form) {
                	var $container = pjQ.$('#trBookingStep_Services_' + self.opts.index);
    				$container.empty();
    	            $container.nextAll('[id^="trBookingStep_"]').empty();
                    self.disableButtons.call(self);
                    var $msg_container = $form.find('.trCheckErrorMsg');
                    $msg_container.find('.alert').html('');
                    $msg_container.hide();
                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionCheck", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                    	if (data) 
						{
							switch (parseInt(data.code, 10)) {
	                            case 100:
	                            	$msg_container.find('.alert').html(self.opts.messages.no_fleet);
	                            	$msg_container.show().fadeOut(10000);
	                                self.enableButtons.call(self);
	                                break;
	                            case 101:
	                            	$msg_container.find('.alert').html(self.opts.messages.invalid_date);
	                            	$msg_container.show().fadeOut(10000);
	                                self.enableButtons.call(self);
	                                break;
	                            case 102:
	                            	$msg_container.find('.alert').html(data.text);
	                            	$msg_container.show().fadeOut(10000);
	                                self.enableButtons.call(self);
	                                break;
	                            case 200:
	                                self.page = 1;
	                                self.loadServices.call(self);
	                                break;
	                        }
						} else {
							 self.enableButtons.call(self);
						}
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
                    return false;
                }
            });

            if ($form.find('[data-skip-first-step]').length > 0) {
                $form.trigger('submit');
            }
		},
		loadServices: function () {
			var self = this,
				index = this.opts.index,
                params = 	{
                                "locale": this.opts.locale,
                                "hide": this.opts.hide,
                                "index": this.opts.index,
                                "page": this.page,
                            };
            var $container = pjQ.$('#trBookingStep_Services_' + self.opts.index);
            $container.nextAll('[id^="trBookingStep_"]').empty();

            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionServices", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                $container.html(data);
                self.enableButtons.call(self);

                if(isPageLoaded)
                {
                    pjQ.$('html, body').animate({
                        scrollTop: $container.offset().top
                    }, 500);
                }
                else
                {
                    self.pageLoaded.call(self);
                }
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		bindCheckout: function () {
			var self = this;
			var $form = pjQ.$('#trCheckoutForm_' + self.opts.index);
			
			pjQ.$('.payment-methods .payment-method').each(function() {
				var $h = pjQ.$(this).find('.payment-method-info').height();
				pjQ.$(this).find('.radio').css('top', ($h/2)-20 + 'px');
			});
			
			pjQ.$(window).resize(function(){
				pjQ.$('.payment-methods .payment-method').each(function() {
					var $h = pjQ.$(this).find('.payment-method-info').height();
					pjQ.$(this).find('.radio').css('top', ($h/2)-20 + 'px');
				});
			});
			
			$form.validate({
				submitHandler: function (form) {
                    // self.disableButtons.call(self);
                    var $msg_container = pjQ.$('#trBookingMsg_' + self.opts.index);
                    $msg_container.find('span').text(self.opts.message_0);
                    $msg_container.css('display', 'block');
                    pjQ.$('.pjCrBookingSesstionExpired').hide();
                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveBooking", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                        if (!data.code) {
                            return;
                        }
                        switch (parseInt(data.code, 10)) {
                            case 100:
                                $msg_container.find('span').text(self.opts.message_4);
                                self.enableButtons.call(self);
                                break;
                            case 102:
                            	self.disableButtons.call(self);
                            	$msg_container.css('display', 'none');
                            	pjQ.$('.pjCrBookingSesstionExpired').show();
                            	pjQ.$('.pjCrRestartBooking').removeAttr("disabled").removeClass("trButtonDisable");
                            	pjQ.$('html, body').animate({
                                    scrollTop: pjQ.$('.pjCrBookingSesstionExpired').offset().top
                                }, 500);
                                break;
                            case 200:
                                self.loadSummary.call(self, data.booking_id);
                                break;
                        }
                    });
					return false;
				}
			});
		},
        loadTransferType: function () {
            var self = this,
                index = this.opts.index,
                params = 	{
                    "locale": this.opts.locale,
                    "hide": this.opts.hide,
                    "index": this.opts.index
                };
            var $container = pjQ.$('#trBookingStep_TransferType_' + self.opts.index);
            $container.nextAll('[id^="trBookingStep_"]').empty();

            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionTransferType", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                $container.html(data);

                $container.find('input[type=radio]').uniform();

                pjQ.$('html, body').animate({
                    scrollTop: $container.offset().top
                }, 500);
                self.enableButtons.call(self);
            }).fail(function () {
                self.enableButtons.call(self);
            });
        },
        loadExtras: function () {
            var self = this,
                params = 	{
                    "locale": this.opts.locale,
                    "hide": this.opts.hide,
                    "index": this.opts.index
                };
            var $container = pjQ.$('#trBookingStep_Extras_' +  + self.opts.index);
            $container.nextAll('[id^="trBookingStep_"]').empty();

            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionExtras", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                $container.html(data);

                $container.find('select:not(.select2)').uniform();

                pjQ.$('html, body').animate({
                    scrollTop: $container.offset().top
                }, 500);
                self.enableButtons.call(self);
            }).fail(function () {
                self.enableButtons.call(self);
            });
        },
        loadDeparture: function (scrollToDeparture, scrollToReturn) {
            var self = this,
                index = this.opts.index,
                params = 	{
                    "locale": this.opts.locale,
                    "hide": this.opts.hide,
                    "index": this.opts.index
                };
            var $container = pjQ.$('#trBookingStep_Departure_' + self.opts.index);
            $container.nextAll('[id^="trBookingStep_"]').empty();

            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionDeparture", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                $container.html(data);
                pjQ.$('.modal-dialog').css("z-index", "9999");
                self.bindDeparture.call(self);
                if (scrollToDeparture == 1) {
	                pjQ.$('html, body').animate({
	                    scrollTop: $container.offset().top
	                }, 500);
	                self.enableButtons.call(self);
                }
                var $form = pjQ.$('#trDepartureForm_' + self.opts.index),
                	$is_return = parseInt(pjQ.$('#trIsReturn_' + self.opts.index).val(), 10);
                if ($is_return) {
                	self.loadReturn.call(self, scrollToReturn);
                }
            }).fail(function () {
                self.enableButtons.call(self);
            });
        },
        bindDeparture: function(){
            var self = this;
            var $form = pjQ.$('#trDepartureForm_' + self.opts.index);

            if(pjQ.$('#pjSbCalendarLocale').length > 0)
			{
				var fday = parseInt(pjQ.$('#pjSbCalendarLocale').data('fday'), 10);
				moment.updateLocale('en', {
					months : pjQ.$('#pjSbCalendarLocale').data('months').split("_"),
			        weekdaysMin : pjQ.$('#pjSbCalendarLocale').data('days').split("_"),
			        week: { dow: fday }
				});
			}
            var currentDate = new Date();
            if($form.find("#trDateConfirm_" + self.opts.index).length > 0)
            {
            	if(pjQ.$('#trBookingDetailsTransferDatePick_' + self.opts.index).length > 0)
				{
					pjQ.$('#trBookingDetailsTransferDatePick_' + self.opts.index).datetimepicker({
						format: self.opts.momentDateFormat.toUpperCase(),
						locale: moment.locale('en'),
						allowInputToggle: true,
						minDate: new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate()),
						ignoreReadonly: true,
						useCurrent: false,
						inline: true,
						sideBySide: true
					});
					pjQ.$('#trBookingDetailsTransferDatePick_' + self.opts.index).on('dp.change', function (e) {
						var $transfer_date = pjQ.$('#trBookingDetailsTransferDatePick_' + self.opts.index).data('date');
						pjQ.$('#trDateConfirm_' + self.opts.index).val($transfer_date);
						pjQ.$('#pjSbBookingDetailsTransferDateModal_' + self.opts.index).modal('hide');
						
						if(pjQ.$('#trDateConfirm_' + self.opts.index).val() != '')
						{
							var toDate = new Date(e.date);
							var $transfer_date = pjQ.$('#trDateConfirm_' + self.opts.index).val();
		                    pjQ.$('dd.trCartDepartureDate').html($transfer_date);
		                    pjQ.$('.trCartDepartureDate').toggle($transfer_date.length > 0);
		                    if ($transfer_date != pjQ.$('#trDateOriginal_' + self.opts.index).val()) {
		                    	pjQ.$('#trDateConfirmMsg_' + self.opts.index).show();
		                    }
		                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveDeparture", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
		                    	self.loadCart.call(self, pjQ.$('.pjSbCartWrap_pjActionDeparture'));
		                    	self.enableButtons.call(self);
		                    }).fail(function () {
		                        self.enableButtons.call(self);
		                    });
		                    
		                    if (pjQ.$('#trBookingDetailsReturnTransferDatePick_' + self.opts.index).length > 0) {
			                    toDate.setDate(toDate.getDate());
								var momentDate = new moment(toDate);
								pjQ.$('#trBookingDetailsReturnTransferDatePick_' + self.opts.index).datetimepicker().children('input').val(momentDate.format(self.opts.momentDateFormat.toUpperCase()));
								pjQ.$('#trBookingDetailsReturnTransferDatePick_' + self.opts.index).data("DateTimePicker").minDate(e.date);
		                    }
						}
					});
				}
            }
            
            if (pjQ.$('#trBookingDetailsArrivalTimePick_' + self.opts.index).length > 0) {
            	pjQ.$('#trBookingDetailsArrivalTimePick_' + self.opts.index).datetimepicker({
    				format: self.opts.time_format,
    				ignoreReadonly: true,
    				allowInputToggle: true,
    				stepping: 5,
    				toolbarPlacement: 'bottom',
    				focusOnShow: true,
    				showClose: true,
    				keepOpen: true,
    				inline: true,
    				sideBySide: true
    			}).on('dp.hide', function (e) {
    				var $arrival_time = pjQ.$('#trBookingDetailsArrivalTimePick_' + self.opts.index).data('date');
                    pjQ.$('#arrival_time').val($arrival_time);
                    pjQ.$('#pjSbBookingDetailsArrivalTimeModal_' + self.opts.index).modal('hide');
                    
    				pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveDeparture", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
    					self.loadCart.call(self, pjQ.$('.pjSbCartWrap_pjActionDeparture'));
                    	self.enableButtons.call(self);
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
    			}).on("dp.show", function (e) {
    				pjQ.$('#pjSbBookingDetailsArrivalTimeModal_' + self.opts.index).find('.picker-switch a').html(pjQ.$('#trBookingDetailsArrivalTimePick_' + self.opts.index).attr('data-label_done'));
    				pjQ.$('#pjSbBookingDetailsArrivalTimeModal_' + self.opts.index).find('.picker-switch a').show();
                });
            }
            
            pjQ.$('#pjSbBookingDetailsArrivalTimeModal_' + self.opts.index).on("show.bs.modal", function () {
            	setTimeout(function (){
            		if (pjQ.$('#trBookingDetailsArrivalTimePick_' + self.opts.index).length > 0) {
            			var $obj = pjQ.$('#trBookingDetailsArrivalTimePick_' + self.opts.index);
            			$obj.focus();
                    	
                        pjQ.$('#pjSbBookingDetailsArrivalTimeModal_' + self.opts.index).find('.picker-switch a').html($obj.attr('data-label_done'));
                    	pjQ.$('#pjSbBookingDetailsArrivalTimeModal_' + self.opts.index).find('.picker-switch a').show();
                    }
                }, 1000);            	
			});
            
            if (pjQ.$('#trBookingDetailsPickupTimePick_' + self.opts.index).length > 0) {
            	pjQ.$('#trBookingDetailsPickupTimePick_' + self.opts.index).datetimepicker({
    				format: self.opts.time_format,
    				ignoreReadonly: true,
    				allowInputToggle: true,
    				stepping: 5,
    				toolbarPlacement: 'bottom',
    				focusOnShow: true,
    				showClose: true,
    				keepOpen: true,
    				inline: true,
    				sideBySide: true
    			}).on('dp.hide', function (e) {
    				var $pickup_time = pjQ.$('#trBookingDetailsPickupTimePick_' + self.opts.index).data('date');
                    pjQ.$('#pickup_time').val($pickup_time);
                    pjQ.$('#pjSbBookingDetailsPickupTimeModal_' + self.opts.index).modal('hide');
                    
    				pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveDeparture", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
    					self.loadCart.call(self, pjQ.$('.pjSbCartWrap_pjActionDeparture'));
                    	self.enableButtons.call(self);
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
    			}).on("dp.show", function (e) {
    				pjQ.$('#pjSbBookingDetailsPickupTimeModal_' + self.opts.index).find('.picker-switch a').html(pjQ.$('#trBookingDetailsPickupTimePick_' + self.opts.index).attr('data-label_done'));
    				pjQ.$('#pjSbBookingDetailsPickupTimeModal_' + self.opts.index).find('.picker-switch a').show();
                });
            }
            
            pjQ.$('#pjSbBookingDetailsPickupTimeModal_' + self.opts.index).on("show.bs.modal", function () {
            	setTimeout(function (){
            		if (pjQ.$('#trBookingDetailsPickupTimePick_' + self.opts.index).length > 0) {
            			var $obj = pjQ.$('#trBookingDetailsPickupTimePick_' + self.opts.index);
            			$obj.focus();
                    	
                        pjQ.$('#pjSbBookingDetailsPickupTimeModal_' + self.opts.index).find('.picker-switch a').html($obj.attr('data-label_done'));
                    	pjQ.$('#pjSbBookingDetailsPickupTimeModal_' + self.opts.index).find('.picker-switch a').show();
                    }
                }, 1000);            	
			});
            
            
            if (pjQ.$('#trBookingDetailsFlightDepartureTimePick_' + self.opts.index).length > 0) {
            	pjQ.$('#trBookingDetailsFlightDepartureTimePick_' + self.opts.index).datetimepicker({
    				format: self.opts.time_format,
    				ignoreReadonly: true,
    				allowInputToggle: true,
    				stepping: 5,
    				toolbarPlacement: 'bottom',
    				focusOnShow: true,
    				showClose: true,
    				keepOpen: true,
    				inline: true,
    				sideBySide: true
    			}).on('dp.hide', function (e) {
    				var $c_departure_flight_time = pjQ.$('#trBookingDetailsFlightDepartureTimePick_' + self.opts.index).data('date');
                    pjQ.$('#c_departure_flight_time').val($c_departure_flight_time);
                    pjQ.$('#pjSbBookingDetailsFlightDepartureTimeModal_' + self.opts.index).modal('hide');
                    
    				pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveDeparture", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
    					self.loadCart.call(self, pjQ.$('.pjSbCartWrap_pjActionDeparture'));
                    	self.enableButtons.call(self);
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
    			}).on("dp.show", function (e) {
    				pjQ.$('#pjSbBookingDetailsFlightDepartureTimeModal_' + self.opts.index).find('.picker-switch a').html(pjQ.$('#trBookingDetailsFlightDepartureTimePick_' + self.opts.index).attr('data-label_done'));
    				pjQ.$('#pjSbBookingDetailsFlightDepartureTimeModal_' + self.opts.index).find('.picker-switch a').show();
                });
            }
            
            pjQ.$('#pjSbBookingDetailsFlightDepartureTimeModal_' + self.opts.index).on("show.bs.modal", function () {
            	setTimeout(function (){
            		if (pjQ.$('#trBookingDetailsFlightDepartureTimePick_' + self.opts.index).length > 0) {
            			var $obj = pjQ.$('#trBookingDetailsFlightDepartureTimePick_' + self.opts.index);
            			$obj.focus();
                    	
                        pjQ.$('#pjSbBookingDetailsFlightDepartureTimeModal_' + self.opts.index).find('.picker-switch a').html($obj.attr('data-label_done'));
                    	pjQ.$('#pjSbBookingDetailsFlightDepartureTimeModal_' + self.opts.index).find('.picker-switch a').show();
                    }
                }, 1000);            	
			});
            
            /*
            pjQ.$('.pjSbTimePick').datetimepicker({
				format: self.opts.time_format,
				ignoreReadonly: true,
				allowInputToggle: true,
				stepping: 5,
				toolbarPlacement: 'bottom',
				showClose: true
			}).on('dp.hide', function (e) {
				pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveDeparture", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
					self.loadCart.call(self, pjQ.$('.pjSbCartWrap_pjActionDeparture'));
                	self.enableButtons.call(self);
                }).fail(function () {
                    self.enableButtons.call(self);
                });
			}).on("dp.show", function (e) {
                pjQ.$(this).find('.picker-switch a').html(pjQ.$(this).attr('data-label_done'));
            });
            */

            $form.find('input[type=radio], input[type=checkbox],input[type=number], select:not(.select2)').uniform();

            $form.validate({
                submitHandler: function (form) {
                    self.disableButtons.call(self);
                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveDeparture&submit=1", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                        switch (parseInt(data.code, 10)) {
                            case 200:
                            	self.loadPassenger.call(self);
                                break;
                            default:
                                $form.find('.trCheckErrorMsg').html(self.opts.messages.generic_error).show().fadeOut(3000);
                                self.enableButtons.call(self);
                        }
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
                    return false;
                }
            });
        },
        loadReturn: function (scrollToReturn) {
            var self = this,
                index = this.opts.index,
                params = 	{
                    "locale": this.opts.locale,
                    "hide": this.opts.hide,
                    "index": this.opts.index
                };
            var $container = pjQ.$('#trBookingStep_Return_' + self.opts.index);
            $container.nextAll('[id^="trBookingStep_"]').empty();

            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionReturn", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                $container.html(data);
                pjQ.$('.modal-dialog').css("z-index", "9999");
                self.bindReturn.call(self);
                if (scrollToReturn == 1) {
	                pjQ.$('html, body').animate({
	                    scrollTop: $container.offset().top
	                }, 500);
                }
                self.enableButtons.call(self);
            }).fail(function () {
                self.enableButtons.call(self);
            });
        },
        bindReturn: function(){
            var self = this;
            var $form = pjQ.$('#trDepartureForm_' + self.opts.index);

            if(pjQ.$('#pjSbReturnCalendarLocale').length > 0)
			{
				var fday = parseInt(pjQ.$('#pjSbReturnCalendarLocale').data('fday'), 10);
				moment.updateLocale('en', {
					months : pjQ.$('#pjSbCalendarLocale').data('months').split("_"),
			        weekdaysMin : pjQ.$('#pjSbCalendarLocale').data('days').split("_"),
			        week: { dow: fday }
				});
			}
            var currentDate = new Date();
            if($form.find("#trReturnDate_" + self.opts.index).length > 0)
            {
            	var $start_date = pjQ.$("#trDateConfirm_" + self.opts.index).val();
            	if ($start_date != '') {
            		var $moment_start_date = moment($start_date, self.opts.momentDateFormat.toUpperCase()).toDate(),
            			$min_date = new Date($moment_start_date.getFullYear(), $moment_start_date.getMonth(), $moment_start_date.getDate());
            	} else {
            		var $min_date = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate());
            	}
            	pjQ.$('#trBookingDetailsReturnDatePick_' + self.opts.index).datetimepicker({
					format: self.opts.momentDateFormat.toUpperCase(),
					locale: moment.locale('en'),
					allowInputToggle: true,
					minDate: $min_date,
					ignoreReadonly: true,
					useCurrent: false,
					inline: true,
					sideBySide: true
				});
            	pjQ.$('#trBookingDetailsReturnDatePick_' + self.opts.index).on('dp.change', function (e) {
            		var $return_date = pjQ.$('#trBookingDetailsReturnDatePick_' + self.opts.index).data('date');
					pjQ.$('.pjSbReturnInfo').find('input[name="return_date"]').val($return_date);
					pjQ.$('#pjSbBookingDetailsReturnDateModal_' + self.opts.index).modal('hide');
					
					if(pjQ.$('#trReturnDate_' + self.opts.index).val() != '')
					{
						var toDate = new Date(e.date);
						var $transfer_date = pjQ.$('#trReturnDate_' + self.opts.index).val();
	                    pjQ.$('dd.trCartReturnDate').html($transfer_date);
	                    pjQ.$('.trCartReturnDate').toggle($transfer_date.length > 0);
	                    
	                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveReturn", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
	                    	self.loadCart.call(self, pjQ.$('.pjSbCartWrap_pjActionDeparture'));
	                    	self.enableButtons.call(self);
	                    }).fail(function () {
	                        self.enableButtons.call(self);
	                    });
					}
				});
            }
            
            if (pjQ.$('#trBookingDetailsReturnPickupTimePick_' + self.opts.index).length > 0) {
            	pjQ.$('#trBookingDetailsReturnPickupTimePick_' + self.opts.index).datetimepicker({
    				format: self.opts.time_format,
    				ignoreReadonly: true,
    				allowInputToggle: true,
    				stepping: 5,
    				toolbarPlacement: 'bottom',
    				focusOnShow: true,
    				showClose: true,
    				keepOpen: true,
    				inline: true,
    				sideBySide: true
    			}).on('dp.hide', function (e) {
    				var $return_pickup_time = pjQ.$('#trBookingDetailsReturnPickupTimePick_' + self.opts.index).data('date');
                    pjQ.$('#return_pickup_time').val($return_pickup_time);
                    pjQ.$('#pjSbBookingDetailsReturnPickupTimeModal_' + self.opts.index).modal('hide');
                    
                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveReturn", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
    					self.loadCart.call(self, pjQ.$('.pjSbCartWrap_pjActionDeparture'));
                    	self.enableButtons.call(self);
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
    			}).on("dp.show", function (e) {
    				pjQ.$('#pjSbBookingDetailsReturnPickupTimeModal_' + self.opts.index).find('.picker-switch a').html(pjQ.$('#trBookingDetailsReturnPickupTimePick_' + self.opts.index).attr('data-label_done'));
    				pjQ.$('#pjSbBookingDetailsReturnPickupTimeModal_' + self.opts.index).find('.picker-switch a').show();
                });
            }
            
            pjQ.$('#pjSbBookingDetailsReturnPickupTimeModal_' + self.opts.index).on("show.bs.modal", function () {
            	setTimeout(function (){
            		if (pjQ.$('#trBookingDetailsReturnPickupTimePick_' + self.opts.index).length > 0) {
            			var $obj = pjQ.$('#trBookingDetailsReturnPickupTimePick_' + self.opts.index);
            			$obj.focus();
                    	
                        pjQ.$('#pjSbBookingDetailsReturnPickupTimeModal_' + self.opts.index).find('.picker-switch a').html($obj.attr('data-label_done'));
                    	pjQ.$('#pjSbBookingDetailsReturnPickupTimeModal_' + self.opts.index).find('.picker-switch a').show();
                    }
                }, 1000);            	
			});
            
            if (pjQ.$('#trBookingDetailsReturnFlightDepartureTimePick_' + self.opts.index).length > 0) {
            	pjQ.$('#trBookingDetailsReturnFlightDepartureTimePick_' + self.opts.index).datetimepicker({
    				format: self.opts.time_format,
    				ignoreReadonly: true,
    				allowInputToggle: true,
    				stepping: 5,
    				toolbarPlacement: 'bottom',
    				focusOnShow: true,
    				showClose: true,
    				keepOpen: true,
    				inline: true,
    				sideBySide: true
    			}).on('dp.hide', function (e) {
    				var $return_c_departure_flight_time = pjQ.$('#trBookingDetailsReturnFlightDepartureTimePick_' + self.opts.index).data('date');
                    pjQ.$('#return_c_departure_flight_time').val($return_c_departure_flight_time);
                    pjQ.$('#pjSbBookingDetailsReturnFlightDepartureTimeModal_' + self.opts.index).modal('hide');
                    
                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveReturn", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
    					self.loadCart.call(self, pjQ.$('.pjSbCartWrap_pjActionDeparture'));
                    	self.enableButtons.call(self);
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
    			}).on("dp.show", function (e) {
    				pjQ.$('#pjSbBookingDetailsReturnFlightDepartureTimeModal_' + self.opts.index).find('.picker-switch a').html(pjQ.$('#trBookingDetailsReturnFlightDepartureTimePick_' + self.opts.index).attr('data-label_done'));
    				pjQ.$('#pjSbBookingDetailsReturnFlightDepartureTimeModal_' + self.opts.index).find('.picker-switch a').show();
                });
            }
            
            pjQ.$('#pjSbBookingDetailsReturnFlightDepartureTimeModal_' + self.opts.index).on("show.bs.modal", function () {
            	setTimeout(function (){
            		if (pjQ.$('#trBookingDetailsReturnFlightDepartureTimePick_' + self.opts.index).length > 0) {
            			var $obj = pjQ.$('#trBookingDetailsReturnFlightDepartureTimePick_' + self.opts.index);
            			$obj.focus();
                    	
                        pjQ.$('#pjSbBookingDetailsReturnFlightDepartureTimeModal_' + self.opts.index).find('.picker-switch a').html($obj.attr('data-label_done'));
                    	pjQ.$('#pjSbBookingDetailsReturnFlightDepartureTimeModal_' + self.opts.index).find('.picker-switch a').show();
                    }
                }, 1000);            	
			});
            
            if (pjQ.$('#trBookingDetailsReturnTimePick_' + self.opts.index).length > 0) {
            	pjQ.$('#trBookingDetailsReturnTimePick_' + self.opts.index).datetimepicker({
    				format: self.opts.time_format,
    				ignoreReadonly: true,
    				allowInputToggle: true,
    				stepping: 5,
    				toolbarPlacement: 'bottom',
    				focusOnShow: true,
    				showClose: true,
    				keepOpen: true,
    				inline: true,
    				sideBySide: true
    			}).on('dp.hide', function (e) {
    				var $return_time = pjQ.$('#trBookingDetailsReturnTimePick_' + self.opts.index).data('date');
                    pjQ.$('#return_time').val($return_time);
                    pjQ.$('#pjSbBookingDetailsReturnTimeModal_' + self.opts.index).modal('hide');
                    
                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveReturn", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
    					self.loadCart.call(self, pjQ.$('.pjSbCartWrap_pjActionDeparture'));
                    	self.enableButtons.call(self);
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
    			}).on("dp.show", function (e) {
    				pjQ.$('#pjSbBookingDetailsReturnTimeModal_' + self.opts.index).find('.picker-switch a').html(pjQ.$('#trBookingDetailsReturnTimePick_' + self.opts.index).attr('data-label_done'));
    				pjQ.$('#pjSbBookingDetailsReturnTimeModal_' + self.opts.index).find('.picker-switch a').show();
                });
            }
            
            pjQ.$('#pjSbBookingDetailsReturnTimeModal_' + self.opts.index).on("show.bs.modal", function () {
            	setTimeout(function (){
            		if (pjQ.$('#trBookingDetailsReturnTimePick_' + self.opts.index).length > 0) {
            			var $obj = pjQ.$('#trBookingDetailsReturnTimePick_' + self.opts.index);
            			$obj.focus();
                    	
                        pjQ.$('#pjSbBookingDetailsReturnTimeModal_' + self.opts.index).find('.picker-switch a').html($obj.attr('data-label_done'));
                    	pjQ.$('#pjSbBookingDetailsReturnTimeModal_' + self.opts.index).find('.picker-switch a').show();
                    }
                }, 1000);            	
			});
            
            /*
            pjQ.$('.pjSbReturnTimePick').datetimepicker({
				format: self.opts.time_format,
				ignoreReadonly: true,
				allowInputToggle: true,
				stepping: 5,
				toolbarPlacement: 'bottom',
				showClose: true
			}).on('dp.hide', function (e) {
				pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveReturn", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
					self.loadCart.call(self, pjQ.$('.pjSbCartWrap_pjActionDeparture'));
                	self.enableButtons.call(self);
                }).fail(function () {
                    self.enableButtons.call(self);
                });
			}).on("dp.show", function (e) {
                pjQ.$(this).find('.picker-switch a').html(pjQ.$(this).attr('data-label_done'));
            });
            */
        },
        loadCart: function ($obj) {
            var self = this,
                index = this.opts.index,
                params = 	{
                    "locale": this.opts.locale,
                    "hide": this.opts.hide,
                    "index": this.opts.index
                };
            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionCart", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                $obj.html(data);
                self.enableButtons.call(self);
            }).fail(function () {
                self.enableButtons.call(self);
            });
        },
        loadPassenger: function () {
            var self = this,
                index = this.opts.index,
                params = 	{
                    "locale": this.opts.locale,
                    "hide": this.opts.hide,
                    "index": this.opts.index
                };
            var $container = pjQ.$('#trBookingStep_Passenger_' + self.opts.index);
            $container.nextAll('[id^="trBookingStep_"]').empty();

            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionPassenger", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                $container.html(data);

                self.bindPassenger.call(self);
                pjQ.$('html, body').animate({
                    scrollTop: $container.offset().top
                }, 500);
                self.enableButtons.call(self);
            }).fail(function () {
                self.enableButtons.call(self);
            });
        },
        bindPassenger: function(){
            var self = this;
            var $form = pjQ.$('#trPassengerForm_' + self.opts.index);

            $form.find('input[type=radio], input[type=checkbox],input[type=number], select:not(.select2)').uniform();
            $form.find('select.select2').select2({ width: "100%" });

            pjQ.$('#trPassengerForm_' + self.opts.index + ' input').on('keypress', function(e) {
            	if (pjQ.$(this).attr('name') == 'voucher_code') {
            		if (e.which !== 13) {
            			return true;
            		} else {
            			pjQ.$('#voucher_code').trigger('change');
            			return false;
            		}            		
            	} else {
            		return true;
            	}
            });
            
            pjQ.$('#trPaymentMethod_' + self.opts.index).trigger('change');
            
            $form.validate({
                rules: {
                    "email2":     {
                        required: true,
                        email: true,
                        equalTo: "#email"
                    }
                },
                ignore: "",
                submitHandler: function (form) {
                    self.disableButtons.call(self);
                    var $msg_container = pjQ.$('#trBookingMsg_' + self.opts.index);
                    $msg_container.find('.alert').html(self.opts.message_0);
                    $msg_container.css('display', 'block');
                    pjQ.$('.pjCrBookingSesstionExpired').hide();
                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionSaveBooking", "&session_id=", self.opts.session_id, "&index=", self.opts.index].join(""), $form.serialize()).done(function (data) {
                        if (!data.code) {
                            return;
                        }
                        switch (parseInt(data.code, 10)) {
                            case 100:
                                $msg_container.find('.alert').html(self.opts.message_8);
                                self.enableButtons.call(self);
                                break;
                            case 101:
                                $msg_container.find('.alert').html(data.text);
                                self.enableButtons.call(self);
                                break;
                            case 102:
                            	self.disableButtons.call(self);
                            	$msg_container.css('display', 'none');
                            	pjQ.$('.pjCrBookingSesstionExpired').show();
                            	pjQ.$('.pjCrRestartBooking').removeAttr("disabled").removeClass("trButtonDisable");
                            	pjQ.$('html, body').animate({
                                    scrollTop: pjQ.$('.pjCrBookingSesstionExpired').offset().top
                                }, 500);
                                break;
                            case 200:
                            	if (data.payment_method == 'saferpay') {
                            		self.loadPayment.call(self, data.booking_uuid);
                            	} else {
                            		self.loadSummary.call(self, data.booking_id);
                            	}
                                break;
                        }
                    });
                    return false;
                }
            });
        },
        loadCheckout: function () {
			var self = this,
				index = this.opts.index,
				params = 	{
								"locale": this.opts.locale,
								"hide": this.opts.hide,
								"index": this.opts.index
							};

            var $container = pjQ.$('#trBookingStep_Checkout_' + self.opts.index);
            $container.nextAll('[id^="trBookingStep_"]').empty();

			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionCheckout", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                $container.html(data);

                $container.find('input[type=radio], input[type=checkbox],input[type=number], select:not(.select2)').uniform();

				pjQ.$('html, body').animate({
			        scrollTop: $container.offset().top
			    }, 500);
				self.bindCheckout.call(self);
                self.enableButtons.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		loadSummary: function (booking_id) {
			var self = this,
				index = this.opts.index,
				params = 	{
								"locale": this.opts.locale,
								"hide": this.opts.hide,
								"index": this.opts.index,
                                "booking_id": booking_id
							};
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionSummary", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
				self.$container.replaceWith(data);
                pjQ.$('html, body').animate({
                    scrollTop: pjQ.$('.pjSbSummaryWrap').offset().top
                }, 500);
                
                var $msg_container = pjQ.$('#trBookingMsg_' + index);
                if ($msg_container.find("form[name='trPaypal']").length > 0) {
                	setTimeout(function() {
                		$msg_container.find("form[name='trPaypal']").trigger('submit');
	        		}, 3000);
                } else if($msg_container.find("form[name='trAuthorize']").length > 0) {
                	setTimeout(function() {
                		$msg_container.find("form[name='trAuthorize']").trigger('submit');
	        		}, 3000);                	
                } else if($msg_container.find("iframe[name='trSaferpay']").length > 0) {
                	setTimeout(function() {
    		            pjQ.$(window).bind("message", function (e) {
    		            	 pjQ.$("#trSaferpay_" + self.opts.index).css("height", e.originalEvent.data.height + "px");
    		            });
    		            pjQ.$('html, body').animate({
    	                    scrollTop: pjQ.$('#trSaferpayForm_' + self.opts.index).offset().top
    	                }, 500);
	        		}, 3000);
                }
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		loadPayment: function (booking_uuid) {
            var self = this,
                index = this.opts.index,
                params = 	{
                    "locale": this.opts.locale,
                    "hide": this.opts.hide,
                    "index": this.opts.index,
                    "booking_uuid": booking_uuid
                };
            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionPayment", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
            	self.$container.html(data);
            	if (pjQ.$('.pjSbPaymentWrap').length > 0) {
	                pjQ.$('html, body').animate({
	                	scrollTop: pjQ.$('.pjSbPaymentWrap').offset().top
	                }, 500);
            	}
                var $form = pjQ.$('#trPaymentForm_' + self.opts.index);
                $form.find('input[type=radio]').uniform();
                
                pjQ.$(window).bind("message", function (e) {
                	if (e.originalEvent.data.height <= 450) return;
                	pjQ.$("#trSaferpay_" + index).css("height", e.originalEvent.data.height + "px");
                });
                self.enableButtons.call(self);
            }).fail(function () {
                self.enableButtons.call(self);
            });
        },
		getLocations: function(){
			var self = this,
				index = this.opts.index,
				params = 	{
								"locale": this.opts.locale,
								"hide": this.opts.hide,
								"index": this.opts.index,
								"location_id" : pjQ.$("#trLocationId_"+ index).val(),
								"custom_pickup_id" : pjQ.$("#custom_pickup_id_"+ index).val(),
								"date" : pjQ.$("#trDate_"+ index).val()
							};
			self.disableButtons();
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionGetLocations", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
				pjQ.$('#dropoffBox_' + index).html(data);
				
				var $dropoffSelect2 = pjQ.$('#trDropoffId_' + self.opts.index);				
				var $defaultResults = pjQ.$('option[value]', $dropoffSelect2);
	            var defaultResults = [];
	            $defaultResults.each(function() {
	            	var $option = pjQ.$(this);
	            	defaultResults.push({
	            		id: $option.attr('value'),
	            		icon: $option.attr('data-icon'),
	            		text: $option.text()
	            	});
	            });

	            $dropoffSelect2.select2({
	            	minimumInputLength: 3,
	            	ajax: {
	            		delay: 250,
	            		url: self.opts.folder + 'index.php?controller=pjFront&action=pjActionSearchLocations&dropoff=1',
	            		cache: true,
	            		delay: 0,
	            		templateResult: function(state) {
	            			return pjQ.$('<span><i class="' + pjQ.$(state.element).data('icon') + '"></i><span>' + state.text + '</span></span>');
	            		}
	            	},
	            	dataAdapter: pjQ.$.fn.select2.amd.require('select2/data/extended-ajax'),
	            	defaultResults: defaultResults,
	            	templateResult: formatRepo,
	            	"language": {
	            		"searching": function() {
	                        return self.opts.i18n.searching;
	                    },
	                    "errorLoading": function () {
	                    	return self.opts.i18n.searching;
	                    },
	                    "noResults": function(){
	                        return self.opts.i18n.locations_empty;
	                    }
	                }
	            });

	            function formatRepo(repo) {
	            	if (repo.icon !== undefined) {
	            		return pjQ.$('<span><i class="' + repo.icon + '"></i><span>' + repo.text + '</span></span>');
	            	} else {
	            		return repo.text;
	            	}
	            }
	            				
	            if (self.switchDropoffLocation != '') {
					pjQ.$('#trDropoffId_' + self.opts.index).val(self.switchDropoffLocation).trigger('change');
					self.switchDropoffLocation = '';
				}
                self.enableButtons.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		getExtras: function(){
			var self = this,
				index = this.opts.index,
				params = 	{
								"locale": this.opts.locale,
								"hide": this.opts.hide,
								"index": this.opts.index
							};
			self.disableButtons();
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionGetExtras", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
				pjQ.$('#pjSbExtras_' + index).html(data);
                self.enableButtons.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		getPaymentForm: function(obj){
			var self = this,
				index = this.opts.index;
			var qs = {
					"cid": this.opts.cid,
					"locale": this.opts.locale,
					"hide": this.opts.hide,
					"index": this.opts.index,
					"booking_id": obj.booking_id, 
					"payment_method": obj.payment
				};
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionGetPaymentForm", "&session_id=", self.opts.session_id].join(""), qs).done(function (data) {
				var $msg_container = pjQ.$('#trBookingMsg_' + index);
				$msg_container.html(data);
				$msg_container.parent().css('display', 'block');
				switch (obj.payment) {
					case 'paypal':
						self.$container.find("form[name='trPaypal']").trigger('submit');
						break;
					case 'authorize':
						self.$container.find("form[name='trAuthorize']").trigger('submit');
						break;
					case 'saferpay':
			            pjQ.$(window).bind("message", function (e) {
			            	 pjQ.$("#iframeSaferpay_" + self.opts.index).css("height", e.originalEvent.data.height + "px");
			            });
						break;
					case 'creditcard':
					case 'bank':
					case 'cash':
						break;
				}
			}).fail(function () {
				log("Deferred is rejected");
			});
		}
	};
	
	window.TransferRes = TransferRes;	
})(window);