
let debug = document.getElementById("debug");
debug.inner1HTML = '<span class="d-block p-2 text-bg-primary"><small><pre>' +
'        _===============÷÷÷÷÷÷÷_' +
"#    _,.;'‾DEBUG‾';.,_   #" +
'" ‾===============÷÷÷÷÷÷÷‾</pre></span><pre>';

function write_debug(text) {
    debug = document.getElementById("debug");
    document.body + "hurr";
    debug.innerHTML = debug.innerHTML + '<span class="d-block p-2 text-bg-dark">' + text + '</span>';
}

$("#ai-type").change(function (event) {
    let selected_value = this[this.selectedIndex].value;
    let model_select = document.getElementById("ai-model");
    let gpt_container = document.getElementById("gpt");
    let dalle_container = document.getElementById("dall-e");
    let focused_models = null;
    let max = 0;
    
    $("#ai-model").empty();
    write_debug("Cleared model dropdown");
    console.log(JSON.stringify(gpt_container));

    let gpt_models = [
        'gpt-4',
        'gpt-3.5-turbo-1106',
        'gpt-3.5-turbo',
        'gpt-3.5-turbo-16k'
    ];

    let dalle_models = [
        '',
        'dall-e-3',
        'dall-e-2'
    ];

    if (selected_value == 'gpt') {
        focused_models = gpt_models;
        try {
            dalle_container.classList.add('invisible');
            gpt_container.classList.remove('invisible');
        } catch (event) {
            console.log("nah" + " " + JSON.stringify(event));
        }
        write_debug("selected gpt");
    } else if (selected_value == 'dalle') {
        focused_models = dalle_models;
        dalle_container.classList.remove('invisible');
        gpt_container.classList.add('invisible');
        write_debug("got dalle");
    }

    max = focused_models.length;

    for (i=0; i<max; i++) {
        model = focused_models[i];
        write_debug("adding " + model);
        option = document.createElement("option");
        option.textContent = model;
        option.value = model;
        model_select.appendChild(option);
    }
});

$("#ai-model").change(function (event) {
    let selected_model = this[this.selectedIndex].value;
    document.getElementById("model").value = selected_model;
    console.log("model set to " + selected_model);
});