jQuery(document).ready(function () {
	jQuery('.filed_label').each(function() {
		if(document.getElementById("frontend_submit_fields").value == document.getElementById("all_fields").value)
			document.getElementById("all_fields").checked = true;
		if(inArray(this.value, document.getElementById("frontend_submit_fields").value.split(","))) {
			this.checked = true;
		}
	});

	jQuery('.stats_filed_label').each(function() {
		if(document.getElementById("frontend_submit_stat_fields").value == document.getElementById("all_stats_fields").value)
			document.getElementById("all_stats_fields").checked = true;
		if(inArray(this.value, document.getElementById("frontend_submit_stat_fields").value.split(","))) {
			this.checked = true;
		}
	});

	jQuery(document).on('change','input[name="all_fields"]',function() {
		jQuery('.filed_label').prop("checked" , this.checked);
	});

	jQuery(document).on('change','input[name="all_stats_fields"]',function() {
		jQuery('.stats_filed_label').prop("checked" , this.checked);
	});
  fm_toggle_email_options(jQuery('input[name=sendemail]:checked').val() == '1' ? true : false);

  // Bind filter action on entering search key and when the user cancel the input.
  jQuery(".placeholders-filter").on("keyup input", function() { filter_placeholders(this); });
  jQuery('#placeholders_overlay').on("click", function() { fm_placeholders_popup_close(); });

  // Close popup on escape.
  jQuery(document).on('keydown', function (e) {
    if (e.keyCode === 27) { /* Esc.*/
      if (jQuery("#placeholders_overlay").is(":visible")) {
        fm_placeholders_popup_close();
      }
    }
  });
});
		
jQuery(window).load(function () {
	var fieldset_id = jQuery("#fieldset_id").val();
	form_maker_options_tabs(fieldset_id);
	
	if ( fieldset_id == 'javascript' ) {
		codemirror_for_javascript();
	}
	
	fm_change_payment_method(payment_method);
	fm_popup();
	function hide_email_labels(event) {
		var e = event.toElement || event.relatedTarget;
		if (e.parentNode == this || e == this) {
			return;
		}
		this.style.display="none";
	}
	if(document.getElementById('mail_from_labels')) {
		document.getElementById('mail_from_labels').addEventListener('mouseout',hide_email_labels,true);
	}
	if(document.getElementById('mail_subject_labels')) {
		document.getElementById('mail_subject_labels').addEventListener('mouseout',hide_email_labels,true);
	}
	if(document.getElementById('mail_from_name_user_labels')) {
		document.getElementById('mail_from_name_user_labels').addEventListener('mouseout',hide_email_labels,true);
	}
	if(document.getElementById('mail_subject_user_labels')) {
		document.getElementById('mail_subject_user_labels').addEventListener('mouseout',hide_email_labels,true);
	}
	if(document.getElementById('post_title_labels')) {
		document.getElementById('post_title_labels').addEventListener('mouseout',hide_email_labels,true);
	}
	if(document.getElementById('post_tags_labels')) {
		document.getElementById('post_tags_labels').addEventListener('mouseout',hide_email_labels,true);
	}
	if(document.getElementById('post_featured_image_labels')) {
		document.getElementById('post_featured_image_labels').addEventListener('mouseout',hide_email_labels,true);
	}
	if(document.getElementById('dbox_upload_dir_labels')) {
		document.getElementById('dbox_upload_dir_labels').addEventListener('mouseout',hide_email_labels,true);
	}
});

/**
 * Filter placeholders.
 *
 * @param that
 */
function filter_placeholders(that) {
  // Get search key.
  var search = jQuery(that).val().toLowerCase();
  // Remove previous serach results from filtered fields section.
  jQuery(".filtered-placeholders .inside").html("");
  if (search != "") {
    var found = false;
    // Hide all field sections.
    jQuery(".placeholders_cont .postbox").addClass("hide");
    jQuery(".placeholders .postbox:not(.filtered-placeholders) .wd-button").each(function () {
      var field_name = jQuery(this).html().toLowerCase();
      if (field_name.indexOf(search) != '-1') {
        jQuery(".filtered-placeholders .inside").append(jQuery(this).clone());
        found = true;
      }
    });
    // If nothing found.
    if (!found) {
      jQuery(".filtered-placeholders .inside").html(form_maker.nothing_found);
    }
    // Show search results in filtered fields section.
    jQuery(".placeholders_cont .filtered-placeholders").removeClass("hide");
  }
  else {
    jQuery(".placeholders_cont .postbox").removeClass("hide");
    jQuery(".placeholders_cont .filtered-placeholders").addClass("hide");
  }
}

function wd_fm_apply_options(task) {	
	var submit = fm_option_tabs_mail_validation();		
	if(submit === true){
		set_condition();
		fm_set_input_value('task', task);
		return true;
	}
	return false;
}

