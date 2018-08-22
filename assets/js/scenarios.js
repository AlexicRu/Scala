var WebToursLoaded = false;

var scenarios = {
    dashboard: [
        {'next .settings' : 'Настройки пользователя'},
        {'click .menu_item_clients' : 'Посмотреть список закрепленных фирм'}
    ],
    clients: [
        {'click .client:first>.fr.btn' : 'Посмотреть список закрепленных фирм'},
        {
            selector:'.client:first tr:first a',
            event:'click',
            description:'Открыть реквизиты договора'
        }
    ],
    client: [
        {'next [ajax_tab="contract"]': 'Настроки договора'},
        {'click [ajax_tab="cards"]': 'Список карт, закрепленных за договором'}
    ],
    cards: [
        {'next .tabs_cards>.tabs_v' : 'Полный список карт'},
        {'click [ajax_tab="account"]': 'Данные по лицевому счету договора'}
    ],
    account: [
        {'next .webtour-account' : 'Баланс по договору, платежи и обороты'},
        {'click [ajax_tab="reports"]': 'Построить отчеты'}
    ],
    reports: [
        {
            'click .webtour-reports' : 'Выбрать шаблон, дату, формат и сформировать',
            'showNext': false,
            'skipButton' : {text: "End"}
        }
    ]
};

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