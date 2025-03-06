document.querySelector('form').addEventListener('submit', function(event) {
    const errorMessage = document.getElementById('error-message');
    
    // Check if there's an error parameter in the URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('error')) {
        errorMessage.textContent = 'Invalid username or password';
        errorMessage.style.display = 'block';
    }
});
