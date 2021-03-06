<?php

class VotingAdminServiceImpl extends DbServiceBase implements VotingAdminService {
	
	public function create(Voting $voting) {
		$insert = InsertBuilder::create()->into('privatevoting_voting')
			->set('creator_user_id', $voting->getCreatorUserId())
			->set('title', $voting->getTitle())
			->set('description', $voting->getDescription())
			->set('start_date', $voting->getStartDate())
			->set('stop_date', $voting->getStopDate())
			->set('private', $voting->getPrivate());
		$votingId = $this->db->exec($insert);
		
		$this->insertAnswers($voting->getAnswers(), $votingId);
		$this->insertParticipants($voting->getParticipants(), $votingId);
		
		return $votingId;
	}
	
	/**
	 * Updates the given voting. This voting contains only
	 * the new participants to insert.
	 * 
	 * @param Voting $voting
	 */
	public function update(Voting $voting) {
		$query = UpdateBuilder::create()->update('privatevoting_voting')
			->set('title',$voting->getTitle())
			->set('description',$voting->getDescription())
			->where('id=?', $voting->getId());
		$this->db->exec($query);

		$this->insertParticipants($voting->getParticipants(), $voting->getId());
	}
	
	public function remove(Voting $voting) {
		$query = DeleteBuilder::create()->from('privatevoting_answer')->where('fk_voting=?', $voting->getId());
		$this->db->exec($query);
		$query = DeleteBuilder::create()->from('privatevoting_participant')->where('fk_voting=?', $voting->getId());
		$this->db->exec($query);
		$query = DeleteBuilder::create()->from('privatevoting_vote')->where('fk_voting=?', $voting->getId());
		$this->db->exec($query);
		$query = DeleteBuilder::create()->from('privatevoting_voting')->where('id=?', $voting->getId());
		$this->db->exec($query);
	}
	
	public function close(Voting $voting) {
		if (is_null($voting->getStopDate())) {
			$query = UpdateBuilder::create()->update('privatevoting_voting')
				->setNative('stop_date', 'NOW()')
				->where('id=?', $voting->getId());
			$this->db->exec($query);
		}
	}
	
	private function insertAnswers(array $answers, $votingId) {
		foreach ($answers as $title) {
			$insert = InsertBuilder::create()->into('privatevoting_answer')
				->set('fk_voting', $votingId)
				->set('title', $title);
			$this->db->exec($insert);
		}
	}

	private function insertParticipants(array $participants, $votingId) {
		foreach ($participants as $participant) {
			$insert = InsertBuilder::create()->into('privatevoting_participant')
				->set('fk_voting', $votingId)
				->set('user_id', $participant->getUser()->getUserId())
				->set('voted', false);
			$this->db->exec($insert);
		}
	}
		
}
