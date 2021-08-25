<?php namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['post_id', 'name', 'email', 'body', 'active', 'created_at', 'updated_at'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $skipValidation     = true;

    public function getComment($id = false)
    {
        $db      = \Config\Database::connect();
        if($id === false){
            return $this->findAll();
        } else {
            $builder = $db->table('comments c');
            $builder->select('c.*, p.slug');
            $builder->join('posts p', 'p.id = c.post_id');
            $builder->where('c.post_id', $id);
            $builder->orWhere('p.slug', $id);
            $builder->where('c.active', 'true');
            $query = $builder->get();
            return $query->getResultArray();
        }  
    }
     
    public function insertComment($data)
    {
        return $this->db->table($this->table)->insert($data);
    }
 
    public function updateComment($data, $id)
    {
        return $this->db->table($this->table)->update($data, ['id' => $id]);
    }
 
    public function deleteComment($id)
    {
        return $this->db->table($this->table)->delete(['id' => $id]);
    }

    public function count_comment()
	{
		return $this->countAll();
	}

}