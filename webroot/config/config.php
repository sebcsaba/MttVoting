<?php

return config_merge(array(

	'url' => '',
	'authentication_url' => '',

	'db' => array(
		'protocol' => 'mysql',
	),

	'impl' => array(
		'ApplicationHandler' => 'ApplicationHandlerImpl',
		'UserService' => 'TohuUserService',
		'VotingListingService' => 'VotingListingServiceImpl',
		'VotingAdminService' => 'VotingAdminServiceImpl',
		'VotingService' => 'VotingServiceImpl',
		'DbDialect' => 'MySqlDbDialect',
		'DbEngine' => 'MySqlDbEngine',
	),
	
	'singletons' => array(
		'DbConnectionParameters',
	),

),require_once('config.local.php'));
