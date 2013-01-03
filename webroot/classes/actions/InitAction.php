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
		$request->setData('interestingFor', $this->votingListingService->getInterestingFor($request->getUser()));
		$request->setData('openedOf', $this->votingListingService->getAllOpenedOf($request->getUser()));
		if ($request->has('show')) {
			$data = base64_decode($request->get('show'));
			$request->set('show', $data);
		}
		return new PageForward('index');
	}
	
}
