var stepper = new Stepper($('.bs-stepper')[0]);
var stepperEl = document.querySelector('.bs-stepper');
var next_button = document.getElementById('next_button');

next_button.addEventListener('click', function (event) {
    if (next_button.type == 'button') {
        event.preventDefault();
        event.stopPropagation();
    }

    let we_good = 1;
    let content_pages = document.querySelectorAll('.content');

    content_pages[stepper._currentIndex].querySelectorAll('input').forEach(function (element) {
        if (element.hasAttribute('required')) {
            if (element.value.length <= 0) {
                we_good = 0;
                element.classList.add('border-danger')
            }
        }
    });
    
    if (we_good) {
        stepper.next();
    }

    let finish_button = document.getElementById('next_button');

    if (stepper._currentIndex == 2) {
        finish_button.innerText = 'Finish';
        finish_button.type = "submit";
    } else {                
        finish_button.innerText = 'Continue';
        finish_button.type = "button";
    }
});

stepperEl.addEventListener('show.bs-stepper', function (event) {
    let pages = document.querySelector('.bs-stepper-content');
    console.log(JSON.stringify(event.detail));
    console.warn(event.detail.indexStep)
});