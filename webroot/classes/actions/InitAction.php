<?php

class InitAction implements Action {
	
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
		$request->setData('answerableFor', $this->votingListingService->getAnswerableFor($request->getUser()));
		$request->setData('openedOf', $this->votingListingService->getAllOpenedOf($request->getUser()));
		return new PageForward('index');
	}
	
}
