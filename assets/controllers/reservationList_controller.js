import {Controller} from '@hotwired/stimulus';
import $ from 'jquery'

export default class extends Controller {

    is_admin = 0
    admin_info = [];
    date
    connect() {
        if ($('#isAdmin').text() === '1')
        {
            this.is_admin = 1;
            this.admin_info['password'] = $('#user_password').text()
            this.admin_info['id'] = $('#user_id').text()

            this.setDate()
        }
    }

    setDate() {
        const service_date = $('#service_date')
        const here = this
        service_date.on('change', function () {
            here.date = service_date.val()
            here.setServices(service_date.val())
        })
        here.setServices(service_date.val())
    }

    setServices(date)
    {
        const user_info = this.admin_info
        getData(
            '/admin/get_services_reservations',
            user_info,
            JSON.stringify({'date': date}),
            function (response) {
                setServices(response, date, user_info)
            },
            function (response) {
                alert(response)
            },

        )
    }
}

function setServices(arr, date, user_info)
{
    const day_div = $('#day_service')
    const night_div = $('#night_service')
    day_div.children().remove()
    night_div.children().remove()


    if (arr['day'])
        day_div.append(setServiceTab(arr['day'], 'midi', date, user_info))
    else
        day_div.append(createDefaultService('day', 'midi', 1, date, user_info))
    if (arr['night'])
        night_div.append(setServiceTab(arr['night'], 'soir', date, user_info))
    else
        night_div.append(createDefaultService('night', 'soir', 2, date, user_info))



}

function createDefaultService(id, type_name, type_key, date, user_info)
{
    const arr = {
        'id': id,
        'max_cutlerys': getCutlerys(),
        'reserved_cutlerys': 0,
        'reservations': [],
        'type': type_key,
        'date': date
    }
    return setServiceTab(arr, type_name, date, user_info)
}

function setServiceTab(arr_service, type_name, date, user_info)
{
    const elem = $('<div  class="reservation_table rounded-3">')

    const title = $('<div class="title center">')
    title.html('<p>Service du ' + type_name + '</p>')

    const head = getHead(arr_service, date, user_info)

    const table = getReservationTab(arr_service)

    elem.append(title)
    elem.append(head)
    elem.append(table)

   return elem
}

function getHead(arr, date, user_info)
{
    const elem = $('<div>');

    const input_id = 'service.' + arr['id'];
    const input_cutlerys = arr['max_cutlerys']
    const input_label = $('<label>')
    input_label.attr('for', input_id)
    input_label.text('Il y à ' + arr['reserved_cutlerys'] + ' couverts réservé sur :')
    const input = $(`<input type="number" id="${input_id}" value="${input_cutlerys}">`)
    const valid_input = $('<button type="button" class="btn bi bi-check-circle" style="background-color: #1b9448">')
    valid_input.on('click', function () {
        sendData(
            '/admin/set_max_cutlerys_service',
            JSON.stringify({'date': date, 'type': arr['type'], 'cutlerys': input.val()}),
            user_info,
        )
    })

    elem.append(input_label)
    const div = $('<div>')
    div.append(input)
    div.append(valid_input)
    elem.append(div)


    return elem
}

function getReservationTab(arr)
{
    const elem = $('<div class="table rounded-3">')

    const reservations = arr['reservations']
    if (reservations.length)
    {
        const table = $('<table>')
        const head = $('<thead>')
        const tr = $('<tr>')
        tr.html(
            '<th class="small">Heure</th>' +
            '<th class="big">Nom</th>' +
            '<th class="small">Couverts</th>' +
            '<th class="big">Allergies</th>'
        )
        head.append(tr)

        const tbody = $('<tbody>')

        reservations.forEach(function (elem) {
            tbody.append(getReservationRow(elem))
        })
        table.append(head)
        table.append(tbody)
        elem.append(table)
    }
    else
        elem.html('<ul><li>Il ny à pas de réservation pour cette date</li></ul>')

    return elem
}

function getReservationRow(res)
{
    const elem = $('<tr>')

    elem.html(
        `<td class="small">${res['hour']}</td>` +
        `<td class="big">${res['name']}</td>` +
        `<td class="small">${res['cutlerys']}</td>` +
        `<td class="big">${getAllergyFromArray(res['allergys'])}</td>`
    )
    return elem
}

function getAllergyFromArray(arr)
{
    if (arr.length)
    {
        let str = '';
        arr.forEach(function (elem) {
            str += elem + ', '
        })
        return str
    }
    else
        return 'Nope'
}

function getCutlerys()
{
    const val = $('#defaut_max_cutlerys').val()
    if (val)
        return val
    else
        return 130
}

function getData(url, user_info, json_info, callback_done, callback_fail)
{
    $.ajax({
        url: url,
        method: 'POST',
        data: {id: user_info.id, password: user_info.password, info: json_info},
        dataType: 'json'
    })
        .done(function (response) {
            callback_done(response)
        })
        .fail(function (error) {
            callback_fail(error.responseTect)
        })
}


function sendData(url, json_info, user_info, doneCallback = false) {
    $.ajax({
        url: url,
        method: 'POST',
        data: {id: user_info.id, password: user_info.password, info: json_info},
        dataType: 'text'
    })
        .done(function (response) {
            alert(response)
            if (doneCallback)
                doneCallback()
        })
        .fail(function (error) {
            alert(error.responseText)
        })
}