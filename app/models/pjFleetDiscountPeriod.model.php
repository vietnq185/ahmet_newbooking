<?php
    if (!defined("ROOT_PATH")) {
        header("HTTP/1.1 403 Forbidden");
        exit;
    }

    /**
     * Class pjFleetDiscountPeriodModel
     */
    class pjFleetDiscountPeriodModel extends pjAppModel
    {
        /**
         * Primary key
         *
         * @var string
         */
        protected $primaryKey = 'id';

        /**
         * Table
         *
         * @var string
         */
        protected $table = 'fleets_discounts_periods';

        /**
         * Schema
         *
         * @var array
         */
        protected $schema = array(
            array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
            array('name' => 'fleet_discount_id', 'type' => 'int', 'default' => ':NULL'),
            array('name' => 'date_from', 'type' => 'date', 'default' => ':NULL'),
            array('name' => 'date_to', 'type' => 'date', 'default' => ':NULL'),
            array('name' => 'is_subtract', 'type' => 'enum', 'default' => ':NULL'),
            array('name' => 'discount', 'type' => 'decimal', 'default' => ':NULL'),
            array('name' => 'type', 'type' => 'enum', 'default' => ':NULL')
        );

        /**
         * Default validation
         * 
         * @var array
         */
        protected $validate = array(
            'rules' => array(
                'fleet_discount_id' => array(
                    'pjActionRequired' => true,
                    'pjActionNotEmpty' => true,
                    'pjActionNumeric' => true
                ),
                'date_from' => array(
                    'pjActionRequired' => true,
                    'pjActionNotEmpty' => true,
                    'pjActionDate' => true
                ),
                'date_to' => array(
                    'pjActionRequired' => true,
                    'pjActionNotEmpty' => true,
                    'pjActionDate' => true
                ),
                'is_subtract' => array(
                    'pjActionRequired' => true,
                    'pjActionNotEmpty' => true,
                    array('pjActionInList', array('T', 'F'))
                ),
                'discount' => array(
                    'pjActionRequired' => true,
                    'pjActionNotEmpty' => true,
                    'pjActionNumeric' => true
                ),
                'type' => array(
                    'pjActionRequired' => true,
                    'pjActionNotEmpty' => true,
                    array('pjActionInList', array('amount', 'percent'))
                )
            )
        );

        public static function factory($attr = array())
        {
            return new self($attr);
        }
    }