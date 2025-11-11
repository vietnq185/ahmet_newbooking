var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmCreateArea = $("#frmCreateArea"),
			$frmUpdateArea = $("#frmUpdateArea"),
			$frmUpdatePrices = $("#frmUpdatePrices"),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			dialog = ($.fn.dialog !== undefined),
			$dialogSetPlaceName = $("#dialogSetPlaceName"),
			overlays = [];
		
		if ($dialogSetPlaceName.length > 0 && dialog) {
			$dialogSetPlaceName.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				width: 600,
				open: function () {
					var $id = $dialogSetPlaceName.data("id");
					$dialogSetPlaceName.find("input[name='coord_id']").val($id);
					
					$.get( "index.php?controller=pjAdminAreas&action=pjActionGetPlaceName", { coord_id: $id}, function(resp) {
						if (resp.status == 'OK') {
							$('#location_icon').val(resp.data.icon);
							$('#is_airport').val(resp.data.is_airport);
							$('#price_level').val(resp.data.price_level);
							if (parseInt(resp.data.is_disabled, 10) == 1) {
								$('#is_disabled').prop('checked', true);
							} else {
								$('#is_disabled').prop('checked', false);
							}							
							$.each(resp.data.i18n, function(locale_id, item) {
								$('#i18n_place_name_' + locale_id).val(item.place_name);
							});
						}
					});
			    },
			    buttons: (function () {
					var buttons = {};
					buttons[myLabel.btnSave] = function () {
						var $id = $(this).data("id"),
							$frm = $(this).find('form');
						$.post("index.php?controller=pjAdminAreas&action=pjActionSetPlaceName", $frm.serialize()).done(function (data) {
							$dialogSetPlaceName.find('.sbs-place-name').val('');
							$('#is_disabled').prop('checked', false);
							$dialogSetPlaceName.dialog("close");
						});
					};
					buttons[myLabel.btnClose] = function () {
						$dialogSetPlaceName.find('.sbs-place-name').val('');
						$('#is_disabled').prop('checked', false);
						$dialogSetPlaceName.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		
		if ($frmCreateArea.length > 0 && validate) {
			$frmCreateArea.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
			$("#frmCreateArea .field-int").spinner({
				min: 0
			});
		}
		if ($frmUpdateArea.length > 0 && validate) {
			$frmUpdateArea.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
			$("#frmUpdateArea .field-int").spinner({
				min: 0
			});
		}
		if ($frmCreateArea.length > 0 || $frmUpdateArea.length > 0) 
		{	
			var myGoogleMaps = null,
				myGoogleMapsMarker = null;
			
			function GoogleMaps() {
				this.map = null;
				this.drawingManager = null;
				this.init();
			}
			GoogleMaps.prototype = {
				init: function () {
					var self = this;
					self.map = new google.maps.Map(document.getElementById("sbs_map_canvas"), {
						zoom: 8,
						center: new google.maps.LatLng(myLabel.default_lat, myLabel.default_lng),
						mapTypeId: google.maps.MapTypeId.ROADMAP
					});
					return self;
				},
				addMarker: function (position) {
					if (myGoogleMapsMarker != null) {
						myGoogleMapsMarker.setMap(null);
					}
					myGoogleMapsMarker = new google.maps.Marker({
						map: this.map,
						position: position,
						icon: "app/web/img/backend/pin.png"
					});
					this.map.setCenter(position);
					return this;
				},
				draw: function () {
					var $el,
						self = this,
						tmp = {cnt: 0, type: ""},
						mapBounds = new google.maps.LatLngBounds();
					$(".coords").each(function (i, el) {
						$el = $(el);
						tmp.cnt += 1;
						switch ($el.data("type")) {
							case 'circle':
								var str = $el.val().replace(/\(|\)|\s+/g, ""),
									arr = str.split("|"),
									center = new google.maps.LatLng(arr[0].split(",")[0], arr[0].split(",")[1]);
	
								var circle = new google.maps.Circle({
									strokeColor: '#008000',
									strokeOpacity: 1,
									strokeWeight: 1,
									fillColor: '#008000',
									fillOpacity: 0.5,
									center: center,								
						            radius: parseFloat(arr[1]),
						            editable: true,
						            center_changed: function ($_el) {
						            	return function () {
						            		self.update.call(self, this, $_el, 'circle');
						            	};
						            }($el),
						            radius_changed: function ($_el) {
						            	return function () {
						            		self.update.call(self, this, $_el, 'circle');
						            	};
						            }($el)
								});
								circle.myObj = {
									"id": $el.data("id")
								};
								circle.setMap(self.map);
								mapBounds.extend(center);
								google.maps.event.addListener(circle, "click", function () {
									self.removeFocus(overlays, this.myObj.id);
									self.setFocus(this, this.myObj.id);
									selectedShape = this.myObj.id;
								});
								overlays.push(circle);
								tmp.type = "circle";
								break;
							case 'polygon':
								var path,
									str = $el.val().replace(/\(|\s+/g, ""),
									arr = str.split("),"),
									paths = [];
								arr[arr.length-1] = arr[arr.length-1].replace(")", "");
								for (var i = 0, len = arr.length; i < len; i++) {
									path = new google.maps.LatLng(arr[i].split(",")[0], arr[i].split(",")[1]);
									paths.push(path);
									mapBounds.extend(path);
								}
								var polygon = new google.maps.Polygon({
									paths: paths,
									strokeColor: '#008000',
									strokeOpacity: 1,
									strokeWeight: 1,
									fillColor: '#008000',
									fillOpacity: 0.5,
						            editable: true
							    });
								polygon.myObj = {
									"id": $el.data("id")
								};
								polygon.setMap(self.map);
									
								google.maps.event.addListener(polygon, "click", function () {
									self.removeFocus(overlays, this.myObj.id);
									self.setFocus(this, this.myObj.id);
									selectedShape = this.myObj.id;
								});
								overlays.push(polygon);
								tmp.type = "plygon";
								break;
							case 'rectangle':
								var bound,
									str = $el.val().replace(/\(|\s+/g, ""),
									arr = str.split("),"), 
									bounds = [];
								for (var i = 0, len = arr.length; i < len; i++) {
									arr[i] = arr[i].replace(/\)/g, "");
									bound = new google.maps.LatLng(arr[i].split(",")[0], arr[i].split(",")[1]);
									bounds.push(bound);
									mapBounds.extend(bound);
								}
								var rectangle = new google.maps.Rectangle({
									strokeColor: '#008000',
						            strokeOpacity: 1,
						            strokeWeight: 1,
						            fillColor: '#008000',
						            fillOpacity: 0.5,
						            bounds: new google.maps.LatLngBounds(bounds[0], bounds[1]),
						            editable: true,
						            bounds_changed: function ($_el) {
						            	return function () {
						            		self.update.call(self, this, $_el, 'rectangle');
						            	};
						            }($el)
								});
								
								rectangle.myObj = {
									"id": $el.data("id")
								};
								rectangle.setMap(self.map);
									
								google.maps.event.addListener(rectangle, "click", function () {
									self.removeFocus(overlays, this.myObj.id);
									self.setFocus(this, this.myObj.id);
									selectedShape = this.myObj.id;
								});
								overlays.push(rectangle);
								tmp.type = "rectangle";
								break;
						}
					});
					
					if (tmp.cnt === 1 && tmp.type === "circle") {
						this.map.setZoom(13);
					} else {
						this.map.fitBounds(mapBounds);
					}
				},
				drawing: function () {
					var self = this;
					this.drawingManager = new google.maps.drawing.DrawingManager({
						drawingMode: google.maps.drawing.OverlayType.POLYGON,
						drawingControl: true,
						drawingControlOptions: {
							position: google.maps.ControlPosition.TOP_CENTER,
							drawingModes: [
					            google.maps.drawing.OverlayType.CIRCLE,
					            google.maps.drawing.OverlayType.POLYGON,
					            google.maps.drawing.OverlayType.RECTANGLE
					        ]
						},
						circleOptions: {
							fillColor: '#008000',
							fillOpacity: 0.5,
						    strokeWeight: 1,
						    strokeColor: '#008000',
						    strokeOpacity: 1,
							editable: true
						},
						polygonOptions: {
							fillColor: '#008000',
							fillOpacity: 0.5,
						    strokeWeight: 1,
						    strokeColor: '#008000',
						    strokeOpacity: 1,
							editable: true
						},
						rectangleOptions: {
							fillColor: '#008000',
							fillOpacity: 0.5,
						    strokeWeight: 1,
						    strokeColor: '#008000',
						    strokeOpacity: 1,
							editable: true
						}
					});
					this.drawingManager.setMap(this.map);
					
					google.maps.event.addListener(this.drawingManager, 'overlaycomplete', function(event) {
						var rand = Math.ceil(Math.random() * 9999999999),
							$frm = $(".frmArea").eq(0);
						switch (event.type) {
							case google.maps.drawing.OverlayType.CIRCLE:
								var input = $("<input>", {
									"type": "hidden",
									"name": "data[circle][new_" + rand + "]",
									"class": "coords",
									"data-type": "circle",
									"data-id": "new_" + rand
								}).appendTo($frm);
								self.update.call(self, event.overlay, input, 'circle');
								break;
							case google.maps.drawing.OverlayType.POLYGON:
								var input = $("<input>", {
									"type": "hidden",
									"name": "data[polygon][new_" + rand + "]",
									"class": "coords",
									"data-type": "polygon",
									"data-id": "new_" + rand
								}).appendTo($frm);
								self.update.call(self, event.overlay, input, 'polygon');
								break;
							case google.maps.drawing.OverlayType.RECTANGLE:
								var input = $("<input>", {
									"type": "hidden",
									"name": "data[rectangle][new_" + rand + "]",
									"class": "coords",
									"data-type": "rectangle",
									"data-id": "new_" + rand
								}).appendTo($frm);
								self.update.call(self, event.overlay, input, 'rectangle');
								break;
						}
						
						event.overlay.myObj = {
							id: "new_" + rand
						};
						
						google.maps.event.addListener(event.overlay, "click", function () {
							self.removeFocus(overlays, this.myObj.id);
							self.setFocus(this, this.myObj.id);
							selectedShape = this.myObj.id;
						});
						
						overlays.push(event.overlay);
					});
				},
				update: function (obj, $el, type) {
					switch (type) {
						case "circle":
							$el.val(obj.getCenter().toString()+"|"+obj.getRadius());
							break;
						case "polygon":
							var str = [],
								paths = obj.getPaths();
							paths.getArray()[0].forEach(function (el, i) {
								str.push(el.toString());
							});
							$el.val(str.join(", "));
							break;
						case "rectangle":
							$el.val(obj.getBounds().toString());
							break;
					}
				},
				deleteShape: function (overlays) {
					if (overlays && overlays.length > 0) {
						for (var i = 0, len = overlays.length; i < len; i++) {
							if (overlays[i].myObj.id == selectedShape) {
								$.post("index.php?controller=pjAdminAreas&action=pjActionDeletePlace", {coord_id: selectedShape}).done(function (data) {
									overlays[i].setMap(null);
									$(".btnDeleteShape").css('display', 'none');
									$(".coords[data-id='" + selectedShape + "']").remove();
								});
								return true;
								break;
							}
						}
					}
					return false;
				},
				clearOverlays: function (overlays) {
					if (overlays && overlays.length > 0) {
						while (overlays[0]) {
							overlays.pop().setMap(null);
						}
					}
				},
				setFocus: function (overlay, ecceptId) {
					overlay.setOptions({
						strokeColor: '#1B7BDC',
						fillColor: '#4295E8'
					});
					if (overlays && overlays.length > 0) {						
						if ($dialogSetPlaceName.length > 0 && dialog) {
							$dialogSetPlaceName.data('id', ecceptId).dialog("open");
						}
					}
					$(".btnDeleteShape").css('display', 'inline-block');
				},
				removeFocus: function (overlays, exceptId) {
					if (overlays && overlays.length > 0) {
						for (var i = 0, len = overlays.length; i < len; i++) {
							if (overlays[i].myObj.id != exceptId) {
								overlays[i].setOptions({
									strokeColor: '#008000',
									fillColor: '#008000'
								});
							}
						}
					}
				}
			};
			if($frmCreateArea.length > 0)
			{
				myGoogleMaps = new GoogleMaps();
				myGoogleMaps.drawing();
				google.maps.event.trigger(myGoogleMaps.map, 'resize');
				
				google.maps.event.addDomListener($(".btnDeleteShape").get(0), "click", function () {
					myGoogleMaps.deleteShape(overlays);
				});
			}
			if($frmUpdateArea.length > 0)
			{
				if (myGoogleMaps == null) {
					myGoogleMaps = new GoogleMaps();
				}
				if ($(".coords").length === 0) {
					myGoogleMaps.map.setCenter(new google.maps.LatLng(myLabel.default_lat, myLabel.default_lng));
				} else {
					myGoogleMaps.draw();
				}
				myGoogleMaps.drawing();
				
				google.maps.event.addDomListener($(".btnDeleteShape").get(0), "click", function () {
					myGoogleMaps.deleteShape(overlays);
				});
			}
		}
		if ($frmUpdatePrices.length > 0 && validate) {
			$frmUpdatePrices.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ""
			});
		}
		var menuOpts = [  
		                  {text: myLabel.working_time, url: "index.php?controller=pjAdminTime&action=pjActionIndex&id={:id}"},
			              {text: myLabel.delivery_fees, url: "index.php?controller=pjAdminAreas&action=pjActionPrice&id={:id}"}
			           ];
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminAreas&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminAreas&action=pjActionDeleteArea&id={:id}"}
				          ],
				columns: [{text: myLabel.area_name, type: "text", sortable: true, editable: true, width: 500, editableWidth: 500},
				          {text: myLabel.places_cities, type: "text", sortable: false, editable: false, width: 600}],
				dataUrl: "index.php?controller=pjAdminAreas&action=pjActionGetArea",
				dataType: "json",
				fields: ['name', 'places'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminAreas&action=pjActionDeleteAreaBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminAreas&action=pjActionSaveArea&id={:id}",
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
			$grid.datagrid("load", "index.php?controller=pjAdminAreas&action=pjActionGetArea", "name", "ASC", content.page, content.rowCount);
			return false;
		});
	});
})(jQuery_1_8_2);