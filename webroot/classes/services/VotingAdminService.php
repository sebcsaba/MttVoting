<?php

interface VotingAdminService {
	
	public function create(Voting $voting);

	/**
	 * Updates the given voting. This voting contains only
	 * the new answers and participants to insert.
	 * 
	 * @param Voting $voting
	 */
	public function update(Voting $voting);
	
	public function remove(Voting $voting);
	
	public function close(Voting $voting);
	
}
