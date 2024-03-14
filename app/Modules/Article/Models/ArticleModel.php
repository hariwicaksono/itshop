<?php

namespace App\Modules\Article\Models;

use CodeIgniter\Model;

class ArticleModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'articles';
    protected $primaryKey           = 'article_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = false;
    protected $allowedFields        = [];

    // Dates
    protected $useTimestamps        = true;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = '';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    public function getArticles()
    {
        $this->select("{$this->table}.*, m.media_path, u.first_name, u.last_name, u.role, u.biography, c.category_name, c.category_slug");
        $this->join("media m", "m.media_id = {$this->table}.article_image", "left");
        $this->join("users u", "u.user_id = {$this->table}.user_id");
        $this->join("category c", "c.category_id = {$this->table}.category_id");
        $this->orderBy("{$this->table}.created_at", 'DESC');
        return $this->findAll();
    }

    public function getAllArticles($page = false, $limit = false, $orderBy = false)
    {
        $offset = ($page - 1) * $limit;
        $this->select("{$this->table}.*, m.media_path, u.first_name, u.last_name, u.role, u.biography, c.category_name, c.category_slug");
        $this->join("media m", "m.media_id = {$this->table}.article_image", "left");
        $this->join("users u", "u.user_id = {$this->table}.user_id");
        $this->join("category c", "c.category_id = {$this->table}.category_id");
        $this->where("{$this->table}.active", 1);
        if ($orderBy == 'created_old') {
            $this->orderBy("{$this->table}.created_at", "ASC");
        } else {
            $this->orderBy("{$this->table}.created_at", "DESC");
        }
        $query = $this->findAll($limit, $offset);
        return $query;
    }

    public function showArticle($slug = false)
    {
        $this->select("{$this->table}.*, m.media_path, u.first_name, u.last_name, u.role, u.biography, c.category_name, c.category_slug");
        $this->join("media m", "m.media_id = {$this->table}.article_image", "left");
        $this->join("users u", "u.user_id = {$this->table}.user_id");
        $this->join("category c", "c.category_id = {$this->table}.category_id");
        $this->where("{$this->table}.slug", $slug);
        $this->where("{$this->table}.active", 1);
        return $this->first();
    }
}
