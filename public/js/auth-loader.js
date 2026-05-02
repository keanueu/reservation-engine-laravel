
// Ensure the auth pages hide the loader after the page loads (partial may be included in auth layouts)
window.addEventListener('load', function () {
    const loader = document.getElementById('cabanas-loader');
    const main = document.getElementById('main-content');
    
    // Add loader-active class initially
    document.body.classList.add('loader-active');
    
    if (main) main.classList.remove('opacity-0');
    if (loader) {
        loader.classList.add('opacity-0');
        setTimeout(() => {
            loader.classList.add('hidden');
            document.body.classList.remove('loader-active');
        }, 500);
    }
});
