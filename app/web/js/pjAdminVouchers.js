var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
	
		var $frmCreateVoucher = $('#frmCreateVoucher'),
			$frmUpdateVoucher = $('#frmUpdateVoucher'),
			$datepick = $(".datepick"),
			datagrid = ($.fn.datagrid !== undefined),
			validate = ($.fn.validate !== undefined),
			dOpts = {};
			
		if ($datepick.length > 0) {
			dOpts = $.extend(dOpts, {
				firstDay: $datepick.attr("rel"),
				dateFormat: $datepick.attr("rev")
			});
		}
		function formatDiscount(str, obj) {
			return obj.discount_f;
		}
		function formatValid(str, obj) {
			return obj.valid_f;
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminVouchers&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminVouchers&action=pjActionDeleteVoucher&id={:id}"}
				          ],
				columns: [{text: myLabel.code, type: "text", sortable: true, editable: true, width: 600},
				          {text: myLabel.discount, type: "text", sortable: true, editable: true, align: "right", renderer: formatDiscount, editableWidth: 120, width: 150},
				          {text: myLabel.valid, type: "text", sortable: false, editable: false, renderer: formatValid, width: 300}
				       ],
				dataUrl: "index.php?controller=pjAdminVouchers&action=pjActionGetVoucher",
				dataType: "json",
				fields: ['code', 'discount', 'valid'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminVouchers&action=pjActionDeleteVoucherBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminVouchers&action=pjActionSaveVoucher&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$("#content").on("focusin", ".datepick", function (e) {
			$(this).datepicker(dOpts);
		}).delegate("#valid", "change", function () {
			switch ($("option:selected", this).val()) {
				case 'fixed':
					$(".vBox").hide();
					$("#vFixed").show();
					break;
				case 'period':
					$(".vBox").hide();
					$("#vPeriod").show();
					break;
				case 'recurring':
					$(".vBox").hide();
					$("#vRecurring").show();
					break;
			}
		});
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				valid: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminVouchers&action=pjActionGetVoucher", "code", "DESC", content.page, content.rowCount);
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
			obj.valid = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminVouchers&action=pjActionGetVoucher", "code", "DESC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminVouchers&action=pjActionGetVoucher", "id", "ASC", content.page, content.rowCount);
			return false;
		});
		
		if ($frmCreateVoucher.length > 0 && validate) {
			$frmCreateVoucher.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				}
			});
		}
		
		if ($frmUpdateVoucher.length > 0 && validate) {
			$frmUpdateVoucher.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				}
			});
		}
	});
})(jQuery_1_8_2);