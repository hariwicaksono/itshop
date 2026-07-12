<?php

namespace App\Modules\Review\Models;

use CodeIgniter\Model;

class ReviewModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'reviews';
    protected $primaryKey       = 'review_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get all reviews joined with product, user, and booking info.
     *
     * @param bool|int $status Filter by status (false = all)
     * @param bool|int $productId  Filter by product (false = all)
     * @return array
     */
    public function getReviews($status = false, $productId = false)
    {
        $this->select("{$this->table}.*, products.product_name, users.first_name, users.last_name, users.email, orders.no_order, orders.status as order_status");
        $this->join('products', "products.product_id = {$this->table}.product_id", 'left');
        $this->join('users', "users.user_id = {$this->table}.user_id", 'left');
        $this->join('orders', "orders.order_id = {$this->table}.order_id", 'left');
        if ($status !== false && $status !== '') {
            $this->where("{$this->table}.status", $status);
        }
        if ($productId !== false && $productId !== '') {
            $this->where("{$this->table}.product_id", $productId);
        }
        $this->orderBy("{$this->table}.created_at", 'DESC');
        return $this->findAll();
    }

    /**
     * Get a single review with full details.
     *
     * @param int $id
     * @return array|null
     */
    public function showReview($id)
    {
        $this->select("{$this->table}.*, products.product_name, users.first_name, users.last_name, users.email, orders.no_order");
        $this->join('products', "products.product_id = {$this->table}.product_id", 'left');
        $this->join('users', "users.user_id = {$this->table}.user_id", 'left');
        $this->join('orders', "orders.order_id = {$this->table}.order_id", 'left');
        $this->where("{$this->table}.review_id", $id);
        return $this->first();
    }

    /**
     * Get all approved reviews for a specific product (public-facing).
     *
     * @param int $productId
     * @param int $limit
     * @return array
     */
    public function getApprovedReviewsByProduct($productId, $limit = 0)
    {
        $this->select("{$this->table}.review_id, {$this->table}.rating, {$this->table}.review_text, {$this->table}.created_at, users.first_name, users.last_name");
        $this->join('users', "users.user_id = {$this->table}.user_id", 'left');
        $this->where("{$this->table}.product_id", $productId);
        $this->where("{$this->table}.status", 1); // approved only
        $this->orderBy("{$this->table}.created_at", 'DESC');
        if ($limit > 0) {
            $this->limit($limit);
        }
        return $this->findAll();
    }

    /**
     * Get the average rating and review count for a product.
     *
     * @param int $productId
     * @return array ['average' => float, 'count' => int]
     */
    public function getAverageRatingByProduct($productId)
    {
        $this->selectAvg('rating', 'average');
        $this->selectCount('review_id', 'count');
        $this->where('product_id', $productId);
        $this->where('status', 1); // approved only
        $result = $this->first();
        return [
            'average' => $result ? round((float)$result['average'], 1) : 0,
            'count'   => $result ? (int)$result['count'] : 0,
        ];
    }

    /**
     * Get average ratings for multiple products in a single query (batch).
     *
     * @param array $productIds Array of product IDs
     * @return array Associative array keyed by product_id: { "1": { average: 4.5, count: 10 }, ... }
     */
    public function getAverageRatingsByProducts(array $productIds)
    {
        if (empty($productIds)) {
            return [];
        }

        $this->select('product_id');
        $this->selectAvg('rating', 'average');
        $this->selectCount('review_id', 'count');
        $this->whereIn('product_id', $productIds);
        $this->where('status', 1); // approved only
        $this->groupBy('product_id');
        $results = $this->findAll();

        $ratings = [];
        foreach ($results as $row) {
            $ratings[$row['product_id']] = [
                'average' => round((float)$row['average'], 1),
                'count'   => (int)$row['count'],
            ];
        }

        // Ensure all requested product IDs have an entry (even if no reviews)
        foreach ($productIds as $id) {
            if (!isset($ratings[$id])) {
                $ratings[$id] = [
                    'average' => 0,
                    'count'   => 0,
                ];
            }
        }

        return $ratings;
    }

    /**
     * Check if a user has already reviewed a specific product in an order.
     *
     * @param int $orderId
     * @param int $productId
     * @param int $userId
     * @return array|null
     */
    public function checkExistingReview($orderId, $productId, $userId)
    {
        return $this->where('order_id', $orderId)
            ->where('product_id', $productId)
            ->where('user_id', $userId)
            ->first();
    }
}
