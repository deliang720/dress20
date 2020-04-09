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

namespace app\admin\controller;

use think\Db;
use think\Page;
use think\Cache;
use app\common\logic\MiniproLogic;

class Minipro extends Base
{
    private $mini_id = 0;
    public $page_type_list = [];
    public $md5code = '';
    /**
     * 模板nid，每套模板唯一
     */
    private $nid = 'uisetMinipro';
    /**
     * 实例化业务逻辑对象
     */
    private $miniproLogic;

    public function _initialize() {
        parent::_initialize();
        $this->language_access(); // 多语言功能操作权限
        $this->miniproLogic = new MiniproLogic;
        $this->mini_id = input('param.mini_id/d');
        if (empty($this->mini_id)) {
            $this->mini_id = model('Minipro')->getDefaultMiniId();
        }
        if (!preg_match('/^theme_/i', ACTION_NAME) && !in_array(ACTION_NAME, ['page_links','index'])) {
            if (empty($this->mini_id)) {
                $this->error('访问页面出错[URL缺少mini_id参数]！');
            }
        }
        $this->page_type_list = model('MiniproPage')->get_page_type_list();

        $install_time = DEFAULT_INSTALL_DATE;
        $serial_number = DEFAULT_SERIALNUMBER;
        $constsant_path = APP_PATH.'admin/conf/constant.php';
        if (file_exists($constsant_path)) {
            require_once($constsant_path);
            defined('INSTALL_DATE') && $install_time = INSTALL_DATE;
            defined('SERIALNUMBER') && $serial_number = SERIALNUMBER;
        }
        $this->md5code = md5($serial_number.$install_time.$this->nid.$this->request->host());

        // 初始化数据
        model('Minipro')->setInitData();
    }

    public function index()
    {
        $this->redirect(url('Minipro/theme_index'));
        exit;
    }

    public function theme_index()
    {
        $list = array();
        $keywords = input('keywords/s');

        $condition = array();
        if (!empty($keywords)) {
            $condition['name'] = array('LIKE', "%{$keywords}%");
        }

        // 多语言
        $condition['lang'] = array('eq', $this->admin_lang);
        $condition['is_del'] = 0;

        $miniproM =  Db::name('minipro');
        $count = $miniproM->where($condition)->count('mini_id');// 查询满足要求的总记录数
        $Page = $pager = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $miniproM->where($condition)->order('is_default desc, mini_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$pager);// 赋值分页对象
        return $this->fetch();
    }

    /**
     * 添加模板风格
     */
    public function theme_add()
    {
        $is_default = 0;
        $count = Db::name('minipro')->where(['is_default'=>1, 'lang'=>$this->admin_lang, 'is_del'=>0])->count();
        if (empty($count)) {
            $is_default = 1;
        }

        // --存储数据
        $data = [
            'cert_pem'  => '',
            'key_pem'  => '',
            'is_default'    => $is_default,
            'lang'  => $this->admin_lang,
            'add_time'    => getTime(),
            'update_time'    => getTime(),
        ];
        $mini_id = Db::name('minipro')->insertGetId($data);
        if (false !== $mini_id) {
            try {
                model('Minipro')->after_theme_add($mini_id);
            } catch (\Exception $e) {
                Db::name('minipro')->where(['mini_id'=>$mini_id])->delete();
                $this->error("操作失败[".$e->getMessage()."]", url('Minipro/theme_index'));
            }
            Db::name('minipro')->where(['mini_id'=>$mini_id])->update([
                'name'  => '未命名-'.$mini_id,
                'update_time'   => getTime(),
            ]);
            Cache::clear('minipro');
            adminLog('新增小程序风格：'.$data['name']);
            $this->success("操作成功!", url('Minipro/theme_index'));
        }else{
            $this->error("操作失败!", url('Minipro/theme_index'));
        }

        // if (IS_POST) {
        //     $post = input('post.');

        //     $is_default = 0;
        //     $count = Db::name('minipro')->where(['is_default'=>1, 'lang'=>$this->admin_lang, 'is_del'=>0])->count();
        //     if (empty($count)) {
        //         $is_default = 1;
        //     }

        //     // --存储数据
        //     $nowData = [
        //         'cert_pem'  => !empty($post['cert_pem']) ? $post['cert_pem'] : '',
        //         'key_pem'  => !empty($post['key_pem']) ? $post['key_pem'] : '',
        //         'is_default'    => $is_default,
        //         'lang'  => $this->admin_lang,
        //         'add_time'    => getTime(),
        //         'update_time'    => getTime(),
        //     ];
        //     $data = array_merge($post, $nowData);
        //     $mini_id = Db::name('minipro')->insertGetId($data);
        //     if (false !== $mini_id) {
        //         try {
        //             model('Minipro')->after_theme_add($mini_id);
        //         } catch (\Exception $e) {
        //             Db::name('minipro')->where(['mini_id'=>$mini_id])->delete();
        //             $this->error("操作失败[".$e->getMessage()."]", url('Minipro/theme_index'));
        //         }
        //         Cache::clear('minipro');
        //         adminLog('新增小程序风格：'.$post['name']);
        //         $this->success("操作成功!", url('Minipro/theme_index'));
        //     }else{
        //         $this->error("操作失败!", url('Minipro/theme_index'));
        //     }
        //     exit;
        // }

        // return $this->fetch();
    }
    
    /**
     * 编辑模板风格
     */
    public function theme_edit()
    {
        if (IS_POST) {
            $post = input('post.');
            $r = false;
            if(!empty($post['mini_id'])){
                // 处理缩略图
                $is_remote = !empty($post['is_remote']) ? $post['is_remote'] : 0;
                $litpic = '';
                if ($is_remote == 1) {
                    $litpic = $post['litpic_remote'];
                } else {
                    $litpic = $post['litpic_local'];
                }
                $post['litpic'] = $litpic;

                // --存储数据
                $nowData = array(
                    'update_time'    => getTime(),
                );
                $data = array_merge($post, $nowData);
                $r = Db::name('minipro')->where([
                        'mini_id'    => $post['mini_id'],
                        'lang'  => $this->admin_lang,
                    ])
                    ->cache(true, null, "minipro")
                    ->update($data);
            }
            if (false !== $r) {
                adminLog('编辑小程序风格：'.$post['name']);
                $this->success("操作成功!",url('Minipro/theme_index'));
            }else{
                $this->error("操作失败!",url('Minipro/theme_index'));
            }
            exit;
        }

        $info = Db::name('minipro')->where([
                'mini_id'    => $this->mini_id,
                'lang'  => $this->admin_lang,
            ])->find();
        if (empty($info)) {
            $this->error('数据不存在，请联系管理员！');
            exit;
        }
        if (is_http_url($info['litpic'])) {
            $info['is_remote'] = 1;
            $info['litpic_remote'] = handle_subdir_pic($info['litpic']);
        } else {
            $info['is_remote'] = 0;
            $info['litpic_local'] = handle_subdir_pic($info['litpic']);
        }
        $this->assign('info',$info);

        return $this->fetch();
    }
    
    /**
     * 删除模板风格
     */
    public function theme_del()
    {
        if (IS_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if(!empty($id_arr)){
                $result = Db::name('minipro')->field('is_default,name')
                    ->where([
                        'mini_id'    => ['IN', $id_arr],
                        'lang'  => $this->admin_lang,
                    ])->select();
                $name_list = [];
                foreach ($result as $key => $val) {
                    if ($val['is_default'] == 1) {
                        $this->error('禁止删除默认风格！');
                    }
                    $name_list[] = $val['name'];
                }

                $r = Db::name('minipro')->where([
                        'mini_id'    => ['IN', $id_arr],
                        'lang'  => $this->admin_lang,
                    ])->update([
                        'is_del'    => 1,
                        'update_time'   => getTime(),
                    ]);
                if($r){
                    /*删除相关记录*/
                    try {
                        Db::name('minipro_category')->where(['mini_id'=>$mini_id,'lang'=>$this->admin_lang])->delete();
                        Db::name('minipro_help')->where(['mini_id'=>$mini_id,'lang'=>$this->admin_lang])->delete();
                        Db::name('minipro_page')->where(['mini_id'=>$mini_id,'lang'=>$this->admin_lang])->delete();
                        Db::name('minipro_setting')->where(['mini_id'=>$mini_id,'lang'=>$this->admin_lang])->delete();
                        Db::name('minipro_tabbar')->where(['mini_id'=>$mini_id,'lang'=>$this->admin_lang])->delete();
                    } catch (\Exception $e) {}
                    /*end*/
                    Cache::clear('minipro_page');
                    adminLog('删除模板风格：'.implode(',', $name_list));
                    $this->success('删除成功');
                }else{
                    $this->error('删除失败');
                }
            } else {
                $this->error('参数有误');
            }
        }
        $this->error('非法访问');
    }

    /**
     * 设为使用
     * @return [type] [description]
     */
    public function ajax_set_default_theme()
    {
        if (IS_POST) {
            $mini_id = input('post.mini_id/d');
            $re = Db::name('minipro')->where([
                'mini_id'    => $mini_id,
                'is_default'    => 1,
            ])->count();
            if (!empty($re)) $this->error('正在使用中');

            $r = Db::name('minipro')->where([
                'mini_id'    => $mini_id,
                'lang'      => $this->admin_lang,
                ])->cache(true,null,'minipro')
                ->update(['is_default' => 1, 'update_time' => getTime()]);

            if (false !== $r) {
                Db::name('minipro')->where([
                    'mini_id'    => ['NEQ', $mini_id],
                    'lang'      => $this->admin_lang,
                ])->update(['is_default' => 0, 'update_time' => getTime()]);
                $this->success('设置成功');
            }
        }
        $this->error('设置失败');
    }

    /**
     * 页面管理
     */
    public function page_index()
    {
        $mini_id = input('param.mini_id/d');
        if (empty($mini_id)) {
            $this->redirect(url('Minipro/page_index', ['mini_id'=>$this->mini_id, 'lang'=>$this->admin_lang]));
            exit;
        }

        $list = array();
        $param = input('param.');
        $condition = array();
        // 应用搜索条件
        foreach (['keywords', 'mini_id'] as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['page_name'] = array('LIKE', "%{$param[$key]}%");
                } else {
                    $tmp_key = $key;
                    $condition[$tmp_key] = array('eq', $param[$key]);
                }
            }
        }

