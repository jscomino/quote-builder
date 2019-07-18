<?php

class Estimates2 extends MY_Controller{

	function __construct(){
		parent::__construct();
		$this->load->model('estimate2');
		$this->load->model('lead');
		$this->load->model('home');
		$this->load->model('user');
		$this->load->helper('url');
	}

	public function add_form()
	{
		$data['user'] = $this->get_user_info();
		$this->load->view('backend/header', $data);
		$this->load->view('estimates2/edit-form', $data);
		$this->load->view('backend/footer');
	}

  public function edit_form()
	{ // prefill form
		$id = $this->uri->segment(3);
		$form = $this->estimate2->read_form($id);
		$form['json'] = trim($form['json'], '[]'); // remove brackets
    $data['form'] = $form;
		$data['user'] = $this->get_user_info();
		$this->load->view('backend/header', $data);
		$this->load->view('estimates2/edit-form', $data);
		$this->load->view('backend/footer');
	}

	public function save_form()
	{
		// gather post data, save or update
		$name = $this->input->post('name');
		$json = $this->input->post('json');
		$id = $this->input->post('id');
		$modified = $this->input->post('modified');

		// since textarea content submits as a string, wrap json in brackets
		$json = "[".$json."]";

		if($id){
			$id = $this->estimate2->update_form($id, $name, $json, $modified);
		}else{
			$id = $this->estimate2->create_form($name, $json, $modified);
		}

		redirect('/form2/edit/'.$id);
	}

	public function add_estimate()
	{
		$data['form'] = $this->estimate2->read_form($_GET['form']);

		if($data['form'] == false){
			show_404(); // if form does not exist
			die();
		}

		$data['estimate_id'] = false;
		$user = $this->get_user_info();

		$prefill = array();

		if($_GET['home'] == 'custom'){
			$prefill['info-title'] = $_GET['title'];
			$prefill['info-base'] = $_GET['base'];
			$prefill['info-shipping'] = $_GET['shipping'];
			$prefill['info-info'] = $_GET['bedrooms'].' bed /'.$_GET['bathrooms'].' bath - '.ucfirst($_GET['homecondition']).' '.$_GET['type'].' ID '.$_GET['home'];
			$prefill['info-floorplan'] = false;
		}else{
			$home = $this->home->read_by_id($_GET['home']);
			$prefill['info-title'] = $home['title'];
			$prefill['info-base'] = $home['listed'];

			// apply sale discount
			if(isset($_GET['sale']) && $_GET['sale'] == 'true')
			{
				$percentage = '1.'.$home['percentage'];
				$our_price = $home['basecost'] * $percentage;
				$sale_discount = $our_price * .15;
				$lower_price = floor($our_price - $sale_discount) + $home['adjust'];
				$prefill['info-base'] = $lower_price;
			}

			$prefill['info-shipping'] = $home['shipping'];
			$prefill['info-info'] = $home['bedrooms'].' bed / '.$home['bathrooms'].' bath - '.ucfirst($home['homecondition']).' '.$home['pretty'].' ID '.$_GET['home'];
			$prefill['info-floorplan'] = $home['floorplan'];
		}

		$prefill['info-state'] = $user['state'];
		$prefill['info-zip'] = $user['zip'];
		$prefill['info-date'] = date("n/j/y");

		$data['estimateid'] = false;
		$data['prefill'] = $prefill;
		$data['user'] = $user;
		$this->load->view('frontend/header', $data);
		$this->load->view('estimates2/home-estimate', $data);
		$this->load->view('frontend/footer');
	}

	public function custom_estimate()
	{
		$data['user'] = $this->get_user_info();
		$data['forms'] = $this->estimate2->read_all_forms();

		$this->load->view('backend/header', $data);
		$this->load->view('estimates2/custom-estimate', $data);
		$this->load->view('backend/footer');
	}

	public function save_estimate()
	{
		$estimate_id = $this->input->post('estimateid');
		$json = $this->input->post('json');
		$form_info = $this->estimate2->extract_info($json);
		$email = $form_info['info-email']; // use email to check for lead
		$sales_email = $form_info['info-sales']; // salespersons email, double check
		$lead = $this->lead->read_by_email($email);

		$this->lead->undelete($email); // resurrect lead if necessary

		if($lead == false){
			$user_info = $this->get_user_info();
			$data = array(); // used to create a lead

			if(isset($form_info['info-fname'])){ $data['fname'] = $form_info['info-fname']; }
			if(isset($form_info['info-lname'])){ $data['lname'] = $form_info['info-lname']; }
			if(isset($form_info['info-email'])){ $data['email'] = $form_info['info-email']; }
			if(isset($form_info['info-phone'])){ $data['phone'] = $form_info['info-phone']; }
			if(isset($form_info['info-state'])){ $data['state'] = $form_info['info-state']; }
			if(isset($form_info['info-zip'])){ $data['zip'] = $form_info['info-zip']; }

			// if user exists, assign new lead to user
			if($this->user->read_by_email($sales_email) == false){
				$data = $this->lead->assign_lead($data);
			}else{
				$data['leadowner'] = $sales_email;
			}

			$this->lead->create($data);
		}

		$this->lead->modify_today($email);

		if($estimate_id){
			$estimate_id = $this->estimate2->update_estimate($estimate_id, $json);
		}else{
			$estimate_id = $this->estimate2->create_estimate($email, $json);
		}

		echo $estimate_id; // must echo
	}

