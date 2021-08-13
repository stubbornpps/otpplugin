<?php 
if(!class_exists('WOO_OTP_ACTIVATE')){
    class WOO_OTP_ACTIVATE{        
        public function __construct(){ 
            add_option("otp_switch", "off" );                   
            global $wpdb;
            $charset_collate = '';
            if (!empty($wpdb->charset)) {
                $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
            }
            if (!empty($wpdb->collate)) {
                $charset_collate .= " COLLATE {$wpdb->collate}";
            }
            require_once(ABSPATH.'wp-admin/includes/upgrade.php');                
            $woo_otp_db = "CREATE TABLE IF NOT EXISTS `" . WOO_OTP_DB . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,        
                `phone` varchar(255) NOT NULL,        
                `otp` varchar(255) NOT NULL,                                       
                `is_verified` varchar(255) NOT NULL,     
                `begin_time` TIMESTAMP NOT NULL,
                `created_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,           
                PRIMARY KEY (id)        
            ) $charset_collate;";                            
            dbDelta($woo_otp_db);                                                                                 
            $woo_otp_api_select = "CREATE TABLE IF NOT EXISTS `" . WOO_OTP_DB_SETTING_API . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,        
                `selected_api` varchar(255) NOT NULL,                                              
                PRIMARY KEY (id)        
            ) $charset_collate;";                            
            dbDelta($woo_otp_api_select);         
            $woo_otp_setting_fast2sms = "CREATE TABLE IF NOT EXISTS `" . WOO_OTP_DB_SETTING_FAST2SMS . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,        
                `curl_url` varchar(255) NOT NULL,        
                `api_key` varchar(255) NOT NULL,                             
                `otp_length` varchar(255) NOT NULL,        
                `otp_message` varchar(255) NOT NULL,                                       
                `otp_resend_time` varchar(255) NOT NULL,                                       
                `otp_expire_time` varchar(255) NOT NULL,                                               
                PRIMARY KEY (id)        
            ) $charset_collate;";                            
            dbDelta($woo_otp_setting_fast2sms);                                                                                                                            
            $woo_otp_setting_firebase = "CREATE TABLE IF NOT EXISTS `" . WOO_OTP_DB_SETTING_FIREBASE . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,  
                `otp_resend_timer_firebase` varchar(255) NOT NULL,              
                `country_code` varchar(255) NOT NULL,              
                `api_key_firebase` varchar(255) NOT NULL,        
                `authDomain` varchar(255) NOT NULL,                             
                `projectid` varchar(255) NOT NULL,        
                `storage_bucket` varchar(255) NOT NULL,                                       
                `messagingSenderId` varchar(255) NOT NULL,                                       
                `appId` varchar(255) NOT NULL,                                               
                `measurementId` varchar(255) NOT NULL,                                               
                PRIMARY KEY (id)        
            ) $charset_collate;";                            
            dbDelta($woo_otp_setting_firebase);
        }
                    
    }
 }
