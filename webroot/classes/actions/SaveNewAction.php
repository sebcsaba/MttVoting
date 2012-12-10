<?php

class SaveNewAction implements Action {

	/**
	 * @var VotingAdminService
	 */
	private $votingAdminService;
	
	public function __construct(VotingAdminService $votingAdminService) {
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
			return new ActionForward('ShowEditVotingAction');
		} catch (Exception $ex) {
			return new ErrorForward($ex->getMessage());
		}
	}
	
	private function convertToVoting(Request $request) {
		$title = $request->get('title');
		if (empty($title)) throw new Exception('A címet kötelező kitölteni!');
		return new Voting(null,
			$request->getUser()->getUserId(),
			$title,
			''.$request->get('description'),
			new DateTime(),
			null,
			$this->getBooleanRadioField($request, 'private', 'Válaszd ki, hogy publikus vagy privát lesz-e a szavazás!'),
			$this->getAnswers($request),
			array());
	}
	
	private function getAnswers(Request $request) {
		$answers = array();
		foreach ($request->get('answer') as $title) {
			if (!empty($title)) {
				$answers []= $title;
			}
		}
		if (count($answers)<2) {
			throw new Exception('Legalább két választ meg kell adni');
		}
		return $answers;
	}
	
	private function getBooleanRadioField(Request $request, $fieldName, $errorMessage) {
		$fieldValue = $request->get($fieldName);
		if (!in_array($fieldValue,array(0,1))) throw new Exception($errorMessage);
		return $fieldValue==1;
	}
	
}
