/*let debug = document.getElementById("debug");
debug.innerHTML = '<span class="d-block p-2 text-bg-primary"><small><pre>' +
'        _===============÷÷÷÷÷÷÷_' +
"#    _,.;'‾DEBUG‾';.,_   #" +
'" ‾===============÷÷÷÷÷÷÷‾</pre></span><pre>';

function write_debug(text) {
    debug = document.getElementById("debug");
    debug.innerHTML = debug.innerHTML + '<span class="d-block p-2 text-bg-dark">' + text + '</span>';
}
*/
function create_dropdowns(parent_select, items) {
    let count = items.length;

    for (let i=-1; i<count; i++) {
        let option = document.createElement('option');
        
        if (i == -1) {
            option.value = '= Select =';
            option.textContent = '= Select =';
            option.disabled = true;
            option.selected = true;
        } else {
            option.value = items[i];
            option.textContent = items[i];
        }
        
        parent_select.appendChild(option);
    }
    console.log(parent_select);
    return parent_select;
}

var ai = {
    'text': {
        'models': [
            'gpt-4',
            'gpt-3.5-turbo-1106',
            'gpt-3.5-turbo',
            'gpt-3.5-turbo-16k'
        ],
    },

    'image': {
        'models': [
            'DALLE2',
            'DALLE3'
        ],
        'DALLE2': {
            'name': 'DALL-E 2',
            'max_prompt_size': 1000,
            'max_count': 5,
            'edits': true,

            'resolutions': [
                '256x256',
                '512x512',
                '1024x1024'
            ],

            'quality': [
                'standard',
                'vivid'
            ]
        },

        'DALLE3': {
            'name': 'DALL-E 3',
            'max_count': 1,
            'max_prompt_size': 4000,
            'edits': false,

            'resolutions': [
                '1024x1024',
                '1792x1024',
                '1024x1792'
            ],

            'quality': [
                'standard',
                'hd'
            ],

            'style': [
                'vivid',
                'natural'
            ]
        }
    },

    'output': [
        'png',
        'webp',
        'jpg'
    ]
};

//write_debug('AI: ' + JSON.stringify(ai, null, 2));

document.addEventListener('DOMContentLoaded', function() {
    const aiType = document.getElementById('ai-type');
    const aiModel = document.getElementById('ai-model');
    const gptDiv = document.getElementById('gpt');
    const dalleDiv = document.getElementById('dall-e');
    const emptyOption = document.createElement('option');
    emptyOption.value = '= Select =';
    emptyOption.textContent = '= Select =';
    emptyOption.disabled = true;
    emptyOption.selected = true;
    
    let selectedType = null;

    function updateModelField() {
        const modelInput = document.querySelector('input[name="model"]');
        if (modelInput) {
            modelInput.value = aiModel.value;
            console.log(`modelInput.value: ${modelInput.value} - aiModel.value: ${aiModel.value}`);
        }
    }

    // Initialize model options based on AI type selection
    aiType.addEventListener('change', function() {
        let models = [];

        if (this.value === 'text') {
            models = ai.text.models;
            gptDiv.classList.remove('invisible');
            dalleDiv.classList.add('invisible');
            selectedType = 'text';
        } else if (this.value === 'image') {
            models = ai.image.models;
            dalleDiv.classList.remove('invisible');
            gptDiv.classList.add('invisible');
            selectedType = 'image';
        }

        // Add options to model select

        models.forEach(model => {
            const option = document.createElement('option');
            option.value = model;
            option.textContent = model;
            aiModel.appendChild(option);
        });
        document.getElementById('ai-model').selectedIndex = 0;

    });

    // Update hidden model field on model selection
    aiModel.addEventListener('change', function() {
        updateModelField();


        
        // Update count input based on selected model
        const countInput = document.getElementById('count');
        var resolutionSelect = document.getElementById('resolutions');
        resolutionSelect.innerHTML = '';
        var resolutions = [];

        if (this.value === 'DALLE3') {
            countInput.value = '1';
            countInput.disabled = true;
            countInput.title = 'DALL-E 3 only supports generating 1 image at a time';
            resolutions = ai.image['DALLE3'].resolutions;
        } else {
            countInput.disabled = false;
            countInput.title = '';
            resolutions = ai.image['DALLE2'].resolutions;
        }

        resolutionSelect = create_dropdowns(resolutionSelect, resolutions);

    });

    // Ensure model is set before form submission
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            var modelInput = form.querySelector('input[name="model"]');
            modelInput = modelInput.replace('DALLE', 'dall-e');
            
            if (!modelInput || !modelInput.value) {
                e.preventDefault();
                alert('Please select an AI model first');
                return; aiModel.value = aiModel.value.replace('DALLE', 'dall-e-');
            }
            
            // Update model field one final time before submission
            updateModelField();
        });
    });

    // Handle DALL-E image generation form
    const dalleForm = document.getElementById('generate-dalle-images');
    if (dalleForm) {
        dalleForm.addEventListener('submit', function(e) {
            const modelInput = this.querySelector('input[name="model"]');
            modelInput = modelInput.replace('DALLE', 'dall-e');
            if (!modelInput || !modelInput.value) {
                e.preventDefault();
                alert('Please select an AI model first');
                return;
            }
            updateModelField();
        });
    }

    // Trigger initial state
    if (aiModel.value === 'DALLE3') {
        const countInput = document.getElementById('count');
        countInput.value = '1';
        countInput.disabled = true;
        countInput.title = 'DALL-E 3 only supports generating 1 image at a time';
    }
});