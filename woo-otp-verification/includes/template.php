<?php 
if(!class_exists('WOO_OTP_TEMPLATE')){
    class WOO_OTP_TEMPLATE{        
        public function __construct(){                    
            add_filter( 'woocommerce_locate_template',array($this,'myplugin_woocommerce_locate_template'), 10, 3 );                                                   
        }
        public function myplugin_woocommerce_locate_template( $template, $template_name, $template_path ) {
            global $woocommerce;

            $_template = $template;

            if ( ! $template_path ) $template_path = $woocommerce->template_url;

            $plugin_path  = untrailingslashit(WOO_OTP_PATH).'/woocommerce/';                                
            $template = locate_template(
                array(
                $template_path . $template_name,
                $template_name
                )
            );                    
            if ( ! $template && file_exists( $plugin_path . $template_name ) )
                $template = $plugin_path . $template_name;                    
            if ( ! $template )
                $template = $_template;
            return $template;
        }                  
    }
    new WOO_OTP_TEMPLATE();
 }
