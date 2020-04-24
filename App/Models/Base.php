<?php
namespace App\Models;
use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\ORM\AbstractModel;

use EasySwoole\ORM\DbManager;


/**
 * 弹幕表
 * Class DanmuList
 */
class Base extends AbstractModel
{
    /**
     * 检查数据是否存在
     * 
     * @return boolen.
     */
    public function checkExistsById($id)
    {
        $data = $this->get($id);

        if($data == null) {
            return false;
        }

        return $data->toArray();
    }
}