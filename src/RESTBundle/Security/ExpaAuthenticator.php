<?php

namespace RESTBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ExpaAuthenticator extends AbstractGuardAuthenticator
{   
    private $session;

    private $simpleToken;

    private $advancedToken;

    public function __construct(Session $session, $simpleToken, $advancedToken)
    {
        $this->session = $session;
        $this->simpleToken = $simpleToken;
        $this->advancedToken = $advancedToken;
    }

    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     */
    public function getCredentials(Request $request)
    {   
        $this->request = $request;
        $token = $request->query->get('access_token');
        
        if (!$token) {
            throw new BadCredentialsException();
            // no token? Return null and no other methods will be called
            return;
        }
        // What you return here will be passed to getUser() as $credentials
        return array(
            'token' => $token
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = $credentials['token'];
        $user = $userProvider->loadUserByUsername($token);
        
        // if a User object, checkCredentials() is called
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // if already authenticated then is also saved in session already
        if( $user->getAuthenticated() ) return true;

        // try to authenticate
        if( $user->authenticateWithToken($this->simpleToken)
        || $user->authenticateWithToken($this->advancedToken,true)
        || $user->authenticateWithExpa() ) {
            //save to session for later
            $this->session->set($user->getUsername(), $user->serialize());
            return true;
        } else {
            throw new BadCredentialsException();
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            // you might translate this message
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}