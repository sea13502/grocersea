function start(){
	window.setInterval("change_margin()",60);
}
var oldmargin = 0;//浮动层需要用到的参数
var is_have_focused = false;//判断标签修改框的是否在焦点上
var public_array = [];//暂存的array
var public_jqob = $();
function change_margin(){
	var distance = Number(document.documentElement.scrollTop + document.body.scrollTop);
	var tag_container = document.getElementById("tag_container");
	var movingscale = (distance - oldmargin)/2;
	var movingdistance;
	if( movingscale > 0 || movingscale == 0 ){
		movingdistance = distance - movingscale;
	}else{
		movingdistance = oldmargin + movingscale;
	}
	tag_container.style.marginTop = movingdistance + "px";
	oldmargin = movingdistance;
}
function addTag(e){
	$(e).siblings('.have_tag').css('display','none');
	$(e).prev().css("display","inline");
	$(e).prev().children().eq(0).focus();
	$(e).css("display","none");
}
function cancelAddTag(e){
	$(e).parent().siblings('.have_tag').css('display','inline');
	$(e).parent().css("display","none");
	//$('#addtagbtn').css("display","inline");
	$(e).parent().next().css("display","inline");
	is_have_focused = false;
}
function changeTag(e){
	$(e).css('display','none');
	$(e).prev().css("display","inline");
	$(e).prev().children().eq(0).focus();
	var n = $(e).siblings('a').size();
	var str = '';
	public_array = [];
	for(var i=0;i<n;i++){
		pause = $(e).siblings('a').eq(i).html();
		if( pause != '' ){
			public_array.push(pause);
			str += pause + ' ';
		}
		$(e).siblings().eq(i).css('display','none');
	}
	$(e).prev().children().eq(0).val(str);
}
function cancelChangeTag(e){
	$(e).parent().css("display","none");
	$(e).siblings().eq(0).val('');
	var n = $(e).parent().siblings('a').size();
	for(var i=0;i<n;i++){
		$(e).parent().siblings().eq(i).css('display','inline');
	}
	$('#changbtn').css('display','inline');
	is_have_focused = false;
}
function changeTagOnFocus(e){
	public_jqob = $(e);
	is_have_focused = true;
}
function transmitTag(e){
	if(is_have_focused){
		//alert(e.innerHTML);
		var str = public_jqob.val();
		str += e.innerHTML;
		arr = str.split(' ');
		if( arr.length > 2 ){
			arr.splice(0,1);
		}
		public_array = arr;
		str = arr.join(' ');
		str += ' ';
		public_jqob.val(str);
		return false;
	}else{
		return true;
	}
}
function sendTagData(e){
	var fid = e.id;
	var str = public_array.join(',');
	$.ajax({
		type:'POST',
		beforeSend:function(){
						e.disabled = true;
						$(e).prev().get(0).disabled = true;
						$(e).next().get(0).disabled = true;
					},
		url:'changetag.php',
		dataType:'text',
		data:'id=' + fid + '&tags=' + str,
		success:function(result){
						e.disabled = false;
						$(e).prev().val('');
						$(e).prev().get(0).disabled = false;
						$(e).next().get(0).disabled = false;
						$(e).parent().css('display','none');
						//var n = $(e).parent().siblings('.have_tag').size();
						var arr = result.split(',');
						//var n = arr.length;
						if( arr[1] == ' ' ){
							n = 1;
						}else{
							n = 2;
						}
						for(var i=0;i<n;i++){
							$(e).parent().siblings().eq(i).html(arr[i]);
							$(e).parent().siblings().eq(i).css('display','inline');
						}
						$(e).parent().siblings().not('.have_tag').css('display','inline');
						is_have_focused = false;
				}	
	});
}
//一个是修改，加标签文字的替换，一个实提交一个标签后，修改加标签的文字消失。无法删除标签的问题
//修改标签 删除标签 解除收藏