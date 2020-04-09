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

namespace app\common\logic;

use think\Model;
use think\Db;
use think\Request;

/**
 * 逻辑定义
 * Class CatsLogic
 * @package common\Logic
 */
class MiniproLogic extends Model
{
    private $request = null; // 当前Request对象实例
    private $current_lang = 'cn'; // 当前多语言标识

    /**
     * 析构函数
     */
    function  __construct() {
        null === $this->request && $this->request = Request::instance();
        $this->current_lang = get_current_lang();
    }
    
    /**
     * 接口转化
     */
    public function get_api_url($query_str)
    {
        $apiUrl = 'aHR0cHM6Ly9zZXJ2aWNlLmV5b3VjbXMuY29t';
        return base64_decode($apiUrl).$query_str;
    }

    /**
     * 获取远程最新的小程序参数配置
     */
    public function synRemoteSetting()
    {
        $data = model('MiniproSetting')->getSettingValue('setting');
        if (!empty($data)) {
            $vaules = [];
            if (!empty($data['md5code'])) {
                $vaules['md5code'] = $data['md5code'];
            } else {
                $vaules['appId'] = $data['appId'];
            }
            $query_str = http_build_query($vaules);
            $url = "/index.php?m=api&c=MiniproClient&a=minipro&".$query_str;
            $response = httpRequest($this->get_api_url($url));
            $params = array();
            $params = json_decode($response, true);
            if (!empty($params) && $params['errcode'] == 0) {
                $bool = model('MiniproSetting')->setSettingValue('setting', $params['errmsg']);
                if ($bool) {
                    $data = model('MiniproSetting')->getSettingValue('setting');
                } else {
                    $data = $params['errmsg'];
                }
            }
        
            if (empty($data['authorizerStatus'])) {
                session('show_qrcode_total', 0);
            }
        }

        return $data;
    }

    /**
     * 获取最新的小程序参数配置
     */
    public function getCreateSetting()
    {
        $data = model('MiniproSetting')->getSettingValue('setting');
        if (!empty($data)) {
            $vaules = [];
            $vaules['appId'] = $data['appId'];
            $query_str = http_build_query($vaules);
            $url = "/index.php?m=api&c=MiniproClient&a=minipro&".$query_str;
            $response = httpRequest($this->get_api_url($url));
            $params = array();
            $params = json_decode($response, true);
            if (!empty($params) && $params['errcode'] == 0) {
                $bool = model('MiniproSetting')->setSettingValue('setting', $params['errmsg']);
                if ($bool) {
                    $data = model('MiniproSetting')->getSettingValue('setting');
                } else {
                    $data = $params['errmsg'];
                }
            }
        }

        return $data;
    }

    /**
     * 页面类型列表
     * @return [type] [description]
     */
    public function path_type_list()
    {
        $data[1] = [
            'id'    => 1,
            'title' => '首页',
            'path' => '/pages/index/index',
            'showtext'  => false,
        ];
        $data[2] = [
            'id'    => 2,
            'title' => '分类页',
            'path' => '/pages/category/index',
            'showtext'  => false,
        ];
        $data[3] = [
            'id'    => 3,
            'title' => '列表页',
            'path' => '/pages/article/list?typeid=',
            'showtext'  => true,
        ];
        $data[4] = [
            'id'    => 4,
            'title' => '文档页',
            'path' => '/pages/article/view?aid=',
            'showtext'  => true,
        ];
        $data[5] = [
            'id'    => 5,
            'title' => '单页面',
            'path' => '/pages/article/single?typeid=',
            'showtext'  => true,
        ];
        $data[6] = [
            'id'    => 6,
            'title' => '在线留言',
            'path' => '/pages/guestbook/index?typeid=',
            'showtext'  => true,
        ];
        $data[7] = [
            'id'    => 7,
            'title' => '联系我们',
            'path' => '/pages/contact/index',
            'showtext'  => false,
        ];
        $data[8] = [
            'id'    => 8,
            'title' => '站内搜索',
            'path' => '/pages/search/index',
            'showtext'  => false,
        ];
        $data[9] = [
            'id'    => 9,
            'title' => '自定义页',
            'path' => '/pages/custom/index?page_id=',
            'showtext'  => true,
        ];

        return $data;
    }
}
