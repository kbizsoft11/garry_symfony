import './styles/admin.scss';
import 'eonasdan-bootstrap-datetimepicker';
import 'typeahead.js';
import Bloodhound from "bloodhound-js";
import 'bootstrap-tagsinput';

$(function() {
    // Datetime picker initialization.
    // See https://eonasdan.github.io/bootstrap-datetimepicker/
    $('[data-toggle="datetimepicker"]').datetimepicker({
        icons: {
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-chevron-up',
            down: 'fa fa-chevron-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-check-circle-o',
            clear: 'fa fa-trash',
            close: 'fa fa-remove'
        }
    });
	
	
	// code for the admin card start
	var modal = document.getElementById("modal_card_post");
	var btn = document.getElementById("post_addNewCard");
	var span = document.getElementsByClassName("close")[0];

	btn.onclick = function() {
		
		modal.style.display = "block";
		$('.card_paragraph,.card_post_template,#card_post_template,#card_post_cardTitle,.card_image').val('');
	}
 
	span.onclick = function() {
		modal.style.display = "none";
	}
	var para_count = 2;
	$(".add_paragraph").click(function(){
		var html_append = '<div class="form-group form_group_'+para_count+'">'; 
		html_append += '<label class="control-label required" for="post_title">Paragraph '+para_count+'</label>'; 
		html_append += '<div class="col-sm-11 cus_par"><input type="text" name="paragraph[]" required="required" autofocus="autofocus" class="form-control card_paragraph" /></div>';
		html_append += '<div class="col-sm-1"><span class="add-delete-buttons"><a href="javascript:void(0);" class="add-post-para-delete btn btn-danger btn-sm btn_para_'+para_count+'" para_id = "'+para_count+'"> <i class="fas fa-times-circle"></i></a></span></div>';
		html_append += '</div>'; 
		$(".other_paragraph").append(html_append);
		para_count++;
	});
    var cart_content_count = 1;
	
	function response_img_data(imgresponse,cart_content_count){
		var card_paragraph = $('.card_paragraph').val();
		var card_post_template = $('#card_post_template').val();
		var gettitle = $('#card_post_cardTitle').val();
		var card_post_cardImage = $('.card_image').val();
		if(gettitle=='' || card_paragraph=='' || card_post_template==''){
			$('#modal_card_post .error').text("Please fill all fields data");
			return false;
		}
		
		console.log(cart_content_count);
		var card_paradata = [];

		$('.card_paragraph').each(function() {		
			var card_paragraph = $(this).val(); 
			card_paradata.push(card_paragraph);
		});
		
		var para_data = card_paradata.toString();
		console.log('kushal response.image_url'+imgresponse);
		var html_append = '<div class="form-group">';  
		html_append += '<input type="hidden" class="control-label required card_data_count" name="card_data" value='+cart_content_count+'>'; 
		html_append += '<input type="hidden" class="control-label required" name="card_title'+cart_content_count+'" value="'+gettitle+'">';
		html_append += '<input type="hidden" class="control-label required" name="card_image'+cart_content_count+'" value="'+imgresponse+'">'; 
		html_append += '<input type="hidden" class="control-label required" name="card_paragraph'+cart_content_count+'" value="'+para_data+'">'; 
		html_append += '<input type="hidden" class="control-label required" name="card_template'+cart_content_count+'" value="'+card_post_template+'">';
		html_append += '<input type="hidden" class="control-label required" name="key_item[]" value="'+cart_content_count+'">';
		html_append += '</div>';
		html_append += '<div class="col-sm-4">';
		html_append += '<div class="inner-card-data">';
		html_append += '<div class="cars-box-img"><img height="200" src="'+imgresponse+'" class="img-rounded" alt="'+gettitle+'"></div>';
		html_append += '<div class="card-box-content">';
		html_append += '<p>'+card_paragraph+'</p>';
		html_append += '</div>';
		html_append += '<div class="row"><span class="col-sm-12"><p class="ad_layout">Ad Layout: '+card_post_template+'</p></span>';
		html_append += '</div>';
		html_append += '</div>';
		
		html_append += '</div>';
		var card_post_template = $('.card_data_count').val(cart_content_count);
		$(".card_content_add").append(html_append);
		
		
		$(".close").trigger('click');
	}
	
	$(document).on('click', '.add_card_post', function(){ 
	
		
		
		var fd = new FormData();
		//var file_data = $('#card_post_cardImage').prop('files')[0];
		var files = $('#card_post_cardImage')[0].files;
		
		var file_data = $('#card_post_cardImage').prop('files')[0]; 
		console.log(file_data);
		//if(file_data==''){
		if(typeof(file_data)  === "undefined") {
			$('#modal_card_post .error').show();
			console.log('false');
			$('#modal_card_post .error').text("Please add correct image");
			return false;
		}
		console.log('true');
		if(files.length > 0 ){			
				var filename = $('input[type=file]').val().replace(/.*(\/|\\)/, '');
				var output = filename.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
				
				var card_post_cardImage = $('#card_post_cardImage').val('');
			    var image_url = window.location.origin+'/images/'+filename;
				var imgresponse = ''
               fd.append('file',file_data);
               $.ajax({
                    url:'/en/admin/post/card_image',
                    type:'post',
                    data:fd,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success:function(response){
						imgresponse = response.image_url;
						response_img_data(imgresponse,cart_content_count)                        
                    }
               });
          }
		  
			cart_content_count++;
		
		
	});

// When the user clicks anywhere outside of the modal, close it
	window.onclick = function(event) {
	if (event.target == modal) {
		modal.style.display = "none";
	}
	}

    // Bootstrap-tagsinput initialization
    // https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/
    var $input = $('input[data-toggle="tagsinput"]');
    if ($input.length) {
        var source = new Bloodhound({
            local: $input.data('tags'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            datumTokenizer: Bloodhound.tokenizers.whitespace
        });
        source.initialize();

        $input.tagsinput({
            trimValue: true,
            focusClass: 'focus',
            typeaheadjs: {
                name: 'tags',
                source: source.ttAdapter()
            }
        });
    }
});

// Handling the modal confirmation message.
$(document).on('submit', 'form[data-confirmation]', function (event) {
    var $form = $(this),
        $confirm = $('#confirmationModal');

    if ($confirm.data('result') !== 'yes') {
        //cancel submit event
        event.preventDefault();

        $confirm
            .off('click', '#btnYes')
            .on('click', '#btnYes', function () {
                $confirm.data('result', 'yes');
                $form.find('input[type="submit"]').attr('disabled', 'disabled');
                $form.submit();
            })
            .modal('show');
    }
});

// Handling the modal confirmation message.
$(document).on('submit', 'form[data-confirmation]', function (event) {
  var $form = $(this),
    $confirm = $('#confirmationModal');
  if ($confirm.data('result') !== 'yes') {
    //cancel submit event
    event.preventDefault();
    $confirm.off('click', '#btnYes').on('click', '#btnYes', function () {
      $confirm.data('result', 'yes');
      $form.find('input[type="submit"]').attr('disabled', 'disabled');
      $form.submit();
    }).modal('show');
  }
});
//save post old code when click on show post
/*$(document).on('click', '#sidebar .btn-success', function (event) {
	event.preventDefault();
	var post_id = $("#post_id_n").val();
	var link = $(this).attr('href');    
	console.log(link);
	var card_iddata = [];
	$('#sortable .inner-card-data').each(function() {		
		var card_id = $(this).attr('card_id'); 
		card_iddata.push(card_id);
	});
	

	var postForm = { //Fetch form data
		'post_id'     : post_id,'card_ids'     : card_iddata
	};

	$.ajax({ //Process the form using $.ajax()
		type      : 'POST', //Method type
		url       : '/en/admin/post/'+post_id+'/editpostcardposition', //Your form processing file URL
		data      : postForm, //Forms name
		dataType  : 'json',
		success   : function(data) {
						
					}
	});
	window.location.href=link;
	
});*/


		

//updated code when click on save
$(document).on('click', '.post_submit_btn', function (event) {
	event.preventDefault();
	var post_id = $("#post_id_n").val();
	var link = $(this).attr('href');    
	console.log(link);
	var card_iddata = [];
	$('#sortable .inner-card-data').each(function() {		
		var card_id = $(this).attr('card_id'); 
		card_iddata.push(card_id);
	});
	

	var postForm = { //Fetch form data
		'post_id'     : post_id,'card_ids'     : card_iddata
	};

	$.ajax({ //Process the form using $.ajax()
		type      : 'POST', //Method type
		url       : '/en/admin/post/'+post_id+'/editpostcardposition', //Your form processing file URL
		data      : postForm, //Forms name
		dataType  : 'json',
		success   : function(data) {
						
					}
	});
	$(".post_form").submit();
	//window.location.href=link;
	
});


//delete paragraph for add
$(document).on('click', '.add-post-para-delete', function (event) {
	event.preventDefault();
	var para_id = $(this).attr('para_id');	
	$(".form_group_"+para_id).remove();
	
});

//delete card para
$(document).on('click', '.delete-post-para', function (event) {
	event.preventDefault();
	if(confirm("Do you want to delete?")) {
		var card_id = $(this).attr('data-post-card-id');	
		var para_id = $(this).attr('para_id');    
		$(".loader_"+para_id).show();
		$(".btn_para_"+para_id).hide();
		var postForm = { //Fetch form data
			'card_id'     : card_id,'para_id'     : para_id
		};
		
		$.ajax({ //Process the form using $.ajax()
			type      : 'POST', //Method type
			url       : '/en/admin/post/'+card_id+'/delete_card_para', //Your form processing file URL
			data      : postForm, //Forms name
			dataType  : 'json',
			success   : function(data) {
				console.log(data);	
							if(data.message=="success"){
								$(".loader_"+para_id).hide();
								$(".btn_para_"+para_id).show();
								$(".data_"+para_id).remove();
							}
						}
		});
		$(".loader_"+para_id).hide();
		$(".btn_para_"+para_id).show();
		//location.reload();
	}		
});

//delete card allpara
$(document).on('click', '.delete-all-para', function (event) {
	event.preventDefault();
	if(confirm("Do you want to delete?")) {
		
		var card_id = $(this).attr('data-post-card-id');	
		
		var postForm = { //Fetch form data
			'card_id'     : card_id
		};
		console.log("xcv");
		$.ajax({ //Process the form using $.ajax()
			type      : 'POST', //Method type
			url       : '/en/admin/post/'+card_id+'/delete_card_allpara', //Your form processing file URL
			data      : postForm, //Forms name
			dataType  : 'json',
			success   : function(data) {
				console.log(data);	
							if(data.message=="success"){
								$(".data_"+para_id).remove();
							}
						}
		});
		location.reload();
	}		
});

//update image field card data

$(document).on('click', '.existing_img_update', function (event) {
	jQuery(".update_image").slideToggle();
	console.log("sdfsd");
});
$(document).on('click', '.delete-update-img', function (event) {
	jQuery(".update_image").hide();
});

$(document).on('click', '.user_delete', function (event) {
	if(!confirm("Do you want to delete user?")) {
		return false;
	}
});
