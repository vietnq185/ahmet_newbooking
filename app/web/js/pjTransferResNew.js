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

    function TransferResNew(opts) {
        if (!(this instanceof TransferResNew)) {
            return new TransferResNew(opts);
        }

        this.reset.call(this);
        this.init.call(this, opts);

        return this;
    }

    TransferResNew.inObject = function (val, obj) {
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

    TransferResNew.size = function(obj) {
        var key,
            size = 0;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) {
                size += 1;
            }
        }
        return size;
    };

    TransferResNew.prototype = {
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
        },
        enableButtons: function () {
            this.$container.find(".btn").removeAttr("disabled").removeClass("trButtonDisable");
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
                    //window.location.reload();
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
            }).on("click.tr", ".trChooseDateButton", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('#trDate_' + self.opts.index).focus();
                return false;
            }).on("click.tr", ".pjSbSwitchOneWay", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('.pjSbSwitch').removeClass('btn-primary');
                pjQ.$(this).addClass('btn-primary');
                pjQ.$('.pjSbAddReturnTransfer').show();
                pjQ.$('.pjSbReturnTransferDateWrap').hide();
                pjQ.$('#is_return_' + self.opts.index).val(0);
                pjQ.$('#trReturnDate_' + self.opts.index).removeClass('required');
            }).on("click.tr", ".pjSbSwitchReturn", function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                }
                pjQ.$('.pjSbSwitch').removeClass('btn-primary');
                pjQ.$(this).addClass('btn-primary');
                pjQ.$('.pjSbAddReturnTransfer').hide();
                pjQ.$('.pjSbReturnTransferDateWrap').show();
                pjQ.$('#is_return_' + self.opts.index).val(1);
                pjQ.$('#trReturnDate_' + self.opts.index).addClass('required');
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
            });

            pjQ.$(document).on("select2:open", function() {
                pjQ.$(".select2-search--dropdown .select2-search__field").attr("placeholder", self.opts.search_placeholder);
            });

            self.loadSearch.call(self);
        },
        loadSearch: function() {
            var self = this,
                index = this.opts.index,
                params = 	{
                    "locale": this.opts.locale,
                    "hide": this.opts.hide,
                    "index": this.opts.index
                };
            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionSearchNew", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
                self.$container.html(data);
                pjQ.$('.modal-dialog').css("z-index", "9999");
                
                if(pjQ.$('#trReturnOn_' + index).is(':checked'))
                {
                    pjQ.$('.trReturnField').prop('disabled', false);
                }else{
                    pjQ.$('.trReturnField').prop('disabled', 'disabled');
                }
                var $dropoff = pjQ.$('#trDropoffId_' + index).val();
				if ($dropoff.length > 0) {
					pjQ.$('#trDropoffId_' + index).trigger('change');
				}
                self.bindSearch.call(self);
                if(parseInt(pjQ.$('#autoloadNextStep_' + index).val(), 10) == 1)
                {
                    pjQ.$('html, body').animate({
                        scrollTop: self.$container.offset().top
                    }, 0);

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
                    pjQ.$.post([self.opts.folder, "index.php?controller=pjFront&action=pjActionCheckNew", "&session_id=", self.opts.session_id].join(""), $form.serialize()).done(function (data) {
                    	switch (parseInt(data.code, 10)) {
	                        case 100:
	                            $form.find('.trCheckErrorMsg').html(self.opts.messages.no_fleet).show().fadeOut(10000);
	                            self.enableButtons.call(self);
	                            break;
	                        case 101:
	                            $form.find('.trCheckErrorMsg').html(self.opts.messages.invalid_date).show().fadeOut(10000);
	                            self.enableButtons.call(self);
	                            break;
	                        case 102:
	                            $form.find('.trCheckErrorMsg').html(data.text).show().fadeOut(10000);
	                            self.enableButtons.call(self);
	                            break;
	                        case 200:
	                            window.location.href = self.opts.siteURL + '?' + pjQ.$.param(data.params);
	                            break;
	                    }
                    }).fail(function () {
                        self.enableButtons.call(self);
                    });
                    return false;
                }
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
            pjQ.$.get([this.opts.folder, "index.php?controller=pjFront&action=pjActionGetLocationsNew", "&session_id=", self.opts.session_id].join(""), params).done(function (data) {
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
        }
    };

    window.TransferResNew = TransferResNew;
})(window);