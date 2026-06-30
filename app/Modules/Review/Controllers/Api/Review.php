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

    /**
     * Admin: Generate AI reviews for completed orders that don't have reviews yet.
     * 
     * This endpoint will:
     * 1. Find all completed orders (status = 2) that don't have reviews
     * 2. For each order, generate an AI-powered review in Indonesian
     * 3. Create the review with rating 4-5 and human-like Indonesian text
     * 
     * @return JSON response
     */
    public function generateAIReviews()
    {
        // Check if user is admin
        if (session('role') != 1) {
            return $this->respond([
                'status'  => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat menggunakan fitur ini.',
                'data'    => [],
            ], 403);
        }
        
        $this->log->save(['keterangan' => session('first_name') . ' ' . session('last_name') . ' (' . session('email') . ') ' . strtolower(lang('App.do')) . ' Generate AI Reviews: Admin memulai generate review menggunakan AI']);

        // Get all completed orders that don't have reviews yet
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT DISTINCT o.order_id, o.no_order, o.user_id, o.status, o.created_at,
                   c.product_id, p.product_name, u.first_name, u.last_name
            FROM orders o
            JOIN carts c ON c.order_id = o.order_id
            JOIN products p ON p.product_id = c.product_id
            JOIN users u ON u.user_id = o.user_id
            WHERE o.status = 2
            AND o.order_id NOT IN (SELECT DISTINCT order_id FROM reviews WHERE order_id IS NOT NULL)
            ORDER BY o.created_at DESC
        ");

        $ordersWithoutReviews = $query->getResultArray();

        if (empty($ordersWithoutReviews)) {
            return $this->respond([
                'status'  => true,
                'message' => 'Tidak ada order yang perlu diberikan review. Semua order sudah memiliki review.',
                'data'    => ['generated' => 0, 'total' => 0],
            ], 200);
        }

        $generated = 0;
        $failed = 0;
        $errors = [];

        foreach ($ordersWithoutReviews as $order) {
            try {
                // Generate AI review
                $reviewData = $this->generateReviewWithAI($order);
                
                if ($reviewData) {
                    // Generate random timestamps 1-7 days AFTER the order date
                    $orderDate = strtotime($order['created_at']);
                    $randomDaysAfter = rand(1, 7); // 1-7 days after order
                    $randomHours = rand(0, 23);
                    $randomMinutes = rand(0, 59);
                    
                    // created_at: order date + 1-7 days + random hours/minutes
                    $createdAt = date('Y-m-d H:i:s', $orderDate + ($randomDaysAfter * 86400) + ($randomHours * 3600) + ($randomMinutes * 60));
                    
                    // updated_at: 0-2 hours after created_at
                    $updatedAt = date('Y-m-d H:i:s', $orderDate + ($randomDaysAfter * 86400) + ($randomHours * 3600) + ($randomMinutes * 60) + rand(0, 7200));

                    // Save review to database
                    $data = [
                        'order_id'    => $order['order_id'],
                        'product_id'  => $order['product_id'],
                        'user_id'     => $order['user_id'],
                        'rating'      => $reviewData['rating'],
                        'review_text' => $reviewData['review_text'],
                        'status'      => 1, // Auto-approve AI generated reviews
                        'created_at'  => $createdAt,
                        'updated_at'  => $updatedAt,
                    ];

                    $this->model->save($data);
                    $generated++;
                } else {
                    $failed++;
                    $errors[] = "Gagal generate review untuk order #{$order['order_id']}";
                }
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Error untuk order #{$order['order_id']}: " . $e->getMessage();
            }
        }

        $message = "Berhasil generate {$generated} review";
        if ($failed > 0) {
            $message .= ". Gagal: {$failed}";
        }

        return $this->respond([
            'status'  => true,
            'message' => $message,
            'data'    => [
                'generated' => $generated,
                'failed'    => $failed,
                'total'     => count($ordersWithoutReviews),
                'errors'    => $errors,
            ],
        ], 200);
    }

    /**
     * Generate a human-like Indonesian review using AI simulation.
     * In production, this can be replaced with actual AI API (OpenAI, etc.)
     * 
     * @param array $order Order data
     * @return array|null ['rating' => int, 'review_text' => string]
     */
    private function generateReviewWithAI($order)
    {
        $productName = $order['product_name'];
        $userName = $order['first_name'] . ' ' . $order['last_name'];
        
        // Generate random rating between 4 and 5 (positive reviews)
        $rating = rand(4, 5);
        
        // Generate human-like Indonesian review text
        $reviewText = $this->generateIndonesianReview($productName, $rating);
        
        return [
            'rating' => $rating,
            'review_text' => $reviewText,
        ];
    }

    /**
     * Generate natural Indonesian review text based on product and rating.
     * Uses templates and variations to create human-like reviews.
     * 
     * @param string $productName
     * @param int $rating
     * @return string
     */
    private function generateIndonesianReview($productName, $rating)
    {
        // Review templates for 5-star rating
        $templates5Star = [
            "Pelayanan sangat memuaskan! {$productName} sesuai ekspektasi, kualitas barang bagus dan pengiriman cepat. Highly recommended!",
            "Sangat puas dengan pembelian {$productName}. Barang original, harga terjangkau, dan pelayanan customer service ramah. Terima kasih!",
            "Alhamdulillah, {$productName} yang saya beli bagus banget! Kemasan rapi, barang sesuai deskripsi, dan pengiriman cepat. Akan order lagi!",
            "Mantap! {$productName} worth it banget. Kualitas premium, harga bersahabat. Proses transaksi mudah dan aman. Sukses terus untuk toko ini!",
            "Barang {$productName} datang dalam kondisi sempurna. Packaging aman, tidak ada kerusakan. Pelayanan top markotop! Lanjutkan!",
            "Pertama kali beli di sini dan tidak menyesal. {$productName} berkualitas, sesuai foto, dan pengiriman lebih cepat dari estimasi. Recommended!",
            "{$productName} yang saya dapat benar-benar original dan bagus. Harga kompetitif, diskon menarik. Admin juga responsif. Thumbs up!",
            "Sangat recommended! {$productName} kualitasnya bagus, tidak mengecewakan. Proses mudah, pengiriman cepat, dan barang aman sampai tujuan.",
        ];

        // Review templates for 4-star rating
        $templates4Star = [
            "{$productName} lumayan bagus untuk harga segini. Kualitas sesuai, pengiriman cukup cepat. Overall puas, tapi masih ada ruang untuk improvement.",
            "Barang {$productName} sudah sesuai ekspektasi. Kemasan baik, barang aman. Cuma pengirimannya agak lama tapi tidak masalah. Recommended!",
            "Alhamdulillah {$productName} bagus. Kualitas oke, harga pas. Pelayanan cukup memuaskan. Akan beli lagi di sini.",
            "Puas belanja di sini. {$productName} sesuai deskripsi, barang original. Proses mudah, tapi pengiriman bisa lebih cepat lagi.",
            "{$productName} worth it. Kualitas bagus, harga terjangkau. Cuma packing bisa diperbaiki lagi. Overall good experience!",
        ];

        // Select template based on rating
        $templates = ($rating == 5) ? $templates5Star : $templates4Star;
        
        // Randomly select a template
        $reviewText = $templates[array_rand($templates)];
        
        // Add some variation to make it more human-like
        $variations = [
            '',
            '',
            '',
            " " . $this->getRandomEmoticon(),
            " Semoga terus berkembang!",
            " Keep it up!",
        ];
        
        $reviewText .= $variations[array_rand($variations)];
        
        return trim($reviewText);
    }

    /**
     * Get random emoticon to add human touch
     * 
     * @return string
     */
    private function getRandomEmoticon()
    {
        $emoticons = ['😊', '👍', '❤️', '😍', '🙏', '💯', '✨', '🌟', '👏', '🔥'];
        return $emoticons[array_rand($emoticons)];
    }
}
