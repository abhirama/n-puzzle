<?php
	require_once "facebook_credentials_defines.php";
	require_once "n-puzzle_defines.php";
?>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
</head>
<body>
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

	<!-- we can improve this by excluding friends who are already using the app-->
	<fb:serverFbml>
		<script type="text/fbml">
			<fb:fbml>
				<fb:request-form
					method="post"
					type="<?php echo APP_NAME; ?>"
					action="<?php echo PUZZLE_URL; ?>"
					content="Want some exercise for your brain? Try out <?php echo APP_NAME; ?>"
					style="width:100%;">
					<fb:multi-friend-selector	actiontext="Invite your friends to try <?php echo APP_NAME; ?>" 
																		cols="3"
																		style="width:100%;"/>
				</fb:request-form>
			</fb:fbml>
		</script>
	</fb:serverFbml>

</body>
</html>
