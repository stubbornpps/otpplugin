<?php
/**
 * Plugin Name: Woocommerce OTP Verification 
 * Description: Login With the Mobile number and Otp authentication 
 * Version: 1.0
 * Author: Evince Development Pvt. Ltd.
 * Author URI: https://evincedev.com/
 * Text Domain: woo-otp-verification
 * */

defined('ABSPATH') || exit;
if (!function_exists('is_plugin_active')) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';        
}
//constants
if(!defined('WOO_OTP_PATH')){
    define('WOO_OTP_PATH', plugin_dir_path(__FILE__));    
}
if(!defined('WOO_OTP_URI')){
    define('WOO_OTP_URI', plugin_dir_url( __FILE__ ));
}
if(!defined('WOO_OTP_DB')){
    global $wpdb;
    define( 'WOO_OTP_DB', $wpdb->prefix.'phone_otp_db');        
}
if(!defined('WOO_OTP_DB_SETTING_API')){    
    define( 'WOO_OTP_DB_SETTING_API', $wpdb->prefix.'phone_otp_setting_db');        
}
if(!defined('WOO_OTP_DB_SETTING_FAST2SMS')){
    define( 'WOO_OTP_DB_SETTING_FAST2SMS', $wpdb->prefix.'phone_otp_setting_fast2sms');        
}
if(!defined('WOO_OTP_DB_SETTING_FIREBASE')){
    define( 'WOO_OTP_DB_SETTING_FIREBASE', $wpdb->prefix.'phone_otp_setting_firebase');        
}
//checking if woocommerce active 
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {    
    if(!class_exists( 'WOO_OTP_VERIFICATION' )){       
        class WOO_OTP_VERIFICATION {
            public function __construct(){                                
                require(WOO_OTP_PATH.'/includes/activate.php');
                register_activation_hook(WOO_OTP_PATH,array(new WOO_OTP_ACTIVATE()));                 
                register_deactivation_hook(__FILE__, array($this,'my_plugin_remove_database'));
                if(get_option('otp_switch') === "on"){
                    require(WOO_OTP_PATH.'/includes/template.php');                
                    require(WOO_OTP_PATH.'/views/frontend/frontend.php');
                }
                    require(WOO_OTP_PATH.'/views/admin/admin.php');
                    require(WOO_OTP_PATH.'/includes/api.php');                    
                    add_action('wp_enqueue_scripts','custom_add_js');   
                    add_action('wp_footer',function(){ ?>                                                        
                        <script src="https://www.gstatic.com/firebasejs/8.8.0/firebase-app.js"></script>
                        <script src="https://www.gstatic.com/firebasejs/8.8.0/firebase-auth.js"></script>
                        <script src="https://www.gstatic.com/firebasejs/8.8.0/firebase-analytics.js"></script>              
                        <?php 
                    });                                                
                add_action( 'admin_enqueue_scripts', 'my_plugin_scripts' );                         
                function my_plugin_scripts(){
                    wp_enqueue_style('woo-otp-admin-style',WOO_OTP_URI.'/assets/css/admin-style.css','',1.0);
                    wp_enqueue_script('woo-otp-admin-script',WOO_OTP_URI.'/assets/js/admin/admin_script.js','',1.0);
                    wp_localize_script("woo-otp-admin-script",'wooadminotp',admin_url('admin-ajax.php'));       
                }
                function custom_add_js() {  
                    wp_enqueue_style( 'woo-otp-style',WOO_OTP_URI.'/assets/css/style.css','',1.0);
                    wp_enqueue_script( 'woo-otp-js',WOO_OTP_URI.'/assets/js/script.js','',1.0);                                           
                    wp_localize_script("woo-otp-js",'woootp',admin_url('admin-ajax.php'));                    
                }                                                                                     
                $plugin = plugin_basename( __FILE__ );
                add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link');                    
                function plugin_add_settings_link( $links ) {                    
                    $settings_link = '<a href="admin.php?page=woo-otp-verification-setting">' . __( 'Settings','woo-otp-verification') . '</a>';
                    array_unshift( $links, $settings_link );
                    return $links;
                }
            }
            public function my_plugin_remove_database(){
                delete_option("otp_switch");
                global $wpdb;            
                $tableArray = [   
                    $wpdb->prefix . "phone_otp_db",                    
                    $wpdb->prefix . "phone_otp_setting_db",                    
                    $wpdb->prefix . "phone_otp_setting_fast2sms",                    
                    $wpdb->prefix . "phone_otp_setting_firebase",                    
                ];          
                foreach ($tableArray as $tablename) {
                   $wpdb->query("DROP TABLE IF EXISTS $tablename");
                }              
            }            
        }
        new WOO_OTP_VERIFICATION();
    }
} else {    
    add_action( 'admin_notices', function(){        
        echo '<div id="message" class="notice notice is-dismissible"><p>'.esc_html_e('Woocommerce','woo-otp-verification').'<b>'.esc_html_e('is not active. Please activate woocommerce first!.','woo-otp-verification').'</b></p></div>';
	} );
    deactivate_plugins( plugin_basename( __FILE__ ) );    
}