<?php
namespace NoobBundle\Controller;

use EntityBundle\Entity\Exchange;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Hateoas\Configuration as Hateoas;

class ExchangeReintegrationActivityParticipationRelationProvider
{

	public static function getRelations(Exchange $exchange, ClassMetadataInterface $classMetadata)
	{
		$relations = array();
		foreach ($exchange->getReintegrationActivityParticipations() as $reintegrationActivityParticipation) {
			$relation = new Hateoas\Relation(
				'reintegrationActivityParticipations',
				new Hateoas\Route(
					'get_reintegrationactivityparticipation',
					array('participationID' => $reintegrationActivityParticipation->getId())
				)
			);
			array_push($relations, $relation);
		}
		return $relations;
	}
}