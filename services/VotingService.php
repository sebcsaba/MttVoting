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
     * Get results of the given voting.
     *
     * @param Voting $voting
     * @return ??TODO??
     */
    public function getResult(Voting $voting);
    
}
