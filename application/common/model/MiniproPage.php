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
use think\Request;
use app\common\model\MiniproBase;

/**
 * 小程序页面设计
 */
class MiniproPage extends MiniproBase
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 页面类型
     * @return array
     */
    public function get_page_type_list()
    {
        return [
            -1   => [
                'id'    => -1,
                'name'  => '自定义页',
            ],
            1   => [
                'id'    => 1,
                'name'  => '首页',
            ],
        ];
    }

    /**
     * 页面标题栏默认数据
     * @return array
     */
    public function getDefaultPage()
    {
        static $defaultPage = [];
        if (!empty($defaultPage)) return $defaultPage;
        return [
            'type' => 'page',
            'name' => '页面设置',
            'params' => [
                'type'  => -1, // 默认是自定义页
                'name' => '页面名称',
                'title' => '页面标题',
                'share_title' => '分享标题'
            ],
            'style' => [
                'titleTextColor' => 'black',
                'titleBackgroundColor' => '#ffffff',
            ]
        ];
    }

    /**
     * 页面diy元素默认数据
     * @return array
     */
    public function getDefaultItems()
    {
        return [
            'search' => [
                'name' => '搜索框',
                'type' => 'search',
                'params' => ['placeholder' => '请输入要搜索的关键字'],
                'style' => [
                    'textAlign' => 'left',
                    'searchStyle' => 'square'
                ]
            ],
            'banner' => [
                'name' => '图片轮播',
                'type' => 'banner',
                'style' => [
                    'btnColor' => '#ffffff',
                    'btnShape' => 'round',
                    'imgHeights' => '200'
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
                    ]
                ]
            ],
            'imageSingle' => [
                'name' => '单图组',
                'type' => 'imageSingle',
                'style' => [
                    'paddingTop' => 0,
                    'paddingLeft' => 0,
                    'background' => '#ffffff'
                ],
                'data' => [
                    [
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/banner/01.png',
                        'imgName' => 'image-1.jpg',
                        'url' => '',
                        'pathconf' => [
                            'index' => 'default', // 默认不选择小程序链接
                            'value' => '', // 参数值
                            'is_vars'   => 0, // 是否传参数值
                        ],
                    ],
                    [
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/banner/01.png',
                        'imgName' => 'banner-2.jpg',
                        'url' => '',
                        'pathconf' => [
                            'index' => 'default', // 默认不选择小程序链接
                            'value' => '', // 参数值
                            'is_vars'   => 0, // 是否传参数值
                        ],
                    ]
                ]
            ],
            'navBar' => [
                'name' => '导航组',
                'type' => 'navBar',
                'style' => ['background' => '#ffffff', 'rowsNum' => '4'],
                'data' => [
                    [
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/navbar/01.png',
                        'imgName' => 'icon-1.png',
                        'url' => '',
                        'text' => '按钮文字1',
                        'color' => '#666666',
                        'pathconf' => [
                            'index' => 'default', // 默认不选择小程序链接
                            'value' => '', // 参数值
                            'is_vars'   => 0, // 是否传参数值
                        ],
                    ],
                    [
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/navbar/01.png',
                        'imgName' => 'icon-2.jpg',
                        'url' => '',
                        'text' => '按钮文字2',
                        'color' => '#666666',
                        'pathconf' => [
                            'index' => 'default', // 默认不选择小程序链接
                            'value' => '', // 参数值
                            'is_vars'   => 0, // 是否传参数值
                        ],
                    ],
                    [
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/navbar/01.png',
                        'imgName' => 'icon-3.jpg',
                        'url' => '',
                        'text' => '按钮文字3',
                        'color' => '#666666',
                        'pathconf' => [
                            'index' => 'default', // 默认不选择小程序链接
                            'value' => '', // 参数值
                            'is_vars'   => 0, // 是否传参数值
                        ],
                    ],
                    [
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/navbar/01.png',
                        'imgName' => 'icon-4.jpg',
                        'url' => '',
                        'text' => '按钮文字4',
                        'color' => '#666666',
                        'pathconf' => [
                            'index' => 'default', // 默认不选择小程序链接
                            'value' => '', // 参数值
                            'is_vars'   => 0, // 是否传参数值
                        ],
                    ]
                ]
            ],
            'blank' => [
                'name' => '辅助空白',
                'type' => 'blank',
                'style' => [
                    'height' => '20',
                    'background' => '#ffffff'
                ]
            ],
            'guide' => [
                'name' => '辅助线',
                'type' => 'guide',
                'style' => [
                    'background' => '#ffffff',
                    'lineStyle' => 'solid',
                    'lineHeight' => '1',
                    'lineColor' => "#000000",
                    'paddingTop' => 10
                ]
            ],
            'video' => [
                'name' => '视频组',
                'type' => 'video',
                'params' => [
                    'url' => 'http://wxsnsdy.tc.qq.com/105/20210/snsdyvideodownload?filekey=30280201010421301f0201690402534804102ca905ce620b1241b726bc41dcff44e00204012882540400',
                    'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/video_poster.png',
                    'autoplay' => '0'
                ],
                'style' => [
                    'paddingTop' => '0',
                    'height' => '190'
                ]
            ],
            'special' => [
                'name' => '头条快报',
                'type' => 'special',
                'params' => [
                    'source' => 'auto', // choice; auto
                    'auto' => [
                        'typeid' => 0,
                        'showNum' => 6
                    ]
                ],
                'style' => [
                    'display' => '1',
                    'image' => ROOT_DIR . '/public/static/common/minipro/img/diy/special.png'
                ],
                // '自动获取' => 默认数据
                'defaultData' => [
                    [
                        'title' => '张小龙4小时演讲：你和高手之间，隔着“简单”二字'
                    ],
                    [
                        'title' => '张小龙4小时演讲：你和高手之间，隔着“简单”二字'
                    ]
                ],
                // '手动选择' => 默认数据
                'data' => []
            ],
            'notice' => [
                'name' => '公告组',
                'type' => 'notice',
                'params' => [
                    'content' => '这里是第一条自定义公告的标题',
                    'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/notice.png'
                ],
                'style' => [
                    'paddingTop' => '4',
                    'background' => '#ffffff',
                    'textColor' => '#000000'
                ]
            ],
            'richText' => [
                'name' => '富文本',
                'type' => 'richText',
                'params' => [
                    'content' => '<p>这里是文本的内容</p>'
                ],
                'style' => [
                    'paddingTop' => '0',
                    'paddingLeft' => '0',
                    'background' => '#ffffff'
                ]
            ],
            'window' => [
                'name' => '图片橱窗',
                'type' => 'window',
                'style' => [
                    'paddingTop' => '0',
                    'paddingLeft' => '0',
                    'background' => '#ffffff',
                    'layout' => '2'
                ],
                'data' => [
                    [
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/window/01.jpg',
                        'url' => '',
                        'pathconf' => [
                            'index' => 'default', // 默认不选择小程序链接
                            'value' => '', // 参数值
                            'is_vars'   => 0, // 是否传参数值
                        ],
                    ],
                    [
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/window/02.jpg',
                        'url' => '',
                        'pathconf' => [
                            'index' => 'default', // 默认不选择小程序链接
                            'value' => '', // 参数值
                            'is_vars'   => 0, // 是否传参数值
                        ],
                    ],
                    [
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/window/03.jpg',
                        'url' => '',
                        'pathconf' => [
                            'index' => 'default', // 默认不选择小程序链接
                            'value' => '', // 参数值
                            'is_vars'   => 0, // 是否传参数值
                        ],
                    ],
                    [
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/window/04.jpg',
                        'url' => '',
                        'pathconf' => [
                            'index' => 'default', // 默认不选择小程序链接
                            'value' => '', // 参数值
                            'is_vars'   => 0, // 是否传参数值
                        ],
                    ]
                ],
                'dataNum' => 4
            ],
            'article' => [
                'name' => '文档组',
                'type' => 'article',
                'params' => [
                    'source' => 'auto', // choice; auto
                    'auto' => [
                        'typeid' => 0,
                        'typename'  => '',
                        'orderby' => 'default', // default; click; rand
                        'showNum' => 6,
                        'navtitle' => '此处显示分类名称'
                    ],
                    'choice' => [
                        'typeid'    => 0,
                        'typename'  => '',
                        'show_more' => 'show', // show; hide
                        'navtitle' => '此处显示导航标题'
                    ],
                ],
                'style' => [
                    'show_type' => 10
                ],
                // '自动获取' => 默认数据
                'defaultData' => [
                    [
                        'title' => '此处显示文档标题',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/article/01.png',
                        'click' => '309'
                    ],
                    [
                        'title' => '此处显示文档标题',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/article/01.png',
                        'click' => '309'
                    ]
                ],
                // '手动选择' => 默认数据
                'data' => []
            ],
            'goods' => [
                'name' => '商品组',
                'type' => 'goods',
                'params' => [
                    'source' => 'auto', // choice; auto
                    'auto' => [
                        'typeid' => 0,
                        'typename'  => '',
                        'orderby' => 'default', // default; sales; price; rand; click
                        'showNum' => 6,
                        'navtitle' => '此处显示分类名称'
                    ],
                    'choice' => [
                        'typeid'    => 0,
                        'typename'  => '',
                        'show_more' => 'show', // show; hide
                        'navtitle' => '此处显示导航标题'
                    ],
                ],
                'style' => [
                    'background' => '#F6F6F6',
                    'display' => 'list', // list; slide
                    'column' => '2',
                    'show' => [
                        'title' => '1',
                        'usersPrice' => '1',
                        'oldPrice' => '1',
                        'seoDescription' => '0',
                        'stockCount' => '0',
                        'salesNum' => '0'
                    ]
                ],
                // '自动获取' => 默认数据
                'defaultData' => [
                    [
                        'title' => '此处显示商品名称',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'users_price' => '99.00',
                        'old_price' => '139.00',
                        'seo_description' => '此款商品美观大方 不容错过',
                        'stock_count' => '100',
                        'sales_num' => '10',
                    ],
                    [
                        'title' => '此处显示商品名称',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'users_price' => '99.00',
                        'old_price' => '139.00',
                        'seo_description' => '此款商品美观大方 不容错过',
                        'stock_count' => '100',
                        'sales_num' => '10',
                    ],
                    [
                        'title' => '此处显示商品名称',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'users_price' => '99.00',
                        'old_price' => '139.00',
                        'seo_description' => '此款商品美观大方 不容错过',
                        'stock_count' => '100',
                        'sales_num' => '10',
                    ],
                    [
                        'title' => '此处显示商品名称',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'users_price' => '99.00',
                        'old_price' => '139.00',
                        'seo_description' => '此款商品美观大方 不容错过',
                        'stock_count' => '100',
                        'sales_num' => '10',
                    ]
                ],
                // '手动选择' => 默认数据
                'data' => [
                    // [
                    //     'title' => '此处显示商品名称',
                    //     'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                    //     'users_price' => '99.00',
                    //     'old_price' => '139.00',
                    //     'seo_description' => '此款商品美观大方 不容错过',
                    //     'stock_count' => '100',
                    //     'sales_num' => '10',
                    //     'is_default' => true
                    // ],
                    // [
                    //     'title' => '此处显示商品名称',
                    //     'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                    //     'users_price' => '99.00',
                    //     'old_price' => '139.00',
                    //     'seo_description' => '此款商品美观大方 不容错过',
                    //     'stock_count' => '100',
                    //     'sales_num' => '10',
                    //     'is_default' => true
                    // ]
                ]
            ],
            'coupon' => [
                'name' => '优惠券组',
                'type' => 'coupon',
                'style' => [
                    'paddingTop' => '10',
                    'background' => '#ffffff'
                ],
                'params' => [
                    'limit' => '5'
                ],
                'data' => [
                    [
                        'color' => 'red',
                        'reduce_price' => '10',
                        'min_price' => '100.00'
                    ],
                    [
                        'color' => 'violet',
                        'reduce_price' => '10',
                        'min_price' => '100.00'
                    ]
                ]
            ],
            'sharingGoods' => [
                'name' => '拼团商品组',
                'type' => 'sharingGoods',
                'params' => [
                    'source' => 'auto', // choice; auto
                    'auto' => [
                        'typeid' => 0,
                        'orderby' => 'default', // default; sales; price
                        'showNum' => 6
                    ]
                ],
                'style' => [
                    'background' => '#F6F6F6',
                    'show' => [
                        'title' => '1',
                        'seoDescription' => '1',
                        'sharingPrice' => '1',
                        'usersPrice' => '1'
                    ]
                ],
                // '自动获取' => 默认数据
                'defaultData' => [
                    [
                        'title' => '此处是拼团商品',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'seo_description' => '此款商品美观大方 性价比较高 不容错过',
                        'sharing_price' => '99.00',
                        'users_price' => '139.00',
                    ],
                    [
                        'title' => '此处是拼团商品',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'seo_description' => '此款商品美观大方 性价比较高 不容错过',
                        'goods_price' => '99.00',
                        'users_price' => '139.00',
                    ],
                    [
                        'title' => '此处是拼团商品',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'seo_description' => '此款商品美观大方 性价比较高 不容错过',
                        'sharing_price' => '99.00',
                        'users_price' => '139.00',
                    ],
                    [
                        'title' => '此处是拼团商品',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'seo_description' => '此款商品美观大方 性价比较高 不容错过',
                        'sharing_price' => '99.00',
                        'users_price' => '139.00',
                    ]
                ],
                // '手动选择' => 默认数据
                'data' => [
                    [
                        'title' => '此处是拼团商品',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'seo_description' => '此款商品美观大方 性价比较高 不容错过',
                        'sharing_price' => '99.00',
                        'users_price' => '139.00',
                        'is_default' => true
                    ],
                    [
                        'title' => '此处是拼团商品',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'seo_description' => '此款商品美观大方 性价比较高 不容错过',
                        'sharing_price' => '99.00',
                        'users_price' => '139.00',
                        'is_default' => true
                    ]
                ]
            ],
            'bargainGoods' => [
                'name' => '砍价商品组',
                'type' => 'bargainGoods',
                'params' => [
                    'source' => 'auto', // choice; auto
                    'auto' => [
                        'typeid' => 0,
                        'orderby' => 'default', // default; sales; price
                        'showNum' => 6
                    ]
                ],
                'style' => [
                    'background' => '#F6F6F6',
                    'show' => [
                        'title' => '1',
                        'peoples' => '1',
                        'floorPrice' => '1',
                        'originalPrice' => '1'
                    ]
                ],
                'demo' => [
                    'helps_count' => 2,
                    'helps' => [
                        ['litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/demo1.jpg'],
                        ['litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/demo2.jpg'],
                    ]
                ],
                // '自动获取' => 默认数据
                'defaultData' => [
                    [
                        'title' => '此处是砍价商品',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'floor_price' => '0.01',
                        'original_price' => '139.00',
                    ],
                    [
                        'title' => '此处是砍价商品',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'floor_price' => '0.01',
                        'original_price' => '139.00',
                    ],
                ],
                // '手动选择' => 默认数据
                'data' => [
                    [
                        'title' => '此处是砍价商品',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'floor_price' => '0.01',
                        'original_price' => '139.00',
                    ],
                    [
                        'title' => '此处是砍价商品',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'floor_price' => '0.01',
                        'original_price' => '139.00',
                    ],
                ]
            ],
            'sharpGoods' => [
                'name' => '秒杀商品组',
                'type' => 'sharpGoods',
                'params' => [
                    'showNum' => 6
                ],
                'style' => [
                    'background' => '#ffffff',
                    'column' => '3',
                    'show' => [
                        'title' => '1',
                        'seckillPrice' => '1',
                        'originalPrice' => '1'
                    ]
                ],
                // '手动选择' => 默认数据
                'data' => [
                    [
                        'title' => '此处是秒杀商品',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'seckill_price' => '69.00',
                        'original_price' => '139.00',
                    ],
                    [
                        'title' => '此处是秒杀商品',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'seckill_price' => '69.00',
                        'original_price' => '139.00',
                    ],
                    [
                        'title' => '此处是秒杀商品',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/goods/01.png',
                        'seckill_price' => '69.00',
                        'original_price' => '139.00',
                    ],
                ]
            ],
            'shop' => [
                'name' => '线下门店',
                'type' => 'shop',
                'params' => [
                    'source' => 'auto', // choice; auto
                    'auto' => [
                        'showNum' => 6
                    ]
                ],
                'style' => [
                ],
                // '自动获取' => 默认数据
                'defaultData' => [
                    [
                        'title' => '此处显示门店名称',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/circular.png',
                        'contact' => '010-6666666',
                        'region' => [
                            'province' => 'xx省',
                            'city' => 'xx市',
                            'region' => 'xx区'
                        ],
                        'address' => 'xx街道',
                    ],
                    [
                        'title' => '此处显示门店名称',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/circular.png',
                        'contact' => '010-6666666',
                        'region' => [
                            'province' => 'xx省',
                            'city' => 'xx市',
                            'region' => 'xx区'
                        ],
                        'address' => 'xx街道',
                    ],
                ],
                // '手动选择' => 默认数据
                'data' => [
                    [
                        'title' => '此处显示门店名称',
                        'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/circular.png',
                        'contact' => '010-6666666',
                        'region' => [
                            'province' => 'xx省',
                            'city' => 'xx市',
                            'region' => 'xx区'
                        ],
                        'address' => 'xx街道',
                    ],
                ]
            ],
            'officialAccount' => [
                'name' => '关注公众号',
                'type' => 'officialAccount',
                'params' => [],
                'style' => []
            ],
            'service' => [
                'name' => '在线客服',
                'type' => 'service',
                'params' => [
                    'type' => 'phone',     // '客服类型' => chat在线聊天，phone拨打电话
                    'litpic' => ROOT_DIR . '/public/static/common/minipro/img/diy/service.png',
                    'phone_num' => ''
                ],
                'style' => [
                    'right' => '1',
                    'bottom' => '10',
                    'opacity' => '100'
                ]
            ],
        ];
    }

    /**
     * diy页面详情
     * @param int $page_id
     * @return static|null
     * @throws \think\exception\DbException
     */
    public static function detail($page_id)
    {
        $result = Db::name('minipro_page')->where([
                'page_id' => $page_id,
                'mini_id'   => parent::$mini_id,
                'lang'  => parent::$lang,
            ])
            ->cache(true,EYOUCMS_CACHE_TIME,"minipro_page")
            ->find();
        $result['page_data'] = json_decode($result['page_data'], true);

        return $result;
    }

    /**
     * diy页面详情
     * @return static|null
     * @throws \think\exception\DbException
     */
    public static function getHomePage()
    {
        $result = Db::name('minipro_page')->where([
                'is_home'   => 1,
                'page_type' => 1,
                'mini_id'   => parent::$mini_id,
                'lang'  => parent::$lang,
            ])
            ->cache(true,EYOUCMS_CACHE_TIME,"minipro_page")
            ->find();
        $result['page_data'] = json_decode($result['page_data'], true);

        return $result;
    }
}