<?php

class ShowAllOfAction implements Action {
	
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
		$request->setData('title','Összes szavazás, amit létrehoztál');
		$request->setData('linksToPage', 'EditVoting');
		$request->setData('votings', $this->votingListingService->getAllOf($request->getUser()));
		return new PageForward('list');
	}
	
}
