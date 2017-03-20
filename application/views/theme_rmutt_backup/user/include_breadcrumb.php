					<div class="breadcrumb clearfix">
                    	<span>You are here:&nbsp;&nbsp;</span>
						
						<a href="<?=site_url('home')?>">Home</a>
						
						<?php 
							switch(thisController){
								case "login_controller" : echo '<span>&nbsp;&nbsp;/&nbsp;&nbsp;</span><a href="'.site_url('login').'" class="current-page">Login</a>'; break;
								case "forgot_controller" : echo '<span>&nbsp;&nbsp;/&nbsp;&nbsp;</span><a href="'.site_url('forgot').'" class="current-page">Forgot password</a>'; break;
								case "registration_controller" : echo '<span>&nbsp;&nbsp;/&nbsp;&nbsp;</span><a href="'.site_url('forgot').'" class="current-page">Registration</a>'; break;
								case "my_account_controller" : echo '<span>&nbsp;&nbsp;/&nbsp;&nbsp;</span><a href="'.site_url('forgot').'" class="current-page">My profile</a>'; break;
							}

						?>
						
						
						
						
                    </div><!--end:breadcrumb-->
