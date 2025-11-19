<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjBookingModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'bookings';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'uuid', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'client_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'driver_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'fleet_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'accept_shared_trip', 'type' => 'bool', 'default' => '0'),
		array('name' => 'location_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'pickup_type', 'type' => 'enum', 'default' => 'server'),
		array('name' => 'dropoff_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'dropoff_place_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'dropoff_type', 'type' => 'enum', 'default' => 'server'),
		array('name' => 'booking_date', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'return_date', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'return_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'passengers', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'luggage', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'sub_total', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'discount', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'tax', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'total', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'deposit', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'extra_price', 'type' => 'decimal', 'default' => ':NULL'),
	    array('name' => 'credit_card_fee', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'station_fee', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'station_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'voucher_code', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'payment_method', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'txn_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'processed_on', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'ip', 'type' => 'varchar', 'default' => ':NULL'),
		
		array('name' => 'c_title', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_fname', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_lname', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_dialing_code', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_phone', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_email', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_company', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_notes', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'c_address', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_city', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_state', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_zip', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_country', 'type' => 'int', 'default' => ':NULL'),
		
		array('name' => 'c_airline_company', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_departure_airline_company', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_flight_number', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_flight_time', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_departure_flight_number', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_departure_flight_time', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_destination_address', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'c_hotel', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_cruise_ship', 'type' => 'int', 'default' => ':NULL'),

		array('name' => 'cc_owner', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'cc_type', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'cc_num', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'cc_exp', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'cc_code', 'type' => 'varchar', 'default' => ':NULL'),

		array('name' => 'internal_notes', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'customized_name_plate', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'name_sign', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'google_map_link', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'pickup_google_map_link', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'dropoff_google_map_link', 'type' => 'text', 'default' => ':NULL'),
		
		array('name' => 'pickup_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'pickup_address', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'pickup_lat', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'pickup_lng', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'pickup_is_airport', 'type' => 'tinyint', 'default' => '0'),
		
		array('name' => 'dropoff_address', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'dropoff_lat', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'dropoff_lng', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'dropoff_is_airport', 'type' => 'tinyint', 'default' => '0'),
		
		array('name' => 'duration', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'distance', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price_by_distance', 'type' => 'enum', 'default' => 'F'),
	    
	    array('name' => 'saferpay_request_id', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'saferpay_token', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'locale_id', 'type' => 'int', 'default' => '1'),
	    array('name' => 'notes_for_support', 'type' => 'text', 'default' => ':NULL'),
	    array('name' => 'paid_via_payment_link', 'type' => 'tinyint', 'default' => '')
	);
	
	
	public $getMapColumns = array(
	    'uuid' => array('label' => 'Unique ID: changed from ', 'type' => 'text'),
	    'booking_date' => array('label' => 'Pickup date & time: changed from ', 'type' => 'datetime'),
	    'accept_shared_trip' => array('label' => 'Accepts shared trip: changed from ', 'type' => 'bolean'),
	    'return_date' => array('label' => 'Return date & time: changed from ', 'type' => 'datetime'),
	    'has_return' => array('label' => 'Return trip: changed from ', 'type' => 'bolean'),
	    'location_id' => array('label' => 'Pick-up location: changed from ', 'value' => 'location', 'type' => 'text'),
	    'dropoff_id' => array('label' => 'Drop-off location: changed from ', 'value' => 'dropoff', 'type' => 'text'),
	    'fleet_id' => array('label' => 'Vehicle: changed from ', 'value' => 'fleet', 'type' => 'text'),
	    'passengers' => array('label' => 'Passengers: changed from ', 'type' => 'int'),
	    'passengers_return' => array('label' => 'Passengers return: changed from ', 'type' => 'int'),
	    'voucher_code' => array('label' => 'Discount Code: changed from ', 'type' => 'text'),
	    'sub_total' => array('label' => 'Sub-total: changed from ', 'type' => 'currency'),
	    'discount' => array('label' => 'Discount: changed from ', 'type' => 'currency'),
	    'credit_card_fee' => array('label' => 'Credit card fee: changed from ', 'type' => 'currency'),
	    'total' => array('label' => 'Total: changed from ', 'type' => 'currency'),
	    'deposit' => array('label' => 'Deposit: changed from ', 'type' => 'currency'),
	    'payment_method' => array('label' => 'Payment method: changed from ', 'type' => 'bolean'),
	    'status' => array('label' => 'Status: changed from ', 'type' => 'bolean'),
	    'status_return_trip' => array('label' => 'Status return trip: changed from ', 'type' => 'bolean'),
	    'notes_for_support' => array('label' => 'Note for support team: changed from ', 'type' => 'text'),
	    'c_title' => array('label' => 'Title: changed from ', 'type' => 'bolean'),
	    'c_fname' => array('label' => 'First name: changed from ', 'type' => 'text'),
	    'c_lname' => array('label' => 'Last name: changed from ', 'type' => 'text'),
	    'c_email' => array('label' => 'Email: changed from ', 'type' => 'text'),
	    'c_country' => array('label' => 'Country: changed from ', 'type' => 'text'),
	    'c_phone' => array('label' => 'Phone: changed from ', 'type' => 'text'),
	    'customized_name_plate' => array('label' => 'Customized name plate: changed from ', 'type' => 'text'),
	    'name_sign' => array('label' => 'Upload name sign', 'type' => 'image'),
	    'c_flight_number' => array('label' => 'Arrival flight number: changed from ', 'type' => 'text'),
	    'c_airline_company' => array('label' => 'Airline company: changed from ', 'type' => 'text'),
	    'c_destination_address' => array('label' => 'Destination address: changed from ', 'type' => 'text'),
	    'c_hotel' => array('label' => 'Name of hotel/pension: changed from ', 'type' => 'text'),
	    'google_map_link' => array('label' => 'Google maps link: changed from ', 'type' => 'text'),
	    'pickup_google_map_link' => array('label' => 'Pick-up Google maps link: changed from ', 'type' => 'text'),
	    'dropoff_google_map_link' => array('label' => 'Drop-off Google maps link: changed from ', 'type' => 'text'),
	    'c_notes' => array('label' => 'Further information or requests: changed from ', 'type' => 'text'),
	    'internal_notes' => array('label' => 'Internal notes: changed from ', 'type' => 'text'),
	    'c_departure_flight_time' => array('label' => 'Flight departure time: changed from ', 'type' => 'text'),
	    'c_address' => array('label' => 'Pick-up address: changed from ', 'type' => 'text'),
	);
	
	public $returnGetMapColumns = array(
	    'booking_date',
	    'c_notes',
	    'status',
	    'passengers',
	    'google_map_link',
	    'pickup_google_map_link',
	    'dropoff_google_map_link',
	    'internal_notes',
	    'c_address',
	    'c_destination_address',
	    'c_departure_flight_time',
	    'c_address',
	    'c_flight_time',
	    'c_flight_number',
	    'c_airline_company'
	);
	
	public static function factory($attr=array())
	{
		return new pjBookingModel($attr);
	}
}
?>