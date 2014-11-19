<?php
//\OCP\Util::addScript('firefoxsync', 'vendor/requirejs/require');
//\OCP\Util::addScript('firefoxsync', 'vendor/fxa-content-server/require_config');
//\OCP\Util::addScript('firefoxsync', 'main');


\OCP\Util::addStyle('firefoxsync', 'style');

?>

<!-- <script data-main="js/scripts/main.js" src=" js/scripts/require_config.js"></script> -->
<script data-main="js/scripts/require_config.js" src="js/bower_components/requirejs/require.js"></script>


<center>
<p><b>FirefoxSync registration for user <?php p($_['user']) ?></b></p>

<br>
<form>
<div>Email: <input id="email" type="email" name="email"></div>

<div>Pw: <input id="password" type="text" name="text"></div>

<br>

<div><button id="submit_btn" onclick="createAccount($('#email').val(), $('#password').val())">Create account</button></div>
</form>
</center>
