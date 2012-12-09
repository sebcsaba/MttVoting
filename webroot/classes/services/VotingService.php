<?php

interface VotingService {

	/**
	 * Give a vote. All important checks should be done here.
	 *
	 * @param Voting $voting
	 * @param User $user
	 * @param int $answerId
	 */
	public function vote(Voting $voting, User $user, $answerId);
	
	/**
	 * Checks, if the voting is opened, and answerable for the given user
	 * 
	 * @return boolean
	 */
	public function isVotingAnswerableForUser(Voting $voting, User $user);
	
}
