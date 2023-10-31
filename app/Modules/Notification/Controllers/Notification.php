<?php

namespace  App\Modules\Notification\Controllers;

use App\Controllers\BaseController;
use App\Modules\Order\Models\OrderModel;
use App\Modules\User\Models\UserModel;
use App\Libraries\Settings;
use App\Modules\Log\Models\LogModel;
use App\Modules\Tracking\Models\TrackingModel;
use CodeIgniter\I18n\Time;

class Notification extends BaseController
{
    protected $order;
    protected $user;
    protected $setting;
    protected $log;
    protected $tracking;

    public function __construct()
    {
        //memanggil Model
        $this->order = new OrderModel();
        $this->user = new UserModel();
        $this->setting = new Settings();
        $this->log = new LogModel();
        $this->tracking = new TrackingModel();
    }

    public function index()
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;

        $notif = new \Midtrans\Notification();

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;
        $dateTime = $notif->transaction_time;

        //$message = 'ok';

        $order = $this->order->where('no_order', $order_id)->first();
        $orderId = $order['order_id'];
        $orderUser = $order['user_id'];
        $orderQty = $order['qty'];
        $orderTotal = $order['total'];
        $orderNote = $order['note'];

        $user = $this->user->find($orderUser);
        $userEmail = $user['email'];
        $userPhone = $user['phone'];

        // Send Email New Order
        helper('email');
        $email = $this->setting->info['company_email2'];
        $dataEmail = [
            'no_order' => $order_id,
            'created_at' => $dateTime,
            'email' => $userEmail,
            'phone' => $userPhone,
            'qty' => $orderQty,
            'total' => $orderTotal,
            'note' => $orderNote,
        ];

        if ($transaction == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    // TODO set payment status in merchant's database to 'Challenge by FDS'
                    // TODO merchant should decide whether this transaction is authorized or not in MAP
                    $message = "Transaction order_id: " . $order_id . " is challenged by FDS";

                    // Update Tabel Order
                    $this->order->update($orderId, ['status' => 0, 'status_payment' => 'challenged']);

                    // Save data Tracking
                    $this->tracking->save(["order_id" => $orderId, "tracking_information" => "Menunggu pembayaran. Keterangan: " . $message]);
                } else {
                    // TODO set payment status in merchant's database to 'Success'
                    $message = "Transaction order_id: " . $order_id . " successfully captured using " . $type;

                    // Save data Tracking
                    $this->tracking->save(["order_id" => $order_id, "tracking_information" => "Pembayaran " . $type . " telah berhasil"]);

                    // Update Tabel Order
                    $this->order->update($orderId, ['status' => 1, 'status_payment' => 'success']);

                    // Send Email
                    sendEmail("Pesanan Baru #$order_id Siap Dikirim", $email, view('App\Modules\Order\Views\email/order_new_pg', $dataEmail));
                }
            }
        } elseif ($transaction == 'settlement') {
            // TODO set payment status in merchant's database to 'Settlement'
            $message = "Transaction order_id: " . $order_id . " successfully transfered using " . $type;

            // Update Tabel Order
            $this->order->update($orderId, ['status' => 1, 'status_payment' => 'settlement']);

            // Save data Tracking
            $this->tracking->save(["order_id" => $orderId, "tracking_information" => "Pembayaran telah terkonfirmasi. Keterangan: " . $message]);

            // Send Email
            sendEmail("Pesanan Baru #$order_id Siap Dikirim", $email, view('App\Modules\Order\Views\email/order_new_pg', $dataEmail));
        } elseif ($transaction == 'pending') {
            // TODO set payment status in merchant's database to 'Pending'
            $message = "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;

            // Update Tabel Order
            $this->order->update($orderId, ['status' => 0, 'status_payment' => 'pending']);

            // Save data Tracking
            $this->tracking->save(["order_id" => $orderId, "tracking_information" => "Menunggu pembayaran dan konfirmasi. Keterangan: " . $message]);
            
        } elseif ($transaction == 'deny') {
            // TODO set payment status in merchant's database to 'Denied'
            $message = "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";

            // Update Tabel Order
            $this->order->update($orderId, ['status' => 3, 'status_payment' => 'denied']);

            // Save data Tracking
            $this->tracking->save(["order_id" => $orderId, "tracking_information" => "Pesanan dibatalkan sistem. Keterangan: " . $message]);
        } elseif ($transaction == 'expire') {
            // TODO set payment status in merchant's database to 'expire'
            $message = "Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";

            // Update Tabel Order
            $this->order->update($orderId, ['status' => 3, 'status_payment' => 'expired']);

            // Save data Tracking
            $this->tracking->save(["order_id" => $orderId, "tracking_information" => "Pesanan dibatalkan sistem. Keterangan: " . $message]);
        } elseif ($transaction == 'cancel') {
            // TODO set payment status in merchant's database to 'Denied'
            $message = "Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";

            // Update Tabel Order
            $this->order->update($orderId, ['status' => 3, 'status_payment' => 'canceled']);

            // Save data Tracking
            $this->tracking->save(["order_id" => $orderId, "tracking_information" => "Pesanan dibatalkan sistem. Keterangan: " . $message]);
        }

        // Save data Log
        $this->log->save(['keterangan' => $message]);
    }
}
