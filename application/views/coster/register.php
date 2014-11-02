<?php $this->load->view("partial/header"); ?>
<div id="page_title" style="margin-bottom:8px;"><?php echo $this->lang->line('item_costing'); ?></div>
<?php
if(isset($error))
{
	echo "<div class='error_message'>".$error."</div>";
}

if (isset($warning))
{
	echo "<div class='warning_mesage'>".$warning."</div>";
}

if (isset($success))
{
	echo "<div class='success_message'>".$success."</div>";
}
?>

<?php
// Coster display
?>

<div id="register_wrapper">
<?php echo form_open("coster/change_mode",array('id'=>'mode_form')); ?>
	<?php if (count($stock_locations) > 1): ?>
<span><?php echo $this->lang->line('sales_stock_location') ?></span>
<?php echo form_dropdown('stock_location',$stock_locations,$stock_location,'onchange="$(\'#mode_form\').submit();"'); ?>
<?php endif; ?>
   
    <div id="show_suspended_sales_button_coster">
     
    <?php
	{
	?>
	<span><?php echo $this->lang->line('coster_trans_no') ?></span>
    <?php echo form_input(array('name'=>'search_trans_no','id'=>'search_trans_no','value'=>$trans_no,'size'=>10));?>
    <?php
	}
	?>
</div>
    
	</form>
<?php echo form_open("coster/add",array('id'=>'add_item_form')); ?>
<label id="item_label" for="item">

<?php
if($mode=='sale_retail' or $mode=='sale_wholesale')
{
	echo $this->lang->line('sales_find_or_scan_item');
}
else
{
	echo $this->lang->line('sales_find_or_scan_item_or_receipt');
}
?>
</label>

<?php echo form_input(array('name'=>'item','id'=>'item','size'=>'40'));?>
<!-- no need the new item button in sale page
<div id="new_item_button_register" >
		<?php echo anchor("items/view/-1/width:360",
		"<div class='small_button'><span>".$this->lang->line('sales_new_item')."</span></div>",
		array('class'=>'thickbox none','title'=>$this->lang->line('sales_new_item')));
		?>
	</div>
-->
<div id="show_suspended_sales_button">
	<?php
	// This part conntrols if there are Items already in the sale.
	if(count($cart)==0)
	{
	?>
    <?php echo anchor("coster/suspended/width:425","<div class='small_button'><span style='font-size:90%;'>".$this->lang->line('sales_suspended_sales')."</span></div>",
	array('class'=>'thickbox none','title'=>$this->lang->line('sales_suspended_sales')));
	 }
	else
	{	// This part conntrols if there are suspended Items already in the cart.
		echo "<div class='small_button' id='new_order_button'><span style='font-size:90%;'>".$this->lang->line('sales_suspended_sale_order')."</span></div>";
	?>
     <?php
		}
		?>  
</div>
	</form>
<table id="register">
<thead>
<tr>
<th style="width:11%;"><?php echo $this->lang->line('common_delete'); ?></th>
<th style="width:15%;"><?php echo $this->lang->line('sales_item_number'); ?></th>
<th style="width:35%;"><?php echo $this->lang->line('sales_item_name'); ?></th>
<th style="width:14%;"><?php echo $this->lang->line('sales_price'); ?></th>
<th style="width:8%;"><?php echo $this->lang->line('sales_quantity'); ?></th>
<th style="width:35%;"><?php echo $this->lang->line('sales_total'); ?></th>
<th style="width:11%;"><?php echo $this->lang->line('sales_edit'); ?></th>
</tr>
</thead>
<tbody id="cart_contents">

