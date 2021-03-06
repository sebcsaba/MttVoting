<?php

class ShowAllForAction implements Action {
	
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
		$request->setData('title','Összes szavazás, amin részt vehetsz');
		$request->setData('linksToPage', 'Voting');
		$request->setData('votings', $this->votingListingService->getAllFor($request->getUser()));
		return new PageForward('list');
	}
	
}
