var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmCreate = $("#frmCreate"),
			$frmUpdate = $("#frmUpdate"),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			spinner = ($.fn.spinner !== undefined),
			dialog = ($.fn.dialog !== undefined);
		
		$(".field-int").spinner({
			min: 0
		});
		
		if ($frmCreate.length > 0 && validate) {
			$frmCreate.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		if ($frmUpdate.length > 0 && validate) {
			$frmUpdate.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		if ($("#grid").length > 0 && datagrid) {
			function formatAvailableFor(str, obj) {
				return obj.available_for_formated;
			}
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminWhatsappMessages&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminWhatsappMessages&action=pjActionDelete&id={:id}"}
				          ],
				columns: [{text: myLabel.subject, type: "text", sortable: true, editable: true, width: 600, editableWidth: 500},
							{text: myLabel.available_for, type: "text", sortable: true, editable: false, width: 200, renderer: formatAvailableFor},
							{text: myLabel.order, type: "text", sortable: true, editable: true, width: 150, editableWidth: 100},
							{text: myLabel.status, type: "select", sortable: true, editable: true, options: [{
					        	  label: myLabel.active, value: "T"
					          }, {
					        	  label: myLabel.inactive, value: "F"
					          }], applyClass: "pj-status"}
						],
				dataUrl: "index.php?controller=pjAdminWhatsappMessages&action=pjActionGet",
				dataType: "json",
				fields: ['subject', 'available_for', 'order', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminWhatsappMessages&action=pjActionDeleteBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminWhatsappMessages&action=pjActionSave&id={:id}",
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
			$grid.datagrid("load", "index.php?controller=pjAdminWhatsappMessages&action=pjActionGet", "subject", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminWhatsappMessages&action=pjActionGet", "subject", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminWhatsappMessages&action=pjActionGet", "subject", "ASC", content.page, content.rowCount);
			return false;
		});
		
	});
})(jQuery_1_8_2);