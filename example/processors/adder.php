<?php

$x = $Router->getRequest(1);

$y = $Router->getRequest(2);

$z = $x + $y;

?><h1>Add some numbers</h1>

<hr>

<p>
	<?= $x ?> + <?= $y ?> = <strong><?= $z ?></strong>
</p>

<p>
	<small>Change to URL to add different numbers.</small>
</p>
