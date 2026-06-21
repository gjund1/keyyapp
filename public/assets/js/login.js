// ===================
//   Visibility PWD
// ===================

const togglePwd = document.querySelectorAll('.togglePwd');

togglePwd.forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        const icon = btn.querySelector('i');
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
});

// =========================
//    SignIn PWD 8 minimum
// ========================

const pwd1 = document.getElementById('pwd');
const pwd2 = document.getElementById('pwd2');
const submitBtn = document.getElementById('submitBtn');

function checkPasswords() {
    const ok = pwd1.value.length >= 8 && pwd1.value === pwd2.value;

    // reset classes
    pwd2.classList.remove('pwd-valid', 'pwd-invalid');
    if (pwd2.value === '') {
        submitBtn.disabled = true;
        return;
    }

    if (ok) {
        pwd2.classList.add('pwd-valid');
        submitBtn.disabled = false;
    } else {
        pwd2.classList.add('pwd-invalid');
        submitBtn.disabled = true;
    }
}

// protection page login vs signup
if (pwd1 && pwd2 && submitBtn) {
    pwd1.addEventListener('input', checkPasswords);
    pwd2.addEventListener('input', checkPasswords);
    checkPasswords();
}