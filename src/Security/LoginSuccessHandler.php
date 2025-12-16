<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();

        if (in_array('ROLE_RECRUITER', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('recruiter_dashboard'));
        }

        if (in_array('ROLE_CANDIDATE', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('candidate_dashboard'));
        }

        // fallback
        return new RedirectResponse($this->urlGenerator->generate('joboffer_list'));
    }
}
