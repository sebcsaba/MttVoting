<?php

class InitAction implements Action {
	
	private $userService;
	private $votingListingService;

	public function __construct() {
		// TODO use DI
		$this->userService = new DummyUserService();
		$this->votingListingService = new VotingListingServiceImpl(new Database());
	}
	/**
	 * @param Request $request
	 * @return Forward
	 */
	public function serve(Request $request) {
		$user = $this->userService->authenticate();
		$request->setData('username', $user->getLoginName());
		$request->setData('answerableFor', $this->votingListingService->getAnswerableFor($user));
		$request->setData('openedOf', $this->votingListingService->getAllOpenedOf($user));
		return new PageForward('index');
	}
	
}
