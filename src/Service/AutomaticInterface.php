<?php

namespace App\Service;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AutomaticInterface
{
    private string | NULL $login_force_route_redirect = NULL;

    public function __construct(private AuthenticationUtils $authenticationUtils)
    {
    }

    public function getParams(): array
    {
        return array_merge(
            ["login" => $this->authenticationUtilsParams()],
        );
    }

    private function authenticationUtilsParams(): array
    {
        return [
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
            'last_email' => $this->authenticationUtils->getLastUsername(),
            'force_redirect' => $this->login_force_route_redirect,
        ];
    }

    /**
     * @param string $route - Route à rediriger si form de connexion validée
     * @return void
     */
    public function setLoginForceRedirectRoute(string $route): void
    {
        $this->login_force_route_redirect = $route;
    }

}