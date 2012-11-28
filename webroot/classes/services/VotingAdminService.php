<?php

interface VotingAdminService {
	
	public function create(Voting $voting);
	
	public function update(Voting $voting);
	
	public function remove(Voting $voting);
	
	public function close(Voting $voting);
	
}
