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
		'nonsingletons' => array(
			'DbEngine'
		),
		'specials' => array(
			'ToHuDatabase' => function(DI $di, $interfaceName, $className){
				$config = $di->get('Config');
				$params = DbConnectionParameters::createFromArray($config->get('tohu_db'));
				return new ToHuDatabase($di->get('DbEngine',array('DbConnectionParameters'=>$params)));
			},
			'Database' => function(DI $di, $interfaceName, $className){
				$config = $di->get('Config');
				$params = DbConnectionParameters::createFromArray($config->get('db'));
				return new ToHuDatabase($di->get('DbEngine',array('DbConnectionParameters'=>$params)));
			},
		),
	),

),require_once('config.local.php'));
