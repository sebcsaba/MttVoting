<?php

return array(

	'impl' => array(
		'UserService' => 'DummyUserService',
		'AuthenticationService' => 'DummyUserService',
	),

	'singletons' => array(
		'DummyUserService',
	),
	
);
