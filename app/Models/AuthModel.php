<?php namespace App\Models;

use CodeIgniter\Model;
use Exception;

class AuthModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['email', 'username', 'password', 'name', 'status_user', 'status_active', 'created_at', 'updated_at'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    //protected $skipValidation     = true;

    public function cek_login($user,$password)
	{
        return $this->db->table('users')
            ->where('email',$user)
            ->where('password',$password)
            ->get()->getResult();
	}

    public function findUserByEmailAddress(string $emailAddress)
    {
        $user = $this
            ->asArray()
            ->where(['email' => $emailAddress])
            ->first();

        if (!$user)
            throw new Exception('User does not exist for specified email address');

        return $user;
    }

    public function findStatusById(string $emailAddress)
    {
        $user = $this
            ->select('status_user')
            ->asArray()
            ->where(['email' => $emailAddress])
            ->first();

        if (!$user) throw new Exception('Could not find client for specified ID');

        return $user;
    }


}