        // 多语言
        $condition['lang'] = array('eq', $this->admin_lang);

        $minipageM =  Db::name('minipro_page');
        $count = $minipageM->where($condition)->count('page_id');// 查询满足要求的总记录数
        $Page = $pager = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $minipageM->where($condition)->order('page_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$pager);// 赋值分页对象

        $this->assign('page_type_list', $this->page_type_list);

        return $this->fetch();
    }

    /**
     * 添加小程序页面
     */
    public function page_add()
    {
        if (IS_POST) {
            if (!empty($this->mini_id)) {
                $data = input('post.data', null, null);
                $data = json_decode($data, true);
                $page_name = !empty($data['page']['params']['name']) ? $data['page']['params']['name'] : '页面名称';
                $page_type = !empty($data['page']['params']['type']) ? $data['page']['params']['type'] : -1;
                // --存储数据
                $saveData = array(
                    'page_type' => $page_type,
                    'page_name' => $page_name,
                    'page_data' => json_encode($data),
                    'mini_id'   => $this->mini_id,
                    'lang'  => $this->admin_lang,
                    'add_time'    => getTime(),
                    'update_time'    => getTime(),
                );
                $page_id = Db::name('minipro_page')->insertGetId($saveData);
                if (false !== $page_id) {
                    Cache::clear('minipro_page');
                    adminLog('新增小程序页面：'.$page_name);

                    $type = input('param.type/s');
                    $authorizerstatus = input('param.authorizerstatus/d');
                    if ('qrcode' == $type) { // 保存预览
                        $this->autoSubmitPreview($authorizerstatus, $page_id);
                    } else if ('audit' == $type) { // 审核发布
                        $this->autoSubmitAudit($authorizerstatus, $page_id);
                    }

                    $this->success("操作成功", url('Minipro/page_index', ['mini_id'=>$this->mini_id]));
                }
            }
            $this->error("操作失败");
            exit;
        }

        $assign['jsonData'] = json_encode(['page' => model('MiniproPage')->getDefaultPage(), 'items' => []]);
        
        $assign['defaultData'] = json_encode(model('MiniproPage')->getDefaultItems());
        $allCatgory = allow_release_arctype(); // 全部分类
        $productCategory = allow_release_arctype(0, [2]); // 产品分类
        $opts = [
            // 'productCategory' => $productCategory, // 产品分类
            // 'sharingCatgory' => [], // 拼团分类
            'page_type_list'    => $this->page_type_list,
            'page_links'    => model('MiniproPage')->get_page_links(),
        ];
        $assign['opts'] = json_encode($opts);
        $assign['allCatgory'] = $allCatgory;
        $assign['productCategory'] = $productCategory;
        $assign['singleCatgory'] = allow_release_arctype(0, [6]); // 单页模型分类;
        $assign['gbookCatgory'] = allow_release_arctype(0, [8]); // 留言模型分类;
        $assign['archivesCatgory'] = allow_release_arctype(0); // 可以发布列表的模型分类;
        $assign['customPageCatgory'] = model('MiniproPage')->get_list_selecthtml(); // DIY页面列表;

        // 小程序配置信息
        $assign['inc'] = $this->miniproLogic->synRemoteSetting();

        /*保存预览 - 第一次授权之后，只自动弹出一次体验二维码*/
        $one_authori = input('param.one_authori/d');
        $show_qrcode_total = intval(session('show_qrcode_total'));
        if (1 == $one_authori) {
            $show_qrcode_total = intval($show_qrcode_total) + 1;
            session('show_qrcode_total', $show_qrcode_total);
        }
        $assign['show_qrcode_total'] = $show_qrcode_total;

        $this->assign($assign);

        return $this->fetch('page_edit');
    }

    /**
     * 编辑小程序页面
     */
    public function page_edit()
    {
        $page_id = input('param.page_id/d');
        // 页面ID不存在，默认编辑首页
        if (empty($page_id)) {
            $page_id = Db::name('minipro_page')->where([
                'mini_id'   => $this->mini_id,
                'is_home'   => 1,
            ])->value('page_id');
            if (empty($page_id)) {
                $this->error('小程序页面不存在！');
            }
            $url = url('Minipro/page_edit', ['page_id'=>$page_id,'mini_id'=>$this->mini_id]);
            $this->redirect($url);
            exit;
        }

        if (IS_POST) {
            if (!empty($this->mini_id)) {
                $data = input('post.data', null, null);
                $data = json_decode($data, true);
                $page_name = !empty($data['page']['params']['name']) ? $data['page']['params']['name'] : '页面名称';
                $page_type = !empty($data['page']['params']['type']) ? $data['page']['params']['type'] : -1;
                // --存储数据
                $saveData = array(
                    'page_type' => $page_type,
                    'page_name' => $page_name,
                    'page_data' => json_encode($data),
                    'mini_id'   => $this->mini_id,
                    'lang'  => $this->admin_lang,
                    'update_time'    => getTime(),
                );
                $r = Db::name('minipro_page')->where([
                    'mini_id'   => $this->mini_id,
                    'page_id'   => $page_id,
                ])->update($saveData);
                if (false !== $r) {
                    Cache::clear('minipro_page');
                    adminLog('编辑小程序页面：'.$page_name);

                    $fmdo = input('param.fmdo/s');
                    $authorizerstatus = input('param.authorizerstatus/d');
                    if ('preview' == $fmdo) { // 保存预览
                        $this->autoSubmitPreview($authorizerstatus, $page_id);
                    } else if ('audit' == $fmdo) { // 审核发布
                        $this->autoSubmitAudit($authorizerstatus, $page_id);
                    }

                    $this->success("操作成功", url('Minipro/page_index', ['mini_id'=>$this->mini_id]));
                }
            }
            $this->error("操作失败");
            exit;
        }

        $info = Db::name('minipro_page')->where([
            'mini_id'   => $this->mini_id,
            'page_id'   => $page_id,
        ])->find();
        $assign['jsonData'] = $info['page_data'];
        $assign['info'] = $info;

        $assign['defaultData'] = json_encode(model('MiniproPage')->getDefaultItems());
        $allCatgory = allow_release_arctype(); // 全部分类
        $productCategory = allow_release_arctype(0, [2]); // 产品分类
        $opts = [
            // 'productCategory' => $productCategory, // 产品分类
            // 'sharingCatgory' => [], // 拼团分类
            'page_type_list'    => $this->page_type_list,
            'page_links'    => model('MiniproPage')->get_page_links(),
        ];
        $assign['opts'] = json_encode($opts);
        $assign['allCatgory'] = $allCatgory;
        $assign['productCategory'] = $productCategory;
        $assign['singleCatgory'] = allow_release_arctype(0, [6]); // 单页模型分类;
        $assign['gbookCatgory'] = allow_release_arctype(0, [8]); // 留言模型分类;
        $assign['archivesCatgory'] = allow_release_arctype(0); // 可以发布列表的模型分类;
        $assign['customPageCatgory'] = model('MiniproPage')->get_list_selecthtml(); // DIY页面列表;

        // 小程序配置信息
        $assign['inc'] = $this->miniproLogic->synRemoteSetting();

        /*保存预览 - 第一次授权之后，只自动弹出一次体验二维码*/
        $one_authori = input('param.one_authori/d');
        $show_qrcode_total = intval(session('show_qrcode_total'));
        if (1 == $one_authori) {
            $show_qrcode_total = intval($show_qrcode_total) + 1;
            session('show_qrcode_total', $show_qrcode_total);
        }
        $assign['show_qrcode_total'] = $show_qrcode_total;

        $this->assign($assign);

        return $this->fetch('page_edit');
    }

