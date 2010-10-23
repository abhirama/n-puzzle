<?php
	require_once "facebook_common.php";

	$uid = getUserId();

	if ($uid) {
	 include 'n-puzzle.php';
	} else {
	 $params = array(
	 'next'          =>    PUZZLE_URL,
	 'cancel_url'    =>    PUZZLE_URL,
	 'fbconnect'     =>    0,
	 'canvas'        =>    1,
	 'req_perms'     =>    ''
	 );

	 $loginUrl = $facebook->getLoginUrl($params);

	 echo '<script type="text/javascript">window.top.location="'. $loginUrl . '"</script>';
	}

?>

