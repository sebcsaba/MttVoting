<?php

abstract class SaveActionBase implements Action {
	
	protected function getTitle(Request $request) {
		$title = $request->get('title');
		if (empty($title)) throw new ValidationException('A címet kötelező kitölteni!');
		return $title;
	}
	
	protected function getAnswers(Request $request, $minimumCount) {
		$answers = array();
		foreach ($request->get('answer') as $title) {
			if (!empty($title)) {
				$answers []= $title;
			}
		}
		if (count($answers)<$minimumCount) {
			throw new ValidationException('Legalább két választ meg kell adni');
		}
		return $answers;
	}
	
}
