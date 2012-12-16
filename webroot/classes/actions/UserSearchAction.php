<?php

class UserSearchAction implements Action {

	/**
	 * @var UserService
	 */
	private $userService;
	
	public function __construct(UserService $userService) {
		$this->userService = $userService;
	}
	
	/**
	 * @param Request $request
	 * @return Forward
	 */
	public function serve(Request $request) {
		$users = $this->userService->findUsersByName($request->get('term'));
		print json_encode($this->createResult($users));
		return null;
	}
	
	private function createResult(array $users) {
		$result = array();
		foreach ($users as $user) {
			$result []= array(
				'label' => $user->getLoginName(),
				'value' => $user->getUserId(),
			);
		}
		return $result;
	}
	
}
