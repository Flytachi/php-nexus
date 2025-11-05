const state = {
    logTimerId: null,
    isLogPaused: false,
};

const ui = {
    btnLogout: document.getElementById('btnLogout'),
    indicator: document.getElementById('indicator'),
    indName: document.getElementById('ind_name'),
    indCondition: document.getElementById('ind_condition'),
    indPid: document.getElementById('ind_pid'),
    indStartedAt: document.getElementById('ind_started_at'),
    indBalancer: document.getElementById('ind_balancer'),
    btnStart: document.getElementById('btnStart'),
    btnStop: document.getElementById('btnStop'),
    logContainer: document.getElementById('log'),
    logFileSelect: document.getElementById('logParamFileName'),
    btnToggleLogs: document.getElementById('btnToggleLogs'),
    notificationContainer: document.getElementById('notification-container'),
    logLimitSelect: document.getElementById('logLimitSelect'),
    logTimerSelect: document.getElementById('logTimerSelect')
};

function main() {
    addEventListeners();
    updateStatus();
    setInterval(updateStatus, 3000);
    initializeLogs();
}

function addEventListeners() {
    ui.btnLogout.addEventListener('click', handleLogout)
    ui.btnStart.addEventListener('click', handleStartService);
    ui.btnStop.addEventListener('click', handleStopService);
    ui.logFileSelect.addEventListener('change', handleLogFileChange);
    ui.btnToggleLogs.addEventListener('click', toggleLogStream);
    ui.logLimitSelect.addEventListener('change', handleLogSettingsChange);
    ui.logTimerSelect.addEventListener('change', handleLogSettingsChange);
}

async function updateStatus() {
    Service.serviceStatus(true, (response) => {
        console.log(response)
        ui.indName.textContent = response.info.status.className;
        ui.indCondition.textContent = response.info.status.condition;
        ui.indPid.textContent = response.info.status.pid || 'N/A';
        ui.indStartedAt.textContent = response.info.status.startedAt || '...';
        ui.indBalancer.textContent = response.info.status.balancer;

        if (response.info.status.condition === 'ACTIVE') {
            ui.indicator.classList.remove('offline');
            ui.indicator.classList.add('online');
            ui.btnStart.style.display = 'none';
            ui.btnStop.style.display = 'block';
        } else {
            ui.indicator.classList.remove('online');
            ui.indicator.classList.add('offline');
            ui.btnStart.style.display = 'block';
            ui.btnStop.style.display = 'none';
        }

        const stats = response.stats;
        document.getElementById('stats_ready').textContent = stats.ready;
        document.getElementById('stats_unacked').textContent = stats.unacked;
        document.getElementById('stats_total').textContent = stats.total;
        document.getElementById('stats_consumers').textContent = stats.consumers;

        const units = response.units || [];
        document.getElementById('units_count').textContent = units.length;
        const unitsStringContainer = document.getElementById('units-string');
        if (units.length > 0) {
            unitsStringContainer.textContent = units.join(', ');
        } else {
            unitsStringContainer.textContent = 'No active units';
        }

    }, (response) => {
        ui.indicator.classList.remove('online');
        ui.indicator.classList.add('offline');
        ui.indCondition.textContent = 'ошибка';
        document.getElementById('stats_ready').textContent = '—';
        document.getElementById('stats_unacked').textContent = '—';
        document.getElementById('stats_total').textContent = '—';
        document.getElementById('stats_consumers').textContent = '—';
        document.getElementById('units_count').textContent = '0';
        document.getElementById('units_list').innerHTML = '<div class="muted">Нет данных</div>';
    });
}

async function handleStartService() {
    ui.btnStart.disabled = true;
    Service.serviceStart(false, async (response) => {
        showNotification('Success', 'The command to launch the kernel has been sent', 'success');
        ui.btnStart.disabled = false;
        await updateStatus();
    }, (response) => {
        showNotification('Start error', response.responseJSON.message, 'error');
        ui.btnStart.disabled = false;
    });
}

async function handleStopService() {
    ui.btnStop.disabled = true;
    Service.serviceStop(false, async (response) => {
        showNotification('Success', 'The command to stop the kernel has been sent', 'success');
        ui.btnStop.disabled = false;
        await updateStatus();
    }, (response) => {
        showNotification('Stop error', response.responseJSON.message, 'error');
        ui.btnStop.disabled = false;
    });
}

async function initializeLogs() {
    Service.logFiles(true, async (response) => {
        ui.logFileSelect.innerHTML = '';
        response.forEach(file => {
            const option = new Option(file, file);
            ui.logFileSelect.add(option);
        });

        if (response.length > 0) {
            await fetchLogs();
            startLogStream();
        }
    });
}

async function fetchLogs() {
    const filename = ui.logFileSelect.value;
    if (!filename) return;
    const limit = parseInt(ui.logLimitSelect.value, 10);

    Service.logList(filename, limit, true, (response) => {
        const wasScrolledToBottom = ui.logContainer.scrollHeight - ui.logContainer.clientHeight <= ui.logContainer.scrollTop + 1;

        ui.logContainer.innerHTML = response.map(log =>
            `<div class="row"><span class="log-entry log-${log.level.toLowerCase()}">${log.message}</span></div>`
        ).join('');

        if (wasScrolledToBottom) {
            ui.logContainer.scrollTop = ui.logContainer.scrollHeight;
        }
    });
}

function handleLogFileChange() {
    ui.logContainer.innerHTML = '';
    fetchLogs();
    if (!state.isLogPaused) {
        restartLogStream();
    }
}

function handleLogSettingsChange() {
    if (this.id === 'logLimitSelect') {
        fetchLogs();
    }
    if (!state.isLogPaused) {
        restartLogStream();
    }
}

function restartLogStream() {
    stopLogStream();
    startLogStream();
}

function startLogStream() {
    if (state.logTimerId) clearInterval(state.logTimerId);
    const interval = parseInt(ui.logTimerSelect.value, 10);
    state.logTimerId = setInterval(fetchLogs, interval);
    state.isLogPaused = false;
    ui.btnToggleLogs.innerHTML = '&#x23F8;';
}

function stopLogStream() {
    clearInterval(state.logTimerId);
    state.logTimerId = null;
    state.isLogPaused = true;
    ui.btnToggleLogs.innerHTML = '&#9658;';
}

function toggleLogStream() {
    if (state.isLogPaused) {
        fetchLogs();
        startLogStream();
    } else {
        stopLogStream();
    }
}

/**
 * Показать уведомление на экране
 * @param {string} title - Заголовок
 * @param {string} message - Текст сообщения
 * @param {'info'|'error'|'warning'|'success'} type - Тип уведомления
 */
function showNotification(title, message, type = 'info') {
    const iconMap = {
        error: '❌',
        warning: '⚠️',
        success: '✅',
        info: 'ℹ️'
    };
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-icon">${iconMap[type]}</div>
        <div class="notification-content">
            <div class="notification-title">${title}</div>
            <div class="notification-text">${message}</div>
        </div>
        <div class="notification-close" onclick="this.parentElement.remove()">✕</div>
    `;
    ui.notificationContainer.appendChild(notification);

    setTimeout(() => notification.classList.add('show'), 10);
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 500);
    }, 5000);
}
