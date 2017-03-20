function showCount(obj_id, count)
{
	var $display = $('#'+obj_id);
	var div_by = 100;
	var run_count = 1;
	var int_speed = 24;
	if(count < 10){
		div_by = 1;
	}else if(count < 100){
		div_by = 10;
	}
	var speed = Math.round(count / div_by);


	var int = setInterval(function() {
		if(run_count < div_by){
			$display.text(speed * run_count);
			run_count++;
		} else if(parseInt($display.text()) < count) {
			var curr_count = parseInt($display.text()) + 1;
			$display.text(curr_count);
		} else if(parseInt($display.text()) > count) {
			$display.text(count);
		} else {
			clearInterval(int);
		}
	}, int_speed);
}