    /**
     * 授权小程序
     * 上传小程序代码
     * 获取体验二维码
     * @return [type] [description]
     */
    public function autoSubmitPreview($authorizerstatus = 0, $page_id = '')
    {
        $page_id = input('param.page_id/d', $page_id);
        $one_authori = input('param.one_authori/d');
        $authorizerstatus = input('param.authorizerstatus/d', $authorizerstatus);
        if (empty($authorizerstatus)) {
            $data = $this->save_setting(false);
            $gourl = urlencode(url('Minipro/autoSubmitPreview', ['authorizerstatus'=>1,'one_authori'=>1,'page_id'=>$page_id,'mini_id'=>$this->mini_id], true, $this->request->domain()));
            $poststr = base64_encode(json_encode($data));
            $authorization_url = $this->miniproLogic->get_api_url("/index.php?m=api&c=Minipro&a=client_authoriza&poststr={$poststr}&template={$this->nid}&gourl={$gourl}");
            $params['url'] = $authorization_url;
            $this->success("操作成功", null, $params);
        } else {
            if (1 == $one_authori) {
                $this->save_setting();
                $url = url('Minipro/page_edit', ['one_authori'=>$one_authori,'page_id'=>$page_id,'mini_id'=>$this->mini_id]);
                header('Location: '.$url);
                exit;
            } else {
                /*同步数据到服务器*/
                $data = $this->save_setting();
                $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=MiniproClient&a=minipro"), "POST", $data);
                $params = array();
                $params = json_decode($response, true);
                if (!isset($params['errcode']) || 0 != $params['errcode']) {
                    $this->error('操作失败', null, $params);
                }
                /*--end*/
            }
        }

        $post_data = array(
            'md5code'   => $this->md5code,
            'domain'    => $this->request->host(true),
            'template'   => $this->nid,
            'fmdo'   => 'preview',
        );
        $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=Minipro&a=autoSubmit"), "POST", $post_data);
        $params = array();
        $params = json_decode($response,true);
        session('show_qrcode_total', 2);
        if (isset($params['errcode']) && in_array($params['errcode'], ['0','85004'])) {
            $this->miniproLogic->synRemoteSetting();
            $imgcode = base64_decode($params['errmsg']);
            $filename = session('admin_id').'-'.dd2char(date("ymdHis").mt_rand(100,999)).".jpg";
            $bannerurl = UPLOAD_PATH.'allimg/'.date('Ymd');
            tp_mkdir($bannerurl);
            $bannerurl = $bannerurl."/".$filename;
            $imgurl = '';
            if (file_put_contents($bannerurl, $imgcode)){
                $imgurl = $this->request->domain().$this->root_dir."/{$bannerurl}";
            }

            $params['imgurl'] = $imgurl;
            $this->success("操作成功", url('Minipro/page_index', ['mini_id'=>$this->mini_id]), $params);
        } else {
            $this->error($params['errmsg'], null, $params);
        }
    }

    /**
     * 授权小程序
     * 上传小程序代码
     * 提交审核
     * @return [type] [description]
     */
    public function autoSubmitAudit($authorizerstatus = 0, $page_id = '')
    {
        $page_id = input('param.page_id/d', $page_id);
        $one_authori = input('param.one_authori/d');
        $authorizerstatus = input('param.authorizerstatus/d', $authorizerstatus);
        if (empty($authorizerstatus)) {
            $data = $this->save_setting(false);
            $gourl = urlencode(url('Minipro/autoSubmitAudit', ['authorizerstatus'=>1,'one_authori'=>1,'page_id'=>$page_id,'mini_id'=>$this->mini_id], true, $this->request->domain()));
            $poststr = base64_encode(json_encode($data));
            $authorization_url = $this->miniproLogic->get_api_url("/index.php?m=api&c=Minipro&a=client_authoriza&poststr={$poststr}&template={$this->nid}&gourl={$gourl}");
            $params['url'] = $authorization_url;
            $this->success("操作成功", null, $params);
        } else {
            if (1 == $one_authori) {
                $this->save_setting();
            } else {
                /*同步数据到服务器*/
                $data = $this->save_setting();
                $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=MiniproClient&a=minipro"), "POST", $data);
                $params = array();
                $params = json_decode($response, true);
                if (!isset($params['errcode']) || 0 != $params['errcode']) {
                    $this->error('操作失败', null, $params);
                }
                /*--end*/
            }
        }

        $inc = model('MiniproSetting')->getSettingValue('setting');
        if (!empty($inc) && 4 == $inc['miniproStatus']) {
            if (2 == $inc['auditstatus']) {
                $estimateTime = date('Y-m-d H:i:s', $inc['estimateTime']);
                $msg = "审核中……预计{$estimateTime}之前完成";
                $url = url('Minipro/page_edit', ['page_id'=>$page_id,'mini_id'=>$this->mini_id]);
                $this->success($msg, $url, [], 3);
            }
        }

        $post_data = array(
            'md5code'   => $this->md5code,
            'domain'    => $this->request->host(true),
            'template'   => $this->nid,
            'fmdo'   => 'audit',
        );
        $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=Minipro&a=autoSubmit"), "POST", $post_data);
        $params = array();
        $params = json_decode($response,true);
        if (isset($params['errcode']) && 0 == $params['errcode']) {
            $msg = "预计7个工作日之内完成审核！";
            $inc = $this->miniproLogic->synRemoteSetting();
            if (!empty($inc) && 4 == $inc['miniproStatus']) {
                if (2 == $inc['auditstatus']) {
                    $estimateTime = date('Y-m-d H:i:s', $inc['estimateTime']);
                    $msg = "审核中……预计{$estimateTime}之前完成";
                }
            }
            $url = url('Minipro/page_edit', ['page_id'=>$page_id,'mini_id'=>$this->mini_id]);
            $this->success($msg, $url, [], 3);
        } else {
            $this->error($params['errmsg']);
        }
    }

    /**
     * 删除授权
     * @return [type] [description]
     */
    public function authori_del()
    {
        if (IS_POST) {
            $inc = $this->miniproLogic->getCreateSetting();
            $post_data = array(
                'appid' => $inc['appId'],
            );
            $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=MiniproClient&a=cancel_authori"), "POST", $post_data);
            $params = array();
            $params = json_decode($response,true);
            if (isset($params['errcode']) && 0 == $params['errcode']) {
                $page_id = input('param.page_id/d');
                $url = url('Minipro/page_edit', ['page_id'=>$page_id,'mini_id'=>$this->mini_id]);
                $this->success("操作成功", $url);
                // $r = Db::name('minipro_setting')->where([
                //         'name'  => 'setting',
                //         'mini_id'   => $this->mini_id,
                //         'lang'  => $this->admin_lang,
                //     ])->delete();
                // if (false !== $r) {
                //     $this->success("操作成功");
                // }
            }
        }
        $this->error('操作失败');
    }

