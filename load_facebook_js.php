<?php  
	require_once 'facebook_credentials_defines.php';
?>

<div id="fb-root"></div>
<script type="text/javascript">
	window.fbAsyncInit = function() {
		FB.init({appId: "<?php echo APP_ID; ?>", status: true, cookie: true,
						 xfbml: true});
	};
	(function() {
		var e = document.createElement('script'); e.async = true;
		e.src = document.location.protocol +
			'//connect.facebook.net/en_US/all.js';
		document.getElementById('fb-root').appendChild(e);
	}());
</script>
	

