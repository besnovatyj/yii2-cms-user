/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

document.addEventListener("DOMContentLoaded", () => {

    let target = document.getElementById('show_hide_password');
    let icon = target.querySelector('span i');
    let input = target.querySelector('input');
    let inputGroup = target.querySelector('.input-group-text');

    icon.addEventListener("click", () => {
        if (input.type === 'text') {
            input.type = 'password';
            icon.classList.add('bi-eye-slash');
            icon.classList.remove('bi-eye');
            inputGroup.classList.remove('bg-danger');
            inputGroup.classList.add('bg-success');

        } else if (input.type === 'password') {
            input.type = 'text';
            icon.classList.add('bi-eye');
            icon.classList.remove('bi-eye-slash');
            inputGroup.classList.add('bg-danger');
            inputGroup.classList.remove('bg-success');
        }
    });
});
