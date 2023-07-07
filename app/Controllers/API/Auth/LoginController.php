<?php

declare(strict_types=1);

namespace App\Controllers\API\Auth;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Authentication\JWTManager;
use CodeIgniter\Shield\Authentication\Passwords;
use CodeIgniter\Shield\Config\AuthSession;

class LoginController extends BaseController
{
    use ResponseTrait;


    /**
     * Authenticate user existing and Issue JWT 
     */
    public function jwtLogin()
    {

        // Get the validation rules
        $rules = $this->getValidationRules();

        // Validate credentials
        if (!$this->validateData($this->request->getJSON(true), $rules))
            return $this->fail([
                'errors' => $this->validator->getErrors()
            ], $this->codes['unauthorized']);

        // Get the credentials for login
        $credentials = $this->request->getJsonVar(setting('Auth.validFields'));
        $credentials = array_filter($credentials);
        $credentials['password'] = $this->request->getJsonVar('password');

        /** @var Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();

        // Check the credentials
        $result = $authenticator->check($credentials);

        // Credentials mismatch
        if (!$result->isOK())
            // @TODO Record a failed login attemp
            return $this->failUnauthorized($result->reason());

        // Credentials match
        // @TODO Record a success login attemp
        $user = $result->extraInfo();

        /** @var JWTManager $manager */
        $manager = service('jwtmanager');

        // Generate JWT and return to client
        $jwt = $manager->generateToken($user);

        return $this->respond([
            'statusCode' => 200,
            'message' => 'OK',
            'data' => [
                'access_token' => $jwt
            ]
        ]);
    }

    /**
     * Returns the rules that should be used for validation
     * 
     * @return array<string, array<string, array<string>|string>>
     * @phpstan-return array<string, array<string, string|list<string>>>
     */
    protected function getValidationRules(): array
    {
        return setting('validation.login') ?? [
            'email' => [
                'lable' => 'Auth.email',
                'rules' => config(AuthSession::class)->emailValidationRules,
            ],
            'password' => [
                'lable' => 'Auth.password',
                'rules' => 'required|' . Passwords::getMaxLenghtRule(),
                'errors' => [
                    'max_byte' => 'Auth.errorPasswordTooLongBytes'
                ]
            ]
        ];
    }

    // public function refreshToken()
    // {
    //     /** @var JWTManager $manager */
    //     $manager = service('jwtmanager');

    //     $user = auth()->user();

    //     $claims = [
    //         'email' => $user->email
    //     ];

    //     $jwt = $manager->generateToken($user, $claims);

    //     dd($jwt);
    // }

    /**
     * Logs the current user out.
     */
    public function logoutAction()
    {
        auth()->logout();

        return $this->respond([
            'statusCode' => 200,
            'message' => lang('Auth.successLogout')
        ], 200);
    }
}
