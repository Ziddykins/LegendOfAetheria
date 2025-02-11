document.querySelectorAll("ul[id$='drop-menu']").forEach((menu) => {
    let which = menu.id.split("-")[0];
    let short = which == 'attack' ? 'atk' : 'spl';

    document.getElementById(`${which}-drop-menu`).querySelectorAll("li").forEach((li) => {
        console.log(`Setting event listener on li-item ${li.textContent} - which: ${which} - short: ${short}`);
	    li.addEventListener("click", (e) => {
		    document.getElementById(`hunt-${which}-btn`).textContent = e.target.textContent;
            document.getElementById(`hunt-${which}-btn`).value = e.target.attributes.getNamedItem(`data-loa-${short}`);
	    });
    });
});

document.querySelectorAll("button[id^='hunt']").forEach((btn) => {
    let which = btn.id.split("-")[1];

    if (which == 'new') {
        console.log("naaaaaoooo");
        return;
    }

    document.getElementById(`hunt-${which}-btn`).addEventListener("click", async (e) => {
        let atk_type = document.getElementById(`hunt-${which}-btn`).textContent;

        fetch('/battle', {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'text/plain'
            },
            method: 'POST',
            body: `action=${which}&type=${atk_type}`}
        ).then((response) => {
            if (!response.ok) {
                return response.text().then((data) => {
                    throw new Error(data);
                });
            } else {
                return response.text();
            }
        }).then((data) => {
            document.body.innerHTML = data;
        });
    });
});