    /**
     * 下载小程序码
     */
    public function down_qrcode()
    {
        $inc = $this->miniproLogic->getCreateSetting();
        $post_data = array(
            'appid' => $inc['appId'],
        );
        $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=Minipro&a=getWxaCodeunlimit"), "POST", $post_data);
        $params = array();
        $params = json_decode($response,true);
        if ($params) {
            if ($params['errcode'] === 0) {
                $imgcode = base64_decode($params['errmsg']);
                $filename = session('admin_id').'-'.dd2char(date("ymdHis").mt_rand(100,999)).".jpg";
                $bannerurl = UPLOAD_PATH.'allimg/'.date('Ymd');
                tp_mkdir($bannerurl);
                $bannerurl = $bannerurl."/".$filename;
                $imgurl = '';
                if (file_put_contents($bannerurl, $imgcode)){
                    $imgurl = request()->domain().$this->root_dir."/{$bannerurl}";
                }
                
                // header("Cache-control: private");
                header("Content-Type:application/force-download"); //设置要下载的文件类型
                header("Content-Disposition: attachment; filename={$filename}"); //设置要下载文件的文件名
                readfile($imgurl);
                exit();
            }
            $this->error($params['errmsg']);
        }
        $this->error('接口请求失败，请尝试');
    }

    /**
     * 复制小程序页面
     */
    public function page_copy()
    {
        $page_id = input('param.page_id/d');
        if (IS_POST) {
            if (!empty($page_id)) {
                $result = Db::name('minipro_page')->field('page_id', true)->where([
                    'mini_id'   => $this->mini_id,
                    'page_id'   => $page_id,
                ])->find();
                /*如果是默认首页，修改为自定义页*/
                if ($result['is_home'] == 1) {
                    $result['is_home'] = 0;
                    $result['page_type'] = -1;
                    $page_data = json_decode($result['page_data'], true);
                    isset($page_data['page']['params']['type']) && $page_data['page']['params']['type'] = $result['page_type'];
                    $result['page_data'] = json_encode($page_data);
                }

                $result['add_time'] = getTime();
                $result['update_time'] = getTime();
                $r = Db::name('minipro_page')->insertGetId($result);
                if (false !== $r) {
                    adminLog('复制小程序页面：'.$result['page_name']);
                    $this->success("操作成功", url('Minipro/page_index', ['mini_id'=>$this->mini_id]));
                }
            }
        }
        $this->error("操作失败");
    }
    
    /**
     * 删除小程序页面
     */
    public function page_del()
    {
        if (IS_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if(!empty($id_arr)){
                $result = Db::name('minipro_page')->field('page_name')
                    ->where([
                        'page_id'    => ['IN', $id_arr],
                        'mini_id'   => $this->mini_id,
                        'lang'  => $this->admin_lang,
                        'is_home'   => ['NEQ', 1],
                    ])->select();
                $page_name_list = get_arr_column($result, 'page_name');

                $r = Db::name('minipro_page')->where([
                        'page_id'    => ['IN', $id_arr],
                        'mini_id'   => $this->mini_id,
                        'lang'  => $this->admin_lang,
                        'is_home'   => ['NEQ', 1],
                    ])->delete();
                if($r){
                    Cache::clear('minipro_page');
                    adminLog('删除小程序页面：'.implode(',', $page_name_list));
                    $this->success('删除成功');
                }else{
                    $this->error('删除失败');
                }
            } else {
                $this->error('参数有误');
            }
        }
        $this->error('访问出错！');
    }

    /**
     * 设置默认首页
     * @return array
     * @throws \think\exception\DbException
     */
    public function page_sethome()
    {
        $page_id = input('param.page_id/d');
        if (IS_AJAX_POST && !empty($page_id)) {
            $row = Db::name('minipro_page')->where([
                'is_home'   => 1,
                'mini_id'   => $this->mini_id,
                'lang'      => $this->admin_lang,
            ])->find();
            $page_data = json_decode($row['page_data'], true);
            $page_data['page']['params']['type'] = -1;
            $page_data_new = json_encode($page_data);

            $r = Db::name('minipro_page')->where([
                'page_id'   => $row['page_id'],
            ])->update([
                'page_data' => $page_data_new,
                'is_home'   => 0,
                'page_type' => -1,
                'update_time'   => getTime(),
            ]);
            if ($r !== false) {
                $row2 = Db::name('minipro_page')->where([
                    'page_id'   => $page_id,
                    'mini_id'   => $this->mini_id,
                    'lang'      => $this->admin_lang,
                ])->find();
                $page_data2 = json_decode($row2['page_data'], true);
                $page_data2['page']['params']['type'] = 1;
                $page_data_new2 = json_encode($page_data2);
                $r = Db::name('minipro_page')->where([
                    'page_id'   => $page_id,
                    'mini_id'   => $this->mini_id,
                ])->update([
                    'page_data' => $page_data_new2,
                    'is_home'   => 1,
                    'page_type' => 1,
                    'update_time'   => getTime(),
                ]);
                if ($r !== false) {
                    Cache::clear('minipro_page');
                    adminLog('设置小程序默认首页：'.$page_id);
                    $this->success('设置成功！');
                }
            }
            $this->error('设置失败！');
        }
        $this->error('访问出错！');
    }

    /**
     * 导入页面
     * @return [type] [description]
     */
    public function page_import()
    {
        if (IS_POST) {
            if (!empty($this->mini_id)) {

                $content = '';
                $file = request()->file('importfile');
                if(empty($file)){
                    $this->error('请上传txt文件');
                }
                // 移动到框架应用根目录/uploads/soft/ 目录下
                $path = UPLOAD_PATH.'soft/'.date('Ymd/');
                $filename = session('admin_id').'-'.dd2char(date("ymdHis").mt_rand(100,999));
                $info = $file->validate(['ext'=>'txt'])->move($path, $filename);

                if ($info) {
                    //上传成功 获取上传文件信息
                    $file_path_full = $info->getPathName();
                    if (file_exists($file_path_full)) {
                        $fp = fopen($file_path_full, 'r');
                        $content = fread($fp, filesize($file_path_full));
                        fclose($fp);
                        $content = trim($content);
                    } else {
                        $this->error('导入失败');
                    }
                } else {
                    //上传错误提示错误信息
                    $this->error($file->getError());
                }

                if (empty($content)) {
                    $this->error('上传文件没有内容！');
                }

                /*远程解密*/
                $vaules = array(
                    'content' => $content,      
                    'domain' => $this->request->host(true),
                );
                $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=MiniproClient&a=decode_minipro_page"), 'POST', $vaules);
                $params = array();
                $params = json_decode($response, true);
                if (!empty($params) && is_array($params)) {
                    if ($params['errcode'] == 0) {
                        $data = $params['errmsg'];
                    } else {
                        $this->error($params['errmsg']);
                    }
                } else {
                    $this->error('远程解密失败，联系官方技术人员！');
                }
                
                $addData = [
                    'mini_id'   => $this->mini_id,
                    'lang'  => $this->admin_lang,
                    'add_time'  => getTime(),
                    'update_time'  => getTime(),
                ];
                $addData = array_merge($addData, $data);
                $r = Db::name('minipro_page')->insert($addData);
                if ($r !== false) {
                    Cache::clear('minipro_page');
                    adminLog('导入小程序DIY页面：'.$addData['page_name']);
                    $this->success("操作成功", url('Minipro/page_index', ['mini_id'=>$this->mini_id]));
                }
            }
            $this->error("操作失败");
        }
    }

    /**
     * 导出页面
     * @return [type] [description]
     */
    public function page_export()
    {
        if (IS_AJAX_POST) {
            $page_id = input('param.page_id/d');
            if (!empty($page_id)) {
                session('minipro_page_export', null);
                $row = Db::name('minipro_page')->find($page_id);
                $content = base64_encode(json_encode($row));

                /*远程加密*/
                $vaules = array(
                    'content' => $content,      
                    'domain' => $this->request->host(true),
                );
                $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=MiniproClient&a=encode_minipro_page"), 'POST', $vaules);
                $params = array();
                $params = json_decode($response, true);
                if (!empty($params) && is_array($params)) {
                    if ($params['errcode'] == 0) {
                        $content = $params['errmsg'];
                    } else {
                        $this->error($params['errmsg']);
                    }
                } else {
                    $this->error('远程加密失败，联系官方技术人员！');
                }

                session('minipro_page_export', $content);
                $this->success('导出成功', url('Minipro/page_index', ['mini_id'=>$this->mini_id,'lang'=>$this->admin_lang]));
            }
        }

        ob_end_clean();
        $content = session('minipro_page_export');
        if (!empty($content)) {
            $filename = session('admin_id').'-'.dd2char(date("ymdHis").mt_rand(100,999));
            header("Content-type: application/text/plain");
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=" . $filename . ".txt");
            header('Expires:0');
            header('Pragma:public');
            echo $content;
            session('minipro_page_export', null);
            exit;
        } else {
            $this->error('导出失败');
        }
    }

