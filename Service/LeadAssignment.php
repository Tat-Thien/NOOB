<?php
namespace NoobBundle\Service;

class LeadAssignment
{
	protected $cityMapping;
	protected $lcMapping;

	public function __construct()
	{
		$data = file_get_contents(__DIR__ . '/../Resources/leadassignment/lead_assignment.json');
		$this->cityMapping = json_decode($data, true);

		$data = file_get_contents(__DIR__ . '/../Resources/leadassignment/gis_lc_mapping.json');
		$this->lcMapping = json_decode($data, true);
	}

	public function getLc($program, $city)
	{
		$result = null;
		$program = strtoupper($program);

		if(isset($this->cityMapping[$program])){
			foreach($this->cityMapping[$program] as $lc) {
				if(in_array($city, $lc['cities'])) {
					$gisId = $this->getLcId($lc['name']);
					if($gisId == null) break;

					$result = new \NoobBundle\Entity\LeadAssignment();
					$result->setLc($lc['name']);
					$result->setGisId($gisId);
				}
			}
		}

		return $result;
	}

	public function getCities($program){
		$result = null;
		$program = strtoupper($program);

		if(isset($this->cityMapping[$program])){
			$result = [];
			foreach($this->cityMapping[$program] as $lc) {
				$result = array_merge($result, $lc['cities']);
			}
		}

		return $result;
	}

	public function getLcName($id){
		$result = null;
		foreach($this->lcMapping['lcs'] as $lc){
			if($lc['id'] == $id){
				$result = $lc['name'];
			}
		}
		return $result;
	}

	public function getLcId($name){
		$result = null;
		foreach($this->lcMapping['lcs'] as $lc){
			if($lc['name'] == $name){
				$result = $lc['id'];
			}
		}
		return $result;
	}
}
