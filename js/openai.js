let debug = document.getElementById("debug");
debug.innerHTML = '<span class="d-block p-2 text-bg-primary"><small><pre>' +
'        _===============÷÷÷÷÷÷÷_' +
"#    _,.;'‾DEBUG‾';.,_   #" +
'" ‾===============÷÷÷÷÷÷÷‾</pre></span><pre>';

function write_debug(text) {
    debug = document.getElementById("debug");
    debug.innerHTML = debug.innerHTML + '<span class="d-block p-2 text-bg-dark">' + text + '</span>';
}

// Model lists
const gptModels = ['gpt-4', 'gpt-3.5-turbo-1106', 'gpt-3.5-turbo', 'gpt-3.5-turbo-16k'];
const dalleModels = ['dall-e-3', 'dall-e-2'];

document.addEventListener('DOMContentLoaded', function() {
    const aiType = document.getElementById('ai-type');
    const aiModel = document.getElementById('ai-model');
    const gptDiv = document.getElementById('gpt');
    const dalleDiv = document.getElementById('dall-e');
    let selectedType = null;

    function updateModelField() {
        const modelInput = document.querySelector('input[name="model"]');
        if (modelInput) {
            modelInput.value = aiModel.value;
        }
    }

    // Initialize model options based on AI type selection
    aiType.addEventListener('change', function() {
        aiModel.innerHTML = ''; // Clear existing options
        let models = [];

        if (this.value === 'gpt') {
            models = gptModels;
            gptDiv.classList.remove('invisible');
            dalleDiv.classList.add('invisible');
            selectedType = 'gpt';
        } else if (this.value === 'dalle') {
            models = dalleModels;
            dalleDiv.classList.remove('invisible');
            gptDiv.classList.add('invisible');
            selectedType = 'dalle';
        }

        // Add options to model select
        models.forEach(model => {
            const option = document.createElement('option');
            option.value = model;
            option.textContent = model;
            aiModel.appendChild(option);
        });

        // Select first model by default
        if (models.length > 0) {
            aiModel.value = models[0];
            updateModelField();
        }
    });

    // Update hidden model field on model selection
    aiModel.addEventListener('change', function() {
        updateModelField();
        
        // Update count input based on selected model
        const countInput = document.getElementById('count');
        if (this.value === 'dall-e-3') {
            countInput.value = '1';
            countInput.disabled = true;
            countInput.title = 'DALL-E 3 only supports generating 1 image at a time';
        } else {
            countInput.disabled = false;
            countInput.title = '';
        }
    });

    // Ensure model is set before form submission
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const modelInput = form.querySelector('input[name="model"]');
            if (!modelInput || !modelInput.value) {
                e.preventDefault();
                alert('Please select an AI model first');
                return;
            }
            
            // Update model field one final time before submission
            updateModelField();
        });
    });

    // Handle DALL-E image generation form
    const dalleForm = document.getElementById('generate-dalle-images');
    if (dalleForm) {
        dalleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            formData.append('gen-images', '1'); // Add the gen-images parameter
            
            const submitButton = form.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...';

            fetch('/game?page=administrator&sub=system', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                // Clear any existing error messages
                const existingAlerts = document.querySelectorAll('.alert');
                existingAlerts.forEach(alert => alert.remove());
                
                // Parse the response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Handle error messages
                const errorMsg = doc.querySelector('.alert');
                if (errorMsg) {
                    const errorContainer = document.createElement('div');
                    errorContainer.innerHTML = errorMsg.outerHTML;
                    form.insertAdjacentElement('afterend', errorContainer);
                    setTimeout(() => errorContainer.remove(), 5000);
                }

                // Update images area if present
                const newImages = doc.querySelector('.d-flex.container.flex-wrap');
                const existingImages = document.querySelector('.d-flex.container.flex-wrap');
                if (newImages) {
                    if (existingImages) {
                        existingImages.replaceWith(newImages);
                    } else {
                        form.insertAdjacentElement('afterend', newImages);
                    }
                }

                // Update the previous image section if exists
                const newPrevImageSection = doc.querySelector('#dall-e .card');
                const currentPrevImageSection = document.querySelector('#dall-e .card');
                if (newPrevImageSection && currentPrevImageSection) {
                    currentPrevImageSection.replaceWith(newPrevImageSection);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorContainer = document.createElement('div');
                errorContainer.className = 'alert alert-danger';
                errorContainer.textContent = 'An error occurred while generating images.';
                form.insertAdjacentElement('afterend', errorContainer);
                setTimeout(() => errorContainer.remove(), 5000);
            })
            .finally(() => {
                // Restore button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        });
    }

    // Trigger initial state
    if (aiModel.value === 'dall-e-3') {
        const countInput = document.getElementById('count');
        countInput.value = '1';
        countInput.disabled = true;
        countInput.title = 'DALL-E 3 only supports generating 1 image at a time';
    }
});