<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    use TargetPathTrait;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        // ✅ FIRST: redirect to the originally requested page (VERY IMPORTANT)
        $targetPath = $this->getTargetPath($request->getSession(), 'main');

        if ($targetPath) {
            return new RedirectResponse($targetPath);
        }

        // Fallback: redirect based on role
        $user = $token->getUser();

        if (in_array('ROLE_RECRUITER', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('recruiter_dashboard'));
        }

        if (in_array('ROLE_CANDIDATE', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('candidate_dashboard'));
        }

        return new RedirectResponse($this->urlGenerator->generate('joboffer_list'));
    }
}
