<?php

class InvoiceModel extends CI_Model {

        public $invoice_name;
        public $invoice_item_no = 1;
		public $invoice_items = array(
									"invoice_id" => 0,
									"item_name" => "",
									"item_no" => 0,
									"item_price" => 0,
									"item_total" => 0
								);
        public $invoice_sub_total;
        public $invoice_tax;
        public $invoice_total; 
		public $invoice_id = 0;		
		public $tables = array();
		

		public function __construct(){    
			parent::__construct();    
			$this->load->database();			
		}		
		
		
		//get invoices list from invoices_list table
        public function getInvoices($id = 0,$start = 0,$limit = 0, $search = ''){
			$this->db->select('*');			
			if($id > 0){
				$this->db->where('invoice_id', $id);
			}
			if($start > 0 || $limit > 0){
				$this->db->limit($start, $limit);
			}
			if($search != ''){
				$this->db->or_like('invoice_name',$search);
				$this->db->or_like('invoice_item_no',$search);
				$this->db->or_like('invoice_total',$search);
			}
			$this->db->where('delete_flag', 0);
			$query = $this->db->get('invoices_list');
			return $query->result_array();			
        }
		//get invoice items list from invoice_items_list table
        public function getInvoiceItems($id){
			$this->db->select('*');
			$this->db->where('invoice_id', $id);
			$this->db->where('delete_flag', 0);
			$query = $this->db->get('invoice_items_list');
			return $query->result_array();			
        }
		
		//get invoice id
		public function getInvoiceId($id){			
			$query = $this->db->select('invoice_id')->where('invoice_id',$id)->get('invoices_list');			
			if(!empty($query->row())){
				return $query->row()->invoice_id;
			}else{
				return 0;
			}
			
		}
		
		//enable the delete flags with related invoice id for invoices_list and invoice_items_list tables
		public function deleteInvoiceId($id){
			$result = false;
			$this->invoice_id = $id;					
			$this->tables = array("invoices_list","invoice_items_list");
			for($i=0;$i<count($this->tables);$i++){
				$data = array(					
					'delete_flag' => 1
					);
				$this->db->where('invoice_id',$this->invoice_id);
				$result = $this->db->update($this->tables[$i],$data);
			}			
			return $result;			
			
		}

		//inserting into invoices_list table only
        public function insertInvoice($data){				
				//$this->date     = time();
                $this->invoice_name = $data['invoice_name'];                
                $this->invoice_item_no = $data['invoice_no'];                
                $this->invoice_sub_total = $data['invoice_sub_total'];                
                $this->invoice_tax = $data['invoice_tax'];                
                $this->invoice_total = $data['invoice_total'];  
				$this->db->insert('invoices_list', $this);				  
				$insert_id = $this->db->insert_id();
				return $insert_id;
							
        }
		
		//inserting into invoice_items_list table only
        public function insertInvoiceItems($data){ 
				$this->invoice_items = $data;
				//to insert multiple records
                return $this->db->insert_batch('invoice_items_list', $this->invoice_items);
        }
		
		
		//updating into invoices_list table only
        public function updateInvoice($data){				
				//$this->date     = time();
                $this->invoice_id = $data['invoice_id'];                
                $this->invoice_name = $data['invoice_name'];                
                $this->invoice_item_no = $data['invoice_no'];                
                $this->invoice_sub_total = $data['invoice_sub_total'];                
                $this->invoice_tax = $data['invoice_tax'];                
                $this->invoice_total = $data['invoice_total'];  
				$this->db->where('invoice_id',$this->invoice_id);
				return $this->db->update('invoices_list', $this);				  
				
        }
		
		//updating into invoice_items_list table only
        public function updateInvoiceItems($data,$id){ 
				$result = false;
				//clear the items first
				$this->db->where('invoice_id',$id);
				$this->db->delete('invoice_items_list');
				//insert all new items
				$result = $this->db->insert_batch('invoice_items_list', $data);
				return $result;
        }
		
		


}

?>