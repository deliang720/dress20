<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:46:"./application/admin/template/index\welcome.htm";i:1586191213;s:67:"D:\project\eyoucmsDemo\application\admin\template\public\footer.htm";i:1571728724;}*/ ?>
<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<!-- Apple devices fullscreen -->
<meta name="apple-mobile-web-app-capable" content="yes">
<!-- Apple devices fullscreen -->
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<link href="/eyoucmsDemo/public/static/admin/css/main.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css">
<link href="/eyoucmsDemo/public/static/admin/font/css/font-awesome.min.css" rel="stylesheet" />
<link href="/eyoucmsDemo/public/static/admin/css/index.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css">
<!--[if IE 7]>
  <link rel="stylesheet" href="/eyoucmsDemo/public/static/admin/font/css/font-awesome-ie7.min.css">
<![endif]-->
<script type="text/javascript">
    var eyou_basefile = "<?php echo \think\Request::instance()->baseFile(); ?>";
    var module_name = "<?php echo MODULE_NAME; ?>";
    var __root_dir__ = "/eyoucmsDemo";
    var __lang__ = "<?php echo $admin_lang; ?>";
    var __main_lang__ = "<?php echo $main_lang; ?>";
</script>  
<script type="text/javascript" src="/eyoucmsDemo/public/static/admin/js/jquery.js"></script>
<script type="text/javascript" src="/eyoucmsDemo/public/plugins/layer-v3.1.0/layer.js"></script>
<script src="/eyoucmsDemo/public/static/admin/js/upgrade.js?v=<?php echo $version; ?>"></script>
<script src="/eyoucmsDemo/public/static/admin/js/global.js?v=<?php echo $version; ?>"></script>
<link href="/eyoucmsDemo/public/static/admin/css/diy_style.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css" />
</head>
<body style="background-color:#F4F4F4;padding:6px; overflow: auto;">
<div id="toolTipLayer" style="position: absolute; z-index: 9999; display: none; visibility: visible; left: 95px; top: 573px;"></div>
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<?php if(empty($system_explanation_welcome) || empty($system_explanation_welcome_2)): ?>
<div id="explanation_welcome" style="margin:10px 15px 0px 15px;">
    <?php if(empty($system_explanation_welcome) || (($system_explanation_welcome instanceof \think\Collection || $system_explanation_welcome instanceof \think\Paginator ) && $system_explanation_welcome->isEmpty())): ?>
    <div class="explanation" style="color: rgb(44, 188, 163); background-color: #fff!important; width: 99%; height: 100%;">
        <div class="title checkZoom" data-type="1">
            <span title="不再提示" style="display: block;"></span>
        </div>
        <ul>
            <li style="color: red;">系统检测当前版本尚未备份数据库，请点击<a href="<?php echo url('Tools/index'); ?>">【数据备份】</a>进入操作。</li>
        </ul>
    </div>
    <?php endif; if(empty($system_explanation_welcome_2)): ?>
    <div class="explanation" style="color: rgb(44, 188, 163); background-color: #fff!important; width: 99%; height: 100%; margin-top: 10px;">
        <div class="title checkZoom" data-type="2">
            <span title="不再提示" style="display: block;"></span>
        </div>
        <ul>
            <li style="color: red;">后台登录密码强度：<?php echo getPasswordLevelTitle($admin_login_pwdlevel); ?>，容易被暴力破解，请及时<a href="<?php echo url('Admin/admin_edit', ['id'=>\think\Session::get('admin_info.admin_id')]); ?>">【修改密码】</a>提高安全性。</li>
        </ul>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
