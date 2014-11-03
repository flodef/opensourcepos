<?php $this->load->view("partial/header"); ?>
<?php
if (isset($error_message))
{
	echo '<h1 style="text-align: center;">'.$error_message.'</h1>';
	exit;
}
?>
<div id="receipt_wrapper">
	<div id="receipt_header">
		<div id="company_name"><?php echo $this->config->item('company'); ?></div>
		<div id="company_address"><?php echo nl2br($this->config->item('address')); ?></div>
		<div id="company_phone"><?php echo $this->config->item('phone'); ?></div>
		<div id="sale_receipt" style="font-weight:bold;text-align:center;font-size:16px;"><?php echo $receipt_title; ?></div>
		<div id="sale_time"><?php // echo $transaction_time ?></div>
	</div>

<div id="receipt_general_info">

<table id="receipt_emp_info">
<?php if(isset($customer))
		{
		?>	
    <tr>
    <th style="width:40%;text-align:left;"><?php echo $this->lang->line('customers_customer').": ".$customer;?></th>
    <th style="width:5%;text-align:center;"><?php // echo $this->lang->line('sales_quantity'); ?></th>
	<th style="width:5%;text-align:center;"><?php // echo $this->lang->line('sales_discount'); ?></th>
	<th style="width:50%;text-align:right;"><?php echo $transaction_time; ?></th>
	</tr>
    
    <?php
		}
	else
	{
		?>
    <tr>
    <th style="width:40%;text-align:left;"><?php //echo $this->lang->line('customers_customer').": ".$customer;?></th>
    <th style="width:5%;text-align:center;"><?php // echo $this->lang->line('sales_quantity'); ?></th>
	<th style="width:5%;text-align:center;"><?php // echo $this->lang->line('sales_discount'); ?></th>
	<th style="width:50%;text-align:right;"><?php echo $transaction_time; ?></th>
	</tr>
   		<?php
		}
		?>
	 <tr>
	<th style="width:40%;text-align:left;"><?php echo $this->lang->line('sales_id')." : ".$sale_id;  ?></th>
    <th style="width:5%;text-align:center;"><?php // echo $this->lang->line('sales_quantity'); ?></th>
	<th style="width:5%;text-align:center;"><?php // echo $this->lang->line('sales_discount'); ?></th>
	<th style="width:50%;text-align:right;"><?php echo $this->lang->line('employees_employee').": ".$employee; ?></th>
	</tr>
    </table>
    </div>
    
   	<table id="receipt_items">
	<tr>
	<th style="width:40%;text-align:center;"><?php echo $this->lang->line('items_item'); ?></th>
	<th style="width:17%;text-align:center;"><?php echo $this->lang->line('common_price'); ?></th>
	<th style="width:16%;text-align:center;"><?php echo $this->lang->line('sales_quantity'); ?></th>
	<th style="width:27%;text-align:center;"><?php echo $this->lang->line('sales_total'); ?></th>
	</tr>
    
	<?php
	foreach(array_reverse($cart, true) as $line=>$item)
	{
	?>        
        <tr>
		<td style='text-align:left;'><span class='long_name'><?php echo $item['name']; ?></span><span class='short_name'><?php echo character_limiter($item['name'],15); ?></span></td>
		<td style='text-align:right;'><?php echo to_currency($item['price']); ?></td>
		<td style='text-align:center;'><?php echo $item['quantity']; ?></td>
		<td style='text-align:right;'><?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100); ?></td>
		</tr>

	    <tr>
        <td style='text-align:right;'><?php echo $item['serialnumber']; ?></td>
        <td style='text-align:right;'><?php echo '&nbsp;'; ?></td>
        <td style='text-align:center;'><?php echo $item['description']; ?></td>
		<td style='text-align:right;'><?php echo '&nbsp;'; ?></td>
	    </tr>

	<?php
	}
	?>	
    
    <table id="receipt_total_info" >
	<tr>
    <td><?php //echo $item['serialnumber']; ?></td>
    <td style='text-align:left;'><?php //echo $item['serialnumber']; ?></td>
    <td style='text-align:right;'><?php echo $this->lang->line('sales_sub_total'); ?></td>
	<td style='text-align:right;'><?php echo to_currency($total); ?></td>
	</tr>
    
    <?php foreach($taxes as $name=>$value) { ?>
		<tr>
        	<td><?php //echo $item['serialnumber']; ?></td>
    		<td><?php //echo $item['serialnumber']; ?></td>
    		<td style='text-align:right;'><?php echo $name; ?>:</td>
			<td style='text-align:right;'><?php echo to_currency($value); ?></td>
		</tr>
	<?php }; ?>


		<tr>
        	<td><?php //echo $item['serialnumber']; ?></td>
    		<td><?php //echo $item['serialnumber']; ?></td>
    		<td style='text-align:right;'><?php echo $this->lang->line('sales_total'); ?></td>
			<td style='text-align:right;'><?php echo to_currency($total); ?></td>
		</tr>
    <tr><td colspan="6">&nbsp;</td></tr>

	<?php
		foreach($payments as $payment_id=>$payment)
	{ ?>
    
    	<tr>
        	<td><?php //echo $item['serialnumber']; ?></td>
    		<td><?php //echo $this->lang->line('sales_payment'); ?></td>
    		<td style='text-align:right;'><?php echo $this->lang->line('sales_payment'); ?> &nbsp;   &nbsp;
			<?php $splitpayment=explode(':',$payment['payment_type']); echo $splitpayment[0]; ?></td>
			<td style='text-align:right;'><?php echo to_currency( $payment['payment_amount'] * 1 ); ?></td>
		</tr>
        
     
	<?php
	}
	?>