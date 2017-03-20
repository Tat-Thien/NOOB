<?php

namespace NoobBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class ExpaUser implements UserInterface, \Serializable
{
	private $id;

	//is actually the token
	private $username;

	private $roles;

	private $authenticated;

	public function __construct($username)
    {
        if($username){
        	$this->username = $username;
        	$this->authenticated = false;
        } else {
        	$this->username = "anonymous";
        	$this->authenticated = true;
        }
        $this->roles = ['ROLE_USER'];
    }

	public function getUsername()
	{
		return $this->username;
	}

	public function getRoles()
	{
		return $this->roles;
	}

	public function getPassword()
	{
		return null;
	}
	public function getSalt()
	{
		return null;
	}
	public function eraseCredentials()
	{
		return true;
	}

	public function getAuthenticated()
	{
		return $this->authenticated;
	}

	private function getExpaUser(){
		$req = curl_init();
		$url = "https://gis-api.aiesec.org/v2/current_person.json?access_token=" . $this->username;
		curl_setopt($req, CURLOPT_URL, $url);
		curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($req);

		$person = json_decode($resp, true);
		curl_close($req);
		if(!isset($person['person'])) {
			return null;
		}
		return $person['person'];
	}

	public function authenticateWithExpa()
	{
		$expaUser = $this->getExpaUser();
		if($expaUser){
			$this->id = $expaUser['id'];
			$this->authenticated = true;
			array_push($this->roles, 'ROLE_SIMPLE_TOKEN');
		} else {
			$this->authenticated = false;
		}

		return $this->authenticated;
	}

	public function authenticateWithToken($token, $advanced = false){
		if($this->username == $token) {
			$this->authenticated = true;
			array_push($this->roles, 'ROLE_SIMPLE_TOKEN');
			if($advanced) array_push($this->roles, 'ROLE_ADVANCED_TOKEN');
		}
		return $this->authenticated;
	}

	public function equals(UserInterface $user)
	{
		return $user->getUsername() === $this->username;
	}

	/** @see \Serializable::serialize() */
	public function serialize()
	{
		return serialize(array(
			$this->id,
			$this->username,
			$this->roles,
			$this->authenticated
		));
	}

	/** @see \Serializable::unserialize() */
	public function unserialize($serialized)
	{
		list (
			$this->id,
			$this->username,
			$this->roles,
			$this->authenticated
		) = unserialize($serialized);
	}

}
