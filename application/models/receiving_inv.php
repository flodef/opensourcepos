<?php 
class Receiving_inv extends CI_Model
{
	function get_all()
	{
		$this->db->from('receivings');
		$this->db->order_by('receiving_id');
		return $this->db->get();
	}
	
	public function get_info($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id',$receiving_id);
		return $this->db->get();
	}
	
	/*	function get_invoice_count()
	{
		$this->db->from('receivings');
		$this->db->where('invoice_number is not null');
		return $this->db->count_all_results();
	}
	
	function get_receiving_inv_by_invoice_number($invoice_number)
	{
		$this->db->from('receivings');
		$this->db->where('invoice_number', $invoice_number);
		return $this->db->get();
	}*/

	function exists($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id',$receiving_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}
	
	function update($receiving_data, $receiving_id)
	{
		$this->db->where('receiving_id', $receiving_id);
		$success = $this->db->update('receivings',$receiving_data);
		
		return $success;
	}
	
	function save ($items,$supplier_id,$employee_id,$comment,$payment_type,$invoice_number,$receiving_id=false)
	{
		if(count($items)==0)
			return -1;

		$receivings_data = array(
		'supplier_id'=> $this->Supplier->exists($supplier_id) ? $supplier_id : null,
		'employee_id'=>$employee_id,
		'comment'=>$comment,
		'payment_type'=>$payment_type,
		'invoice_number'=>$invoice_number,
		
		);

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->insert('receivings',$receivings_data);
		$receiving_id = $this->db->insert_id();


		foreach($items as $line=>$item)
		{
			$cur_item_info = $this->Item->get_info($item['item_id']);

			$receivings_items_data = array
			(
				'receiving_id'=>$receiving_id,
				'item_id'=>$item['item_id'],
				'line'=>$item['line'],
				'description'=>$item['description'],
				'serialnumber'=>$item['serialnumber'],
				'quantity_purchased'=>$item['quantity'],
				'discount_percent'=>$item['discount'],
				'item_cost_price' => $cur_item_info->cost_price,
				'item_unit_price'=>$item['price'],
				'invoice_number'=>$invoice_number,
				'item_location'=>$item['item_location']
			);

			$this->db->insert('receivings_items',$receivings_items_data);

			//Update stock quantity
			$item_quantity = $this->Item_quantities->get_item_quantity($item['item_id'], $item['item_location']);		
            $this->Item_quantities->save(array('quantity'=>$item_quantity->quantity + $item['quantity'],
                                              'item_id'=>$item['item_id'],
                                              'location_id'=>$item['item_location']), $item['item_id'], $item['item_location']);
			
			$qty_recv = $item['quantity'];
			$recv_remarks ='RECV '.$receiving_id;
			$inv_data = array
			(
				'trans_date'=>date('Y-m-d H:i:s'),
				'trans_items'=>$item['item_id'],
				'trans_user'=>$employee_id,
				'trans_comment'=>$recv_remarks,
				'trans_inventory'=>$qty_recv
			);
			$this->Inventory->insert($inv_data);

			$supplier = $this->Supplier->get_info($supplier_id);
		}
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
			return -1;
		}

		return $receiving_id;
	}
	
	function delete($receiving_id)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		
		$this->db->delete('receivings_items', array('receiving_id' => $receiving_id)); 
		$this->db->delete('receivings', array('receiving_id' => $receiving_id)); 
		
		$this->db->trans_complete();
				
		return $this->db->trans_status();
	}
	
	function get_invoice_number($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id',$receiving_id);
		return $this->db->get()->row()->invoice_number;
	}

	function get_receiving_items($receiving_id)
	{
		$this->db->from('receivings_items');
		$this->db->where('receiving_id',$receiving_id);
		return $this->db->get();
	}
	
	function get_supplier($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id',$receiving_id);
		return $this->Supplier->get_info($this->db->get()->row()->supplier_id);
	}
	
	function get_comment($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id',$receiving_id);
		return $this->db->get()->row()->comment;
	}
}
?>
