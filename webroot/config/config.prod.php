<?php

return array(

	'lifecycle' => 'production',
	
	'di' => array(

		'impl' => array(
			'UserService' => 'TohuUserService',
			'AuthenticationService' => 'TohuAuthenticationService',
		),

	),
	
);
