let icon_holder = null;

function spinner_swap(element, original_icon, unswap) {
    if (unswap) {
        element.children[0].classList.remove('spinner-border');
        element.children[0].remove('spinner-border-sm');
        element.children[0].add(icon_holder);
        icon_holder = null;
    } else {
        icon_holder = original_icon; // will be like, 'bi-whatever'
        console.log("add spinner");
        console.log("original icon:" + original_icon);
        element.children[0].remove(original_icon);
        element.children[0].add('spinner-border');
        element.children[0].add('spinner-border-sm');
    }
    return;
}

document.querySelectorAll('button').forEach(function(element) {
    element.addEventListener('click', function() {
        let original_icon =  element.children[0].classList[1];

        if (element.id == 'clear-description') {
            if (confirm("Are you sure you want to clear the character description?")) {
                document.getElementById("character-description").textContent = "";
            }
        } else {
            let do_fetch = 0;
            let data    = null;
            let url     = null;

            if (element.id == "save-description") {
                url = "save";
                data = {
                    data: document.getElementById("character-description").value,
                    id: loa.u_cid,
                    save_description: 1
                };
                do_fetch = 1;
            } else if (element.id == "generate-description") {
                url = "openai";
                data = { 
                    characterID: loa.u_cid,
                    accountID: loa.u_aid,
                    generate_description: 1
                };
                do_fetch = 1;
            }

            if (do_fetch) {
                spinner_swap(element, original_icon, false);
                fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    type: "POST",
                    body: JSON.stringify(data)
                }).then((response) => {
                    if (!response.ok) {
                        spinner_swap(element, original_icon, true);
                        throw new Error(response.text());                        
                    } else {
                        spinner_swap(element, original_icon, true);
                        return response.json();
                    }
                }).then((data) => {
                    console.log(data);
                }).catch((error) => {
                    console.error('Error:', error);
                    
                });
            }
        }
    });
});