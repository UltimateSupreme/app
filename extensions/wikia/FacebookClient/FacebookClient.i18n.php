<?php

$messages = [];

/** English */
$messages['en'] = [

	// New keys introduced with FacebookClient

	'fbconnect-connect-existing' => 'Connect account with Facebook',
	'fbconnect-connect-login-legend' => 'Login to your existing account',
	'fbconnect-connect-username-label' => 'Username:',
	'fbconnect-connect-password-label' => 'Password:',
	'fbconnect-wrong-pass-msg' => 'The password you have entered is incorrect',
	'fbconnect-graphapi-not-configured' => 'Facebook App ID and App secret missing from configuration.',

	// Keys copied from FBConnect and in use

	'fbconnect-desc' => 'Enables users to [[Special:FacebookConnect|Connect]] with their [http://www.facebook.com Facebook] accounts. Offers authentification based on Facebook groups and the use of FBML in wiki text.',
	'fbconnect-connect' => 'Log in with Facebook Connect',
	'fbconnect-connect-simple' => 'Connect',
	'fbconnect-convert' => 'Connect this account with Facebook',
	'fbconnect-or' => 'OR',
	'fbconnect-error' => 'Verification error',
	'fbconnect-errortext' => 'Yikes! It looks like that didn\'t work out. Please try again.',
	'fbconnect-disconnect-link' => 'You can also <a id="fbConnectDisconnect" href="#"> disconnect your Wikia account from Facebook.</a> You will be able to continue using your Wikia account as normal, with your history (edits, points, achievements) intact.',
	'fbconnect-disconnect-done' => 'Disconnecting <span id="fbConnectDisconnectDone">... done! </span>',
	'fbconnect-disconnect-info' => 'We have emailed a new password to use with your account - you can log in with the same username as before. Hooray!',
	'fbconnect-disconnect-info-existing' => 'You can still log in using your user name and password, as usual.',
	'fbconnect-unknown-error' => 'Unknown error, please try again.',
	'fbconnect-passwordremindertitle' => 'Your Wikia account is now disconnected from Facebook!',
	'fbconnect-passwordremindertitle-exist' => 'Your Wikia account is now disconnected from Facebook!',
	'fbconnect-passwordremindertext' => 'Hi,
It looks like you\'ve just disconnected your Wikia account from Facebook. We\'ve kept all of your history, edit points and achievements intact, so don\'t worry!

You can use the same username as before, and we\'ve generated a new password for you to use. Here are your details:

Username: $2
Password: $3

The replacement password has been sent only to you at this email address.

Thanks,

The Wikia Community Team',
	'fbconnect-passwordremindertext-exist' => 'Hi,
It looks like you\'ve just disconnected your Wikia account from Facebook. We\'ve kept all of your history, edit points and achievements intact, so don\'t worry!

You can use the same username and password as you did before you connected.

Thanks,

The Wikia Community Team',
	'fbconnect-wikia-login-w-facebook' => 'Log in / Sign up with Facebook Connect',
	'fbconnect-wikia-signup-w-facebook' => 'Sign up with Facebook',
	'fbconnect-wikia-login-or-create' => 'Log in / Create an account',
	'fbconnect-logout-confirm' => 'Choosing to cancel will log you out of Wikia and Facebook. Do you want to continue?',
	'prefs-fbconnect-prefstext' => 'Facebook Connect',
	'prefs-fbconnect-status-prefstext' => 'Status',
];

/**
 * Message documentation (Message documentation)
 */
$messages['qqq'] = [
	// Keys in use

	'fbconnect-connect-existing' => 'Title for page that asks a user to login to their Wikia account and link it to their Facebook account',
	'fbconnect-connect-login-legend' => 'Text that shows as a label for a box containing Wikia username and password text fields',
	'fbconnect-connect-username-label' => 'Label for the Wikia username field on the login box to link your Wikia account to Facebook',
	'fbconnect-connect-password-label' => 'Label for the Wikia password field on the login box to link your Wikia account to Facebook',
	'fbconnect-wrong-pass-msg' => 'Error text that appears when the user has entered their username/password incorrectly',
	'fbconnect-graphapi-not-configured' => 'Text that appears when the Facebook credentials within the Wikia app are incorrect.',

	'fbconnect-desc' => 'Short description of the FBConnect extension, shown in [[Special:Version]]. Do not translate or change links.',
	'fbconnect-or' => 'This is just the word "OR" in English, used to separate the Facebook Connect login option from the normal Wikia login options on the AJAX login dialog box.',
	'fbconnect-disconnect-info-existing' => 'Confirmation message shown to users who have disconnected their account from Facebook but had a regular Wikia account.',
	'fbconnect-connect' => '',
	'fbconnect-connect-cancel' => '',
	'fbconnect-connect-canceltext' => '',
	'fbconnect-connect-simple' => '',
	'fbconnect-convert' => 'Title text above a Facebook login button.  Shown on the Facebook Connect tab of the users preferences page.',
	'fbconnect-disconnect' => '',
	'fbconnect-disconnect-done' => '',
	'fbconnect-disconnect-info' => '',
	'fbconnect-disconnect-link' => '',
	'fbconnect-error' => '',
	'fbconnect-errortext' => '',
	'fbconnect-passwordremindertext' => '',
	'fbconnect-passwordremindertext-exist => ',
	'fbconnect-passwordremindertitle' => '',
	'fbconnect-passwordremindertitle-exist' => '',
	'fbconnect-prefstext' => '',
	'fbconnect-prefstext/fbconnect-status-prefstext' => '',
	'fbconnect-unknown-error' => '',
	'fbconnect-wikia-login-or-create' => '',
	'fbconnect-wikia-login-w-facebook' => '',
	'fbconnect-wikia-signup-w-facebook' => '',
	'fbconnect-logout-confirm' => 'Message shown if a user attempts to cancel process of connecting Wikia account with Facebook account. It informs the user that proceeding with this action will result in logout from Facebook and asks for confirmation.',
];
