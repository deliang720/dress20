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
use app\common\model\MiniproTabbar AS MiniproTabbarModel;

/**
 * 小程序底部菜单
 */
class MiniproTabbar extends MiniproTabbarModel
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 新增小程序底部菜单
     * @param $mini_id
     * @return false|int
     */
    public function insertDefault($mini_id)
    {
        $data = [
            [
                'text'  => '首页',
                'path_type' => 1,
                'path_value'    => '',
                'icon'  => ROOT_DIR.'/public/static/common/minipro/img/tabbar/home.png',
                'selected_icon'  => ROOT_DIR.'/public/static/common/minipro/img/tabbar/home-active.png',
                'mini_id'   => $mini_id,
                'sort_order'    => 100,
                'lang'    => self::$lang,
                'add_time'  => getTime(),
                'update_time'  => getTime(),
            ],
            [
                'text'  => '分类',
                'path_type' => 2,
                'path_value'    => '',
                'icon'  => ROOT_DIR.'/public/static/common/minipro/img/tabbar/cate.png',
                'selected_icon'  => ROOT_DIR.'/public/static/common/minipro/img/tabbar/cate-active.png',
                'mini_id'   => $mini_id,
                'sort_order'    => 100,
                'lang'    => self::$lang,
                'add_time'  => getTime(),
                'update_time'  => getTime(),
            ],
            [
                'text'  => '联系我们',
                'path_type' => 7,
                'path_value'    => '',
                'icon'  => ROOT_DIR.'/public/static/common/minipro/img/tabbar/contact.png',
                'selected_icon'  => ROOT_DIR.'/public/static/common/minipro/img/tabbar/contact-active.png',
                'mini_id'   => $mini_id,
                'sort_order'    => 100,
                'lang'    => self::$lang,
                'add_time'  => getTime(),
                'update_time'  => getTime(),
            ],
        ];

        return $this->saveAll($data);
    }
}