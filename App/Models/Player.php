<?php
namespace App\Models;
use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\ORM\AbstractModel;

use EasySwoole\ORM\DbManager;


use App\Models\Base;
/**
 * 弹幕表
 * Class DanmuList
 */
class Player extends Base
{

    //连接名称
    protected $connectionName = 'mysql2';

    /**
    *  表名称
    * @var string 
    */
    protected $tableName = 't_player';

    // 都是非必选的，默认值看文档下面说明
    protected $autoTimeStamp = false;
    protected $createTime    = 'create_time';
    protected $updateTime    = 'update_time';
}