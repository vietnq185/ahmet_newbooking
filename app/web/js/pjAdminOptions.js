var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmOptions = $('#frmOptions'),
			$frmExportEmails = $('#frmExportEmails'),
			datepicker = ($.fn.datepicker !== undefined),
			select2 = ($.fn.select2 !== undefined),
            tabs = ($.fn.tabs !== undefined),
			$tabs = $("#tabs"),
			tOpt = {
				select: function (event, ui) {
					$(":input[name='tab_id']").val(ui.panel.id);
				}
			};
		
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs(tOpt);
		}
		$(".field-int").spinner({
			min: 0
		});

        if (select2) {
            $("#install_theme").select2();
            $("#install_location_id").select2();
            $("#install_dropoff_id").select2();
        }
		
		if ($frmOptions.length > 0) 
		{
			$.validator.addMethod('positiveNumber', function (value) { 
				return Number(value) >= 0;
			}, myLabel.positive_number);
			$.validator.addMethod('greaterThanZero', function (value) { 
				return Number(value) > 0;
			}, myLabel.positive_number);
			
			$frmOptions.validate({
				rules: {
					"value-int-o_deposit_payment": {
						positiveNumber: true,
						required: true
					},
					"value-int-o_tax_payment": {
						positiveNumber: true,
						required: true
					},
					"value-int-o_vehicle_per_page": {
						greaterThanZero: true,
						required: true
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ""
			});
		}
		
		if ($frmExportEmails.length > 0) {
			$frmExportEmails.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		function reDrawCode() 
		{
			var code = $("#hidden_code").text(),
				code2 = $('#hidden_code2').text(),
				locale = $("select[name='install_locale']").find("option:selected").val(),
				locale2 = $("select[name='install_locale2']").find("option:selected").val(),
				hide = $("input[name='install_hide']").is(":checked") ? "&hide=1" : "",
				hide2 = $("input[name='install_hide2']").is(":checked") ? "&hide=1" : "",
                theme = $("select[name='install_theme']").find("option:selected").val(),
                location = $("select[name='install_location_id']").find("option:selected").val(),
                dropoff = $("select[name='install_dropoff_id']").find("option:selected").val(),
                prices_load_default = '';
			locale = locale !== undefined && parseInt(locale.length, 10) > 0 ? "&locale=" + locale : "";
			locale2 = locale2 !== undefined && parseInt(locale2.length, 10) > 0 ? "&locale=" + locale2 : "";
            theme = theme !== undefined && parseInt(theme.length, 10) > 0 ? "&theme=" + theme: "";
            location = location !== undefined && parseInt(location.length, 10) > 0 ? "&location=" + location : "";
            if (dropoff !== undefined && dropoff.length > 0) {
            	dropoff = "&dropoff=" + dropoff;
            } else {
            	dropoff = "";
            }
            if ($('#install_price').is(':checked')) {
            	prices_load_default = "&load_prices=1";
            }
			$("#install_code").text(
                code.replace(/&action=pjActionLoadCss/g, function(match) {
                    return ["&action=pjActionLoadCss", theme].join("");
                }).replace(/&action=pjActionLoadJS/g, function(match) {
                    return ["&action=pjActionLoad", locale, hide, location, dropoff, prices_load_default].join("");
                })
            );
			$("#install_code2").text(
                code2.replace(/&action=pjActionLoadNewJS/g, function(match) {
                    return ["&action=pjActionLoadNew", locale2, hide2].join("");
                })
            );
		}
		
		$("#content").on("focusin", ".textarea_install", function (e) {
			$(this).select();
		}).on("change", "select[name='value-enum-o_send_email']", function (e) {
            $(".boxSmtp").toggle($("option:selected", this).val() == 'mail|smtp::smtp');
		}).on("change", "select[name='value-enum-o_allow_paypal']", function (e) {
            $(".boxPaypal").toggle($("option:selected", this).val() == 'Yes|No::Yes');
		}).on("change", "select[name='value-enum-o_allow_authorize']", function (e) {
            $(".boxAuthorize").toggle($("option:selected", this).val() == 'Yes|No::Yes');
		}).on("change", "select[name='value-enum-o_allow_bank']", function (e) {
            $(".boxBankAccount").toggle($("option:selected", this).val() == 'Yes|No::Yes');
		}).on("change", "select[name='value-enum-o_allow_saferpay']", function (e) {
            $(".boxSaferpay").toggle($("option:selected", this).val() == 'Yes|No::Yes');
		}).on("change", "select[name='value-enum-o_allow_creditcard_later']", function (e) {
            $(".boxCreditcardLater").toggle($("option:selected", this).val() == 'Yes|No::Yes');
		}).on("change", "select[name='value-enum-o_email_confirmation']", function (e) {
            $(".boxClientConfirmation").toggle($("option:selected", this).val() == '0|1::1');
		}).on("change", "select[name='value-enum-o_email_payment']", function (e) {
            $(".boxClientPayment").toggle($("option:selected", this).val() == '0|1::1');
		}).on("change", "select[name='value-enum-o_email_cancel']", function (e) {
            $(".boxClientCancel").toggle($("option:selected", this).val() == '0|1::1');
		}).on("change", "select[name='value-enum-o_email_client_account']", function (e) {
            $(".boxClientAccount").toggle($("option:selected", this).val() == '0|1::1');
		}).on("change", "select[name='value-enum-o_admin_email_confirmation']", function (e) {
            $(".boxAdminConfirmation").toggle($("option:selected", this).val() == '0|1::1');
		}).on("change", "select[name='value-enum-o_admin_email_payment']", function (e) {
            $(".boxAdminPayment").toggle($("option:selected", this).val() == '0|1::1');
		}).on("change", "select[name='value-enum-o_admin_email_cancel']", function (e) {
            $(".boxAdminCancel").toggle($("option:selected", this).val() == '0|1::1');
		}).on("change", "select[name='value-enum-o_admin_email_client_account']", function (e) {
            $(".boxAdminClientAccount").toggle($("option:selected", this).val() == '0|1::1');
		}).on("change", "select[name='value-enum-o_email_reminder']", function (e) {
            $(".boxClientReminder").toggle($("option:selected", this).val() == '0|1::1');
        }).on("change", "select[name='value-enum-o_email_return_reminder']", function (e) {
            $(".boxClientReturnReminder").toggle($("option:selected", this).val() == '0|1::1');
        }).on("change", "select[name='value-enum-o_email_arrival_confirmation']", function (e) {
            $(".boxClientArrivalConfirmation").toggle($("option:selected", this).val() == '0|1::1');
		}).on("change", "select[name='value-enum-o_admin_email_arrival_confirmation']", function (e) {
            $(".boxAdminArrivalConfirmation").toggle($("option:selected", this).val() == '0|1::1');
		}).on("change", "select[name='install_locale'], input[name='install_hide'], select[name='install_locale2'], input[name='install_hide2'], select[name='install_theme'], select[name='install_dropoff_id']", function(e) {
            reDrawCode.call(null);
		}).on("change", "#install_location_id", function (e) {
            $.get("index.php?controller=pjAdminOptions&action=pjActionGetDropoff", {
                install_location_id: $(this).val()
            }).done(function (data) {
                $("#trDropoffContainer").html(data);
                $("#install_dropoff_id").select2();
                reDrawCode.call(null);
            });
        }).on("click", "#install_price", function (e) {
        	 reDrawCode.call(null);
        }).on("focusin", ".datepick", function (e) {
			var minDate, 
				maxDate,
				$this = $(this),
				custom = {},
				o = {
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev"),
					changeMonth: true,
					changeYear: true
				};
			switch ($this.attr("name")) {
				case "date_from":
					if($(".datepick[name='date_to']").val() != '')
					{
						maxDate = $(".datepick[name='date_to']").datepicker({
							firstDay: $this.attr("rel"),
							dateFormat: $this.attr("rev")
						}).datepicker("getDate");
						$(".datepick[name='date_to']").datepicker("destroy").removeAttr("id");
						if (maxDate !== null) {
							custom.maxDate = maxDate;
						}
					}
					break;
				case "date_to":
					if($(".datepick[name='date_from']").val() != '')
					{
						minDate = $(".datepick[name='date_from']").datepicker({
							firstDay: $this.attr("rel"),
							dateFormat: $this.attr("rev")
						}).datepicker("getDate");
						$(".datepick[name='date_from']").datepicker("destroy").removeAttr("id");
						if (minDate !== null) {
							custom.minDate = minDate;
						}
					}
					break;
			}
			$this.datepicker($.extend(o, custom));
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				$dp.trigger("focusin").datepicker("show");
			}
		}).on('click', '[data-add-certain-date]', function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}

			var index = Math.round(Math.random() * 10000);
			var row = $('[data-copy-certain-date]').find('tr')[0].outerHTML;
			row = row.replace(/\{INDEX\}/g, 'new_' + index);
			$('[data-paste-certain-date]')
				.find('tbody')
				.append(row)
				.end()
				.find('[data-datepicker]')
				.addClass('datepick');
		}).on('click', '[data-remove-certain-date]', function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}

			$(this).closest('tr').remove();
		});

        if (window.tinymce !== undefined) {
            tinymce.init({
                document_base_url: myLabel.install_url,
                relative_urls: false,
                remove_script_host: false,
                selector: "textarea.mceEditor",
                theme: "modern",
                width: 750,
                height: 300,
                content_css: "app/web/css/emails.css",
                plugins: [
                    "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                    "searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                    "save table contextmenu directionality emoticons template paste textcolor"
                ],
                toolbar: "insertfile undo redo | styleselect fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons"
            });
        }
	});
})(jQuery_1_8_2);