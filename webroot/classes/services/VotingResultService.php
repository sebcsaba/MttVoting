<?php

interface VotingResultService {
	
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
	public function getResult(Voting $voting);
	
}
