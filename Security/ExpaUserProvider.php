<?php

namespace NoobBundle\Security;


use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class ExpaUserProvider implements UserProviderInterface
{
	private $session;

	public function __construct(Session $session)
	{
		$this->session = $session;
	}

	public function loadUserByUsername($username)
	{   
		$user = new ExpaUser($username);
		//load user from session
		$sessionValue = $this->session->get($username);
		
		if($sessionValue) $user->unserialize($sessionValue);
		
		return $user;
	}

	public function refreshUser(UserInterface $user)
	{
		if (!$user instanceof ExpaUser) {
			throw new UnsupportedUserException(
				sprintf('Instances of "%s" are not supported.', get_class($user))
			);
		}

		return $this->loadUserByUsername($user->getUsername());
	}

	public function supportsClass($class)
	{
		return ExpaUser::class === $class;
	}
}