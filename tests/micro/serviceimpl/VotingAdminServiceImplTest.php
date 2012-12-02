<?php

class VotingAdminServiceImplTest extends TestCase {
	
	public function testCreate() {
		$answers = array(
			'1'=>'yes',
			'2'=>'no',
		);
		$participants = array();
		$voting = new Voting(null, 1, 'test voting', 'test voting description', '2012-12-01 15:00:00', null, true, $answers, $participants);
		$db = $this->buildMock('Database', array('insert'));
		$db->expects($this->exactly(3))->method('insert');
		$db->expects($this->at(0))->method('insert')->with($this->stringEndsWith('_voting'))
			//->will($this->returnCallback(array($this,'assertArrayArgumentForTestCreateRun1')));
			->will($this->returnValue(111));
		$db->expects($this->at(1))->method('insert')->with($this->stringEndsWith('_answer'))
			->will($this->returnCallback(array($this,'assertArrayArgumentForTestCreateRun2')));
		$db->expects($this->at(2))->method('insert')->with($this->stringEndsWith('_answer'))
			->will($this->returnCallback(array($this,'assertArrayArgumentForTestCreateRun2')));
		$service = new VotingAdminServiceImpl($db);
		$service->create($voting);
	}
	
	public function assertArrayArgumentForTestCreateRun1($table, array $array) {
		$this->assertArrayNotHasKey('id', $array);
		$this->assertArrayHasKey('creator_user_id', $array);
		$this->assertArrayHasKey('stop_date', $array);
		$this->assertNull($array['stop_date']);
		$this->assertArrayHasKey('private', $array);
		$this->assertTrue($array['private']);
		return 111;
	}
	
	public function assertArrayArgumentForTestCreateRun2($table, array $array) {
		$this->assertArrayNotHasKey('id', $array);
		$this->assertArrayHasKey('fk_voting', $array);
		$this->assertEquals(111, $array['fk_voting']);
		$this->assertArrayHasKey('title', $array);
		return 112;
	}
	
}