<div class="warpper">
    <div class="content start_content">
        <div class="contentWarp">
            <div class="index_box">
                <div class="info_count">
                     <h3><i class="fa fa-bars"></i>快捷导航</h3>
                     <div class="container-fluid">
                         <ul>
                            <?php if(is_array($quickMenu) || $quickMenu instanceof \think\Collection || $quickMenu instanceof \think\Paginator): $i = 0; $__LIST__ = $quickMenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <li>
                               <a href="<?php echo url($vo['controller'].'/'.$vo['action'], $vo['vars']); ?>"><p class="navs"><?php echo $vo['title']; ?></p></a>
                            </li>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                            <li>
                               <a href="javascript:void(0);" id="quickMenuAdd"><p class="navs"><i style="font-size: 20px;" class="fa fa-plus"></i></p></a>
                            </li>
                         </ul>
                     </div>
                </div>
            </div>
            <div class="index_box" >
                <div class="info_count">
                     <h3><i class="fa fa-bar-chart"></i>内容统计</h3>
                     <div class="container-fluid">
                         <ul>
                            <?php if(is_array($contentTotal) || $contentTotal instanceof \think\Collection || $contentTotal instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($contentTotal) ? array_slice($contentTotal,0,9, true) : $contentTotal->slice(0,9, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <li>
                               <a href="<?php echo url($vo['controller'].'/'.$vo['action'], $vo['vars']); ?>">
                                   <h2><?php echo $vo['title']; ?></h2>
                                   <p><cite><?php echo (isset($vo['total']) && ($vo['total'] !== '')?$vo['total']:'0'); ?></cite></p>
                               </a>
                            </li>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                            <li>
                               <a href="javascript:void(0);" id="contentTotalAdd">
                                   <h2>添加统计</h2>
                                   <p><cite><i class="fa fa-plus"></i></cite></p>
                               </a>
                            </li>
                         </ul>
                     </div>
                </div>
            </div>
            <div class="section system_section" style="float: none;width: inherit;">
                <div class="system_section_con">
                    <div class="sc_title" style="padding: 26px 0 14px;border-bottom: 1px solid #e4eaec;">
                      
                        <h3><i class="fa fa-gear"></i>程序信息</h3>
                    </div>
                    <div class="sc_warp" id="system_warp" style="display: block;">
                        <table cellpadding="0" cellspacing="0" class="system_table">
                            <tbody>
                                <tr>
                                    <td class="gray_bg">系统更新：</td>
                                    <td id="td_upgrade_msg">
                                        <div id="upgrade_filelist" style="display:none;"></div> 
                                        <div id="upgrade_intro" style="display:none;"></div> 
                                        <div id="upgrade_notice" style="display:none;"></div> 
                                        <a href="javascript:void(0);" id="a_upgrade" data-version="" data-max_version="" data-iframe="workspace" title="" data-tips_url="<?php echo url('Upgrade/setPopupUpgrade'); ?>" data-upgrade_url="<?php echo url('Upgrade/OneKeyUpgrade'); ?>" data-check_authority="<?php echo url('Upgrade/check_authority'); ?>">正在版本检测中……</a>
                                    </td>
                                    <td class="gray_bg">当前版本：</td>
                                    <td><?php echo (isset($sys_info['curent_version']) && ($sys_info['curent_version'] !== '')?$sys_info['curent_version']:'v1.0'); ?></td>
                                </tr>
                                <tr>
                                    <td class="gray_bg">程序名称：</td>
                                    <td><?php echo (isset($sys_info['web_name']) && ($sys_info['web_name'] !== '')?$sys_info['web_name']:'Eyoucms企业网站管理系统'); ?></td>
                                    <td class="gray_bg">版权所有：</td>
                                    <td><?php if(!(empty($is_eyou_authortoken) || (($is_eyou_authortoken instanceof \think\Collection || $is_eyou_authortoken instanceof \think\Paginator ) && $is_eyou_authortoken->isEmpty()))): ?>盗版必究<?php else: ?>正版软件<?php endif; ?></td>
                                </tr>
                                <?php if(!(empty($is_eyou_authortoken) || (($is_eyou_authortoken instanceof \think\Collection || $is_eyou_authortoken instanceof \think\Paginator ) && $is_eyou_authortoken->isEmpty()))): ?>
                                <tr>
                                    <td class="gray_bg">更新日志：</td>
                                    <td><a href="http://www.eyoucms.com/rizhi/" target="_blank">查看</a></td>
                                    <td class="gray_bg">帮助中心:</td>
                                    <td><a href="http://www.eyoucms.com/ask/" target="_blank">查看</a></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="system_section_con">
                    <div class="sc_title" style="padding: 26px 0 14px;border-bottom: 1px solid #e4eaec;">
                        
                        <h3><i class="fa fa-tasks"></i>服务器信息</h3>
                    </div>
                    <div class="sc_warp" id="system_warp" style="display: block;padding-bottom: 20px;">
                        <table cellpadding="0" cellspacing="0" class="system_table">
                            <tbody><tr>
                                <td class="gray_bg">服务器操作系统：</td>
                                <td><?php echo $sys_info['os']; ?></td>
                                <td class="gray_bg">服务器域名/IP：</td>
                                <td><?php echo $sys_info['domain']; ?> [ <?php echo $sys_info['ip']; ?> ]</td>
                            </tr>
                            <tr>
                                <td class="gray_bg">服务器环境：</td>
                                <td><?php echo $sys_info['web_server']; ?></td>
                                <td class="gray_bg">PHP 版本：</td>
                                <td><?php echo $sys_info['phpv']; ?></td>
                            </tr>
                            <tr>
                                <td class="gray_bg">Mysql 版本：</td>
                                <td><?php echo $sys_info['mysql_version']; ?></td>
                                <td class="gray_bg">GD 版本：</td>
                                <td><?php echo $sys_info['gdinfo']; ?></td>
                            </tr>
                            <tr>
                                <td class="gray_bg">文件上传限制：</td>
                                <td><?php echo $sys_info['fileupload']; ?></td>
                                <td class="gray_bg">最大占用内存：</td>
                                <td><?php echo $sys_info['memory_limit']; ?></td>
                            </tr>
                            <tr>
                                <td class="gray_bg">POST限制：</td>
                                <td><?php echo (isset($sys_info['postsize']) && ($sys_info['postsize'] !== '')?$sys_info['postsize']:'unknown'); ?></td>
                                <td class="gray_bg">最大执行时间：</td>
                                <td><?php echo $sys_info['max_ex_time']; ?></td>
                            </tr>
                            <tr>
                                <td class="gray_bg">Zip支持：</td>
                                <td><?php echo $sys_info['zip']; ?></td>
                                <td class="gray_bg">Zlib支持：</td>
                                <td><?php echo $sys_info['zlib']; ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="footer" style="position: static; bottom: 0px; font-size:14px;">
    <p>
        <b><?php echo htmlspecialchars_decode($global['web_copyright']); ?></b>
    </p>
</div>
<script type="text/javascript">
    $(function () {
        check_upgrade_version();
        $.get("<?php echo url('Ajax/welcome_handle', ['_ajax'=>1]); ?>"); // 进入欢迎页面需要异步处理的业务
        check_language_tips();

        // 检测语言版本
        function check_language_tips()
        {
            if (__main_lang__ != __lang__) {
                var language_title = $('#language_title', window.parent.document).html();
                layer.msg('当前后台已切换至【'+language_title+'】编辑状态！', {time:3000});
            }
        }

        // 版本检测更新弹窗
        function check_upgrade_version(){
            $.ajax({
                type : "GET",
                url  : "<?php echo url('Ajax/check_upgrade_version', ['_ajax'=>1]); ?>",
                data : {},
                dataType : "JSON",
                success: function(res) {
                    if (1 == res.code) {
                        if (2 == res.data.code) {

                            /*显示顶部导航更新提示*/
                            try{
                                $("#upgrade_filelist", window.parent.document).html(res.data.msg.upgrade);    
                                $("#upgrade_intro", window.parent.document).html(res.data.msg.intro);
                                $("#upgrade_notice", window.parent.document).html(res.data.msg.notice);
                                $('#a_upgrade', window.parent.document).attr('data-version',res.data.msg.key_num)
                                    .attr('data-max_version',res.data.msg.max_version)
                                    .show();
                            }catch(e){}
                            /*--end*/
                            
                            $('#upgrade_filelist').html(res.data.msg.upgrade);
                            $('#upgrade_intro').html(res.data.msg.intro);
                            $('#upgrade_notice').html(res.data.msg.notice);
                            $('#a_upgrade').attr('data-version', res.data.msg.key_num)
                            .attr('data-max_version', res.data.msg.max_version)
                            .attr('title', res.data.msg.tips);
                            $('#a_upgrade').html('检测到新版本'+res.data.msg.key_num+'[点击查看]').css('color', '#F00');

                            <?php if(-1 != $web_show_popup_upgrade AND (0 >= \think\Session::get('admin_info.role_id') OR 1 == \think\Session::get('admin_info.auth_role_info.online_update'))): ?>
                                btn_upgrade($("#a_upgrade"), 1);
                            <?php endif; ?>
                        } else {
                            $('#td_upgrade_msg').html(res.data.msg);
                        }
                    }
                }
            }); 
        }
    });

    $(function() {
        //操作提示缩放动画
        $(".checkZoom").click(function(){
            $(this).parent().animate({
                color: "#FFF",
                backgroundColor: "#4FD6BE",             
                width: "0",
                height: "0",                             
            },300,function(){
                $(this).remove();
            });
            if(1 >= $('#explanation_welcome').find('div.explanation').length) {
                $('#explanation_welcome').remove();
            }
            var url = eyou_basefile+"?m=admin&c=Ajax&a=explanation_welcome&type="+$(this).attr('data-type')+"&lang="+__lang__+"&_ajax=1";
            $.get(url);
        });

        checkInlet(); // 自动检测隐藏index.php
    });

    // 自动检测隐藏index.php
    function checkInlet() {
        layer.open({
            type: 2,
            title: false,
            area: ['0px', '0px'],
            shade: 0.0,
            closeBtn: 0,
            shadeClose: true,
            content: '//<?php echo \think\Request::instance()->host(); ?>/eyoucmsDemo/api/Rewrite/setInlet.html',
            success: function(layero, index){
                layer.close(index);
                var body = layer.getChildFrame('body', index);
                var content = body.html();
                if (content.indexOf("Congratulations on passing") == -1)
                {
                    $.ajax({
                        type : "POST",
                        url  : "/eyoucmsDemo/index.php?m=api&c=Rewrite&a=setInlet&_ajax=1",
                        data : {seo_inlet:0},
                        dataType : "JSON",
                        success: function(res) {

                        }
                    }); 
                }
            }
        });
    }

    // 新增内容统计
    $('#contentTotalAdd').click(function(){
        //iframe窗
        var iframes = layer.open({
            type: 2,
            title: '内容统计管理',
            fixed: true, //不固定
            shadeClose: false,
            shade: 0.3,
            // maxmin: false, //开启最大化最小化按钮
            area: ['550px', '220px'],
            content: "<?php echo url('Index/ajax_content_total'); ?>"
        });
    });

    // 新增快捷导航
    $('#quickMenuAdd').click(function(){
        //iframe窗
        var iframes = layer.open({
            type: 2,
            title: '快捷导航管理',
            fixed: true, //不固定
            shadeClose: false,
            shade: 0.3,
            // maxmin: false, //开启最大化最小化按钮
            area: ['550px', '300px'],
            content: "<?php echo url('Index/ajax_quickmenu'); ?>"
        });
    });
</script>
<br/>
<div id="goTop">
    <a href="JavaScript:void(0);" id="btntop">
        <i class="fa fa-angle-up"></i>
    </a>
    <a href="JavaScript:void(0);" id="btnbottom">
        <i class="fa fa-angle-down"></i>
    </a>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#think_page_trace_open').css('z-index', 99999);
    });
</script>
</body>
</html>