<?php
namespace NoobBundle\Controller;

use EntityBundle\Entity\Person;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Hateoas\Configuration as Hateoas;

class PersonExchangeRelationProvider
{

	public static function getRelations(Person $person, ClassMetadataInterface $classMetadata)
	{
		$relations = array();
		foreach ($person->getExchanges() as $exchange) {
			$relation = new Hateoas\Relation(
				'exchanges',
				new Hateoas\Route(
					'get_people_exchanges',
					array('personID' => $person->getId(),
						'exchangeID' => $exchange->getId())
				)
			);
			array_push($relations, $relation);
		}
		return $relations;
	}
}