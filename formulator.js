var FormForm = (function($){
	return function(wrapper, fields){

		var self = {};

		self.render = function(){


			// NOTE: this code checks for options that have duplicate names
			// if an option has a duplicate name, 2 options will be selected at once
			$.each(fields, function (index, field){
				$total = 0;
				$.each(fields, function (newindex, newfield){
					if(newfield.name != undefined){
						if(field.name == newfield.name){
							$total += 1;
						}
					}
				});
				if($total > 1){
					alert('We have a problem with option name: '+field.name);
				}
			});
			

			$.each(fields, function (index, field){

				if(!field.hasOwnProperty('value')){
					field.value = ''; // if value is not set
				}

				if(field.type == 'text')
				{
					wrapper.append('<label>'+field.label+'</label>');
					wrapper.append('<input type="text" class="form-control" name="'+field.name+'" value="'+field.value+'"/><br>');
				}
				else if(field.type == 'textarea')
				{
					wrapper.append('<label>'+field.label+'</label>');
					wrapper.append('<textarea class="form-control" name="'+field.name+'" rows="6">'+field.value+'</textarea><br>');
				}
				else if(field.type == 'number')
				{
					if(field.hasOwnProperty('html')){
						popover = '<a href="#" class="infobox text-gray" onclick="return false" data-toggle="popover" title="Info" data-content="'+field.html+'<p><i>*your option may look different</i></p>"><i class="fas fa-camera text-info font-size-twenty-more"></i></a>';
					}else{
						popover = '';
					}
					wrapper.append('<label>'+field.label+' - $'+field.price+' '+popover+'</label>');
					wrapper.append('<input type="number" class="form-control" name="'+field.name+'" price="'+field.price+'" value="'+field.value+'"/><br>');
				}
				else if(field.type == 'hidden')
				{
					wrapper.append('<input type="hidden" name="'+field.name+'" value="'+field.value+'"/>');
				}
				else if(field.type == 'checkbox')
				{
					if(field.hasOwnProperty('html')){
						popover = '<a href="#" class="infobox text-gray" onclick="return false" data-toggle="popover" title="Info" data-content="'+field.html+'<p><i>*your option may look different</i></p>"><i class="fas fa-camera text-info font-size-twenty-more" style="opacity:0.5"></i></a>';
					}else{ popover = ''; }

					if(field.hasOwnProperty('checked') && field.checked != ''){
						checked = 'checked';
					}else{
						checked = '';
					}

					wrapper.append('<input type="checkbox" name="'+field.name+'" price="'+field.price+'" '+checked+'> <label> '+field.label+' - $'+field.price+' '+popover+'</label><br>');
				}
				else if(field.type == 'select')
				{
					options = '';

					if(field.hasOwnProperty('html')){
						popover = '<a href="#" class="infobox text-gray" onclick="return false" data-toggle="popover" title="Info" data-content="'+field.html+'<p><i>*your option may look different</i></p>"><i class="fas fa-camera text-info font-size-twenty-more" style="opacity:0.5"></i></a>';
					}else{ popover = ''; }

					wrapper.append('<label>'+field.label+' '+popover+'</label>');

					$.each(field.choices, function(index, choice){

						if(choice.selected == ''){
							choice.selected = '';
						}else{
							choice.selected = 'selected';
						}

						// IMPORTANT DISTINCTION:
						// If an option has a price of '', then it will NOT be included in the final Home Estimate,
						// eg. a "None" value. If the price is $0 and it is selected, it WILL be included in the final
						// Home Estimate as $0 will be treated as a string containing a zero character
						if(choice.price == ''){
							choice.price = '';
							private_variable = choice.label;
						}else if(choice.price == '0'){
							choice.price = '0';
							private_variable = choice.label;
						}else{
							private_variable = choice.label+' - $'+choice.price;
						} // NOTICE: setting this variable will NOT modify the actual JSON form on frontend

						options += '<option value="'+choice.price+'" '+choice.selected+'>'+private_variable+'</option>';
					});

					wrapper.append('<select class="form-control" name="'+field.name+'">'+options+'</select><br>');
				}
				else if(field.type == 'header')
				{
					wrapper.append('<h3>'+field.content+'</h3>');
				}
				else if(field.type == 'label')
				{
					wrapper.append('<br><label><big>'+field.content+'</big></label><br>');
				}
				else if(field.type == 'html')
				{
					wrapper.append(field.content);
					wrapper.append('<br>');
				}
			});
		}

		return self
	};
})(jQuery);
