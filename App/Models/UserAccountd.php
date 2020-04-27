<?php
namespace App\Models;
use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\DbManager;

use App\Models\Base;
/**
 *  foke / 用户余额明细表
 * Class Player
 */
class UserAccountd extends Base
{

    //连接名称
    protected $connectionName = 'foke';

    /**
    *  表名称
    * @var string 
    */
    protected $tableName     = 't_user_accountd';

    // 都是非必选的，默认值看文档下面说明
    protected $autoTimeStamp = false;
    protected $createTime    = 'create_time';
    protected $updateTime    = 'update_time';

    /**
     * 直播明细记录
     * 
     * @param int    $orderId 订单号
     * @param int    $uid     user's id
     * @param number $account 金额
     * @param number $type    类型 5 直播红包 6 直播送礼
     * 
     * @return false | array.
     */
    public function LiveAccountLog($orderId, $uid, $account, $type)
    {

       $this->amount       = $account;
       $this->type         = 1;
       $this->order_id     = $orderId;
       $this->user_id      = $uid;
       $this->add_time     = date('Y-m-d H:i:s', time());
       $this->account_type = 7;
       $this->other_type   = $type;

       
       return $this->save();
    }
}