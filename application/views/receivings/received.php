<table id="received_receivings_table">
	<tr>
    	<th><?php echo $this->lang->line('recvs_ID'); ?></th>
		<th><?php echo $this->lang->line('recvs_inv_no'); ?></th>
		<th><?php echo $this->lang->line('recvs_inv_date'); ?></th>
		<th><?php echo $this->lang->line('recvs_inv_supplier'); ?></th>
		<th><?php echo $this->lang->line('sales_unsuspend_and_delete'); ?></th>
	</tr>
	<?php
	foreach ($received_receivings as $received_receiving)
	{
	?>
		<tr>
			<td><?php echo $received_receiving['receiving_id'];?></td>
            <td><?php echo $received_receiving['invoice_number'];?></td>
			<td><?php echo date('m/d/Y',strtotime($received_receiving['receiving_time']));?></td>
			<td>
				<?php
				if (isset($received_receiving['supplier_id']))
				{
					$supplier = $this->Supplier->get_info($received_receiving['supplier_id']);
					echo $supplier->first_name. ' '. $supplier->last_name;
				}
				else
				{
				?>
					<?php
				  
                 echo $received_receiving['comment'];
				}
				?> 
			</td>
			<td>
				<?php 
				echo form_open('receivings/recvsinv');
				echo form_hidden('received_receiving_id', $received_receiving['receiving_id']);
				?>
				<input type="submit" name="submit" value="<?php echo $this->lang->line('recvs_received'); ?>" id="submit" class="submit_button float_right"></td>
				</form>
		</tr>
	<?php
	}
	
	?>
	
</table>