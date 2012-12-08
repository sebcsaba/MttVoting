<?php

return config_merge(array(

	'db' => array(
		'protocol' => 'mysql',
	),

	'impl' => array(
		'UserService' => 'TohuUserService',
		'VotingListingService' => 'VotingListingServiceImpl',
		'VotingAdminService' => 'VotingAdminServiceImpl',
		'VotingService' => 'VotingServiceImpl',
	),
	
	'singletons' => array(
		'DbConnectionParameters',
	),

),require_once('config.local.php'));
