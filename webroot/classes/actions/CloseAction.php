<?php

class CloseAction implements Action {
	
	/**
	 * @var VotingAdminService
	 */
	private $votingAdminService;
	
	/**
	 * @var VotingListingService
	 */
	private $votingListingService;

	public function __construct(VotingAdminService $votingAdminService, VotingListingService $votingListingService) {
		$this->votingAdminService = $votingAdminService;
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
		} else if (!is_null($voting->getStopDate())) {
			$request->setData('message', 'A megadott szavazás már lezárásra került!');
			return new PageForward('error');
		} else {
			$this->votingAdminService->close($voting);
			return new PageForward('edit_closed');
		}
	}
	
}
