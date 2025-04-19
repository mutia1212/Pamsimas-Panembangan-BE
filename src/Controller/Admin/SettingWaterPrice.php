<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Helper\JsonResponse;
use Pimple\Psr11\Container;

use App\Model\AuthModel;
use App\Model\SettingWaterPriceModel;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class SettingWaterPrice
{
    private $container;
    private $auth;
    private $user;
    private $model;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->auth                 = new AuthModel($this->container->get('db'));
        $this->model                = new SettingWaterPriceModel($this->container->get('db'));
        $this->user                 = $this->auth->validateToken();

        $roles                      = array('superadmin');

        if(!in_array($this->user->role, $roles)) {
            $this->auth->denyAccess();
        }
    }

    public function index(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];
        $params['role'] = 'staff';
        $list   = $this->model->list($params);

        if (!empty($list['data'])) {
            $result = ['status' => true, 'message' => 'Data ditemukan', 'data' => $list['data']];
        }

        if (!empty($params['page'])) {
            $result['pagination'] = [
                'page' => (int) $params['page'],
                'prev' => $params['page'] > 1,
                'next' => ($list['total'] - ($params['page'] * $params['limit'])) > 0,
                'total' => $list['total']
            ];
        }
        
        return JsonResponse::withJson($response, $result, 200);
    }

    public function detail(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];
        
        $data   = $this->model->detail($params['id']);
        if (!empty($data)) {
            $result = ['status' => true, 'message' => 'Data berhasil ditemukan', 'data' => $data];
        }

        return JsonResponse::withJson($response, $result, 200);
    }

    public function save(Request $request, Response $response): Response
    {
        $post                   = $request->getParsedBody();

        $result                 = $this->model->save($post);

        return JsonResponse::withJson($response, $result, 200);
    }

    public function delete(Request $request, Response $response, $parameters): Response
    {
        $result = $this->model->delete($parameters['id']);
        return JsonResponse::withJson($response, $result, 200);
    }
}
