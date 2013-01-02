<?php

class SaveDetailsAction extends SaveActionBase implements Action {
	
	/**
	 * @var VotingAdminService
	 */
	private $votingAdminService;
	
	/**
	 * @var VotingListingService
	 */
	private $votingListingService;

	public function __construct(UserService $userService, VotingAdminService $votingAdminService, VotingListingService $votingListingService) {
		parent::__construct($userService);
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
		if (is_null($voting)) {
			$request->setData('message', 'Nincs elérhető szavazás a megadott azonosítóval');
			return new PageForward('error');
		}
		try {
			if (!is_null($voting->getStopDate())) throw new ValidationException('A szavazás már le van zárva!');
			$modifiedVoting = $this->createVoting($voting, $request);
			$this->votingAdminService->update($modifiedVoting);
			return new ActionForward('ShowEditVotingAction');
		} catch (ValidationException $ex) {
			return new ErrorForward($ex->getMessage());
		}
	}
	
	private function createVoting(Voting $originalVoting, Request $request) {
		return new Voting($originalVoting->getId(),
			$originalVoting->getCreatorUserId(),
			$this->getTitle($request),
			''.$request->get('description'),
			$originalVoting->getStartDate(),
			null,
			$originalVoting->getPrivate(),
			array(),
			$this->getParticipants($request));
	}
	
}
