var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateDialingCode = $("#frmCreateDialingCode"),
			$frmUpdateDialingCode = $("#frmUpdateDialingCode"),
			chosen = ($.fn.chosen !== undefined),
			datagrid = ($.fn.datagrid !== undefined);

        if (chosen) {
            $("#country_id").chosen();
        }
		if ($frmCreateDialingCode.length > 0) {
            $frmCreateDialingCode.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		if ($frmUpdateDialingCode.length > 0) {
			$frmUpdateDialingCode.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminDialingCodes&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminDialingCodes&action=pjActionDelete&id={:id}"}
				          ],
				columns: [{text: myLabel.country, type: "text", sortable: true, editable: false, width: 900},
				          {text: myLabel.dialing_code, type: "text", sortable: true, editable: true}],
				dataUrl: "index.php?controller=pjAdminDialingCodes&action=pjActionGet",
				dataType: "json",
				fields: ['country', 'code'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminDialingCodes&action=pjActionDeleteBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminDialingCodes&action=pjActionExport", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminDialingCodes&action=pjActionSave&id={:id}",
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
			$grid.datagrid("load", "index.php?controller=pjAdminDialingCodes&action=pjActionGet", "country", "ASC", content.page, content.rowCount);
			return false;
		});
	});
})(jQuery_1_8_2);