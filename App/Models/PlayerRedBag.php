<?php
namespace App\Models;
use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\DbManager;

use App\Models\Base;
/**
 *  foke / 活动 红包表
 * Class Player
 */
class PlayerRedBag extends Base
{

    //连接名称
    protected $connectionName = 'foke';

    /**
    *  表名称
    * @var string 
    */
    protected $tableName = 't_player_red_bag';

    // 都是非必选的，默认值看文档下面说明
    protected $autoTimeStamp = true;
    protected $createTime    = 'create_time';
    protected $updateTime    = 'update_time';

    /**
     * 检查数据是否存在 pid
     * 
     * @return false | array.
     */
    public function checkExistsByPid($pid)
    {
        $data = $this->where('red_pid', $pid)->get();

        if($data == null) {
            return false;
        }

        return $data->toArray();
    }
}