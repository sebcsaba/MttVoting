<?php

class ShowActivesOfAction implements Action {
	
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
		$request->setData('title','Általad létrehozott, még nyitott szavazások');
		$request->setData('linksToPage', 'EditVoting');
		$request->setData('votings', $this->votingListingService->getAllOpenedOf($request->getUser()));
		return new PageForward('list');
	}
	
}
