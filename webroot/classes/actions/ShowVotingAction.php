<?php

class ShowVotingAction implements Action {
	
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
		$id = $request->get('id');
		$voting = $this->votingListingService->findFor($id, $request->getUser());
		$request->setData('voting', $voting);
		if ($voting==null) {
			$request->setData('message', 'Invalid voting id');
			return new PageForward('error');
		} else if (is_null($voting->getStopDate())) {
			return new PageForward('vote');
		} else {
			return new PageForward('result');
		}
	}
	
}
