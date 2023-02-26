<?php

// src/Security/AccessDeniedHandler.php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private UrlGeneratorInterface $urlGenerator;

    private Security $security;
    public function __construct(UrlGeneratorInterface $urlGenerator, Security $security)
    {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): Response
    {
        if ($this->security->isGranted('IS_AUTHENTICATED')) {
            return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
        }

        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }
}
