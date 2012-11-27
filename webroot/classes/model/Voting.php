<?php

class Voting {

	/**
	 * @var int
	 */
	private $id;
	
	/**
	 * @var int
	 */
	private $creatorUserId;
	
	/**
	 * @var string
	 */
	private $title;
	
	/**
	 * @var string
	 */
	private $description;
	
	/**
	 * @var date
	 */
	private $startDate;
	
	/**
	 * @var date
	 */
	private $stopDate;
	
	/**
	 * @var boolean
	 */
	private $private;
	
	/**
	 * @var array[int(id)=>string]
	 */
	private $answers;
	
	/**
	 * @var array[int(userId)=>Participant]
	 */
	private $participants;
	
	public function getId() {
		return $this->id;
	}
	
	public function getCreatorUserId() {
		return $this->creatorUserId;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function getStartDate() {
		return $this->startDate;
	}
	
	public function getStopDate() {
		return $this->stopDate;
	}
	
	public function getPrivate() {
		return $this->private;
	}
	
	public function getAnswers() {
		return $this->answers;
	}
	
	public function getParticipants() {
		return $this->participants;
	}
	
}
