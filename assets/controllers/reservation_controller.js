import {Controller} from '@hotwired/stimulus';
import $ from 'jquery'


export default class extends Controller {

    stage_list
    date
    connect() {
        this.setStageSelector()
        this.setShowIngredients()
    }

    setShowIngredients()
    {
        const here = this
        const checkbox = $('#reservation_allergy')
        checkbox.change(function () {
            here.showIngredients($(this))
        })
        this.showIngredients(checkbox)
    }

    showIngredients(checkbox)
    {
        const ingredients_tab = $('#ingredients_tab_div')
        const ingredients_list = $('#ingredients')
        if (checkbox.is(':checked'))
        {
            ingredients_tab.attr('hidden', false)
            ingredients_list.attr('hidden', false)
        }
        else
        {
            ingredients_list.attr('hidden', '')
            ingredients_tab.attr('hidden', '')
        }
    }

    setStageSelector() {
        this.stage_list = $('#stage_list')
        this.date = $('#date_widget')
        const here = this
        this.date.change(function () {
            here.setStageList($(this).val())
        })
        this.setStageList(this.date.val())
    }

    setStageList(date)
    {
        const here = this
        $.ajax({
            url: '/reservation/get_stage',
            method: 'POST',
            data: {date: date},
            dataType: 'json'
        })
            .done(function (response) {
                if (Object.keys(response).length)
                    here.setButtonElem(response)
                else
                {
                    here.setError("Il n'y a pas de reservation disponible à cette date")
                    $('#submit_btn').addClass('disabled')
                }
            })
            .fail(function (error) {
                here.setError(error.responseText)
                $('#submit_btn').addClass('disabled')
            })
    }

    setButtonElem(arr)
    {
        let elem = $('<div class="btn_list reservation">')
        const keys = Object.keys(arr)
        const here = this
        keys.forEach(function (key) {
            const button =  $(`<button class="no_submit_btn" value="${key}" type="button">`)
            button.html(arr[key])
            button.on('click', function () {
                here.setButtonValidate(elem, button)
            })
            elem.append(button)
        })
        this.stage_list.children('div').remove()
        this.stage_list.append(elem)
    }

    setButtonValidate(list, button)
    {
        const value = button.val()
        $.ajax({
            url: '/reservation/good_stage',
            method: 'POST',
            data: {stage: value, date: this.date.val()},
        })
            .done(function () {
                list.children('.validate').removeClass('validate')
                button.addClass('validate')
                $('#reservation_stage_hour').val(value)
                $('#submit_btn').removeClass('disabled')
            })
            .fail(function (error) {
                alert(error.responseText);
                $('#submit_btn').addClass('disabled')
                button.remove()
                list.children('.validate').removeClass('validate')
            })
    }

    setError(error)
    {
        let elem = $('<div>')
        let error_ul = $('<ul>')
        let error_li = $('<li>')
        error_li.html(error)
        error_ul.append(error_li)
        elem.append(error_ul)
        this.stage_list.children('div').remove()
        this.stage_list.append(elem)
    }

}
