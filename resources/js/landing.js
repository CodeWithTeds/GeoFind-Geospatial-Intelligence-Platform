/**
 * Landing Page Logic
 * Handles 3D perspective text effect.
 */

document.addEventListener('DOMContentLoaded', () => {
    initPerspectiveText();
});

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
