<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends CI_Controller{	
	//to show error message from form validation
	public $invoice_error_messages = array(
							"invoice_name" => "Enter invoice name",	
							"invoice_items" => array(
									"item_name" => "Enter item name",
									"item_no" => "Enter # items",												
									"item_price" => "Enter price"			
								),
							"invoice_tax" => "Enter tax",
							"numeric_only" => "Numeric only"
						);
	//to display error message at view page according to the filtering method
	public $invoice_display_error_messages = array(
							"error" => 0,
							"invoice_name" => "",	
							"invoice_items" => array(),
							"invoice_tax" => ""
						);
	public $item_display_error_messages = array(
											"item_name" => "",
											"item_no" => "",
											"item_price" => "",
										);
	//no error condition
	public $error = 0;
	//to get total of items
	public $invoice_items = 1;	
	//to collect all input data to insert into database
	public $invoice_data = array(
								"invoice_no" => 1,
								"invoice_name" => '',
								"invoice_items" => array(),
								"invoice_sub_total" => 0,
								"invoice_tax" => 0,
								"invoice_total" => 0
							);	
	//to collect invoice items' detail
	public $invoice_items_detail = array(
										"invoice_id" => 0,
										"item_name" => "",
										"item_no" => 0,
										"item_price" => 0,
										"item_total" => 0
									);
	//messages for inserting and updating
	public $invoice_sucess_messages = array(
										"insert" => "A new invoice was successfully inserted.",
										"update" => "An invoice was successfully updated.",										
										"delete" => "An invoice was successfully deleted."										
									);
	//messages for inserting and updating
	public $invoice_fail_messages = array(
										"insert" => "Something was wrong during inserting.",
										"update" => "Something was wrong during updating.",									
										"delete" => "Something was wrong during deleting."								
									);
	
	
	public $insert_id = 0;		
	public $update_id = 0;		
	public $invoices_list = array(
								"invoice_id" => "",
								"invoice_name" => "",
								"invoice_items_no" => "",
								"invoice_total" => ""
							);
	/*
	* constructor method
	*/
	public function __construct(){
           parent::__construct();
		   $this->load->library('session');
		   $this->load->helper('form');
		   $this->load->helper('url');
		   $this->load->model('InvoiceModel'); // automatically call the model of invoice 		   
		   $this->load->library('pagination');
    }        
	
	
	/*
	 * function for invoice list
	*/
	public function index(){
		//get invoices list 
		$search_str = '';
		if(!empty($this->input->post('search',TRUE))){
			$search_str = $this->input->post('search',TRUE);
		}		
		$data['invoices'] = array();
		if(!empty($this->InvoiceModel->getInvoices(0,0,0,$search_str))){					
			//pagination settings
			$config['base_url'] = base_url("/invoice/index");
			$config['total_rows'] = count($this->InvoiceModel->getInvoices());
			$config['per_page'] = 10;
			$config["uri_segment"] = 3;
			$choice = $config["total_rows"] / $config["per_page"];
			$config["num_links"] = floor($choice);
			//config for bootstrap pagination class integration
			$config['full_tag_open'] = '<ul class="pagination">';
			$config['full_tag_close'] = '</ul>';
			$config['first_link'] = false;
			$config['last_link'] = false;
			$config['first_tag_open'] = '<li>';
			$config['first_tag_close'] = '</li>';
			$config['prev_link'] = '&laquo';
			$config['prev_tag_open'] = '<li class="prev">';
			$config['prev_tag_close'] = '</li>';
			$config['next_link'] = '&raquo';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			$config['last_tag_open'] = '<li>';
			$config['last_tag_close'] = '</li>';
			$config['cur_tag_open'] = '<li class="active"><a href="#">';
			$config['cur_tag_close'] = '</a></li>';
			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';
			$this->pagination->initialize($config);
			$data['page'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
			//call the model function to get the department data        
			$data['invoices'] = $this->InvoiceModel->getInvoices(0,$config["per_page"], $data['page'],$search_str);	
			$data['pagination'] = $this->pagination->create_links();
		}
		//pass all invoices list to view
		$this->load->view('index',$data);
	}
	
	
	/*
	 * function for removing an invoice
	*/
	public function removeInvoice(){		
		//get and check invoice name
		$invoice_id = $this->input->post('invoice_id',TRUE);		
		if(!empty($invoice_id)){
			//search id from database
			if(!empty($this->InvoiceModel->getInvoiceId($invoice_id))){
				//delete invoice from database
				if($this->InvoiceModel->deleteInvoiceId($invoice_id) == true){
					$this->session->set_flashdata('info_message', $this->invoice_sucess_messages['delete']);					
				}else{
					$this->session->set_flashdata('info_message', $this->invoice_fail_messages['delete']);					
				}
			}else{
				$this->session->set_flashdata('info_message', $this->invoice_fail_messages['delete']);				
			}
		}else{
			$this->session->set_flashdata('info_message', $this->invoice_fail_messages['delete']);			
		}
		redirect('/');
		
	}
	
	/*
	 * function for calling a new form for a new invoice 
	*/
	public function newInvoice(){		
		$this->load->view('new');
	}
	
	/*
	 * function for inserting a new invoice
	*/
	public function insertInvoice(){		
		$this->_allValidations();		
		//redirect to new form when there is validation error	
		if($this->error == 1){
			$this->session->set_flashdata('message', $this->invoice_display_error_messages);
			$this->session->set_flashdata('data', $this->invoice_data);
			redirect('/newInvoice');

		}else{ //insert into database when the is no validation error			
			if($this->_insertInvoice() == true){
				$this->session->set_flashdata('info_message', $this->invoice_sucess_messages['insert']);
			}else{
				$this->session->set_flashdata('info_message', $this->invoice_fail_messages['insert']);
			}
			redirect('/');
		}
		
	}
	
	/*
	 * function for updating an old invoice 
	*/
	public function editInvoice(){	
		if(!empty($this->input->post('invoice_id',TRUE)) && !empty($this->InvoiceModel->getInvoiceId($this->input->post('invoice_id',TRUE)))){			
			foreach($this->InvoiceModel->getInvoices($this->input->post('invoice_id',TRUE)) as $invoice){
				$this->invoice_data['invoice_id'] = $invoice['invoice_id'];
				$this->invoice_data['invoice_no'] = $invoice['invoice_item_no'];
				$this->invoice_data['invoice_name'] = $invoice['invoice_name'];				
				foreach($this->InvoiceModel->getInvoiceItems($this->input->post('invoice_id',TRUE)) as $item){
					$this->invoice_items_detail["invoice_id"] = $item['invoice_id'];		
					$this->invoice_items_detail["item_name"] = $item['item_name'];		
					$this->invoice_items_detail["item_no"] = $item['item_no'];	
					$this->invoice_items_detail["item_price"] = $item['item_price'];				
					$this->invoice_items_detail["item_total"] = $item['item_total'];
					$this->invoice_data['invoice_items'][] = $this->invoice_items_detail;
				}				
				$this->invoice_data['invoice_sub_total'] = $invoice['invoice_sub_total'];
				$this->invoice_data['invoice_tax'] = $invoice['invoice_tax'];
				$this->invoice_data['invoice_total'] = $invoice['invoice_total'];
			}
			//$data['message'] = $this->invoice_display_error_messages;
			//$data['data'] = $this->invoice_data;
			$this->session->set_flashdata('message', $this->invoice_display_error_messages);
			$this->session->set_flashdata('data', $this->invoice_data);
			$this->load->view('edit');
		}else{
			redirect('/');
		}
		
	}
	
	
	/*
	 * function for updating a new invoice
	*/
	public function updateInvoice(){
		$this->update_id = $this->input->post('invoice_id',TRUE);
		$this->invoice_data['invoice_id'] = $this->update_id;
		$this->invoice_data['item_no'] = $this->input->post('invoice_items',TRUE);
		$this->_allValidations();		
		//redirect to new form when there is validation error	
		if($this->error == 1){
			$this->session->set_flashdata('message', $this->invoice_display_error_messages);
			$this->session->set_flashdata('data', $this->invoice_data);
			$this->load->view('edit');
		}else{ //insert into database when the is no validation error	
			if($this->update_id == 0){
				$this->session->set_flashdata('info_message', $this->invoice_fail_messages['update']);
			}else{
				$this->invoice_data['invoice_id'] = $this->update_id;
				if($this->_updateInvoice() == true){
					$this->session->set_flashdata('info_message', $this->invoice_sucess_messages['update']);
				}else{
					$this->session->set_flashdata('info_message', $this->invoice_fail_messages['update']);
				}
			}
			redirect('/');
		}
		
	}
	
	//to make all validations
	function _allValidations(){
		//get and check invoice name
		$this->_invoiceValidation('invoice_name',$this->input->post('invoice_name',TRUE),0);		
		$this->invoice_data['invoice_name'] = $this->input->post('invoice_name',TRUE);
		//get no of items to know total of items included
		$this->invoice_items = $this->input->post('invoice_items',TRUE);		
		//get all items
		for($i=0;$i<$this->invoice_items;$i++){
			//get and check invoice tax	
			$input_item_name = '';
			$input_item_no = 0;
			$input_item_price = 0;
			if(!empty($this->input->post('invoice_items_name',TRUE)[$i])){
				$input_item_name = $this->input->post('invoice_items_name',TRUE)[$i];
			}	
			if(!empty($this->input->post('invoice_items_no',TRUE)[$i])){
				$input_item_no = $this->input->post('invoice_items_no',TRUE)[$i];
			}
			if(!empty($this->input->post('invoice_items_price',TRUE)[$i])){
				$input_item_price = $this->input->post('invoice_items_price',TRUE)[$i];
			}			
			$this->_invoiceItemsValidation($input_item_name,$input_item_no,$input_item_price);	
			//passing all input into array and calculate all items total, invoice subtotal and invoice total
			$this->_calculation($input_item_name,$input_item_no,$input_item_price);
		}		
		//get and check invoice tax
		$this->_invoiceValidation('invoice_tax',$this->input->post('invoice_tax',TRUE),1);
		$this->invoice_data['invoice_no'] = $this->invoice_items;
		$this->invoice_data['invoice_tax'] = $this->input->post('invoice_tax',TRUE);
		$this->invoice_data['invoice_total'] = $this->invoice_data['invoice_sub_total'] + $this->invoice_data['invoice_tax'];
		//to check easily where or not error in form validation
		$this->invoice_display_error_messages['error'] = $this->error;
	}
	
	/*
	 * function for checking validation of invoice for is not empty and numeric only
	*/
	function _invoiceValidation($input_name = '',$input_value = '',$numeric = 0){		
		//check empty or not
		if($this->_validInput($input_value,$numeric) == 1){
			$this->error = 1;
			$this->invoice_display_error_messages[$input_name]= $this->invoice_error_messages[$input_name];
		}
		//check empty or not
		if($this->_validInput($input_value,$numeric) == 2){
			$this->error = 1;
			$this->invoice_display_error_messages[$input_name]= $this->invoice_error_messages['numeric_only'];
		}		
		
	}
	
	/*
	 * function for checking validation of items for is not empty and numeric only
	*/
	function _invoiceItemsValidation($item_name = "" ,$item_no = 0,$item_price = 0){		
		//validate for item name
		if($this->_validInput($item_name,0) != 0){
			$this->error = 1;
			$this->item_display_error_messages['item_name'] = $this->invoice_error_messages['invoice_items']['item_name'];			
		}
		//validate for item no
		if($this->_validInput($item_no,1) != 0){
			$this->error = 1;
			if($this->_validInput($item_no,1) == 1){
				$this->item_display_error_messages['item_no'] = $this->invoice_error_messages['invoice_items']['item_no'];
			}
			if($this->_validInput($item_no,1) == 2){
				$this->item_display_error_messages['item_no'] = $this->invoice_error_messages['numeric_only'];
			}
		}
		//validate for item price
		if($this->_validInput($item_price,1) != 0){
			$this->error = 1;
			if($this->_validInput($item_price,1) == 1){
				$this->item_display_error_messages['item_price'] = $this->invoice_error_messages['invoice_items']['item_price'];
			}
			if($this->_validInput($item_price,1) == 2){
				$this->item_display_error_messages['item_price'] = $this->invoice_error_messages['numeric_only'];
			}
		}
		//check and insert into items's error array		
		$this->invoice_display_error_messages['invoice_items'][] = $this->item_display_error_messages;
		
	}
	
	/*
	 * function for checking validation of items for is not empty and numeric only
	*/
	function _validInput($input_value = '',$numeric = 0){		
		//check empty or not and is numeric, return 1 when the input is empty, return 2 when the input is not empty and not numeric, return 0 if the value is not empty or both not empty and numeric
		if(empty($input_value)){
			return 1;			
		}else{
			if($numeric == 1 && is_numeric($input_value) == false){
				return 2;				
			}else{
				return 0;				
			}
		}
		
	}
	
	/*
	 * function for calculation for all items to get item's total, invoice's subtotal and invoice's total
	*/
	function _calculation($item = '',$no = 0,$price = 0){		
		$this->invoice_items_detail["invoice_id"] = 0;	
		if($this->update_id > 0){
			$this->invoice_items_detail["invoice_id"] = $this->update_id;
		}
		$this->invoice_items_detail["item_name"] = $item;		
		$this->invoice_items_detail["item_no"] = $no;		
		$this->invoice_items_detail["item_price"] = $price;				
		$this->invoice_items_detail["item_total"] = ($no*$price);	
		$this->invoice_data['invoice_sub_total'] = $this->invoice_data['invoice_sub_total'] + $this->invoice_items_detail["item_total"];		
		$this->invoice_data['invoice_items'][] = $this->invoice_items_detail;
		
	}
	
	/*
	 * function for inserting into database
	*/
	function _insertInvoice(){
		//insert into invoices_list table
		$this->insert_id = $this->InvoiceModel->insertInvoice($this->invoice_data);
		if(!empty($this->insert_id)){			
			//to add invoice_id into invoice_items_details array
			$array = range(0,$this->invoice_data['invoice_no'] - 1);
			array_walk($array, function ($value){				
				$this->invoice_data['invoice_items'][$value]['invoice_id'] = $this->insert_id;
			});
			//insert into invoice items 
			if($this->InvoiceModel->insertInvoiceItems($this->invoice_data['invoice_items']) == true){
				$this->session->set_flashdata('info_message', $this->invoice_sucess_messages['insert']);
				redirect('/');
			}else{
				$this->session->set_flashdata('info_message', $this->invoice_fail_messages['insert']);
				redirect('/');
			}
		}else{
			$this->session->set_flashdata('info_message', $this->invoice_fail_messages['insert']);
			redirect('/');
		}
								
	}
	
	/*
	 * function for updating into database
	*/
	function _updateInvoice(){
		//update into invoices_list table		
		if($this->InvoiceModel->updateInvoice($this->invoice_data) == true){			
			//to update invoice_id into invoice items			
			$array = range(0,$this->invoice_data['item_no'] - 1);
			array_walk($array, function ($value){				
				$this->invoice_data['invoice_items'][$value]['invoice_id'] = $this->invoice_data['invoice_id'];
			});			
			//update into invoice items 
			if($this->InvoiceModel->updateInvoiceItems($this->invoice_data['invoice_items'],$this->invoice_data['invoice_id']) == true){
				$this->session->set_flashdata('info_message', $this->invoice_sucess_messages['update']);
				redirect('/');
			}else{
				$this->session->set_flashdata('info_message', $this->invoice_fail_messages['update']);
				redirect('/');
			}
		}else{
			$this->session->set_flashdata('info_message', $this->invoice_fail_messages['update']);
			redirect('/');
		}
								
	}
	
	
	
}

?>