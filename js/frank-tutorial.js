// Frank Tutorial Overlay Logic
(function() {
    if (!window.FANK_TUTORIAL_STEPS || !window.FANK_TUTORIAL_STEPS.steps) return;
    const steps = window.FANK_TUTORIAL_STEPS.steps;
    let current = 0;
    let overlay, speech, lastFocus;
    expand_all();

    function blurExcept(selector) {
        document.querySelectorAll('body > *:not(.frank-tutorial-overlay):not(.frank-tutorial-speech)').forEach(el => {
            if (!el.matches(selector) && !el.classList.contains('frank-tutorial-speech')) {
                el.classList.add('frank-tutorial-blur');
            } else {
                el.classList.remove('frank-tutorial-blur');
            }
        });
    }

    function removeBlur() {
        document.querySelectorAll('.frank-tutorial-blur').forEach(el => el.classList.remove('frank-tutorial-blur'));
    }

    function positionSpeechBubble(target) {
        if (!target) return;
        const rect = target.getBoundingClientRect();
        speech.style.top = (window.scrollY + rect.top + rect.height + 12) + 'px';
        speech.style.left = (window.scrollX + rect.left - 20) + 'px';
    }

    function showStep(idx) {
        const step = steps[idx];
        if (!step) return;
        // Blur everything except the focused element
        blurExcept(step.focusSelector);
        // Highlight/focus the element
        const focusEl = document.querySelector(step.focusSelector);
        if (focusEl) {
            focusEl.scrollIntoView({behavior: 'smooth', block: 'center'});
            lastFocus = focusEl;
        }
        // Update speech bubble
        speech.innerHTML = `
            <img src="${step.npcImage}" class="frank-tutorial-npc-img" alt="Frank">
            <div class="frank-tutorial-bubble">
                ${step.message}
                <br><button class="frank-tutorial-next-btn">${idx < steps.length-1 ? 'Next' : 'Finish'}</button>
            </div>
        `;
        setTimeout(() => positionSpeechBubble(focusEl), 100);
        document.getElementsByClassName('frank-tutorial-bubble')[0].scrollTo();
        // Next button
        speech.querySelector('.frank-tutorial-next-btn').onclick = function(e) {
            e.stopPropagation();
            if (current < steps.length-1) {
                current++;
                showStep(current);
            } else {
                // End tutorial
                overlay.remove();
                speech.remove();
                removeBlur();
            }
        };
    }

    // Create overlay
    overlay = document.createElement('div');
    overlay.className = 'frank-tutorial-overlay';
    document.body.appendChild(overlay);
    // Create speech bubble
    speech = document.createElement('div');
    speech.className = 'frank-tutorial-speech';
    document.body.appendChild(speech);

    // Start tutorial
    showStep(current);

    // Reposition on resize/scroll
    window.addEventListener('resize', () => positionSpeechBubble(lastFocus));
    window.addEventListener('scroll', () => positionSpeechBubble(lastFocus));
})();
