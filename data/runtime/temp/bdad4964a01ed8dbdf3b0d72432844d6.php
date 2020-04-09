<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:31:"./template/pc/lists_product.htm";i:1586454295;s:45:"D:\project\eyoucmsDemo\template\pc\header.htm";i:1586452944;s:45:"D:\project\eyoucmsDemo\template\pc\banner.htm";i:1586445808;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <title><?php  $tagGlobal = new \think\template\taglib\eyou\TagGlobal; $__VALUE__ = $tagGlobal->getGlobal("web_title"); echo $__VALUE__; ?></title>
    <?php  $tagStatic = new \think\template\taglib\eyou\TagStatic; $__VALUE__ = $tagStatic->getStatic("skin/aites/css/css.css","","",""); echo $__VALUE__;  $tagStatic = new \think\template\taglib\eyou\TagStatic; $__VALUE__ = $tagStatic->getStatic("skin/aites/css/common.css","","",""); echo $__VALUE__;  $tagStatic = new \think\template\taglib\eyou\TagStatic; $__VALUE__ = $tagStatic->getStatic("skin/aites/css/swiper.min.css","","",""); echo $__VALUE__;  $tagStatic = new \think\template\taglib\eyou\TagStatic; $__VALUE__ = $tagStatic->getStatic("skin/aites/js/jquery-1.9.0.min.js","","",""); echo $__VALUE__;  $tagStatic = new \think\template\taglib\eyou\TagStatic; $__VALUE__ = $tagStatic->getStatic("skin/aites/js/modernizr.custom.js","","",""); echo $__VALUE__;  $tagStatic = new \think\template\taglib\eyou\TagStatic; $__VALUE__ = $tagStatic->getStatic("skin/aites/js/ejmenu.js","","",""); echo $__VALUE__;  $tagStatic = new \think\template\taglib\eyou\TagStatic; $__VALUE__ = $tagStatic->getStatic("skin/aites/js/jquery.dlmenu.js","","",""); echo $__VALUE__; ?>

    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?d093227a4a5d83266b69c61beb01821c";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
</head>

<body>

<div class="top">
    <div class="middle">
        <div class="logo"><h1><a href="index.html" title=""><img src="images/logo.png" alt="" /></a></h1></div>
        <div class="nav">

            <a href="index.php" title="" class="on">网站首页</a>
            <a href="产品展示.html" title="">产品展示</a>
            <a href="案例展示.html" title="">案例展示</a>
            <a href="新闻中心.html" title="">新闻中心</a>
            <a href="关于我们.html" title="">关于我们</a>
            <a href="联系我们.html" title="">联系我们</a>
        </div>
    </div>
</div>
<div class="banner_case"></div>

