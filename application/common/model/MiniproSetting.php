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
use app\common\model\MiniproBase;

/**
 * 小程序配置表
 */
class MiniproSetting extends MiniproBase
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 获取配置记录
     * @param string $name 类型
     * @author 小虎哥 by 2018-8-18
     */
    public static function getSettingInfo($name)
    {
        $map = array(
            'name'  => $name,
            'mini_id'   => parent::$mini_id,
            'lang'  => parent::$lang,
        );
        $result = Db::name('minipro_setting')->where($map)->find();
        if (!empty($result)) {
            $result['value'] = (array)json_decode($result['value'], true);
        }

        return $result;
    }

    /**
     * 设置配置值
     * @param string $name 名称
     * @author 小虎哥 by 2018-8-18
     */
    public function setSettingValue($name, $value)
    {
        $result = self::getSettingInfo($name);
        if (empty($result)) {
            $data = array(
                'name' => $name,
                'value' => json_encode($value),
                'mini_id'   => parent::$mini_id,
                'lang'  => parent::$lang,
                'add_time' => getTime(),
            );
            $r = Db::name('minipro_setting')->insert($data);
        } else {
            $data = array(
                'value' => json_encode($value),
                'update_time' => getTime(),
            );
            $r = $this->where([
                'name' => $name,
                'mini_id'   => parent::$mini_id,
                'lang'  => parent::$lang,
            ])->update($data);
        }
        if (false !== $r) {
            return true;
        }

        return false;
    }

    /**
     * 获取配置值
     * @param string $name 类型
     * @author 小虎哥 by 2018-8-18
     */
    public static function getSettingValue($name)
    {
        $result = self::getSettingInfo($name);

        return empty($result['value']) ? [] : $result['value'];
    }
}