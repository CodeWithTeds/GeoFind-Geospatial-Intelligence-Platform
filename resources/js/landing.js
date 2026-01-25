/**
 * Landing Page Logic
 * Handles 3D perspective text effect.
 */

document.addEventListener('DOMContentLoaded', () => {
    initPerspectiveText();
    handleLoadingScreen();
});

/**
 * Handles the loading screen fade out.
 */
function handleLoadingScreen() {
    const loadingScreen = document.getElementById('loading-screen');
    if (!loadingScreen) return;

    // Minimum display time of 2.5 seconds to show off the slow animation
    const minDisplayTime = 2500;
    const startTime = Date.now();

    window.addEventListener('load', () => {
        const elapsedTime = Date.now() - startTime;
        const remainingTime = Math.max(0, minDisplayTime - elapsedTime);

        setTimeout(() => {
            loadingScreen.style.opacity = '0';
            setTimeout(() => {
                loadingScreen.remove();
            }, 500); // Wait for transition to finish
        }, remainingTime);
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
