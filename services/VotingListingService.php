<?php

interace VotingListingService {

    /**
     * Returns all opened Voting of the given User.
     * (The creator of the returned items is the given.)
     *
     * @param User $user creator user
     * @return Voting[]
     */
    public function getAllOpenedOf(User $user);
    
    /**
     * Returns the last Votings of the given User.
     * (The creator of the returned items is the given.)
     *
     * @param User $user creator user
     * @param int $limit maximum number of the returned items
     * @return Voting[]
     */
    public function getAllOf(User $user, $limit = 10);
    
    /**
     * Returns all answerable Votings of the given User.
     * (The given user is participant of returned items.)
     *
     * @param User $user participant user
     * @return Voting[]
     */
    public function getAnswerableFor(User $user);
    
    /**
     * Returns the last Votings of the given User.
     * (The given user is participant of returned items.)
     *
     * @param User $user participant user
     * @param int $limit maximum number of the returned items
     * @return Voting[]
     */
    public function getAllFor(User $user, $limit = 10);
    
}
