// JavaScript Document
var ni_product_id = 0;
var ni_product_name = '';
var ni_sku = '';
var ni_sale_price = 0;
var ni_regular_price = 0;
var ni_stock_status = 'instock';
var ni_stock_qty = 0;
var ni_backorders='no';
var ni_manage_stock = 'no';
var ni_product_data = {};

jQuery(function($){
	$(".ni-success-msg").hide();
	//alert(niwpe_pe_ajax_object.niwpe_pe_ajax_object_ajaxurl);
	//$( "#frm_niwpe_product_editor" ).submit(function( e ) {
	$(document).on('submit','#frm_niwpe_product_editor, form#frm_niwpe_product_editor_pagination',  function(e){
		$.ajax({
			url:niwpe_pe_ajax_object.niwpe_pe_ajax_object_ajaxurl,
			data: $("#frm_niwpe_product_editor").serialize(),
			success:function(response) {
				//alert(JSON.stringify(response));
				$("._ajax_niwpe_content").html(response);
			},
			error: function(response){
				console.log(response);
				//alert("e");
			}
		}); 
		e.preventDefault();
	});
	
	$( "#frm_niwpe_product_editor" ).trigger( "submit" );
	/*Pagination*/
	$(document).on('click', "ul.niwpe_pagination a",function(){
		
		var p = $(this).attr("data-page");
		//alert(p);
		
		$("#frm_niwpe_product_editor").find("input[name=p]").val(p);
		
		$("#frm_niwpe_product_editor" ).submit();
		
		return false;
	});
	
	/*Button Click*/
	$(document).on('click','._niwpe_product_update',  function(e){
		//alert("1");
		//alert($(this).attr("data-product-id"));	
		product_id = $(this).attr("data-product-id");
		ni_product_name  = $(this).parent().parent().find('._product_name').val();
		ni_sku 			 = $(this).parent().parent().find('._sku').val();
		ni_stock_status  = $(this).parent().parent().find('._stock_status').val();
		ni_stock_qty  	 = $(this).parent().parent().find('._stock').val();
		ni_backorders  	 = $(this).parent().parent().find('._backorders').val();
		ni_manage_stock  = $(this).parent().parent().find('._manage_stock').val();
		ni_sale_price  	 = $(this).parent().parent().find('._sale_price').val();
		ni_regular_price = $(this).parent().parent().find('._regular_price').val();
		
		 ni_product_data = {
			'action'		  : 'niwpe_product_editor',
			'sub_action'	  : 'niwpe_product_update',
			'product_id'	  : product_id,
			'ni_product_name' : ni_product_name,
			'ni_sku'		  : ni_sku,
			'ni_stock_status' : ni_stock_status,
			'ni_stock_qty'	  : ni_stock_qty,
			'ni_backorders'	  : ni_backorders,
			'ni_manage_stock' : ni_manage_stock,
			'ni_sale_price'	  : ni_sale_price,
			'ni_regular_price': ni_regular_price,
		};
		$.ajax({
			url:niwpe_pe_ajax_object.niwpe_pe_ajax_object_ajaxurl,
			data: ni_product_data,
			success:function(response) {
				//alert("success");
				//alert(JSON.stringify(response));
				//$("._ajax_niwpe_content").html(response);
				//$(".ni-success-msg").show();
				if(response=="Done"){
					//$("._ajax_niwpe_save").html("Record updated successfully").delay(5000).fadeOut('slow');
					//$("body").scrollTop();
					alert("Record updated successfully");
				}else{
					//$("._ajax_niwpe_save").html(response).delay(5000).fadeOut('slow');
					alert("Record not updated successfully");
				}
				
			},
			error: function(response){
				alert("error");
				alert(JSON.stringify(response));
				console.log(response);
				//alert("e");
			}
		}); 
		
		
		
		e.preventDefault();	 
	});
	
});