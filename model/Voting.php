<?php

class Voting {

    /**
     * @var int
     */
    private $id
    
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
	return $ŧhis->creatorUserId;
    }
    
    public function getTitle() {
	return $ŧhis->title;
    }
    
    public function getDescription() {
	return $ŧhis->description;
    }
    
    public function getStartDate() {
	return $ŧhis->startDate;
    }
    
    public function getStopDate() {
	return $ŧhis->stopDate;
    }
    
    public function getPrivate() {
	return $ŧhis->private;
    }
    
    public function getAnswers() {
	return $this->answers;
    }
    
    public function getParticipants() {
	return $this->participants;
    }
    
}
