/**
 * Landing Page Logic
 * Handles 3D perspective text effect.
 */

document.addEventListener('DOMContentLoaded', () => {
    initPerspectiveText();
    initModalLogic();
});

/**
 * Initializes the modal interaction logic.
 */
function initModalLogic() {
    const playBtn = document.getElementById('play-now-btn');
    const modal = document.getElementById('instruction-modal');
    const startBtn = document.getElementById('start-game-btn');
    const closeModalBtn = document.getElementById('close-modal-btn');

    // Guard clause if essential elements are missing
    if (!playBtn || !modal || !startBtn || !closeModalBtn) return;

    // Show Modal
    playBtn.addEventListener('click', (e) => {
        e.preventDefault(); // Prevent default action if it was a link
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    // Close Modal (Cancel)
    closeModalBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    // Start Game (Navigate to /play)
    startBtn.addEventListener('click', () => {
        window.location.href = '/play';
    });
}

/**
 * Initializes the 3D perspective text effect.
 */
function initPerspectiveText() {
    const perspectiveText = document.getElementById('perspective-text');
    if (!perspectiveText) return;

    let progress = 0.5;

    function updateTransform() {
        const rotateY = -35 + progress * 6;
        const translateZ = 30 + progress * 15;
        const translateY = -progress * 12;
        perspectiveText.style.transform = `perspective(900px) rotateY(${rotateY}deg) skewX(-6deg) translateZ(${translateZ}px) translateY(${translateY}px)`;
    }

    updateTransform();
    document.addEventListener('mousemove', (e) => {
        progress = e.clientX / window.innerWidth;
        updateTransform();
    });
}
