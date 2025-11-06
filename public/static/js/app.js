/**
 * Web Console Application
 * Lightweight, responsive control panel with tabbed interface
 */

// ============================================
// STATE MANAGEMENT
// ============================================

const state = {
    logTimerId: null,
    isLogPaused: false,
    unitsTimerId: null,
    isUnitsPaused: false,
    currentTab: 'logs',
    statusUpdateInterval: null,
    statusUpdateRate: 5000,
};

// ============================================
// UI ELEMENTS CACHE
// ============================================

const ui = {
    // Header
    btnLogout: document.getElementById('btnLogout'),

    // Status Panel
    indicator: document.getElementById('indicator'),
    statusName: document.getElementById('statusName'),
    statusClass: document.getElementById('statusClass'),
    statusCondition: document.getElementById('statusCondition'),
    startedAt: document.getElementById('startedAt'),
    balancerValue: document.getElementById('balancerValue'),

    // Info Section (System Stats) - Only shown when ACTIVE
    infoSection: document.getElementById('infoSection'),
    infoCpu: document.getElementById('infoCpu'),
    infoMem: document.getElementById('infoMem'),
    infoRss: document.getElementById('infoRss'),
    infoUptime: document.getElementById('infoUptime'),
    infoUser: document.getElementById('infoUser'),
    infoPid: document.getElementById('infoPid'),

    // Stats
    statReady: document.getElementById('statReady'),
    statUnacked: document.getElementById('statUnacked'),
    statTotal: document.getElementById('statTotal'),
    statConsumers: document.getElementById('statConsumers'),

    // Status Refresh Control
    statusRefresh: document.getElementById('statusRefresh'),

    // Controls
    btnStart: document.getElementById('btnStart'),
    btnStop: document.getElementById('btnStop'),

    // Logs Tab
    logFile: document.getElementById('logFile'),
    logLimit: document.getElementById('logLimit'),
    logRefresh: document.getElementById('logRefresh'),
    btnToggleLogs: document.getElementById('btnToggleLogs'),
    logContainer: document.getElementById('logContainer'),

    // Units Tab
    unitsTotal: document.getElementById('unitsTotal'),
    unitsRefresh: document.getElementById('unitsRefresh'),
    btnToggleUnits: document.getElementById('btnToggleUnits'),
    unitsTableBody: document.getElementById('unitsTableBody'),

    // Metrics Tab
    metricReady: document.getElementById('metricReady'),
    metricTotal: document.getElementById('metricTotal'),

    // Tabs
    tabButtons: document.querySelectorAll('.tab-btn'),
    tabPanes: document.querySelectorAll('.tab-pane'),

    // Notifications
    notificationContainer: document.getElementById('notificationContainer'),
};


// ============================================
// EVENT LISTENERS
// ============================================

function initializeEventListeners() {
    // Header
    ui.btnLogout?.addEventListener('click', handleLogout);

    // Controls
    ui.btnStart?.addEventListener('click', handleStartService);
    ui.btnStop?.addEventListener('click', handleStopService);

    // Status Refresh Control
    ui.statusRefresh?.addEventListener('change', setupStatusUpdateInterval);

    // Logs
    ui.logFile?.addEventListener('change', handleLogFileChange);
    ui.btnToggleLogs?.addEventListener('click', toggleLogStream);
    ui.logLimit?.addEventListener('change', handleLogSettingsChange);
    ui.logRefresh?.addEventListener('change', handleLogSettingsChange);

    // Units
    ui.unitsRefresh?.addEventListener('change', setupUnitsUpdateInterval);
    ui.btnToggleUnits?.addEventListener('click', toggleUnitsStream);
}

// ============================================
// TAB MANAGEMENT
// ============================================

function initializeTabs() {
    ui.tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabName = button.getAttribute('data-tab');
            switchTab(tabName);
        });
    });
}

function switchTab(tabName) {
    // Update button states
    ui.tabButtons.forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-tab') === tabName);
    });

    // Update pane visibility
    ui.tabPanes.forEach(pane => {
        pane.classList.toggle('active', pane.id === tabName);
    });

    state.currentTab = tabName;

    // Trigger tab-specific initialization if needed
    setupLogUpdateInterval();
    setupUnitsUpdateInterval();

    if (tabName === 'logs') {
        initializeLogs();
    } else if (tabName === 'units') {
        updateUnits();
    } else if (tabName === 'metrics') {
        updateMetrics();
    } else if (tabName === 'config') {
        updateConfig();
    }
}

// ============================================
// STATUS & STATS MANAGEMENT
// ============================================

function setupStatusUpdateInterval() {
    if (state.statusUpdateInterval) {
        clearInterval(state.statusUpdateInterval);
    }

    state.statusUpdateRate = parseInt(ui.statusRefresh.value, 10);
    state.statusUpdateInterval = setInterval(updateStatus, state.statusUpdateRate);
}

