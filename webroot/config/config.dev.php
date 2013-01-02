<?php

return array(

	'lifecycle' => 'development',
	
	'di' => array(

		'impl' => array(
			'UserService' => 'DummyUserService',
			'AuthenticationService' => 'DummyUserService',
		),

	),
	
);
