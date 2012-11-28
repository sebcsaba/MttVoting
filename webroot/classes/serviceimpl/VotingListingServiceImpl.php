<?php

class VotingListingServiceImpl extends DbServiceBase implements VotingListingService {
	
	/**
	 * Returns all opened Voting of the given User.
	 * (The creator of the returned items is the given.)
	 *
	 * @param User $user creator user
	 * @return Voting[]
	 */
	public function getAllOpenedOf(User $user) {
		$query = $this->createQueryOf($user)->where('stop_date IS NULL');
		return $this->loadVotings($query);
	}
	
	/**
	 * Returns the last Votings of the given User.
	 * (The creator of the returned items is the given.)
	 *
	 * @param User $user creator user
	 * @param int $limit maximum number of the returned items
	 * @return Voting[]
	 */
	public function getAllOf(User $user, $limit = 10) {
		$query = $this->createQueryOf($user)->limit($limit);
		return $this->loadVotings($query);
		
	}
	
	/**
	 * Returns all answerable Votings of the given User.
	 * (The given user is participant of returned items.)
	 *
	 * @param User $user participant user
	 * @return Voting[]
	 */
	public function getAnswerableFor(User $user) {
		$query = $this->createQueryFor($user)->where('NOT p.voted');
		return $this->loadVotings($query);
	}
	
	/**
	 * Returns the last Votings of the given User.
	 * (The given user is participant of returned items.)
	 *
	 * @param User $user participant user
	 * @param int $limit maximum number of the returned items
	 * @return Voting[]
	 */
	public function getAllFor(User $user, $limit = 10) {
		$query = $this->createQueryFor($user)->limit($limit);
		return $this->loadVotings($query);
	}
	
	/**
	 * @param User $user
	 * @return QueryBuilder
	 */
	private function createQueryOf(User $user) {
		return QueryBuilder::create()->from('privatevoting_voting')
			->where('creator_user_id = ?', $user->getUserId());
	}

	/**
	 * @param User $user
	 * @return QueryBuilder
	 */
	private function createQueryFor(User $user) {
		return QueryBuilder::create()->select('v.*')
			->from('privatevoting_voting v')
			->join('privatevoting_participant p ON (v.id=p.fk_voting)')
			->where('p.user_id = ?', $user->getUserId());
	}
	
	/**
	 * @param QueryBuilder $query
	 * @return Voting[]
	 */
	private function loadVotings(QueryBuilder $query) {
		$result = array();
		foreach ($this->db->query($query) as $row) {
			$result []= new Voting($row['id'], $row['creator_user_id'], $row['title'], $row['description'], 
				$row['start_date'], $row['stop_date'], $row['private'], array(), array());
		}
		return $result;
	}
	
}