    /**
     * 分类模板
     */
    public function page_category()
    {
        if (IS_AJAX_POST) {
            if (!empty($this->mini_id)) {
                $category = input('post.category/a');
                $newData = [
                    'update_time'   => getTime(),
                ];
                $saveData = array_merge($category, $newData);
                $r = Db::name('minipro_category')->where([
                    'mini_id'   => $this->mini_id,
                    'lang'  => $this->admin_lang,
                ])->update($saveData);
                if (false !== $r) {
                    Cache::clear('minipro_category');
                    adminLog('编辑小程序分类模板：'.$page_name);
                    $this->success("操作成功", url('Minipro/page_category', ['mini_id'=>$this->mini_id]));
                }
            }
            $this->error("操作失败");
            exit;
        }

        $catInfo = Db::name('minipro_category')->where([
            'mini_id'   => $this->mini_id,
            'lang'      => $this->admin_lang,
        ])->find();
        if (empty($catInfo)) {
            // 新增小程序分类页模板
            model('MiniproCategory')->insertDefault($this->mini_id);
            $catInfo = Db::name('minipro_category')->where([
                'mini_id'   => $this->mini_id,
                'lang'      => $this->admin_lang,
            ])->find();
        }
        $assign['catInfo'] = $catInfo;

        $this->assign($assign);

        return $this->fetch();
    }

    /**
     * 底部菜单
     */
    public function tabbar_index()
    {
        $list = array();
        $param = input('param.');
        $condition = array();
        // 应用搜索条件
        $condition['mini_id'] = array('eq', $this->mini_id);
        // 多语言
        $condition['lang'] = array('eq', $this->admin_lang);

        $tabbarM =  Db::name('minipro_tabbar');
        $count = $tabbarM->where($condition)->count('id');// 查询满足要求的总记录数
        $pageObj = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $tabbarM->where($condition)->order('sort_order asc, id asc')->limit($pageObj->firstRow.','.$pageObj->listRows)->select();

        foreach ($list as $key => &$val) {
            $val['path_type_html'] = $this->get_pathvalue($val['path_type'], $val['path_value']);
        }

        $pageStr = $pageObj->show();// 分页显示输出
        $this->assign('pageStr',$pageStr);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pageObj',$pageObj);// 赋值分页对象
        // 页面路径类型
        $this->assign('path_type_list', $this->miniproLogic->path_type_list());

        return $this->fetch();
    }

    private function get_pathvalue($path_type = '', $path_value = '')
    {
        $html_arr = [];
        $select_begin = '<select name="path_value[]">';
        $select_end = '</select>';

        // 列表页
        $select_html = allow_release_arctype(intval($path_value));
        if (empty($select_html)) {
            $select_html = "<option value='0'>找不到相关数据</option>";
        }
        $html_arr[3] = $select_begin.$select_html.$select_end;

        // 文档页
        $html_arr[4] = '<input type="text" name="path_value[]" value="'.$path_value.'" placeholder="填写文档ID" style="width: 100px;">';

        // 单页栏目
        $select_html = allow_release_arctype(intval($path_value), [6]);
        if (empty($select_html)) {
            $select_html = "<option value='0'>找不到相关数据</option>";
        }
        $html_arr[5] = $select_begin.$select_html.$select_end;

        // 在线留言
        $select_html = allow_release_arctype(intval($path_value), [8]);
        if (empty($select_html)) {
            $select_html = "<option value='0'>找不到相关数据</option>";
        }
        $html_arr[6] = $select_begin.$select_html.$select_end;

        // 自定义页
        $select_html = '';
        $row = Db::name('minipro_page')->field('page_id,page_name')
            ->where([
                'page_type' => -1,
                'is_del'    => 0,
                'mini_id'   => $this->mini_id,
                'lang'      => $this->admin_lang,
            ])->order('page_id asc')->select();
        if (!empty($row)) {
            $select_html = $select_begin.$select_html;
            foreach ($row as $key => $val) {
                $selected = '';
                if (intval($val['page_id']) == intval($path_value)) {
                    $selected = ' selected="ture" ';
                }
                $select_html .= "<option value='{$val['page_id']}' {$selected}>{$val['page_name']}</option>";
            }
            $select_html .= $select_end;
        }
        $html_arr[9] = $select_html;

        return !empty($html_arr[$path_type]) ? $html_arr[$path_type] : '<input type="hidden" name="path_value[]" value="">';
    }

    /**
     * 保存底部菜单
     */
    public function tabbar_save()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');

            if (empty($post['text'])) {
                $this->error('至少新增一个菜单！');
            }

            $saveData = [];
            foreach ($post['id'] as $key => $val) {
                $saveData[] = [
                    'text'  => !empty($post['text'][$key]) ? trim($post['text'][$key]) : '',
                    'path_type'  => !empty($post['path_type'][$key]) ? intval($post['path_type'][$key]) : 0,
                    'path_value'  => !empty($post['path_value'][$key]) ? intval($post['path_value'][$key]) : '',
                    'icon'  => !empty($post['icon'][$key]) ? trim($post['icon'][$key]) : '',
                    'selected_icon'  => !empty($post['selected_icon'][$key]) ? trim($post['selected_icon'][$key]) : '',
                    'mini_id'  => $this->mini_id,
                    'lang'  => $this->admin_lang,
                    'sort_order'  => !empty($post['sort_order'][$key]) ? intval($post['sort_order'][$key]) : 0,
                    'status'  => !empty($post['status'][$key]) ? intval($post['status'][$key]) : 0,
                    'add_time'  => getTime(),
                    'update_time'  => getTime(),
                ];
            }
            $tabbar_db = Db::name('minipro_tabbar');
            if (empty($saveData)) {
                $r = $tabbar_db->where([
                    'mini_id'   => $this->mini_id,
                    'lang'   => $this->admin_lang,
                ])->delete();
            } else {
                $max_id = $tabbar_db->where([
                    'mini_id'   => $this->mini_id,
                    'lang'   => $this->admin_lang,
                ])->max('id');
                $r = $tabbar_db->insertAll($saveData);
                if ($r !== false) {
                    if (intval($max_id) > 0) {
                        $tabbar_db->where([
                            'id'    => ['elt', $max_id],
                            'mini_id'   => $this->mini_id,
                            'lang'   => $this->admin_lang,
                        ])->delete();
                    }
                }
            }

