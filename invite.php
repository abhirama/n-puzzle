<?PHP
	require_once "facebook_common.php";
	require_once "n-puzzle_defines.php";

	$uid = getUserId();
?>

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<link rel="stylesheet" href="style.css" type="text/css" media="screen" charset="utf-8" />
</head>
<body onload="init()">
	<?php
	if ($uid) {
			$_allFriends = $facebook->api('/me/friends');
			$allFriends = array();

			if (is_array($_allFriends) && count($_allFriends)) {
				foreach ($allFriends as $friend) {
					$allFriends[] = $friend["id"];
				}
			}

			// Retrieve array of friends who've already authorized the app.
			$_appUsingfriends = $facebook->api(array(
				'method' => 'fql.query',
				'query' => 'SELECT uid FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1='.$uid.') AND is_app_user = 1',
			));

			// Extract the user ID's returned in the FQL request into a new array.
			$appUsingfriends = array();
			if (is_array($_appUsingfriends) && count($_appUsingfriends)) {
				foreach ($_appUsingfriends as $friend) {
					$appUsingfriends[] = $friend['uid'];
				}
			}


			if (count($appUsingfriends) == count($allFriends)) {
				// Convert the array of friends into a comma-delimited string.
				$friends = implode(',', $friends);
				//todo has to be removed after testing
				$friends = "";
				// Prepare the invitation text that all invited users will receive.

				$content = sprintf(FACEBOOK_INVITE_CONTENT);
			?>

			<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script>
			<script type="text/javascript">
				window.onload = function()
				{
					FB_RequireFeatures(["XFBML"], function()
					{
						FB.Facebook.init("<?php echo APP_ID; ?>", "xd_receiver.htm");
					});
				};
			</script>

		<fb:serverFbml>
			<script type="text/fbml">
				<fb:fbml>
					<fb:request-form
						method="post"
						action="n-puzzle.php"
						type="<?php echo APP_NAME; ?>"
						content="<?php echo htmlentities($content, ENT_COMPAT, 'UTF-8'); ?>" 
						style="width:100%;">
						<fb:multi-friend-selector	actiontext="Invite your friends to try <?php echo APP_NAME; ?>" 
																			exclude_ids="<?php echo $friends; ?>" 
																			cols="3"
																			style="width:100%;"/>
					</fb:request-form>
				</fb:fbml>
			</script>
		</fb:serverFbml>

		<?PHP
			} else {
				//todo maybe we can say that all your friends are already using the app
				require_once "n-puzzle.php";
			}
		}  else {
			echo "Looks like your session expired :(. Please take the quiz again";	
		}

		?>
</body>
</html>
