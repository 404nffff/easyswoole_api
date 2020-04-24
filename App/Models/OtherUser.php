<?php
namespace App\Models;
use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\DbManager;


use App\Models\Base;
/**
 *  foke / 用户表
 * Class DanmuList
 */
class OtherUser extends Base
{

    //连接名称
    protected $connectionName = 'foke';

    /**
    *  表名称
    * @var string 
    */
    protected $tableName     = 't_other_user';

    // 都是非必选的，默认值看文档下面说明
    protected $autoTimeStamp = false;
    protected $createTime    = 'create_time';
    protected $updateTime    = 'update_time';


     /**
     * 检查数据是否存在 unionid
     * 
     * @return false | array.
     */
    public function checkExistsByUnionid($unionid)
    {
        $data = $this->where('unionid', $unionid)->get();

        if($data == null) {
            return false;
        }

        return $data->toArray();
    }

}