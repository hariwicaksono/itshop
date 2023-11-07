<?php 
namespace App\Modules\Media\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Media\Models\MediaModel;
use CodeIgniter\I18n\Time;

class Media extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = MediaModel::class;

	public function create()
    {
        $image = $this->request->getFile('productImage');
        $fileName = $image->getRandomName();
        if ($image !== "") {
            $path = "images/products/";
            $moved = $image->move($path, $fileName);
            if ($moved) {
                $save = $this->model->save([
                    'media_path' => $path . $fileName
                ]);
                if ($save) {
                    return $this->respond(["status" => true, "message" => lang('App.imgSuccess'), "data" => $this->model->getInsertID()], 200);
                } else {
                    return $this->respond(["status" => false, "message" => lang('App.imgFailed'), "data" => []], 200);
                }
            }
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.uploadFailed'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function delete($id = null)
    {
        $hapus = $this->model->find($id);
        if ($hapus) {
            $this->model->delete($id);
            unlink($hapus['media_path']);
            
            $response = [
                'status' => true,
                'message' => lang('App.imgDeleted'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.delFailed'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }
    
}