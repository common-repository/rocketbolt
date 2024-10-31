<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<style>
	.rb-logo {
		width: 175px;
		margin: auto;
		display: block;
	}

	.rb-logo img {
		width: 175px;
		margin: 20px auto;
	}

	.rb-login {
		border: 1px solid #ccc;
	    padding: 0 20px 20px;
	    max-width: 300px;
	    border-radius: 4px;
	    margin: 20px auto;
	}

	#rb-activate-form, #rb-register-form {
		display: none;
	}

	.rb-header-error {
		text-align: center;
	    background-color: #d54e21;
	    color: #fff;
	    border-radius: 3px;
	    padding: 5px;
	    margin-top: 20px;
	}

	.rb-form-error {
	    color: #d54e21;
	    font-weight: normal;
	    float: right;
	    font-size: 11px;
	    margin-top: 0px;
	}

	#rb-activate-error, #rb-register-error, #rb-account-activated {
		display: none;
	}

	.rb-activate-now {
		font-size: 10px;
	    background-color: #0085ba;
	    padding: 0 10px;
	    height: 19px;
	    display: inline-block;
	    border-radius: 3px;
	    margin-left: 8px;
	    margin-top: 15px;
	    cursor: pointer;
	}

	.rb-activate-now:hover {
		text-decoration: underline;
	}

	.rb-login label {
	    display: block;
	    font-weight: bold;
	    margin-top: 10px;
	    margin-bottom: 5px;
	}

	.rb-login input {
		width: 100%;
	}

	.rb-login .rb-btn {
		margin-top: 20px;
	    width: 100%;
	    text-align: center;
	}

	.rb-login .rb-switch-form {
		text-align: center;
	    font-size: 12px;
	    color: #999;
	    margin-top: 10px;
	    cursor: pointer;		
	}

	.rb-login .rb-switch-form:hover {
		text-decoration: underline;
		color: #444;
	}
</style>

<div class="wrap">

	<a href="http://rocketbolt.com" class="rb-logo" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/logo-round.png'; ?>"></a>

	<div class="rb-login">
		<div id="rb-active">
			<p style="margin-bottom: 0;">RocketBolt is installed on <strong><?php echo $_SERVER['HTTP_HOST']; ?></strong>. To review website activity or configure settings, visit your RocketBolt account.</p>

			<a href="https://app.rocketbolt.com" target="_blank" class="button-primary rb-btn">Open RocketBolt</a>
			<div id="rb-re-install" class="rb-switch-form">Reinstall RocketBolt</div>
		</div>
		<div id="rb-activate-form">
			<p style="text-align: center;">Enter login credentials to activate RocketBolt on:<br /><strong><?php echo $_SERVER['HTTP_HOST']; ?></strong></p>

			<div id="rb-activate-error" class="rb-header-error"></div>

			<label>Email:</label>
			<input type="text" id="rb-activate-email" />
			<label>Password:</label>
			<input type="password" id="rb-activate-password" />

			<input type="hidden" id="rb-activate-site" value="<?php echo $_SERVER['HTTP_HOST']; ?>">

			<div id="rb-activate-btn" class="button-primary rb-btn" onclick="activate_rocketbolt()">Activate RocketBolt</div>
			<div id="rb-show-register" class="rb-switch-form">Need an account?</div>
		</div>

		<div id="rb-register-form">
			<p style="text-align: center;">Create an account to activate RocketBolt on:<br /><strong><?php echo $_SERVER['HTTP_HOST']; ?></strong></p>

			<div id="rb-account-activated" class="rb-header-error">Account created and activation link emailed. Activating website in <span id="rb-register-countdown">5</span> seconds<span class="rb-activate-now" onclick="activate_rocketbolt()">activate now</span></div>

			<div id="rb-register-inner-box">
				<div id="rb-register-error" class="rb-header-error"></div>

				<label>First name: <div id="rb-fname-error" class="rb-form-error"></div></label>
				<input type="text" id="rb-register-fname" />
				<label>Last name: <div id="rb-lname-error" class="rb-form-error"></div></label>
				<input type="text" id="rb-register-lname" />
				<label>Email Address: <div id="rb-email-error" class="rb-form-error"></div></label>
				<input type="text" id="rb-register-email" />
				<label>Company: <div id="rb-org-error" class="rb-form-error"></div></label>
				<input type="text" id="rb-register-org" />

				<div id="rb-register-btn" class="button-primary rb-btn" onclick="register_rocketbolt()">Register &amp; Activate RocketBolt</div>
				<div id="rb-show-activation" class="rb-switch-form">Already registered?</div>
			</div>
		</div>

		<div id="rb-limit-message" style="display: none;">
			<div class="rb-header-error">
				Website limit reached. To link additional sites, please upgrade your RocketBolt account.
			</div>

			<a href="https://app.rocketbolt.com/settings/organization/plans" target="_blank" class="button-primary rb-btn">Upgrade RocketBolt</a>
		</div>
	</div>

	<!-- Data form for inserting code snippet data into WP DB -->
	<form method="post" action="options.php" style="display: none !important;">
		<?php wp_nonce_field('update-options'); ?>
		<?php settings_fields('rocketbolt'); ?>

		<table class="form-table">

			<tr valign="top">
				<th scope="row">Organization ID:</th>
				<td><input type="text" name="rocketbolt_organization_id" value="<?php echo get_option('rocketbolt_organization_id'); ?>" id="rb-organization-id" /></td>
				<td><input type="text" name="rocketbolt_property_id" value="<?php echo get_option('rocketbolt_property_id'); ?>" id="rb-property-id" /></td>
			</tr>

			</tr>

		</table>

		<input type="hidden" name="action" value="update" />

		<p class="submit">
			<input type="submit" class="button-primary" id="rb-submit-values" value="<?php _e('Save Changes') ?>" />
		</p>

	</form>
