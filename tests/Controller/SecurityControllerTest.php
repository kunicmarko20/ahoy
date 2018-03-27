<?php

namespace Tests\App\Controller;

use Tests\App\AbstractTestCase;

class SecurityControllerTest extends AbstractTestCase
{
    public function testLogin(): void
    {
        $client = $this->request(
            'POST',
            'login',
            [
                'login' =>  [
                    'email' => 'you_shall_not_pass@email,com',
                    'password' => 'fakepass',
                ],
            ]
        );

        if ($client->getResponse()->isRedirection()) {
            $client->followRedirect();
        }

        $this->assertContains('Invalid credentials.', $client->getResponse()->getContent());
    }
}
