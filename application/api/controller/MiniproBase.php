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

namespace app\api\controller;

use think\Db;

class MiniproBase extends Base
{
    /**
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();
        session('user'); // 哪里用到 session_id() , 哪个文件就加上这行代码
    }

    /**
     * 获取当前用户信息
     * @param bool $is_force
     * @return UserModel|bool|null
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    protected function getUser($is_force = true)
    {
        return false;
/*        if (!$token = $this->request->param('token')) {
            $is_force && $this->throwError('缺少必要的参数：token', -1);
            return false;
        }
        if (!$user = UserModel::getUser($token)) {
            $is_force && $this->throwError('没有找到用户信息', -1);
            return false;
        }
        return $user;*/
    }

    /**
     * 返回操作成功
     * @param array $data
     * @param string|array $msg
     * @return array
     */
    protected function renderSuccess($data = [], $msg = 'success', $url = null)
    {
        // 底部菜单
        $data['tabbar'] = model('Minipro')->getTabbar();

        // 顶部导航
        $diy_page = model('MiniproPage')->getPageData($this->getUser(false));
        $data['navbar'] = $diy_page['page'];

        // 全局配置
        $globalConf = [
            'mini_id'   => model('Minipro')->getMiniId(),
        ];
        $data['globalConf'] = $globalConf;

        return $this->success($msg, $url, $data);
    }
}