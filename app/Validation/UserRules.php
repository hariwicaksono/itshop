<?php

namespace App\Validation;

use App\Models\AuthModel;
use Exception;

class AuthRules
{
    public function validateUser(string $str, string $fields, array $data): bool
    {
        try {
            $model = new AuthModel();
            $user = $model->findUserByEmailAddress($data['email']);
            return password_verify($data['password'], $user['password']);
        } catch (Exception $e) {
            return false;
        }
    }
}
