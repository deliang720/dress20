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
use think\Request;
use app\common\model\MiniproPage AS MiniproPageModel;

/**
 * 小程序页面设计
 */
class MiniproPage extends MiniproPageModel
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 新增小程序首页diy默认设置
     * @param $mini_id
     * @return false|int
     */
    public function insertDefault($mini_id)
    {
        return $this->save([
            'page_type' => 1,
            'page_name' => '小程序首页',
            'page_data' => json_encode([
                'page' => [
                    'type' => 'page',
                    'name' => '页面设置',
                    'params' => [
                        'type'  => 1,
                        'name' => '小程序首页',
                        'title' => '页面标题',
                        'share_title' => '分享标题'
                    ],
                    'style' => [
                        'titleTextColor' => 'black',
                        'titleBackgroundColor' => '#ffffff',
                    ]
                ],
                'items' => [
                    [
                        'type' => 'search',
                        'name' => '搜索框',
                        'params' => ['placeholder' => '站内搜索'],
                        'style' => [
                            'textAlign' => 'center',
                            'searchStyle' => 'radius',
                        ],
                    ],
                    [
                        'type' => 'banner',
                        'name' => '图片轮播',
                        'style' => [
                            'btnColor' => '#ffffff',
                            'btnShape' => 'round',
                        ],
                        'params' => [
                            'interval' => '2800'
                        ],
                        'data' => [
                            [
                                'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/banner/01.png',
                                'url' => '',
                                'pathconf' => [
                                    'index' => 'default', // 默认不选择小程序链接
                                    'value' => '', // 参数值
                                    'is_vars'   => 0, // 是否传参数值
                                ],
                            ],
                            [
                                'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/banner/01.png',
                                'url' => '',
                                'pathconf' => [
                                    'index' => 'default', // 默认不选择小程序链接
                                    'value' => '', // 参数值
                                    'is_vars'   => 0, // 是否传参数值
                                ],
                            ],
                        ],
                    ]
                ],
            ]),
            'mini_id' => $mini_id,
            'is_home'   => 1,
            'lang'  => self::$lang,
            'add_time'  => getTime(),
            'update_time'   => getTime(),
        ]);
    }

    /**
     * 获取select表单里的option组成HTML
     * @return [type] [description]
     */
    public function get_list_selecthtml()
    {
        $select_html = '';
        $row = Db::name('minipro_page')->field('page_id,page_name')
            ->where([
                'page_type' => -1,
                'is_del'    => 0,
                'mini_id'   => self::$mini_id,
                'lang'      => self::$lang,
            ])->order('page_id asc')->select();
        if (!empty($row)) {
            foreach ($row as $key => $val) {
                $selected = '';
                if (intval($val['page_id']) == intval($path_value)) {
                    $selected = ' selected="ture" ';
                }
                $select_html .= "<option value='{$val['page_id']}'>{$val['page_name']}</option>";
            }
        }

        return $select_html;
    }

    /**
     * 获取小程序页面链接
     * @return [type] [description]
     */
    public function get_page_links()
    {
        return  [
            'default' => [
                'name'      => '--请选择--',
                'path'      => '',
                'is_vars'   => 0,
                'placeholder'=> '',
            ],
            'index_index' => [
                'name'      => '首页',
                'path'      => 'pages/index/index',
                'is_vars'   => 0,
                'placeholder'=> '',
            ],
            'category_index' => [
                'name'      => '分类页',
                'path'      => 'pages/category/index',
                'is_vars'   => 0,
                'placeholder'=> '',
            ],
            'article_list' => [
                'name'      => '列表页',
                'path'      => 'pages/article/list?typeid=',
                'is_vars'   => 1,
                'placeholder'=> '此处输入栏目ID',
            ],
            'article_view' => [
                'name'      => '文档页',
                'path'      => 'pages/article/view?aid=',
                'is_vars'   => 1,
                'placeholder'=> '此处输入文档ID',
            ],
            'article_single' => [
                'name'      => '单页面',
                'path'      => 'pages/article/single?typeid=',
                'is_vars'   => 1,
                'placeholder'=> '此处输入单页栏目ID',
            ],
            'guestbook_index' => [
                'name'      => '在线留言',
                'path'      => 'pages/guestbook/index?typeid=',
                'is_vars'   => 1,
                'placeholder'=> '此处输入留言栏目ID',
            ],
            'contact_view' => [
                'name'      => '联系我们',
                'path'      => 'pages/contact/index',
                'is_vars'   => 0,
                'placeholder'=> '',
            ],
            'search_index' => [
                'name'      => '站内搜索',
                'path'      => 'pages/search/index',
                'is_vars'   => 0,
                'placeholder'=> '',
            ],
            'custom_index' => [
                'name'      => '自定义页',
                'path'      => 'pages/custom/index?page_id=',
                'is_vars'   => 1,
                'placeholder'=> '此处输入小程序页面ID',
            ],
        ];
    }
}