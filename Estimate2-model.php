<?php

class Estimate2 extends CI_Model{

  public function create_form($name, $json, $modified)
	{
		$data = array(
			'name' => $name,
			'json' => $json,
      'modified' => $modified
		);
		$insert = $this->db->insert('forms2', $data);
		$insert_id = $this->db->insert_id();

		if($insert){
			return $insert_id;
		} else {
			return 'false';
		}
	}

  public function create_estimate($email, $json)
  {
    $new_data = array(
      'email' => $email,
      'json' => $json
    );
    $insert = $this->db->insert('estimates2', $new_data);
    $insert_id = $this->db->insert_id();
    return $insert_id;
  }

  public function read_form($id)
  {
    $this->db->where('id', $id);
    $query = $this->db->get('forms2');

    if($query->num_rows() > 0){
      $query = $query->row();
      $row = (array) $query;
      return $row;
    }else{
      // form does not exist
      return false;
    }
  }

  public function read_estimate($estimate_id)
  {
    $this->db->where('id', $estimate_id);
    $query = $this->db->get('estimates2');
    if($query->num_rows() > 0){
      $row = $query->row();
      return (array) $row;
    }else{
      return false;
    }
  }

  public function read_by_email($email)
  {
    $this->db->where('email', $email);
    $this->db->order_by('id', 'DESC');
    $query = $this->db->get('estimates2');

    if($query->num_rows() > 0){
      return $query;
    }else{
      return false;
    }
  }

  public function update_form($id, $name, $json, $modified)
  {
    $data = array(
        'id' => $id,
        'name' => $name,
        'json' => $json,
        'modified' => $modified
    );
    $this->db->where('id', $id);
    $this->db->update('forms2', $data);
    return $id;
  }

  public function update_estimate($estimate_id, $json)
  {
    $data = array(
        'id' => $estimate_id,
        'json' => $json
    );
    $this->db->where('id', $estimate_id);
    $this->db->update('estimates2', $data);
    return $estimate_id;
  }

  public function read_all_forms()
  {
    $this->db->order_by('name', 'DESC');
    return $this->db->get('forms2');
  }

  public function read_all_estimates()
  {
    $this->db->order_by('id', 'DESC');
    return $this->db->get('estimates2');
  }

  public function delete_form($form_id)
  {
    $this->db->where('id', $form_id);
    $this->db->delete('forms2');
  }

  public function delete_estimate($estimate_id)
  {
    $this->db->where('id', $estimate_id);
    $this->db->delete('estimates2');
  }

  public function extract_info($json)
  {
    $prefill = array();
    $json = json_decode($json, true);

    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-title'){
          $prefill['info-title'] = $inner_array['value'];
        }
      }
    }
    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-floorplan'){
          $prefill['info-floorplan'] = $inner_array['value'];
        }
      }
    }
    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-base'){
          $prefill['info-base'] = $inner_array['value'];
        }
      }
    }
    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-shipping'){
          $prefill['info-shipping'] = $inner_array['value'];
        }
      }
    }
    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-info'){
          $prefill['info-info'] = $inner_array['value'];
        }
      }
    }
    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-state'){
          $prefill['info-state'] = $inner_array['value'];
        }
      }
    }
    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-zip'){
          $prefill['info-zip'] = $inner_array['value'];
        }
      }
    }
    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-date'){
          $prefill['info-date'] = $inner_array['value'];
        }
      }
    }
    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-fname'){
          $prefill['info-fname'] = $inner_array['value'];
        }
      }
    }
    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-lname'){
          $prefill['info-lname'] = $inner_array['value'];
        }
      }
    }
    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-phone'){
          $prefill['info-phone'] = $inner_array['value'];
        }
      }
    }
    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-email'){
          $prefill['info-email'] = $inner_array['value'];
        }
      }
    }
    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-sales'){
          $prefill['info-sales'] = $inner_array['value'];
        }
      }
    }
    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-notes'){
          $prefill['info-notes'] = $inner_array['value'];
        }
      }
    }
    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-taxes'){
          $prefill['info-taxes'] = $inner_array['value'];
        }
      }
    }
    foreach($json as $inner_array){
      if(isset($inner_array['name']) && isset($inner_array['value'])){
        if($inner_array['name'] == 'info-adjust'){
          $prefill['info-adjust'] = $inner_array['value'];
        }
      }
    }

    $total = 0;

    // add up values
    $checkbox_total = 0;
    foreach($json as $inner_array){
      if($inner_array['type'] == 'checkbox' && $inner_array['checked'] != false){
          $checkbox_total += $inner_array['price'];
      }
    }

    $number_total = 0;
    foreach($json as $inner_array){
      if($inner_array['type'] == 'number'){
        if($inner_array['value'] != 0){
          $number_total += $inner_array['price'] * $inner_array['value'];
        }
      }
    }

    $select_total = 0;
    foreach($json as $inner_array){
      if($inner_array['type'] == 'select'){
        foreach($inner_array['choices'] as $single_dropdown){
          if($single_dropdown['selected'] == true){
            $select_total += $dropdown_price = $single_dropdown['price'];
          }
        }
      }
    }

    $options_total = $checkbox_total + $number_total + $select_total;
    $prefill['info-options'] = $options_total;

    $prefill['info-total'] = $options_total + $prefill['info-base'] + $prefill['info-shipping'] + $prefill['info-adjust'] + $prefill['info-taxes'] + 500;

    return $prefill;
  }

}

?>
