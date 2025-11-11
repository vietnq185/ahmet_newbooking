var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	"use strict";
	$(function () {
		var $frmCreateLocation = $("#frmCreateLocation"),
			$frmUpdateLocation = $("#frmUpdateLocation"),
			$frmUpdatePrice = $('#frmUpdatePrice'),
			$dialogCopy = $("#dialogCopy"),
			$dialogPrompt = $("#dialogPrompt"),
			$dialogLocationsStatus = $('#dialogLocationsStatus'),
			$dialogPricesStatus = $('#dialogPricesStatus'),
			dialog = ($.fn.dialog !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			chosen = ($.fn.chosen !== undefined),
			remove_arr = new Array(),
			spinner = ($.fn.spinner !== undefined),
			miniColors = ($.fn.miniColors !== undefined);
		
		if (miniColors) {
			$(".colorSelector").miniColors();
		}
		
		function setLocations()
		{
			var index_arr = new Array();
				
			$('#tr_dropoff_table').find(".tr-location-row").each(function (index, row) {
				index_arr.push($(row).attr('data-index'));
			});
			$('#index_arr').val(index_arr.join("|"));
		}
		
		function initializePriceGrid()
		{
			if($(".pj-location-grid").length > 0)
			{
				var head_height = $('.content-head-row').height();
				$('.content-head-row').height(head_height + 20);
				$('.title-head-row').height(head_height + 20);
				
				$('.title-row').each(function(index) {
				    var id = $(this).attr('lang');
				    var h = $('.content_row_' + id).height();
				    if(h < 56){
				    	h = 56;
				    }
				    $(this).height(h);
				    $('.content_row_' + id).height(h);
				});
				$(".wrapper1").scroll(function(){
			        $(".wrapper2")
			            .scrollLeft($(".wrapper1").scrollLeft());
			    });
			    $(".wrapper2").scroll(function(){
			        $(".wrapper1")
			            .scrollLeft($(".wrapper2").scrollLeft());
			    });
			    
			    $(".wrapper2").height($("#compare_table").height() + 24);
			}
		}
		
		if ($frmUpdatePrice.length > 0) {
			$frmUpdatePrice.validate({
				errorLabelContainer: $("#priceErrorMessage"),
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				submitHandler: function(form){
					var post = null;
					var post_arr = new Array(); 
					var fields = 0;
					var max_fields_post = 100;
					var loop = 0;
					
					function setBeforeSave(i)
					{
						$.post("index.php?controller=pjAdminLocations&action=pjActionBeforeSavePrice", post_arr[i], callback);
					}
					function callback(data){
						loop++;
						if(loop < post_arr.length)
						{
							setBeforeSave.call(null, [loop]);
						}else{
							$.post("index.php?controller=pjAdminLocations&action=pjActionSavePrice").done(function (data) {
					    	
						    	$dialogPricesStatus
									.find(".bxPriceStatusStart, .bxPriceStatusFail").hide().end()
									.find(".bxPriceStatusEnd").show();
						    	
						    	$dialogPricesStatus.dialog("option", "close", function () {
						    		$(this).dialog("option", "buttons", {});
						    		if(data.code == '200')
						    		{
						    			window.location.href="index.php?controller=pjAdminLocations&action=pjActionPrice&id="+data.id+"&err=" + data.text;
						    		}
						    	});
						    	$dialogPricesStatus.dialog("option", "buttons", {
						    		'Close': function () {
						    			$(this).dialog("close");
						    		}
						    	});
					    	});
						}
					}
					$dialogPricesStatus.dialog("open");
					$frmUpdatePrice.find(":input").each(function(index){
						if(post == null)
						{
							post = $(this).serialize();
						}else{
							post += '&' + $(this).serialize();
						}
						fields++;
						if(fields == max_fields_post)
						{
							post_arr.push(post);
							fields = 0;
							post = null;
						}
					});
					if(post != null)
					{
						post_arr.push(post);
					}
					if(post_arr.length > 0)
					{
						setBeforeSave.call(null, [loop]);
					}
					return false;
				}
			});
			initializePriceGrid();
		}
		
		if ($frmCreateLocation.length > 0 || $frmUpdateLocation.length > 0) {
			var $frm, $frm_str = '';
			if($frmCreateLocation.length > 0)
			{
				$frm = $frmCreateLocation;
				$frm_str = 'frmCreateLocation'; 
			}
			if($frmUpdateLocation.length > 0)
			{
				$frm = $frmUpdateLocation;
				$frm_str = 'frmUpdateLocation';
			}
			$frm.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				submitHandler: function(form){
					setLocations();
					var valid = true;
					
					var post = null;
					var post_arr = new Array(); 
					var fields = 0;
					var max_fields_post = 100;
					var loop = 0;
					$("#"+$frm_str+" .pj-positive-number").each(function() {
						if($(this).val() == '')
						{
							$(this).parent().removeClass('pj-error-field');
						}else{
							if(Number($(this).val()) < 0 || $.isNumeric($(this).val()) == false)
						    {
						    	valid = false;
						    	$(this).parent().addClass('pj-error-field');
						    }else{
						    	$(this).parent().removeClass('pj-error-field');
						    }
						}
					});
					function setBeforeSave(i)
					{
						$.post("index.php?controller=pjAdminLocations&action=pjActionBeforeSave", post_arr[i], callback);
					}
					function callback(data){
						loop++;
						if(loop < post_arr.length)
						{
							setBeforeSave.call(null, [loop]);
						}else{
							$.post("index.php?controller=pjAdminLocations&action=pjActionSave").done(function (data) {
					    	
						    	$dialogLocationsStatus
									.find(".bxLocationStatusStart, .bxLocationStatusFail").hide().end()
									.find(".bxLocationStatusEnd").show();
						    	
						    	$dialogLocationsStatus.dialog("option", "close", function () {
						    		$(this).dialog("option", "buttons", {});
						    		if(data.code == '200')
						    		{
						    			window.location.href="index.php?controller=pjAdminLocations&action=pjActionUpdate&id="+data.id+"&err=" + data.text;
						    		}
						    	});
						    	$dialogLocationsStatus.dialog("option", "buttons", {
						    		'Close': function () {
						    			$(this).dialog("close");
						    		}
						    	});
					    	});
						}
					}
					if(valid == true)
					{
						$dialogLocationsStatus.dialog("open");
						$frm.find(":input").each(function(index){
							if(post == null)
							{
								post = $(this).serialize();
							}else{
								post += '&' + $(this).serialize();
							}
							fields++;
							if(fields == max_fields_post)
							{
								post_arr.push(post);
								fields = 0;
								post = null;
							}
						});
						if(post != null)
						{
							post_arr.push(post);
						}
						if(post_arr.length > 0)
						{
							setBeforeSave.call(null, [loop]);
						}
					}else{
						
					}
				}
			});
			
			if (chosen) {
	            $(".select-areas").chosen();
	        }
		}
		
		$(".field-int").spinner({
			min: 0,
			create: function( e, ui ) {
				if ($(e.target).prop('disabled')) {
					$(e.target).spinner('option', 'disabled', true);
				}
			}
		});
		if ($dialogCopy.length > 0 && dialog) 
		{
			$dialogCopy.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				width: 400,
				buttons: (function () {
					var buttons = {};
					buttons[trApp.locale.button.copy] = function () {
						if($('#location_id').val() != '')
						{
							var $type = $dialogCopy.data('type');
							$.ajax({
								type: "GET",
								dataType: "html",
								url: "index.php?controller=pjAdminLocations&action=pjActionCopy&id=" + $('#location_id').val() + '&type=' + $type,
								success: function (resp) {
									if ($frmUpdateLocation.length > 0) 
									{
										/*$('#tr_dropoff_container').find(".tr-location-row").each(function (index, element) {
											var id = $(element).attr('data-index');
											if(id.indexOf("tr") == -1)
											{
												remove_arr.push(id);
											}
											$('#remove_arr').val(remove_arr.join("|"));
										});*/
									}
									//$('#tr_dropoff_container').html(resp);
									$('#tr_dropoff_table').append(resp);
									$(".field-int").spinner({
										min: 0,
										create: function( e, ui ) {
											if ($(e.target).prop('disabled')) {
												$(e.target).spinner('option', 'disabled', true);
											}
										}
									});
									if (chosen) {
							            $(".select-areas").chosen();
							        }
									$dialogCopy.dialog("close");
								}
							});
						}
					};
					buttons[trApp.locale.button.cancel] = function () {
						$dialogCopy.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		
		if ($dialogLocationsStatus.length > 0 && dialog) {
			$dialogLocationsStatus.dialog({
				autoOpen: false,
				modal: true,
				resizable: false,
				draggable: false,
				open: function () {
					$dialogLocationsStatus
						.find(".bxLocationStatusFail, .bxLocationStatusEnd").hide().end()
						.find(".bxLocationStatusStart").show();
				},
				close: function () {
					$(this).dialog("option", "buttons", {});
					window.location.reload();
				},
				buttons: {}
			});
		}
		if ($dialogPricesStatus.length > 0 && dialog) {
			$dialogPricesStatus.dialog({
				autoOpen: false,
				modal: true,
				resizable: false,
				draggable: false,
				open: function () {
					$dialogPricesStatus
						.find(".bxPriceStatusFail, .bxPriceStatusEnd").hide().end()
						.find(".bxPriceStatusStart").show();
				},
				close: function () {
					$(this).dialog("option", "buttons", {});
					window.location.reload();
				},
				buttons: {}
			});
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminLocations&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminLocations&action=pjActionDeleteLocation&id={:id}"}],
				columns: [{text: myLabel.pickup_location, type: "text", sortable: true, editable: false, width: 350},
				          {text: myLabel.pickup_address, type: "text", sortable: true, editable: false, width: 400},
				          {text: myLabel.pickup_area, type: "text", sortable: true, editable: false, width: 350}],
				dataUrl: "index.php?controller=pjAdminLocations&action=pjActionGetLocation",
				dataType: "json",
				fields: ['pickup_location', 'address', 'pickup_area'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminLocations&action=pjActionDeleteLocationBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminLocations&action=pjActionExportLocation", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminLocations&action=pjActionSaveLocation&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminLocations&action=pjActionGetLocation", "pickup_location", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminLocations&action=pjActionGetLocation", "pickup_location", "ASC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminLocations&action=pjActionGetLocation", "pickup_location", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", '.pj-add-dropoff', function(e){
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var clone_text = $('#tr_dropoff_table_clone').html(),
				index = Math.ceil(Math.random() * 999999);
			clone_text = clone_text.replace(/\{INDEX\}/g, 'tr_' + index);
			clone_text = clone_text.replace(/\{SPINNER\}/g, 'field-int');
			$('#tr_dropoff_table').append(clone_text);
			$(".field-int").spinner({
				min: 0,
				create: function( e, ui ) {
					if ($(e.target).prop('disabled')) {
						$(e.target).spinner('option', 'disabled', true);
					}
				}
			});
			if (chosen) {
	            $("#area_id_tr_" + index).chosen();
	        }
		}).on("click", '.pj-remove-dropoff', function(e){
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tr = $(this).parent().parent().parent().parent(),
				id = $tr.attr('data-index'),
				cnt = parseInt($(this).attr('data-cnt'), 10);
			if(cnt > 0)
			{
				if ($dialogPrompt.length > 0 && dialog) 
				{
					$dialogPrompt.dialog({
						modal: true,
						autoOpen: false,
						resizable: false,
						draggable: false,
						width: 400,
						buttons: (function () {
							var buttons = {};
							buttons[trApp.locale.button.yes] = function () {
								if(id.indexOf("tr") == -1)
								{
									remove_arr.push(id);
								}
								$('#remove_arr').val(remove_arr.join("|"));
								$tr.remove();
								$dialogPrompt.dialog("close");
							};
							buttons[trApp.locale.button.no] = function () {
								$dialogPrompt.dialog("close");
							};
							
							return buttons;
						})()
					});
					$dialogPrompt.dialog("open");
				}
			}else{
				if(id.indexOf("tr") == -1)
				{
					remove_arr.push(id);
				}
				$('#remove_arr').val(remove_arr.join("|"));
				$tr.remove();
			}
		}).on("click", '.tr_copy_location', function(e){
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogCopy.data('type', $(this).attr('data-type')).dialog('open');
			return false;
		}).on("keyup", '.pj-positive-number', function(e){
			if($(this).val() == '')
			{
				$(this).parent().removeClass('pj-error-field');
			}else{
				if(Number($(this).val()) < 0 || $.isNumeric($(this).val()) == false)
			    {
			    	$(this).parent().addClass('pj-error-field');
			    }else{
			    	$(this).parent().removeClass('pj-error-field');
			    }
			}
			
		}).on('change', '[name="is_airport"]', function (e) {
			$('[data-order-index]').hide();
			$('[data-order-index]').find('input').val('');
			if ($(this).val() == 1) {
				$('[data-order-index]').show();
			}
		}).on('change', '[name^="airport"]', function (e) {
			$(this).closest('tr').find('[name^="order_index"]').spinner({
				min: 0,
				disabled: true
			});

			if ($(this).val() == 1) {
				$(this).closest('tr').find('[name^="order_index"]').spinner({
					min: 0,
					disabled: false
				});
			}
		});
		
	});
})(jQuery_1_8_2);