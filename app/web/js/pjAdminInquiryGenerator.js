var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmInquiryGenerator = $("#frmInquiryGenerator"),
			$frmSendInquiry = $("#frmSendInquiry"),
			validate = ($.fn.validate !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			spinner = ($.fn.spinner !== undefined),
			select2 = ($.fn.select2 !== undefined),
			chosen = ($.fn.chosen !== undefined);
		
		$(".field-int").spinner({
			min: 0,
			spin: function(event, ui) {
		        if (this.name == 'passengers') {
		        	var $has_return = $('#has_return').is(":checked"),
		        		$passengers = parseInt($('#fleet_id').find(':selected').attr('data-passengers'), 10);
		        	if ($has_return) {
		        		var $cnt = ui.value;
		        		if ($cnt > $passengers) {
		        			$cnt = $passengers;
		        		}
		        		$('#passengers_return').val($cnt);
		        	}
		        }
		    }
		});
		if (select2) {
			$("#search_pickup_id").select2();
			$("#search_dropoff_place_id").select2();
			$("#location_id").select2();
			$("#dropoff_id").select2();			
			$("#fleet_id").select2();
		}
		
		if ($frmSendInquiry.length > 0 && validate) {
			$frmSendInquiry.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		if ($frmInquiryGenerator.length > 0) 
		{
			$.validator.addMethod('positiveNumber', function (value) { 
				return Number(value) >= 0;
			}, myLabel.positive_number);
			
			$.validator.addMethod('maximumNumber', function (value, element) { 
				var data = parseInt($(element).attr('data-value'), 10);
				if(Number(value) > data)
				{
					return false;
				}else{
					return true;
				}
			}, myLabel.max_number);
			
			$frmInquiryGenerator.validate({
				rules: {
					passengers: {
						positiveNumber: true,
						maximumNumber: true
					},
					passengers_return: {
						positiveNumber: true,
						maximumNumber: true
					},
					luggage: {
						positiveNumber: true,
						maximumNumber: true
					}
				},
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'booking_date' || element.attr('name') == 'passengers' || element.attr('name') == 'passengers_return')
					{
						error.insertAfter(element.parent().parent());
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ":hidden",
				submitHandler: function (form) {
					$('.bs-loader').show();
					$('.inquiryTemplate').html('');
					$.post('index.php?controller=pjAdminInquiryGenerator&action=pjActionGenerateInquiry', $(form).serialize()).done(function (data) {
						$('.inquiryTemplate').html(data);	
						if ($('.mceEditor').length > 0) {
							myTinyMceDestroy.call(null);
							myTinyMceInit.call(null, 'textarea.mceEditor');
				        }
						$('.bs-loader').hide();
	        		});
					return false;
				}
			});
		}
		
		$(document).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				if(!$dp.is('[disabled=disabled]'))
				{
					$dp.trigger("focusin").datepicker("show");
				}
			}
		}).on("focusin", ".datetimepick", function (e) {
			var minDateTime, maxDateTime,
				$this = $(this),
				custom = {},
				o = {
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev"),
					timeFormat: $this.attr("lang"),
					stepMinute: 5,
                    onSelect: function (dateText, inst) {
                        if($this.attr('name') == 'booking_date' || $this.attr('name') == 'return_date')
                        {
                        	var $form = $this.closest('form');
    						calPrice($form);
                        }
                    }
			    };
			switch ($this.attr("name")) 
			{
				case "booking_date":
					if($(".datetimepick[name='return_date']").val() != '')
					{
						maxDateTime = $(".datetimepick[name='return_date']").datetimepicker({
							firstDay: $this.attr("rel"),
							dateFormat: $this.attr("rev"),
							timeFormat: $this.attr("lang")
						}).datetimepicker("getDate");
						$(".datetimepick[name='return_date']").datepicker("destroy").removeAttr("id");
						if (maxDateTime !== null) {
							custom.maxDateTime = maxDateTime;
						}
					}
					break;
				case "return_date":
					if($(".datetimepick[name='booking_date']").val() != '')
					{
						minDateTime = $(".datetimepick[name='booking_date']").datetimepicker({
							firstDay: $this.attr("rel"),
							dateFormat: $this.attr("rev"),
							timeFormat: $this.attr("lang")
						}).datetimepicker("getDate");
						$(".datetimepick[name='booking_date']").datepicker("destroy").removeAttr("id");
						if (minDateTime !== null) {
							custom.minDateTime = minDateTime;
						}
					}
					break;
			}
			if($('#has_return').length)
			{			
				$(this).datetimepicker($.extend(o, custom));
			}else{
				$(this).datetimepicker(o);
			}
		}).on("change", "#location_id", function (e) {
			var $location_id = $(this).val();
			$('#pickup_id').val('');
	   		 if ($location_id != '') {
	   			 var $location_id_arr = $location_id.split('~::~');
	   			 if ($location_id_arr[0] == 'google') {
	   				$.get(["index.php?controller=pjAdminInquiryGenerator&action=pjActionGetLatLngPickup"].join(""), {
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
    											$('#pickup_id').val(data.pickup_arr[j].location_id);
    											getDropoff($location_id, data.pickup_arr[j].location_id);
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
    											$('#pickup_id').val(data.pickup_arr[j].location_id);
    											getDropoff($location_id, data.pickup_arr[j].location_id);
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
    											$('#pickup_id').val(data.pickup_arr[j].location_id);
    											getDropoff($location_id, data.pickup_arr[j].location_id);
    											return true;
    										}
    										break;
    								}
    							}
    	                    }
    					}
    					getDropoff($location_id);
    				}).fail(function () {
    					getDropoff($location_id);
    				});
	   			 } else {
	   				getDropoff($location_id);
	   			 }
	   		 }
			getDropoff($location_id);
		}).on("change", "#dropoff_id", function (e) {
			$('#custom_dropoff_id').val('');
			$('#custom_dropoff_place_id').val('');
            var $form = $(this).closest('form'),
            	$dropoff_id = $(this).val();
            if ($dropoff_id != '') {
            	var $dropoff_id_arr = $dropoff_id.split('~::~');
            	if ($dropoff_id_arr[0] == 'server') {
            		$.get("index.php?controller=pjAdminInquiryGenerator&action=pjActionGetLatLngDropoff", {
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
						$('#dropoff_lat').val($lat);
						$('#dropoff_lng').val($lng);
						
						calPrice($form);
    				}).fail(function () {
    					calPrice($form);
    				});
            	} else {
            		var $location_id = $('#location_id').val(),
	        		 	$pickup_id = parseInt($('#pickup_id').val(), 10);
	        		 if ($location_id != '') {
	        			 var $location_id_arr = $location_id.split('~::~');
	        			 if ($location_id_arr[0] == 'server' || $pickup_id > 0) {
	        				 $.post("index.php?controller=pjAdminInquiryGenerator&action=pjActionGetLocationDropoff", $form.serialize()).done(function (data) {
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
	    											$('#custom_dropoff_id').val(data.dropoff_arr[j].dropoff_id);
	    											$('#custom_dropoff_place_id').val(data.dropoff_arr[j].id);
	    											calPrice($form);
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
	    											$('#custom_dropoff_id').val(data.dropoff_arr[j].dropoff_id);
	    											$('#custom_dropoff_place_id').val(data.dropoff_arr[j].id);
	    											calPrice($form);
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
	    											$('#custom_dropoff_id').val(data.dropoff_arr[j].dropoff_id);
	    											$('#custom_dropoff_place_id').val(data.dropoff_arr[j].id);
	    											calPrice($form);
	    											return true;
	    										}
	    										break;
	    								}
	    							}
	    	                    }        	           
	    	                    calPrice($form);
	    	                }).fail(function () {
	    	                    
	    	                });
	        			 } else {
	        				 calPrice($form);
	        			 }
	        		 }
            	}
            }
            
		}).on("change", "#fleet_id", function (e) {
			var $form = $(this).closest('form');
			calPrice($form);

			var passengers = parseInt($('#fleet_id').find(':selected').attr('data-passengers'), 10),
				luggage = parseInt($('#fleet_id').find(':selected').attr('data-luggage'), 10),
				curr_passengers = parseInt($('#passengers').val(),10),
				curr_passengers_return = parseInt($('#passengers_return').val(),10),
				curr_luggage = parseInt($("#luggage").val(), 10);
			if(passengers > 0)
			{
				$('#tr_max_passengers').html("("+myLabel.maximum+" "+passengers+")");
				$( "#passengers" ).spinner( "option", "max", passengers);
				if(curr_passengers > passengers)
				{
					$( "#passengers" ).val("");
				}
				$( "#passengers" ).attr('data-value', passengers);
				
				$('#tr_max_passengers_return').html("("+myLabel.maximum+" "+passengers+")");
				$( "#passengers_return" ).spinner( "option", "max", passengers);
				if(curr_passengers_return > passengers)
				{
					$( "#passengers_return" ).val("");
				}
				$( "#passengers_return" ).attr('data-value', passengers);
			}
			if(luggage > 0)
			{
				$('#tr_max_luggage').html("("+myLabel.maximum+" "+luggage+")");
				$( "#luggage").spinner( "option", "max", luggage);
				if(curr_luggage > luggage)
				{
					$( "#luggage").val("");
				}
				$( "#luggage" ).attr('data-value', luggage);
			}
		}).on("click", "#has_return", function (e) {
			var $form = $(this).closest('form');
			if($(this).is(':checked'))
			{
				$("#return_date_outer").show();
				$('.trReturnDetails').show();
				
				$('.pjPriceRoundtrip').show();
				$('.pjPriceOneway').hide();
				$('.pjSbReturnExtras').show();
			}else{
				$("input[name='return_date']").val("");
				$("#return_date_outer").hide(); 
				$('.trReturnDetails').hide();
				
				$('.pjPriceRoundtrip').hide();
				$('.pjPriceOneway').show();
				$('.pjSbReturnExtras').hide();
			}

			calPrice($form);
		}).on("change", "#search_pickup_id", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $location_id = $(this).val();
			$.get("index.php?controller=pjAdminInquiryGenerator&action=pjActionGetDropoff", {location_id: $location_id, is_search: 1}).done(function (data) {
                $('.pjFilterDropoffLocations').html(data);
                $("#search_dropoff_place_id").select2();
            });
		});
		
		function getDropoff($location_id, $pickup_id='') {
			$.get("index.php?controller=pjAdminInquiryGenerator&action=pjActionGetDropoff", {
				location_id: $location_id,
				pickup_id: $pickup_id
			}).done(function (data) {
				$("#trDropoffContainer").html(data);
				var $dropoffSelect2 = $('#dropoff_id');
				function formatRepo(repo) {
			    	if (repo.icon !== undefined) {
			    		return $('<span><i class="material-icons">' + repo.icon + '</i><span>' + repo.text + '</span></span>');
			    	} else {
			    		return repo.text;
			    	}
			    }
			    
			    var $defaultResults = $('option[value]', $dropoffSelect2);
	            var defaultResults = [];
	            $defaultResults.each(function() {
	            	var $option = $(this);
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
	            		url: 'index.php?controller=pjAdminInquiryGenerator&action=pjActionSearchLocations&dropoff=1',
	            		cache: true,
	            		delay: 0,
	            		templateResult: function(state) {
	            			return $('<span><i class="' + $(state.element).data('icon') + '"></i><span>' + state.text + '</span></span>');
	            		}
	            	},
	            	dataAdapter: $.fn.select2.amd.require('select2/data/extended-ajax'),
	            	defaultResults: defaultResults,
	            	templateResult: formatRepo,
	            	"language": {
	            		"searching": function() {
	                        return myLabel.searching;
	                    },
	                    "errorLoading": function () {
	                    	return myLabel.searching;
	                    },
	                    "noResults": function(){
	                        return myLabel.locations_empty;
	                    }
	                }
	            });

                var is_airport = parseInt($('#location_id').find(':selected').attr('data-is-airport'), 10);
                $('#departure_info_is_airport_0').hide();
                $('#departure_info_is_airport_1').hide();
                $('#departure_info_is_airport_2').hide();
                
                $('#return_info_is_airport_0').hide();
                $('#return_info_is_airport_1').hide();
                $('#return_info_is_airport_2').hide();
			});
		}
		
		function calPrice($form)
		{
            setTimeout(function() {
            	if($('#dropoff_id').val() != '' && $('#fleet_id').val() != '' && $("input[name='booking_date']").val() != '')
                {
                    $.post("index.php?controller=pjAdminInquiryGenerator&action=pjActionCalPrice", $form.serialize()).done(function (data) {
                        $('#total_extra_price').val(parseFloat(data.total_extra_price).toFixed(2));
                        $('#extra_price_first_transfe').val(parseFloat(data.extra_price_first_transfe).toFixed(2));
                        $('#extra_price_return_transfe').val(parseFloat(data.extra_price_return_transfe).toFixed(2));
                        
                        $('#sub_total').val(parseFloat(data.sub_total).toFixed(2));
                        $('#tax').val(parseFloat(data.tax).toFixed(2));
                        $('#discount').val(parseFloat(data.discount).toFixed(2));
                        $('#credit_card_fee').val(parseFloat(data.credit_card_fee).toFixed(2));
                        $('#total').val(parseFloat(data.total).toFixed(2));
                        $('#deposit').val(parseFloat(data.deposit).toFixed(2));                    
                        $('#price').val(parseFloat(data.price).toFixed(2));
                        $('#price_first_transfer').val(parseFloat(data.price_first_transfer).toFixed(2));
                        $('#price_return_transfer').val(parseFloat(data.price_return_transfer).toFixed(2));
                        $('#price_by_distance').val(data.price_by_distance);
                        $.each(data, function(key, value) {
                        	if ($('input[name="'+key+'"]').length > 0 && $('input[name="'+key+'"]').is(":hidden")) {
                        		$('input[name="'+key+'"]').val(value);
                        	}
                    	});
                        
                        $('#tr_duration').html(data.duration_formated);
            			$('#tr_distance').html(data.distance_formated);
            			$('#tr_duration').parent().css('display', 'block');
            			$('#tr_distance').parent().css('display', 'block');
            			
            			$('#departure_info_is_airport_0').hide();
                        $('#departure_info_is_airport_1').hide();
                        $('#departure_info_is_airport_2').hide();
                        
                        $('#return_info_is_airport_0').hide();
                        $('#return_info_is_airport_1').hide();
                        $('#return_info_is_airport_2').hide();
                        
                        $('.pjHotelName').hide();
            			if (data.pickup_is_airport == 0 && data.dropoff_is_airport == 0) {
            				$('#departure_info_is_airport_2').show();
            				$('#return_info_is_airport_2').show();
            			} else if (data.pickup_is_airport == 1 && data.dropoff_is_airport == 0) {
            				$('#departure_info_is_airport_1').show();
            				$('#return_info_is_airport_1').show();
            				$('.pjHotelName').show();
            			} else {
            				$('#departure_info_is_airport_0').show();
            				$('#return_info_is_airport_0').show();
            				$('.pjHotelName').show();
            			}	
            			
            			if ($form.attr('id') == 'frmCreateBooking') {
            				var $pm = $form.find('select[name="payment_method"]').val();
                			if ($pm == 'creditcard' || $pm == 'bank' || $pm == 'saferpay') {
                				$form.find('input[name="deposit"]').val($form.find('input[name="total"]').val());
                			} else if ($pm == 'cash' || $pm == 'creditcard_later') {
                				$form.find('input[name="deposit"]').val(0);
                			}
            			}
                    });
                }
            }, 1000);
		}
		
		if ($frmInquiryGenerator.length > 0) {
        	$.fn.select2.amd.define('select2/data/extended-ajax', ['./ajax', './tags', '../utils', 'module', 'jquery'], function(AjaxAdapter, Tags, Utils, module, $) {
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
        	
	        var $pickupSelect2 = $('#location_id'),
	        	$dropoffSelect2 = $('#dropoff_id');
		    
		    var $defaultResults = $('option[value]', $pickupSelect2);
		    var defaultResults = [];
		    $defaultResults.each(function() {
		    	var $option = $(this);
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
		    		url: 'index.php?controller=pjAdminInquiryGenerator&action=pjActionSearchLocations',
		    		cache: true,
		    		delay: 0,
		    		templateResult: function(state) {
		    			return $('<span><i class="' + $(state.element).data('icon') + '"></i><span>' + state.text + '</span></span>');
		    		}
		    	},
		    	dataAdapter: $.fn.select2.amd.require('select2/data/extended-ajax'),
		    	defaultResults: defaultResults,
		    	templateResult: formatRepo,
		    	"language": {
		    		"searching": function() {
		                return myLabel.searching;
		            },
		            "errorLoading": function () {
		            	return myLabel.searching;
		            },
		            "noResults": function(){
		                return myLabel.locations_empty;
		            }
		        }
		    });
		
		    function formatRepo(repo) {
		    	if (repo.icon !== undefined) {
		    		return $('<span><i class="material-icons">' + repo.icon + '</i><span>' + repo.text + '</span></span>');
		    	} else {
		    		return repo.text;
		    	}
		    }
		    
		    var $defaultResults = $('option[value]', $dropoffSelect2);
            var defaultResults = [];
            $defaultResults.each(function() {
            	var $option = $(this);
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
            		url: 'index.php?controller=pjAdminInquiryGenerator&action=pjActionSearchLocations&dropoff=1',
            		cache: true,
            		delay: 0,
            		templateResult: function(state) {
            			return $('<span><i class="' + $(state.element).data('icon') + '"></i><span>' + state.text + '</span></span>');
            		}
            	},
            	dataAdapter: $.fn.select2.amd.require('select2/data/extended-ajax'),
            	defaultResults: defaultResults,
            	templateResult: formatRepo,
            	"language": {
            		"searching": function() {
                        return myLabel.searching;
                    },
                    "errorLoading": function () {
                    	return myLabel.searching;
                    },
                    "noResults": function(){
                        return myLabel.locations_empty;
                    }
                }
            });
        }
		
		function myTinyMceDestroy() {
			
			if (window.tinymce === undefined) {
				return;
			}
			
			var iCnt = tinymce.editors.length;
			
			if (!iCnt) {
				return;
			}
			
			for (var i = 0; i < iCnt; i++) {
				tinymce.remove(tinymce.editors[i]);
			}
		}
		
		function myTinyMceInit(pSelector) {
			
			if (window.tinymce === undefined) {
				return;
			}
			
			tinymce.init({
                document_base_url: myLabel.install_url,
                relative_urls: false,
                remove_script_host: false,
                selector: "textarea.mceEditor",
                theme: "modern",
                width: 1000,
                height: 300,
                //content_css: "app/web/css/emails.css",
                plugins: [
                    "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                    "searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                    "save table contextmenu directionality emoticons template paste textcolor"
                ],
                toolbar: "insertfile undo redo | styleselect fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
                setup: function(editor) {
                    editor.on('keydown', function(e) {
                        // Ignore Ctrl+S combination to prevent saving in TinyMCE as there is nothing to save.
                        if(e.ctrlKey && (e.which == 83)) {
                            e.preventDefault();
                            return false;
                        }
                    });
                }
            });
		}
	});
})(jQuery_1_8_2);