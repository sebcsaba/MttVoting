<?php

interface VotingListingService {
	
	/**
	 * Returns the Voting with the given id,
	 * if this voting is created by the given user.
	 * 
	 * @param int $id
	 * @param User $user
	 * @return Voting or null
	 */
	public function findOf($id, User $user);
	
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
	 * Returns the Voting with the given id,
	 * if the given user is participant of this voting.
	 * 
	 * @param int $id
	 * @param User $user
	 * @return Voting or null
	 */
	public function findFor($id, User $user);
	
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
	
	/**
	 * Returns all the Votings that can be interesting for the given user.
	 * This contains:
	 * - All the votings what is not answered yet, but may be answered by him.
	 * - All the votings he answered, but not closed yet
	 * - Some closed votings in which he was a participant. Maximum 10, and closed not earlier than one month.
	 * 
	 * @param User $user
	 */
	public function getInterestingFor(User $user);
	
}
