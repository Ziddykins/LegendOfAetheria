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
            'dall-e-2',
            'dall-e-3'
        ],
        'dall-e-2': {
            'name': 'dall-e-2',
            'max_prompt_size': 1000,
            'max_count': 5,
            'edits': true,

            'resolutions': [
                '1024x1024',
                '1024x1536',
                '1536x1024'
            ],

            'quality': [
                'standard',
                'vivid'
            ]
        },

        'dall-e-3': {
            'name': 'dall-e-3',
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
    const dalleDiv = document.getElementById('dall-e-container');
    const emptyOption = document.createElement('option');
    const previousImages = document.getElementById('previous-images');

    emptyOption.value = '= Select =';
    emptyOption.textContent = '= Select =';
    emptyOption.disabled = true;
    emptyOption.selected = true;
    
    let selectedType = null;

    gptDiv.style.display = 'none';
    dalleDiv.style.display = 'none';
    previousImages.style.display = 'none';

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
            selectedType = 'text';
        } else if (this.value === 'image') {
            models = ai.image.models;
            selectedType = 'image';
        }

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


        if (aiType.value === 'text') {
            dalleDiv.style.display = 'none';
            gptDiv.style.display = 'block';
            previousImages.style.display = 'none';
        } else if (aiType.value === 'image') {
            dalleDiv.style.display = 'block';
            gptDiv.style.display = 'none';
            previousImages.style.display = 'block';
        }

        // Update count input based on selected model
        const countInput = document.getElementById('count');
        var resolutionSelect = document.getElementById('resolutions');
        resolutionSelect.innerHTML = '';
        var resolutions = [];

        if (this.value === 'dall-e-3') {
            countInput.value = '1';
            countInput.disabled = true;
            countInput.title = 'dall-e-3 only supports generating 1 image at a time';
            resolutions = ai.image['dall-e-3'].resolutions;
        } else {
            countInput.disabled = false;
            countInput.title = '';
            resolutions = ai.image['dall-e-2'].resolutions;
        }

        resolutionSelect = create_dropdowns(resolutionSelect, resolutions);

    });

    // Ensure model is set before form submission
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            var modelInput = form.querySelector('input[name="model"]');
            
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
            if (!modelInput || !modelInput.value) {
                e.preventDefault();
                alert('Please select an AI model first');
                return;
            }
            document.getElementById('use-previous-slot').value = parseInt(document.querySelector('input[type = radio]:checked').id.split('-')[2]);
            updateModelField();
        });
    }

    // Trigger initial state
    if (aiModel.value === 'dall-e-3') {
        const countInput = document.getElementById('count');
        countInput.value = '1';
        countInput.disabled = true;
        countInput.title = 'dall-e-3 only supports generating 1 image at a time';
    }
});