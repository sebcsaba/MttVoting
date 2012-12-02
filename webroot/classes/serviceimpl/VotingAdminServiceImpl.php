<?php

class VotingAdminServiceImpl extends DbServiceBase implements VotingAdminService {
	
	public function create(Voting $voting) {
		$votingRow = array(
			'creator_user_id' => $voting->getCreatorUserId(),
			'title' => $voting->getTitle(),
			'description' => $voting->getDescription(),
			'start_date' => $voting->getStartDate(),
			'stop_date' => $voting->getStopDate(),
			'private' => $voting->getPrivate()
		);
		$votingId = $this->db->insert('privatevoting_voting', $votingRow);
		
		foreach ($voting->getAnswers() as $title) {
			$answerRow = array(
				'fk_voting' => $votingId,
				'title' => $title
			);
			$this->db->insert('privatevoting_answer', $answerRow);
		}

		foreach ($voting->getParticipants() as $participant) {
			$participantRow = array(
				'fk_voting' => $votingId,
				'user_id' => $participant->getUser()->getUserId(),
				'voted' => false
			);
			$this->db->insert('privatevoting_participant', $participantRow);
		}
	}
	
	public function update(Voting $voting) {
		$query = UpdateBuilder::create()->update('privatevoting_voting')
			->set('title',$voting->getTitle())
			->set('description',$voting->getDescription())
			->where('id=?', $voting->getId());
		$this->db->update($query);

		$query = QueryBuilder::create()->select('user_id')->from('privatevoting_participant')
			->where('fk_voting=?', $voting->getId());
		$oldParticipandUIDs = $this->db->queryColumn($query);
		foreach ($voting->getParticipants() as $participant) {
			$userId = $participant->getUser()->getUserId();
			$index = array_search($userId, $oldParticipandUIDs);
			if ($index===false) {
				// new user added to list
				$participantRow = array(
					'fk_voting' => $voting->getId(),
					'user_id' => $userId,
					'voted' => false
				);
				$this->db->insert('privatevoting_participant', $participantRow);
			} else {
				// old user found, ignore
				unset($oldParticipandUIDs[$index]);
			}
		}
		// $oldParticipandUIDs contains only ids of removable participants
		$query = UpdateBuilder::create()->deleteFrom('privatevoting_participant')
			->where('fk_voting=?', $voting->getId())
			->where('user_id IN (?)', join(',', $oldParticipandUIDs))
			->where('NOT voted');
		$this->db->update($query);
	}
	
	public function remove(Voting $voting) {
		$query = UpdateBuilder::create()->deleteFrom('privatevoting_answer')->where('fk_voting=?', $voting->getId());
		$this->db->update($query);
		$query = UpdateBuilder::create()->deleteFrom('privatevoting_participant')->where('fk_voting=?', $voting->getId());
		$this->db->update($query);
		$query = UpdateBuilder::create()->deleteFrom('privatevoting_vote')->where('fk_voting=?', $voting->getId());
		$this->db->update($query);
		$query = UpdateBuilder::create()->deleteFrom('privatevoting_voting')->where('id=?', $voting->getId());
		$this->db->update($query);
	}
	
	public function close(Voting $voting) {
		if (is_null($voting->getStopDate())) {
			$query = UpdateBuilder::create()->update('privatevoting_voting')
				->setNative('stop_date', 'NOW()')
				->where('id=?', $voting->getId());
			$this->db->update($query);
		}
	}
	
}
