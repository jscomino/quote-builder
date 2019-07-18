<title>Mobile Home Options - Home Nation</title>
<meta content="Select options for your new Mobile Home here. We publish all of our home base prices and options, this allows you to easily customize your new manufactured home" name="description">
<!--...........XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX <- 160 characters -->

<div class="row">
	<div class="col-sm-12">
		<h1>Home Estimate</h1>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 text-center">
		<h2><?php echo $prefill['info-title'] ?> - $<?php echo number_format($prefill['info-base'] + $prefill['info-shipping']) ?></h2>
		<p><?php echo $prefill['info-info'] ?></p>
		<?php
			if($prefill['info-floorplan']){
				echo '<img class="img-fluid img-thumbnail" src="/codeigniter/assets/uploads/'.$prefill['info-floorplan'].'">';
			}else{
				echo '<img class="img-fluid img-thumbnail" src="/codeigniter/assets/img/no-photo.png">';
			}
		?>
	</div>
</div>
<hr>
<div class="height-twenty"></div>
<div class="row">
	<div class="col-sm-12">
		<form id="form-wrapper" method="post"></form>
	</div>
</div>
<div>
	<?php if($user['logged'] == true){ ?>
		<small><b>Note:</b> This field is only shown when logged in</small><br>
		<div class="input-group col-sm-4">
   		<input type="text" id="autofill" class="form-control" name="" placeholder="Enter email of customer" style="margin-left:-15px;">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" id="autofillbutton">Autofill</button>
			</span>
		</div>

		<script>
			$( "#autofillbutton" ).click(function(){

					var url = "/leads/quick_info";
					var memail = $('#autofill').val();
					$.ajax({
						type: "POST",
						url: url,
						data: {
							'email': memail
						},
						success: function(data){
							if(data == 'false'){
								alert('Lead does not exist');
							}else{
								c_info = JSON.parse(data);
								$('[name=info-fname]').val(c_info.fname);
								$('[name=info-lname]').val(c_info.lname);
								$('[name=info-email]').val(c_info.email);
								$('[name=info-phone]').val(c_info.phone);
								$('[name=info-sales]').val(c_info.leadowner);
								alert('Autofill Complete');
							}
						}
					});

			});
		</script>
	<?php } ?>
</div>
<div class="row">
	<div class="col-sm-12 text-center">
		<p class="font-size-fifty-more"><b>Total</b><br><span id="form-total"></span></p>
		<button type="button" class="btn btn-lg btn-primary save-form finger">Save</button>
	</div>