<div class="case_center">
    <div class="middle">
        <h2>案例展示</h2>
        <h4>——</h4>
        <h3>ATTES DESIGN AGENCY</h3>
        <div class="product_case"><?php var_dump($channelartlist); $__VALUE__ = isset($channelartlist["id"]) ? $channelartlist["id"] : "变量名不存在"; echo $__VALUE__;  $tagScreening = new \think\template\taglib\eyou\TagScreening; $_result = $tagScreening->getScreening("on", "", "", "不限", "");if(!empty($_result["list"]) || (($_result["list"] instanceof \think\Collection || $_result["list"] instanceof \think\Paginator ) && $_result["list"]->isEmpty())): $field = $_result; ?>'}
            <?php if(is_array($field['list']) || $field['list'] instanceof \think\Collection || $field['list'] instanceof \think\Paginator): $i = 0; $e = 1; $__LIST__ = $field['list'];if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("");else: foreach($__LIST__ as $key=>$vo): $i= intval($key) + 1;$mod = ($i % 2 ); ?>
            <div class="product_li">
                <span><?php echo $vo['title']; ?>：</span>
                <div class="fenlei">
                    <?php if(is_array($vo['dfvalue']) || $vo['dfvalue'] instanceof \think\Collection || $vo['dfvalue'] instanceof \think\Paginator): $i = 0; $e = 1; $__LIST__ = $vo['dfvalue'];if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("");else: foreach($__LIST__ as $key=>$val): $i= intval($key) + 1;$mod = ($i % 2 ); ?>
                    <a <?php echo $val['onClick']; ?> class="<?php echo $val['currentstyle']; ?>"><?php echo $val['name']; ?></a>
                    <?php echo isset($val["ey_1563185380"])?$val["ey_1563185380"]:""; ?><?php echo (1 == $e && isset($val["ey_1563185376"]))?$val["ey_1563185376"]:""; ++$e; endforeach; endif; else: echo htmlspecialchars_decode("");endif; $val = []; ?>
                </div>
            </div>
            <?php echo isset($vo["ey_1563185380"])?$vo["ey_1563185380"]:""; ?><?php echo (1 == $e && isset($vo["ey_1563185376"]))?$vo["ey_1563185376"]:""; ++$e; endforeach; endif; else: echo htmlspecialchars_decode("");endif; $vo = []; ?>
            <?php echo $field['hidden']; endif; $field = []; ?>
        </div>
        <div class="case_content">
            <?php  $typeid = "";  if(empty($typeid) && isset($channelartlist["id"]) && !empty($channelartlist["id"])) : $typeid = intval($channelartlist["id"]); endif;  $param = array(      "typeid"=> $typeid,      "notypeid"=> "",      "flag"=> "",      "noflag"=> "",      "channel"=> "", ); $tagList = new \think\template\taglib\eyou\TagList; $_result_tmp = $tagList->getList($param, 12, "", "", "desc", "on","off");if(is_array($_result_tmp) || $_result_tmp instanceof \think\Collection || $_result_tmp instanceof \think\Paginator): $i = 0; $e = 1; $__LIST__ = $_result = $_result_tmp["list"]; $__PAGES__ = $_result_tmp["pages"];if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("");else: foreach($__LIST__ as $key=>$field): $aid = $field["aid"];$field["title"] = text_msubstr($field["title"], 0, 20, false);$field["seo_description"] = text_msubstr($field["seo_description"], 0, 160, true);$i= intval($key) + 1;$mod = ($i % 2 ); ?>

            <div class="view view-tenth">
                <a href="<?php echo $field['arcurl']; ?>" title=""><img src="<?php echo $field['litpic']; ?>" /></a>
                <div class="xinxi"><?php echo $field['title']; ?></div>
                <div class="mask">
                    <a href="<?php echo $field['arcurl']; ?>" title="">
                        <h2><?php echo $field['title']; ?></h2>
                        <p>面积：500㎡</p>
                    </a>
                </div>
            </div>
            <?php ++$e; $aid = 0; endforeach; endif; else: echo htmlspecialchars_decode("");endif; $field = []; ?>

        </div>
        <div class="paging">  <?php  $__PAGES__ = isset($__PAGES__) ? $__PAGES__ : ""; $tagPagelist = new \think\template\taglib\eyou\TagPagelist; $__VALUE__ = $tagPagelist->getPagelist($__PAGES__, "index,pre,pageno,next,end", "2"); echo $__VALUE__; ?> <a href="#" title="">上一页</a> <a href="#" title="" class="on">1</a> <a href="#" title="">2</a> <a href="#" title="">3</a> <a href="#" title="">上一页</a> </div>
    </div>
</div>
<div class="footer">
    <div class="middle">
        <div class="footer_center"> <a href="#" title="">网站首页</a> <a href="#" title="">产品展示</a> <a href="#" title="">案例展示</a> <a href="#" title="">新闻中心</a> <a href="#" title="">关于我们</a> <a href="#" title="">联系我们</a> </div>
        <p>版权所有：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;备案号：xxxxxxxx-1&nbsp;&nbsp;&nbsp;&nbsp;技术支持：大橙子北京信息科技有限公司</p>
        <p>全国服务热线：xxxxxxxxxxx&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;传真：xxxxxxxxxxxx&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;网址：xxxxxxxxxx</p>
        <p>地址：xxxxxxxxxxx</p>
        <p><img src="images/img_19.jpg" alt="" /><img src="images/img_21.jpg" alt="" /></p>
    </div>
</div>
<script src="js/swiper.min.js"></script>
<script>
    var swiper = new Swiper('.swiper-container', {
        slidesPerView: 1,
        loop: true,
        autoplay:true,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
</script>
<script type="text/javascript">
    $(function(){
        $( '#dl-menu' ).dlmenu();
    });
</script>
</body>
</html>
