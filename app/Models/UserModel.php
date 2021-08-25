<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
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

    protected $skipValidation     = true;

    public function getUser($id = false)
    {
        if($id === false){
            return $this->findAll();
        } else {
            return $this->getWhere(['email' => $id])->getResultArray();
        }  
    }
     
    public function insertUser($data)
    {
        return $this->db->table($this->table)->insert($data);
    }
 
    public function updateUser($data, $id)
    {
        return $this->db->table($this->table)->update($data, ['id' => $id]);
    }
 
    public function deleteUser($id)
    {
        return $this->db->table($this->table)->delete(['id' => $id]);
    }

    public function updatePassword($data, $id)
    {
        return $this->db->table($this->table)->update($data, ['email' => $id]);
    }

    public function count_user()
	{
		return $this->countAll();
	}

}