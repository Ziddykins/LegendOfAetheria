function flipmode() {
    let current_theme = document.body.attributes.getNamedItem('data-bs-theme').nodeValue;
    let flop = current_theme === 'dark' ? 'light' : 'dark';
    
    document.body.setAttribute('data-bs-theme', flop);
}