<?php
if(count($cart)==0)
{
?>
<tr><td colspan='8'>
<div class='warning_message' style='padding:7px;'><?php echo $this->lang->line('sales_no_items_in_cart'); ?></div>
</tr></tr>

<?php
}
else
{	
			
			
	foreach(array_reverse($cart, true) as $line=>$item)
	{
		$cur_item_info = $this->Item->get_info($item['item_id']);
		echo form_open("coster/edit_item/$line");
	?>
		<tr>
		<td><?php echo anchor("coster/delete_item/$line",'['.$this->lang->line('common_delete').']');?></td>
		<td><?php echo $item['item_number']; ?></td>
		<td style="align: center;"><?php echo $item['name']; ?><br /> [<?php echo $item['in_stock'] ?> in <?php echo $item['stock_name']; ?>]
		<?php echo form_hidden('location', $item['item_location']); ?>
		</td>

		<?php if (!$items_module_allowed)
		{
		?>		
        
   		<td><?php echo form_input(array('align'=>'center','name'=>'price','value'=>$item['price'],'size'=>'6'));?></td>
		<?php
		}
		else
		{
		?>
			<td style="align:right;"><?php echo $item['price']; ?></td>
			<?php echo form_hidden('price',$item['price']); ?>
		<?php
		}
		?>

		<td style="align:right;">
		<?php
        	if($item['is_serialized']==1)
        	{
        		echo $item['quantity'];
        		echo form_hidden('quantity',$item['quantity']);
        	}
        	else
        	{
        		echo form_input(array('name'=>'quantity','value'=>$item['quantity'],'size'=>'2'));
        	}
		?>
		</td>

		<td><?php echo to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100); ?></td>
		<td><?php echo form_submit("edit_item", $this->lang->line('sales_edit_item'));?></td>
		</tr>
		<tr>
		<td style="color:#2F4F4F";><?php echo $this->lang->line('sales_description_abbrv').':';?></td>
		<td colspan=2 style="text-align:left;">

		<?php
        	if($item['allow_alt_description']==1)
        	{
        		echo form_input(array('name'=>'description','value'=>$item['description'],'size'=>'20'));
        	}
        	else
        	{
				if ($item['description']!='')
				{
					echo $item['description'];
        			echo form_hidden('description',$item['description']);
        		}
        		else
        		{
        		    echo $this->lang->line('sales_no_description');
           			echo form_hidden('description','');
        		}
        	}
		?>
		</td>
		<td>&nbsp;</td>
		<td style="color:#2F4F4F";>
		<?php
        	if($item['is_serialized']==1)
        	{
				echo $this->lang->line('sales_serial').':';
			}
		?>
		</td>
		<td colspan=3 style="text-align:left;">
		<?php
        	if($item['is_serialized']==1)
        	{
        		echo form_input(array('name'=>'serialnumber','value'=>$item['serialnumber'],'size'=>'20'));
			}
			else
			{
				echo form_hidden('serialnumber', '');
			}
		?>
		</td>


		</tr>
		<tr style="height:3px">
		<td colspan=8 style="background-color:white"> </td>
		</tr>		</form>
         
	<?php
	}
}
?>
</tbody>
</table>
</div>
<div id="overall_sale"> 
          
	<?php
	if(isset($customer))
	{
		echo $this->lang->line("sales_customer").': <b>'.$customer. '</b><br />';
		echo anchor("coster/remove_customer",'['.$this->lang->line('common_remove').' '.$this->lang->line('customers_customer').']');
	}
	else
	{
		echo form_open("coster/select_customer",array('id'=>'select_customer_form')); ?>
		<label id="customer_label" for="customer"><?php echo $this->lang->line('sales_select_customer'); ?></label>
		<?php echo form_input(array('name'=>'customer','id'=>'customer','size'=>'30','value'=>$this->lang->line('sales_start_typing_customer_name')));?>
		</form>
		<div style="margin-top:5px;text-align:center;">
		<h3 style="margin: 5px 0 5px 0"><?php echo $this->lang->line('common_or'); ?></h3>
		<?php echo anchor("customers/view/-1/width:350",
		"<div class='small_button' style='margin:0 auto;'><span>".$this->lang->line('sales_new_customer')."</span></div>",
		array('class'=>'thickbox none','title'=>$this->lang->line('sales_new_customer')));
		?>
		</div>
		<div class="clearfix">&nbsp;</div>
		<?php
	}
	?>

	<div id='sale_details' style="font-size:16px;">
		<div class="float_left" style="width:55%;"><?php echo $this->lang->line('sales_sub_total'); ?>:</div>
		<div class="float_left" style="width:42%;font-weight:bold;text-align:right;"><?php echo to_currency($subtotal); ?></div>

		<?php foreach($taxes as $name=>$value) { ?>
		<div class="float_left" style='width:55%;'><?php echo $name; ?>:</div>
		<div class="float_left" style="width:42%;font-weight:bold;text-align:right;"><?php echo to_currency($value); ?></div>
		<?php }; ?>

		<div class="float_left" style='width:55%;'><?php echo $this->lang->line('sales_total'); ?>:</div>
		<div class="float_left" style="width:42%;font-weight:bold;text-align:right;"><?php echo to_currency($total); ?></div>
	</div>




	<?php
	// Only show this part if there are Items already in the sale.
	if(count($cart) > 0)
	{
	?>

    	<div id="Cancel_sale">
		<?php echo form_open("coster/cancel_sale",array('id'=>'cancel_sale_form')); ?>
		<div class='small_button' id='cancel_sale_button' style='margin-top:5px; margin:5 auto;'>
			<span><?php echo $this->lang->line('sales_cancel_sale'); ?></span>
		</div>
    	</form>
    	</div>
		<div class="clearfix" style="margin-bottom:1px;">&nbsp;</div>
        
        
        <div id="finish_sale">
				<?php echo form_open("coster/complete",array('id'=>'finish_sale_form')); ?>
				<label id="comment_label" for="comment"><?php echo $this->lang->line('common_comments'); ?>:</label>
				<?php echo form_textarea(array('name'=>'comment', 'id' => 'comment', 'value'=>$comment,'rows'=>'4','cols'=>'28'));?>
	          	<?php echo form_input(array('name'=>'trans_no', 'id' => 'trans_no', 'value'=>$trans_no,'size'=>'31'));?> 
                <br /><br />      
		<?php
		// Only show this part if there is at least one payment entered.
		if(count($amount_due) > 0)
		{
		?>
			<div id="finish_sale">
				<?php echo form_open("coster/complete",array('id'=>'finish_sale_form')); ?>
								
				<?php
				
				if(!empty($customer_email))
				{
					echo $this->lang->line('sales_email_receipt'). ': '. form_checkbox(array(
					    'name'        => 'email_receipt',
					    'id'          => 'email_receipt',
					    'value'       => '1',
					    'checked'     => (boolean)$email_receipt,
					    )).'<br />('.$customer_email.')<br />';
				}
				?>
				</form>
			</div>
   		<?php
		}
		?>

	<table width="97%"><tr>
    <td style="width:55%; "><div class="float_left"><?php echo $this->lang->line('sales_payments_total').':';?></div></td>
    <td style="width:45%;font-weight:bold;text-align:right;"><div class="float_right" style="text-align:right;font-weight:bold;"><?php echo to_currency($payments_total); ?></div></td>
	</tr>
	<tr>
	<td style="width:55%; "><div class="float_left" ><?php echo $this->lang->line('sales_amount_due').':';?></div></td>
	<td style="width:45%;"><div class="float_right" style="text-align:right;font-weight:bold;"><?php echo to_currency($amount_due); ?></div></td>
	</tr></table>

	<div id="Payment_Types" >

		<div style="height:40px;">

			<div class='small_button' id='post_order_button' style='margin:15 auto;'>
				<span><?php echo $this->lang->line('sales_add_payment_coster'); ?></span>
            </div>
		</div>
	</div>
  	<?php
	}
	?>
     
