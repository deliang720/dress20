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
use app\common\model\MiniproPage AS MiniproPageModel;

/**
 * 微信小程序diy页面模型 
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
     * DIY页面详情
     * @param $user
     * @param null $page_id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getPageData($user, $page_id = null)
    {
        // 全部栏目
        $arctypeAll = model('MiniproCategory')->getCacheAllName();

        $cacheKey = "api-model-MiniproPage-getPageData-{$user}-{$page_id}-".json_encode($arctypeAll);
        $returnData = cache($cacheKey);
        if (empty($returnData)) {
            // 页面详情
            $detail = intval($page_id) > 0 ? parent::detail($page_id) : parent::getHomePage();
            // 页面diy元素
            $items = $detail['page_data']['items'];
            // 页面顶部导航
            isset($detail['page_data']['page']) && $items['page'] = $detail['page_data']['page'];
            // 获取动态数据
            $model = new self;
            foreach ($items as $key => $item) {
                if ($item['type'] === 'window') { // 图片橱窗组件
                    $items[$key]['data'] = array_values($item['data']);
                    foreach ($items[$key]['data'] as $_k => $_v) {
                        isset($_v['litpic']) && $items[$key]['data'][$_k]['litpic'] = get_default_pic($_v['litpic'], true);
                    }
                } else if ($item['type'] === 'goods') { // 商品组件
                    $items[$key]['data'] = $model->getProductList($item);
                    if (!empty($items[$key]['defaultData']) && is_array($items[$key]['defaultData'])) {
                        foreach ($items[$key]['defaultData'] as $_k => $_v) {
                            isset($_v['litpic']) && $items[$key]['defaultData'][$_k]['litpic'] = get_default_pic($_v['litpic'], true);
                        }
                    }
                    if (!empty($items[$key]['data']) && is_array($items[$key]['data'])) {
                        foreach ($items[$key]['data'] as $_k => $_v) {
                            isset($_v['litpic']) && $items[$key]['data'][$_k]['litpic'] = get_default_pic($_v['litpic'], true);
                        }
                    }
                    
                    if ($items[$key]['params']['source'] == 'auto') {
                        $typeid = intval($items[$key]['params']['auto']['typeid']);
                        $items[$key]['params']['auto']['typename'] = !empty($typeid) ? $arctypeAll[$typeid]['typename'] : '商品中心';
                    } else if ($items[$key]['params']['source'] == 'choice') {
                        $items[$key]['params']['choice']['typename'] = !empty($items[$key]['params']['choice']['typename']) ? $items[$key]['params']['choice']['typename'] : '';
                    }
                    
                // } else if ($item['type'] === 'sharingGoods') { // 拼团商品组
                    // $items[$key]['data'] = $this->getSharingGoodsList($user, $item);
                // } else if ($item['type'] === 'bargainGoods') { // 砍价商品组
                    // $items[$key]['data'] = $this->getBargainGoodsList($item);
                // } else if ($item['type'] === 'sharpGoods') { // 秒杀商品组
                    // $items[$key]['data'] = $this->getSharpGoodsList($item);
                // } else if ($item['type'] === 'coupon') { // 优惠券组件
                    // $items[$key]['data'] = $this->getCouponList($user, $item);
                } else if ($item['type'] === 'article') { // 文档组件
                    $items[$key]['data'] = $model->getArchivesList($item);
                    
                    if ($items[$key]['params']['source'] == 'auto') {
                        $typeid = intval($items[$key]['params']['auto']['typeid']);
                        $items[$key]['params']['auto']['typename'] = !empty($typeid) ? $arctypeAll[$typeid]['typename'] : '列表中心';
                    } else if ($items[$key]['params']['source'] == 'choice') {
                        $items[$key]['params']['choice']['typename'] = !empty($items[$key]['params']['choice']['typename']) ? $items[$key]['params']['choice']['typename'] : '';
                    }
                } else if ($item['type'] === 'special') { // 头条快报组件
                    $items[$key]['data'] = $model->getSpecialList($item);
                    $items[$key]['style']['image'] = get_default_pic($items[$key]['style']['image'], true);
                } else if ($item['type'] === 'shop') { // 线下门店组件
                    if (!empty($items[$key]['defaultData']) && is_array($items[$key]['defaultData'])) {
                        foreach ($items[$key]['defaultData'] as $_k => $_v) {
                            isset($_v['litpic']) && $items[$key]['defaultData'][$_k]['litpic'] = get_default_pic($_v['litpic'], true);
                        }
                    }
                    if (!empty($items[$key]['data']) && is_array($items[$key]['data'])) {
                        foreach ($items[$key]['data'] as $_k => $_v) {
                            isset($_v['litpic']) && $items[$key]['data'][$_k]['litpic'] = get_default_pic($_v['litpic'], true);
                        }
                    }
                } else if ($item['type'] === 'richText') {
                    isset($items[$key]['params']['content']) && $items[$key]['params']['content'] = html_httpimgurl($items[$key]['params']['content']); // 转换内容图片为http路径
                } else { // 其他组件
                    if (!empty($items[$key]['data']) && is_array($items[$key]['data'])) {
                        foreach ($items[$key]['data'] as $_k => $_v) {
                            isset($_v['litpic']) && $items[$key]['data'][$_k]['litpic'] = get_default_pic($_v['litpic'], true);
                        }
                    }
                    isset($items[$key]['params']['litpic']) && $items[$key]['params']['litpic'] = get_default_pic($items[$key]['params']['litpic'], true);
                }
            }
            $returnData = ['page' => $items['page'], 'items' => $items];

            cache($cacheKey, $returnData, null, 'minipro_page');
        }

        return $returnData;
    }

    /**
     * 商品组件：获取商品列表
     * @param $item
     * @return array
     * @throws \think\exception\DbException
     */
    private function getProductList($item)
    {
        $map = [];
        // 获取商品数据
        if ($item['params']['source'] == 'choice') { // 手动选择
            if (empty($item['data'])) {
                return [];
            } else {
                $aids = [];
                foreach ($item['data'] as $key => $val) {
                    array_push($aids, $val['aid']);
                }
                $map['aid'] = ['IN', $aids];
                $num = count($aids);
            }
        } else {
            $num = intval($item['params']['auto']['showNum']);
            $typeid = intval($item['params']['auto']['typeid']);
            if (!empty($typeid)) {
                $typeids = model('Arctype')->getHasChildren($typeid);
                $map['typeid'] = ['IN', get_arr_column($typeids, 'id')];
            }
        }
        $map['channel'] = 2;

        // 排序
        $orderby = $item['params']['auto']['orderby'];
        switch ($orderby) {
            case 'price':
                $orderby = 'users_price asc, sort_order asc, aid desc';
                break;

            case 'sales_num':
                $orderby = 'sales_num desc, sort_order asc, aid desc';
                break;

            case 'click':
                $orderby = 'click desc, sort_order asc, aid desc';
                break;

            case 'rand':
                $orderby = 'rand()';
                break;
            
            default:
                $orderby = 'sort_order asc, aid desc';
                break;
        }

        $result = $this->getList('aid,title,litpic,seo_description,users_price,old_price,stock_count', $map, $num, $orderby);

        if ($item['params']['source'] == 'choice') { // 手动选择
            $list = [];
            $result_1 = convert_arr_key($result, 'aid');
            foreach ($aids as $key => $val) {
                array_push($list, $result_1[$val]);
            }
            $result = $list;
        }

        return empty($result) ? [] : $result;
    }

    /**
     * 文档组件：获取文档列表
     * @param $item
     * @return array
     * @throws \think\exception\DbException
     */
    private function getArchivesList($item)
    {
        $map = [];
        // 获取文档数据
        if ($item['params']['source'] == 'choice') { // 手动选择
            if (empty($item['data'])) {
                return [];
            } else {
                $aids = [];
                foreach ($item['data'] as $key => $val) {
                    array_push($aids, $val['aid']);
                }
                $map['aid'] = ['IN', $aids];
                $num = count($aids);
            }
        } else {
            $num = intval($item['params']['auto']['showNum']);
            $typeid = intval($item['params']['auto']['typeid']);
            if (!empty($typeid)) {
                $typeids = model('Arctype')->getHasChildren($typeid);
                $map['typeid'] = ['IN', get_arr_column($typeids, 'id')];
            }
        }

        // 排序
        $orderby = $item['params']['auto']['orderby'];
        switch ($orderby) {
            case 'click':
                $orderby = 'click desc, sort_order asc, aid desc';
                break;

            case 'rand':
                $orderby = 'rand()';
                break;
            
            default:
                $orderby = 'sort_order asc, aid desc';
                break;
        }

        $result = $this->getList('aid,title,litpic,click,add_time', $map, $num);

        if ($item['params']['source'] == 'choice') { // 手动选择
            $list = [];
            $result_1 = convert_arr_key($result, 'aid');
            foreach ($aids as $key => $val) {
                array_push($list, $result_1[$val]);
            }
            $result = $list;
        }

        return empty($result) ? [] : $result;
    }

    /**
     * 头条快报：获取头条列表
     * @param $item
     * @return array
     * @throws \think\exception\DbException
     */
    private function getSpecialList($item)
    {
        // 获取头条数据
        $map = [];
        $num = intval($item['params']['auto']['showNum']);
        $typeid = intval($item['params']['auto']['typeid']);
        if (!empty($typeid)) {
            $typeids = model('Arctype')->getHasChildren($typeid);
            $map['typeid'] = ['IN', get_arr_column($typeids, 'id')];
        }
        $map['is_head'] = 1;
        $result = $this->getList('aid,title', $map, $num);
        return empty($result) ? [] : $result;
    }

    /**
     * 获取文档列表
     * @return [type] [description]
     */
    private function getList($field = '*', $where = [], $limit = 20, $orderby = 'sort_order asc, aid desc')
    {
        $map = [
            'arcrank'   => ['gt', -1],
            'status'    => 1,
            'is_del'    => 0,
            'lang'      => get_current_lang(),
        ];
        is_array($where) && $map = array_merge($where, $map);

        $result = Db::name('Archives')->field($field)
            ->where($map)
            ->cache(true,EYOUCMS_CACHE_TIME,"archives")
            ->order($orderby)
            ->limit($limit)
            ->select();
        foreach ($result as $key => &$val) {
            isset($val['litpic']) && $val['litpic']  = handle_subdir_pic(get_default_pic($val['litpic']), 'img', true);
        }

        return $result;
    }
}