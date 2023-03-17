import {Controller} from '@hotwired/stimulus';
import $ from 'jquery'


export default class extends Controller {

    is_admin = 0
    admin_info = [];
    input_val;
    input;
    connect() {
        if ($('#isAdmin').text() === '1')
        {
            this.is_admin = 1;
            this.admin_info['password'] = $('#user_password').text()
            this.admin_info['id'] = $('#user_id').text()

            this.input = $('#defaut_max_cutlerys');
            this.input_val = this.input.val();

            this.setInput()
        }
    }

    setInput() {
        const here = this
        $('#set_default_cutlerys').on('click', function ()
        {
            if (here.input.val() !== here.input_val)
            {
                sendData(
                    '/admin/set_default_cutlerys',
                    JSON.stringify({'cutlerys': here.input.val()}),
                    here.admin_info,
                    function () {
                        here.input_val = here.input.val()
                    }
                )
            }
            else
                alert('La valeurs est à jours')

        })
    }

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