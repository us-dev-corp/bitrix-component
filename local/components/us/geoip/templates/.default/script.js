document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('formFindIp').addEventListener('submit', function(event) {
        event.preventDefault();
        document.getElementById('error').classList.add('hide');
        document.getElementById('success').classList.add('hide');

        const isIpAddress = (ipAddress) => /^((\d){1,3}\.){3}(\d){1,3}$/.test(ipAddress);
        const ip = document.getElementById('ip-input').value;
        const service = document.getElementById('service').value;
        if (!isIpAddress(ip)) {
            addError('Не валидный IP!')
            return;
        }
        const request = BX.ajax.runComponentAction('us:geoip', 'callIp', {
            mode: 'class',
            data: {
                ip: ip,
                service: service,
                sessid: BX.message('bitrix_sessid')
            }
        });

        request.then(function (response) {
            if (response.data.ajaxErrors) {
                addError(response.data.ajaxErrors)
            } else {
                addMessage(`
                    <p>IP: ${response.data.result.IP}</p>
                    <p>Город: ${response.data.result.CITY ?? 'не найдено'}</p>
                    <p>Регион: ${response.data.result.REGION ?? 'не найдено'}</p>
                    <p>Страна: ${response.data.result.COUNTRY ?? 'не найдено'}</p>
                    <p>Временная зона: ${response.data.result.TIMEZONE ?? 'не найдено'}</p>
                `);
            }
        });
    });
});

function addError(error)
{
    errorDiv = document.getElementById('error');
    successDiv = document.getElementById('success');

    if (!successDiv.classList.contains('hide')) {
        successDiv.classList.add('hide');
    }

    if (errorDiv.classList.contains('hide')) {
        errorDiv.classList.remove('hide')
    }

    errorDiv.innerHTML = error;
}

function addMessage(text)
{
    errorDiv = document.getElementById('error');
    successDiv = document.getElementById('success');

    if (!errorDiv.classList.contains('hide')) {
        errorDiv.classList.add('hide');
    }

    if (successDiv.classList.contains('hide')) {
        successDiv.classList.remove('hide')
    }

    successDiv.innerHTML = text;
}
