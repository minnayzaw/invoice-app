<?php $this->load->view('header'); ?>
		<!-- Div for new form -->
		<div class="new_invoice_box">
			<h3>New Invoice</h3>
			<!-- New form -->			
			<?php echo form_open(base_url('insertInvoice' ),array( 'id' => '', 'class' => '' ));?>
					<input type="hidden" name="invoice_items" id="invoice_items" value="<?php if(!empty($this->session->flashdata('data')['invoice_no'])){ echo $this->session->flashdata('data')['invoice_no']; }else{ echo 1; } ?>" />
					<!-- when there is error from form validation -->
					<?php if(!empty($this->session->flashdata('message')['error']) && $this->session->flashdata('message')['error'] == 1){ ?>
					<div class="row">						
						<div class="col-2">							
							<label for="invoice_name">Invoice Name</label>
						</div>						
						<div class="col-2">							
							<input type="text" class="form-control input_value <?php if(!empty($this->session->flashdata('message')['invoice_name'])){ echo 'error_border'; } ?>" name="invoice_name" id="invoice_name" value="<?php if(!empty($this->session->flashdata('data')['invoice_name'])){ echo $this->session->flashdata('data')['invoice_name']; } ?>" />	
							<?php if(!empty($this->session->flashdata('message')['invoice_name'])){ ?>
							<span class="error">Enter invoice name.</span>
							<?php } ?>
						</div>											
					</div>					
					<div class="row">						
						<div class="col-2">							
							<label for="item_name">Item Name</label>							
						</div>						
						<div class="col-1">						
							<label for="no_items"># of items</label>							
						</div>						
						<div class="col-1">	
							<label for="price">Price</label>													
						</div>						
						<div class="col-1">	
							<label for="price">Total</label>													
						</div>
						<div class="col-1">								
						</div>						
					</div>
					<?php for($i=0;$i<count($this->session->flashdata('message')['invoice_items']);$i++){ $j = $i + 1;?>					
					<div class="row item_row item_data_row" id="<?php echo $j; ?>">						
						<div class="col-2">														
							<input type="text" class="form-control input_value <?php if(!empty($this->session->flashdata('message')['invoice_items'][$i]['item_name'])){ echo 'error_border'; } ?>" name="invoice_items_name[]" id="item_name_<?php echo $j; ?>" value="<?php if(!empty($this->session->flashdata('data')['invoice_items'][$i]['item_name'])){ echo $this->session->flashdata('data')['invoice_items'][$i]['item_name']; } ?>" />	
							<?php if(!empty($this->session->flashdata('message')['invoice_items'][$i]['item_name'])){ ?>
							<span class="error">Enter item name.</span>
							<?php } ?>
						</div>						
						<div class="col-1">													
							<input type="text" class="form-control input_value number_value <?php if(!empty($this->session->flashdata('message')['invoice_items'][$i]['item_no'])){ echo 'error_border'; } ?>" name="invoice_items_no[]" id="item_no_<?php echo $j; ?>" value="<?php if(!empty($this->session->flashdata('data')['invoice_items'][$i]['item_no'])){ echo $this->session->flashdata('data')['invoice_items'][$i]['item_no']; } ?>" />	
							<?php if(!empty($this->session->flashdata('message')['invoice_items'][$i]['item_no'])){ ?>
							<span class="error">Enter # items.</span>
							<?php } ?>													
						</div>						
						<div class="col-1">								
							<input type="text" class="form-control input_value number_value <?php if(!empty($this->session->flashdata('message')['invoice_items'][$i]['item_price'])){ echo 'error_border'; } ?>" name="invoice_items_price[]" id="item_price_<?php echo $j; ?>" value="<?php if(!empty($this->session->flashdata('data')['invoice_items'][$i]['item_price'])){ echo $this->session->flashdata('data')['invoice_items'][$i]['item_price']; } ?>" />	
							<?php if(!empty($this->session->flashdata('message')['invoice_items'][$i]['item_price'])){ ?>
							<span class="error">Enter price.</span>
							<?php } ?>	
						</div>						
						<div class="col-1">								
							<span id="item_total_<?php echo $j; ?>"><?php if(!empty($this->session->flashdata('data')['invoice_items'][$i]['item_total'])){ echo $this->session->flashdata('data')['invoice_items'][$i]['item_total']; }else{ echo 0; } ?></span>
						</div>	
						<?php if($j > 1){ ?>
							<div class="col-1">
								<a href="#" class="remove" id="remove_<?php echo $j; ?>"><i class="fa fa-trash-o fa-2x" aria-hidden="true"></i></a>
							</div>
						<?php } ?>
					</div>	
					<?php } ?>					
					<div class="row">						
						<div class="col">														
							<button type="button" class="btn btn-primary btn-md" id="add_item_btn"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add Item</button>
						</div>																
					</div>
					<div class="row item_row">						
						<div class="col-2">																					
						</div>						
						<div class="col-1">																				
						</div>						
						<div class="col-1">								
							<label for="invoice_sub_total">Sub Total</label>							
						</div>						
						<div class="col-1">								
							<span id="invoice_sub_total"><?php if(!empty($this->session->flashdata('data')['invoice_sub_total'])){ echo $this->session->flashdata('data')['invoice_sub_total']; }else{ echo 0; } ?></span>
						</div>				
					</div>
					<div class="row item_row">						
						<div class="col-2">																					
						</div>						
						<div class="col-1">																				
						</div>						
						<div class="col-1">								
							<label for="invoice_tax">Tax</label>							
						</div>						
						<div class="col-1">																
							<input type="text" class="form-control input_value number_value <?php if(!empty($this->session->flashdata('message')['invoice_tax'])){ echo 'error_border'; } ?>" name="invoice_tax" id="invoice_tax" value="<?php if(!empty($this->session->flashdata('data')['invoice_tax'])){ echo $this->session->flashdata('data')['invoice_tax']; } ?>" />	
							<?php if(!empty($this->session->flashdata('message')['invoice_tax'])){ ?>
							<span class="error">Enter tax.</span>
							<?php } ?>
						</div>				
					</div>	
					<div class="row item_row">						
						<div class="col-2">																					
						</div>						
						<div class="col-1">																				
						</div>						
						<div class="col-1">								
							<label for="invoice_total">Total</label>							
						</div>						
						<div class="col-1">								
							<span id="invoice_total"><?php if(!empty($this->session->flashdata('data')['invoice_total'])){ echo $this->session->flashdata('data')['invoice_total']; }else{ echo 0; } ?></span>
						</div>				
					</div>
					<?php }else{ ?>
					<div class="row">						
						<div class="col-2">							
							<label for="invoice_name">Invoice Name</label>
						</div>						
						<div class="col-2">							
							<input type="text" class="form-control input_value" name="invoice_name" id="invoice_name" value="<?php if(!empty($this->session->flashdata('data')['invoice_name'])){ echo $this->session->flashdata('data')['invoice_name']; } ?>" />	
							<?php ?>
						</div>											
					</div>					
					<div class="row">						
						<div class="col-2">							
							<label for="item_name">Item Name</label>							
						</div>						
						<div class="col-1">						
							<label for="no_items"># of items</label>							
						</div>						
						<div class="col-1">	
							<label for="price">Price</label>													
						</div>						
						<div class="col-1">	
							<label for="price">Total</label>													
						</div>
						<div class="col-1">								
						</div>						
					</div>				
					<div class="row item_row item_data_row" id="1">						
						<div class="col-2">														
							<input type="text" class="form-control input_value" name="invoice_items_name[]" id="item_name_1" />	
						</div>						
						<div class="col-1">													
							<input type="text" class="form-control input_value number_value" name="invoice_items_no[]" id="item_no_1" />														
						</div>						
						<div class="col-1">								
							<input type="text" class="form-control input_value number_value" name="invoice_items_price[]" id="item_price_1" />														
						</div>						
						<div class="col-1">								
							<span id="item_total_1">0</span>
						</div>							
					</div>				
					<div class="row">						
						<div class="col">														
							<button type="button" class="btn btn-primary btn-md" id="add_item_btn"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add Item</button>
						</div>																
					</div>
					<div class="row item_row">						
						<div class="col-2">																					
						</div>						
						<div class="col-1">																				
						</div>						
						<div class="col-1">								
							<label for="invoice_sub_total">Sub Total</label>							
						</div>						
						<div class="col-1">								
							<span id="invoice_sub_total">0</span>
						</div>				
					</div>
					<div class="row item_row">						
						<div class="col-2">																					
						</div>						
						<div class="col-1">																				
						</div>						
						<div class="col-1">								
							<label for="invoice_tax">Tax</label>							
						</div>						
						<div class="col-1">								
							<input type="text" class="form-control input_value number_value" name="invoice_tax" id="invoice_tax" value="" />								
						</div>				
					</div>	
					<div class="row item_row">						
						<div class="col-2">																					
						</div>						
						<div class="col-1">																				
						</div>						
						<div class="col-1">								
							<label for="invoice_total">Total</label>							
						</div>						
						<div class="col-1">								
							<span id="invoice_total">0</span>
						</div>				
					</div>					
					<?php } ?>
					<div class="row">						
						<div class="col">														
							<button type="submit" class="btn btn-primary btn-md" id="create_btn">Create</button>
						</div>																
					</div>
					
			<?php echo form_close();?>
			<!-- /New form -->
		</div>
		<!-- /Div for new form -->       
  <?php $this->load->view('footer'); ?>
