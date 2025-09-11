<div class="wrap">
    <div class="top">
        <div class="logo" aria-hidden="true">
            <img src="/favicon.svg" width="60" alt="">
        </div>

        <div class="title">
            <h1>Nexus</h1>
            <p class="muted">lightweight event broker</p>
        </div>

        <div style="margin-left:auto;display:flex;gap:10px;align-items:center">
            <div class="chip">v<?= resourceData('version') ?></div>
            <div class="tag"><?= resourceData('name') ?></div>
        </div>
    </div>

    <div class="grid">
        <!-- left column: control panel -->
        <div class="card panel">
            <div>
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <div>
                        <div class="k">Node Status</div>
                        <div class="status" style="margin-top:8px">
                            <div id="indicator" class="dot"></div>
                            <div>
                                <div style="font-weight:700" id="ind_name"></div>
                                <div class="muted">status: <span id="ind_condition"></span></div>
                                <div class="muted">PID: <span id="ind_pid"></span></div>
                                <div class="muted"><span id="ind_started_at"></span></div>
                            </div>
                        </div>
                    </div>
                    <div style="text-align:right">
                        <div class="k">Balancer</div>
                        <div style="font-weight:700;font-size:18px" id="ind_balancer">0</div>
                    </div>
                </div>
            </div>

            <div class="controls">
                <button id="btnStart" style="display: none" onclick="nodeStart()" class="btn">Start Core</button>
                <button id="btnStop" style="display: none" onclick="nodeStop()" class="btn ghost">Stop Core</button>
            </div>

            <div style="margin-top:auto;display:flex;gap:8px;align-items:center;justify-content:space-between">
                <div class="muted">Connected: <strong id="connCount">0</strong> clients</div>
                <div class="k">Last sync: <span id="lastSync">—</span></div>
            </div>
        </div>

        <!-- right column: metrics & logs -->
        <div class="card">
            <div class="toolbar">
                <div class="topic">
                    <div style="font-weight:700">Logs:</div>
                    <div class="chip">telemetry</div>
                </div>
                <div style="display:flex;gap:8px">
                    <select id="logParamFileName" onchange="logStream()"></select>
                    <button id="btnClear" class="btn ghost">Start</button>
                </div>
            </div>

            <div class="log" id="log"></div>
        </div>
    </div>
</div>
