<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace app\admin\model;

use think\Db;
use think\Model;
use app\common\model\MiniproHelp AS MiniproHelpModel;

/**
 * 小程序帮助中心
 */
class MiniproHelp extends MiniproHelpModel
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 新增小程序帮助中心
     * @param $mini_id
     * @return false|int
     */
    public function insertDefault($mini_id)
    {
        return $this->save([
            'title' => '关于小程序',
            'content' => '小程序本身无需下载，无需注册，不占用手机内存，可以跨平台使用，响应迅速，体验接近原生APP。',
            'sort_order' => 100,
            'mini_id' => $mini_id,
            'lang'  => self::$lang,
            'add_time'  => getTime(),
            'update_time'   => getTime(),
        ]);
    }
}