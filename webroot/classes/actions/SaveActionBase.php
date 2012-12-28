<?php

abstract class SaveActionBase implements Action {
	
	/**
	 * @var UserService
	 */
	protected $userService;
	
	public function __construct(UserService $userService) {
		$this->userService = $userService;
	}
	
	protected function getTitle(Request $request) {
		$title = $request->get('title');
		if (empty($title)) throw new ValidationException('A címet kötelező kitölteni!');
		return $title;
	}
	
	protected function getAnswers(Request $request, $minimumCount) {
		$answers = array();
		if (is_array($request->get('answer'))) {
			foreach ($request->get('answer') as $title) {
				if (!empty($title)) {
					$answers []= $title;
				}
			}
		}
		if (count($answers)<$minimumCount) {
			throw new ValidationException('Legalább két választ meg kell adni');
		}
		return $answers;
	}
	
	protected function getParticipants(Request $request) {
		$participants = array();
		if (is_array($request->get('participant'))) {
			foreach ($request->get('participant') as $userId) {
				if (!empty($userId)) {
					$u = $this->userService->findUserById($userId);
					$p = new Participant(null, $u, false);
					$participants[$userId] = $p;
				}
			}
		}
		return $participants;
	}
	
}
