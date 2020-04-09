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

namespace app\common\model;

use think\Db;
use think\Model;
use think\Request;

/**
 * 小程序基类模型
 */
class MiniproBase extends Model
{
    /**
     * 当前Request对象实例
     * @var null
     */
    public static $request = null; // 当前Request对象实例

    /**
     * 小程序风格ID
     * @var null
     */
    public static $mini_id = null;

    /**
     * 语言标识
     */
    public static $lang = 'cn';

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        self::$lang = get_current_lang();
        self::$mini_id = input('param.mini_id/d');
        if (null === self::$mini_id || empty(self::$mini_id)) {
            self::$mini_id = Db::name('minipro')->where([
                'is_default'    => 1,
                'is_del'        => 0,
                'lang'          => self::$lang
            ])->getField('mini_id');
        }

        null === self::$request && self::$request = Request::instance();
    }
}