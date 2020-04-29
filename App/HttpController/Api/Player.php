<?php
/**
 * 直播相关接口
 * 
 * Class Qiniu
 */
namespace App\HttpController\Api;


use App\HttpController\Api\Base;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\EasySwoole\ServerManager;

use EasySwoole\EasySwoole\Config as GlobalConfig;


use App\Models\Player as PlayerModel;

use App\Models\SUser;
use App\Models\PlayerGift;
use App\Models\PlayerGiftInfo;
use App\Models\OtherUser;
use App\Models\LiveStatus;




class Player extends Base
{  

    /**
     * 送礼物
     * 
     * @param string unionid  user unionid.
     * @param string playerId 活动id.
     * @param int    rid      红包id.
     * 
     * @return json | null.
     */
    public function giftSend()
    {
        $giftsid         = $this->request()->getRequestParam('giftsid');
        $giftsCountPrice = $this->request()->getRequestParam('giftsCountPrice');
        $giftCount       = $this->request()->getRequestParam('giftCount');
        $unionid         = $this->request()->getRequestParam('unionid');
        $playerId        = $this->request()->getRequestParam('playerId');
        $payId           = $this->request()->getRequestParam('payId');
        $playerTimeId    = $this->request()->getRequestParam('playerTimeId');
        

        if(
            !is_numeric($giftsid) || 
            !is_numeric($giftsCountPrice) || 
            !is_numeric($giftCount) || 
            !is_numeric($playerId) || 
            !is_numeric($playerTimeId) || 
            !is_numeric($payId) || 
            $unionid == ''
        ) {
            $this->writeJson('400', null, '参数错误');
            return false;
        }

        if(strlen($giftsCountPrice) > 8) {
            $this->writeJson('403', null, '总金额最大长度为8');
        }

        if(strlen($giftCount) > 8) {
            $this->writeJson('403', null, '个数最大长度为8');
        }

        

        $resForPlayer = PlayerModel::create()->checkExistsById($playerId);

        if(!$resForPlayer) {
            $this->writeJson('999', null, '活动不存在');
            return false;
        }

       
        
        $playerName       = $resForPlayer['name'];
        $playerMasterId   = $resForPlayer['player_master_id'];
        $playerMasterName = $resForPlayer['player_master_name'];


        $resForLiveStatus = LiveStatus::create()->checkExistsByIdAndPlayerId($playerTimeId, $playerId);

        if(!$resForLiveStatus) {
            $this->writeJson('999', null, '活动时间段不存在');
            return false;
        }
        $playerLiveTime = $resForLiveStatus['live_time'];


        $resForSUser = SUser::create()->checkExistsById($playerMasterId);

        if(!$resForSUser) {
            $this->writeJson('999', null, '用户不存在');
            return false;
        }


        $resForOther = OtherUser::create()->checkExistsByUnionid($unionid);

        if(!$resForOther) {
            $this->writeJson('999', null, '用户不存在');
            return false;
        }

        $otherUserId = $resForOther['user_id'];


        $resForGift = PlayerGift::create()->checkExistsById($giftsid);

        if(!$resForGift) {
            $this->writeJson('999', null, '礼物不存在');
            return false;
        }

        $giftImg   = $resForGift['image_url'];
        $giftPrice = $resForGift['price'];




        $playGiftInfo = PlayerGiftInfo::create();

        $playGiftInfo->player_id          = $playerId;
        $playGiftInfo->player_time_id     = $playerTimeId;
        $playGiftInfo->player_name        = $playerName;
        $playGiftInfo->player_live_time   = $playerLiveTime;
        $playGiftInfo->player_master_id   = $playerMasterId;
        $playGiftInfo->player_master_name = $playerMasterName;
        $playGiftInfo->other_user_id      = $otherUserId;
        $playGiftInfo->other_uesr_unionid = $unionid;
        $playGiftInfo->player_gift_id     = $giftsid;
        $playGiftInfo->gift_total_money   = $giftsCountPrice;
        $playGiftInfo->gift_money         = $giftPrice;
        $playGiftInfo->gift_num           = $giftCount;
        $playGiftInfo->gift_send_time     = time();
        $playGiftInfo->payment_id         = $payId;


        $giftsInfoId = $playGiftInfo->save();
        if($giftsInfoId == null) {
            $this->writeJson('999', null, '操作失败');
            return false;
        }

        if(!SUser::create()->amountUpdate($giftsInfoId, $payId, $playerMasterId, $giftsCountPrice)) {
            $this->writeJson('998', null, '操作失败');
            return false;
        }

        $pushData = [
            'action'  => 'giftSend', 
            'content' => ['giftImg' => $giftImg, 'giftNum' => $giftCount, 'giftInfoId' => $giftsInfoId], 
            'roomId'  => $playerId, 
            'uid'     => $unionid
            ];

        $this->redisPush($pushData);
        
        $this->writeJson('200', null, '打赏成功');
    }

    /**
     * 消息推送
     * 
     * @param string       $action       动作名称.
     * @param string|array $content     发送内容.
     * @param int          $playerId     活动id.
     * @param int          $playerTimeId 活动时间段id.
     * @param int          $unionid      用户unionid.
     * 
     * @return json | null.
     */
    public function msgPush()
    {
        $action       = $this->request()->getRequestParam('action');
        $content      = $this->request()->getRequestParam('content');
        $playerId     = $this->request()->getRequestParam('playerId');
        $unionid      = $this->request()->getRequestParam('unionid');
        $playerTimeId = $this->request()->getRequestParam('playerTimeId');

        if(
            !is_numeric($playerId) || 
            $unionid == '' ||
            $action == '' ||
            $content == ''
        ) {
            $this->writeJson('400', null, '参数错误');
            return false;
        }

        $resForPlayer = PlayerModel::create()->checkExistsById($playerId);

        if(!$resForPlayer) {
            $this->writeJson('999', null, '活动不存在');
            return false;
        }
        
        $resForLiveStatus = LiveStatus::create()->checkExistsByIdAndPlayerId($playerTimeId, $playerId);

        if(!$resForLiveStatus) {
            $this->writeJson('999', null, '活动时间段不存在');
            return false;
        }


        $resForOther = OtherUser::create()->checkExistsByUnionid($unionid);

        if(!$resForOther) {
            $this->writeJson('999', null, '用户不存在');
            return false;
        }

        $pushData = [
            'action'  => $action, 
            'content' => $content, 
            'roomId'  => $playerId, 
            'uid'     => $unionid
            ];

        
        $redisStatus = $this->redisPush($pushData);

        if(!$redisStatus) {
            $this->writeJson('999', null, '推送失败');
            return false;
        }
        
        $this->writeJson('200', null, '推送成功');
    }
}