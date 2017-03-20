<?php 
$command = @$command;
$item_detail = @$item_detail;
$user_branch_detail = @$user_branch_detail;
$master_user_role = @$master_user_role;
$departments = @$departments;
?>
<script type="text/javascript" src="<?=JS_PATH?>user/user.js"></script>
<form id="frm_user" name="frm_user" method="POST" action="<?=site_url('admin/user/save')?>" class="cmxform form-horizontal tasi-form" data-role="validator" novalidate="novalidate">
	<input type="hidden" id="aid" name="aid" value="<?=get_array_value($item_detail,"aid","")?>" />
	<input type="hidden" id="command" name="command" value="<?=$command?>" />
	<input type="hidden" id="save_option" name="save_option" value="" />

	<div class="row">
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					<?=@$header_title?>
				</header>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="cid">User code</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="cid" name="cid" value="<?=get_array_value($item_detail,"cid","")?>" onkeypress="isKeyUsername(event);" maxlength="<?=CONST_MAX_LENGTH_USERNAME?>" />
							<p class="help-block">User code must be between <?=CONST_MIN_LENGTH_USERNAME?> and <?=CONST_MAX_LENGTH_USERNAME?> characters long. <?=$this->lang->line('common_leave_blank_for_generate')?></p>
						</div>
					</div>

					<?php if(is_specify_username()){ ?>
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="username">Username</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="username" name="username" value="<?=get_array_value($item_detail,"username","")?>" onkeypress="isKeyUsername(event);" maxlength="<?=CONST_MAX_LENGTH_USERNAME?>" />
							<p class="help-block">Username must be between <?=CONST_MIN_LENGTH_USERNAME?> and <?=CONST_MAX_LENGTH_USERNAME?> characters long.</p>
						</div>
					</div>
					<?php } ?>

					<?php
						if(CONST_EMAIL_REQUIRED == "2"){
							$required = "";
						}else{
							$required = "required";
						}
					?>
					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label <?=$required?>" for="email">Email address</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control <?=$required?>" type="email" id="email" name="email" value="<?=get_array_value($item_detail,"email","")?>" />
						</div>
					</div>

					<?php if($command != "_update"){ ?>
						<div class="form-group">
							<label class="col-md-12 col-lg-2 control-label" for="password">Password</label>
							<div class="col-md-12 col-lg-8">
								<label class="checkbox-inline">
									<input type="checkbox" id="gen_pass" name="gen_pass" value="1" onclick="changGenPassOption(this)" checked />Generate Password
								</label>
								<label class="checkbox-inline">
									<input type="checkbox" id="send_activate" name="send_activate" value="1" onclick="" />Send Activate Email
								</label>

								<input class="form-control" type="password" id="password" name="password" value="" maxlength="<?=CONST_MAX_LENGTH_PASSWORD?>" disabled />
								<p class="help-block">Password must be between <?=CONST_MIN_LENGTH_PASSWORD?> and <?=CONST_MAX_LENGTH_PASSWORD?> characters long.</p>
							</div>

							<div class="clear"></div>
							<div class="spaceUp">
								<label class="col-md-12 col-lg-2 control-label" for="password_retype">Re-type Password</label>
								<div class="col-md-12 col-lg-8">
									<input class="form-control" type="password" id="password_retype" name="password_retype" value="" maxlength="<?=CONST_MAX_LENGTH_PASSWORD?>" disabled />
								</div>
							</div>
						</div>
					<?php } ?>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="user_role_aid">User Role</label>
						<div class="col-md-12 col-lg-8">
							<?php $user_role_aid =  get_array_value($item_detail,"user_role_aid",""); ?>
							<?php 
								if(is_var_array($master_user_role)){ 
									foreach($master_user_role as $m_item){
							?>
										<label class="radio-inline">
											<input type="radio" name="user_role_aid" id="user_role_aid_<?=get_array_value($m_item,"aid","")?>" value="<?=get_array_value($m_item,"aid","")?>" <?php if($user_role_aid == get_array_value($m_item,"aid","")) echo 'checked="checked"';?> onclick="changeUserRoleAid()" /><?=get_array_value($m_item,"name","")?>
										</label>
							<?php } } ?>
							<span id="publisher_area" class="hidden">
								<select id="publisher_aid" name="publisher_aid" disabled="disabled" class="required form-control w-auto inline">
									<option value="">Choose publisher..</option>
									<?php
									if(is_var_array($master_publisher)){
										foreach($master_publisher as $item){
											$selected = (get_array_value($item,"aid","0") == get_array_value($item_detail,"publisher_aid","")) ? 'selected="selected"' : '';
											echo '<option value="'.get_array_value($item,"aid","0").'" '.$selected.'>'.get_array_value($item,"name","N/A").'</option>';
										}									
									}
									?>
								</select>
							</span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="user_section_aid">User Section</label>
						<div class="col-md-12 col-lg-8">
							<?php 
								$user_section_aid =  get_array_value($item_detail,"user_section_aid","0"); 
							?>
							<?php 
								if(is_var_array($master_user_section)){ 
									foreach($master_user_section as $m_item){
										$m_user_section_aid = get_array_value($m_item,"aid","");
										$m_is_default = get_array_value($m_item,"is_default","0");
										if($m_is_default == '1' && !is_number_no_zero($user_section_aid)){
											$user_section_aid = $m_user_section_aid;
										}
							?>
										<label class="radio-inline">
											<input type="radio" name="user_section_aid" id="user_section_aid_<?=get_array_value($m_item,"aid","")?>" value="<?=get_array_value($m_item,"aid","")?>" <?php if($user_section_aid == $m_user_section_aid) echo 'checked="checked"';?> onclick="changeUserSectionAid(this.value)" /><?=get_array_value($m_item,"name","")?>
										</label>
							<?php } } ?>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="status">User Status</label>
						<div class="col-md-12 col-lg-8">
							<?php $status =  get_array_value($item_detail,"status",""); ?>
							<label class="radio-inline">
								<input type="radio" name="status" id="user_status_1" value="1" checked />Active
							</label>
							<label class="radio-inline">
								<input type="radio" name="status" id="user_status_0" value="0" <?php if($status == "0") echo 'checked="checked"';?> />Inactive
							</label>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label required" for="first_name_th">First name</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control required" type="text" id="first_name_th" name="first_name_th" value="<?=get_array_value($item_detail,"first_name_th","")?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="last_name_th">Last name</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="last_name_th" name="last_name_th" value="<?=get_array_value($item_detail,"last_name_th","")?>" />
						</div>
					</div>

					<div class="form-group ">
						<label class="col-md-12 col-lg-2 control-label" for="gender">Gender</label>
						<div class="col-md-12 col-lg-8">
							<?php $gender =  get_array_value($item_detail,"gender",""); ?>
							<label class="radio-inline">
								<input type="radio" id="gender_female" name="gender" value="f" checked />Female
							</label>
							<label class="radio-inline">
								<input type="radio" id="gender_male" name="gender" value="m" <?php if(get_array_value($item_detail,"gender","") == "m"){ echo 'checked'; } ?> />Male
							</label>
						</div>
					</div>


					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="contact_number">Phone</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="contact_number" name="contact_number" value="<?=get_array_value($item_detail,"contact_number","")?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="address">Address</label>
						<div class="col-md-12 col-lg-8">
							<textarea class="form-control" id="address" name="address"><?=get_array_value($item_detail,"address","")?></textarea>
						</div>
					</div>

					<div class="form-group hide" id="form_note_2">
						<label class="col-md-12 col-lg-2 control-label" for="note_2">Company Name</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="note_2" name="note_2" value="<?=get_array_value($item_detail,"note_2","")?>" />
						</div>
					</div>

					<div class="form-group hide" id="form_note_4">
						<label class="col-md-12 col-lg-2 control-label" for="note_4">Company Phone Number</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="note_4" name="note_4" value="<?=get_array_value($item_detail,"note_4","")?>" />
						</div>
					</div>


					<div class="form-group">
						<label class="col-md-12 col-lg-2 control-label" for="position">Job Position</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="position" name="position" value="<?=get_array_value($item_detail,"position","")?>" />
						</div>
					</div>

					
					

					<div class="form-group hide" id="form_department">
						<label class="col-md-12 col-lg-2 control-label" for="department">Department</label>
						<div class="col-md-12 col-lg-8">
							<?php 
								if(is_var_array($departments)){ 
									foreach ($departments as $key => $dept) { ?>
								<label class="radio-inline">
									<input type="radio" id="department" name="department" value="<?=get_array_value($dept, 'aid')?>" <?=($item_detail['department_aid'] == $dept['aid'] ? 'checked' : '')?>/> <?=get_array_value($dept, 'name', '')?>
								</label>
							<?php } } ?>
						</div>
					</div>

					
					<div class="form-group hide" id="form_note_1">
						<label class="col-md-12 col-lg-2 control-label" for="note_1">Department</label>
						<div class="col-md-12 col-lg-8">
							<input class="form-control" type="text" id="note_1" name="note_1" value="<?=get_array_value($item_detail,"note_1","")?>" />
						</div>
					</div>

					
					<?php if(CONST_HAS_POINT == "1" || CONST_HAS_REWARD_POINT == "1"){ ?>
						<div class="form-group">
							<label class="col-md-12 col-lg-2 control-label" for="point_remain">Point</label>
							<div class="col-md-12 col-lg-8">
								<input class="form-control" type="text" id="point_remain" name="point_remain" value="<?=get_array_value($item_detail,"point_remain","")?>" />
							</div>
						</div>
					<?php } ?>

					<!-- Button -->
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-8">
							<a class="btn btn-primary" onclick="processSubmitOption('frm_user', '0');" />Save & Close</a>
							<a class="btn btn-default" onclick="processRedirect('admin/user/show');" />Cancel</a>
						</div>
					</div>
					<!-- End : Button -->
				</div>
			</section>
		</div>
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function($){		
		var radios = $('input:radio[name=user_role_aid]');
		if(radios.is(':checked') === false) {
			radios.filter('[value=5]').attr('checked', true);
		}
		
		changeUserRoleAid();
		changeUserSectionAid();	

		var user_min_length = '<?=CONST_MIN_LENGTH_USERNAME?>';
		var user_max_length = '<?=CONST_MAX_LENGTH_USERNAME?>';
		var pass_min_length = '<?=CONST_MIN_LENGTH_PASSWORD?>';
		var pass_max_length = '<?=CONST_MAX_LENGTH_PASSWORD?>';
		
		$("#registration_date, #expiration_date").datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: "yy-mm-dd",
			dateISO:"true"
		});
		
		$("#birthday").datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: "yy-mm-dd",
			yearRange: 'c-80:c',
			dateISO:"true"
		});

		$("#frm_user").validate({
			rules: {
				username: {
					rangelength: [user_min_length, user_max_length]
				},
				password:{
					required: function (element) {
									if($("#gen_pass:checked").val() == 1){
										return false;
									}else{
										return true;
									}
					},
					rangelength: [pass_min_length, pass_max_length]			
				},
				password_retype:{
					equalTo: "#password"
				},
				email: {
					email: true
				},
				user_role_aid: {
					required: true
				},
				user_status: {
					required: true
				},
				publisher_aid:{
					required: function (element) {
									if($("input:radio[name=user_role_aid]:checked").val() == 6){
										return true;
									}else{
										return false;
									}
					}
				}
			},
			messages: {
				password_retype: {
					equalTo: "Enter the same password as above"
				},
				email: {
					required: "Please enter a valid email address",
					minlength: "Please enter a valid email address"
				},
				publisher_aid: {
					required: "&nbsp;&nbsp;Please choose publisher"
				}
			}
		});

		
		<?=@$message?>
		<?=@$js_code?>
	});
</script>