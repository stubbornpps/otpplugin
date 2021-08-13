jQuery(document).ready(function(){
    var filter = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;    
    jQuery('#login_button').prop('disabled',true);
    jQuery('#user_phone_number').keyup(function(){
        if(jQuery(this).val().length >= 10 && filter.test(jQuery(this).val())){
            jQuery('#login_button').prop('disabled',false);
        }
    });
    jQuery('#login_button').click(function(){
        var resend_timer ;        
        let mobile_number =jQuery('#user_phone_number').val() ;          
        let form_data = {
            action: "apicall",
            param: "apicall",
            mobile_number : mobile_number,     
            resend_otp:0,       
        };         
        jQuery.ajax({
            url: woootp,
            type: "POST",
            dataType: "json",
            data: form_data,
            success:function(response){   
                console.log(response);
                if(response.status == 1){                                        
                    if(response.gateway == 'Fast2Sms'){                                    
                        window.resend_timer = response.resend_timer;
                        sessionStorage.setItem("otp_token", response.otp);
                        jQuery('#login_button').fadeOut();
                        jQuery('#verify_otp,#otp_field').fadeIn();                                           
                        jQuery(response.resend_button).insertAfter('#verify_otp');                                      
                    }                    
                    if(response.gateway == 'Firebase'){    
                        jQuery('#login_button').fadeOut();
                        jQuery('#verify_otp,#otp_field').fadeIn(); 
                        window.resend_timer = response.resend_timer_firebase;                        
                        jQuery('<button type="button" id="resend_otp" disabled>Resend in <span id="timer">'+response.resend_timer_firebase+'</span></button>').insertAfter('#verify_otp');                                                      
                                var firebaseConfig = {
                                    apiKey: response.response_back.api_key_firebase,
                                    authDomain: response.response_back.authDomain,
                                    projectId: response.response_back.projectid,
                                    storageBucket:response.response_back.storage_bucket,
                                    messagingSenderId: response.response_back.messagingSenderId,
                                    appId:response.response_back.appId,
                                    measurementId: response.response_back.measurementId
                                };                
                                firebase.initializeApp(firebaseConfig);
                                firebase.analytics();        
                                window.country_code_js = response.response_back.country_code;         
                                var phoneNumber = "+"+response.response_back.country_code+mobile_number;                                                
                                window.appVerifier = new firebase.auth.RecaptchaVerifier('phone-sign-in-recaptcha',{
                                    'size': 'invisible',                                
                                });                                                             
                                firebase.auth().settings.appVerificationDisabledForTesting = true; 
                                firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function(confirmationResult){
                                    window.confirmationResult = confirmationResult;                                         
                                }).catch(function (error){                                                               
                                    if(error.code == "auth/quota-exceeded"){
                                        error_response(error.message);
                                    }else if(error.code === 'auth/too-many-requests'){
                                        too_many_error = "Too many request for the OTP . Please Try Again Later.";
                                        error_response(too_many_error);                                                                
                                    }else{
                                        error_response(error.message);                                                                
                                    }
                                    console.log(error);
                                });                                                                              
                    }                
                }
                if(response.status == 0){      
                    $account_error = 'An account is not registered.<a href="#" class="showregister">Please sign up.</a>';
                    error_response($account_error);                
                }                        
                if(response.status == -1){                    
                    console.log(response.response_back);
                }                        
                if(response.status == -2){   
                    $error_message='Error while sending otp.<a href="#" class="showregister">Please check the authorization key.</a>';
                    error_response($error_message);                                        
                }                        
            },
            complete:function(){                
                startTimer();                
                jQuery('#resend_otp').click(function(){                         
                    jQuery('#resend_otp').prop('disabled',true);                    
                    jQuery('#timer').html(window.resend_timer);                          
                    startTimer(); 
                    let mobile_number =jQuery('#user_phone_number').val() ;                              
                    let form_data = {
                        action: "apicall",
                        param: "apicall",
                        mobile_number : mobile_number, 
                        previous_otp:sessionStorage.getItem("otp_token"),
                        resend_otp:1,                                              
                    }; 
                    jQuery.ajax({
                        url: woootp,
                        type: "POST",
                        dataType: "json",
                        data: form_data,
                        success:function(response){
                            if(response.gateway == 'Fast2Sms'){                                
                                if(response.status == 1){       
                                    sessionStorage.setItem("otp_token", response.otp);                                           
                                }
                                if(response.status == 0){
                                    error_response('An account is not registered.<a href="#" class="showregister">Please sign up.</a>');                                    
                                }                        
                                if(response.status == -1){
                                    console.log(response);
                                }                        
                                if(response.status == -2){
                                    error_response(' While sending otp.Please check the authorization key.');                                                                        
                                }   
                            }
                            if(response.gateway == 'Firebase'){
                                if(response.status == 1){                                                                                                                     
                                    firebase.auth().settings.appVerificationDisabledForTesting = true;
                                    var phoneNumber = "+"+country_code_js+mobile_number;                                                                                                                                                                           
                                    firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function(confirmationResult){
                                        window.confirmationResult = confirmationResult;                                                                                                   
                                    }).catch(function (error){    
                                        if(error.code == "auth/quota-exceeded"){
                                            error_response(error.message);
                                        }else if(error.code === 'auth/too-many-requests'){
                                            too_many_error = "Too many request for the OTP . Please Try Again Later.";
                                            error_response(too_many_error);                                                                
                                        }else{
                                            error_response(error.message);                                                                
                                        }
                                    });   
                                }
                            }                                                 
                        }
                    })
                });
            }          
        })       
    });
    
    jQuery('#verify_otp').click(function(){        
        let mobile_number =jQuery('#user_phone_number').val();   
        let otp = jQuery('#confirm_otp').val();           
        let form_data = {
            action: "apicall",
            param: "confirm_otp",
            mobile_number : mobile_number,    
            otp:otp        
        };         
        jQuery.ajax({
            url: woootp,
            type: "POST",
            dataType: "json",
            data: form_data,
            success:function(response){                
                if(response.status == 1){ 
                    if(response.gateway == 'Fast2Sms'){ 
                        sessionStorage.removeItem('otp_token');
                        location.reload();                    
                    }
                    if(response.gateway == 'Firebase'){ 
                         let form_data_1 = {
                            action: "apicall",
                            param: "confirm_otp_firebase",                            
                            mobile_number : mobile_number,    
                        };   
                        confirmationResult.confirm(otp).then((result) => {
                            console.log(result);
                            jQuery.ajax({
                                url: woootp,
                                type: "POST",
                                dataType: "json",
                                data: form_data_1,
                                success:function(response){
                                    if(response.status == 1){                                         
                                        location.reload();                                                            
                                    }       
                                }
                            })
                          }).catch((error) => {
                            if(error.code === 'auth/invalid-verification-code'){
                                auth_message = "Otp is wrong. Please entry the correct Otp.";
                                error_response(auth_message);                                                                
                            }else{
                                error_response(error.message);                                
                                console.log(error.code);                            
                            }
                          });                                                  
                    }
                }
                if(response.status == -1){
                    error_response(response.response_back);                    
                }
                if(response.status == -2){                    
                    error_response(response.response_back);                    
                }
                if(response.status == 0){
                    error_response(response.response_back);                    
                }
            }
        });  
    }) 
    //Timer JS
    function startTimer() {          
        var presentTime = jQuery('#timer').html();
        var timeArray = presentTime.split(/[:]+/);    
        var m = timeArray[0];
        var s = checkSecond((timeArray[1] - 1));
        if(s==59){m=m-1}
        if(m<0){
            jQuery('#resend_otp').prop('disabled',false);
            return
        }
        jQuery('#timer').html(m + ":" + s);        
        setTimeout(startTimer, 1000);    
    }
    function checkSecond(sec) {
        if (sec < 10 && sec >= 0) {sec = "0" + sec};    
        if (sec < 0) {sec = "59"};
        return sec;
    }
    //Close Timer JS    
    function error_response(error_message){
        jQuery('ul.woocommerce-error').remove();
        jQuery('.col-full .woocommerce .woocommerce-notices-wrapper').append('<ul class="woocommerce-error" role="alert"><li><strong>Error:</strong>'+error_message+'</li></ul>');
    }
});