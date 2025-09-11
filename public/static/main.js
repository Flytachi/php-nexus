function showNotification(title, message, iconType = 'info') {
    let icon;
    if (iconType === 'error') icon = '❌';
    else if (iconType === 'warning') icon = '⚠';
    else icon = '🛈';
    const container = document.getElementById('notification-container');

    // Создаем уведомление
    const notification = document.createElement('div');
    notification.className = 'notification';

    notification.innerHTML = `
            <div class="notification-icon">${icon}</div>
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                <div class="notification-text">${message}</div>
            </div>
            <div class="notification-close" onclick="this.parentElement.remove()">✕</div>
        `;
    container.appendChild(notification);
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 500);
    }, 5000);
}

function nodeStatus() {
    Service.serviceStatus(true, (response) => {
        document.getElementById('ind_name').innerText = response.className;
        document.getElementById('ind_condition').innerText = response.condition;
        document.getElementById('ind_pid').innerText = response.pid;
        document.getElementById('ind_balancer').innerText = response.balancer;
        document.getElementById('ind_started_at').innerText = response.startedAt;
        document.getElementById('indicator').classList.remove('offline', 'online');
        if (response.condition === 'active') {
            document.getElementById('indicator').classList.add('online');
            document.getElementById('btnStart').style.display = 'none';
            document.getElementById('btnStop').style.display = 'block';
        } else {
            document.getElementById('indicator').classList.add('offline');
            document.getElementById('btnStart').style.display = 'block';
            document.getElementById('btnStop').style.display = 'none';
        }
    });
}

function nodeStart() {
    let btn = document.getElementById('btnStart');
    btn.style.display = 'none';
    Service.serviceStart(true, (response) => {
        nodeStatus()
    });
}

function nodeStop() {
    let btn = document.getElementById('btnStop');
    btn.style.display = 'none';
    Service.serviceStop(true, (response) => {
        nodeStatus()
    });
}

let logTimerId;

function logStream(reset = false) {
    if (reset) unsetTimer();
    if (logTimerId === undefined) {
        setTimer();
    }
    showLogs();
}

function showLogs() {
    let filename = document.querySelector("#logParamFileName").value;
    let limit =  1000;

    Service.logList(filename, limit, true, (response) => {
        console.log(response);
        let logEl = document.getElementById('log');
        let result = '';
        response.forEach(function(element) {
            result += `<div class="row"><span class="log-entry log-${element.level}">${element.message}</span></div>`;
        });
        logEl.innerHTML = result;
        logEl.scrollTo(0, logEl.scrollHeight);
    });
}

function logUpdateFileList() {
    Service.logFiles(true, (response) => {
        let select = document.getElementById('logParamFileName');
        response.forEach(function(element, i) {
            let option = document.createElement('option');
            option.text = element;
            option.value = element;
            if (i === 0) option.selected = true;
            select.append(option);
        });
        showLogs();
    });
}

function setTimer() {
    let limit = 5000;
    logTimerId = setInterval(showLogs, limit);
    let btn = document.querySelector("#btnClear");
    if (btn) {
        btn.innerHTML = 'stop'
        btn.onclick = unsetTimer;
    }
}

function unsetTimer() {
    clearInterval(logTimerId);
    let btn = document.querySelector("#btnClear");
    if (btn) {
        btn.innerHTML = 'start'
        btn.onclick = setTimer;
    }
}

$(document).ready(function() {
    nodeStatus();

    // service
    setInterval(()=> {
        nodeStatus();
    },7000);

    // logs
    logUpdateFileList();
    setTimer();
})
