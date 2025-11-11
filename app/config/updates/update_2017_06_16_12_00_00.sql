
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "payment_methods_ARRAY_cash");
UPDATE `multi_lang` SET `content` = 'Cash on the day of Transfer' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "payment_methods_ARRAY_creditcard");
UPDATE `multi_lang` SET `content` = 'Credit Card in Advance' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoListingBookingsBody");
UPDATE `multi_lang` SET `content` = 'Bookings' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoListingBookingsTitle");
UPDATE `multi_lang` SET `content` = 'Bookings' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "menuReservations");
UPDATE `multi_lang` SET `content` = 'Bookings' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "menuReservation");
UPDATE `multi_lang` SET `content` = 'Bookings' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoReservationListTitle");
UPDATE `multi_lang` SET `content` = 'List of bookings' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoReservationListDesc");
UPDATE `multi_lang` SET `content` = 'Below is a list of all customer bookings made via the system. Here you can filter bookings by status, re-order the list, search by multiple criteria, export and print the list. To see booking and client details, click the pencil icon corresponding to the row. To add a new one, go to the above tab “Add new”.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblNewReservationsToday");
UPDATE `multi_lang` SET `content` = 'new bookings today' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblLatestReservations");
UPDATE `multi_lang` SET `content` = 'latest bookings' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblReservationsToday");
UPDATE `multi_lang` SET `content` = 'bookings today' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoGeneralDesc");
UPDATE `multi_lang` SET `content` = 'The general report summarizes your data using criteria such as pick-up location and vehicles. The report shows you the total number of bookings, passengers served, luggage carried, traveling distance, amount collected via the system, as well as create comparison between the one-way and the round-trip bookings during the selected period of time.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoPickupLocationDesc");
UPDATE `multi_lang` SET `content` = 'In this form, you can select a specific location to generate the report. You will find in the report all of bookings that will be transferred within the date range for selected pick-up location.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoVehicleDesc");
UPDATE `multi_lang` SET `content` = 'You can select which vehicle to view report. In this report, you will find all of bookings that will be transferred within the date range for selected vehicle.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblReservations");
UPDATE `multi_lang` SET `content` = 'Bookings' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblTotalReservations");
UPDATE `multi_lang` SET `content` = 'Total Bookings' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblConfirmedReservations");
UPDATE `multi_lang` SET `content` = 'Confirmed Bookings' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblCancelledReservations");
UPDATE `multi_lang` SET `content` = 'Cancelled Bookings' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblGeneralReservationsReport");
UPDATE `multi_lang` SET `content` = 'General Bookings Report' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblReservationPrintList");
UPDATE `multi_lang` SET `content` = 'Bookings Print List' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblDeleteDropoffConfirm");
UPDATE `multi_lang` SET `content` = 'Theer are bookings made for this drop-off. Are you sure that you want to delete it?' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblEnquiries");
UPDATE `multi_lang` SET `content` = 'Bookings' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "status_ARRAY_996");
UPDATE `multi_lang` SET `content` = 'No property for the booking found' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "status_ARRAY_997");
UPDATE `multi_lang` SET `content` = 'No booking found' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "status_ARRAY_998");
UPDATE `multi_lang` SET `content` = 'No permisions to edit the booking.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "opt_o_booking_status");
UPDATE `multi_lang` SET `content` = 'New booking status if not paid.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "opt_o_payment_status");
UPDATE `multi_lang` SET `content` = 'New booking status if paid.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "opt_o_email_confirmation");
UPDATE `multi_lang` SET `content` = 'New booking is received' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "opt_o_email_confirmation_text");
UPDATE `multi_lang` SET `content` = 'Select ''Yes'' if you want an auto-responder to be sent to clients after submiting new booking.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "opt_o_admin_email_confirmation_text");
UPDATE `multi_lang` SET `content` = 'Select ''Yes'' if you want the system to send you email every time a new booking is submitted.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "opt_o_admin_sms_confirmation_message");
UPDATE `multi_lang` SET `content` = 'SMS notification upon new booking' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoReservationTitle");
UPDATE `multi_lang` SET `content` = 'Booking options' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoReservationDesc");
UPDATE `multi_lang` SET `content` = 'Customise your own transfer booking system by using the options below. You can enable/disable payments, specify a percentage of security deposit and taxes, and more.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "error_titles_ARRAY_AO02");
UPDATE `multi_lang` SET `content` = 'Booking options have been changed.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoAddBookingTitle");
UPDATE `multi_lang` SET `content` = 'Add booking' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoAddBookingDesc");
UPDATE `multi_lang` SET `content` = 'Add a new booking to the system below.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblBookingDetails");
UPDATE `multi_lang` SET `content` = 'Booking details' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoUpdateBookingTitle");
UPDATE `multi_lang` SET `content` = 'Booking details' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_booking_details");
UPDATE `multi_lang` SET `content` = 'Booking Details' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_booking_id");
UPDATE `multi_lang` SET `content` = 'Booking ID' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_booking_created");
UPDATE `multi_lang` SET `content` = 'Booking created' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblBookingID");
UPDATE `multi_lang` SET `content` = 'Booking ID' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "btnAddEnquiry");
UPDATE `multi_lang` SET `content` = '+ Add booking' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "opt_o_sms_confirmation_message_text");
UPDATE `multi_lang` SET `content` = 'You can also send personalized SMS notifications via the each booking page. Available Tokens:<br/><br/>{FirstName}<br/>{LastName}<br/>{Date}<br/>{From}<br/>{To}<br/>{Fleet}<br/>{Passengers}<br/>{Luggage}' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmationDesc");
UPDATE `multi_lang` SET `content` = 'There are three type of auto-responders you can send to both the client and the admin. The first one is sent after new booking is submitted via the software. The second one is sent to confirm a successful payment and the third one after service has been canceled. You may  enable or disable all auto-responders separately as well as customize the message using the tokens below.  <label></label><br/><div class="float_left w350">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Phone}<br/>{Notes}<br/>{Country}<br/>{City}<br/>{State}<br/>{Zip}<br/>{Address}<br/>{Company}<br/>{PaymentMethod}<br/>{CCType}<br/>{CCNum}<br/></div><div class="float_left w350">{Date}<br/>{ReturnDate}<br/>{From}<br/>{To}<br/>{Fleet}<br/>{Passengers}<br/>{Luggage}<br/>{UniqueID}<br/>{SubTotal}<br/>{Tax}<br/>{Total}<br/>{Deposit}<br/>{Rest}<br/>{CCExp}<br/>{CCSec}<br/>{CancelURL}</div>' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "infoConfirmation2Desc");
UPDATE `multi_lang` SET `content` = 'There are three type of auto-responders you can send to both the client and the admin. The first one is sent after new booking is submitted via the software. The second one is sent to confirm a successful payment and the third one after service has been canceled. You may enable or disable all auto-responders separately as well as customize the message using the tokens below.  <label></label><br/><div class="float_left w350">{Title}<br/>{FirstName}<br/>{LastName}<br/>{Email}<br/>{Phone}<br/>{Notes}<br/>{Country}<br/>{City}<br/>{State}<br/>{Zip}<br/>{Address}<br/>{Company}<br/>{PaymentMethod}<br/>{CCType}<br/>{CCNum}<br/></div><div class="float_left w350">{Date}<br/>{ReturnDate}<br/>{From}<br/>{To}<br/>{Fleet}<br/>{Passengers}<br/>{Luggage}<br/>{UniqueID}<br/>{SubTotal}<br/>{Tax}<br/>{Total}<br/>{Deposit}<br/>{Rest}<br/>{CCExp}<br/>{CCSec}<br/>{CancelURL}</div>' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

