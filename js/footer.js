          let tick_counter = setInterval(function() {
                let obj_tick_left = document.getElementById('tick-left');
                let obj_ep_status = document.getElementById('ep-status');
                let obj_ep_value  = <?php echo $character['ep']; ?>;
                let obj_ep_max    = <?php echo $character['max_ep']; ?>;
                
                let obj_ep_icon   = document.getElementById('ep-icon');
                
                let tick          = new Date().getTime();
                let out_string    = new String();
                
                let cur_energy    = parseInt(obj_ep_value.innerHTML);
                let max_energy    = parseInt(obj_ep_max.innerHTML);
                let percent_full  = Math.ceil(cur_energy / max_energy * 100);

                let icon          = 'bi bi-battery-full';
                let txt_color     = 'text-success';
                

                tick = (60 - Math.ceil(tick/1000) % 60) - 1;
                
                if (tick < 10) {
                    out_string = '0:0' + tick.toString();
                } else if (tick == 60) {
                    out_string = '1:00';
                } else {
                    out_string = '0:' + tick.toString();
                }

                obj_tick_left.innerHTML = out_string;

                if (cur_energy >= max_energy) {
                    obj_ep_value.innerHTML = Math.random(max_energy - 1);
                }

                if (percent_full > 0 && percent_full < 49) {
                    icon = 'bi bi-battery';
                    txt_color = 'text-danger';
                } else if (percent_full > 49 && percent_full < 75) {
                    icon = 'bi bi-battery-half';
                    txt_color = 'text-warning';
                } else if (percent_full > 75) {
                    icon = 'bi bi-battery-full';
                    txt_color = 'text-success';
                }
                
                if (!obj_ep_status.classList.contains(/text-/)) {
                    obj_ep_icon.innerHTML = '<i class="' + icon + '"></i>';
                    obj_ep_status.classList.add(txt_color);
                }

                if (tick < 1) {
                    obj_ep_icon.innerHTML = '<i class="' + icon + '"></i>';
                    obj_ep_value.innerHTML = cur_energy + 1;
                    obj_ep_value.classList = txt_color;
                    obj_ep_icon.classList = txt_color;
                }
            }, 1000);