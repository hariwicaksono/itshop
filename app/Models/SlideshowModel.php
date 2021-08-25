<?php namespace App\Models;

use CodeIgniter\Model;

class SlideshowModel extends Model
{
    protected $table = 'slideshows';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['img_slide', 'text_slide', 'created_at', 'updated_at'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
 
    protected $skipValidation     = true;

    public function getSlideshow($id = false)
    {
        if($id === false){
            return $this->findAll();
        } else {
            return $this->getWhere(['id' => $id])->getResult();
        }  
    }
     
    public function insertSlideshow($data)
    {
        return $this->db->table($this->table)->insert($data);
    }
 
    public function updateSlideshow($data, $id)
    {
        return $this->db->table($this->table)->update($data, ['id' => $id]);
    }
 
    public function deleteSlideshow($id)
    {
        return $this->db->table($this->table)->delete(['id' => $id]);
    }

    public function count_slideshow()
	{
		return $this->countAll();
	}

}