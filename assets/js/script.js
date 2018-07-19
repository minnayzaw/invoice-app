$(document).ready(function(){
	//function for clicking add item button to get one pair of new item
	$("#add_item_btn").on("click",function(e){		
		//check input validation to avoid the blank from existing fields before appending new item
		if(itemsValidation() == true){
			//add a new item 
			newItem();	
		}
	}); 
	
	//function for clicking create button
	$("#create_btn,#update_btn").on("click",function(e){		
		//check input validation to avoid the blank for invoice name and tax		
		invoiceValidation();	
		if($.isEmptyObject($(".error").html()) == false){
			return false;
		}
	});

	//to check the value is empty or not from user input
	$(document).on("keyup keydown", ".input_value",function(e){		
		if($(this).val() != ""){
			$("#"+$(this).attr("id")).removeClass("error_border");			
			$(this).next("span").remove();
		}  
	});	
	
	//to allow numeric value and greater than 0
	$(document).on("keyup keydown change", ".number_value",function(e){		 
		if($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 || (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||  (e.keyCode >= 35 && e.keyCode <= 40)){				
			return;
		}			
		if((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)){
			e.preventDefault();
		}
		if(parseFloat($(this).val()) == 0){
			$(this).val("");
			e.preventDefault();
		}  
		totalCalculation();
	});	
	
	//to remove an item
	$(document).on("click", ".remove",function(e){
		e.preventDefault();
		$("#"+$(this).attr("id").split('_')[1]).remove();
		totalCalculation();
		var new_item_count = parseInt($("#invoice_items").val()) - 1;
		$("#invoice_items").val(new_item_count);
	});	
	
	//function for validation of the blank fields
	function fieldsValidation(id,v_name,v_error){
		var error = true;
		if($(v_name+id).val() == ""){			
			error = false;			
			$(v_name+id).addClass(" error_border");	
			if(!$(v_name+id).next("span")[0]){
				$(v_error).insertAfter(v_name+id);				
			}			
			
		}		
		return error;
	}
	
	//function for validation input fields
	function itemsValidation(){
		var error = true;
		var validation_name = ["#item_name_","#item_no_","#item_price_"];
		var validation_error = ["<span class=\"error\">Enter item name.</span>","<span class=\"error\">Enter # items.</span>","<span class=\"error\">Enter price.</span>"];
		//to get all rows of items
		$(".item_data_row").each(function(){
			//validation for item name, item no and item price	
			for(var i=0; i < validation_name.length; i++){
				if(error != fieldsValidation($(this).attr("id"),validation_name[i],validation_error[i])){
					error = fieldsValidation($(this).attr("id"),validation_name[i],validation_error[i]);
				}				
			}			
		});			
		return error;
	}
	
	//function for validation the whole fields of a new form
	function invoiceValidation(){
		//validation of invoice name
		fieldsValidation("","#invoice_name","<span class=\"error\">Enter invoice name.</span>");
		//check input validation for all items
		itemsValidation();
		//validation of tax
		fieldsValidation("","#invoice_tax","<span class=\"error\">Enter tax.</span>");
	}
	
	//function for calculation of all items to get total 
	function totalCalculation(){
		var invoice_sub_total = 0, invoice_tax = 0, invoice_total = 0;
		$(".item_data_row").each(function(){			
			//to get each item's total with item no and price	
			var item_total = 0, item_no = 0, item_price = 0;			
			item_no = $("#item_no_"+$(this).attr("id")).val();		
			item_price = $("#item_price_"+$(this).attr("id")).val();
			item_total = item_no * item_price;
			invoice_sub_total = parseFloat(invoice_sub_total) + parseFloat(item_total);
			$("#item_total_"+$(this).attr("id")).html(item_total);	
		});		
		$("#invoice_sub_total").html(invoice_sub_total);
		invoice_total = parseFloat(invoice_total) + parseFloat(invoice_sub_total);		
		invoice_tax = $("#invoice_tax").val();
		if(invoice_tax > 0){
			invoice_total = parseFloat(invoice_total) + parseFloat(invoice_tax);
		}
		$("#invoice_total").html(invoice_total);
		
	}
	
	//function for add a new item
	function newItem(){
		var last_id = 0, new_id = 0;
		$(".item_data_row").each(function(){			
			//to get the last id of item row
			last_id = $(this).attr("id");
		});
		var new_id = parseInt(last_id) + 1;
		var new_item = "<div class=\"row item_row item_data_row\" id=\""+new_id+"\"><div class=\"col-2\"><input type=\"text\" class=\"form-control input_value\" name=\"invoice_items_name[]\" id=\"item_name_"+new_id+"\" /></div><div class=\"col-1\"><input type=\"text\" class=\"form-control input_value number_value\" name=\"invoice_items_no[]\" id=\"item_no_"+new_id+"\" /></div><div class=\"col-1\"><input type=\"text\" class=\"form-control input_value number_value\" name=\"invoice_items_price[]\" id=\"item_price_"+new_id+"\" /></div><div class=\"col-1\"><span id=\"item_total_"+new_id+"\">0</span></div><div class=\"col-1\"><a href=\"#\" class=\"remove\" id=\"remove_"+new_id+"\"><i class=\"fa fa-trash-o fa-2x\" aria-hidden=\"true\"></i></a></div></div>";
		$(new_item).insertAfter("#"+last_id);	
		var new_item_count = parseInt($("#invoice_items").val()) + 1;
		$("#invoice_items").val(new_item_count);
	}
	
	//to delete invoice list
	$(document).on("click", ".remove_invoice",function(e){
		e.preventDefault();
		$("#invoice_id").val($(this).attr("id"));
		 $("#invoice_form").attr("action", "removeInvoice");
		$("#invoice_form").submit();
	});
	
	//to update invoice list
	$(document).on("click", ".update_invoice",function(e){
		e.preventDefault();
		$("#invoice_id").val($(this).attr("id"));	
		 $("#invoice_form").attr("action", "editInvoice");
		$("#invoice_form").submit();
	});
	
	
}); 

