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

namespace app\api\model;

use think\Model;
use think\Db;
use app\common\model\MiniproBase AS MiniproBaseModel;

/**
 * 微信小程序模型
 */
class Minipro extends MiniproBaseModel
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 文档列表
     * @param string $param 查询条件的数组
     * @param int $page 页码
     * @param int $pagesize 每页记录数
     */
    public function getArchivesList($param = array(), $page = 1, $pagesize = null, $field = '')
    {
        empty($field) && $field = 'aid,title,litpic,click,channel,users_price,seo_description,add_time';
        $pagesize = empty($pagesize) ? config('paginate.list_rows') : $pagesize;
        $args = [$param,$page,$pagesize,$field];
        $cacheKey = "api-model-Minipro-getArchivesList-".md5(json_encode($args));
        $result = cache($cacheKey);
        if (empty($result)) {
            $condition = array();

            // 应用搜索条件
            foreach (['keywords','typeid','flag','arcrank'] as $key) {
                if (isset($param[$key]) && ('' !== $param[$key] || null !== $param[$key])) {
                    if ($key == 'keywords') {
                        $condition['title'] = array('LIKE', "%{$param[$key]}%");
                    } else if ($key == 'typeid') {
                        if (!empty($param[$key])) {
                            if (!is_array($param[$key]) && stristr($param[$key], ',')) {
                                // 指定多个栏目ID
                                $typeid = func_preg_replace(array('，'), ',', $param[$key]);
                                $typeid = explode(',', $typeid);
                            } else if (!is_array($param[$key]) && !stristr($param[$key], ',')) {
                                /*当前栏目ID，以及所有子栏目ID*/
                                $channel_info = Db::name('Arctype')->field('id,current_channel')->where(['id'=>['eq', $param[$key]], 'is_del'=>0])->find();
                                $childrenRow = model('Arctype')->getHasChildren($param[$key]);
                                foreach ($childrenRow as $k2 => $v2) {
                                    if ($channel_info['current_channel'] != $v2['current_channel']) {
                                        unset($childrenRow[$k2]); // 排除不是同一模型的栏目
                                    }
                                }
                                $typeid = get_arr_column($childrenRow, 'id');
                                /*--end*/
                            }
                            $condition[$key] = array('IN', $typeid);
                        }
                    } else if ('channel' == $key) {
                        if (!empty($param[$key])) {
                            if (is_string($param[$key])) {
                                $channel = func_preg_replace(array('，'), ',', $param[$key]);
                                $channel = explode(',', $channel);
                            }
                            $condition[$key] = array('IN', $channel);
                        }
                    } else if ('flag' == $key) {
                        $tmp_key_arr = array();
                        $flag_arr = explode(",", $param[$key]);
                        foreach ($flag_arr as $k2 => $v2) {
                            if ($v2 == "c") {
                                array_push($tmp_key_arr, 'is_recom');
                            } elseif ($v2 == "h") {
                                array_push($tmp_key_arr, 'is_head');
                            } elseif ($v2 == "a") {
                                array_push($tmp_key_arr, 'is_special');
                            } elseif ($v2 == "j") {
                                array_push($tmp_key_arr, 'is_jump');
                            }
                        }
                        $tmp_key_str = implode('|', $tmp_key_arr);
                        $condition[$tmp_key_str] = array('eq', 1);
                    } else {
                        $condition[$key] = array('eq', $param[$key]);
                    }
                }
            }

            $paginate = array(
                'page'  => $page,
            );
            $pages = Db::name('archives')->field($field)
                ->where($condition)
                ->where([
                    'channel'   => ['NEQ', 6],
                    'lang'      => parent::$lang,
                    'arcrank'   => ['gt', -1],
                    'is_del'    => 0,
                ])
                ->order('sort_order asc, aid desc')
                ->paginate($pagesize, false, $paginate);

            $result = $pages->toArray();

            foreach ($result['data'] as $key => &$val) {
                $val['litpic'] = get_default_pic($val['litpic'], true); // 默认封面图
                if (isset($val['add_time'])) {
                    $val['add_time'] = date('Y-m-d', $val['add_time']);
                }
            }

            cache($cacheKey, $result, null, 'archives');
        }

        return $result;
    }

    /**
     * 文档详情
     * @param int $aid 文档ID
     */
    public function getArchivesView($aid = '')
    {
        $aid = intval($aid);
        $args = [$aid];
        $cacheKey = "api-model-Minipro-getArchivesView-".md5(json_encode($args));
        $result = cache($cacheKey);
        if (empty($result)) {
            $status = 0;
            $msg = 'Request Error!';
            $detail = array();
            if (0 < $aid) {
                $where = [
                    'status'    => 1,
                    'is_del'    => 0,
                ];
                $archivesModel = new \app\home\model\Archives;
                $detail = $archivesModel->getViewInfo($aid, true, $where);
                if (!empty($detail)) {
                    if (0 == $detail['arcrank']) {
                        $status = 1;
                    } else if (0 > $detail['arcrank']) {
                        $msg = '文档审核中，无权查看！';
                    } else if (0 < $detail['arcrank']) {
                        $msg = '当前是游客，无权查看！';
                    }
                } else {
                    $msg = '文档已删除！';
                }
                $detail['add_time'] = date('Y-m-d H:i:s', $detail['add_time']); // 格式化发布时间
                $detail['update_time'] = date('Y-m-d H:i:s', $detail['update_time']); // 格式化更新时间
                $detail['content'] = html_httpimgurl($detail['content']); // 转换内容图片为http路径

                /* 上一篇 */
                $preDetail = Db::name('archives')->field('a.aid, a.typeid, a.title')
                    ->alias('a')
                    ->where([
                        'a.typeid'  => $detail['typeid'],
                        'a.aid'     => ['lt', $aid],
                        'a.lang'    => parent::$lang,
                        'a.status'  => 1,
                        'a.is_del'  => 0,
                        'a.arcrank' => ['EGT', 0],
                    ])
                    ->order('a.aid desc')
                    ->find();

                /* 下一篇 */
                $nextDetail = Db::name('archives')->field('a.aid, a.typeid, a.title')
                    ->alias('a')
                    ->where([
                        'a.typeid'  => $detail['typeid'],
                        'a.aid'     => ['gt', $aid],
                        'a.lang'    => parent::$lang,
                        'a.status'  => 1,
                        'a.is_del'  => 0,
                        'a.arcrank' => ['EGT', 0],
                    ])
                    ->order('a.aid asc')
                    ->find();
            }

            $result = [
                'conf' => [
                    'status' => $status,
                    'msg'   => $msg,
                ],
                'detail' => !empty($detail) ? $detail : [],
                'preDetail' => !empty($preDetail) ? $preDetail : [],
                'nextDetail' => !empty($nextDetail) ? $nextDetail : [],
            ];

            cache($cacheKey, $result, null, 'archives');
        }

        return $result;
    }

    /**
     * 单页栏目详情
     * @param int $typeid 栏目ID
     */
    public function getSingleView($typeid = '')
    {
        $typeid = intval($typeid);
        $args = [$typeid];
        $cacheKey = "api-model-Minipro-getSingleView-".md5(json_encode($args));
        $result = cache($cacheKey);
        if (empty($result)) {
            $status = 0;
            $msg = 'Request Error!';
            $detail = array();
            if (0 < $typeid) {
                $archivesModel = new \app\home\model\Archives;
                $detail = $archivesModel->getSingleInfo($typeid, true);

                $status = 1;
                if (0 == $detail['status']) {
                    $msg = '该文档已屏蔽，无权查看';
                }
                /*--end*/
                $detail['add_time'] = date('Y-m-d H:i:s', $detail['add_time']); // 格式化发布时间
                $detail['update_time'] = date('Y-m-d H:i:s', $detail['update_time']); // 格式化更新时间
                $detail['content'] = html_httpimgurl($detail['content']); // 转换内容图片为http路径
            }

            $result = array(
                // 'conf' => array(
                //     'status' => $status,
                //     'msg'   => $msg,
                //     'shareTitle' => $detail['title'].'_'.tpCache('web.web_name'),
                // ),
                'detail' => $detail,
            );

            cache($cacheKey, $result, null, 'arctype');
        }

        return $result;
    }

    /**
     * 联系我们
     */
    public function getContact()
    {
        $cacheKey = "api-model-Minipro-getContact-".parent::$mini_id;
        $result = cache($cacheKey);
        if (empty($result)) {
            $detail = model('MiniproSetting')->getSettingValue('contact');

            if (empty($detail)) {
                $detail['logo'] = '/public/static/common/minipro/img/logo.png';
                $detail['banner'] = '/public/static/common/minipro/img/banner.jpg';
                $detail['content'] = '';
            }

            !empty($detail['logo']) && $detail['logo'] = get_default_pic($detail['logo'], true).'?t='.getTime(); // logo图片
            !empty($detail['banner']) && $detail['banner'] = get_default_pic($detail['banner'], true).'?t='.getTime(); // banner图片
            !empty($detail['content']) && $detail['content'] = html_httpimgurl($detail['content']); // 转换内容图片为http路径

            $result = array(
                'detail' => $detail,
            );
            cache($cacheKey, $result, null, 'minipro_setting');
        }

        return $result;
    }

    /**
     * 留言栏目表单
     * @param int $typeid 栏目ID
     */
    public function getGuestbookForm($typeid)
    {
        $typeid = intval($typeid);
        if (empty($typeid)) {
            $typeid = Db::name('arctype')->where([
                'current_channel'   => 8,
                'is_del' => 0,
                'status'    => 1,
                'lang'  => parent::$lang,
            ])->getField('id');
        }

        $attr_list = array();
        $typename = '';
        if (0 < $typeid) {
            $detail = Db::name('arctype')->field('id,id as typeid,typename')->where([
                'id'    => $typeid,
                'lang'  => parent::$lang,
            ])->find();
            $attr_list = Db::name('GuestbookAttribute')->field('attr_id,attr_name,attr_input_type,attr_values')
                ->where([
                    'typeid'    => $typeid,
                    'is_del'    => 0,
                ])
                ->order('sort_order asc, attr_id asc')
                ->select();
            foreach ($attr_list as $key => $val) {
                if (in_array($val['attr_input_type'], array(1,3,4))) {
                    $val['attr_values'] = explode(PHP_EOL, $val['attr_values']);
                    $attr_list[$key] = $val;
                }
            }
        }

        $result = array(
            'detail'    => $detail,
            'attr_list' => $attr_list,
        );

        return $result;
    }

    /**
     *  给指定留言添加表单值到 guestbook_attr
     * @param int $aid 留言id
     * @param int $typeid 留言栏目id
     */
    public function saveGuestbookAttr($post, $aid, $typeid)
    {
        $attrArr = [];

        /*多语言*/
        if (is_language()) {
            foreach ($post as $key => $val) {
                if (preg_match_all('/^attr_(\d+)$/i', $key, $matchs)) {
                    $attr_value           = intval($matchs[1][0]);
                    $attrArr[$attr_value] = [
                        'attr_id' => $attr_value,
                    ];
                }
            }
            $attrArr = model('LanguageAttr')->getBindValue($attrArr, 'guestbook_attribute'); // 多语言
        }
        /*--end*/

        foreach ($post as $k => $v) {
            if (!strstr($k, 'attr_'))
                continue;

            $attr_id = str_replace('attr_', '', $k);
            is_array($v) && $v = implode(PHP_EOL, $v);

            /*多语言*/
            if (!empty($attrArr)) {
                $attr_id = $attrArr[$attr_id]['attr_id'];
            }
            /*--end*/

            //$v = str_replace('_', '', $v); // 替换特殊字符
            //$v = str_replace('@', '', $v); // 替换特殊字符
            $v       = trim($v);
            $adddata = array(
                'aid'         => $aid,
                'attr_id'     => $attr_id,
                'attr_value'  => $v,
                'lang'        => parent::$lang,
                'add_time'    => getTime(),
                'update_time' => getTime(),
            );
            Db::name('GuestbookAttr')->add($adddata);
        }
    }

    /**
     * 底部导航菜单
     */
    public function getTabbar()
    {
        $data = [
            'selected'  => -1,
            'color'  => '#7A7E83',
            'selectedColor'  => '#00aeff',
        ];
        $cacheKey = "api-model-Minipro-getTabbar-".md5(json_encode($data)).parent::$mini_id;
        $result = cache($cacheKey);
        if (empty($result)) {
            $row = Db::name('minipro_tabbar')->field('text,path_type,path_value,icon,selected_icon')
                ->where([
                    'status'    => 1,
                    'mini_id'     => parent::$mini_id,
                    'lang'        => parent::$lang,
                ])->order('sort_order asc, id asc')->select();
            $list = [];

            $miniproLogic = new \app\common\logic\MiniproLogic;
            $path_type_list = $miniproLogic->path_type_list();
            foreach ($row as $key => $val) {
                $list[] = [
                    "pagePath" => $path_type_list[$val['path_type']]['path'].$val['path_value'],
                    "text" => $val['text'],
                    "iconPath" => get_default_pic($val['icon'], true),
                    "selectedIconPath" => get_default_pic($val['selected_icon'], true),
                ];
            }

            $data['list'] = $list;
            $result = $data;

            cache($cacheKey, $result, null, 'minipro_tabbar');
        }

        return $result;
    }

    /**
     * 获取小程序风格ID
     * @return [type] [description]
     */
    public function getMiniId()
    {
        $mini_id = parent::$mini_id;
        if (empty($mini_id)) {
            $mini_id = Db::name('minipro')->where([
                'is_default'    => 1,
                'is_del'        => 0,
                'lang'          => parent::$lang
            ])->getField('mini_id');
        }

        return intval($mini_id);
    }
}