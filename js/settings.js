document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap components
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(el => new bootstrap.Tooltip(el));

    // Handle IP lock toggle
    const ipLockSwitch = document.getElementById('ip-lock-switch');
    const ipLockAddress = document.getElementById('ip-lock-address');
    
    ipLockSwitch?.addEventListener('change', function() {
        ipLockAddress.classList.toggle('invisible', !this.checked);
        ipLockAddress.required = this.checked;
    });

    // Handle font preview
    const fontSelect = document.getElementById('font-type');
    const fontPreview = document.getElementById('font-preview');
    
    // Load Google Fonts
    const fonts = [
        'Roboto',
        'Open+Sans',
        'Lato',
        'Merriweather',
        'PT+Serif',
        'Montserrat',
        'Poppins'
    ];
    
    WebFont.load({
        google: {
            families: fonts
        }
    });

    fontSelect?.addEventListener('change', function() {
        const selectedFont = this.value.replace('-', ' ');
        fontPreview.style.fontFamily = selectedFont;
    });

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Save settings
    const saveBtn = document.getElementById('save-settings');
    saveBtn?.addEventListener('click', async function() {
        const spinner = this.querySelector('.spinner-border');
        spinner.classList.remove('d-none');
        this.disabled = true;

        try {
            // Your existing save_settings logic here
            await save_settings('ip_lock');
        } finally {
            spinner.classList.add('d-none');
            this.disabled = false;
        }
    });
});
