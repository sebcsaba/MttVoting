<?php

class DeleteAction implements Action {
	
	/**
	 * @var VotingListingService
	 */
	private $votingListingService;
	
	/**
	 * @var VotingAdminService
	 */
	private $votingAdminService;

	public function __construct(VotingListingService $votingListingService, VotingAdminService $votingAdminService) {
		$this->votingListingService = $votingListingService;
		$this->votingAdminService = $votingAdminService;
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
		} else {
			$this->votingAdminService->remove($voting);
			return new ActionForward('ShowAllOfAction');
		}
	}
	
}
