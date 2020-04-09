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
use app\common\model\Minipro AS MiniproModel;

/**
 * 小程序风格
 */
class Minipro extends MiniproModel
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 初始化数据
     */
    public function setInitData()
    {
        $system_minipro_initsyn = tpCache('system.system_minipro_initsyn');
        if (empty($system_minipro_initsyn)) {
            $mini_id = Db::name('minipro')->insertGetId([
                'name'  => '默认风格',
                'app_id'    => 'xxxxxx',
                'app_secret'    => 'xxxxxxxxxxxxxx',
                'cert_pem'  => '',
                'key_pem'   => '',
                'is_default'    => 1,
                'lang'  => self::$lang,
                'add_time'  => getTime(),
                'update_time'  => getTime(),
            ]);
            if (!empty($mini_id)) {
                try {
                    $this->after_theme_add($mini_id);
                } catch (\Exception $e) {
                    Db::name('minipro')->where(['mini_id'=>$mini_id])->delete();
                }
            }

            tpCache('system', ['system_minipro_initsyn'=>1]);
        }
    }

    /**
     * 新增风格的后置操作
     * @return [type] [description]
     */
    public function after_theme_add($mini_id)
    {
        // 新增小程序diy配置
        model('MiniproPage')->insertDefault($mini_id);
        // 新增小程序分类页模板
        model('MiniproCategory')->insertDefault($mini_id);
        // 新增小程序默认帮助
        model('MiniproHelp')->insertDefault($mini_id);
        // 新增小程序默认底部菜单
        model('MiniproTabbar')->insertDefault($mini_id);
    }
}