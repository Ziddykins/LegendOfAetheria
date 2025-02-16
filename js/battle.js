document.querySelectorAll("ul[id$='drop-menu']").forEach((menu) => {
    let which = menu.id.split("-")[0];
    let short = which == 'attack' ? 'atk' : 'spl';

    document.getElementById(`${which}-drop-menu`).querySelectorAll("li").forEach((li) => {
	    li.addEventListener("click", (e) => {
		    document.getElementById(`hunt-${which}-btn`).textContent = e.target.textContent;
            document.getElementById(`hunt-${which}-btn`).value = e.target.attributes.getNamedItem(`data-loa-${short}`);
	    });
    });
});

document.querySelectorAll("button[id^='hunt']").forEach((btn) => {
    let which = btn.id.split("-")[1];

    if (which == 'new') {
        return;
    }

    document.getElementById(`hunt-${which}-btn`).addEventListener("click", async (e) => {
        let atk_type = document.getElementById(`hunt-${which}-btn`).textContent;
        let battle_log = document.getElementById("battle-log");

        fetch('/battle', {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'text/plain'
            },
            method: 'POST',
            body: `action=${which}&type=${atk_type}&csrf-token=${csrf_token}`}
        ).then((response) => {
            if (!response.ok) {
                return response.text().then((data) => {
                    throw new Error(data);
                });
            } else {
                return response.text();
            }
        }).then((data) => {
            battle_log.innerHTML = battle_log.innerHTML + `${data}`;
            document.getElementById('battle-log').textContent = player_hp;
        }).catch((error) => {
            battle_log.innerHTML = battle_log.innerHTML + `${error.message}`;
        });
    });
});

document.querySelectorAll("button").forEach((btn) => {
    if (btn.attributes.getNamedItem("data-loa-monld") != null) {

        if (btn.attributes.getNamedItem("data-loa-monld").value == "1") {
            if (mon_loaded == 1) {
                btn.disabled = false;
            } else {
                btn.disabled = true;
            }
        } else {
            if (mon_loaded == 1) {
                btn.disabled = true;
            } else {
                btn.disabled = false;
            }
        }
    }
});