async function updateStatus() {
    Service.serviceStatus(true, (response) => {
        const status = response.info.status;
        const stats = response.info.stats;
        const isPassive = status.condition === 'PASSIVE';

        // Update main status info
        ui.statusName.textContent = status.pid || 'N/A';
        ui.statusClass.textContent = status.className || '—';
        ui.statusCondition.textContent = status.condition || 'Unknown';
        ui.statusCondition.className = `status-badge status-${status.condition}`;
        ui.startedAt.textContent = status.startedAt || '—';
        ui.balancerValue.textContent = status.balancer || '0';

        // Update indicator
        ui.indicator.classList.toggle('online', !isPassive);
        ui.indicator.classList.toggle('offline', isPassive);

        // Handle info.stats based on condition
        // If PASSIVE, info.stats will be empty, so hide the info section
        if (isPassive || !stats || Object.keys(stats).length === 0) {
            // Hide info section when PASSIVE or stats is empty
            ui.infoSection.style.display = 'none';
        } else {
            // Show info section when ACTIVE and stats available
            ui.infoSection.style.display = 'block';

            // Update system info (from stats)
            ui.infoCpu.textContent = (stats.cpu || 0) + '%';
            ui.infoMem.textContent = (stats.mem || 0).toFixed(1) + '%';
            ui.infoRss.textContent = (stats.rssKb || 0) + ' KB';
            ui.infoUptime.textContent = stats.etime || '—';
            ui.infoUser.textContent = stats.user || '—';
            ui.infoPid.textContent = stats.pid || '—';
        }

        // Update buttons
        ui.btnStart.style.display = !isPassive ? 'none' : 'block';
        ui.btnStop.style.display = !isPassive ? 'block' : 'none';

        // Update stats
        ui.statReady.textContent = response.stats.ready || '0';
        ui.statUnacked.textContent = response.stats.unacked || '0';
        ui.statTotal.textContent = response.stats.total || '0';
        ui.statConsumers.textContent = response.stats.consumers || '0';

        // Update units
        const units = response.units || [];
        document.getElementById('units_count').textContent = units.length;
        const unitsStringContainer = document.getElementById('units-string');
        if (units.length > 0) {
            unitsStringContainer.textContent = units.join(', ');
        } else {
            unitsStringContainer.textContent = 'No active units';
        }
    }, (response) => {
        console.error('Failed to update status:', response);
        ui.statusCondition.textContent = 'Error';
        ui.indicator.classList.remove('online');
        ui.indicator.classList.add('offline');
        ui.infoSection.style.display = 'none';
        // ui.indicator.classList.remove('online');
        // ui.indicator.classList.add('offline');
        // ui.indCondition.textContent = 'ошибка';
        // document.getElementById('stats_ready').textContent = '—';
        // document.getElementById('stats_unacked').textContent = '—';
        // document.getElementById('stats_total').textContent = '—';
        // document.getElementById('stats_consumers').textContent = '—';
        document.getElementById('units_count').textContent = '0';
        document.getElementById('units_list').innerHTML = '<div class="muted">Нет данных</div>';
    });
}

// ============================================
// SERVICE CONTROL
// ============================================

async function handleStartService() {
    ui.btnStart.disabled = true;
    ui.btnStart.innerHTML = '<div class="spinner"></div>';
    Service.serviceStart(false, async (response) => {
        showNotification('Success', 'The command to launch the kernel has been sent', 'success');
        await updateStatus();
        ui.btnStart.disabled = false;
        ui.btnStart.innerHTML = 'Start Service';
    }, (response) => {
        showNotification('Start error', response.responseJSON.message, 'error');
        ui.btnStart.disabled = false;
        ui.btnStart.innerHTML = 'Start Service';
    });
}

async function handleStopService() {
    ui.btnStop.disabled = true;
    ui.btnStop.innerHTML = '<div class="spinner"></div>';
    Service.serviceStop(false, async (response) => {
        showNotification('Success', 'The command to stop the kernel has been sent', 'success');
        await updateStatus();
        ui.btnStop.disabled = false;
        ui.btnStop.innerHTML = 'Stop Service';
    }, (response) => {
        showNotification('Stop error', response.responseJSON.message, 'error');
        ui.btnStop.disabled = false;
        ui.btnStop.innerHTML = 'Stop Service';
    });
}

// ============================================
// LOG MANAGEMENT
// ============================================

function setupLogUpdateInterval() {
    stopLogStream();
    if (state.currentTab === 'logs') {
        startLogStream();
    }
}

async function initializeLogs() {
    Service.logFiles(true, async (response) => {
        ui.logFile.innerHTML = '<option value="">Select log file...</option>';
        response.forEach(file => {
            const option = document.createElement('option');
            option.value = file;
            option.textContent = file;
            ui.logFile.appendChild(option);
        });

        if (response.length > 0) {
            ui.logFile.value = response[0];
            await fetchLogs();
            startLogStream();
        }
    });
}