UPDATE `multi_lang` SET `content` = 'New booking has been received' WHERE `model` = "pjOption" AND `field` = "o_admin_email_confirmation_subject";

UPDATE `multi_lang` SET `content` = 'New booking has been received:\r\n\r\nPersonal details:\r\nTitle: {Title}\r\nFirst Name: {FirstName}\r\nLast Name: {LastName}\r\nE-Mail: {Email}\r\nPhone: {Phone}\r\nNotes: {Notes}\r\nCountry: {Country}\r\nCity: {City}\r\nState: {State}\r\nZip: {Zip}\r\nAddress: {Address}\r\nCompany: {Company}\r\n\r\nReservation details:\r\nBooking date: {Date}\r\nFrom: {From}\r\nTo: {To}\r\nFleet: {Fleet}\r\nPassengers: {Passengers}\r\nLuggage: {Luggage}\r\nUnique ID: {UniqueID}\r\nTotal: {Total}\r\n\r\nIf you want to cancel your reservation follow next link: {CancelURL}\r\n\r\nThank you, we will contact you ASAP.' WHERE `model` = "pjOption" AND `field` = "o_admin_email_confirmation_message";

UPDATE `multi_lang` SET `content` = 'Your booking has been received.' WHERE `model` = "pjOption" AND `field` = "o_email_confirmation_subject";

