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

<p>Email: <input id="email" type="email" name="email"></p>

<p>Pw: <input id="text" type="text" name="text"></p>

<br>

<p><button id="hello">Create account</button></p>
</center>
