<div class="wrap">
	
	<div id="icon-options-general" class="icon32"></div>
	<h2>WP Access Codes</h2>
	
	<div id="poststuff">
	
		<div id="post-body" class="metabox-holder columns-2">
		
			<!-- main content -->
			<div id="post-body-content">
				
				<div class="meta-box-sortables ui-sortable">
					
					<div class="postbox">
					
						<h3><span>Add new fixed access code</span></h3>
						<div class="inside">
							
							
							<form name="wpac_form" method="post" action="">
								<input type="hidden" name="wpac_form_submitted" value="Y"/>
								<table class="form-table">
                                    <tr>
                                        <td><label for="wp_access_code">Access Code </label></td>
                                        <td><input type="text" name="wp_access_code" value="" class="regular-text"></td>
                                    </tr>

                                    <tr>
                                        <td><label for="wpac_role">Wordpress Role</label></td>
                                        <td><?php echo wpac_wp_role_dropdown(); ?></td>
                                    </tr>

                                    <?php if(wpac_pp_activated()) :?>
									<tr>
										<td><label for="wpac_role">Press Permit Groups</label></td>
										<td><?php echo wpac_pp_groups_checklist();?></td>
									</tr>
                                    <?php endif ?>

                                    <tr>
                                        <td><label for="wp_access_code">Usage Limit</label></td>
                                        <td><input type="text" name="wpac_limit" value="" class="regular-text"></td>
                                    </tr>

                                    <tr>
                                        <td><label for="wp_access_code">Success Message</label></td>
                                        <td><input type="text" name="wpac_message" value="Access Approved." class="regular-text"></td>
                                    </tr>

								</table>
								<input class="button-primary" type="submit" name="wpac_combinination_submit" value="Add Access Code" />
							</form>

						</div> <!-- .inside -->
					
					</div> <!-- .postbox -->
					
				</div> <!-- .meta-box-sortables .ui-sortable -->
				
			</div> <!-- post-body-content -->
			
			<!-- sidebar -->
			<div id="postbox-container-1" class="postbox-container">
				
				<div class="meta-box-sortables">
					
					<div class="postbox">
					
						<h3><span>Active Codes</span></h3>
						<div class="inside">
                            <?php wpac_active_codes_list() ?>
						</div> <!-- .inside -->
						
					</div> <!-- .postbox -->
					
				</div> <!-- .meta-box-sortables -->
				
			</div> <!-- #postbox-container-1 .postbox-container -->
			
		</div> <!-- #post-body .metabox-holder .columns-2 -->
		
		<br class="clear">
	</div> <!-- #poststuff -->
	
</div> <!-- .wrap -->
