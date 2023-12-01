

function write_debug(text) {
    let debug = document.getElementById("debug");
    document.body + "hurr";
    debug.innerHTML = debug.innerHTML + "<br><br>" + text;
}

$("#ai-type").change(function (event) {
    let selected_value = this[this.selectedIndex].value;
    let model_select = document.getElementById("ai-model");
    let gpt_container = document.getElementById("gpt");
    let dalle_container = document.getElementById("dalle");
    let focused_models = null;
    let max = 0;

    let gpt_models = [
        'gpt-4',
        'gpt-3.5-turbo-1106',
        'gpt-3.5-turbo',
        'gpt-3.5-turbo-16k'
    ];

    let dalle_models = [
        'dall-e-3',
        'dall-e-2'
    ];

    if (selected_value == 'gpt') {
        focused_models = gpt_models;
        dalle_container.classList.add('invisible');
        gpt_container.classList.remove('invisible');
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