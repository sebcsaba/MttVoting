<?php

class VoteAction implements Action {
	
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
		$id = $request->get('voting_id');
		$voting = $this->votingListingService->findFor($id, $request->getUser());
		if (is_null($voting)) {
			$request->setData('message', 'Nincs elérhető szavazás a megadott azonosítóval');
			return new PageForward('error');
		}
		$a = $request->get('a');
		if (!array_key_exists($a, $voting->getAnswers())) {
			$request->setData('message', 'Nem megfelelő választ adott meg!');
			return new PageForward('error');
		}
		return new ActionForward('ShowAllForAction');
	}
	
}