async function fetchLogs() {
    const filename = ui.logFile.value;
    if (!filename) return;

    const limit = parseInt(ui.logLimit.value, 10);

    ui.btnToggleLogs.disabled = true;
    ui.btnToggleLogs.innerHTML = '<div class="spinner"></div>';
    Service.logList(filename, limit, true, (response) => {
        const wasScrolledToBottom =
            ui.logContainer.scrollHeight - ui.logContainer.clientHeight <=
            ui.logContainer.scrollTop + 1;

        ui.logContainer.innerHTML = response.map(log =>
            `<div class="row"><span class="log-entry log-${log.level.toLowerCase()}">${escapeHtml(log.message)}</span></div>`
        ).join('');

        if (wasScrolledToBottom) {
            ui.logContainer.scrollTop = ui.logContainer.scrollHeight;
        }
        ui.btnToggleLogs.innerHTML = '⏸';
        ui.btnToggleLogs.disabled = false;
    }, (response) => {
        ui.btnToggleLogs.innerHTML = '⏸';
        ui.btnToggleLogs.disabled = false;
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
    if (this.id === 'logLimit') {
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
    const interval = parseInt(ui.logRefresh.value, 10);
    state.logTimerId = setInterval(fetchLogs, interval);
    state.isLogPaused = false;
    ui.btnToggleLogs.innerHTML = '⏸';
}

function stopLogStream() {
    if (state.logTimerId) clearInterval(state.logTimerId);
    state.logTimerId = null;
    state.isLogPaused = true;
    ui.btnToggleLogs.innerHTML = '▶';
}

function toggleLogStream() {
    if (state.isLogPaused) {
        fetchLogs();
        startLogStream();
    } else {
        stopLogStream();
    }
}

// ============================================
// UNITS MANAGEMENT
// ============================================

function setupUnitsUpdateInterval() {
    stopUnitsStream();
    if (state.currentTab === 'units') {
        startUnitsStream();
    }
}

async function updateUnits() {
    ui.btnToggleUnits.disabled = true;
    ui.btnToggleUnits.innerHTML = '<div class="spinner"></div>';
    Service.unitList(true, (response) => {
        if (response.length === 0) {
            ui.unitsTableBody.innerHTML = '<tr><td colspan="9" class="empty-message">No units available</td></tr>';
            ui.btnToggleUnits.innerHTML = '⏸';
            ui.btnToggleUnits.disabled = false;
            return;
        }

        ui.unitsTableBody.innerHTML = response.map(unit => {
            return `
                <tr>
                    <td><strong>${unit.status.pid}</strong></td>
                    <td>${unit.stats.user}</td>
                    <td><span class="status-badge status-${unit.status.condition}">${unit.status.condition}</span></td>
                    <td>${unit.stats.cpu}%</td>
                    <td>${unit.stats.mem.toFixed(1)}%</td>
                    <td>${unit.stats.rssKb}</td>
                    <td>${unit.stats.etime}</td>
                    <td>${unit.status.startedAt}</td>
                    <td><code>${escapeHtml(unit.stats.command)}</code></td>
                </tr>
            `;
        }).join('');
        ui.btnToggleUnits.innerHTML = '⏸';
        ui.btnToggleUnits.disabled = false;
    }, (response) => {
        ui.btnToggleUnits.innerHTML = '⏸';
        ui.btnToggleUnits.disabled = false;
        console.error('Failed to update units:', error);
        ui.unitsTableBody.innerHTML = '<tr><td colspan="9" class="empty-message">Error loading units</td></tr>';
    });
}

function startUnitsStream() {
    if (state.unitsTimerId) clearInterval(state.unitsTimerId);
    const interval = parseInt(ui.unitsRefresh.value, 10);
    state.unitsTimerId = setInterval(updateUnits, interval);
    state.isUnitsPaused = false;
    ui.btnToggleUnits.innerHTML = '⏸';
}

function stopUnitsStream() {
    if (state.unitsTimerId) clearInterval(state.unitsTimerId);
    state.unitsTimerId = null;
    state.isUnitsPaused = true;
    ui.btnToggleUnits.innerHTML = '▶';
}

function toggleUnitsStream() {
    if (state.isUnitsPaused) {
        updateUnits();
        startUnitsStream();
    } else {
        stopUnitsStream();
    }
}

// ============================================
// TAB-SPECIFIC UPDATES
// ============================================

function updateMetrics() {
    // Metrics are updated from status data
    // Additional metric updates can be added here
}

function updateConfig() {
    // Config is updated from status data
    // Additional config updates can be added here
}

// ============================================
// NOTIFICATIONS
// ============================================

/**
 * Show notification message
 * @param {string} title - Notification title
 * @param {string} message - Notification message
 * @param {'info'|'error'|'warning'|'success'} type - Notification type
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
            <div class="notification-title">${escapeHtml(title)}</div>
            <div class="notification-text">${escapeHtml(message)}</div>
        </div>
        <div class="notification-close">✕</div>
    `;

    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 500);
    });

    ui.notificationContainer.appendChild(notification);

    // Trigger animation
    setTimeout(() => notification.classList.add('show'), 10);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 500);
    }, 5000);
}

// ============================================
// UTILITIES
// ============================================

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// ============================================
// CLEANUP
// ============================================

window.addEventListener('beforeunload', () => {
    if (state.logTimerId) clearInterval(state.logTimerId);
    if (state.unitsTimerId) clearInterval(state.unitsTimerId);
    if (state.statusUpdateInterval) clearInterval(state.statusUpdateInterval);
});
