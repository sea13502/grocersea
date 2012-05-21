<?php
ini_set('display_errors',true);
session_start();

include_once( 'config.php' );
include_once( 'saetv2.ex.class.php' );

$b = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );

if( !empty($_POST['tags']) && !empty($_POST['id']) ){
	$result = $b->favorites_tags_update($_POST['id'],$_POST['tags']);
	if( empty($result['tags'][1]['tag']) ){
		echo $result['tags'][0]['tag'].','.' ';
		//echo empty($result['tags'][1]['tag']),$result['tags'][1]['tag'];
	}else{
		echo $result['tags'][0]['tag']. ',' . $result['tags'][1]['tag'] .' ';
	}
}
?>