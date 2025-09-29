
/**
 * Обработчик входа в систему.
 * @param {Event} e - Событие отправки формы
 */
async function handleLogin(e) {
    e.preventDefault();
    const username = e.target.username.value;
    const password = e.target.password.value;
    const btn = e.target.querySelector('button');
    btn.disabled = true;
    btn.textContent = 'Logging in...';

    Service.login(username, password, false,
        (response) => {
            showNotification('Login', 'Successfully', 'success');
            setTimeout(function() {
                window.location.href = '/web';
            }, 1000);
        },
        (response) => {
            showNotification('Login Error', response.responseJSON.message, 'error');
            btn.disabled = false;
            btn.textContent = 'Login';
        }
    )
}

async function handleLogout(e) {
    e.preventDefault();
    Service.logout(false,
        (response) => {
            showNotification('Logout', 'Successfully', 'success');
            setTimeout(function() {
                window.location.href = '/web/login';
            }, 1000);
        },
        (response) => {
            showNotification('Logout Error', response.responseJSON.message, 'error');
        }
    )
}

function checkAuth() {
    Service.sessionExist(true, (response) => {
        if (response.session === true) {
            if (window.location.pathname !== '/web') {
                window.location.href = '/web';
            }
        } else {
            if (window.location.pathname !== '/web/login') {
                window.location.href = '/web/login';
            }
        }
    });
}