            if ($r !== false) {
                \think\Cache::clear("minipro_tabbar");
                $this->success('操作成功');
            }
        }
        $this->error('操作失败');
    }

    /**
     * ajax获取底部菜单的页面路径对应的页面类型
     * @return [type] [description]
     */
    public function ajax_get_tabbar_pathvalue()
    {
        if (IS_POST) {
            $pathtype = input('param.pathtype/d');
            $select_html = $this->get_pathvalue($pathtype);
            $this->success('读取成功', null, ['msg'=>$select_html]);
        }
        $this->error('非法访问');
    }

    /**
     * 帮助中心
     */
    public function help_index()
    {
        $list = array();
        $param = input('param.');
        $condition = array();
        // 应用搜索条件
        foreach (['keywords', 'mini_id'] as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['title'] = array('LIKE', "%{$param[$key]}%");
                } else {
                    $tmp_key = $key;
                    $condition[$tmp_key] = array('eq', $param[$key]);
                }
            }
        }

        // 多语言
        $condition['lang'] = array('eq', $this->admin_lang);

        $minihelpM =  Db::name('minipro_help');
        $count = $minihelpM->where($condition)->count('help_id');// 查询满足要求的总记录数
        $Page = $pager = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $minihelpM->where($condition)->order('help_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$pager);// 赋值分页对象

        return $this->fetch();
    }

    /**
     * 新增小程序帮助中心
     */
    public function help_add()
    {
        if (IS_POST) {
            $post = input('post.');

            // --存储数据
            $nowData = array(
                'lang'  => $this->admin_lang,
                'add_time'    => getTime(),
                'update_time'    => getTime(),
            );
            $saveData = array_merge($post, $nowData);
            $insertId = Db::name('minipro_help')->insertGetId($saveData);
            if (false !== $insertId) {
                Cache::clear('minipro_help');
                adminLog('新增小程序帮助中心：'.$post['title']);
                $this->success("操作成功!", url('Minipro/help_index', ['mini_id'=>$this->mini_id]));
            }else{
                $this->error("操作失败!");
            }
            exit;
        }

        return $this->fetch();
    }

    /**
     * 编辑小程序帮助中心
     */
    public function help_edit()
    {
        $help_id = input('param.help_id/d');
        if (IS_POST) {
            if (!empty($this->mini_id)) {
                $post = input('post.');

                // --存储数据
                $nowData = array(
                    'lang'  => $this->admin_lang,
                    'update_time'    => getTime(),
                );
                $saveData = array_merge($post, $nowData);
                $r = Db::name('minipro_help')->where([
                    'mini_id'   => $this->mini_id,
                    'help_id'   => $help_id,
                ])->update($saveData);
                if (false !== $r) {
                    Cache::clear('minipro_help');
                    adminLog('编辑小程序帮助中心：'.$post['title']);
                    $this->success("操作成功", url('Minipro/help_index', ['mini_id'=>$this->mini_id]));
                }
            }
            $this->error("操作失败");
            exit;
        }

        $info = Db::name('minipro_help')->where([
            'mini_id'   => $this->mini_id,
            'help_id'   => $help_id,
        ])->find();
        if (empty($info)) {
            $this->error('数据不存在，请联系管理员！');
            exit;
        }
        $assign['info'] = $info;

        $this->assign($assign);

        return $this->fetch();
    }
    
    /**
     * 删除小程序帮助中心
     */
    public function help_del()
    {
        if (IS_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if(!empty($id_arr)){
                $result = Db::name('minipro_help')->field('title')
                    ->where([
                        'help_id'    => ['IN', $id_arr],
                        'mini_id'   => $this->mini_id,
                        'lang'  => $this->admin_lang,
                    ])->select();
                $title_list = get_arr_column($result, 'title');

                $r = Db::name('minipro_help')->where([
                        'help_id'    => ['IN', $id_arr],
                        'mini_id'   => $this->mini_id,
                        'lang'  => $this->admin_lang,
                    ])
                    ->cache(true, null, "minipro_help")
                    ->delete();
                if($r){
                    adminLog('删除小程序帮助中心：'.implode(',', $title_list));
                    $this->success('删除成功');
                }
            }
            $this->error('删除失败');
        }
        $this->error('访问出错！');
    }

    /**
     * 页面链接参考
     */
    public function page_links()
    {
        return $this->fetch();
    }

    /**
     * 联系我们
     */
    public function contact_edit()
    {
        if (IS_POST) {
            $post = input('post.');
            $data = array();
            foreach ($post as $key => $val) {
                if (1 == preg_match('/(_is_remote|_remote|_local)$/', $key)) { // 处理上传本地与远程图片的字段转化
                    if (1 == preg_match('/(_local)$/', $key)) {
                        $tmpkey = preg_replace('/^(.*)(_local)$/', '$1', $key);
                        $tmp_is_remote = !empty($post[$tmpkey.'_is_remote']) ? $post[$tmpkey.'_is_remote'] : 0;
                        $val = '';
                        if ($tmp_is_remote == 1) {
                            $val = $post[$tmpkey.'_remote'];
                        } else {
                            $val = $post[$tmpkey.'_local'];
                        }
                        $data[$tmpkey] = $val;
                        unset($post[$tmpkey.'_local']);
                        unset($post[$tmpkey.'_remote']);
                        unset($post[$tmpkey.'_is_remote']);
                    }
                } else {
                    if ('coordinate' == $key) {
                        $coordinateArr = explode(',', $val);
                        $data['latitude'] = !empty($coordinateArr[0]) ? $coordinateArr[0] : 0;
                        $data['longitude'] = !empty($coordinateArr[1]) ? $coordinateArr[1] : 0;
                    }
                    $data[$key] = $val;
                }
            }

            /*保存数据*/
            $newData = array(
                'value' => json_encode($data),
            );
            $info = model('MiniproSetting')->getSettingInfo('contact');
            if (empty($info)) { // 新增
                $newData['name'] = 'contact';
                $newData['mini_id'] = $this->mini_id;
                $newData['lang'] = $this->admin_lang;
                $newData['add_time'] = getTime();
                $r = Db::name('minipro_setting')->insert($newData);
            } else {
                $newData['update_time'] = getTime();
                $r = Db::name('minipro_setting')->where([
                    'name' => 'contact',
                    'mini_id'   => $this->mini_id,
                    'lang'  => $this->admin_lang,
                ])->update($newData);
            }
            if (false !== $r) {
                \think\Cache::clear('minipro_setting');
                $this->success('操作成功', url('Minipro/contact_edit', ['mini_id'=>$this->mini_id]));
            }
            /*--end*/
            $this->error('操作失败');
        }

        $assign_data = array();

        $info = model('MiniproSetting')->getSettingValue('contact');
        if (empty($info)) {
            $info = array(
                'logo_is_remote' => 0,
                'logo_local' => $this->root_dir.'/public/static/common/minipro/img/logo.png',
                'banner_is_remote' => 0,
                'banner_local' => $this->root_dir.'/public/static/common/minipro/img/banner.jpg',
            );
        } else {
            foreach ($info as $key => $val) {
                /*转换图片为本地与远程*/
                if (1 == preg_match('/(logo|banner)$/', $key)) {
                    if (is_http_url($val)) {
                        $info[$key.'_is_remote'] = 1;
                        $info[$key.'_remote'] = $val;
                    } else {
                        $info[$key.'_is_remote'] = 0;
                        $info[$key.'_local'] = $val;
                    }
                }
                /*--end*/
            }
        }
        $assign_data['info'] = $info;

        $this->assign($assign_data);

        return $this->fetch();
    }

    /**
     * 在线生成小程序
     * @return [type] [description]
     */
    // public function create_step1()
    // {
    //     if (IS_POST) {
    //         $post = input('post.');
    //         $post['domain'] = trim($post['domain'], '/');
    //         if (empty($post['domain'])) {
    //             $this->error('api接口域名不能为空！');
    //         }
    //         $post['name'] = 'setting'; // 小程序配置信息的name值
    //         $post['nid'] = $this->nid; // 模板nid，每套模板唯一

    //         /*提取小程序首页的导航标题*/
    //         $page_data = Db::name('minipro_page')->where([
    //                 'mini_id'   => $this->mini_id,
    //                 'is_home'   => 1,
    //                 'is_del'    => 0,
    //                 'lang'      => $this->admin_lang,
    //             ])->value('page_data');
    //         $page_data = json_decode($page_data, true);
    //         $navTitle = !empty($page_data['page']['params']['title']) ? $page_data['page']['params']['title'] : '';
    //         $post['navTitle'] = $navTitle;
    //         /*end*/

    //         /*同步数据到服务器*/
    //         $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=MiniproClient&a=minipro"), "POST", $post);
    //         $params = array();
    //         $params = json_decode($response, true);
    //         /*--end*/

    //         if (!empty($params)) {
    //             if ($params['errcode'] == 0) {
    //                 /*保存数据*/
    //                 $newData = array(
    //                     'name' => 'setting', // 小程序配置信息的name值
    //                     'value' => json_encode($post),
    //                 );
    //                 $row = model('MiniproSetting')->getSettingInfo('setting');
    //                 if (empty($row)) { // 新增
    //                     $newData['mini_id'] = $this->mini_id;
    //                     $newData['lang'] = $this->admin_lang;
    //                     $newData['add_time'] = getTime();
    //                     $r = Db::name('minipro_setting')->insert($newData);
    //                 } else {
    //                     $newData['update_time'] = getTime();
    //                     $r = Db::name('minipro_setting')->where([
    //                         'name'  => 'setting',
    //                         'mini_id'   => $this->mini_id,
    //                         'lang'  => $this->admin_lang,
    //                     ])->update($newData);
    //                 }
    //                 if (false !== $r) {
    //                     /*同步小程序appid/appsecret到minipro表*/
    //                     Db::name('minipro')->where([
    //                         'mini_id'   => $this->mini_id,
    //                         'lang'  => $this->admin_lang,
    //                     ])->update([
    //                         'app_id'        => trim($post['appId']),
    //                         'app_secret'    => trim($post['appSecret']),
    //                         'update_time'   => getTime(),
    //                     ]);
    //                     /*end*/
    //                     header('Location: '.url('Minipro/createMinipro', ['mini_id'=>$this->mini_id]));
    //                     exit;
    //                 }
    //                 /*--end*/
    //             } else {
    //                 $this->error($params['errmsg']);
    //             }
    //         }
    //         $this->error('操作失败');
    //     }

    //     $assign_data = array();

    //     $row = $this->miniproLogic->getCreateSetting();
    //     if (empty($row) || is_array($row)) {
    //         $miniproInfo = Db::name('minipro')->field('app_id,app_secret')->where(['mini_id'=>$this->mini_id,'lang'=>$this->admin_lang])->find();
    //         $row['appId'] = $miniproInfo['app_id'];
    //         $row['appSecret'] = $miniproInfo['app_secret'];
    //     }
    //     $assign_data['row'] = $row;

    //     /*模板类型*/
    //     $template_list = array();
    //     $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=MiniproClient&a=get_minipro_list"), "GET");
    //     $params = json_decode($response,true);
    //     if (!empty($params) && $params['errcode'] == 0) {
    //         $template_list = $params['errmsg'];
    //     } else {
    //         $this->error('小程序模板不存在');
    //     }
    //     $miniproNum = preg_replace('/([a-z])/i', '', $template_list[$this->nid]['nid']);
    //     $assign_data['version'] = 'v'.intval($miniproNum).'.0';
    //     $assign_data['template_list'] = $template_list;
    //     /*--end*/

    //     $assign_data['scheme'] = $this->request->scheme();

    //     $this->assign($assign_data);

    //     return $this->fetch();
    // }

    /**
     * 生成小程序
     */
    // public function createMinipro()
    // {
    //     $inc = $this->miniproLogic->getCreateSetting();
    //     if (empty($inc)) {
    //         $this->error('先填写小程序参数配置！');
    //     }

    //     if ($inc['authorizerStatus'] == 0) {
    //         $gourl = urlencode(url('Minipro/createMinipro', ['mini_id'=>$this->mini_id], true, $this->request->domain()));
    //         $authorization_url = $this->miniproLogic->get_api_url("/index.php?m=api&c=Minipro&a=client_authoriza&authorizer_appid=".$inc['appId']."&gourl={$gourl}");
    //         header('Location: '.$authorization_url);
    //         exit;
    //     }

    //     $post_data = array(
    //         'appid' => $inc['appId'],
    //         'domain'    => $this->request->host(true),
    //         'template'   => $this->nid,
    //     );
    //     $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=Minipro&a=createMinipro"), "POST", $post_data);
    //     $params = array();
    //     $params = json_decode($response,true);
    //     if ($params) {
    //         if ($params['errcode'] === 0) {
    //             $this->success('正在生成小程序中……', url('Minipro/create_step1', ['mini_id'=>$this->mini_id]));
    //         } else {
    //             $this->error($params['errmsg']);
    //         }
    //     }
    // }

    /**
     * 获取体验二维码
     */
    // public function create_step2()
    // {
    //     $inc = $this->miniproLogic->getCreateSetting();
    //     if (empty($inc)) {
    //         $this->error('先填写小程序参数配置！');
    //     }

    //     $post_data = array(
    //         'appid' => $inc['appId'],
    //     );
    //     $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=Minipro&a=getQrcode"), "POST", $post_data);
    //     $params = array();
    //     $params = json_decode($response,true);
    //     if ($params) {
    //         if ($params['errcode'] === 0 || $params['errcode'] == 85004) {
    //             $imgcode = base64_decode($params['errmsg']);
    //             $filename = session('admin_id').'-'.dd2char(date("ymdHis").mt_rand(100,999)).".jpg";
    //             $bannerurl = UPLOAD_PATH.'allimg/'.date('Ymd');
    //             tp_mkdir($bannerurl);
    //             $bannerurl = $bannerurl."/".$filename;
    //             $imgurl = '';
    //             if (file_put_contents($bannerurl, $imgcode)){
    //                 $imgurl = $this->request->domain().$this->root_dir."/{$bannerurl}";
    //             }

    //             $params['msg'] = $imgurl;
    //             $this->success('操作成功', null, $params);
    //         } else {
    //             $this->error($params['errmsg'], null, $params);
    //         }
    //     }

    //     $this->error('获取体验二维码失败，请重试！');
    // }

    /**
     * 提交小程序审核
     */
    // public function create_step3()
    // {
    //     $inc = $this->miniproLogic->getCreateSetting();
    //     if (empty($inc)) {
    //         $this->error('先填写小程序参数配置！');
    //     }

    //     if (2 == $inc['auditstatus']) {
    //         $estimateTime = date('Y-m-d H:i:s', $inc['estimateTime']);
    //         $this->success("审核中……预计{$estimateTime}之前完成", url('Minipro/create_step1', ['mini_id'=>$this->mini_id]), '', 3);
    //     }

    //     $post_data = array(
    //         'appid' => $inc['appId'],
    //     );
    //     $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=Minipro&a=submitAudit"), "POST", $post_data);
    //     $params = array();
    //     $params = json_decode($response,true);
    //     if ($params) {
    //         if ($params['errcode'] === 0) {
    //             $this->success("进入审核中……", url('Minipro/create_step1', ['mini_id'=>$this->mini_id]));
    //         } else {
    //             $this->error($params['errmsg']);
    //         }
    //     }

    //     $this->error('接口调用失败，请重新尝试');
    // }

    /**
     * 查询审核状态
     */
    // public function create_step4()
    // {
    //     $inc = $this->miniproLogic->getCreateSetting();
    //     if (empty($inc)) {
    //         $this->error('先填写小程序参数配置！');
    //     }

    //     $post_data = array(
    //         'appid' => $inc['appId'],
    //     );
    //     $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=Minipro&a=getAuditstatus"), "POST", $post_data);
    //     $params = array();
    //     $params = json_decode($response,true);
    //     if ($params) {
    //         echo json_encode($params);
    //         exit;
    //     }

    //     echo json_encode(array('errcode'=>-1, 'errmsg'=>'查询审核状态出错！'));
    //     exit;
    // }

    /**
     * 发布小程序
     */
    // public function create_step5()
    // {
    //     $inc = $this->miniproLogic->getCreateSetting();
    //     if (empty($inc)) {
    //         $this->error('先填写小程序参数配置！');
    //     }

    //     if ($inc['auditstatus'] == 2) {
    //         $estimateTime = date('Y-m-d H:i:s', $inc['estimateTime']);
    //         $this->success("审核中……预计{$estimateTime}之前完成", url('Minipro/create_step1', ['mini_id'=>$this->mini_id]), '', 3);
    //     } else if ($inc['auditstatus'] == 1) {
    //         $this->error('审核失败，原因：'.$inc['reason'], url('Minipro/create_step1', ['mini_id'=>$this->mini_id]), '', 5);
    //     }

    //     $post_data = array(
    //         'appid' => $inc['appId'],
    //     );
    //     $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=Minipro&a=release"), "POST", $post_data);
    //     $params = array();
    //     $params = json_decode($response,true);
    //     if ($params) {
    //         if ($params['errcode'] === 0) {
    //             $this->success("发布成功", url('Minipro/create_step1', ['mini_id'=>$this->mini_id]));
    //         } else {
    //             $this->error($params['errmsg'].'(代码'.$params['errcode'].')', url('Minipro/create_step1', ['mini_id'=>$this->mini_id]), '', 3);
    //         }
    //     }

    //     $this->error('接口调用失败，请重新尝试');
    // }

    /**
     * 下载小程序码
     */
    // public function create_step6()
    // {
    //     $inc = $this->miniproLogic->getCreateSetting();
    //     if (empty($inc)) {
    //         $this->error('先填写小程序参数配置！');
    //     }

    //     $post_data = array(
    //         'appid' => $inc['appId'],
    //     );
    //     $response = httpRequest($this->miniproLogic->get_api_url("/index.php?m=api&c=Minipro&a=getWxaCodeunlimit"), "POST", $post_data);
    //     $params = array();
    //     $params = json_decode($response,true);
    //     if ($params) {
    //         if ($params['errcode'] === 0) {
    //             $imgcode = base64_decode($params['errmsg']);
    //             $filename = session('admin_id').'-'.dd2char(date("ymdHis").mt_rand(100,999)).".jpg";
    //             $bannerurl = UPLOAD_PATH.'allimg/'.date('Ymd');
    //             tp_mkdir($bannerurl);
    //             $bannerurl = $bannerurl."/".$filename;
    //             $imgurl = '';
    //             if (file_put_contents($bannerurl, $imgcode)){
    //                 $imgurl = request()->domain().$this->root_dir."/{$bannerurl}";
    //             }
                
    //             // header("Cache-control: private");
    //             header("Content-Type:application/force-download"); //设置要下载的文件类型
    //             header("Content-Disposition: attachment; filename={$filename}"); //设置要下载文件的文件名
    //             readfile($imgurl);
    //             exit();
    //         }
    //     }

    //     $this->error('接口调用失败，请重新尝试');
    // }

    /**
     * 商品/文档组件（选择）
     * @return [type] [description]
     */
    public function ajax_archives_list()
    {
        $assign_data = array();
        $condition = array();
        // 获取到所有URL参数
        $param = input('param.');
        $typeid = input('param.typeid/d');
        $channel = input('param.channel/d');
        $assembly = input('param.assembly/s');

        // 应用搜索条件
        foreach (['keywords','typeid','channel'] as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['a.title'] = array('LIKE', "%{$param[$key]}%");
                } else if ($key == 'typeid' && !empty($param[$key])) {
                    $typeid = $param[$key];
                    $hasRow = model('Arctype')->getHasChildren($typeid);
                    $typeids = get_arr_column($hasRow, 'id');
                    /*权限控制 by 小虎哥*/
                    $admin_info = session('admin_info');
                    if (0 < intval($admin_info['role_id'])) {
                        $auth_role_info = $admin_info['auth_role_info'];
                        if(! empty($auth_role_info)){
                            if(isset($auth_role_info['only_oneself']) && 1 == $auth_role_info['only_oneself']){
                                $condition['a.admin_id'] = $admin_info['admin_id'];
                            }
                            if(! empty($auth_role_info['permission']['arctype'])){
                                if (!empty($typeid)) {
                                    $typeids = array_intersect($typeids, $auth_role_info['permission']['arctype']);
                                }
                            }
                        }
                    }
                    /*--end*/
                    $condition['a.typeid'] = array('IN', $typeids);
                } else if ($key == 'channel' && !empty($param[$key])) {
                    $condition['a.'.$key] = array('eq', $param[$key]);
                } else {
                    $condition['a.'.$key] = array('eq', $param[$key]);
                }
            }
        }

        // 排除已选中的文档
        $aids = input('param.aids/s');
        $aidArr = explode(',', trim($aids, ','));
        if (!empty($aidArr)) {
            $condition['a.aid'] = ['NOT IN', $aids];
        }

        /*多语言*/
        $condition['a.lang'] = array('eq', $this->admin_lang);
        /*--end*/

        /*回收站数据不显示*/
        $condition['a.is_del'] = array('eq', 0);
        /*--end*/

        /**
         * 数据查询，搜索出主键ID的值
         */
        $count = Db::name('archives')->alias('a')->where($condition)->count('aid');// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = Db::name('archives')
            ->field("a.aid,a.channel")
            ->alias('a')
            ->where($condition)
            ->order($orderby)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->getAllWithIndex('aid');

        /**
         * 完善数据集信息
         * 在数据量大的情况下，经过优化的搜索逻辑，先搜索出主键ID，再通过ID将其他信息补充完整；
         */
        if ($list) {
            $aids = array_keys($list);
            $fields = "b.*, a.*, a.aid as aid";
            $row = Db::name('archives')
                ->field($fields)
                ->alias('a')
                ->join('__ARCTYPE__ b', 'a.typeid = b.id', 'LEFT')
                ->where('a.aid', 'in', $aids)
                ->getAllWithIndex('aid');

            foreach ($list as $key => $val) {
                $info = $row[$val['aid']];
                if ('goods' == $assembly) {
                    $json_encode_params = [
                        'aid'   => $info['aid'],
                        'title' => func_preg_replace(['"'], '', $info['title']),
                        'litpic' => $info['litpic'],
                        'users_price' => $info['users_price'],
                        'old_price' => $info['old_price'],
                        'seo_description' => func_preg_replace(['"','\''], '', $info['seo_description']),
                        'stock_count' => $info['stock_count'],
                        'sales_count' => $info['sales_count'],
                    ];
                } else if ('article' == $assembly) {
                    $json_encode_params = [
                        'aid'   => $info['aid'],
                        'title' => func_preg_replace(['"'], '', $info['title']),
                        'litpic' => $info['litpic'],
                        'click' => $info['click'],
                    ];
                }
                $info['json_encode_params'] = json_encode($json_encode_params, JSON_UNESCAPED_SLASHES);
                $list[$key] = $info;
            }
        }
        $show = $Page->show(); // 分页显示输出
        $assign_data['page'] = $show; // 赋值分页输出
        $assign_data['list'] = $list; // 赋值数据集
        $assign_data['pager'] = $Page; // 赋值分页对象

        /*允许发布文档列表的栏目*/
        $assign_data['arctype_html'] = allow_release_arctype($typeid, [$channel]);
        /*--end*/

        $this->assign($assign_data);
        
        return $this->fetch();
    }

    private function save_setting($save = true)
    {
        $data = [];
        $data['tcp'] = $this->request->scheme();
        $data['domain'] = $this->request->host();
        $data['root_dir'] = $this->root_dir;
        $data['name'] = 'setting'; // 小程序配置信息的name值
        $data['nid'] = $this->nid; // 模板nid，每套模板唯一
        $data['intro']  = tpCache('web.web_name');
        $data['mini_id'] = $this->mini_id;
        $data['email'] = tpCache('smtp.smtp_from_eamil');
        // $data['authorizerStatus'] = input('param.authorizerStatus/d', 0);
        $data['md5code'] = $this->md5code;

        /*提取小程序首页的导航标题*/
        $page_data = Db::name('minipro_page')->where([
                'mini_id'   => $this->mini_id,
                'is_home'   => 1,
                'is_del'    => 0,
                'lang'      => $this->admin_lang,
            ])->value('page_data');
        $page_data = json_decode($page_data, true);
        $navTitle = !empty($page_data['page']['params']['title']) ? $page_data['page']['params']['title'] : '';
        $data['navTitle'] = $navTitle;

        $row = model('MiniproSetting')->getSettingInfo('setting');
        if (!empty($row) && !empty($row['value']['version'])) {
            $data['version'] = $row['value']['version'];
        }

        /*保存数据*/
        if ($save) {
            if (empty($row)) { // 新增
                $newData = array(
                    'name' => 'setting', // 小程序配置信息的name值
                    'value' => json_encode($data),
                    'mini_id'   => $this->mini_id,
                    'lang'   => $this->admin_lang,
                    'add_time'   => getTime(),
                );
                $r = Db::name('minipro_setting')->insert($newData);
            } else {
                $newData = array(
                    'name' => 'setting', // 小程序配置信息的name值
                    'value' => json_encode($data),
                    'update_time'   => getTime(),
                );
                if (is_array($row['value'])) {
                    $value = array_merge($row['value'], $data);
                    $newData['value'] = json_encode($value);
                }
                $r = Db::name('minipro_setting')->where([
                    'name'  => 'setting',
                    'mini_id'   => $this->mini_id,
                    'lang'  => $this->admin_lang,
                ])->update($newData);
            }
        }

        return false !== $r ? $data : false;
    }

    /*
     * 设置minipro表数据
     */
    public function ajax_setfield()
    {
        if (IS_POST) {
            $param = input('param.');
            $table  = input('post.table/s');
            $id_name  = input('post.id_name/s');
            $id_value = input('post.id_value/d');
            $field  = input('post.field/s'); // 修改哪个字段
            $value  = input('post.value/s'); // 修改字段值  
            $value    = eyPreventShell($value) ? $value : strip_sql($value);
            if (!empty($table)) {

                /*处理数据的安全性*/
                if (empty($id_value)) {
                    $this->error('查询条件id不合法！');
                }
                foreach ($param as $key => $val) {
                    if ('value' == $key) {
                        continue;
                    }
                    if (!preg_match('/^([A-Za-z0-9_-]*)$/i', $val)) {
                        $this->error('数据含有非法入侵字符！');
                    }
                }
                /*end*/

                $r = M($table)->where([
                        $id_name   => $id_value,
                        'lang'  => $this->admin_lang,
                    ])->cache(true, null, $table)
                    ->update([
                        $field => $value,
                        'update_time' => getTime(),
                    ]); // 根据条件保存修改的数据
                if (false !== $r) {
                    $this->success('操作成功');
                }
            }
        }
        $this->error('操作失败');
    }
}