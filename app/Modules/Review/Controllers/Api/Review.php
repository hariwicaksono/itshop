<?php

namespace App\Modules\Review\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Review\Models\ReviewModel;
use App\Modules\Order\Models\OrderModel;
use App\Modules\Log\Models\LogModel;
use App\Libraries\Settings;

class Review extends BaseControllerApi
{
    protected $format    = 'json';
    protected $modelName = ReviewModel::class;
    protected $setting;
    protected $log;
    protected $order;

    public function __construct()
    {
        $this->setting = new Settings();
        $this->log = new LogModel();
        $this->order = new OrderModel();
    }

    /**
     * Admin: List all reviews with optional filters.
     */
    public function index()
    {
        $input = $this->request->getVar();
        $status = $input['status'] ?? false;
        $productId = $input['product_id'] ?? false;
        $data = $this->model->getReviews($status, $productId);

        if (!empty($data)) {
            return $this->respond([
                'status'  => true,
                'message' => lang('App.getSuccess'),
                'data'    => $data,
            ], 200);
        }
        return $this->respond([
            'status'  => false,
            'message' => lang('App.noData'),
            'data'    => [],
        ], 200);
    }

    public function show($id = null)
    {
        $data = $this->model->showReview($id);
        if ($data) {
            return $this->respond([
                'status'  => true,
                'message' => lang('App.getSuccess'),
                'data'    => $data,
            ], 200);
        }
        return $this->respond([
            'status'  => false,
            'message' => lang('App.noData'),
            'data'    => [],
        ], 200);
    }

