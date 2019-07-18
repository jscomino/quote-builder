<div class="row">
  <div class="col-sm-12">
    <h1><?php if(isset($form)){ echo 'Edit'; }else{ echo 'Add'; } ?> Form</h1>
  </div>
</div>
<div class="row">
  <div class="col-sm-8">
    <form action="/estimates2/save_form" method="post">
      <div class="form-group row">
        <div class="col-sm-8">
          <input type="text" class="form-control" name="name" placeholder="Form Name" value="<?php if(isset($form)){ echo $form['name']; } ?>" required>
        </div>
        <div class="col-sm-2">
          <p>Form ID</p>
        </div>
        <div class="col-sm-2">
          <input type="text" class="form-control" name="id" value="<?php if(isset($form)){ echo $form['id']; } ?>" readonly>
        </div>
      </div>
      <div class="form-group">
        <textarea id="fieldtextarea" name="json" class="form-control" rows="22" required><?php if(isset($form)){ echo $form['json']; } ?></textarea>
      </div>
      <div class="form-group">
        <span class="btn btn-primary pull-left" onclick="add_element('checkbox')">Add Checkbox</span>
        <input type="hidden" name="modified" value="<?php echo date("Y-m-d h:i:s", time()) ?>" />
        <input type="submit" name="field-form" class="btn btn-primary btn-margin pull-right finger" value="Save" />
        <a class="btn btn-primary btn-margin pull-right <?php if(!isset($form)){echo 'disabled';}?>" href="/form2/view/<?php if(isset($form)){ echo $form['id']; } ?>">View</a>
      </div>
    </form>
  </div>
  <div class="col-sm-4">
    <div class="row">
      <ul class="list-group">
        <li class="list-group-item finger" onclick="add_element('checkbox')"><i class="far fa-check-square"></i>&nbsp;Checkbox</li>
        <li class="list-group-item finger" onclick="add_element('number')"><i class="fas fa-sort-numeric-up"></i>&nbsp;Number</li>
        <li class="list-group-item finger" onclick="add_element('select')"><i class="fas fa-list"></i>&nbsp;Select</li>
        <li class="list-group-item finger" onclick="add_element('label')"><i class="fas fa-tag"></i>&nbsp;Label</li>
        <li class="list-group-item finger" onclick="add_element('header')"><i class="fas fa-heading"></i>&nbsp;Header</li>
        <li class="list-group-item finger" onclick="add_element('html')"><i class="fas fa-code"></i>&nbsp;HTML</li>
        <li class="list-group-item finger" onclick="add_element('default')"><i class="fas fa-sitemap"></i>&nbsp;Default Fields</li>
        <!--<li class="list-group-item finger" onclick="add_element('text')"><i class="fas fa-battery-empty"></i>&nbsp;Text Input</li>
        <li class="list-group-item finger" onclick="add_element('textarea')"><i class="fas fa-text-height"></i>&nbsp;Text Area</li>
        <li class="list-group-item finger" onclick="add_element('hidden')"><i class="fas fa-eye-slash"></i>&nbsp;Hidden</li>-->
      </ul>
    </div>
  </div>
</div>
<script>
function add_element(type){
    random = 'hn' + Math.floor((Math.random() * 1000000) + 1);
    elements = {
      checkbox: '{\n\t"type":"checkbox",\n\t"name":"'+random+'",\n\t"label":"",\n\t"price":""\n}',
      number: '{\n\t"type":"number",\n\t"name":"'+random+'",\n\t"label":"",\n\t"price":""\n}',
      select: '{\n\t"type":"select",\n\t"label": "",\n\t"name":"'+random+'",\n\t"choices": [\n\t\t{\n\t\t\t"label":"",\n\t\t\t"price":"",\n\t\t\t"selected":""\n\t\t},\n\t\t{\n\t\t\t"label":"",\n\t\t\t"price":"",\n\t\t\t"selected":""\n\t\t},\n\t\t{\n\t\t\t"label":"",\n\t\t\t"price":"",\n\t\t\t"selected":""\n\t\t}\n\t]\n}',
      header: '{\n\t"type":"header",\n\t"content":""\n}',
      label: '{\n\t"type":"label",\n\t"content":""\n}',
      html: '{\n\t"type":"html",\n\t"content":""\n}',
      hidden: '{\n\t"type":"hidden",\n\t"name":"'+random+'"\n}',
      text: '{\n\t"type":"text",\n\t"label":"option nam",\n\t"name":"'+random+'"\n}',
      textarea: '{\n\t"type":"textarea",\n\t"label":"option name",\n\t"name":"'+random+'",\n}',
      default: '{\n\t"type":"header",\n\t"content":"Contact"\n},{\n\t"type":"hidden",\n\t"label":"'+random+'",\n\t"name":"info-base"\n},{\n\t"type":"hidden",\n\t"label":"'+random+'",\n\t"name":"info-shipping"\n},\
      {\n\t"type":"hidden",\n\t"label":"'+random+'",\n\t"name":"info-info"\n},{\n\t"type":"hidden",\n\t"label":"'+random+'",\n\t"name":"info-floorplan"\n},\
      {\n\t"type":"hidden",\n\t"label":"'+random+'",\n\t"name":"info-title"\n},{\n\t"type":"hidden",\n\t"label":"'+random+'",\n\t"name":"info-state"\n},\
      {\n\t"type":"hidden",\n\t"label":"'+random+'",\n\t"name":"info-date"\n},{\n\t"type": "text",\n\t"label":"Taxes",\n\t"name":"info-taxes"\n},\
      {\n\t"type":"text",\n\t"label":"Adjust",\n\t"name":"info-adjust"\n},{\n\t"type": "text",\n\t"label":"First Name",\n\t"name":"info-fname"\n},{\n\t"type": "text",\n\t"label":"Last Name",\n\t"name":"info-lname"\n},\
      {\n\t"type":"text",\n\t"label":"Email",\n\t"name":"info-email"\n},{\n\t"type":"text",\n\t"label":"Salesperson Email",\n\t"name":"info-sales"\n},{\n\t"type":"text",\n\t"label":"Phone Number",\n\t"name":"info-phone"\n},\
      {\n\t"type":"text",\n\t"label":"Zip Code",\n\t"name":"info-zip"\n},{\n\t"type":"textarea",\n\t"label":"Notes",\n\t"name":"info-notes"\n}'
    }
    current_value = $("#fieldtextarea").val();
    last_character = current_value[current_value.length-1];
    if(last_character == '}'){
      current_value = current_value + ',';
    }
    new_value =  current_value + elements[type];
    $("#fieldtextarea").val(new_value);
    $('#fieldtextarea').scrollTop($('#fieldtextarea')[0].scrollHeight); // scroll to bottom
};

// allow tabs in textarea
$(document).delegate("#fieldtextarea","keydown",function(t){if(9==(t.keyCode||t.which)){t.preventDefault();var e=this.selectionStart,i=this.selectionEnd;$(this).val($(this).val().substring(0,e)+"\t"+$(this).val().substring(i)),this.selectionStart=this.selectionEnd=e+1}});
</script>
