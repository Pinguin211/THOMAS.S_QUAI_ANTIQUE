import {Controller} from '@hotwired/stimulus';


export default class extends Controller {
    form_name = ''

    connect() {
        this.setSubmit()
        this.setAll()
        this.setAddMethod()
    }

    setSubmit() {
        const form = this.element.querySelector('form')
        this.form_name = form.name
        form.addEventListener('submit', (ev) => {
            ev.preventDefault()
            this.activeAllCheckbox()
            form.submit()
        })
    }

    setAddMethod()
    {
        const ingredients_tab = this.element.querySelector('#ingredients_tab');
        const ingredients = this.element.querySelector('#ingredients')
        ingredients_tab.addEventListener('click', (ev) => {
            if (ev.target.matches('button'))
            {
                const id = ev.target.id
                if (id)
                {
                    const elem = ingredients.querySelector(`[value="${id}"]`);
                    this.switchSelected(elem, true)
                }
            }
        })
    }

    setAll() {
        let i = 0;
        while (this.element.querySelector(`#${this.form_name}_ingredients_${i}`))
        {
            const element = this.element.querySelector(`#${this.form_name}_ingredients_${i}`)
            element.setAttribute('hidden', '')
            element.disabled = true
            let label = this.element.querySelector(`label[for='${element.getAttribute('id')}']`);
            label.addEventListener('click', () => {
                this.switchSelected(element, false)
            })
            this.updateLabel(element)
            i++
        }
    }

    switchSelected(element, force = null)
    {
        element.disabled = false
        if (force !== null)
            element.checked = force
        else
            element.checked = !element.checked;
        element.disabled = true
        this.updateLabel(element)
    }

    updateLabel(element)
    {
        let label = this.element.querySelector(`label[for='${element.getAttribute('id')}']`);
        if (element.checked)
            label.removeAttribute('hidden')
        else
            label.setAttribute('hidden', '')
    }

    activeAllCheckbox()
    {
        let i = 0;
        while (this.element.querySelector(`#${this.form_name}_ingredients_${i}`)) {
            const element = this.element.querySelector(`#${this.form_name}_ingredients_${i}`)
            element.disabled = false
            i++
        }
    }
}


