class Service
{
    static aXhr = [];

    static cXhrInit(key = 'default') {
        if(this.aXhr[key] && this.aXhr[key].readyState !== 4)
            this.aXhr[key].abort();
    }

    static serviceStatus(
        notifyError = true,
        callbackSuccess = (response) => {},
        callbackError = (response) => {},
    ) {
        this.cXhrInit('service')

        this.aXhr['service'] = $.ajax({
            type: "GET",
            url: "/api/remote/status",
            contentType: "application/json",
            dataType: "json",
            headers: {
                "Accept": "application/json",
                "User-Agent": "sentinel"
            },
            success: function (response) {
                callbackSuccess(response)
            },
            error: function (response) {
                if (notifyError) {
                    showNotification(
                        'Error',
                        response.responseJSON.message ?? 'Unknown error',
                        'error'
                    );
                }
                callbackError(response)
            }
        });
    }

    static serviceStart(
        notifyError = true,
        callbackSuccess = (response) => {},
        callbackError = (response) => {},
    ) {
        this.cXhrInit('service')

        this.aXhr['service'] = $.ajax({
            type: "PATCH",
            url: "/api/remote",
            contentType: "application/json",
            dataType: "json",
            headers: {
                "Accept": "application/json",
                "User-Agent": "sentinel"
            },
            success: function (response) {
                callbackSuccess(response)
            },
            error: function (response) {
                if (notifyError) {
                    showNotification(
                        'Error',
                        response.responseJSON.message ?? 'Unknown error',
                        'error'
                    );
                }
                callbackError(response)
            }
        });
    }

    static serviceStop(
        notifyError = true,
        callbackSuccess = (response) => {},
        callbackError = (response) => {},
    ) {
        this.cXhrInit('service')

        this.aXhr['service'] = $.ajax({
            type: "DELETE",
            url: "/api/remote",
            contentType: "application/json",
            dataType: "json",
            headers: {
                "Accept": "application/json",
                "User-Agent": "sentinel"
            },
            success: function (response) {
                callbackSuccess(response)
            },
            error: function (response) {
                if (notifyError) {
                    showNotification(
                        'Error',
                        response.responseJSON.message ?? 'Unknown error',
                        'error'
                    );
                }
                callbackError(response)
            }
        });
    }

    static logFiles(
        notifyError = true,
        callbackSuccess = (response) => {},
        callbackError = (response) => {},
    ) {
        this.cXhrInit('logs_f')

        this.aXhr['logs_f'] = $.ajax({
            type: "GET",
            url: "/api/logs/files",
            contentType: "application/json",
            dataType: "json",
            headers: {
                "Accept": "application/json",
                "User-Agent": "sentinel"
            },
            success: function (response) {
                callbackSuccess(response)
            },
            error: function (response) {
                if (notifyError) {
                    showNotification(
                        'Error',
                        response.responseJSON.message ?? 'Unknown error',
                        'error'
                    );
                }
                callbackError(response)
            }
        });
    }

    static logList(
        filename,
        limit = 1000,
        notifyError = true,
        callbackSuccess = (response) => {},
        callbackError = (response) => {},
    ) {
        this.cXhrInit('logs')

        this.aXhr['logs'] = $.ajax({
            type: "GET",
            url: "/api/logs",
            contentType: "application/json",
            dataType: "json",
            headers: {
                "Accept": "application/json",
                "User-Agent": "sentinel"
            },
            data: {filename, limit},
            success: function (response) {
                callbackSuccess(response)
            },
            error: function (response) {
                if (notifyError) {
                    showNotification(
                        'Error',
                        response.responseJSON.message ?? 'Unknown error',
                        'error'
                    );
                }
                callbackError(response)
            }
        });
    }

}