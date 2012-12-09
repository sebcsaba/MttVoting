<?php

class VotingServiceImpl extends DbServiceBase implements VotingService {
	
	/**
	 * Give a vote. All important checks should be done here.
	 *
	 * @param Voting $voting
	 * @param User $user
	 * @param int $answerId
	 */
	public function vote(Voting $voting, User $user, $answerId) {
		$this->checkIfVotingAndAnswerIsOpened($voting, $answerId);
		$participantId = $this->checkIfVotingIsAnswerableForUser($voting, $user);
		$query = UpdateBuilder::create()->update('privatevoting_participant')
			->where('id=?', $participantId)
			->set('voted', true);
		$this->db->update($query);
		$voteRow = array(
			'fk_voting' => $voting->getId(),
			'fk_answer' => $answerId,
			'fk_participant' => null,
		);
		if (!$voting->getPrivate()) {
			$voteRow['fk_participant'] = $participantId;
		}
		$this->db->insert('privatevoting_vote', $voteRow);
	}
	
	private function checkIfVotingAndAnswerIsOpened(Voting $voting, $answerId) {
		if (!is_null($voting->getStopDate())) throw new Exception('voting is closed');
		$query = QueryBuilder::create()->select('fk_voting')->from('privatevoting_answer')->where('id=?',$answerId);
		if ($this->db->queryCell($query)!=$voting->getId()) throw new Exception('invalid answer id');
	}
	
	/**
	 * @return int participant id
	 */
	private function checkIfVotingIsAnswerableForUser(Voting $voting, User $user) {
		$query = QueryBuilder::create()->from('privatevoting_participant')
			->where('fk_voting=?', $voting->getId())
			->where('user_id=?', $user->getUserId());
		$row = $this->db->queryRow($query);
		if (is_null($row)) throw new Exception('this user is not participant of the given voting');
		if ($row['voted']) throw new Exception('this user has already voted');
		return $row['id'];
	}
	
	/**
	 * Get results of the given voting.
	 *
	 * @param Voting $voting
	 * @return ??TODO??
	 */
	public function getResult(Voting $voting) {
		// TODO
	}
	
}
