var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmLoginAdmin = $("#frmLoginAdmin"),
			$frmForgotAdmin = $("#frmForgotAdmin"),
			$frmUpdateProfile = $("#frmUpdateProfile"),
			validate = ($.fn.validate !== undefined);
		
		if ($frmLoginAdmin.length > 0 && validate) {
			$frmLoginAdmin.validate({
				rules: {
					login_email: {
						required: true,
						email: true
					},
					login_password: "required"
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		if ($frmForgotAdmin.length > 0 && validate) {
			$frmForgotAdmin.validate({
				rules: {
					forgot_email: {
						required: true,
						email: true
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		if ($frmUpdateProfile.length > 0 && validate) {
			$frmUpdateProfile.validate({
				rules: {
					"email": {
						required: true,
						email: true
					},
					"password": "required",
					"name": "required"
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}

        $(document).on("click", ".abCalendarLinkMonth", function (e) {
            if (e && e.preventDefault) {
                e.preventDefault();
            }
            var $this = $(this);
            $.get("index.php?controller=pjAdmin&action=pjActionGetCal", {
                "cid": $this.data("cid"),
                "year": $this.data("year"),
                "month": $this.data("month")
            }).done(function (data) {
                $("#abCalendar_" + $this.data("cid")).html(data);
            });
            return false;
        }).on("click", ".abCalendarReserved", function (e) {
        	if (e && e.preventDefault) {
                e.preventDefault();
            }
            window.location.href = 'index.php?controller=pjAdminBookings&action=pjActionIndex&date=' + $(this).data("date") + '&status=confirmed';
        	//getTotalAmounts($(this).attr("data-date"));
        })/*.on("click", ".abCalendarCell", function (e) {
            if (e && e.preventDefault) {
                e.preventDefault();
            }
            getTotalAmounts($(this).attr("data-date"));
            return false;
        })*/;
        
        $(document).ready(function() {
        	getTotalAmounts();
        });
        
        function getTotalAmounts($date='') {
        	$.get("index.php?controller=pjAdmin&action=pjActionGetTotalAmounts", {
				date: $date
			}).done(function (data) {
				$(".pjDashboardTotalAmounts").html(data);
			});
        }
	});
})(jQuery_1_8_2);