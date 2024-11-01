<?php
$access_code_object = wpac_get_access_code_object($_GET['access_code']);
?>

<div class="wrap">
	
	<div id="icon-options-general" class="icon32"></div>
	<h2>WP Access Codes</h2>
	
	<div id="poststuff">
	
		<div id="post-body" class="metabox-holder columns-2">
		
			<!-- main content -->
			<div id="post-body-content">
				
				<div class="meta-box-sortables ui-sortable">
					
					<div class="postbox">
					
						<h3><span>Edit access code</span></h3>
						<div class="inside">
							
							
							<form name="wpac_form" method="post" action="">
								<input type="hidden" name="wpac_form_submitted" value="Y"/>
								<table class="form-table">
                                    <tr>
                                        <td><label for="wp_access_code">Access Code </label></td>
                                        <td><input type="text" name="wp_access_code" value="<?php echo $access_code_object->code ?>" class="regular-text"></td>
                                    </tr>

                                    <tr>
                                        <td><label for="wpac_role">Wordpress Role</label></td>
                                        <td><?php echo wpac_wp_role_dropdown($access_code_object->wp_role); ?></td>
                                    </tr>

                                    <?php if(wpac_pp_activated()) :?>
									<tr>
										<td><label for="wpac_role">Press Permit Groups</label></td>
										<td><?php echo wpac_pp_groups_checklist($access_code_object->pp_groups);?></td>
									</tr>
                                    <?php endif ?>

                                    <tr>
                                        <td><label for="wp_access_code">Remaining Uses</label></td>
                                        <td><input type="text" name="wpac_limit" value="<?php echo $access_code_object->remaining ?>" class="regular-text"></td>
                                    </tr>

                                    <tr>
                                        <td><label for="wp_access_code">Success Message</label></td>
                                        <td><input type="text" name="wpac_message" value="<?php echo $access_code_object->message ?>" class="regular-text"></td>
                                    </tr>

								</table>
								<input class="button-primary" type="submit" name="wpac_combinination_submit" value="Update Access Code" />
							</form>

						</div> <!-- .inside -->
					
					</div> <!-- .postbox -->
					
				</div> <!-- .meta-box-sortables .ui-sortable -->
				
			</div> <!-- post-body-content -->


			
		</div> <!-- #post-body .metabox-holder .columns-2 -->
		
		<br class="clear">
	</div> <!-- #poststuff -->
	
</div> <!-- .wrap -->
