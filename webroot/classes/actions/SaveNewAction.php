<?php

class SaveNewAction extends SaveActionBase implements Action {

	/**
	 * @var VotingAdminService
	 */
	private $votingAdminService;
	
	public function __construct(UserService $userService, VotingAdminService $votingAdminService) {
		parent::__construct($userService);
		$this->votingAdminService = $votingAdminService;
	}
	
	/**
	 * @param Request $request
	 * @return Forward
	 */
	public function serve(Request $request) {
		try {
			$voting = $this->convertToVoting($request);
			$id = $this->votingAdminService->create($voting);
			$request->set('id', $id);
			$request->setData('reload_leftmenu',true);
			return new ActionForward('ShowEditVotingAction');
		} catch (ValidationException $ex) {
			return new ErrorForward($ex->getMessage());
		}
	}
	
	private function convertToVoting(Request $request) {
		return new Voting(null,
			$request->getUser()->getUserId(),
			$this->getTitle($request),
			''.$request->get('description'),
			new DateTime(),
			null,
			$this->getBooleanRadioField($request, 'private', 'Válaszd ki, hogy publikus vagy privát lesz-e a szavazás!'),
			$this->getAnswers($request, 2),
			$this->getParticipants($request));
	}
	
	private function getBooleanRadioField(Request $request, $fieldName, $errorMessage) {
		$fieldValue = $request->get($fieldName);
		if (!in_array($fieldValue,array(0,1))) throw new ValidationException($errorMessage);
		return $fieldValue==1;
	}
	
}
