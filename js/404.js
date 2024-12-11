document.addEventListener('DOMContentLoaded', function() {
    const glitch = document.querySelector('.glitch-effect');
    if (!glitch) return;

    const tl = new TimelineMax({ 
        id: 'glitch404', 
        repeat: -1, 
        repeatDelay: 3 
    });

    tl.fromTo(glitch, 0.1, 
        { x: 0 }, 
        { x: 10, ease: SteppedEase.config(2), className: '+=active' }
    )
    .to(glitch, 0.1, { scale: 1.4, ease: SteppedEase.config(2) })
    .to(glitch, 0.1, { scale: 1, rotationY: 180, ease: SteppedEase.config(2) })
    .fromTo(glitch, 0.1, { y: 0 }, { y: -10, ease: Linear.easeNone })
    .fromTo(glitch, 0.1, { y: -10 }, { y: 0, ease: Linear.easeNone })
    .to(glitch, 0.1, { rotationY: 0, ease: Linear.easeNone })
    .set(glitch, { className: '+=slice' })
    .to(glitch, 0.1, { x: -30, ease: SteppedEase.config(1) })
    .set(glitch, { className: '-=slice' })
    .to(glitch, 0.1, { x: 10, ease: SteppedEase.config(1) })
    .to(glitch, 0.1, { scale: 1.8, ease: SteppedEase.config(2) })
    .to(glitch, 0.1, { scale: 1, ease: SteppedEase.config(2) })
    .to(glitch, 0.1, { x: 0, ease: SteppedEase.config(1) })
    .fromTo(glitch, 0.1, 
        { x: 0 }, 
        { x: 5, ease: SteppedEase.config(2), className: '-=active' }
    );
});
