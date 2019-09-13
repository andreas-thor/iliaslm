<?php 

class CP_Question {
	
	protected $json;
	protected $qid;
	
	public function __construct (int $qid) {
		$this->qid = $qid;
	}
	
	
	public function getJSONString (): string {
		return 'questions[' . $this->qid . '] = ' . json_encode($this->json) . ';';
	}
	
}

?>