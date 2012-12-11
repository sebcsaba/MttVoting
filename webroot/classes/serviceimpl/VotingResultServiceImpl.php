<?php

class VotingResultServiceImpl extends DbServiceBase implements VotingResultService {

	/**
	 * TODO copy documentation
	 */
	public function getResult(Voting $voting) {
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
	
}