	public function edit_estimate()
	{
		$estimate_id = $this->uri->segment(3);
		$data['form'] = $this->estimate2->read_estimate($estimate_id);
		$data['prefill'] = $this->estimate2->extract_info($data['form']['json']);
		$data['estimate_id'] = $estimate_id;
		$data['user'] = $this->get_user_info();
		$this->load->view('frontend/header', $data);
		$this->load->view('estimates2/home-estimate', $data);
		$this->load->view('frontend/footer');
	}

	public function view_form()
  {
		$data['prefill'] = array(
			'info-base'=>'500',
			'info-title'=>'Example Home',
			'info-shipping'=>'500',
			'info-info'=>'3 Bed 2 Bath',
			'info-floorplan'=>false,
			'info-state'=>'IN',
			'info-zip'=>'46543',
			'info-date'=>date("n/j/y")
		);
    $id = $this->uri->segment(3);
    $data['form'] = $this->estimate2->read_form($id);
		$data['estimate_id'] = false;
		$data['user'] = $this->get_user_info();
    $this->load->view('frontend/header', $data);
    $this->load->view('estimates2/home-estimate', $data);
    $this->load->view('frontend/footer');
  }

	public function view_estimate()
	{
		$estimate_id = $_GET['id'];
		$estimate = $this->estimate2->read_estimate($estimate_id);
		$estimate_info = $this->estimate2->extract_info($estimate['json']);
		$options_array = json_decode($estimate['json'], true);

		if(isset($estimate_info['info-floorplan'])){
			if($estimate_info['info-floorplan'] != false){
				$home_image = '/uploads/'.$estimate_info['info-floorplan'];
			}else{
				$home_image = '/img/no-photo.png';
			}
		}else{
			// used in case we somehow didn't include a info-floorplan field on the form
			$home_image = '/img/no-photo.png';
		}

		$mpdf = new mPDF();

		$mpdf->SetHTMLHeader('<div style="text-align: right;">'.$estimate_info['info-date'].'</div>');
		$mpdf->setFooter('{PAGENO}');
		$mpdf->SetWatermarkText('HOME NATION', 0.06);
		$mpdf->showWatermarkText = true;
		$mpdf->SetTitle('Home Nation - My Home Estimate');

		if($estimate_info['info-fname'] == ''){
			$phrase = 'Your home estimate';
		}else{
			$phrase = $estimate_info['info-fname'].', your home Estimate';
		}

		// this flag determines if we show prices or not on the Home Estimate
		// used when sending the Home Estimate to manufacturers to hide pricing
		$showprice = $_GET['price'];

		$html = '
		<html>
			<head>
				<link rel="stylesheet" type="text/css" href="/codeigniter/assets/css/pdf.css">
				<meta name="viewport" content="width=device-width, initial-scale=1">
			</head>
			<body>
				<div class="logo-div">
					<img src="/home/homena7/public_html/codeigniter/assets/img/logo-square.jpg"></img>
				</div>
				<div class="text-div">
					<h1>'.$phrase.'</h1>
					<p>Thank you for choosing Home Nation. We take pride in offering only the best value homes to customers across the nation. The following is an estimate for your home and options. Please note this is not a quote. We hope you choose us to build your next home! <i>- Paul Comino</i></p>
				</div>
				<hr>
				<p><b>'.$estimate_info["info-title"].'</b> - '.$estimate_info["info-info"].'</p>

				<div class="image-div">
					<img class="floorplan-photo" src="/home/homena7/public_html/codeigniter/assets/'.$home_image.'">
				</div>
				<br>
				<table class="info-table">
					<tr>
						<td>Date Created</td>
						<td>'.$estimate_info["info-date"].'</td>
					</tr>
					<tr>
						<td>Name</td>
						<td>'.$estimate_info["info-fname"].' '.$estimate_info["info-lname"].'</td>
					</tr>
					<tr>
						<td>Phone</td>
						<td>'.$estimate_info["info-phone"].'</td>
					</tr>
					<tr>
						<td>Email</td>
						<td>'.$estimate_info["info-email"].'</td>
					</tr>
					<tr>
						<td>Salesperson</td>
						<td>'.$estimate_info["info-sales"].'</td>
					</tr>
					<tr>
						<td>State</td>
						<td>'.$estimate_info["info-state"].'</td>
					</tr>
					<tr>
						<td>Zip Code</td>
						<td>'.$estimate_info["info-zip"].'</td>
					</tr>
				</table>
		';

		$mpdf->WriteHTML($html);
		$mpdf->AddPage();

		// start the "option table"
		$html2 = '
			<strong>Options</strong>
			<table class="option-table">
				<tr>
					<th><b>Description</b></th>
					<th><b>Unit Price</b></th>
					<th><b>Quantity</b></th>
					<th><b>Cost</b></th>
				</tr>
				<tr>
					<td>'.$estimate_info['info-title'].'</td>
					<td></td>
					<td></td>
					<td>'.($estimate_info['info-base'] + $estimate_info['info-shipping']).'</td>
				</tr>
		';

		// echo checkbox options row by row
		$checkbox_total = 0;

		foreach($options_array as $inner_array){
			if($inner_array['type'] == 'checkbox' && $inner_array['checked'] != ''){

				if($inner_array['price'] != true){
					$checkbox_price = 'included';
				}else{
					$checkbox_price = $inner_array['price'];
				}

				// show price override
				if($showprice == 'false'){ $checkbox_price = ''; }

				$html2 .= '<tr>';
				$html2 .= '<td>'.$inner_array['label'].'</td>';
				$html2 .= '<td></td>';
				$html2 .= '<td></td>';
				$html2 .= '<td>'.$checkbox_price.'</td>';
				$html2 .= '</tr>';
			}
		}

		// echo number options row by row
		$number_total = 0;
		foreach($options_array as $inner_array){
			if($inner_array['type'] == 'number'){
				if($inner_array['value'] != 0){
					$html2 .= '<tr>';
					$html2 .= '<td>'.$inner_array['label'].'</td>';
					$html2 .= '<td>'.$inner_array['price'].'</td>';
					$html2 .= '<td>'.$inner_array['value'].'</td>';
					$total_number = $inner_array['price'] * $inner_array['value'];

					// show price override
					if($showprice == 'false'){ $total_number = ''; }

					$html2 .= '<td>'.$total_number.'</td>';
					$html2 .= '</tr>';
				}
			}
		}

		// echo dropdown options row by row
		$dropdown_total = 0;
		foreach($options_array as $inner_array){
			if($inner_array['type'] == 'select'){
				foreach($inner_array['choices'] as $single_dropdown){
					if($single_dropdown['selected'] == true && $single_dropdown['price'] != ''){

						if($single_dropdown['price'] == '0'){
							$dropdown_price = 'included';
						}else{
							$dropdown_price = $single_dropdown['price'];
						}

						// show price override
						if($showprice == 'false'){ $dropdown_price = ''; }

						$html2 .= '<tr>';
						$html2 .= '<td>'.$inner_array['label'].' - '.$single_dropdown['label'].'</td>';
						$html2 .= '<td></td>';
						$html2 .= '<td></td>';
						$html2 .= '<td>'.$dropdown_price.'</td>';
						$html2 .= '</tr>';
					}
				}
			}
		}

		// replace line breaks with <br> for display
		$notes = str_replace("\\n","<br>",$estimate_info['info-notes']);

		$html2 .= '
		</table>
		<p class="bigger-notes"><b>Notes:</b><br><pre style="font-family: \'Arial\', monospace;">'.$notes.'</pre></p>
		<table class="invisible-table">
			<tr>
				<td>Base Price</td>
				<td>'.floor($estimate_info['info-base'] + $estimate_info['info-shipping']).'</td>
			</tr>
			<tr>
				<td>Options</td>
				<td>'.$estimate_info['info-options'].'</td>
			</tr>
			<tr>
				<td>Delivery</td>
				<td>Included</td>
			</tr>
			<tr>
				<td>Admin Fee</td>
				<td>500</td>
			</tr>
			<tr>
				<td>Sales Tax</td>
				<td>'.floor($estimate_info['info-taxes']).'</td>
			</tr>
			<tr>
				<td>Adjust</td>
				<td>'.floor($estimate_info['info-adjust']).'</td>
			</tr>
			<tr>
				<td class="grinch-text"><b>Total</b></td>
				<td class="grinch-text"><b>'.number_format($estimate_info['info-base'] + $estimate_info['info-options'] + 500 + $estimate_info['info-taxes'] + $estimate_info['info-adjust'] + $estimate_info['info-shipping']).'</b></td>
			</tr>
		</table>';

		$mpdf->WriteHTML($html2);

		$html_final = '
			</body>
		</html>
		';

		$mpdf->WriteHTML($html_final);

		// Output a PDF file directly to the browser
		$mpdf->Output('Home Nation - Estimate for '.$estimate_info['info-fname']. '.pdf', 'I');
	}

  public function form_list()
  {
    $data['user'] = $this->get_user_info();
    $data['query'] = $this->estimate2->read_all_forms();
    $this->load->view('backend/header', $data);
    $this->load->view('estimates2/form-list', $data);
    $this->load->view('backend/footer');
  }

	public function estimate_list()
	{
		$data['user'] = $this->get_user_info();
    $data['query'] = $this->estimate2->read_all_estimates();
    $this->load->view('backend/header', $data);
    $this->load->view('estimates2/estimate-list', $data);
    $this->load->view('backend/footer');
	}

	public function delete_form()
	{
		$form_id = $this->uri->segment(3);
		$this->estimate2->delete_form($form_id);
		redirect('/forms2');
	}

	public function delete_estimate()
	{
		$estimate_id = $this->uri->segment(3);
		$this->estimate2->delete_estimate($estimate_id);

		if(isset($_SERVER['HTTP_REFERER'])){
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			redirect('/leads');
		}

	}
}

?>
