<script type="text/javascript">
	$(document).ready(function($){		
		<?=@$message?>
		<?=@$js_code?>
	});
</script> 
<?php 
$item_detail = @$item_detail;
$user_avatar = @$user_avatar;
$master_department = @$master_department;

// echo "<pre>";
// print_r($item_detail);
// echo "</pre>";

?>
<script>
jQuery(document).ready(function($){
		
		validator_frm_user();
		<?=@$message?>
		<?=@$js_code?>
	});
</script>
<script type="text/javascript" src="<?=JS_PATH?>user/user.js"></script>
<script type="text/javascript" src="<?=JS_PATH?>user/my_account.js"></script>

		<section id="page">

			<section id="content" class="mt30 mb30">
				<div class="container">

					<div class="row">
						<div class="col-sm-12">
							<div class="">


								<form id="frm_user" name="frm_user" class="form-horizontal tasi-form" method="POST" action="<?=site_url('my-account/save')?>" enctype="multipart/form-data" data-role="validator" novalidate="novalidate">
									<div id="result-msg-box" class="hidden" ></div>								
									<div class="comment-left">
										<div class="form-group">  
											<label class="" for="email">Accumulated Reading Points : </label>
											<?=get_array_value($item_detail,"point_remain","0");?> point
										</div>
										<div class="form-group">  
											<label class="" for="avatar">Avatar:</label><BR />
											<?=$user_avatar?><BR /><input name="avatar" id="avatar" type="file" class="default" />
										</div>

									<?php 

										if(get_array_value($item_detail,"user_section_aid","") == "2" ){ 
									
									?>
										
										<!-- <div class="form-group">                                                  
											<label class="required" for="username">Username: </label>
											<input type="text" class="form-control required" id="username" name="username" value="<?=get_array_value($item_detail,"username","")?>" readonly/>
										</div> -->

									<?php 

										}
									
									?>
										<div class="form-group">  
											<label class="required" for="email">Email:</label>
											<input type="text" class="form-control required email" id="email" name="email" value="<?=get_array_value($item_detail,"email","")?>"  readonly />
										</div>

										<!-- <div class="form-group">  
											<label class="required" for="cid">Citizen ID / Passport ID:</label>
											<input type="text" class="form-control required" id="cid" name="cid" value="<?=get_array_value($item_detail,"cid","")?>" <?php if(!is_blank(get_array_value($item_detail,"cid",""))){ echo "readonly"; }?> />
										</div> -->

										<div class="form-group">  
											<label class="required" for="first_name_th">First name:</label>
											<input type="text" class="form-control required" id="first_name_th" name="first_name_th" value="<?=get_array_value($item_detail,"first_name_th","")?>"  />
										</div>
										
										<div class="form-group">  
											<label class="required" for="last_name_th">Last name:</label>
											<input type="text" class="form-control required" id="last_name_th" name="last_name_th" value="<?=get_array_value($item_detail,"last_name_th","")?>"  />
										</div>

										<div class="form-group">  
											<label class="required" for="gender">Gender:</label>
											<?php $gender =  get_array_value($item_detail,"gender",""); ?>
											<div class="required">
												<label class="radio-inline">
													<input type="radio" id="gender_female" name="gender" value="f" checked />Female
												</label>
												<label class="radio-inline">
													<input type="radio" id="gender_male" name="gender" value="m" <?php if(get_array_value($item_detail,"gender","") == "m"){ echo 'checked'; } ?> />Male
												</label>
											</div>
										</div>

										
										
										
										<div class="form-group">  
											<label class="required" for="contact_number">Phone Number:</label>
											<input type="text" class="form-control required" id="contact_number" name="contact_number" value="<?=get_array_value($item_detail,"contact_number","")?>" />
										</div>

										<div class="form-group">
											<label class="required" for="address">Address</label>
											<div class="">
												<textarea class="form-control required" id="address" name="address"><?=get_array_value($item_detail,"address","")?></textarea>
											</div>
										</div>

									<?php if(get_array_value($item_detail,"user_section_aid","") == "1" ){ ?>
										
										<div class="form-group">  
											<label class="required" for="note_2">Company Name:</label>
											<input type="text" class="form-control required" id="note_2" name="note_2" value="<?=get_array_value($item_detail,"note_2","")?>" />
										</div>



										<!-- <div class="form-group ">  
											<label class="required" for="note_3">Company Address:</label>
											<input type="text" class="form-control required" id="note_3" name="note_3" value="<?=get_array_value($item_detail,"note_3","")?>" />
										</div> -->
									<?php
										 } 
									?>
										
										<div class="form-group">  
											<label class="required" for="position">Job Position:</label>
											<input type="text" class="form-control required" id="position" name="position" value="<?=get_array_value($item_detail,"position","")?>" />
										</div>
										
									<?php 
										if(get_array_value($item_detail,"user_section_aid","") > "1" ){ 
									?>
										
										<div class="form-group">
										<?php if(get_array_value($item_detail,"user_section_aid","") == "2" ){ ?> 
											<label class="required" for="department">Department</label>
										<?php } ?>
											<div class="required">
												<?php $department_aid =  get_array_value($item_detail,"department_aid",""); ?>
												<select id="department_aid" name="department_aid" class="form-control required chzn-select" >
													<option value="">Choose Department..</option>
													<?php 
														if(is_var_array($master_department)){ 
															foreach($master_department as $m_item){

													?>
															<option value="<?=get_array_value($m_item,"aid","")?>" <?php if($department_aid == get_array_value($m_item,"aid","")) echo 'selected="selected"';?>><?=get_array_value($m_item,"name","")?>
															</option>
													<?php
													 	} 
													} 
													?>
												</select>
											</div>
										</div>
										<div class="form-group">  
											<label class="required" for="note_4">Company Phone Number:</label>
											<input type="text" class="form-control required" id="note_4" name="note_4" value="<?=get_array_value($item_detail,"note_4","")?>" />
										</div>

									<?php }else{ ?>

										<div class="form-group">  
											<label class="required" for="note_1">Department</label>
											<input type="text" class="form-control required" id="note_1" name="note_1" value="<?=get_array_value($item_detail,"note_1","")?>" />
										</div>

									<?php } ?>

										

										<div class="form-group hide">  
											<label class="" for="display_name">Display name:</label>
											<input type="text" class="form-control" id="display_name" name="display_name" value="<?=get_array_value($item_detail,"display_name","")?>" onkeypress="isEnterGoTo(event,'processSubmit(\'frm_user\')')" />
										</div>
										
										
										
									</div>
									<div class="clear"></div>
									<p class="frm-button">         
										<button type="submit" name="btn_login" id="btn_login" class="btn btn-primary" onClick="processSubmit('frm_user')"> Save My Profile > </button>

									<?php 
										if(get_array_value($item_detail,"user_section_aid","") < "2" ){ 
									?>
											<?=anchor("change-password","Change password")?> 
									<?php 
										}
									?>
									</p>                        
								</form>
							</div>
						</div>
					</div>
				</div>
		
			</section>
		</section>
