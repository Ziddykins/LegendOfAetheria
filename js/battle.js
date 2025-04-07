update_hud();

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

document.querySelectorAll("button[id^='hunt']").forEach(async (btn) => {
    let which = btn.id.split("-")[1];

    if (which == 'new') {
        return;
    }

    document.getElementById(`hunt-${which}-btn`).addEventListener("click", async (e) => {
        let atk_type = document.getElementById(`hunt-${which}-btn`).textContent;
        let battle_log = document.getElementById("battle-log");
        let lines = document.getElementById("battle-log").querySelectorAll("span").length;
        let text_height = 24;
        let max_lines = Math.round(document.getElementById("battle-log").clientHeight / text_height);

        if (lines >= max_lines) {
            battle_log.innerHTML = "";
        }

        fetch('/battle', {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'text/plain'
            },
            method: 'POST',
            body: `action=${which}&type=${atk_type}&csrf-token=${csrf_token}`
        }).then((response) => response.text()).then(async(data) => {
                console.log(`got data: ${data}`);
                battle_log.insertAdjacentHTML('afterbegin', data);
                await update_hud();
            }).catch((error) => {
                battle_log.insertAdjacentHTML = battle_log.innerHTML + `${error.message}`;
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

async function update_hud() {
    fetch(`/hud?action=get&csrf-token=${loa.u_csrf}`, {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'application/json'
        },
        method: 'GET',
    }).then((response) => response.json())
    .then((data) => {
        let monster_stats = data.monster;
        let player_stats = data.player;
        let player_hp = document.getElementById("player-hp");
        let player_mp = document.getElementById("player-mp");
        let player_ep = document.getElementById("player-ep");
        let monster_hp = document.getElementById("monster-hp");
        let monster_mp = document.getElementById("monster-mp");

        player_hp.textContent = player_stats.hp + ' / ' + player_stats.maxHP;
        player_mp.textContent = player_stats.mp + ' / ' + player_stats.maxMP;
        player_ep.textContent = player_stats.ep + ' / ' + player_stats.maxEP;
        monster_hp.textContent = monster_stats.hp + ' / ' + monster_stats.maxHP;
        monster_mp.textContent = monster_stats.mp + ' / ' + monster_stats.maxMP;
    }).catch((error) => {
        console.error(error);
    });
}