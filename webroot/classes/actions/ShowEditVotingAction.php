<?php

class ShowEditVotingAction implements Action {
	
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
		$voting = $this->votingListingService->findOf($id, $request->getUser());
		$request->setData('voting', $voting);
		if ($voting==null) {
			$request->setData('message', 'Nincs elérhető szavazás a megadott azonosítóval');
			return new PageForward('error');
		} else if (is_null($voting->getStopDate())) {
			return new PageForward('edit_details');
		} else {
			return new ActionForward('ShowResultAction');
		}
	}
	
}
