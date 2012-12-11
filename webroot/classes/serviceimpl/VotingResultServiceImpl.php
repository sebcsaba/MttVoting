<?php

class VotingResultServiceImpl extends DbServiceBase implements VotingResultService {

	/**
	 * @var UserService
	 */
	private $userService;
	
	public function __construct(Database $db, UserService $userService) {
		parent::__construct($db);
		//$this->userService = $userService;
	}
	
	/**
	 * TODO copy documentation
	 */
	public function getResult(Voting $voting) {
		$query = QueryBuilder::create()
			->select('fk_answer')->select('COUNT(id) AS cnt')
			->from('privatevoting_vote')
			->where('fk_voting=?',$voting->getId())
			->groupBy('fk_answer')
			->orderByDesc('cnt');
		$q2 = QueryBuilder::create()
			->select('a.id')->select('a.title')->select('v.cnt')
			->from('? AS v',$query)->join('privatevoting_answer a ON (v.fk_answer=a.id)');
		return $this->db->queryAssocTable($q2);
		// select fk_answer,count(id) as cnt from privatevoting_vote where fk_voting=1 group by fk_answer order by cnt desc;
		
	}
	
}
