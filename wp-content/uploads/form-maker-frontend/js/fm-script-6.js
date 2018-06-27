    var fm_currentDate = new Date();
    var FormCurrency_6 = '';
    var FormPaypalTax_6 = '0';
    var check_submit6 = 0;
    var check_before_submit6 = {};
    var required_fields6 = ["2","10","3","4"];
    var labels_and_ids6 = {"2":"type_text","10":"type_name","9":"type_date_new","13":"type_time","1":"type_submit_reset","3":"type_text","4":"type_submitter_mail"};
    var check_regExp_all6 = [];
    var check_paypal_price_min_max6 = [];
    var file_upload_check6 = [];
    var spinner_check6 = [];
    var scrollbox_trigger_point6 = '20';
    var header_image_animation6 = 'none';
    var scrollbox_loading_delay6 = '0';
    var scrollbox_auto_hide6 = '1';
         function before_load6() {	
}	
 function before_submit6() {
	 }	
 function before_reset6() {	
}
    function onload_js6() {
  jQuery("#button_calendar_9, #fm-calendar-9").click(function() {
    jQuery("#wdform_9_element6").datepicker("show");
  });
  jQuery("#wdform_9_element6").datepicker({
    dateFormat: format_date,
    minDate: "",
    maxDate: "",
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+50",
    showOtherMonths: true,
    selectOtherMonths: true,
    firstDay: "0",
    beforeShow: function(input, inst) {
      jQuery("#ui-datepicker-div").addClass("fm_datepicker");
    },
    beforeShowDay: function(date) {
      var invalid_dates = "";
      var invalid_dates_finish = [];
      var invalid_dates_start = invalid_dates.split(",");
      var invalid_date_range =[];
      for(var i = 0; i < invalid_dates_start.length; i++ ) {
        invalid_dates_start[i] = invalid_dates_start[i].trim();
        if(invalid_dates_start[i].length < 11 || invalid_dates_start[i].indexOf("-") == -1){
          invalid_dates_finish.push(invalid_dates_start[i]);
        }
        else{
          if(invalid_dates_start[i].indexOf("-") > 4) {
            invalid_date_range.push(invalid_dates_start[i].split("-"));
          }
          else {
            var invalid_date_array = invalid_dates_start[i].split("-");
            var start_invalid_day = invalid_date_array[0] + "-" + invalid_date_array[1] + "-" + invalid_date_array[2];
            var end_invalid_day = invalid_date_array[3] + "-" + invalid_date_array[4] + "-" + invalid_date_array[5];
            invalid_date_range.push([start_invalid_day, end_invalid_day]);
          }
        }
      }
      jQuery.each(invalid_date_range, function( index, value ) {
        for(var d = new Date(value[0]); d <= new Date(value[1]); d.setDate(d.getDate() + 1)) {
          invalid_dates_finish.push(jQuery.datepicker.formatDate(format_date, d));
        }
      });
      var string_days = jQuery.datepicker.formatDate(format_date, date);
      var day = date.getDay();
      return [ invalid_dates_finish.indexOf(string_days) == -1 ];
    }
  });
  var default_date;
  var date_value = jQuery("#wdform_9_element6").val();
  (date_value != "") ? default_date = date_value : default_date = "";
  var format_date = "mm/dd/yy";
  jQuery("#wdform_9_element6").datepicker("option", "dateFormat", format_date);
  if(default_date == "today") {
    jQuery("#wdform_9_element6").datepicker("setDate", new Date());
  }
  else if (default_date.indexOf("d") == -1 && default_date.indexOf("m") == -1 && default_date.indexOf("y") == -1 && default_date.indexOf("w") == -1) {
    jQuery("#wdform_9_element6").datepicker("setDate", default_date);
  }
  else {
    jQuery("#wdform_9_element6").datepicker("setDate", default_date);
  }
    }
    function condition_js6() {
    }
    function check_js6(id, form_id) {
    if (id != 0) {
    x = jQuery("#" + form_id + "form_view"+id);
    }
    else {
    x = jQuery("#form"+form_id);
    }    }
    function onsubmit_js6() {
    
  var disabled_fields = "";
  jQuery("#form6 div[wdid]").each(function() {
    if(jQuery(this).css("display") == "none") {
      disabled_fields += jQuery(this).attr("wdid");
      disabled_fields += ",";
    }
    if(disabled_fields) {
      jQuery("<input type=\"hidden\" name=\"disabled_fields6\" value =\""+disabled_fields+"\" />").appendTo("#form6");
    }
  });    }
    jQuery(window).load(function () {
    formOnload(6);
    });
    form_view_count6 = 0;
    jQuery(document).ready(function () {
    fm_document_ready(6);
    });
    