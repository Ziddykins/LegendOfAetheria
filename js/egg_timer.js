function init_egg_timer(end, selector) {
	let hatch_time = new Date(end).getTime();
	
	let x = setInterval(function() {
		let now      = new Date().getTime();
		let time_left = hatch_time - now;
		let days     = Math.floor(time_left / (1000 * 60 * 60 * 24));
		let hours    = Math.floor((time_left % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		let minutes  = Math.floor((time_left % (1000 * 60 * 60)) / (1000 * 60));
		let seconds  = Math.floor((time_left % (1000 * 60)) / 1000);
		
		document.getElementById(selector).innerHTML = days + " " + hours + ":" + minutes + ":" + seconds;

		if (time_left <= 0) {
			clearInterval(x);
			document.getElementById(selector).classList.remove('disabled');
			document.getElementById(selector).classList.remove('btn-warning');
			document.getElementById(selector).innerHTML = '<button id="hatch_egg" name="hatch_egg" class="btn btn-primary">Hatch!</button>';
		}
	}, 1000);
}
