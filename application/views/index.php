<?php $this->load->view('header'); ?>
		<div id="upper_row">
			<!-- Search Box -->
			<div class="upper_box_left">
			  <!-- Default form grid -->
				<?php echo form_open(base_url('/index' ),array( 'id' => '', 'class' => '' ));?>
					<!-- Grid row -->
					<div class="row">
						<!-- Grid column -->
						<div class="col">
							<!-- input type for search box -->
							<input type="text" class="form-control" name="search" id="search_box" />							
						</div>
						<!-- /Grid column -->
						<!-- Grid column -->
						<div class="col">
							<!-- submit button for search box -->
							<button type="submit" class="btn btn-primary btn-md" id="search_btn">Search</button>						
						</div>
						<!-- /Grid column -->
					</div>
					<!-- /Grid row -->
				<?php echo form_close();?>
				<!-- /Default form grid -->
			</div>
			<!-- /Search Box -->
			<!-- Add new button -->
			<div class="upper_box_right">
					<!-- Grid row -->
					<div class="row">
						<!-- Grid column -->
						<div class="col">
							<!-- search box -->
							<a class="btn btn-primary btn-md" id="add_btn" href="<?php echo base_url(); ?>newInvoice"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add Invoice</a>
						</div>
						<!-- /Grid column -->
					</div>
					<!-- /Grid row -->
			</div>
			<!-- /Add new button -->
			<div class="clr"></div>
		</div>		
		<?php if(!empty($this->session->flashdata('info_message'))){ ?>	
		<div class="d-flex p-2"><?php echo $this->session->flashdata('info_message'); ?></div>
		<?php } ?>
        <!--Table-->
		<table class="table table-striped table-bordered">
			<!--Table head-->
			<thead class="grey">
				<tr>
					<th>Invoice Name</th>
					<th>#of Items</th>
					<th>Total</th>
					<th></th>
				</tr>
			</thead>
			<!--Table head-->
			<?php if(!empty($invoices)){foreach($invoices as $invoice){ ?>
			<!--Table body-->
			<tbody>
				<tr>
					<th scope="row"><a id="<?php echo $invoice['invoice_id']; ?>" class="update_invoice" href="#"><?php echo $invoice['invoice_name']; ?></a></th>
					<td><?php echo $invoice['invoice_item_no']; ?></td>
					<td><?php echo $invoice['invoice_total']; ?></td>
					<td><a id="<?php echo $invoice['invoice_id']; ?>" class="remove_invoice" href="#">Remove</a></td>
				</tr>											
			</tbody>
			<!--Table body-->
			<?php } } ?>			
		</table>
		<?php if(!empty($pagination)){echo $pagination;} ?>
		<?php echo form_open(base_url('#' ),array( 'id' => 'invoice_form', 'class' => '' ));?>
		<input type="hidden" name="invoice_id" id="invoice_id" />
		<?php echo form_close();?>
		<!--Table-->        
  <?php $this->load->view('footer'); ?>
