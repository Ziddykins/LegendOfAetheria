const glitch = document.querySelector('.glitch-effect');
const tl = new TimelineMax({ id: 'getGlitch', repeat: -1, repeatDelay: 3 });

tl.
fromTo(glitch, 0.1, { x: 0 }, { x: 10, ease: SteppedEase.config(2), className: '+=active' }).
to(glitch, 0.1, { scale: 1.4, ease: SteppedEase.config(2) }).
to(glitch, 0.1, { scale: 1, rotationY: 180, ease: SteppedEase.config(2) }).
fromTo(glitch, 0.1, { y: 0 }, { y: -10, ease: Linear.easeNone }).
fromTo(glitch, 0.1, { y: -10 }, { y: 0, ease: Linear.easeNone }).
to(glitch, 0.1, { rotationY: 0, ease: Linear.easeNone }).
set(glitch, { className: '+=slice' }).
to(glitch, 0.1, { x: -30, ease: SteppedEase.config(1) }).
set(glitch, { className: '-=slice' }).
to(glitch, 0.1, { x: 10, ease: SteppedEase.config(1) }).
to(glitch, 0.1, { scale: 1.8, ease: SteppedEase.config(2) }).
to(glitch, 0.1, { scale: 1, ease: SteppedEase.config(2) }).
to(glitch, 0.1, { x: 0, ease: SteppedEase.config(1) }).
fromTo(glitch, 0.1, { x: 0 }, { x: 5, ease: SteppedEase.config(2), className: '-=active' }).

fromTo(glitch, 0.2, { x: 0 }, { x: 5, ease: SteppedEase.config(2), delay: 2, className: '+=active' }).
set(glitch, { className: '-=active' }).

fromTo(glitch, 0.2, { x: 0 }, { x: 5, ease: SteppedEase.config(2), repeat: 1, delay: 1, className: '+=active' }).
set(glitch, { className: '-=active' });


Particles.init({
  selector: '.particles',
  color: '#111',
  sizeVariations: 3,
  maxParticles: 140,
  responsive: [
  {
    breakpoint: 768,
    options: {
      maxParticles: 70 } },

  {
    breakpoint: 320,
    options: {
      maxParticles: 35 } }] });
