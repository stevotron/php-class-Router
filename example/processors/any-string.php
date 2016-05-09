<?php

$string = $Router->getRequest(1);

?><h1>Retrieving a string</h1>

<hr>

<p>
	You passed through &quot;<?= htmlspecialchars($string) ?>&quot;!
</p>

<p>
	<small>Change to URL to pass through different values.</small>
</p>
