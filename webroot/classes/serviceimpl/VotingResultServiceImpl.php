<?php

class VotingResultServiceImpl extends DbServiceBase implements VotingResultService {

	/**
	 * Get results of the given voting.
	 * 
	 * The result contains one item for each possible answer of the given voting.
	 * Each line contains the following keys:
	 *     id: id of the answer
	 *     title: title of the answer
	 *     cnt: number of votes for the answer
	 * Thre result is ordered by the 'cnt' field, descendant.
	 *
	 * @param Voting $voting
	 * @return array
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
