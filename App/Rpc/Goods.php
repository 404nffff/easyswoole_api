<?php
namespace App\Rpc;

use EasySwoole\Rpc\AbstractService;

class Goods extends AbstractService
{

    public function serviceName(): string
    {
        return 'goods';
    }

    public function list()
    {
        $this->response()->setResult([
            [
                'goodsId'=>'100001',
                'goodsName'=>'商品1',
                'prices'=>1124
            ],
            [
                'goodsId'=>'100002',
                'goodsName'=>'商品2',
                'prices'=>599
            ]
        ]);
        $this->response()->setMsg('get goods list success');
    }
}