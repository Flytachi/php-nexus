<main class="grid">

    <!-- LEFT PANEL: Control & Status -->
    <section class="card panel">

        <!-- Status Refresh Control (Top) -->
        <div class="refresh-control">
            <div class="k">Status Refresh</div>
            <select id="statusRefresh">
                <option value="3000">3s</option>
                <option value="5000" selected>5s</option>
                <option value="7000">7s</option>
            </select>
        </div>

        <!-- Main Status Block (Emphasized) -->
        <div class="status-block">
            <div class="status-indicator">
                <div id="indicator" class="dot offline"></div>
            </div>
            <div class="status-main">
                <div class="status-title">
                    <div id="statusName" class="status-name">Offline</div>
                    <div id="statusClass" class="status-class">—</div>
                </div>
                <div class="status-condition">
                    <span class="condition-label">Condition:</span>
                    <span id="statusCondition" class="condition-value">Unknown</span>
                </div>
                <div class="status-time">
                    <span class="time-label">Started:</span>
                    <span id="startedAt" class="time-value">—</span>
                </div>
            </div>
            <div class="status-balancer">
                <div class="k">Balancer</div>
                <div id="balancerValue" class="balancer-value">0</div>
            </div>
        </div>

        <!-- Info Section (System Stats) - Only when ACTIVE -->
        <div id="infoSection" class="info-section" style="display: none;">
            <div class="k">System Info</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">CPU</span>
                    <span id="infoCpu" class="info-value">—</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Memory</span>
                    <span id="infoMem" class="info-value">—</span>
                </div>
                <div class="info-item">
                    <span class="info-label">RSS</span>
                    <span id="infoRss" class="info-value">—</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Uptime</span>
                    <span id="infoUptime" class="info-value">—</span>
                </div>
                <div class="info-item">
                    <span class="info-label">User</span>
                    <span id="infoUser" class="info-value">—</span>
                </div>
                <div class="info-item">
                    <span class="info-label">PID</span>
                    <span id="infoPid" class="info-value">—</span>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="stats-list">
            <div class="stat-item-kv">
                <span class="k">Ready</span>
                <span id="statReady" class="stat-value">0</span>
            </div>
            <div class="stat-item-kv">
                <span class="k">Unacked</span>
                <span id="statUnacked" class="stat-value">0</span>
            </div>
            <div class="stat-item-kv">
                <span class="k">Total</span>
                <span id="statTotal" class="stat-value">0</span>
            </div>
            <div class="stat-item-kv">
                <span class="k">Consumers</span>
                <span id="statConsumers" class="stat-value">0</span>
            </div>
        </div>

        <!-- Units Section -->
        <div class="units-section">
            <div class="k">Units (<span id="units_count">0</span>)</div>
            <div class="units-container">
                <span id="units-string" class="units-string"></span>
            </div>
        </div>

        <div class="controls">
            <button id="btnStart" class="btn">Start Service</button>
            <button id="btnStop" class="btn ghost">Stop Service</button>
        </div>

    </section>

    <!-- RIGHT PANEL: Tabbed Content -->
    <section class="card tabs-container">

        <!-- Tab Navigation -->
        <div class="tabs-nav">
            <button class="tab-btn active" data-tab="info">
                <span class="tab-icon">ℹ️</span>
                <span class="tab-label">Information</span>
            </button>
            <button class="tab-btn" data-tab="units">
                <span class="tab-icon">⚙️</span>
                <span class="tab-label">Units</span>
            </button>
            <button class="tab-btn" data-tab="logs">
                <span class="tab-icon">📋</span>
                <span class="tab-label">Logs</span>
            </button>
            <button class="tab-btn" data-tab="metrics">
                <span class="tab-icon">📊</span>
                <span class="tab-label">Metrics</span>
            </button>
            <button class="tab-btn" data-tab="config">
                <span class="tab-icon">⚙️</span>
                <span class="tab-label">Config</span>
            </button>
        </div>

        <!-- Tab Content -->
        <div class="tabs-content">

            <!-- Information Tab -->
            <div class="tab-pane active" id="info">
                <div class="config-content">
                    <?php foreach (resourceData('information') as $label => $value): ?>
                        <div class="config-item">
                            <div class="config-label"><?= $label ?></div>
                            <div class="config-value"><?= $value ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Units Tab -->
            <div class="tab-pane" id="units">
                <div class="units-toolbar">
                    <div class="units-info">
                        <span class="k">Total Units: <strong id="unitsTotal">0</strong></span>
                    </div>
                    <div class="units-controls">
                        <span class="k">Refresh:</span>
                        <select id="unitsRefresh">
                            <option value="3000">3s</option>
                            <option value="5000" selected>5s</option>
                            <option value="10000">10s</option>
                        </select>
                        <button id="btnToggleUnits" class="btn ghost" style="width: 50px" title="Pause/Resume">⏸</button>
                    </div>
                </div>
                <div class="units-table-container">
                    <table class="units-table">
                        <thead>
                        <tr>
                            <th>PID</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>CPU</th>
                            <th>Memory</th>
                            <th>RSS (KB)</th>
                            <th>Uptime</th>
                            <th>Started</th>
                            <th>Command</th>
                        </tr>
                        </thead>
                        <tbody id="unitsTableBody">
                        <tr>
                            <td colspan="8" class="empty-message">No units available</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Logs Tab -->
            <div class="tab-pane" id="logs">
                <div class="toolbar">
                    <div class="topic">
                        <strong>Log File:</strong>
                        <select id="logFile">
                            <option value="">Select log file...</option>
                        </select>
                    </div>
                    <div class="log-controls">
                        <span class="k">Limit:</span>
                        <select id="logLimit">
                            <option value="500">500</option>
                            <option value="1000" selected>1000</option>
                            <option value="3000">3000</option>
                        </select>
                        <span class="k">Refresh:</span>
                        <select id="logRefresh">
                            <option value="5000">5s</option>
                            <option value="10000" selected>10s</option>
                            <option value="20000">20s</option>
                        </select>
                        <button id="btnToggleLogs" class="btn ghost" style="width: 50px" title="Pause/Resume">⏸</button>
                    </div>
                </div>
                <div class="log" id="logContainer"></div>
            </div>

            <!-- Metrics Tab -->
            <div class="tab-pane" id="metrics">
                <div class="metrics-content">
                    <div class="metric-card">
                        <div class="metric-label">CPU Usage</div>
                        <div class="metric-value">—</div>
                        <div class="metric-bar">
                            <div class="metric-fill" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Memory Usage</div>
                        <div class="metric-value">—</div>
                        <div class="metric-bar">
                            <div class="metric-fill" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Ready Items</div>
                        <div class="metric-value" id="metricReady">0</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Total Items</div>
                        <div class="metric-value" id="metricTotal">0</div>
                    </div>
                </div>
            </div>

            <!-- Config Tab -->
            <div class="tab-pane" id="config">
                <div class="config-content">
                    <div class="config-item">
                        <div class="config-label">Service Class</div>
                        <div class="config-value" id="configClass">—</div>
                    </div>
                    <div class="config-item">
                        <div class="config-label">Status</div>
                        <div class="config-value" id="configStatus">—</div>
                    </div>
                    <div class="config-item">
                        <div class="config-label">Balancer Load</div>
                        <div class="config-value" id="configBalancer">0</div>
                    </div>
                    <div class="config-item">
                        <div class="config-label">Started At</div>
                        <div class="config-value" id="configStartedAt">—</div>
                    </div>
                </div>
            </div>

        </div>

    </section>

</main>

<script>
    // ============================================
    // INITIALIZATION
    // ============================================
    document.addEventListener('DOMContentLoaded', () => {
        checkAuth();
        if (document.querySelector('.grid')) {
            initializeEventListeners();
            updateStatus();
            setupStatusUpdateInterval();
            initializeTabs();
        }
    });
</script>