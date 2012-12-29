<?php

class VotingResultServiceImpl extends DbServiceBase implements VotingResultService {
	
	/**
	 * @var UserService
	 */
	private $userService;
	
	public function __construct(Database $db, UserService $userService) {
		parent::__construct($db);
		$this->userService = $userService;
	}

	/**
	 * Get results of the given voting.
	 * 
	 * The result contains one item for each possible answer of the given voting.
	 * Each line contains the following keys:
	 *     id: id of the answer
	 *     title: title of the answer
	 *     cnt: number of votes for the answer
	 * If the voting is public, an additional key exists:
	 *     users: array of the usernames who voted for this answer
	 * Thre result is ordered by the 'cnt' field, descendant.
	 *
	 * @param Voting $voting
	 * @return array
	 */
	public function getResult(Voting $voting) {
		$result = $this->createGeneralResult($voting);
		if (!$voting->getPrivate()) {
			$users = $this->createPublicInfo($voting);
			$result = $this->mergeUserInfoToGeneral($result, $users);
		}
		return $result;
	}
	
	private function createGeneralResult(Voting $voting) {
		$innerQuery = QueryBuilder::create()
			->select('fk_answer')->select('COUNT(id) AS cnt')
			->from('privatevoting_vote')
			->where('fk_voting=?',$voting->getId())
			->groupBy('fk_answer');
		$outerQuery = QueryBuilder::create()
			->select('a.id')->select('a.title')->select('COALESCE(v.cnt,0) AS cnt')
			->from('privatevoting_answer a')->leftJoin('? AS v ON (v.fk_answer=a.id)',$innerQuery)
			->where('fk_voting=?',$voting->getId())
			->orderByDesc('cnt');
		return $this->db->queryAssocTable($outerQuery);
	}
	
	private function createPublicInfo(Voting $voting) {
		$query = QueryBuilder::create()
			->from('privatevoting_vote v')
			->join('privatevoting_participant p ON (v.fk_participant=p.id)')
			->where('v.fk_voting=?', $voting->getId())
			->where('p.fk_voting=?', $voting->getId())
			->select('p.user_id')->select('v.fk_answer');
		$userVotes = $this->db->queryMapping($query);
		$users = $this->userService->findUsersByIds(array_keys($userVotes));
		$result = array();
		foreach ($users as $user) {
			$answerId = $userVotes[$user->getUserId()];
			$usersBlock = I($result,$answerId,array());
			$usersBlock []= $user->getLoginName();
			$result[$answerId] = $usersBlock;
		}
		return $result;
	}
	
	private function mergeUserInfoToGeneral(array $general, array $users) {
		$result = array();
		foreach ($general as $row) {
			$row['users'] = I($users, $row['id'], array());
			$result []= $row;
		}
		return $result;
	}
	
}