UPDATE `multi_lang` SET `content` = 'Your booking has been received. Details are below:\r\n\r\nPersonal details:\r\nTitle: {Title}\r\nFirst Name: {FirstName}\r\nLast Name: {LastName}\r\nE-Mail: {Email}\r\nPhone: {Phone}\r\nNotes: {Notes}\r\nCountry: {Country}\r\nCity: {City}\r\nState: {State}\r\nZip: {Zip}\r\nAddress: {Address}\r\nCompany: {Company}\r\n\r\nReservation details:\r\nBooking date: {Date}\r\nFrom: {From}\r\nTo: {To}\r\nFleet: {Fleet}\r\nPassengers: {Passengers}\r\nLuggage: {Luggage}\r\nUnique ID: {UniqueID}\r\nTotal: {Total}\r\n\r\nIf you want to cancel your reservation follow next link: {CancelURL}\r\n\r\nThank you, we will contact you ASAP.' WHERE `model` = "pjOption" AND `field` = "o_email_confirmation_message";


SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_from");
UPDATE `multi_lang` SET `content` = 'Pick up location' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_to");
UPDATE `multi_lang` SET `content` = 'Drop off location' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_departure");
UPDATE `multi_lang` SET `content` = 'Departure date' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_search");
UPDATE `multi_lang` SET `content` = 'Find a transfer' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_all_inclusive");
UPDATE `multi_lang` SET `content` = 'total price for all passengers<br/>incl. luggage and skis' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_flight_time");
UPDATE `multi_lang` SET `content` = 'Time of arrival' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblFlightArrivalTime");
UPDATE `multi_lang` SET `content` = 'Time of arrival' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_airline_company");
UPDATE `multi_lang` SET `content` = 'Airline name' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_destination_address");
UPDATE `multi_lang` SET `content` = 'Destination address' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblBookingDestAddress");
UPDATE `multi_lang` SET `content` = 'Destination address' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "opt_o_bf_include_destination_address");
UPDATE `multi_lang` SET `content` = 'Destination address' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_notes");
UPDATE `multi_lang` SET `content` = 'Further information or requests:' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "opt_o_bf_include_notes");
UPDATE `multi_lang` SET `content` = 'Further information or requests:' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblBookingNotes");
UPDATE `multi_lang` SET `content` = 'Further information or requests:' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_address");
UPDATE `multi_lang` SET `content` = 'Pick-up address' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "opt_o_bf_include_address");
UPDATE `multi_lang` SET `content` = 'Pick-up address' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblBookingAddress");
UPDATE `multi_lang` SET `content` = 'Pick-up address' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_email");
UPDATE `multi_lang` SET `content` = 'Email address' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_phone");
UPDATE `multi_lang` SET `content` = 'Mobile phone' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_agree");
UPDATE `multi_lang` SET `content` = 'I''ve read and accepted the General Terms and Conditions' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_cc_num");
UPDATE `multi_lang` SET `content` = 'Credit card number' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_cc_code");
UPDATE `multi_lang` SET `content` = 'Check number' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_cc_exp");
UPDATE `multi_lang` SET `content` = 'Valid until' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_payment_medthod");
UPDATE `multi_lang` SET `content` = 'Payment Method' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;