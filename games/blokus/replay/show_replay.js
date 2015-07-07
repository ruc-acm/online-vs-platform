var id = 0;
var name1 = "Attacker"
var name2 = "Defender";
var step = 0;
var gotJson;
var shape = '[ [{"num":1},{"x":0,"y":0}],[{"num":2},{"x":0,"y":0},{"x":0,"y":1}],[{"num":3},{"x":0,"y":0},{"x":0,"y":1},{"x":1,"y":1}],[{"num":3},{"x":0,"y":0},{"x":0,"y":1},{"x":0,"y":2}],[{"num":4},{"x":0,"y":0},{"x":0,"y":1},{"x":1,"y":0},{"x":1,"y":1}],[{"num":4},{"x":0,"y":0},{"x":1,"y":0},{"x":1,"y":1},{"x":1,"y":-1}],[{"num":4},{"x":0,"y":0},{"x":0,"y":1},{"x":0,"y":2},{"x":0,"y":3}],[{"num":4},{"x":0,"y":0},{"x":1,"y":0},{"x":1,"y":-1},{"x":1,"y":-2}],[{"num":4},{"x":0,"y":0},{"x":0,"y":1},{"x":1,"y":0},{"x":1,"y":-1}],[{"num":5},{"x":0,"y":0},{"x":1,"y":0},{"x":1,"y":1},{"x":1,"y":2},{"x":1,"y":3}],[{"num":5},{"x":0,"y":0},{"x":1,"y":0},{"x":2,"y":0},{"x":2,"y":1},{"x":2,"y":-1}],[{"num":5},{"x":0,"y":0},{"x":1,"y":0},{"x":2,"y":0},{"x":2,"y":1},{"x":2,"y":2}],[{"num":5},{"x":0,"y":0},{"x":0,"y":1},{"x":0,"y":2},{"x":1,"y":0},{"x":1,"y":-1}],[{"num":5},{"x":0,"y":0},{"x":1,"y":0},{"x":1,"y":-1},{"x":1,"y":-2},{"x":2,"y":-2}],[{"num":5},{"x":0,"y":0},{"x":1,"y":0},{"x":2,"y":0},{"x":3,"y":0},{"x":4,"y":0}],[{"num":5},{"x":0,"y":0},{"x":1,"y":0},{"x":1,"y":1},{"x":2,"y":0},{"x":2,"y":1}],[{"num":5},{"x":0,"y":0},{"x":0,"y":1},{"x":1,"y":0},{"x":1,"y":-1},{"x":2,"y":-1}],[{"num":5},{"x":0,"y":0},{"x":0,"y":1},{"x":1,"y":0},{"x":2,"y":0},{"x":2,"y":1}],[{"num":5},{"x":0,"y":0},{"x":0,"y":1},{"x":1,"y":0},{"x":1,"y":-1},{"x":2,"y":0}],[{"num":5},{"x":0,"y":0},{"x":1,"y":0},{"x":1,"y":1},{"x":1,"y":-1},{"x":2,"y":0}],[{"num":5},{"x":0,"y":0},{"x":1,"y":0},{"x":1,"y":-1},{"x":1,"y":1},{"x":1,"y":2}] ]';
var shapeJson;
var score1 = 0;
var score2 = 0;

var target = $('#replay-container');

function getUrlParam(name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); 
            var r = window.location.search.substr(1).match(reg);  
            if (r != null) return unescape(r[2]); return null; 
}

function push_alert(str){
	target.before("<div class=\"alert alert-warning alert-dismissable\">" + "</div>");
	$(".alert").last().append("<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>" + str);
}

function freshStep(){
	if (step == 0)
	$(".state").html("ID = 0 x= 0 y= 0 block_id= 0 l= 0 v= 0 d= 0");
	else
	$(".state").html("ID= " + gotJson[step].color + " " + 
					 "x= " + gotJson[step].x + " " + 
					 "y= " + gotJson[step].y + " " + 
					 "block_id= " + gotJson[step].chess + " " + 
					 "l= " + gotJson[step].flipX + " " + 
					 "v= " + gotJson[step].flipY + " " + 
					 "d= " + gotJson[step].rotate + " "
				    );
}

function freshScore(){
	$(".state1").html(name1 + " Score " + score1);
	$(".state2").html(name2 + " Score " + score2);
}

