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
	 * If the voting is public, an additional key exists:
	 *     users: array of the usernames who voted for this answer
	 * Thre result is ordered by the 'cnt' field, descendant.
	 *
	 * @param Voting $voting
	 * @return array
	 */
	public function getResult(Voting $voting);
	
	/**
	 * Return small statistics about the current status of the voting.
	 * The result array contains the following key:
	 *     'not-voted-count': number of the participants who haven't voted yet
	 * If the voting is public, an additional key exists:
	 *     'not-voted': array of the user names of the participants who haven't voted yet
	 * @param Voting $voting
	 * @return array
	 */
	public function getStatus(Voting $voting);
	
}
