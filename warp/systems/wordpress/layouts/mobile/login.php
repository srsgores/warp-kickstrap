
<form name="loginform" action="<?php echo home_url('/wp-login.php'); ?>" method="post" name="login" id="form-login" >

        <p>
            <input type="text" name="log" placeholder="Username" />
        </p>
        <p>
            <input type="password" name="pwd" placeholder="Password" />
        </p>

        <input type="submit" name="wp-submit" class="button" value="Login" />

    	<input type="hidden" name="redirect_to" value="<?php echo home_url('/wp-admin'); ?>">
		<input type="hidden" name="testcookie" value="1">

</form>