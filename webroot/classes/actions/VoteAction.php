<?php

class VoteAction implements Action {
	
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
		if (is_null($voting)) {
			$request->setData('message', 'Nincs elérhető szavazás a megadott azonosítóval');
			return new PageForward('error');
		}
		$answerId = $request->get('answer_id');
		if (!array_key_exists($answerId, $voting->getAnswers())) {
			return new ErrorForward('Nem megfelelő választ adott meg!');
		}
		$this->votingService->vote($voting, $request->getUser(), $answerId);
		return new ActionForward('ShowVotingAction');
	}
	
}
