<?php
namespace App\Models;
use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\ORM\AbstractModel;

use EasySwoole\ORM\DbManager;
/**
 * 弹幕表
 * Class DanmuList
 */
class DanmuList extends AbstractModel
{

    /**
    *  表名称
    * @var string 
    */
    protected $tableName = 'danmu_list';

    // 都是非必选的，默认值看文档下面说明
    protected $autoTimeStamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

}