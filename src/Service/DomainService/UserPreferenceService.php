<?php

namespace App\Service\DomainService;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserPreferenceService {

    public function create(array $data): array
    {
        if (empty($data['darkMode'])) {
            throw new AccessDeniedException('Dark mode preference is required');
        }

        $darkModeValue = (string) $data['darkMode'];

        if ($darkModeValue !== '1' && $darkModeValue !== '2') {
            throw new AccessDeniedException('Dark mode value can only be 1 or 2');
        }

        return [
            'darkMode' => $darkModeValue,
            'status' => 'created'
        ];
    }

    public function createDarkModeCookie(array $data): Cookie
    {
        $preferenceData = $this->create($data);
        $darkModeValue = $preferenceData['darkMode'];

        $cookie = Cookie::create(
            'darkModePreference',
            $darkModeValue,
            strtotime('+30 days'), 
            '/',                   
            null,                
            false,                 
            true,                   
            false,               
            Cookie::SAMESITE_LAX 
        );

        return $cookie;
    }

    public function setDarkModePreference(array $data, Response $response): Response
    {
        $cookie = $this->createDarkModeCookie($data);
        $response->headers->setCookie($cookie);
        
        return $response;
    }

    public function createWithCookieResponse(array $data): array
    {
        $preferenceData = $this->create($data);
        $preferenceData['status'] = 201;
        $preferenceData['cookie'] = $this->createDarkModeCookie($data);
        
        return $preferenceData;
    }
}