function init_board(){
	target.append("<div class=\"board\"></div>");
	for (var i = 1;i <= 20;++i)
	{
		$(".board").append("<div class=\"row\" id=\"r" + i + "\"></div>");
		for (var j = 1;j <= 20;++j)
		$("#r" + i).append("<div class=\"block\" id=\"r" + i  + "c" + j + "\"></div>");
	}
	$(".board").append("<div class=\"state\"></div>");
	$(".board").append("<div class=\"state1\"></div>");
	$(".board").append("<div class=\"state2\"></div>");
	$(".board").css({
				"height":"484px",
				"width":"440px"
			});
	$(".row").each(function(){
			$(this).css({
				"height":"22px",
				"width":"440px",
				"float":"left"
			});
	});
	$(".block").each(function(){
			$(this).css({
				"height":"20px",
				"width":"20px",
				"float":"left",
				"border":"1px solid black"
			});
	});
	$(".state").css({
			"height":"20px",
			"width":"438px",
			"float":"left",
			"border":"1px dashed black"
	});
	$(".state1").css({
			"height":"20px",
			"width":"218px",
			"float":"left",
			"border":"1px dashed black"
	});
	$(".state2").css({
			"height":"20px",
			"width":"218",
			"float":"left",
			"border":"1px dashed black"
	});
	target.append("<button class=\"btn btn-primary \" onclick=\"pre()\">pre</button>");
	target.append("<button class=\"btn btn-primary \" onclick=\"next()\">next</button>");
	freshStep();
	freshScore();
}

function changeColor(x,y,c){
	$("#" + "r" + y + "c" + x).css("backgroundColor",c);
}

function getColor(c){
	switch(c){
		case 1 : return "blue";
		case 2 : return "green";
		case 3 : return "red";
		case 4 : return "yellow";
	}
	return "white";
}

function pre(){
	if (step == 0) return;
	if (step > gotJson.length - 1){
		step--;
		freshStep();
		return;
	}
	var stepJson = gotJson[step];
	step--;
	freshStep();
	if (stepJson.chess == 0) return;
	var shape = shapeJson[stepJson.chess - 1];
	var x0 = stepJson.x;
	var y0 = stepJson.y;
	if (stepJson.color == 1 || stepJson.color == 3)
		score1 -= shape[0].num;
	else
		score2 -= shape[0].num;
	for (var i = 1;i <= shape[0].num;++i)
	{
		var x = shape[i].x;
		var y = shape[i].y;
		var rotate = stepJson.rotate;
		if (stepJson.flipX) x = -x;
		if (stepJson.flipY) y = -y;
		switch(rotate){
			case 0 : break;
			case 1 : tmp = x;
					 x = y;
					 y = tmp;
					 y = -y;
					 break;
			case 2 : x = -x;
					 y = -y;
					 break;
			case 3 : tmp = x;
					 x = y;
					 y = tmp;
					 x = -x;
					 break;
			default : break;
		}
		changeColor(x0 + x,y0 + y,"white");
	}
	freshScore();
}

function next(){
	if (step == gotJson.length - 1) return;
	step++;
	freshStep();
	var stepJson = gotJson[step];
	if (stepJson.chess == 0) return;
	var shape = shapeJson[stepJson.chess - 1];
	var x0 = stepJson.x;
	var y0 = stepJson.y;
	if (stepJson.color == 1 || stepJson.color == 3)
		score1 += shape[0].num;
	else
		score2 += shape[0].num;
	for (var i = 1;i <= shape[0].num;++i)
	{
		var x = shape[i].x;
		var y = shape[i].y;
		var rotate = stepJson.rotate;
		if (stepJson.flipX) x = -x;
		if (stepJson.flipY) y = -y;
		switch(rotate){
			case 0 : break;
			case 1 : tmp = x;
					 x = y;
					 y = tmp;
					 y = -y;
					 break;
			case 2 : x = -x;
					 y = -y;
					 break;
			case 3 : tmp = x;
					 x = y;
					 y = tmp;
					 x = -x;
					 break;
			default : break;
		}
		changeColor(x0 + x,y0 + y,getColor(stepJson.color));
	}	
	freshScore();
}

$(document).ready(function(){
	var id = getUrlParam('id');
	
	$.ajax({
				url:"http://ovs.indeed.moe/index.php?r=competition%2Freplay&id=" + id + "&json=1",
				type:"get",
				timeout:5000, 
				success:function(data){				 
				    	gotJson = data;
					name1 = gotJson[0].name[0];
					name2 = gotJson[0].name[1];
					shapeJson = eval('(' + shape + ')');
					init_board();
				},
				error:function(data){
					console.log(data);
					push_alert("fail to get the response");
				}
		});
});

