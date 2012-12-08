<?php

class InitAction implements Action {
	
	/**
	 * @var UserService
	 */
	private $userService;
	
	/**
	 * @var VotingListingService
	 */
	private $votingListingService;

	public function __construct(UserService $userService, VotingListingService $votingListingService) {
		$this->userService = $userService;
		$this->votingListingService = $votingListingService;
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
