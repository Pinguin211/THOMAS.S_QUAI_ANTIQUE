import {Controller} from '@hotwired/stimulus';
import $ from 'jquery'


export default class extends Controller {

    arr_img = []
    is_admin = 0
    admin_info = [];
    connect() {
        if ($('#isAdmin').text() === '1')
        {
            this.is_admin = 1;
            this.admin_info['password'] = $('#user_password').text()
            this.admin_info['id'] = $('#user_id').text()
            addImageOption()
        }
        this.setImg()
    }

    setImg()
    {
        let arr_img = this.arr_img
        const openImgCallback = this.openElement.bind(this)
        let i = 0;
        $('.galery_img').each(function() {
            const img = $(this).children('img:first')
            if (img.length)
            {
                let arr = []
                arr['title'] = img.attr('alt')
                arr['id'] = img.attr('id')
                arr['path'] = img.attr('src')
                arr['i'] = i
                arr_img.push(arr)
                $(this).on('click', function () {
                    openImgCallback('diapo', arr)
                })
            }
            i++
        })
    }

    openElement(name, arr) {

        const user_info = this.admin_info

        setBackgroundBrightness('0.5')
        const body = $('body')

        const type = $('<div>')
        type.attr('id', name)

        const div = $('<div>')
        div.attr('id', `div_${name}`)

        const title_div = $('<div>')
        title_div.attr('class', 'title_div')
        let title = ''
        if (this.is_admin)
        {
            const input = $(`<input type="text" value="${arr['title']}">`)
            const validate = $('<button type="button" class="btn bi bi-check-circle" style="background-color: #1b9448; font-size: 0.8em">')
            validate.on('click', function () {
                setTitleImage(arr, input.val(), user_info)
            })
            title = $('<div>')
            title.append(input)
            title.append(validate)
        }
        else
            title = $('<p>').html(arr['title'])
        const closed = $('<i>')
        closed.on('click', function () {
            closeId('diapo')
        })
        closed.attr('id', 'closed')
        closed.addClass('bi bi-x-lg')

        const trash = $('<i>')
        trash.on('click', function () {
            deleteImg(user_info, arr)
        })
        trash.attr('id', 'trash')
        trash.addClass('bi bi-trash')

        title_div.append(title)
        title_div.append(closed)
        title_div.append(trash)


        div.append(title_div)
        if (name === 'diapo')
            div.append(this.getImageDiv(arr))
        type.append(div)

        body.prepend(type)
    }

    getImageDiv(arr) {
        const image_div = $('<div>').attr('class', 'image_div')
        image_div.append($('<img>').attr({
            'id': arr['i'],
            'src': arr['path'],
            'alt': arr['title'],
        }))
        image_div.prepend(this.getNextBack(image_div.children('img:first'), -1))
        image_div.append(this.getNextBack(image_div.children('img:first'),  1))
        return image_div
    }

    getNextBack(img, sens)
    {
        const callback = this
        let arrow = ''
        if (sens > 0)
            arrow = 'bi bi-chevron-compact-right'
        else
            arrow = 'bi bi-chevron-compact-left'
        const elem = $('<i>').addClass(arrow)
        this.showNextBack(elem, parseInt(img.attr('id')), sens)
        elem.on('click', function () {
            const i = parseInt(img.attr('id')) + sens
            $('#diapo').remove()
            callback.openElement('diapo', callback.arr_img[i])
        })
        return elem
    }

    showNextBack(elem, i, sens) {
        if (this.arr_img[i + sens] !== undefined)
            elem.css('display', 'absolute')
        else
            elem.css('display', 'none')
    }
}


function closeId(id) {

    $(`#${id}`).remove()
    setBackgroundBrightness(1)
}

function setBackgroundBrightness(brightness)
{
    $('main').css('filter', `brightness(${brightness})`)
    $('footer').css('filter', `brightness(${brightness})`)
}

function addImageOption() {
    const div = $('#addImage')
    const button = $('<button>')
    button.attr('class', 'btn')
    button.html('Ajouter une image')
    button.on('click', function () {
        openForm()
    })
    div.append(button)
}


function openForm() {
    setBackgroundBrightness('0.5')
    const body = $('body')

    const form = $('<div>')
    form.attr('id', 'form')


    form.html(
        '<form class="basic_form rounded-3" method="post" action="/galery/add_image" enctype="multipart/form-data">' +
        '<ul>' +
        '<li>La taille ideale de vos images est de 1920*1080</li>' +
        '<li>L\'image de doit pas depasser 2mo</li>' +
        '</ul>' +
        '<div class="div-column"><div>' +
        '<label for="image">Choisir une image :</label>' +
        '<input type="file" id="image" name="image" required>' +
        '</div></div>' +
        '<div class="div-column"><div>' +
        '<label for="title">Donner un titre à votre image</label>' +
        '<input type="text" id="title" name="title" required>' +
        '</div></div>' +
        '<div class="center"><div>' +
        '<button class="btn" type="submit"> AJOUTER</button>' +
        '</div></div>' +
        '</form>'
    )

    const closed = $('<i>')
    closed.on('click', function () {
        closeId('form')
    })
    closed.attr('id', 'closed')
    closed.addClass('bi bi-x-lg')
    form.prepend(closed)
    body.prepend(form)
}


function deleteImg(user_info, arr_image)
{
    sendData(
        '/galery/delete_image',
        JSON.stringify({'image': arr_image['id']}),
        user_info,
        function () {
            closeId('diapo')
            $(`.galery_img#${arr_image['id']}`).remove()
        }
    )
}

function setTitleImage(arr_image, new_title, user_info)
{
    if (new_title.length < 1 || new_title.length > 32)
        alert('Le nouveaux titre ne doit pas être vide et ne doit pas contenir plus de 32 caractères')
    else if (arr_image['title'] !== new_title)
    {
        sendData(
            '/galery/set_title_image',
            JSON.stringify({'title' : new_title, 'image': arr_image['id']}),
            user_info,
            function () {
                const galery_img = $(`.galery_img#${arr_image['id']}`)
                galery_img.children('label:first').text(new_title)
                galery_img.children('img:first').attr('alt', new_title)
                arr_image['title'] = new_title
            }
        )
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