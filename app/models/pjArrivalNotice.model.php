<?php
    if (!defined("ROOT_PATH")) {
        header("HTTP/1.1 403 Forbidden");
        exit;
    }

    /**
     * Class pjArrivalNotice
     */
    class pjArrivalNoticeModel extends pjAppModel
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
        protected $table = 'arrival_notice';

        /**
         * Schema
         *
         * @var array
         */
        protected $schema = array(
            array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
            array('name' => 'date_from', 'type' => 'date', 'default' => ':NULL'),
            array('name' => 'date_to', 'type' => 'date', 'default' => ':NULL')
        );

        /**
         * Default validation
         *
         * @var array
         */
        protected $validate = array();

        /**
         * Translatable fields
         * 
         * @var array
         */
        protected $i18n = array();

        public static function factory($attr = array())
        {
            return new self($attr);
        }
    }