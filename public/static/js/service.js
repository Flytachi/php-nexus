class Service
{
    static aXhr = [];

    static cXhrInit(key = 'default') {
        // if(this.aXhr[key] && this.aXhr[key].readyState !== 4)
            // this.aXhr[key].abort();
    }

    static isAuth(response) {
        if (response.status === 401) {
            window.location.href = '/web/login';
        }
    }

    static sessionExist(
        notifyError = true,
        callbackSuccess = (response) => {},
        callbackError = (response) => {},
    ) {
        this.cXhrInit('auth')

        this.aXhr['auth'] = $.ajax({
            type: "GET",
            url: "/web/auth",
            contentType: "application/json",
            dataType: "json",
            headers: {
                "Accept": "application/json"
            },
            success: function (response) {
                callbackSuccess(response)
            },
            error: function (response) {
                console.log(response);
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

    static login(
        username, password,
        notifyError = true,
        callbackSuccess = (response) => {},
        callbackError = (response) => {},
    ) {
        this.cXhrInit('auth')

        this.aXhr['auth'] = $.ajax({
            type: "POST",
            url: "/web/auth",
            contentType: "application/json",
            dataType: "json",
            headers: {
                "Accept": "application/json"
            },
            data: JSON.stringify({
                username,
                password
            }),
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

    static logout(
        notifyError = true,
        callbackSuccess = (response) => {},
        callbackError = (response) => {},
    ) {
        this.cXhrInit('auth')

        this.aXhr['auth'] = $.ajax({
            type: "DELETE",
            url: "/web/auth",
            contentType: "application/json",
            dataType: "json",
            headers: {
                "Accept": "application/json"
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

    static serviceStatus(
        notifyError = true,
        callbackSuccess = (response) => {},
        callbackError = (response) => {},
    ) {
        this.cXhrInit('service')

        this.aXhr['service'] = $.ajax({
            type: "GET",
            url: "web/api/service",
            contentType: "application/json",
            dataType: "json",
            headers: {
                "Accept": "application/json",
            },
            success: function (response) {
                callbackSuccess(response)
            },
            error: function (response) {
                Service.isAuth(response);
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
            url: "/web/api/service",
            contentType: "application/json",
            dataType: "json",
            headers: {
                "Accept": "application/json",
            },
            success: function (response) {
                callbackSuccess(response)
            },
            error: function (response) {
                Service.isAuth(response);
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
            url: "/web/api/service",
            contentType: "application/json",
            dataType: "json",
            headers: {
                "Accept": "application/json"
            },
            success: function (response) {
                callbackSuccess(response)
            },
            error: function (response) {
                Service.isAuth(response);
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
            url: "/web/api/logs/files",
            contentType: "application/json",
            dataType: "json",
            headers: {
                "Accept": "application/json"
            },
            success: function (response) {
                callbackSuccess(response)
            },
            error: function (response) {
                Service.isAuth(response);
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
            url: "/web/api/logs",
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
                Service.isAuth(response);
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

    static unitList(
        notifyError = true,
        callbackSuccess = (response) => {},
        callbackError = (response) => {},
    ) {
        this.cXhrInit('units')

        this.aXhr['units'] = $.ajax({
            type: "GET",
            url: "/web/api/units",
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
                Service.isAuth(response);
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