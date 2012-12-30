<?php

class ConfirmCloseAction implements Action {
	
	/**
	 * @var VotingListingService
	 */
	private $votingListingService;
	
	/**
	 * @var VotingResultService
	 */
	private $votingResultService;

	public function __construct(VotingListingService $votingListingService, VotingResultService $votingResultService) {
		$this->votingListingService = $votingListingService;
		$this->votingResultService = $votingResultService;
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
			$request->setData('status', $this->getStatusText($voting));
			return new PageForward('confirm_close');
		}
	}
	
	private function getStatusText(Voting $voting) {
		$status = $this->votingResultService->getStatus($voting);
		$totalCount = count($voting->getParticipants());
		$notVotedCount = $status['not-voted-count'];
		if ($notVotedCount==0) {
			return sprintf('Minden szavazásra jogosult leadta szavazatát (%d személy).',$totalCount);
		} else {
			$text = sprintf('A szavazásra jogosult %d személy közül még nem szavazott %d személy', $totalCount, $notVotedCount);
			if ($voting->getPrivate()) {
				return $text . '.';
			} else {
				return $text . sprintf(': %s.', join(', ', $status['not-voted']));
			}
		}
	}
	
}