</div>
<div class="clearfix" style="margin-bottom:1px;"></div>

<?php $this->load->view("partial/footer"); ?>

<script type="text/javascript" language="javascript">
$(document).ready(function()
{
	$("#item").autocomplete('<?php echo site_url("coster/item_search"); ?>',
    {
    	minChars:0,
    	max:100,
    	selectFirst: false,
       	delay:10,
    	formatItem: function(row) {
			return row[1];
		}
    });

    $("#item").result(function(event, data, formatted)
    {
		$("#add_item_form").submit();
    });

	$('#item').focus();

	$('#item').blur(function()
    {
    	$(this).attr('value',"<?php echo $this->lang->line('sales_start_typing_item_name'); ?>");
    });

	$('#item,#customer').click(function()
    {
    	$(this).attr('value','');
    });
	
    $("#customer").autocomplete('<?php echo site_url("coster/customer_search"); ?>',
    {
    	minChars:0,
    	delay:10,
    	max:100,
    	formatItem: function(row) {
			return row[1];
		}
    });

    $("#customer").result(function(event, data, formatted)
    {
		$("#select_customer_form").submit();
    });

    $('#customer').blur(function()
    {
    	$(this).attr('value',"<?php echo $this->lang->line('sales_start_typing_customer_name'); ?>");
    });
	
	$('#comment').keyup(function() 
	{
		$.post('<?php echo site_url("coster/set_comment");?>', {comment: $('#comment').val()});
	});
	
	$('#trans_no').keyup(function()
	{
	$.post('<?php echo site_url("coster/set_trans_no");?>', {trans_no: $('#trans_no').val()});
	});
		
	$('#email_receipt').change(function() 
	{
		$.post('<?php echo site_url("coster/set_email_receipt");?>', {email_receipt: $('#email_receipt').is(':checked') ? '1' : '0'});
	});	
	
    $("#finish_sale_button").click(function()
    {
    	if (confirm('<?php echo $this->lang->line("sales_confirm_finish_sale"); ?>'))
    	{
    		$('#finish_sale_form').submit();
    	}
    });

	$("#suspend_sale_button").click(function()
	{
		if (confirm('<?php echo $this->lang->line("sales_confirm_suspend_sale"); ?>'))
    	{
			$('#finish_sale_form').attr('action', '<?php echo site_url("coster/suspend"); ?>');
    		$('#finish_sale_form').submit();
    	}
	});
	
		$("#new_order_button").click(function()
	{
		if (confirm('<?php echo $this->lang->line("sales_confirm_order_sale"); ?>'))
    	{
			$('#finish_sale_form').attr('action', '<?php echo site_url("coster/suspend"); ?>');
    		$('#finish_sale_form').submit();
    	}
	});

	$("#post_order_button").click(function()
	{
		if (confirm('<?php echo $this->lang->line("post_order_confirm").'   '.("Please Confirm Transancation ID:-"."$trans_no"); ?>'
		))
    	{
			$('#finish_sale_form').attr('action', '<?php echo site_url("coster/suspend"); ?>');
    		$('#finish_sale_form').submit();
    	}
	});
	
	//'value'=>$item  .':' 

    $("#cancel_sale_button").click(function()
    {
    	if (confirm('<?php echo $this->lang->line("sales_confirm_cancel_sale"); ?>'))
    	{
    		$('#cancel_sale_form').submit();
    	}
    });

	$("#add_payment_button").click(function()
	{
	   $('#add_payment_form').submit();
    });

	$("#payment_types").change(checkPaymentTypeGiftcard).ready(checkPaymentTypeGiftcard)
});

function post_item_form_submit(response)
{
	if(response.success)
	{
		$("#item").attr("value",response.item_id);
		$("#add_item_form").submit();
	}
}

function post_person_form_submit(response)
{
	if(response.success)
	{
		$("#customer").attr("value",response.person_id);
		$("#select_customer_form").submit();
	}
}

function checkPaymentTypeGiftcard()
{
	if ($("#payment_types").val() == "<?php echo $this->lang->line('sales_giftcard'); ?>")
	{
		$("#amount_tendered_label").html("<?php echo $this->lang->line('sales_giftcard_number'); ?>");
		$("#amount_tendered").val('');
		$("#amount_tendered").focus();
	}
	else
	{
		$("#amount_tendered_label").html("<?php echo $this->lang->line('sales_amount_tendered'); ?>");		
	}
}

</script>