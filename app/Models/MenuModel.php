<?php namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{ 
    protected $table = 'menus';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['menu', 'url', 'menu_order', 'parent_id'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $skipValidation     = true;

    public function getMenu($id = false)
    {
        if($id === false){
            $this->orderBy('menu_order', 'ASC');
            return $this->findAll();
        } else {
            return $this->getWhere(['id' => $id])->getResult();
        }  
    }
     
    public function insertMenu($data)
    {
        return $this->db->table($this->table)->insert($data);
    }
 
    public function updateMenu($data, $id)
    {
        return $this->db->table($this->table)->update($data, ['id' => $id]);
    }
 
    public function deleteMenu($id)
    {
        return $this->db->table($this->table)->delete(['id' => $id]);
    }

    public function count_menu()
	{
		return $this->countAll();
	}

}