</div>

<!-- JS for signup and activation processes -->
<script>
	// Get current organization and property values
	var org = parseInt(document.getElementById('rb-organization-id').value);
	var prop = parseInt(document.getElementById('rb-property-id').value);

	// If org or prop not available, show signup form
	if((org === 0) || (prop === 0)) {
		hideActiveMessage();
		showSignupForm();
	}

	// Show activation form from active message
	document.getElementById('rb-re-install')
		.addEventListener('click', function(event) {
		hideActiveMessage();
		showActivationForm();
	});

	// Show activation from from signup form
	document.getElementById('rb-show-activation')
		.addEventListener('click', function(event) {
		hideSignupForm();
		showActivationForm();
	});

	// Show register form from activation form
	document.getElementById('rb-show-register')
		.addEventListener('click', function(event) {
		hideActivationForm();
		showSignupForm();
	});


	// Show active message
	function showActiveMessage() {
		document.getElementById('rb-active').style.display = 'block';
	}
	// Hide active message
	function hideActiveMessage() {
		document.getElementById('rb-active').style.display = 'none';
	}
	// Show activation form
	function showActivationForm() {
		document.getElementById('rb-activate-form').style.display = 'block';
	}
	// Hide activation form
	function hideActivationForm() {
		document.getElementById('rb-activate-form').style.display = 'none';
	}
	// Show signup form
	function showSignupForm() {
		document.getElementById('rb-register-form').style.display = 'block';
	}
	// Hide signup form
	function hideSignupForm() {
		document.getElementById('rb-register-form').style.display = 'none';
	}

	// Trigger form submits with enter key
	document.getElementById('rb-activate-password')
		.addEventListener('keyup', function(event) {
	    event.preventDefault();
	    if (event.keyCode == 13) {
	        document.getElementById('rb-activate-btn').click();
	    }
	});
	document.getElementById('rb-register-org')
		.addEventListener('keyup', function(event) {
	    event.preventDefault();
	    if (event.keyCode == 13) {
	        document.getElementById('rb-register-btn').click();
	    }
	});

	// Activation form
	function activate_rocketbolt() {
		// Disable button
		var activateBtn = document.getElementById('rb-activate-btn');
		activateBtn.disabled = true;
		activateBtn.innerHTML = 'Processing...';

		var email = document.getElementById('rb-activate-email').value;
		var pass = document.getElementById('rb-activate-password').value;
		var site = document.getElementById('rb-activate-site').value;

		// Hide error message in case it's visible
		var errorDiv = document.getElementById('rb-activate-error');
		errorDiv.style.display = 'none';

	    var xhr = new XMLHttpRequest();

		xhr.open('POST', 'https://app.rocketbolt.com/framed/auth_activate/wordpress');
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onload = function() {
		    if (xhr.status === 200) {
		    	// Parse response
		    	var data = JSON.parse(xhr.responseText);
		        
		        // Error handling
		        if(data.result === 'error') {
		        	// Set error message
		        	if(data.type === 'banned') {
		        		errorDiv.innerHTML = 'Account disabled. Email <a href="mailto:team@rocketbolt.com">team@rocketbolt.com</a> for more info.';
		        	} else if(data.type === 'properties limit') {
		        		// Hide activation form
		        		document.getElementById('rb-activate-form').style.display = 'none';

		        		// Show limit reached message
		        		document.getElementById('rb-limit-message').style.display = 'block';
		        	} else if(data.type === 'bad url') {
		        		errorDiv.innerHTML = 'Unable to identify website. Email <a href="mailto:team@rocketbolt.com">team@rocketbolt.com</a> for help.';
		        	} else { // Handle all other errors
		        		errorDiv.innerHTML = 'Invalid user or password';
		        	}

		        	// Show error message
		        	errorDiv.style.display = 'block';

		        	activateBtn.disabled = true;
					activateBtn.innerHTML = 'Activate RocketBolt';
		        } else { // Activation successful
		        	// Set correct organization and property
					document.getElementById('rb-organization-id').value = data.organization;
					document.getElementById('rb-property-id').value = data.property;

					// Submit update
					document.getElementById('rb-submit-values').click();
		        }
		    }
		    else if (xhr.status !== 200) {
		        errorDiv.innerHTML = 'Could not reach RocketBolt\'s servers. Contact <a href="mailto:team@rocketbolt.com">team@rocketbolt.com</a> for help.';

		        // Show error message
		    	errorDiv.style.display = 'block';

		    	activateBtn.disabled = true;
				activateBtn.innerHTML = 'Activate RocketBolt';
		    }
		};
		xhr.send(encodeURI('email=' + email + '&password=' + pass + '&site=' + site));
	}

	// Registration form
	function register_rocketbolt() {
		// Disable button
		var registerBtn = document.getElementById('rb-register-btn');
		registerBtn.disabled = true;
		registerBtn.innerHTML = 'Processing...';

		var fname = document.getElementById('rb-register-fname').value;
		var lname = document.getElementById('rb-register-lname').value;
		var email = document.getElementById('rb-register-email').value;
		var org = document.getElementById('rb-register-org').value;

		// Create default org name if one not entered
		if(org === '') {
			org = fname + 'Land';
		}

		// Empty all error messages in case any are visible
		var errorDiv = document.getElementById('rb-activate-error');
		errorDiv.style.display = 'none';
		var fnameError = document.getElementById('rb-fname-error');
		fnameError.innerHTML = '';
		var lnameError = document.getElementById('rb-lname-error');
		lnameError.innerHTML = '';
		var emailError = document.getElementById('rb-email-error');
		emailError.innerHTML = '';
		var orgError = document.getElementById('rb-org-error');
		orgError.innerHTML = '';

	    var xhr = new XMLHttpRequest();

		xhr.open('POST', 'https://app.rocketbolt.com/auth/integration_register/wordpress');
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onload = function() {
		    if (xhr.status === 200) {
		    	// Parse response
		    	var data = JSON.parse(xhr.responseText);
		        
		        // Error handling
		        if(data.result === 'error') {
		        	// Set error messages
		        	fnameError.innerHTML = data.first_name;
		        	lnameError.innerHTML = data.last_name;
		        	emailError.innerHTML = data.email;
		        	orgError.innerHTML = data.organization;

		        	// Re-enable button
		        	registerBtn.disabled = false;
					registerBtn.innerHTML = 'Register &amp; Activate RocketBolt';
		        } else { // Activation successful
		        	// Fill in activation form details
		        	document.getElementById('rb-activate-email').value = email;
					document.getElementById('rb-activate-password').value = data.temp_key;

					// Hide form component
					document.getElementById('rb-register-inner-box').style.display = 'none';

					// Show activation countdown
					document.getElementById('rb-account-activated').style.display = 'block';

					// Start activation countdown timer
					activation_countdown();
		        }
		    }
		    else if (xhr.status !== 200) {
		        errorDiv.innerHTML = 'Could not reach RocketBolt\'s servers. Contact <a href="mailto:team@rocketbolt.com">team@rocketbolt.com</a> for help.';

		        // Show error message
		    	errorDiv.style.display = 'block';
		    }
		};
		xhr.send(encodeURI('first_name=' + fname + '&last_name=' + lname + '&email=' + email + '&organization=' + org));
	}

	function activation_countdown() {
		// Get counter number element
		var counter = document.getElementById('rb-register-countdown');
		
		// Get current counter value
		var count = parseInt(counter.innerHTML);

		// If counter is zero, initiate activation
		if(count === 0) {
			activate_rocketbolt();
		} else { // Otherwise remove a second
			setTimeout(function() {
				counter.innerHTML = count - 1;
				activation_countdown();
			}, 1000);
		}
	}
</script>