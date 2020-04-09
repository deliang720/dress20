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
use app\common\model\MiniproCategory AS MiniproCategoryModel;

/**
 * 小程序分类模板
 */
class MiniproCategory extends MiniproCategoryModel
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 新增小程序分类模板
     * @param $mini_id
     * @return false|int
     */
    public function insertDefault($mini_id)
    {
        return $this->save([
            'mini_id' => $mini_id,
            'category_style' => 30,
            'nav_title' => '全部分类',
            'share_title' => '全部分类',
            'lang'  => self::$lang,
            'add_time'  => getTime(),
            'update_time'   => getTime(),
        ]);
    }
}