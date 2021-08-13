jQuery(document).ready(function(){    
    jQuery('#otp_enable_switch').on('change',function(){
        if(jQuery(this).val() === "off"){                                   
            let form_data = {
                action:'adminapicall',
                param:"otp_switch",
                otp_enable : jQuery(this).val(),
            };
            jQuery.ajax({
                url: wooadminotp,
                type: "POST",
                dataType: "json",
                data: form_data,
                success:function(response){                    
                    if(response.status === 1){                        
                        jQuery('.api_gateway_div,.setting_form_1').hide();
                        jQuery('#api_gateway').find("option[selected]").removeAttr("selected");
                        jQuery('#api_gateway').find("option[value='nooption']").attr("selected",true);
                    }else{
                        console.log('Error occured while saving ! ');
                    }
                },
                error:function(err){                                        
                    console.log(err);
                }
            })            
        }
        if(jQuery(this).val() === "on"){            
            let form_data = {
                action:'adminapicall',
                param:"otp_switch",
                otp_enable : jQuery(this).val(),
            };
            jQuery.ajax({
                url: wooadminotp,
                type: "POST",
                dataType: "json",
                data: form_data,
                success:function(response){                    
                    if(response.status === 1){
                        jQuery('.api_gateway_div').show();
                    }else{
                        console.log('Error occured while saving ! ');
                    }
                },
                error:function(err){
                    console.log(err);
                }
            }) 
        }        
    })
    jQuery('#api_gateway').on('change',function(){        
        if(jQuery(this).val() == "Fast2Sms"){                        
            let form_data = {
                action: "adminapicall",
                param: "select_gateway",                
                select_gateway:jQuery(this).val(),
            };      
            jQuery.ajax({
                url: wooadminotp,
                type: "POST",
                dataType: "json",
                data: form_data,
                success:function(response){
                    if(response.status == 1){
                        jQuery('.setting_form_1').show();
                        jQuery('.Firebase_Setting').hide();
                        jQuery('.Fast2Sms').show();
                        jQuery('#Firebase_decision').val('No');
                        jQuery('#Fast2Sms_decision').val('Yes');
                    }                  
                    if(response.status == 0){
                        jQuery(response.response_back).insertAfter('.wrap');
                    }
                },
                error:function(err){
                    alert(err);
                }
            });
        }
        if(jQuery(this).val() == "Firebase"){                        
            let form_data = {
                action: "adminapicall",
                param: "select_gateway",                
                select_gateway:jQuery(this).val(),
            };      
            jQuery.ajax({
                url: wooadminotp,
                type: "POST",
                dataType: "json",
                data: form_data,
                success:function(response){
                    jQuery('.setting_form_1').show();
                    jQuery('.Fast2Sms').hide();
                    jQuery('.Firebase_Setting').show();
                    jQuery('#Firebase_decision').val('Yes');
                    jQuery('#Fast2Sms_decision').val('No');                                        
                }
            });
        }        
    });    
});