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
		
		foreach ($voting->getAnswers() as $title) {
			$insert = InsertBuilder::create()->into('privatevoting_answer')
				->set('fk_voting', $votingId)
				->set('title', $title);
			$this->db->exec($insert);
		}

		foreach ($voting->getParticipants() as $participant) {
			$insert = InsertBuilder::create()->into('privatevoting_participant')
				->set('fk_voting', $votingId)
				->set('user_id', $participant->getUser()->getUserId())
				->set('voted', false);
			$this->db->exec($insert);
		}
		
		return $votingId;
	}
	
	public function update(Voting $voting) {
		$query = UpdateBuilder::create()->update('privatevoting_voting')
			->set('title',$voting->getTitle())
			->set('description',$voting->getDescription())
			->where('id=?', $voting->getId());
		$this->db->exec($query);

		$query = QueryBuilder::create()->select('user_id')->from('privatevoting_participant')
			->where('fk_voting=?', $voting->getId());
		$oldParticipandUIDs = $this->db->queryColumn($query);
		foreach ($voting->getParticipants() as $participant) {
			$userId = $participant->getUser()->getUserId();
			$index = array_search($userId, $oldParticipandUIDs);
			if ($index===false) {
				// new user added to list
				$insert = InsertBuilder::create()->into('privatevoting_participant')
					->set('fk_voting', $voting->getId())
					->set('user_id', $userId)
					->set('voted', false);
				$this->db->exec($insert);
			} else {
				// old user found, ignore
				unset($oldParticipandUIDs[$index]);
			}
		}
		// $oldParticipandUIDs contains only ids of removable participants
		$query = DeleteBuilder::create()->from('privatevoting_participant')
			->where('fk_voting=?', $voting->getId())
			->where('user_id IN (?)', join(',', $oldParticipandUIDs))
			->where('NOT voted');
		$this->db->exec($query);
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
	
}
