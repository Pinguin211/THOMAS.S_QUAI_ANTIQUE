import {Controller} from '@hotwired/stimulus';
import $ from 'jquery'


export default class extends Controller {

    arr_elem = [];
    connect() {
        this.set()
        this.setColor()
    }

    set()
    {
        let arr = this.arr_elem
        const select = $('.formules_select')
        select.children('input').each(function ()
        {
            $(this).attr('hidden', '')
            const id = $(this).attr('id')
            const label = $(`label[for='${id}']`)
            $(this).change(function () {
                setColorLabel(label, $(this))
            })
            arr.push([label, $(this)])
        })
    }

    setColor()
    {
        this.arr_elem.forEach(function (value) {
            setColorLabel(value[0], value[1])
        })
    }

}

function setColorLabel(label, checkbox)
{
    if (checkbox.is(':checked'))
        label.css('background-color', '#5b8a43')
    else
        label.css('background-color', '#906427')
}