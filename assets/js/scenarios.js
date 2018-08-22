var WebToursLoaded = false;

function EnjoyHintRun(scenario)
{
    if (WebToursLoaded == false) {
        $.post('/index/check-webtours', {}, function (data) {
            if (data.success) {

                for (var i in data.data) {
                    delete scenarios[data.data[i]];
                }

                _EnjoyHintRun(scenario);
            }

            WebToursLoaded = true;
        });
    } else {
        _EnjoyHintRun(scenario);
    }
}

function _EnjoyHintRun(scenario)
{
    if (scenarios[scenario]) {
        var EnjoyHintInstance = new EnjoyHint({});

        EnjoyHintInstance.set(scenarios[scenario]);

        EnjoyHintInstance.run();

        delete scenarios[scenario];

        $.post('/index/see-webtour', {scenario: scenario});
    }
}