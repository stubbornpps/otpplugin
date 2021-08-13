<?php
if(!class_exists('WOO_OTP_FRONTEND')){
    class WOO_OTP_FRONTEND{
        public function __construct(){   
            //register form myaccount page
            add_action('woocommerce_register_form_start',array($this,'woocom_extra_register_fields'),10);                                            
            add_action('woocommerce_register_post', array($this,'woocom_validate_extra_register_fields'), 10, 3);
            add_action('personal_options_update', array($this,'woocom_save_extra_register_fields'));    
            add_action('edit_user_profile_update',array($this,'woocom_save_extra_register_fields'));   
            add_action('woocommerce_created_customer', array($this,'woocom_save_extra_register_fields'));
            //admin edit account page            
            add_action( 'woocommerce_save_account_details',array($this, 'save_account_details' ));
            add_filter('woocommerce_save_account_details_required_fields', array($this,'make_field_required_phone'));              
            add_action( 'wp_logout', array($this,'firebase_after_logout')  );          
        }
        public function firebase_after_logout(){
            global $wpdb;
            $selected_otp_gateway = $wpdb->get_results("SELECT * FROM " . WOO_OTP_DB_SETTING_API . " ORDER BY id DESC LIMIT 1");
            if ($selected_otp_gateway[0]->selected_api == "Firebase") {
                ?>
                <script>
                firebase.auth().signOut().then(() => {
                    console.log('firebase logout');
                }).catch((error) => {
                    console.log('error while logout '+error);
                });
                </script>
                  <?php
            }   
        }
        public function woocom_extra_register_fields() {
            ?>
            <p class='form-row form-row-wide'>
                <label for='reg_mobile_number'><?php _e( 'Mobile Number', 'woo-otp-verification' ); ?><span class='required'> *</span></label><input type='text' class='input-text' name='reg_mobile_number' id='reg_mobile_number' value='<?php if ( ! empty( $_POST['reg_mobile_number'] ) ) esc_attr_e( $_POST['reg_mobile_number'] ); ?>' />                
            </p>                
            <?php
        }
        public function woocom_validate_extra_register_fields( $username, $email, $validation_errors ){                
            if (isset($_POST['reg_mobile_number']) && empty($_POST['reg_mobile_number']) ) {
                $validation_errors->add('mobile_number_error', __('Mobile Number is Required!', 'woo-otp-verification'));
            }
            if (isset($_POST['reg_mobile_number']) && !empty($_POST['reg_mobile_number']) && $_POST['reg_mobile_number'] < 10  ) {
                $validation_errors->add('mobile_number_error', __('Please type a correct phone number!', 'woo-otp-verification'));
            }
            $user_exists = $this->wooc_get_users_by_phone($_POST['reg_mobile_number']);            
            if($user_exists){                
                $validation_errors->add('mobile_number_error', __('User already exists with this nummber!', 'woo-otp-verification'));
            }
            return $validation_errors;
        }
        public function woocom_save_extra_register_fields($customer_id) {
            if (isset($_POST['reg_mobile_number'])) {
                update_user_meta($customer_id, 'billing_phone', sanitize_text_field($_POST['reg_mobile_number']));
            }                
        }
        public function wooc_get_users_by_phone($phone_number){
            $user_query = new WP_User_Query( array(
                'meta_key' => 'billing_phone',
                'meta_value' => $phone_number,
                'compare'=> '='
            ));
            return $user_query->get_results();
        }        
        public function save_account_details($user_id){
            update_user_meta( $user_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
        }
        public function make_field_required_phone(){
            $required_fields['billing_phone'] = 'Phone Number';
            return $required_fields;
        }
  }
  if(get_option('otp_switch') === "on"){
      new WOO_OTP_FRONTEND();
  }
}

