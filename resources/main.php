<main class="grid">

    <section class="card panel">
        <div>
            <div class="status-header">
                <div>
                    <div class="k">Node status</div>
                    <div class="status">
                        <div id="indicator" class="dot offline"></div>
                        <div>
                            <div id="ind_name" class="status-name">...</div>
                            <div class="muted">Status: <span id="ind_condition">...</span></div>
                            <div class="muted">PID: <span id="ind_pid">...</span></div>
                        </div>
                    </div>
                </div>
                <div class="balancer">
                    <div class="k">Balancer</div>
                    <div id="ind_balancer" class="balancer-value">0</div>
                </div>
            </div>
            <div class="muted" style="font-size: 12px; margin-top: 4px;">Launched: <span id="ind_started_at">...</span></div>
        </div>

        <div class="stats-list">
            <div class="stat-item-kv">
                <span class="k">Ready</span>
                <span id="stats_ready" class="stat-value">0</span>
            </div>
            <div class="stat-item-kv">
                <span class="k">Unacked</span>
                <span id="stats_unacked" class="stat-value">0</span>
            </div>
            <div class="stat-item-kv">
                <span class="k">Total</span>
                <span id="stats_total" class="stat-value">0</span>
            </div>
            <div class="stat-item-kv">
                <span class="k">Consumers</span>
                <span id="stats_consumers" class="stat-value">0</span>
            </div>
        </div>

        <div class="pids-section">
            <div class="k">Units (<span id="pids_count">0</span>)</div>
            <div class="pids-container">
                <span id="pids-string" class="pids-string"></span>
            </div>
        </div>

        <div class="controls">
            <button id="btnStart" class="btn">Run the kernel</button>
            <button id="btnStop" class="btn ghost">Stop the kernel</button>
        </div>

    </section>

    <section class="card">
        <div class="toolbar">
            <div class="topic">
                <strong>Logs:</strong>
                <select id="logParamFileName"></select>
            </div>
            <div class="log-controls">
                <span class="k">Limit:</span>
                <select id="logLimitSelect">
                    <option value="500">500</option>
                    <option value="1000" selected>1000</option>
                    <option value="3000">3000</option>
                </select>
                <span class="k">Timer (sec):</span>
                <select id="logTimerSelect">
                    <option value="5000">5</option>
                    <option value="10000" selected>10</option>
                    <option value="20000">20</option>
                </select>
                <button id="btnToggleLogs" class="btn ghost" style="width: 50px">&#x23F8;</button>
            </div>
        </div>
        <div class="log" id="log"></div>
    </section>

</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        checkAuth();

        if (document.querySelector('.grid')) {
            main();
        }
    });
</script>