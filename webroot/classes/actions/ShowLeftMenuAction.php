<?php

class ShowLeftMenuAction implements Action {
	
	/**
	 * @var VotingListingService
	 */
	private $votingListingService;

	public function __construct(VotingListingService $votingListingService) {
		$this->votingListingService = $votingListingService;
	}
	
	/**
	 * @param Request $request
	 * @return Forward
	 */
	public function serve(Request $request) {
		$request->setData('interestingFor', $this->votingListingService->getInterestingFor($request->getUser()));
		return new PageForward('leftmenu');
	}
	
}
