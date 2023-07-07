<?php

declare(strict_types=1);

namespace App\Controllers\API\Auth;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Events\Events;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Authentication\Passwords;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Validation\Exceptions\ValidationException;
use Psr\Log\LoggerInterface;

class RegisterController extends BaseController
{
    use ResponseTrait;

    protected $helpers = ['setting'];

    /**
     * Auth Table Name
     */
    private array $tables;


    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ): void {
        parent::initController(
            $request,
            $response,
            $logger,
        );

        /** @var Auth $authConfig */
        $authConfig = config('Auth');
        $this->tables = $authConfig->tables;

    }

    /**
     * Attemps to register the user
     */
    public function apiRegisterAction()
    {
        if (auth()->loggedIn())
            return $this->respond([
                'statusCode' => 400,
                'status' => 'Error Bad Request',
                'data' => 'you have been logged in!'
            ], 400);

        // Check if registration is allowd 
        if (!setting('Auth.allowRegistration'))
            return $this->respond([
                'statusCode' => 400,
                'status' => 'Error Bad Request',
                'data' => lang('Auth.registerDisabled')
            ], 400);

        $users = $this->getUserProvider();

        // Validate here first, since some things,
        // like the password, can only be validated properly here.
        $rules = $this->getValidationRules();

        if (!$this->validateData($this->request->getPost(), $rules))
            return $this->failValidationErrors($this->validator->getErrors());

        // save the user
        $allowedPostFields = array_keys($rules);
        $user = $this->getUserEntity();
        $user->fill($this->request->getPost($allowedPostFields));

        // Workaround for email only registration/login
        if ($user->username === null)
            $user->username == null;

        try {
            $users->save($user);
        } catch (ValidationException $error) {
            return $this->failValidationErrors($users->errors());
        }

        // To get the complete user object with ID, we need to get from database
        $user = $users->findById($users->getInsertID());

        // add to default group
        $users->addToDefaultGroup($user);

        Events::trigger('register', $user);

        /** @var Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();

        $authenticator->startLogin($user);

        // If an action has been defined for register, start it up.
        $hasAction = $authenticator->startUpAction('register', $user);
        if ($hasAction)
            return $this->respond([
                'statusCode' => 200,
                'message' => 'OK',
                'data' => 'Please Activation your account with OTP send by email!'
            ], 200);

        // Set the user active
        $user->activate();

        $authenticator->completeLogin($user);

        // Success
        return $this->respond([
            'statusCode' => 201,
            'message' => 'OK',
            'data' => lang('Auth.registerSuccess')
        ], 201);
    }

    /**
     * Returns the user provider
     */
    protected function getUserProvider(): UserModel
    {
        $provider = model(setting('Auth.userProvider'));

        assert($provider instanceof UserModel, 'Config Auth.userProvider is not a valid UserProvider.');

        return $provider;
    }

    /**
     * Returns the Entity class that should be used
     */
    protected function getUserEntity(): User
    {
        return new User();
    }

    /**
     * Returns the rules that should be used for validation.
     *
     * @return array<string, array<string, array<string>|string>>
     * @phpstan-return array<string, array<string, string|list<string>>>
     */
    protected function getValidationRules(): array
    {
        $registrationUsernameRules = array_merge(
            config('AuthSession')->usernameValidationRules,
            [sprintf('is_unique[%s.username]', $this->tables['users'])]
        );
        $registrationEmailRules = array_merge(
            config('AuthSession')->emailValidationRules,
            [sprintf('is_unique[%s.secret]', $this->tables['identities'])]
        );

        return setting('Validation.registration') ?? [
            'username' => [
                'label' => 'Auth.username',
                'rules' => $registrationUsernameRules,
            ],
            'email' => [
                'label' => 'Auth.email',
                'rules' => $registrationEmailRules,
            ],
            'password' => [
                'label' => 'Auth.password',
                'rules' => 'required|' . Passwords::getMaxLenghtRule() . '|strong_password',
                'errors' => [
                    'max_byte' => 'Auth.errorPasswordTooLongBytes',
                ],
            ],
            'password_confirm' => [
                'label' => 'Auth.passwordConfirm',
                'rules' => 'required|matches[password]',
            ],
        ];
    }
}