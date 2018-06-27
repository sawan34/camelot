    var fm_currentDate = new Date();
    var FormCurrency_4 = '';
    var FormPaypalTax_4 = '0';
    var check_submit4 = 0;
    var check_before_submit4 = {};
    var required_fields4 = ["5"];
    var labels_and_ids4 = {"2":"type_radio","3":"type_radio","4":"type_own_select","6":"type_radio","5":"type_captcha","1":"type_submit_reset"};
    var check_regExp_all4 = [];
    var check_paypal_price_min_max4 = [];
    var file_upload_check4 = [];
    var spinner_check4 = [];
    var scrollbox_trigger_point4 = '20';
    var header_image_animation4 = 'none';
    var scrollbox_loading_delay4 = '0';
    var scrollbox_auto_hide4 = '1';
         function before_load4() {	
}	
 function before_submit4() {
	 }	
 function before_reset4() {	
}
    function onload_js4() {
  jQuery("#wd_captcha4").click(function() {captcha_refresh("wd_captcha","4")});
  jQuery("#_element_refresh4").click(function() {captcha_refresh("wd_captcha","4")});
  captcha_refresh("wd_captcha", "4");
    }
    function condition_js4() {
    }
    function check_js4(id, form_id) {
    if (id != 0) {
    x = jQuery("#" + form_id + "form_view"+id);
    }
    else {
    x = jQuery("#form"+form_id);
    }    }
    function onsubmit_js4() {
    
  jQuery("<input type=\"hidden\" name=\"wdform_2_allow_other4\" value=\"no\" />").appendTo("#form4");
  jQuery("<input type=\"hidden\" name=\"wdform_2_allow_other_num4\" value=\"0\" />").appendTo("#form4");
  jQuery("<input type=\"hidden\" name=\"wdform_3_allow_other4\" value=\"no\" />").appendTo("#form4");
  jQuery("<input type=\"hidden\" name=\"wdform_3_allow_other_num4\" value=\"0\" />").appendTo("#form4");
  jQuery("<input type=\"hidden\" name=\"wdform_6_allow_other4\" value=\"no\" />").appendTo("#form4");
  jQuery("<input type=\"hidden\" name=\"wdform_6_allow_other_num4\" value=\"0\" />").appendTo("#form4");
  var disabled_fields = "";
  jQuery("#form4 div[wdid]").each(function() {
    if(jQuery(this).css("display") == "none") {
      disabled_fields += jQuery(this).attr("wdid");
      disabled_fields += ",";
    }
    if(disabled_fields) {
      jQuery("<input type=\"hidden\" name=\"disabled_fields4\" value =\""+disabled_fields+"\" />").appendTo("#form4");
    }
  });    }
    jQuery(window).load(function () {
    formOnload(4);
    });
    form_view_count4 = 0;
    jQuery(document).ready(function () {
    fm_document_ready(4);
    });
    