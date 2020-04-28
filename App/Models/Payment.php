<?php
namespace App\Models;
use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\DbManager;

use App\Models\Base;
/**
 *  foke / 支付表
 * Class Player
 */
class Payment extends Base
{

    //连接名称
    protected $connectionName = 'foke';

    /**
    *  表名称
    * @var string 
    */
    protected $tableName = 't_payment';

    // 都是非必选的，默认值看文档下面说明
    protected $autoTimeStamp = false;
    protected $createTime    = 'create_time';
    protected $updateTime    = 'update_time';


    /**
     * 支付订单关联
     * 
     * @param int    $id      主键id     
     * @param int    $orderId 订单号
     *
     * @return false | array.
     */
    public function payOrderIdConnect($id, $orderId)
    {
        $userData =  $this->get($id);

        if(!$userData) {
            return false;
        }

        $updateStatus = $userData->update(
            //update
            [
            'order_id' => $orderId,
            ], 
            //where
            [
                'id' => $id
            ]
        );

        if(!$updateStatus){
            return false;
        }
        
        return true;
    }

   
}