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

class Minipro extends MiniproBase
{
    /**
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 自定义页面
     * @param  [type] $page_id [页面ID]
     * @return [type]          [返回数组]
     */
    public function diy_page($page_id = null)
    {
        // 页面元素
        $data = model('MiniproPage')->getPageData($this->getUser(false), $page_id);

        $this->renderSuccess($data);
    }

    /**
     * 分类页面
     * @return [type]          [description]
     */
    public function category()
    {
        $data = [];
        // 分类模板
        $data['templet'] = model('MiniproCategory')->detail();
        // 分类列表
        $list = model('MiniproCategory')->getCacheTree();
        $data['list'] = array_values($list);

        $this->renderSuccess($data);
    }

    /**
     * 文档列表的栏目列表
     * @param  string $typeid 栏目ID
     * @return [type]         返回值
     */
    // public function arctype($typeid = '')
    // {
    //     // 栏目列表
    //     $arctypeList = model('MiniproCategory')->getCacheTree($typeid);

    //     $this->renderSuccess(compact('arctypeList'));
    // }

    /**
     * 文档列表
     * @param  string  $typeid 栏目ID
     * @param  integer $page   页码
     * @param  string $keywords   关键词
     * @return array          返回值
     */
    public function archivesList($typeid = '', $page = 1, $keywords = '')
    {
        $typeid = $typeid2 = intval($typeid);
        $arctype = [];

        // 当前栏目信息
        $arctype_current = model('MiniproCategory')->getCacheInfo($typeid);
        $arctype['current_data'] = $arctype_current;

        // 列表顶部分类风格
        $showAllTxt = 'off'; // 是否显示“全部”字样
        $topcategory_style = 10;
        if (10 == $topcategory_style) {
            $showAllTxt = 'notSub'; // 无子栏目时显示“全部”字样
            $parent_list = model('Arctype')->getAllPid($typeid2);
            $parent_list = current($parent_list); // 第一级栏目
            $typeid2 = $parent_list['id'];
        } else if (20 == $topcategory_style) {
            $showAllTxt = 'all'; // 所有子层级栏目都显示“全部”字样
        }

        // 栏目列表
        $arctype_data = model('MiniproCategory')->getCacheTree($typeid2, $showAllTxt);
        if (empty($arctype_data)) {
            $arctype_data = model('MiniproCategory')->getCacheTree($arctype_current['parent_id'], $showAllTxt);
        }
        $arctype['data'] = $arctype_data;

        // 文档列表
        $params = [];
        !empty($typeid) && $params['typeid'] = $typeid;
        !empty($keywords) && $params['keywords'] = $keywords;
        $list = model('Minipro')->getArchivesList($params, intval($page));

        $current_channel = !empty($arctype_current['current_channel']) ? intval($arctype_current['current_channel']) : 0;
        switch ($current_channel) {
            case '2':
                $show_type = 20;
                break;

            case '3':
                $show_type = 30;
                break;
            
            default:
                $show_type = 10;
                break;
        }

        // 列表风格
        $style = [
            'show_type' => $show_type,
            'topcategory_style' => $topcategory_style,
        ];

        $this->renderSuccess(compact('list', 'style', 'arctype'));
    }

    /**
     * 文档详情页
     * @param  string  $aid 文档ID
     * @return array          返回值
     */
    public function archivesView($aid = '', $typeid = '')
    {
        if (empty($aid) && !empty($typeid)) {
            // 栏目详情
            $data = model('Minipro')->getSingleView($typeid);
        } else {
            // 文档详情
            $data = model('Minipro')->getArchivesView($aid);
        }

        $this->renderSuccess($data);
    }

    /**
     * 联系我们
     * @param  string  $aid 文档ID
     * @return array          返回值
     */
    public function contact()
    {
        $data = model('Minipro')->getContact();

        $this->renderSuccess($data);
    }

    /**
     * 留言栏目
     */
    public function guestbook($typeid = '')
    {
        if (IS_POST) {
            $post = input('post.');
            $typeid = !empty($post['typeid']) ? intval($post['typeid']) : 0;
            if (0 < $typeid) {
                $ip = clientIP();
                $map = array(
                    'ip'    => $ip,
                    'typeid'    => $typeid,
                    'add_time'  => array('gt', getTime() - 5),
                );
                $count = Db::name('guestbook')->where($map)->count('aid');
                if (!empty($count)) {
                    $this->error('同一个IP在60秒之内不能重复提交！');
                }

                $channeltype_list = config('global.channeltype_list');
                $newData = array(
                    'typeid'    => $typeid,
                    'channel'   => $channeltype_list['guestbook'],
                    'ip'    => $ip,
                    'lang'  => $this->home_lang,
                    'add_time'  => getTime(),
                    'update_time' => getTime(),
                );
                $aid = Db::name('guestbook')->insertGetId($newData);
                if ($aid > 0) {
                    model('Minipro')->saveGuestbookAttr($post, $aid, $typeid);
                }

                $this->renderSuccess([], '提交成功');
            } else {
                $this->error('链接没有指定留言栏目ID值！');
            }
        } else {
            $data = model('Minipro')->getGuestbookForm($typeid);
        }

        $this->renderSuccess($data);
    }

    /**
     * 底部导航菜单
     * @return array          返回值
     */
    public function tabbar()
    {
        $data = model('Minipro')->getTabbar();

        $this->renderSuccess($data);
    }
}