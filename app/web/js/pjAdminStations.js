var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateStation = $("#frmCreateStation"),
			$frmUpdateStation = $("#frmUpdateStation"),
			datagrid = ($.fn.datagrid !== undefined),
			spinner = ($.fn.spinner !== undefined),
			remove_arr = new Array();

		$("#frmCreateStation .field-int").spinner({
			min: 0
		});
		$("#frmUpdateStation .field-int").spinner({
			min: 0
		});
		
		function setPrices()
		{
			var index_arr = new Array();
				
			$('#pjTbPriceTable').find(".pjTbPriceRow").each(function (index, row) {
				index_arr.push($(row).attr('data-index'));
			});
			$('#index_arr').val(index_arr.join("|"));
		}
		
		if ($frmCreateStation.length > 0) {
			$.validator.addMethod('not_smaller_than', function (value, element, param) {
		        return + $(element).val() > + $(param).val();
		    });
			$frmCreateStation.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				submitHandler: function(form){
					setPrices();
					form.submit();
					return false;
				}
			});
		}
		if ($frmUpdateStation.length > 0) {
			$.validator.addMethod('not_smaller_than', function (value, element, param) {
		        return + $(element).val() > + $(param).val();
		    });
			$frmUpdateStation.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				submitHandler: function(form){
					setPrices();
					form.submit();
				}
			});
		}
		
		function formatStartFee(str, obj){
			return obj.start_fee_formated;
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminStations&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminStations&action=pjActionDelete&id={:id}"}
				          ],
				columns: [{text: myLabel.name, type: "text", sortable: true, editable: true, width: 250, editableWidth: 350},
				          {text: myLabel.address, type: "text", sortable: true, editable: false, width: 350, editableWidth: 350},
				          {text: myLabel.free_start_fee, type: "text", sortable: true, editable: true, width: 400, editableWidth: 150, align: 'right'},
				          {text: myLabel.start_fee, type: "text", sortable: true, editable: true, editableWidth: 150, align: 'right', renderer: formatStartFee}],
				dataUrl: "index.php?controller=pjAdminStations&action=pjActionGet",
				dataType: "json",
				fields: ['name', 'address', 'free_starting_fee_in_km', 'start_fee'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminStations&action=pjActionDeleteBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminStations&action=pjActionExport", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminStations&action=pjActionSave&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("submit", ".frm-filter", function (e) {
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
			$grid.datagrid("load", "index.php?controller=pjAdminStations&action=pjActionGet", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btnAddPrice", function () {
			var $c = $("#pjTbPriceClone tbody").clone(),
				r = $c.html().replace(/\{INDEX\}/g, 'new_' + Math.ceil(Math.random() * 99999));
			
			$(this).closest("form").find("table").find("tbody").append(r);
			$("#frmCreateStation .field-int").spinner({
				min: 0
			});
			$("#frmUpdateStation .field-int").spinner({
				min: 0
			});
		}).on("click", ".lnkRemovePrice", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tr = $(this).closest("tr");
			var id =  $(this).attr("data-index");
			if(id.indexOf("new") == -1)
			{
				remove_arr.push(id);
			}
			$('#remove_arr').val(remove_arr.join("|"));
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
			});			
			return false;
		});
	});
})(jQuery_1_8_2);