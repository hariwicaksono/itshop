<?php namespace App\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Models\AuthModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use ReflectionException;

class Auth extends BaseControllerApi
{
    /**
     * Authenticate Existing User
     * @return Response
     */
    public function login()
    {
        $rules = [
            'email' => 'required|min_length[6]|max_length[50]|valid_email',
            'password' => 'required|min_length[8]|max_length[255]|validateUser[email, password]'
        ];

        $errors = [
            'password' => [
                'validateUser' => 'Invalid login credentials provided'
            ]
        ];

        $input = $this->getRequestInput();

        if (!$this->validate($rules, $errors)) {
            return $this
                ->getResponse(
                    $this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }

        return $this->getJWTForUser($input['email']);
    }

    private function getJWTForUser(
        string $emailAddress,
        int $responseCode = ResponseInterface::HTTP_OK
    )
    {
        try {
            $model = new AuthModel();
            $user = $model->findUserByEmailAddress($emailAddress);
            unset($user['password']);

            $query = $model->findStatusById($emailAddress);
            $isuser = $query['status_user'];

            helper('jwt');

            if ($isuser == "Admin") {
            return $this
                ->getResponse(
                    [
                        'status' => true,
                        'message' => 'Admin authenticated successfully',
                        'data' => $user,
                        'isadmin' => true,
                        'access_token' => getSignedJWTForUser($emailAddress)
                    ]
                );
            } else {
                return $this
                ->getResponse(
                    [
                        'status' => true,
                        'message' => 'User authenticated successfully',
                        'data' => $user,
                        'isadmin' => false,
                        'access_token' => getSignedJWTForUser($emailAddress)
                    ]
                );
            }
        } catch (Exception $exception) {
            return $this
                ->getResponse(
                    [
                        'error' => $exception->getMessage(),
                    ],
                    $responseCode
                );
        }
    }
    
}