</div>
<script src="/codeigniter/assets/js/formulator.js"></script>
<script src="/codeigniter/assets/js/formulator-popper.js"></script>
<link href="//cdn.rawgit.com/noelboss/featherlight/1.7.8/release/featherlight.min.css" type="text/css" rel="stylesheet" />
<script src="//cdn.rawgit.com/noelboss/featherlight/1.7.8/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>
<script>
	$(document).ready(function(){

		// set up empty form object
		empty_form = <?php echo $form['json'] ?>;

		// render empty form
		output = $('#form-wrapper');
		form = FormForm( output, empty_form );
		form.render();

		// prefill form
		var infobase = '<?php echo $prefill["info-base"] ?>';
		var infoshipping = '<?php echo $prefill["info-shipping"] ?>';
		var infoinfo = '<?php echo $prefill['info-info'] ?>';
		var infofloorplan = '<?php echo $prefill['info-floorplan'] ?>';
		var infotitle = '<?php echo $prefill['info-title'] ?>';
		var infostate = '<?php echo $prefill["info-state"] ?>';
		var infozip = '<?php echo $prefill["info-zip"] ?>';
		var infodate = '<?php echo $prefill['info-date']; ?>';

		$('[name=info-base]').val(infobase);
		$('[name=info-shipping]').val(infoshipping);
		$('[name=info-info]').val(infoinfo);
		$('[name=info-floorplan]').val(infofloorplan);
		$('[name=info-title]').val(infotitle);
		$('[name=info-state]').val(infostate);
		$('[name=info-zip]').val(infozip);
		$('[name=info-date]').val(infodate);

		// calculate total on load
		calculateTotal();

		// if user interacts with form, recalculate total
		$('#form-wrapper :input[type=checkbox]').on('click', function(event){
			calculateTotal();
		});
		$('#form-wrapper :input[type=number]').on('focusout', function(event){
			calculateTotal();
		});
		$('#form-wrapper select, [name=info-adjust], [name=info-taxes]').on('change', function(event){
			calculateTotal();
		});

		// upon form submit
		$('.save-form').click(function(){

			// forces user to fill out email
			var useremail = $('[name="info-email"]').val();
			if(useremail == ''){
				alert('You must fill in an email address');
				e.preventDefault();
			}

			// forces user to fill out notes if adjust is filled out
			var adjustfield = $("[name=info-adjust]").val();
			var notesfield = $("[name=info-notes]").val();
			if(adjustfield != false && notesfield == false){
				alert('You must provide a reason for the Adjust value in Notes');
				e.preventDefault();
			}

			<?php
				// if user is logged in and has permissions...
				if(isset($user) && $user['logged'] == 'true' && in_array('adjust', $user['permissions'])){
					echo 'var adjustbypass = "true";';
				}else{
					echo 'var adjustbypass = "false";';
				}
			?>


			// ensure user does not have "adjust" that is more than 8% of total
			if(adjustfield != false && adjustbypass == 'false'){
				thetotal = calculateTotal();
				eightpercent = Math.floor(thetotal * 0.08); // get 8% of total
				if(adjustfield < 0){
					adjustfield *= -1; // convert to a positive number
					if(adjustfield > eightpercent){
						alert('You cannot adjust the price more than 8% without prior approval');
						e.preventDefault();
					}
				}
			}

			// empty form
			$.each(empty_form, function(key, field){
				if(field.type == 'checkbox'){
					field.checked = '';
				}else if(field.type == 'select'){
					$.each(field.choices, function(key, option){
						option.selected = '';
					});
				}else if(field.hasOwnProperty('value')){
					field.value = '';
				}
			});

			// transpose checked values
			var checkers = $('#form-wrapper :input[type=checkbox]:checked');
			$.each(checkers, function(key, checkbox){
				if(checkbox.checked){
					$.each(empty_form, function(key, field){
						if(field.name == checkbox.name){
							// make sure "name" values are unique
							field.checked = 'true';
						}
					});
				}
			});

			// transpose text values
			var texters = $('#form-wrapper input[type=placeholder], input[type=text], input[type=number], input[type=hidden], textarea');
			$.each(texters, function(key, textinput){
				if(textinput.value){
					$.each(empty_form, function(key, field){
						if(field.name == textinput.name){
							field.value = textinput.value;
						}
					});
				}
			});

			// transpose selected values
			var selecters = $('#form-wrapper select');
			$.each(selecters, function(key, dropdown){ // for each select element
				$.each(empty_form, function(key, field){ // for each field in the empty form
					if(field.name == dropdown.name){ // if the field and select element match
						$.each(dropdown, function(outerkey, option){ // loop through dropdown options from select element
							if(option.selected){ // if an option is selected & not empty
								$.each(field.choices, function(innerkey, inner){ // loop through empty select options
									if(innerkey == outerkey){ // if keys match up
										field.choices[innerkey].selected = 'selected'; // change value
									}
								});
							}
						});
					}
				});
			});

			/*console.log(empty_form);
			throw new Error("my error message");*/

			var finalform = JSON.stringify(empty_form);

			$.post("/estimates2/save_estimate", {
					json: finalform,
					estimateid: '<?php echo $estimate_id ?>'
				},
				function(data){
					console.log(data);
					window.location="/estimate2-pdf?id="+data;
				}
			);
		});

		function calculateTotal(){

			var total = 0;

			// add checkbox values
			var checks = $("#form-wrapper :input[type=checkbox]:checked");
			$.each(checks, function(key, checkbox){
				total += parseInt(checkbox.getAttribute('price'));
			});

			// add number values
			var numbers = $("#form-wrapper :input[type=number]");
			$.each(numbers, function( key, numberinput){
				unitprice  = parseInt(numberinput.getAttribute('price'));
				multiplier = parseInt(numberinput['value']);
				if(!isNaN(multiplier)){
					pretotal = unitprice * multiplier;
					total += pretotal;
				}
			});

			// add dropdown values
			var droppies = $("#form-wrapper :selected");
			$.each(droppies, function( key, dropper){
				if(dropper.value != ''){
					total += parseInt(dropper.value);
				}
			});

			// add adjust
			adjust = $("[name=info-adjust]");
			adjust = parseInt(adjust.val());
			if(Number.isInteger(adjust)){
				total += parseInt(adjust);
			};

			// add taxes
			taxes = $("[name=info-taxes]");
			taxes = parseInt(taxes.val());
			if(Number.isInteger(taxes)){
				total += parseInt(taxes);
			};

			pretotal = total + parseInt(infobase) + parseInt(infoshipping);

			// format both as USD, remove trailing decimal places
			total = (pretotal).toLocaleString('en-US', { style: 'currency', currency: 'USD', }).slice(0, -3);

			$('#form-total').text(total); // display total
			return pretotal; // used to calculate if "adjust" is more or less than 4%
		}

		// print options list
		$(".print-options-button").click(function(){
			var options_content = document.getElementById("print-options-div");
			var print_page = window.open('', '', '');
			print_page.document.write(options_content.innerHTML);
			print_page.document.close();
			print_page.focus();
			print_page.print();
			print_page.close();
		});

		// enable popovers
		options = {
			html:true,
			placement:'bottom',
			trigger: 'focus'
		}
		$('.infobox').popover(options)

	});
</script>
