<?php

return config_merge(array(

	'url' => '',
	'authentication_url' => '',
	'joomla_config_file' => '',

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
		'singletons' => array(
			'DbConnectionParameters',
		),
	),

),require_once('config.local.php'));
