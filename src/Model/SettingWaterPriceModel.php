<?php

declare(strict_types=1);

namespace App\Model;

use Exception;

final class SettingWaterPriceModel
{
    protected $database;

    protected function db()
    {
        $pdo = new \Pecee\Pixie\QueryBuilder\QueryBuilderHandler($this->database);
        return $pdo;
    }

    public function __construct(\Pecee\Pixie\Connection $database)
    {
        $this->database       = $database;
    }

    public function buildQueryList($params=null) {
        $getQuery = $this->db()->table('setting_harga_air');

        if (!empty($params['rwId'])) {
            $getQuery->where('setting_harga_air.rw', $params['rwId']);
        }

        return $getQuery;
    }

    public function list($params)
    {
        $getQuery = $this->buildQueryList($params);
        
        $totalData = $getQuery->count();

        if (!empty($params['page'])) {
            $page = $params['page'] == 1 ? $params['page'] - 1 : ($params['page'] * $params['limit']) - $params['limit'];

            $getQuery->limit((int) $params['limit']);
            $getQuery->offset((int) $page);
        }

        $list = $getQuery->get();

        $result = [];
        
        foreach ($list as $data) {
            $data->harga_air = json_decode($data->harga_air);
            $result[] = $data;
        }

        return ['data' => $result, 'total' => $totalData];
    }

    public function save($params) {
        $result                 = ['status' => false, 'message' => 'Data gagal disimpan'];
        
        $data = [
            'rw' => $params['rwId'],
            'harga_air' => json_encode($params['setting_price']),
            'beban' => $params['admin_fee_unmasked'],
            'jatuh_tempo' => $params['deadline'],
        ];
        if (empty($params['id'])) {
            $data = array_merge(['created_at' => date('Y-m-d H:i:s')], $data);
            $userId = $this->db()->table('setting_harga_air')->insert($data);
        } else {
            $userId = $params['id'];
            $this->db()->table('setting_harga_air')->where('id', $params['id'])->update($data);
        }
        $result                 = ['status' => true, 'message' => 'Data gagal disimpan'];

        return $result;
    }

    public function detail($id) {
        $result = $this->db()->table('setting_harga_air')->where('id', $id)->first();

        if (!empty($result)){
            $result->harga_air = json_decode($result->harga_air);
        }

        return $result;
    }

    public function delete($id) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('setting_harga_air')->where('id', $id)->first();

        if (!empty($checkData)) {
            $process = $this->db()->table('setting_harga_air')->where('id', $id)->delete();

            if ($process) {
                $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
            }
        }

        return $result;
    }

    public function deleteBatch($listId) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('setting_harga_air')->whereIn('id', $listId)->get();

        if (!empty($checkData)) {
            $totalSuccess = 0;
            foreach ($checkData as $data) {
                $process = $this->db()->table('setting_harga_air')->where('id', $id)->delete();
                $totalSuccess += $process ? 1 : 0;
            }

            if ($totalSuccess) {
                $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
            }
        }

        return $result;
    }
}
