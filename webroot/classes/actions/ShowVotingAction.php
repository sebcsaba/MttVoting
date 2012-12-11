<?php

class ShowVotingAction implements Action {
	
	/**
	 * @var VotingListingService
	 */
	private $votingListingService;
	
	/**
	 * @var VotingService
	 */
	private $votingService;
	
	public function __construct(VotingListingService $votingListingService, VotingService $votingService) {
		$this->votingListingService = $votingListingService;
		$this->votingService = $votingService;
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
			$request->setData('message', 'Nincs elérhető szavazás a megadott azonosítóval');
			return new PageForward('error');
		} else if ($this->votingService->isVotingAnswerableForUser($voting, $request->getUser())) {
			return new PageForward('vote');
		} else if (is_null($voting->getStopDate())) {
			return new PageForward('voted');
		} else {
			return new ActionForward('ShowResultAction');
		}
	}
	
}
