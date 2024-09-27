<?php

namespace App\Providers\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use GuzzleHttp\RequestOptions;

class TKParkProvider extends AbstractProvider implements ProviderInterface
{
    /**
    * @var string[]
    */
    protected $scopes = [
        'all',
    ];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        // Set client_id and redirect_uri because tk park use GET method (Socialite use POST method)
        $this->with(['client_id' => $this->clientId, 'redirect_uri' => $this->redirectUrl]);
        return $this->buildAuthUrlFromBase(config('bookdose.tkpark_url').'/auth/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return config('bookdose.tkpark_url').'/auth/access_token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->post(config('bookdose.tkpark_url').'/api/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'email' => $user['email'],
            'username' => $user['uname'],
            'name' => $user['fname']." ".$user['lname'],
            'avatar_original' => !empty($user['image']) ? $user['image'] : null,
        ]);
    }

}