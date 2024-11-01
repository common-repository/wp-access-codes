<?php
if (isset($message)){
    echo "<p class='wpac-message'>$message</p>";
}
?>

<form name="wpac_frontend_form" method="post" action="" class="wpac-form">
	<input type="hidden" name="wpac_frontend_form_submitted" value="Y"/>
	<input class="wpac-form-code-input" type="text" name="wp_access_code_frontend" value="" />
	<input class="wpac-form-submit" type="submit" name="wp_access_code_frontend_submit" value="Verify Access Code"/>
</form>

