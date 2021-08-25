<?php namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{ 
    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['category_id', 'user_id', 'title', 'slug', 'summary', 'body', 'price', 'post_image', 'created_at', 'updated_at'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $skipValidation     = true;

    public function getProduct($id = false)
    {
        $db      = \Config\Database::connect();

        if($id === false){
            $builder = $db->table('products p');
            $builder->select('p.*, c.name as category, u.name as user');
            $builder->join('categories c', 'c.id = p.category_id','left');
            $builder->join('users u', 'u.id = p.user_id');
            $builder->where('c.group', 'product');
            $builder->orderBy('p.id', 'DESC');
            $query = $builder->get();
            return $query->getResultArray();
        } else {
            $builder = $db->table('products p');
            $builder->select('p.*, c.name as category, u.name as user');
            $builder->join('categories c', 'c.id = p.category_id');
            $builder->join('users u', 'u.id = p.user_id');
            $builder->where('p.id', $id);
            $builder->orWhere('p.slug', $id);
            $query = $builder->get();
            return $query->getResultArray();
        }  
    }
     
    public function insertProduct($data)
    {
        return $this->db->table($this->table)->insert($data);
    }
 
    public function updateProduct($data, $id)
    {
        return $this->db->table($this->table)->update($data, ['id' => $id]);
    }
 
    public function deleteProduct($id)
    {
        return $this->db->table($this->table)->delete(['id' => $id]);
    }

    public function count_product()
	{
		return $this->countAll();
	}

    public function searchProduct($id){
        $this->like("title", $id);
        $this->orLike("body", $id);
        $query = $this->get();
        return $query->getResultArray();
    }

    public function searchTag($category){
        $db      = \Config\Database::connect();
        $builder = $db->table('products p');
        $builder->select('p.*, c.name as category, u.name as user');
        $builder->join('categories c', 'c.id = p.category_id');
        $builder->join('users u', 'u.id = p.user_id');
        $builder->where('c.name', $category);
        $builder->orderBy('p.id', 'DESC');
        $query = $builder->get();
        return $query->getResultArray();
    }

}