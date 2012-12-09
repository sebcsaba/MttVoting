<?php

interface VotingResultService {
	
	/**
	 * Get results of the given voting.
	 *
	 * @param Voting $voting
	 * @return ??TODO??
	 */
	public function getResult(Voting $voting);
	
}
