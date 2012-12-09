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
		$participantId = $this->checkIfVotingIsAnswerableForUser($voting, $user);
		$this->checkIfAnswerIsValidForVoting($voting, $answerId);
		
		$update = UpdateBuilder::create()->update('privatevoting_participant')
			->where('id=?', $participantId)
			->set('voted', true);
		$this->db->exec($update);
		
		$insert = InsertBuilder::create()->into('privatevoting_vote')
			->set('fk_voting', $voting->getId())
			->set('fk_answer', $answerId);
		if (!$voting->getPrivate()) {
			$insert->set('fk_participant', $participantId);
		}
		$this->db->exec($insert);
	}
	
	private function checkIfAnswerIsValidForVoting(Voting $voting, $answerId) {
		$query = QueryBuilder::create()->select('fk_voting')->from('privatevoting_answer')->where('id=?',$answerId);
		if ($this->db->queryCell($query)!=$voting->getId()) throw new Exception('invalid answer id');
	}
	
	/**
	 * @return int participant id
	 */
	private function checkIfVotingIsAnswerableForUser(Voting $voting, User $user) {
		if (!is_null($voting->getStopDate())) throw new Exception('voting is closed');
		$query = QueryBuilder::create()->from('privatevoting_participant')
			->where('fk_voting=?', $voting->getId())
			->where('user_id=?', $user->getUserId());
		$row = $this->db->queryRow($query);
		if (is_null($row)) throw new Exception('this user is not participant of the given voting');
		if ($row['voted']) throw new Exception('this user has already voted');
		return $row['id'];
	}
	
	/**
	 * Checks, if the voting is opened, and answerable for the given user
	 * 
	 * @return boolean
	 */
	public function isVotingAnswerableForUser(Voting $voting, User $user) {
		try {
			$this->checkIfVotingIsAnswerableForUser($voting, $user);
			return true;
		} catch (Exception $ex) {
			return false;
		}
	}
	
}