    /**
     * Submit a review for a completed order.
     *
     * Validation:
     * - Order must exist and belong to the logged-in user
     * - Order must be completed (order_status = 2)
     * - No existing review for this order (one review per order)
     * - Rating must be 1-5
     */
    public function create()
    {
        $rules = [
            'order_id' => 'required|numeric',
            'rating'     => 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[5]',
        ];

        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => lang('App.isRequired'),
                'data'    => $this->validator->getErrors(),
            ]);
        }

        // Ambil input: mendukung JSON dan POST secara terpadu
        if ($this->request->getJSON()) {
            $input = $this->request->getJSON(true);
        } else {
            $input = $this->request->getPost();
        }

        $orderId = $input['order_id'];
        $productId = $input['product_id'];
        $rating = (int)$input['rating'];
        $reviewText = $input['review_text'] ?? null;
        $userId = session('id') ?? null;

        if (!$userId) {
            return $this->respond([
                'status'  => false,
                'message' => 'Anda harus login untuk memberikan ulasan.',
                'data'    => [],
            ], 200);
        }

        // Verify order exists and belongs to this user
        $order = $this->order->find($orderId);
        if (!$order) {
            return $this->respond([
                'status'  => false,
                'message' => 'Order tidak ditemukan.',
                'data'    => [],
            ], 200);
        }

        if ((int)$order['user_id'] !== (int)$userId && session('role') != 1) {
            return $this->respond([
                'status'  => false,
                'message' => 'Anda hanya dapat mengulas order Anda sendiri.',
                'data'    => [],
            ], 200);
        }

        // Verify order is completed (status = 2)
        if ((int)$order['status'] !== 2) {
            return $this->respond([
                'status'  => false,
                'message' => 'Hanya order yang sudah selesai (mobil sudah dikembalikan) yang dapat diulas.',
                'data'    => [],
            ], 200);
        }

        // Check for existing review (one review per order)
        $existingReview = $this->model->checkExistingReview($orderId, $userId);
        if ($existingReview) {
            return $this->respond([
                'status'  => false,
                'message' => 'Anda sudah memberikan ulasan untuk order ini.',
                'data'    => [],
            ], 200);
        }

        $data = [
            'order_id'  => $orderId,
            'product_id'  => $productId,
            'user_id'     => $userId,
            'rating'      => $rating,
            'review_text' => $reviewText,
            'status'      => 0, // pending - awaiting admin approval
        ];

        $this->model->save($data);
        $insertId = $this->model->getInsertID();

        return $this->respond([
            'status'  => true,
            'message' => 'Terima kasih! Ulasan Anda akan ditampilkan setelah disetujui admin.',
            'data'    => ['review_id' => $insertId],
        ]);
    }

    public function update($id = null)
    {
        $review = $this->model->find($id);
        if (!$review) {
            return $this->respond([
                'status'  => false,
                'message' => lang('App.noData'),
            ]);
        }

        if ($this->request->getJSON()) {
            $input = $this->request->getJSON(true);
        } else {
            $input = $this->request->getRawInput();
        }

        $data = [];
        if (isset($input['rating'])) {
            $data['rating'] = (int)$input['rating'];
        }
        if (isset($input['review_text'])) {
            $data['review_text'] = $input['review_text'];
        }
        if (isset($input['status'])) {
            $data['status'] = (int)$input['status'];
        }

        $this->model->update($id, $data);

        return $this->respond([
            'status'  => true,
            'message' => lang('App.updSuccess'),
            'data'    => [],
        ]);
    }

    public function delete($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) {
            return $this->respond([
                'status'  => false,
                'message' => lang('App.delFailed'),
                'data'    => [],
            ], 404);
        }

        $this->model->delete($id);

        return $this->respond([
            'status'  => true,
            'message' => lang('App.delSuccess'),
            'data'    => [],
        ], 200);
    }

    /**
     * Admin: Approve (1) or reject (2) a review.
     */
    public function setStatus($id = null)
    {
        if ($this->request->getJSON()) {
            $input = $this->request->getJSON(true);
        } else {
            $input = $this->request->getRawInput();
        }

        $status = $input['status'] ?? null;
        if ($status === null) {
            return $this->respond([
                'status'  => false,
                'message' => lang('App.isRequired'),
                'data'    => ['status' => 'Status is required'],
            ]);
        }

        $data = ['status' => (int)$status];
        $this->model->update($id, $data);

        return $this->respond([
            'status'  => true,
            'message' => lang('App.updSuccess'),
            'data'    => [],
        ]);
    }

    /**
     * Public: Get all approved reviews for a specific product.
     */
    public function getByProduct($productId = null)
    {
        $limit = (int)($this->request->getVar('limit') ?? 0);
        $data = $this->model->getApprovedReviewsByProduct($productId, $limit);
        $rating = $this->model->getAverageRatingByProduct($productId);

        return $this->respond([
            'status'   => true,
            'message'  => lang('App.getSuccess'),
            'data'     => $data,
            'rating'   => $rating,
        ], 200);
    }

    /**
     * Public: Get average rating and count for a specific product.
     */
    public function getRating($productId = null)
    {
        $rating = $this->model->getAverageRatingByProduct($productId);

        return $this->respond([
            'status'  => true,
            'message' => lang('App.getSuccess'),
            'data'    => $rating,
        ], 200);
    }

    /**
     * Public: Get ratings for multiple products in a single request (batch).
     * 
     * Query param: product_ids - comma-separated list of product IDs
     * Example: GET /api/home/review/ratings-batch?product_ids=1,2,3,4,5
     * 
     * Response: { status: true, data: { "1": { average: 4.5, count: 10 }, "2": { ... } } }
     */
    public function getRatingsBatch()
    {
        $productIdsInput = $this->request->getVar('product_ids');
        if (empty($productIdsInput)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Parameter product_ids wajib diisi.',
                'data'    => [],
            ], 200);
        }

        // Parse comma-separated IDs, filter to only numeric values
        $ids = explode(',', $productIdsInput);
        $ids = array_map('trim', $ids);
        $ids = array_filter($ids, function ($id) {
            return is_numeric($id) && $id > 0;
        });
        $ids = array_values(array_unique($ids));

        if (empty($ids)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Tidak ada product_id valid yang ditemukan.',
                'data'    => [],
            ], 200);
        }

        // Get ratings for all products in one query
        $ratings = $this->model->getAverageRatingsByProducts($ids);

        return $this->respond([
            'status'  => true,
            'message' => lang('App.getSuccess'),
            'data'    => $ratings,
        ], 200);
    }
}
