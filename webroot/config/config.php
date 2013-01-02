<?php

return config_merge(array(

	'url' => '',
	'authentication_url' => '',
	'joomla_config_file' => '',
	
	'lifecycle' => 'unknown', // values: production, development, test
	
	'db' => array(
		'protocol' => 'mysql',
	),
	
	'tohu_db' => array(
		'protocol' => 'mysql',
	),
	
	'di' => array(
		'impl' => array(
			'ApplicationHandler' => 'ApplicationHandlerImpl',
			'UserService' => 'TohuUserService',
			'AuthenticationService' => 'TohuAuthenticationService',
			'VotingListingService' => 'VotingListingServiceImpl',
			'VotingAdminService' => 'VotingAdminServiceImpl',
			'VotingService' => 'VotingServiceImpl',
			'VotingResultService' => 'VotingResultServiceImpl',
			'DbDialect' => 'MySqlDbDialect',
			'DbEngine' => 'MySqlDbEngine',
		),
		'nonsingletons' => array(
			'DbEngine'
		),
		'specials' => array(
			'Database' => new DatabaseDIHandler(),
			'ToHuDatabase' => new DatabaseDIHandler('tohu_db'),
		),
	),

),require_once('config.local.php'));
