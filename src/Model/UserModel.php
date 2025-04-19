<?php

declare(strict_types=1);

namespace App\Model;

use Exception;

final class UserModel
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

    public function countUser()
    {
        return $this->db()->table('users')->count();
    }

    public function buildQueryListStaff($params=null) {
        $getQuery = $this->db()->table('users');
        $getQuery->select($getQuery->raw('users_petugas.*, users.password_raw as password'));
        $getQuery->innerJoin('users_petugas', 'users_petugas.user_id', '=', 'users.id');

        $getQuery->where('role', $params['role']);
        
        if (!empty($params['rwId'])) {
            $getQuery->where('users_petugas.rw', $params['rwId']);
        } if(!empty($params['keywords'])) {
            $keywords = $params['keywords'];
            $getQuery->where('users_petugas.nama', 'LIKE', "%$keywords%");
        }

        return $getQuery;
    }

    public function list($params)
    {
        $getQuery = $this->buildQueryListStaff($params);
        
        $totalData = $getQuery->count();

        if (!empty($params['page'])) {
            $page = $params['page'] == 1 ? $params['page'] - 1 : ($params['page'] * $params['limit']) - $params['limit'];

            $getQuery->limit((int) $params['limit']);
            $getQuery->offset((int) $page);
        }

        $getQuery->orderBy("users_petugas.nama", "asc"); 
        
        $list = $getQuery->get();

        return ['data' => $list, 'total' => $totalData];
    }

    public function saveStaff($params) {
        $result                 = ['status' => false, 'message' => 'Data gagal disimpan'];
        
        $checkUser = $this->db()->table('users')->where('username', $params['email'])->where('id', '!=', $params['id'])->first();
        
        if (!empty($checkUser)) {
            $result['message'] = 'Email sudah terdaftar disistem! Silahkan gunakan email lain';
        } else {
            // register user auth
            $dataUser = [
                'username' => $params['email'],
                'role' => $params['role']
            ];

            if (!empty($params['password'])) {
                $dataUser = array_merge([
                    'password' => password_hash($params['password'], PASSWORD_BCRYPT),
                    'password_raw' => $params['password'],
                ], $dataUser);
            }

            if (empty($params['id'])) {
                $dataUser = array_merge(['created_at' => date('Y-m-d H:i:s')], $dataUser);
                $userId = $this->db()->table('users')->insert($dataUser);
            } else {
                $userId = $params['id'];
                $this->db()->table('users')->where('id', $params['id'])->update($dataUser);
            }

            if (!empty($userId)) {
                // save profile
                $dataProfile = [
                    'user_id' => $userId,
                    'nama' => $params['name'],
                    'email' => $params['email'],
                    'telepon' => $params['phone'],
                    'alamat' => $params['address'],
                    'rw' => $params['rwId'],
                ];
    
                $checkProfile = $this->db()->table('users_petugas')->where('user_id', $userId)->first();
    
                if (empty($checkProfile)) {
                    $dataProfile['created_at'] = date('Y-m-d H:i:s');
                    $this->db()->table('users_petugas')->insert($dataProfile);
                } else {
                    $this->db()->table('users_petugas')->where('id', $checkProfile->id)->update($dataProfile);
                }

                $result                 = ['status' => true, 'message' => 'Data gagal disimpan'];
            }
        }

        return $result;
    }

    public function detailStaff($id) {
        return $this->db()->table('users_petugas')->where('id', $id)->first();
    }

    public function buildQueryListCustomer($params=null) {
        $getQuery = $this->db()->table('users');
        $getQuery->select($getQuery->raw('users_pelanggan.*, users.password_raw as password'));
        $getQuery->innerJoin('users_pelanggan', 'users_pelanggan.user_id', '=', 'users.id');

        $getQuery->where('role', 'customer');
        
        if (!empty($params['rwId'])) {
            $getQuery->where('users_pelanggan.rw', $params['rwId']);
        } if(!empty($params['keywords'])) {
            $keywords = $params['keywords'];
            $getQuery->where('users_pelanggan.nama', 'LIKE', "%$keywords%");
        }

        return $getQuery;
    }

    public function listCustomer($params)
    {
        $getQuery = $this->buildQueryListCustomer($params);
        
        $totalData = $getQuery->count();

        if (!empty($params['page'])) {
            $page = $params['page'] == 1 ? $params['page'] - 1 : ($params['page'] * $params['limit']) - $params['limit'];

            $getQuery->limit((int) $params['limit']);
            $getQuery->offset((int) $page);
        }

        $getQuery->orderBy("users_pelanggan.nama", "asc"); 
        
        $list = $getQuery->get();

        return ['data' => $list, 'total' => $totalData];
    }

    public function saveCustomer($params) {
        $result                 = ['status' => false, 'message' => 'Data gagal disimpan'];
        
        $checkUser = $this->db()->table('users')->where('username', $params['email'])->where('id', '!=', $params['id'])->first();
        
        if (!empty($checkUser)) {
            $result['message'] = 'Email sudah terdaftar disistem! Silahkan gunakan email lain';
        } else {
            // register user auth
            $dataUser = [
                'username' => $params['email'],
                'role' => 'customer'
            ];

            if (!empty($params['password'])) {
                $dataUser = array_merge([
                    'password' => password_hash($params['password'], PASSWORD_BCRYPT),
                    'password_raw' => $params['password'],
                ], $dataUser);
            }

            if (empty($params['id'])) {
                $dataUser = array_merge(['created_at' => date('Y-m-d H:i:s')], $dataUser);
                $userId = $this->db()->table('users')->insert($dataUser);
            } else {
                $userId = $params['id'];
                $this->db()->table('users')->where('id', $params['id'])->update($dataUser);
            }

            if (!empty($userId)) {
                // save profile
                $dataProfile = [
                    'user_id' => $userId,
                    'nama' => $params['name'],
                    'email' => $params['email'],
                    'telepon' => $params['phone'],
                    'alamat' => $params['address'],
                    'rw' => $params['rwId'],
                    'no_pel' => $params['number'],
                    'no_rek' => $params['bank_number'],
                    'status' => $params['status'],
                    'category_id' => $params['category_id'],
                ];
    
                $checkProfile = $this->db()->table('users_pelanggan')->where('user_id', $userId)->first();
    
                if (empty($checkProfile)) {
                    $dataProfile['created_at'] = date('Y-m-d H:i:s');
                    $this->db()->table('users_pelanggan')->insert($dataProfile);
                } else {
                    $this->db()->table('users_pelanggan')->where('id', $checkProfile->id)->update($dataProfile);
                }

                $result                 = ['status' => true, 'message' => 'Data gagal disimpan'];
            }
        }

        return $result;
    }

    public function detailCustomer($id) {
        return $this->db()->table('users_pelanggan')->where('id', $id)->first();
    }

    public function deleteUser($id) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('users')->where('id', $id)->first();

        if (!empty($checkData)) {
            $process = $this->db()->table('users')->where('id', $id)->delete();

            if ($process) {
                $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
            }
        }

        return $result;
    }

    public function deleteUserBatch($listId) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('users')->whereIn('id', $listId)->get();

        if (!empty($checkData)) {
            $totalSuccess = 0;
            foreach ($checkData as $data) {
                $process = $this->db()->table('users')->where('id', $id)->delete();
                $totalSuccess += $process ? 1 : 0;
            }

            if ($totalSuccess) {
                $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
            }
        }

        return $result;
    }
}
