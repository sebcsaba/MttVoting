<?php

class ShowEditVotingAction implements Action {
	
	/**
	 * @var Config
	 */
	private $config;
	
	/**
	 * @var VotingListingService
	 */
	private $votingListingService;

	public function __construct(Config $config, VotingListingService $votingListingService) {
		$this->votingListingService = $votingListingService;
		$this->config = $config;
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
			$request->setData('link', $this->createPermalink($voting));
			return new PageForward('edit_details');
		} else {
			return new ActionForward('ShowResultAction');
		}
	}
	
	private function createPermalink(Voting $voting) {
		$data = array(
			'id' => $voting->getId(),
			'do' => 'ShowVoting',
		);
		return Url::create($this->config->get('url'))
			->show(base64_encode(json_encode($data)));
	}
	
}
