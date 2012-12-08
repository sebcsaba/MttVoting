<?php

return array(

	'db' => array(),

	'impl' => array(
		'UserService' => 'DummyUserService',
		'VotingListingService' => 'VotingListingServiceImpl',
		'VotingAdminService' => 'VotingAdminServiceImpl',
		'VotingService' => 'VotingServiceImpl',
	),
	
	'singletons' => array(
		'DbConnectionParameters',
	),

);