function fm_placeholders_popup(input_id) {
  var active_input = jQuery('#' + input_id);
  var active_input_container = active_input.closest('.wd-group');
  active_input_container.addClass('placeholders-active');
  jQuery('html').animate({scrollTop: active_input_container.offset().top - 50}, 500);
  var popup = jQuery('.placeholder-popup');
  active_input_container.prepend(jQuery('#placeholders_overlay'));
  if (active_input_container.find('.wp-editor-wrap').length) {
    active_input_container.find('.wp-editor-wrap').append(popup);
  }
  else {
    active_input.after( popup );
    var orig_val = active_input.val();
    active_input.focus().val('').val(orig_val);
  }
  popup.show();
}

function fm_placeholders_popup_close() {
  var active_input = jQuery('#placeholders_overlay').closest('.wd-group');
  active_input.removeClass('placeholders-active');
  var popup = jQuery('.placeholder-popup');
  popup.hide();
}

function set_condition() {
	field_condition = '';
	for(i=0;i<500;i++) {
		conditions = '';
		if(document.getElementById("condition"+i)) {
			field_condition+=document.getElementById("show_hide"+i).value+"*:*show_hide*:*";
			field_condition+=document.getElementById("fields"+i).value+"*:*field_label*:*";
			field_condition+=document.getElementById("all_any"+i).value+"*:*all_any*:*";
			for(k=0;k<500;k++) {
				if(document.getElementById("condition_div"+i+"_"+k)) {
					conditions+=document.getElementById("field_labels"+i+"_"+k).value+"***";
					conditions+=document.getElementById("is_select"+i+"_"+k).value+"***";
					if(document.getElementById("field_value"+i+"_"+k).tagName=="SELECT" ) {
						if(document.getElementById("field_value"+i+"_"+k).getAttribute('multiple')) {
							var sel = document.getElementById("field_value"+i+"_"+k);
							var selValues = '';
							for(m=0; m < sel.length; m++) {
								if(sel.options[m].selected)

								selValues += sel.options[m].value+"@@@";
							}
							conditions+=selValues;
						} else {
							conditions+=document.getElementById("field_value"+i+"_"+k).value;
						}
					}
					else
						conditions+=document.getElementById("field_value"+i+"_"+k).value;
					conditions+="*:*next_condition*:*";
				}
			}
			field_condition+=conditions;
			field_condition+="*:*new_condition*:*";
		}
	}
	document.getElementById('condition').value = field_condition;
}

function show_verify_options(s){
	if(s){
		jQuery(".verification_div").removeAttr( "style" );
		jQuery(".expire_link").removeAttr( "style" );

	} else{
		jQuery(".verification_div").css( 'display', 'none' );
		jQuery(".expire_link").css( 'display', 'none' );
	}
}
		
function inArray(needle, myarray) {
	var length = myarray.length;
	for(var i = 0; i < length; i++) {
		if(myarray[i] == needle) return true;
	}
	return false;
}
		
function checkAllByParentId(id) {
	var checkboxes = document.getElementById(id).getElementsByTagName('input');					
	if (checkboxes[0].checked) {
		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].type == 'checkbox') {
				 checkboxes[i].checked = true;
			}
		}
	} else {
		 for (var i = 0; i < checkboxes.length; i++) {
			 if (checkboxes[i].type == 'checkbox') {
				 checkboxes[i].checked = false;
			 }
		 }
	}
}
	
function checked_labels(class_name) {								
	var checked_ids ='';
	jQuery('.'+class_name).each(function() {
	  if(this.checked) {
		checked_ids += this.value+',';
	  }
	});

	if(class_name == 'filed_label') {
		document.getElementById("frontend_submit_fields").value = checked_ids;
		if(checked_ids == document.getElementById("all_fields").value)
			document.getElementById("all_fields").checked = true;
		else
			document.getElementById("all_fields").checked = false;
	}
	else {
	  document.getElementById("frontend_submit_stat_fields").value = checked_ids;
	  if(checked_ids == document.getElementById("all_stats_fields").value)
		document.getElementById("all_stats_fields").checked = true;
	  else
		document.getElementById("all_stats_fields").checked = false;
	}
}			

function codemirror_for_javascript() {
	if (!jQuery("#form_javascript").next().length) {
		var editor = CodeMirror.fromTextArea(document.getElementById("form_javascript"), {
			lineNumbers: true,
			lineWrapping: true,
			mode: "javascript"
		});
		CodeMirror.commands["selectAll"](editor);
		editor.autoFormatRange(editor.getCursor(true), editor.getCursor(false));
		editor.scrollTo(0,0);
	}
}

function fm_toggle_email_options(show) {
  if (show) {
    jQuery('.fm_email_options').show();
  }
  else {
    jQuery('.fm_email_options').hide();
  }
}
