<?php namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id'; 

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['brand', 'company', 'website', 'phone', 'email', 'landing_intro', 'landing_img', 'theme','updated_at'];
    protected $useTimestamps = false;
    protected $updatedField  = 'updated_at';

    protected $skipValidation     = true;

    public function getSetting($id)
    {
        return $this->getWhere(['id' => $id])->getResult();  
    }
     
    public function insertSetting($data)
    {
        return $this->db->table($this->table)->insert($data);
    }
 
    public function updateSetting($data, $id)
    {
        return $this->db->table($this->table)->update($data, ['id' => $id]);
    }
 
    public function deleteSetting($id)
    {
        return $this->db->table($this->table)->delete(['id' => $id]);
    }

}