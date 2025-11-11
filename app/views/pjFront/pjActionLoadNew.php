<?php
mt_srand();
$index = $controller->defaultIndex;
$front_messages = __('front_messages', true, false);

$months = __('months', true);
ksort($months);
$time_ampm = 0;
if(strpos($tpl['option_arr']['o_time_format'], 'a') > -1)
{
	$time_ampm = 1;
}
if(strpos($tpl['option_arr']['o_time_format'], 'A') > -1)
{
	$time_ampm = 2;
}
?>
<div id="pjWrapperShuttleBookingSearch">
	<div id="pjSbContainer_<?php echo $index; ?>" class="container-fluid pjSbContainer"></div>
</div>
<script type="text/javascript">
    var pjQ = pjQ || {},
        TransferResNew_<?php echo $index; ?>;
    (function () {
        "use strict";
        var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor),

            loadCssHack = function(url, callback){
                var link = document.createElement('link');
                link.type = 'text/css';
                link.rel = 'stylesheet';
                link.href = url;

                document.getElementsByTagName('head')[0].appendChild(link);

                var img = document.createElement('img');
                img.onerror = function(){
                    if (callback && typeof callback === "function") {
                        callback();
                    }
                };
                img.src = url;
            },
            loadRemote = function(url, type, callback) {
                if (type === "css" && isSafari) {
                    loadCssHack(url, callback);
                    return;
                }
                var _element, _type, _attr, scr, s, element;

                switch (type) {
                    case 'css':
                        _element = "link";
                        _type = "text/css";
                        _attr = "href";
                        break;
                    case 'js':
                        _element = "script";
                        _type = "text/javascript";
                        _attr = "src";
                        break;
                }

                scr = document.getElementsByTagName(_element);
                s = scr[scr.length - 1];
                element = document.createElement(_element);
                element.type = _type;
                if (type == "css") {
        			element.rel = "stylesheet";
        		} else {
        			element.crossorigin = "anonymous";
        		}
                if (element.readyState) {
                    element.onreadystatechange = function () {
                        if (element.readyState == "loaded" || element.readyState == "complete") {
                            element.onreadystatechange = null;
                            if (callback && typeof callback === "function") {
                                callback();
                            }
                        }
                    };
                } else {
                    element.onload = function () {
                        if (callback && typeof callback === "function") {
                            callback();
                        }
                    };
                }
                element[_attr] = url;
                s.parentNode.insertBefore(element, s.nextSibling);
            },
            loadScript = function (url, callback) {
                loadRemote(url, "js", callback);
            },
            loadCss = function (url, callback) {
                loadRemote(url, "css", callback);
            },
            getSessionId = function () {
                return sessionStorage.getItem("session_id") == null ? "" : sessionStorage.getItem("session_id");
            },
            createSessionId = function () {
                if(getSessionId()=="") {
                    sessionStorage.setItem("session_id", "<?php echo session_id(); ?>");
                }
            },
            options = {
                server: "<?php echo PJ_INSTALL_URL; ?>",
                folder: "<?php echo PJ_INSTALL_URL; ?>",
                index: <?php echo $index; ?>,
                hide: <?php echo isset($_GET['hide']) && (int) $_GET['hide'] === 1 ? 1 : 0; ?>,
                locale: <?php echo isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : $controller->pjActionGetLocale(); ?>,
                search_placeholder: "<?php echo __('front_search_placeholder', true, true) ?>",
                startDay: <?php echo (int) $tpl['option_arr']['o_week_start']; ?>,
                dateFormat: "<?php echo $tpl['option_arr']['o_date_format']; ?>",
                jsDateFormat: "<?php echo pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']); ?>",
                dayNames: ["<?php echo join('","', __('day_names', true)); ?>"],
                monthNamesFull: ["<?php echo join('","', $months); ?>"],
                momentDateFormat: "<?php echo pjUtil::toMomemtJS($tpl['option_arr']['o_date_format']); ?>",
        		time_format: "<?php echo $time_ampm > 0 ? 'LT' : "HH:mm";?>",
                siteURL: "<?php echo $tpl['siteUrl']; ?>",
                messages: {
                    no_fleet: "<?php echo pjSanitize::clean(__('front_no_fleet_found', true, false));?>",
                    generic_error: "Error",
                    invalid_date: "<?php echo pjSanitize::clean(__('front_invalid_date', true, false));?>",
                },
                message_0: "<?php echo pjSanitize::clean($front_messages[0]); ?>",
                message_1: "<?php echo pjSanitize::clean($front_messages[1]); ?>",
                message_2: "<?php echo pjSanitize::clean($front_messages[2]); ?>",
                message_3: "<?php echo pjSanitize::clean($front_messages[3]); ?>",
                message_4: "<?php echo pjSanitize::clean($front_messages[4]); ?>",
                message_5: "<?php echo pjSanitize::clean($front_messages[5]); ?>",

                validation:{
                    required_field: "<?php echo pjSanitize::clean(__('front_required_field', true, false));?>",
                    exp_month: "<?php echo pjSanitize::clean(__('front_exp_month', true, false));?>",
                    exp_year: "<?php echo pjSanitize::clean(__('front_exp_year', true, false));?>",
                    invalid_email: "<?php echo pjSanitize::clean(__('front_invalid_email', true, false));?>",
                    incorrect_captcha: "<?php echo pjSanitize::clean(__('front_incorrect_captcha', true, false));?>"
                },
                i18n: {
        			send_inquiry: "<?php __('lblSendInquiry', false, true); ?>",
        			locations_empty: "<?php __('front_search_locations_empty', false, true); ?>",
        			searching: "<?php __('front_search_locations_searching', false, true); ?>",
        		}
            };
        <?php
        $dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
        $dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
        ?>
        loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('storage_polyfill'); ?>storagePolyfill.min.js", function () {
            if (isSafari) {
                createSessionId();
                options.session_id = getSessionId();
            }else{
                options.session_id = "";
            }
            loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_jquery'); ?>pjQuery.min.js", function () {
                loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_jquery_ui'); ?>js/pjQuery-ui.custom.min.js", function () {
                    loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_validate'); ?>pjQuery.validate.min.js", function () {
                    	loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap'); ?>pjQuery.bootstrap.min.js", function () {
                    		loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap_datetimepicker'); ?>moment-with-locales.min.js", function () {
    							loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap_datetimepicker'); ?>pjQuery.bootstrap-datetimepicker.min.js", function () {
	                                loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_select2'); ?>pjQuery.select2.full.js", function () {
	                                    loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('uniform'); ?>jquery.uniform.min.js", function () {
	                                    	 loadScript("https://kit.fontawesome.com/b28ae88bfd.js", function () {
		                                        loadScript("<?php echo PJ_INSTALL_URL . PJ_JS_PATH; ?>pjTransferResNew.js", function () {
		                                            TransferResNew_<?php echo $index; ?> = new TransferResNew(options);
		                                        });
	                                    	 });
	                                    });
	                                });
    							});
                            });
                        });
                    });
                });
            });
        });
        loadScript("https://maps.googleapis.com/maps/api/js?key=<?php echo $tpl['option_arr']['o_google_api_key'];?>&sensor=false&libraries=places,geometry,drawing", function () {});
    })();
</script>