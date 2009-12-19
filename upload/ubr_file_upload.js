//******************************************************************************************************
//	Name: ubr_file_upload.js
//	Revision: 3.8
//	Date: 5:58 PM August 22, 2009
//	Link: http://uber-uploader.sourceforge.net
//	Developer Peter Schmandra
//
//	BEGIN LICENSE BLOCK
//	The contents of this file are subject to the Mozilla Public License
//	Version 1.1 (the "License"); you may not use this file except in
//	compliance with the License. You may obtain a copy of the License
//	at http://www.mozilla.org/MPL/
//
//	Software distributed under the License is distributed on an "AS IS"
//	basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See
//	the License for the specific language governing rights and
//	limitations under the License.
//
//	Alternatively, the contents of this file may be used under the
//	terms of either the GNU General Public License Version 2 or later
//	(the "GPL"), or the GNU Lesser General Public License Version 2.1
//	or later (the "LGPL"), in which case the provisions of the GPL or
//	the LGPL are applicable instead of those above. If you wish to
//	allow use of your version of this file only under the terms of
//	either the GPL or the LGPL, and not to allow others to use your
//	version of this file under the terms of the MPL, indicate your
//	decision by deleting the provisions above and replace them with the
//	notice and other provisions required by the GPL or the LGPL. If you
//	do not delete the provisions above, a recipient may use your
//	version of this file under the terms of any one of the MPL, the GPL
//	or the LGPL.
//	END LICENSE BLOCK
//***************************************************************************************************************

