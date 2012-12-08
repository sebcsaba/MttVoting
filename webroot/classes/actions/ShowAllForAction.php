<?php

class ShowAllForAction implements Action {
	
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
		$request->setData('title','Összes szavazás, amin részt vehetsz');
		$request->setData('linksToPage', 'Voting');
		$request->setData('votings', $this->votingListingService->getAllFor($request->getUser()));
		return new PageForward('list');
	}
	
}
