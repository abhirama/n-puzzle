<?php
	require_once 'facebook.php';
	require_once 'facebook_credentials_defines.php';

	// Create our Application instance.
	$facebook = new Facebook(array(
	 'appId'  => APP_ID,
	 'secret' => SECRET_KEY,
	 'cookie' => true,
	));

	//Get current user's Facebook session
	$session = $facebook->getSession();

	function getUserId() {
		global $session;
		global $facebook;
		if ($session) {
			try {
				return $uid = $facebook->getUser();
			} catch (FacebookApiException $e) {
				error_log($e);
			}
		}

		return 0;
	}
?>
