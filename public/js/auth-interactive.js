document.addEventListener('DOMContentLoaded', function() {
    // Password Strength Meter
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');

    if (passwordInput && strengthBar) {
        passwordInput.addEventListener('input', function() {
            const val = passwordInput.value;
            let strength = 0;
            
            if (val.length > 5) strength++;
            if (val.length > 8) strength++;
            if (/[A-Z]/.test(val)) strength++;
            if (/[0-9]/.test(val)) strength++;
            if (/[^A-Za-z0-9]/.test(val)) strength++;

            strengthBar.className = 'strength-meter-bar';
            
            if (val.length === 0) {
                strengthText.textContent = '';
            } else if (strength <= 2) {
                strengthBar.classList.add('strength-poor');
                strengthText.textContent = 'Poor';
                strengthText.className = 'text-xs mt-1 text-red-500';
            } else if (strength === 3) {
                strengthBar.classList.add('strength-good');
                strengthText.textContent = 'Good';
                strengthText.className = 'text-xs mt-1 text-amber-500';
            } else if (strength === 4) {
                strengthBar.classList.add('strength-strong');
                strengthText.textContent = 'Strong';
                strengthText.className = 'text-xs mt-1 text-emerald-500';
            } else {
                strengthBar.classList.add('strength-very-strong');
                strengthText.textContent = 'Very Strong';
                strengthText.className = 'text-xs mt-1 text-emerald-600 font-bold';
            }
        });
    }

    // Password Visibility Toggle
    const toggles = document.querySelectorAll('.password-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = document.getElementById(this.dataset.target);
            const icon = this.querySelector('span');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        });
    });

    // Form Field Entrance Animations
    const fields = document.querySelectorAll('.animate-fade-up');
    fields.forEach((field, index) => {
        field.style.animationDelay = `${0.1 * (index + 1)}s`;
    });
});
