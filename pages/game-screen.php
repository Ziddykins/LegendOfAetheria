<?php
    use Game\Monster\Enums\Scope;
    use Game\System\System;
    use Game\Monster\Monster;

    require_once "functions.php";



?>

<div class="container border border-1 w-50">
    <div class="d-flex align-items-evenly">
        <label class="flex-grow-1" for="timeout">Amount:</label>&ensp;
        <input class="flex-grow-1" id="amount" name="amount" type="text" value="10">
        <button id="pull" name="pull" role="button" class="bg-success bg-gradient flex-grow-1">Submit</button>
    </div>

    <div class="d-flex align-items-evenly">
        <label class="flex-grow-1" for="timeout">Timeout(ms):</label>&ensp;
        <input class="flex-grow-1" id="timeout" name="timeout" type="text" value="2000">
        <button id="clear" name="clear" role="button" class="bg-secondary bg-gradient text-black flex-grow-1">Clear&nbsp;</button>
        <button id="clear" name="clear" role="button" class="bg-danger bg-gradient flex-grow-1">-Stop-&nbsp;</button>
    </div>

        
    <div class="d-flex align-items-center">
        <span class="col">Status&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
        <span id="status" name="status" class="col"></span>
        <div class="small" style="font-size: 10px;" id="proxy" name="proxy">[0/0] Not Running</div>
    </div>

    <div id="img-container" name="img-container" class="d-flex">
        <script>
            function getRndInteger(min, max) {
                return Math.floor(Math.random() * (max - min + 1) ) + min;
            }

            var cors_proxies = [
                //tml https://cors-proxy.htmldriven.com/?url=',
                //tml https://cors-proxy.htmldriven.com/?url='
               //tml https://corsproxy.io/?url=',
               //tml https://corsproxy.io/?url='
                'html https://api.allorigins.win/get?url=',
                'html https://api.allorigins.win/get?url='
                //'html https://thingproxy.freeboard.io/fetch/',
                //'html https://thingproxy.freeboard.io/fetch/'
            ];

            var proxy_count = cors_proxies.length;
            var success = 0;
            var fail = 0;
            var cur_proxy = 0;
            var timeout = 2000;

            document.getElementById("clear").addEventListener("click", () => {
                document.getElementById("timeout").value = 2000;
                document.getElementById("amount").value = 10;
            });

            function getScId() {
                let id = '';
                for (let i = 0; i < 200; i++) {
                    id += String.fromCharCode(Math.floor(Math.random() * (127 - 33)) + 33);
                }
                id = id.replace(/[^0-9A-Za-z]/g, '');
                id = id.substring(0, getRndInteger(6, 8));
                return id;
            }
 
            async function do_get(idz, idx) {
                const proxy_return = cors_proxies[idx].split(" ")[0];
                const proxy_url    = cors_proxies[idx].split(" ")[1];

                console.log(`fetching ${idz} with ${proxy_url}`);

                const response = await fetch(`${proxy_url}https://prnt.sc/${idz}`).catch((error) => { console.error(error); });
                
                if (!response) {
                    console.log("no");
                    return;
                }
                const data = await response.json();
                const woop = data.contents
                const parser  = new DOMParser();
                const doc     = parser.parseFromString(woop, "text/html");
                const img_ele = doc.querySelector(".screenshot-image");

                if (img_ele != null) {
                    if (!img_ele.src.match(/211be8ff/)) {
                        success += 1;
                        document.getElementById("img-container").after(img_ele);
                        console.log("Found positive result");
                    } else {
                        console.log("screenshot removed");
                    }
                } else {
                    fail += 1;
                    console.log(`${idz}: no results`);
                }
            }

            document.getElementById("pull").addEventListener("click", async () => {
                const amount = document.getElementById("amount").value;
                var cur_proxy = 0;

                for (let i=0; i<amount; i++) {
                    const identifier = getScId();
                    const status_ele = document.getElementById("status");
                    const timeout    = document.getElementById("timeout").value;

                    if (cur_proxy = cors_proxies.length - 1) {
                        cur_proxy = 0;
                    } else {
                        cur_proxy += 1;
                    }
                    
                    if (cors_proxies.length == 0) {
                        status_ele.innerText = "Exhausted proxies";
                        return;
                    }

                    document.getElementById("proxy").innerHTML = `[${cur_proxy} / ${cors_proxies.length} ] ${cors_proxies[cur_proxy]}`;
                    
                    await do_get(identifier, cur_proxy);
                    await new Promise(r => setTimeout(r, timeout));
                    status_ele.innerHTML = `(<span class="text-success">${success}</span>:<span class="text-danger">${fail}</span>) - (${i+1}/${amount})`;
                }
            });
        </script>
    </div>
</div>
            