var UberUpload={
	seconds:0,
	minutes:0,
	hours:0,
	get_status_url:null,
	total_upload_size:0,
	total_kbytes:0,
	toggle_upload_stats:0,
	file_label_highlight_on:'#FFFFE0',
	file_label_highlight_off:'#F9F9F9',
	CPB_loop:false,
	CPB_width:0,
	CPB_bytes:0,
	CPB_time_width:500,
	CPB_time_bytes:15,
	CPB_hold:true,
	CPB_byte_timer:null,
	CPB_status_timer:null,
	BPB_width_inc:0,
	BPB_width_new:0,
	BPB_width_old:0,
	BPB_timer:null,
	UP_timer:null,
	path_to_link_script:null,
	path_to_set_progress_script:null,
	path_to_get_progress_script:null,
	path_to_upload_script:null,
	check_allow_extensions_on_client:null,
	check_disallow_extensions_on_client:null,
	allow_extensions:null,
	disallow_extensions:null,
	check_file_name_format:null,
	check_file_name_regex:null,
	check_file_name_error_message:null,
	max_file_name_chars:null,
	min_file_name_chars:null,
	check_null_file_count:null,
	check_duplicate_file_count:null,
	max_upload_slots:null,
	cedric_progress_bar:null,
	cedric_hold_to_sync:null,
	bucket_progress_bar:null,
	progress_bar_width:null,
	block_ui_enabled:null,
	show_percent_complete:null,
	show_files_uploaded:null,
	show_current_position:null,
	show_current_file:null,
	show_elapsed_time:null,
	show_est_time_left:null,
	show_est_speed:null,

	getFileName:function(slot_value){
		var index_of_last_slash = slot_value.lastIndexOf("\\");

		if(index_of_last_slash < 1){ index_of_last_slash = slot_value.lastIndexOf("/"); }

		var file_name = slot_value.slice(index_of_last_slash + 1, slot_value.length);

		return file_name;
	},

	getFileExtension:function(slot_value){
		var file_extension = slot_value.substring(slot_value.lastIndexOf('.') + 1, slot_value.length).toLowerCase();

		return file_extension;
	},

	highlightFileLabel:function(file_label, color){ JQ("#"+file_label).css({background:color}); },

	clearFileLabels:function(){
		JQ(":file").each(function(){
			UberUpload.highlightFileLabel(JQ(this).attr('id')+"_label", UberUpload.file_label_highlight_off);
		});
	},

	// Check the file format before uploading
	checkFileNameFormat:function(){
		if(!UberUpload.check_file_name_format){ return false; }

		var found_error = false;

		JQ(":file").each(function(){
			if(JQ(this).val() !== ""){
				var file_name = UberUpload.getFileName(JQ(this).val());

				if(file_name.length > UberUpload.max_file_name_chars){
					UberUpload.highlightFileLabel(JQ(this).attr('id')+"_label", UberUpload.file_label_highlight_on);
					UberUpload.showAlert("Error, file name cannot be more than " + UberUpload.max_file_name_chars + " characters.", 500, 85, UberUpload.block_ui_enabled);
					found_error = true;
				}

				if(file_name.length < UberUpload.min_file_name_chars){
					UberUpload.highlightFileLabel(JQ(this).attr('id')+"_label", UberUpload.file_label_highlight_on);
					UberUpload.showAlert("Error, file name cannot be less than " + UberUpload.min_file_name_chars + " characters.", 500, 85, UberUpload.block_ui_enabled);
					found_error = true;
				}

				if(!UberUpload.check_file_name_regex.test(file_name)){
					UberUpload.highlightFileLabel(JQ(this).attr('id')+"_label", UberUpload.file_label_highlight_on);
					UberUpload.showAlert(UberUpload.check_file_name_error_message, 500, 85, UberUpload.block_ui_enabled);
					found_error = true;
				}
			}
		});

		return found_error;
	},

	// Check for legal file extentions
	checkAllowFileExtensions:function(){
		if(!UberUpload.check_allow_extensions_on_client){ return false; }

		var found_error = false;

		JQ(":file").each(function(){
			if(JQ(this).val() !== ""){
				var file_extension = UberUpload.getFileExtension(UberUpload.getFileName(JQ(this).val()));

				if(!file_extension.match(UberUpload.allow_extensions)){
					UberUpload.highlightFileLabel(JQ(this).attr('id')+"_label", UberUpload.file_label_highlight_on);
					UberUpload.showAlert('Sorry, uploading a file with the extension "' + file_extension + '" is not allowed.', 500, 85, UberUpload.block_ui_enabled);
					found_error = true;
				}
			}
		});

		return found_error;
	},

	// Check for illegal file extentions
	checkDisallowFileExtensions:function(){
		if(!UberUpload.check_disallow_extensions_on_client){ return false; }

		var found_error = false;

		JQ(":file").each(function(){
			if(JQ(this).val() !== ""){
				var file_extension = UberUpload.getFileExtension(UberUpload.getFileName(JQ(this).val()));

				if(file_extension.match(UberUpload.disallow_extensions)){
					UberUpload.highlightFileLabel(JQ(this).attr('id')+"_label", UberUpload.file_label_highlight_on);
					UberUpload.showAlert('Sorry, uploading a file with the extension "' + file_extension + '" is not allowed.', 500, 85, UberUpload.block_ui_enabled);
					found_error = true;
				}
			}
		});

		return found_error;
	},

	// Make sure the user selected at least one file
	checkNullFileCount:function(){
		if(!UberUpload.check_null_file_count){ return false; }

		var found_file = false;

		JQ(":file").each(function(){
			if(JQ(this).val() !== ""){ found_file = true; }
		});

		if(!found_file){
			UberUpload.showAlert("Please Choose A File To Upload.", 400, 80, UberUpload.block_ui_enabled);
			return true;
		}
		else{ return false; }
	},

	// Make sure the user is not uploading duplicate files
	checkDuplicateFileCount:function(){
		if(!UberUpload.check_duplicate_file_count){ return false; }

		var found_duplicate = false;
		var file_count = 0;
		var file_name_array = [];

		JQ(":file").each(function(){
			if(JQ(this).val() !== ""){
				var obj = {};
				obj.file_name = UberUpload.getFileName(JQ(this).val());
				obj.label_name = JQ(this).attr('id')+"_label";
				file_name_array[file_count] = obj;
				file_count++;
			}
		});

		for(var i = 0; i < file_name_array.length; i++){
			var obj_1 = file_name_array[i];

			for(var j = 0; j < file_name_array.length; j++){
				var obj_2 = file_name_array[j];

				if(obj_1.file_name === obj_2.file_name && obj_1.label_name !== obj_2.label_name){
					found_duplicate = true;
					UberUpload.highlightFileLabel(obj_1.label_name, UberUpload.file_label_highlight_on);
					UberUpload.highlightFileLabel(obj_2.label_name, UberUpload.file_label_highlight_on);
				}
			}
		}

		if(found_duplicate){
			UberUpload.showAlert("Duplicate upload files detected.", 400, 80, UberUpload.block_ui_enabled);
			return true;
		}
		else{ return false; }
	},

	showAlert:function(alert_message, alert_width, alert_height, block_ui_enabled){
		if(!block_ui_enabled){ alert(alert_message); }
		else{
			alert_message = '<br>' + alert_message + "<br><br><input style='width:75px;' type='button' id='ok_btn' name='ok' value='OK' onClick='JQ.unblockUI();'>";

			JQ.blockUI({
				message:alert_message,
				css:{
					width:alert_width+'px',
					height:alert_height+'px',
					top:(JQ(window).height() / 2) - (alert_height / 2) + 'px',
					left:(JQ(window).width() / 2) - (alert_width / 2) + 'px',
					textAlign:'center',
					cursor:'default',
					backgroundColor:'#EDEDED',
					borderColor:'#D9D9D9',
					color:'black',
					font:'14px Arial',
					fontWeight:'bold',
					padding:'2px',
					opacity:'1',
					'-webkit-border-radius':'2px',
					'-moz-border-radius':'2px'
				},
				overlayCSS:{
					cursor:'default',
					applyPlatformOpacityRules:true
				}
			});
		}
	},

	showCGIOutput:function(CGI_message, reset_page){
		UberUpload.showAlert(CGI_message, 400, 80, UberUpload.block_ui_enabled);
		if(reset_page){ UberUpload.resetFileUploadPage(); }
	},
	showDebugMessage:function(message){ JQ("#ubr_debug").append(message + "<br>"); },
	showAlertMessage:function(message){ JQ("#ubr_alert").html(message); },
	redirectAfterUpload:function(redirect_url, embedded_upload_results){
		if(embedded_upload_results){
			JQ('#upload_container').load(redirect_url);
			UberUpload.showEmbeddedUploadResults();
		}
		else{ self.location.href = redirect_url; }
	},

	showEmbeddedUploadResults:function(){
		UberUpload.stopDataLoop();
		UberUpload.resetProgressBar();

		JQ("#ubr_alert").html("");
		JQ("#upload_container").show();
		JQ("#reset_button").val("Reset");
		JQ(".upfile_ultimo").remove();
		JQ(".upfile").remove();
		JQ(".upfile_label").remove();
		JQ("#upload_button").show();
		JQ("#upload_slots_container").hide();

		UberUpload.addUploadSlot();
	},

	stopDataLoop:function(){
		UberUpload.CPB_loop = false;
		clearInterval(UberUpload.UP_timer);
		clearInterval(UberUpload.BPB_timer);

		if(UberUpload.cedric_progress_bar){
			if(UberUpload.show_current_position){ clearTimeout(UberUpload.CPB_byte_timer); }
			clearTimeout(UberUpload.CPB_status_timer);
		}
	},

	// Reset the progress bar
	resetProgressBar:function(){
		JQ("#progress_bar_container").hide();
		JQ("#upload_stats_container").hide();

		UberUpload.seconds = 0;
		UberUpload.minutes = 0;
		UberUpload.hours = 0;
		UberUpload.get_status_url = '';
		UberUpload.total_upload_size = 0;
		UberUpload.total_kbytes = 0;
		UberUpload.toggle_upload_stats = 0;
		UberUpload.CPB_loop = false;
		UberUpload.CPB_width = 0;
		UberUpload.CPB_bytes = 0;
		UberUpload.CPB_hold = true;
		UberUpload.BPB_width_inc = 0;
		UberUpload.BPB_width_new = 0;
		UberUpload.BPB_width_old = 0;

		JQ("#progress_bar").css("width", "0px");

		if(UberUpload.show_files_uploaded || UberUpload.show_current_position || UberUpload.show_elapsed_time || UberUpload.show_est_time_left || UberUpload.show_est_speed){
			JQ("#upload_stats_toggle").html("[+]");
			//JQ("#upload_stats_toggle").css({ backgroundImage : "url(./images/toggle.png)" });
		}

		if(UberUpload.show_percent_complete){ JQ("#percent_complete").html("0%"); }
		if(UberUpload.show_files_uploaded){ JQ("#files_uploaded").html("0"); }
		if(UberUpload.show_files_uploaded){ JQ("#total_uploads").html("0"); }
		if(UberUpload.show_current_position){ JQ("#current_position").html("0"); }
		if(UberUpload.show_current_position){ JQ("#total_kbytes").html("0"); }
		if(UberUpload.show_elapsed_time){ JQ("#elapsed_time").html("00:00:00"); }
		if(UberUpload.show_est_time_left){ JQ("#est_time_left").html("00:00:00"); }
		if(UberUpload.show_est_speed){ JQ("#est_speed").html("0"); }
	},

	resetUploadDiv:function(){
		JQ("#upload_container").hide();
		JQ("#upload_container").html("");
	},

	// Initialize the file upload page
	resetFileUploadPage:function(){
		UberUpload.stopDataLoop();
		UberUpload.resetProgressBar();
		UberUpload.resetUploadDiv();

		JQ("#ubr_alert").html("");
		JQ("#reset_button").val("Reset");
		JQ(".upfile_ultimo").remove();
		JQ(".upfile").remove();
		JQ(".upfile_label").remove();
		JQ("#upload_button").show();
		JQ("#upload_slots_container").hide();

		UberUpload.addUploadSlot();
	},

	// Link the upload
	linkUpload:function(){
		if(UberUpload.check_file_name_format || UberUpload.check_allow_extensions_on_client || UberUpload.check_disallow_extensions_on_client || UberUpload.check_duplicate_file_count){ UberUpload.clearFileLabels(); }
		if(UberUpload.checkFileNameFormat()){ return false; }
		if(UberUpload.checkAllowFileExtensions()){ return false; }
		if(UberUpload.checkDisallowFileExtensions()){ return false; }
		if(UberUpload.checkNullFileCount()){ return false; }
		if(UberUpload.checkDuplicateFileCount()){ return false; }

		JQ("#upload_button").hide();

		if(UberUpload.show_files_uploaded){ JQ("#total_uploads").html(JQ('.upfile').length - 1); }

		JQ.getScript(UberUpload.path_to_link_script, function(){});

		return true;
	},

	// Initialize progress bar
	initializeProgressBar:function(upload_id, debug_ajax){
		if(debug_ajax){ UberUpload.showDebugMessage("Initializing Progress Bar: " + UberUpload.path_to_set_progress_script + '?upload_id=' + upload_id); }

		JQ.getScript(UberUpload.path_to_set_progress_script + '?upload_id=' + upload_id, function(){});
	},

	//Submit the upload form
	startUpload:function(upload_id, debug_upload, debug_ajax){
		UberUpload.resetUploadDiv();

		var iframe_name = "upload_iframe_" + upload_id;

		if(debug_ajax){ UberUpload.showDebugMessage("Submitting Upload: " + UberUpload.path_to_upload_script + "?upload_id=" + upload_id); }

		JQ("#upload_container").html("<iframe name='"+iframe_name+"' frameborder='0' width='780' height='200' scrolling='auto'></iframe>");
		JQ("#uu_upload").attr("target", iframe_name);
		JQ("#uu_upload").attr("action", UberUpload.path_to_upload_script + "?upload_id=" + upload_id);
		JQ("#upload_slots_container").fadeOut("fast");
		JQ(".upfile_ultimo").fadeOut("fast");
		JQ("#uu_upload").submit();
		JQ("#reset_button").val("Stop Upload");

		if(!debug_upload){ UberUpload.initializeProgressBar(upload_id, debug_ajax); }
		else{ UberUpload.showAlertMessage("Debug Uploader Detected, Please Wait..."); }
	},

	// Stop the upload
	stopUpload:function(){
		try{ window.stop(); }
		catch(e){
			try{ document.execCommand("Stop"); }
			catch(e2){}
		}

		JQ("#upload_slots_container").fadeIn("fast");
		JQ("#upload_button").show();
		JQ("#reset_button").val("Reset");
	},

	// Get the progress of the upload
	getProgressStatus:function(){
		if(UberUpload.CPB_loop){ JQ.getScript(UberUpload.get_status_url, function(){}); }
	},

	// Make the progress bar smooth
	smoothCedricStatus:function(){
		if(UberUpload.CPB_width < UberUpload.progress_bar_width && !UberUpload.CPB_hold){
			UberUpload.CPB_width++;
			JQ("#progress_bar").css("width", UberUpload.CPB_width + "px");
		}

		if(UberUpload.CPB_loop){
			clearTimeout(UberUpload.CPB_status_timer);
			UberUpload.CPB_status_timer = setTimeout("UberUpload.smoothCedricStatus()", UberUpload.CPB_time_width);
		}
	},

	// Make the bytes uploaded smooth
	smoothCedricBytes:function(){
		if(UberUpload.CPB_bytes < UberUpload.total_kbytes && !UberUpload.CPB_hold){
			UberUpload.CPB_bytes++;
			JQ("#current_position").html(UberUpload.CPB_bytes);
		}

		if(UberUpload.CPB_loop){
			clearTimeout(UberUpload.CPB_byte_timer);
			UberUpload.CPB_byte_timer = setTimeout("UberUpload.smoothCedricBytes()", UberUpload.CPB_time_bytes);
		}
	},

	//Start the progress bar
	startProgressBar:function(upload_id, upload_size, start_time){
		UberUpload.total_upload_size = upload_size;
		UberUpload.total_kbytes = Math.round(UberUpload.total_upload_size / 1024);
		UberUpload.CPB_loop = true;

		JQ("#progress_bar_container").fadeIn("fast");
		UberUpload.showAlertMessage("Upload In Progress");

		if(UberUpload.show_current_position){ JQ("#total_kbytes").html(UberUpload.total_kbytes + " "); }
		if(UberUpload.show_elapsed_time){ UberUpload.UP_timer = setInterval("UberUpload.getElapsedTime()", 1000); }

		UberUpload.get_status_url = UberUpload.path_to_get_progress_script + "?upload_id=" + upload_id + "&start_time=" + start_time + "&total_upload_size=" + UberUpload.total_upload_size;
		UberUpload.getProgressStatus();

		if(UberUpload.cedric_progress_bar){
			if(UberUpload.show_current_position){ UberUpload.smoothCedricBytes(); }
			UberUpload.smoothCedricStatus();
		}
	},

	// Calculate and display upload information
	setProgressStatus:function(total_bytes_read, files_uploaded, current_file, bytes_read, lapsed_time){
		var byte_speed = 0;
		var time_remaining = 0;

		if(lapsed_time > 0){ byte_speed = total_bytes_read / lapsed_time; }
		if(byte_speed > 0){ time_remaining = Math.round((UberUpload.total_upload_size - total_bytes_read) / byte_speed); }

		if(UberUpload.cedric_progress_bar === 1){
			if(byte_speed !== 0){
				var temp_CPB_time_width = Math.round(UberUpload.total_upload_size * 1000 / (byte_speed * UberUpload.progress_bar_width));
				var temp_CPB_time_bytes = Math.round(1024000 / byte_speed);

				if(temp_CPB_time_width < 5001){ UberUpload.CPB_time_width = temp_CPB_time_width; }
				if(temp_CPB_time_bytes < 5001){ UberUpload.CPB_time_bytes = temp_CPB_time_bytes; }
			}
			else{
				UberUpload.CPB_time_width = 500;
				UberUpload.CPB_time_bytes = 15;
			}
		}

		// Calculate percent_complete finished
		var percent_complete = Math.floor(100 * parseInt(total_bytes_read, 10) / parseInt(UberUpload.total_upload_size, 10));
		var progress_bar_status = Math.floor(UberUpload.progress_bar_width * (parseInt(total_bytes_read, 10) / parseInt(UberUpload.total_upload_size, 10)));

		// Calculate time remaining
		var remaining_sec = (time_remaining % 60);
		var remaining_min = (((time_remaining - remaining_sec) % 3600) / 60);
		var remaining_hours = ((((time_remaining - remaining_sec) - (remaining_min * 60)) % 86400) / 3600);

		if(remaining_sec < 10){ remaining_sec = "0" + remaining_sec; }
		if(remaining_min < 10){ remaining_min = "0" + remaining_min; }
		if(remaining_hours < 10){ remaining_hours = "0" + remaining_hours; }

		var est_time_left = remaining_hours + ":" + remaining_min + ":" + remaining_sec;
		var est_speed = Math.round(byte_speed / 1024);
		var current_position = Math.round(total_bytes_read / 1024);

		if(UberUpload.cedric_progress_bar === 1){
			if(UberUpload.cedric_hold_to_sync){
				if(progress_bar_status < UberUpload.CPB_width){ UberUpload.CPB_hold = true; }
				else{
					UberUpload.CPB_hold = false;
					UberUpload.CPB_width = progress_bar_status;
					UberUpload.CPB_bytes = current_position;
				}
			}
			else{
				UberUpload.CPB_hold = false;
				UberUpload.CPB_width = progress_bar_status;
				UberUpload.CPB_bytes = current_position;
			}

			JQ("#progress_bar").css("width", progress_bar_status + "px");
		}
		else if(UberUpload.bucket_progress_bar === 1){
			UberUpload.BPB_width_old = UberUpload.BPB_width_new;
			UberUpload.BPB_width_new = progress_bar_status;

			if((UberUpload.BPB_width_inc < UberUpload.BPB_width_old) && (UberUpload.BPB_width_new > UberUpload.BPB_width_old)){ UberUpload.BPB_width_inc = UberUpload.BPB_width_old; }

			clearInterval(UberUpload.BPB_timer);
			UberUpload.BPB_timer = setInterval("UberUpload.incrementProgressBar()", 10);
		}
		else{ JQ("#progress_bar").css("width", progress_bar_status + "px"); }

		if(UberUpload.show_current_position){ JQ("#current_position").html(current_position); }
		if(UberUpload.show_current_file){ JQ("#current_file").html(current_file); }
		if(UberUpload.show_percent_complete){ JQ("#percent_complete").html(percent_complete + "%"); }
		if(UberUpload.show_files_uploaded){ if(files_uploaded > 0){ JQ("#files_uploaded").html(files_uploaded); } }
		if(UberUpload.show_est_time_left){ JQ("#est_time_left").html(est_time_left); }
		if(UberUpload.show_est_speed){ JQ("#est_speed").html(est_speed); }
	},

	incrementProgressBar:function(){
		if(UberUpload.BPB_width_inc < UberUpload.BPB_width_new){
			UberUpload.BPB_width_inc++;
			JQ("#progress_bar").css("width", UberUpload.BPB_width_inc + "px");
		}
	},

	// Calculate the time spent uploading
	getElapsedTime:function(){
		UberUpload.seconds++;

		if(UberUpload.seconds === 60){
			UberUpload.seconds = 0;
			UberUpload.minutes++;
		}

		if(UberUpload.minutes === 60){
			UberUpload.minutes = 0;
			UberUpload.hours++;
		}

		var hr = "" + ((UberUpload.hours < 10) ? "0" : "") + UberUpload.hours;
		var min = "" + ((UberUpload.minutes < 10) ? "0" : "") + UberUpload.minutes;
		var sec = "" + ((UberUpload.seconds < 10) ? "0" : "") + UberUpload.seconds;

		JQ("#elapsed_time").html(hr + ":" + min + ":" + sec);
	},

	// Add one upload slot
	addUploadSlot:function(){
		if(JQ(".upfile_ultimo").val() !== ""){
			if(JQ(".upfile").length < UberUpload.max_upload_slots + 1){
				if(JQ(".upfile").length > 0){
					JQ(".upfile_ultimo").hide();
					JQ("#upload_slots_container").show();
					JQ("#upload_slots_container").append('<div class="upfile_label" id="' + JQ(".upfile_ultimo").attr("id") +'_label"><span class="upfile_name">' + UberUpload.getFileName(JQ(".upfile_ultimo").val()) + '</span><span class="upfile_remove" title="Remove File" onClick="UberUpload.deleteUploadSlot(\'' + JQ(".upfile_ultimo").attr("id") + '\')">[x]</span></div>');
					//JQ("#upload_slots_container").append('<div class="upfile_label" id="' + JQ(".upfile_ultimo").attr("id") +'_label"><span class="upfile_name">' + UberUpload.getFileName(JQ(".upfile_ultimo").val()) + '</span><span class="upfile_remove" title="Remove File" onClick="UberUpload.deleteUploadSlot(\'' + JQ(".upfile_ultimo").attr("id") + '\')"></span></div>');
				}

				var id = new Date().getTime();

				JQ(".upfile_ultimo").removeClass("upfile_ultimo");
				JQ("#file_picker_container").prepend('<input type="file" class="upfile upfile_ultimo" name="upfile_' + id + '" id="upfile_' + id + '" size="35" value="">');
				JQ("#upfile_" + id).bind("keypress", function(e){ if(e === 13){ return false; } });
				JQ("#upfile_" + id).bind("change", function(e){ UberUpload.addUploadSlot(); });

				if(JQ(".upfile").length > UberUpload.max_upload_slots){ JQ(".upfile_ultimo").fadeOut("fast"); }
			}
		}
	},

	deleteUploadSlot:function(id){
		JQ("#"+id).remove();
		JQ("#"+id+'_label').remove();

		if(JQ(".upfile").length <= UberUpload.max_upload_slots){ JQ(".upfile_ultimo").fadeIn("fast"); }
		if(JQ(".upfile").length === 1){ JQ("#upload_slots_container").hide(); }
	},

	toggleUploadStats:function(){
		if(UberUpload.toggle_upload_stats){
			if(UberUpload.show_files_uploaded || UberUpload.show_current_position || UberUpload.show_elapsed_time || UberUpload.show_est_time_left || UberUpload.show_est_speed){
				JQ("#upload_stats_toggle").html("[+]");
				//JQ("#upload_stats_toggle").css({ backgroundImage : "url(./images/toggle.png)" });
			}

			JQ("#upload_stats_container").slideUp("fast");
			UberUpload.toggle_upload_stats = 0;
		}
		else{
			if(UberUpload.show_files_uploaded || UberUpload.show_current_position || UberUpload.show_elapsed_time || UberUpload.show_est_time_left || UberUpload.show_est_speed){
				JQ("#upload_stats_toggle").html("[-]");
				//JQ("#upload_stats_toggle").css({ backgroundImage : "url(./images/toggle_collapse.png)" });
			}

			JQ("#upload_stats_container").slideDown("fast");
			UberUpload.toggle_upload_stats = 1;
		}
	}
};