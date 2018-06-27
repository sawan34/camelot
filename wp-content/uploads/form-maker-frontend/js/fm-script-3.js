    var fm_currentDate = new Date();
    var FormCurrency_3 = '$';
    var FormPaypalTax_3 = '0';
    var check_submit3 = 0;
    var check_before_submit3 = {};
    var required_fields3 = ["3","4","5","6","7","2","8"];
    var labels_and_ids3 = {"3":"type_name","4":"type_submitter_mail","5":"type_radio","6":"type_own_select","7":"type_own_select","2":"type_text","8":"type_textarea","1":"type_submit_reset"};
    var check_regExp_all3 = [];
    var check_paypal_price_min_max3 = [];
    var file_upload_check3 = [];
    var spinner_check3 = [];
    var scrollbox_trigger_point3 = '20';
    var header_image_animation3 = 'flash';
    var scrollbox_loading_delay3 = '0';
    var scrollbox_auto_hide3 = '1';
         function before_load3() {	
}	
 function before_submit3() {
	 }	
 function before_reset3() {	
}
    function onload_js3() {
    }
    function condition_js3() {
  if( jQuery("input[name^='wdform_5_element3']:checked").val()=="Joomla!" ) {
    jQuery("#form3 div[wdid=6]").removeAttr("style");
  }
  else {
    jQuery("#form3 div[wdid=6]").css("display", "none");
  }
  jQuery("div[wdid=5] input[type='radio']").click(function() {
    if( jQuery("input[name^='wdform_5_element3']:checked").val()=="Joomla!" ) {
      jQuery("#form3 div[wdid=6]").removeAttr("style");
    }
    else {
      jQuery("#form3 div[wdid=6]").css("display", "none");
    }
  });
  if( jQuery("input[name^='wdform_5_element3']:checked").val()=="Wordpress" ) {
    jQuery("#form3 div[wdid=7]").removeAttr("style");
  }
  else {
    jQuery("#form3 div[wdid=7]").css("display", "none");
  }
  jQuery("div[wdid=5] input[type='radio']").click(function() {
    if( jQuery("input[name^='wdform_5_element3']:checked").val()=="Wordpress" ) {
      jQuery("#form3 div[wdid=7]").removeAttr("style");
    }
    else {
      jQuery("#form3 div[wdid=7]").css("display", "none");
    }
  });
    }
    function check_js3(id, form_id) {
    if (id != 0) {
    x = jQuery("#" + form_id + "form_view"+id);
    }
    else {
    x = jQuery("#form"+form_id);
    }    }
    function onsubmit_js3() {
    
  jQuery("<input type=\"hidden\" name=\"wdform_5_allow_other3\" value=\"no\" />").appendTo("#form3");
  jQuery("<input type=\"hidden\" name=\"wdform_5_allow_other_num3\" value=\"0\" />").appendTo("#form3");
  var disabled_fields = "";
  jQuery("#form3 div[wdid]").each(function() {
    if(jQuery(this).css("display") == "none") {
      disabled_fields += jQuery(this).attr("wdid");
      disabled_fields += ",";
    }
    if(disabled_fields) {
      jQuery("<input type=\"hidden\" name=\"disabled_fields3\" value =\""+disabled_fields+"\" />").appendTo("#form3");
    }
  });    }
    jQuery(window).load(function () {
    formOnload(3);
    });
    form_view_count3 = 0;
    jQuery(document).ready(function () {
    fm_document_ready(3);
    });
    