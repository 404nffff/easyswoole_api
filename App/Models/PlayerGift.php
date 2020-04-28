<?php
namespace App\Models;
use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\ORM\AbstractModel;

use EasySwoole\ORM\DbManager;

use App\Models\Base;
/**
 * 礼物表
 * Class DanmuList
 */
class PlayerGift extends Base
{

    //连接名称
     protected $connectionName = 'foke';

    /**
    *  表名称
    * @var string 
    */
    protected $tableName = 't_player_gift';

    // 都是非必选的，默认值看文档下面说明
    protected $autoTimeStamp = false;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

}