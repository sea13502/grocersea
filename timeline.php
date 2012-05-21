<?php
ini_set('display_errors',true);
session_start();

if( isset($_GET['logout']) ){//登出用
	header('location:index.php');
	session_destroy();
}

include_once( 'config.php' );
include_once( 'saetv2.ex.class.php' );
//include_once( 'function.php' );

$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
//翻页代码
if( isset($_GET['previous']) && $_SESSION['page'] > 1 ){
	$_SESSION['page'] -= 1;
}else if( isset($_GET['next']) ){
	if( $_SESSION['page_count'] == $_SESSION['page'] ){
		$_SESSION['page'] = $_SESSION['page_count'];
	}else{
		$_SESSION['page'] += 1;
	}
}else if( isset($_GET['first']) ){
	$_SESSION['page'] = 1;
}else if( isset($_GET['page']) ){
	$_SESSION['page'] = $_GET['page'];
}

if( isset( $_GET['tag'] ) ){
	$_SESSION['tag'] = $_GET['tag'];
	//echo $_GET['tag'];
}else if( isset($_GET['toall']) ){
	$_SESSION['page'] = 1;
	unset( $_SESSION['tag'] );
}

if( isset($_GET['toall']) || !isset($_SESSION['tag']) ){
	if( !isset($_SESSION['page']) ){//call all feeds
		$_SESSION['page'] = 1;
		$ms = $c->home_timeline();
	}else{
		$ms = $c->home_timeline($_SESSION['page']);
	}
}

$uid_get = $c->get_uid();
$uid = $uid_get['uid'];
$user_message = $c->show_user_by_id( $uid);//根据ID获取用户等基本信息
$weibo_tags = $c->favorites_tags();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>weiboManager</title>
<link href="managerstyle.css" rel="stylesheet"/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script><!--googlejqueryCDN-->
</head>

<body onload="start();">
							<div id="main_container"><!--整个大的容器-->
<div id="send_weibo"><!--上面的-->
	<?=$user_message['screen_name']?>,您好！
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>?logout" method="post">
    	<input type="submit" value="登出"/>
    </form> 
	<h2 align="left">发送新微博</h2>
	<form action="" >
		<input type="text" name="text" style="width:300px" />
		<input type="submit" value="提交"/>
	</form>
<?php
if( isset($_REQUEST['text']) ) {
	$ret = $c->update( $_REQUEST['text'] );	//发送微博
	if ( isset($ret['error_code']) && $ret['error_code'] > 0 ) {
		echo "<p>发送失败，错误：{$ret['error_code']}:{$ret['error']}</p>";
	} else {
		echo "<p>发送成功</p>";
	}
}
?>

<?php 
    if( is_array( $ms['statuses'] ) ):
    //if( is_array( $ms['favorites'] ) ): 
    //$text = implode(",", $ms['favorites']);  
    //echo $ms['favorites'];
    //$t=print_r($ms,true);
    //echo $t; 
	//echo $ms['total_number'];
?>
</div><!--上面的-->
<div id="weibo_container"><!--装weibo的容器-->
	<?php 
        //foreach( $ms['statuses'] as $item ):
        foreach( $ms['statuses'] as $item ): 
    ?>
    <dl>
    	<dt class="face">
        	<a>
              <img src="<?php echo $item["user"]["profile_image_url"] //显示头像?>"/>
            </a>
        </dt>
        <dd class="content">    
            <p><a target="_blank" href="http://weibo.com/<?php echo $item["user"]["domain"] ?>"><?php echo $item["user"]["screen_name"] ?></a><em><?=$item['text'];?></em></p>
            
            <?php if( !empty($item['thumbnail_pic']) ){ ?>
            <div><!--图片容器-->
					<img class="bigcursor" src="<?php echo $item['thumbnail_pic'] ?>">
					<img style="left: 39.5px; top: 52px; display: none;" src="http://img.t.sinajs.cn/t4/style/images/common/loading.gif" class="loading_gif">
			</div><!--图片容器-->
            <?php } ?>
            
            <?php if( isset($item['retweeted_status']['text']) ){ ?>
            <dl><!--如果是转发微博显示原始微博-->
            	<p><a target="_blank" href="http://weibo.com/<?php echo $item['retweeted_status']["user"]["domain"] ?>"><?php echo $item['retweeted_status']["user"]["screen_name"] ?></a><em><?=$item['retweeted_status']['text'];?></em></p>
                
				 <?php if( !empty($item['retweeted_status']['thumbnail_pic']) ){ ?>
                <div><!--图片容器-->
                        <img class="bigcursor" src="<?php echo $item['retweeted_status']['thumbnail_pic'] ?>">
                        <img style="left: 39.5px; top: 52px; display: none;" src="http://img.t.sinajs.cn/t4/style/images/common/loading.gif" class="loading_gif">
                </div><!--图片容器-->
                <?php } ?>
                
            </dl><!--如果是转发微博显示原始微博-->
            <?php } ?>
        
        </dd>
        <a style="float:left" target="_blank" href="http://api.t.sina.com.cn/<?= $item["user"]['id'] ?>/statuses/<?= $item['id'] ?>">访问原微博</a>
    </dl>
    <?php endforeach; ?>
</div><!--装weibo的容器-->
<?php endif; ?>
<div><!--翻页-->
    <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>?first">首页</a>
    <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>?previous">上一页</a>
    <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>?next">下一页</a>  
</div><!--翻页-->
												</div><!--整个大的容器-->                                                
</body>
</html>
