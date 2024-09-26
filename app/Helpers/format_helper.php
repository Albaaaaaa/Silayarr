<?php

function format_ribuan($value) {
	if (!$value)
		return 0;
	return number_format($value, 0, ',' , '.');
}

function format_bytes($bytes, $decimals = 2) {
	// $sz = 'BKMGTP';
	$sz = ['Byte', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb'];
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$sz[$factor];
}