<?php

return array(

	'path'               => storage_path('logs'),
	'filename'           => 'monthly-'.date('Y-m').'.log',
	'environment'        => null,
	'level'              => null,
	'order_by_field'     => 'date',
	'order_by_direction' => 'asc'

);