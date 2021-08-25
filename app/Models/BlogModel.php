<?php namespace App\Models;

use CodeIgniter\Model;

class BlogModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['category_id', 'user_id', 'title', 'slug', 'summary', 'body', 'post_image', 'date', 'time', 'created_at', 'updated_at'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $skipValidation     = true;

    public function getBlog($id = false)
    {
        $db      = \Config\Database::connect();

        if($id === false){
            $builder = $db->table('posts p');
            $builder->select('p.*, c.name as category, u.name as user');
            $builder->join('categories c', 'c.id = p.category_id','left');
            $builder->join('users u', 'u.id = p.user_id');
            $builder->where('c.group', 'blog');
            $builder->orderBy('p.id', 'DESC');
            $query = $builder->get();
            return $query->getResultArray();
        } else {
            $builder = $db->table('posts p');
            $builder->select('p.*, c.name as category, u.name as user');
            $builder->join('categories c', 'c.id = p.category_id');
            $builder->join('users u', 'u.id = p.user_id');
            $builder->where('p.id', $id);
            $builder->orWhere('p.slug', $id);
            $query = $builder->get();
            return $query->getResultArray();
        }  
    }
     
    public function insertBlog($data)
    {
        return $this->db->table($this->table)->insert($data);
    }
 
    public function updateBlog($data, $id)
    {
        return $this->db->table($this->table)->update($data, ['id' => $id]);
    }
 
    public function deleteBlog($id)
    {
        return $this->db->table($this->table)->delete(['id' => $id]);
    }

    public function count_blog()
	{
		return $this->countAll();
	}

    public function searchBlog($id){
        $this->like("title", $id);
        $this->orLike("body", $id);
        $query = $this->get();
        return $query->getResultArray();
    }

    public function searchTag($category){
        $db      = \Config\Database::connect();
        $builder = $db->table('posts p');
        $builder->select('p.*, c.name as category, u.name as user');
        $builder->join('categories c', 'c.id = p.category_id');
        $builder->join('users u', 'u.id = p.user_id');
        $builder->where('c.name', $category);
        $builder->orderBy('p.id', 'DESC');
        $query = $builder->get();
        return $query->getResultArray();
    }

}