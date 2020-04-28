<?php
namespace App\Models;
use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\DbManager;

use App\Models\Base;
/**
 *  foke / 活动时间段表
 * Class Player
 */
class LiveStatus extends Base
{

    //连接名称
    protected $connectionName = 'foke';

    /**
    *  表名称
    * @var string 
    */
    protected $tableName = 't_live_status';

    // 都是非必选的，默认值看文档下面说明
    protected $autoTimeStamp = false;
    protected $createTime    = 'create_time';
    protected $updateTime    = 'update_time';

    /**
     * 检查数据是否存在
     * 
     * @return boolen.
     */
    public function checkExistsByIdAndPlayerId($id, $playerId)
    {
        $data = $this->get([
            'id'        => $id,
            'player_id' => $playerId
        ]);

        if($data == null) {
            return false;
        }

        return $data->toArray();
    }
}