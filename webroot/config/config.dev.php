<?php

return array(

	'di' => array(

		'impl' => array(
			'UserService' => 'DummyUserService',
			'AuthenticationService' => 'DummyUserService',
		),

		'singletons' => array(
			'DummyUserService',
		),
	
	),
	
);
