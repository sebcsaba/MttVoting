<?php

class VotingListingServiceImpl extends DbServiceBase implements VotingListingService {
	
	/**
	 * @var UserService
	 */
	private $userService;
	
	public function __construct(Database $db, UserService $userService) {
		parent::__construct($db);
		$this->userService = $userService;
	}
	
	/**
	 * Returns the Voting with the given id,
	 * if this voting is created by the given user.
	 * 
	 * @param int $id
	 * @param User $user
	 * @return Voting or null
	 */
	public function findOf($id, User $user) {
		$query = $this->createQueryOf($user)->where('id=?', $id);
		return $this->loadFirstVoting($query);
	}
	
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
	 * Returns the Voting with the given id,
	 * if the given user is participant of this voting.
	 * 
	 * @param int $id
	 * @param User $user
	 * @return Voting or null
	 */
	public function findFor($id, User $user) {
		$query = $this->createQueryFor($user)->where('v.id=?', $id);
		return $this->loadFirstVoting($query);
		
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
			$result []= $this->createVoting($row, array(), array());
		}
		return $result;
	}

	/**
	 * Create a full Voting for the first row of the result.
	 * Returns null, if the resultset is empty.
	 * 
	 * @param array $row
	 * @return Voting or null
	 */
	private function loadFirstVoting(QueryBuilder $query) {
		$row = $this->db->queryRow($query, true);
		if (is_null($row)) {
			return null;
		} else {
			$answerQuery = QueryBuilder::create()->from('privatevoting_answer')->where('fk_voting=?', $row['id']);
			$answers = $this->db->queryMapping($answerQuery,'id','title');
			$participants = $this->loadParticipants($row['id']);
			return $this->createVoting($row, $answers, $participants);
		}
	}
	
	/**
	 * Create a full Voting.
	 * 
	 * @param array $row (fieldname=>value)
	 * @param array $answers (id=>string)
	 * @param array $participants (id=>Participant)
	 * @return Voting
	 */
	private function createVoting(array $row, array $answers, array $participants) {
		return new Voting($row['id'], $row['creator_user_id'], $row['title'], $row['description'], 
				$row['start_date'], $row['stop_date'], $row['private'], $answers, $participants);
	}
	
	/**
	 * Returns the participants for the given voting
	 * 
	 * @param int $votingId
	 * @return (id=>Participant)
	 */
	private function loadParticipants($votingId) {
		$query = QueryBuilder::create()->from('privatevoting_participant')->where('fk_voting=?', $votingId);
		$result = array();
		foreach ($this->db->query($query) as $row) {
			$id = $row['id'];
			$user = $this->userService->findUserById($row['user_id']);
			$result[$id] = new Participant($id, $user, $row['voted']);
		}
		return $result;
	}
	
	/**
	 * Returns all the Votings that can be interesting for the given user.
	 * This contains:
	 * - All the votings what is not answered yet, but may be answered by him.
	 * - All the votings he answered, but not closed yet
	 * - Some closed votings in which he was a participant. Maximum 10, and closed not earlier than one month.
	 * 
	 * @param User $user
	 */
	public function getInterestingFor(User $user) {
		$query = $this->createQueryFor($user)
			->where('(NOT p.voted) OR (v.stop_date IS NULL) OR (v.stop_date>=DATE_SUB(NOW(),INTERVAL 1 MONTH))');
		$result = array();
		$closedCount = 0;
		foreach ($this->db->query($query) as $row) {
			$closed = !is_null($row['stop_date']);
			if ($closed) {
				++$closedCount;
			}
			if (!$closed || $closedCount<=10) {
				$result []= $this->createVoting($row, array(), array());
			}
		}
		return $result;
	}
	
}
