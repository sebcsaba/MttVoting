<?php

class ShowResultAction implements Action {
	
	/**
	 * @var VotingListingService
	 */
	private $votingListingService;
	
	/**
	 * @var VotingResultService
	 */
	private $votingResultService;
	
	public function __construct(VotingListingService $votingListingService, VotingResultService $votingResultService) {
		$this->votingListingService = $votingListingService;
		$this->votingResultService = $votingResultService;
	}
	
	/**
	 * @param Request $request
	 * @return Forward
	 */
	public function serve(Request $request) {
		$id = $request->get('id');
		$voting = $this->findVoting($id, $request->getUser());
		if (is_null($voting)) {
			$request->setData('message', 'Nincs elérhető szavazás a megadott azonosítóval');
			return new PageForward('error');
		}
		$result = $this->votingResultService->getResult($voting);
		$request->setData('voting', $voting);
		$request->setData('result', $result);
		return new PageForward('result');
	}
	
	private function findVoting($id, User $user) {
		$voting = $this->votingListingService->findOf($id, $user);
		if (is_null($voting)) {
			$voting = $this->votingListingService->findFor($id, $user);
		}
		return $voting;
	}
	
}
