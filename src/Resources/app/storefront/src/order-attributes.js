import Debouncer from 'src/helper/debouncer.helper';

const Plugin = window.PluginBaseClass;

export default class OrderAttributesPlugin extends Plugin {
    init() {
        this.inputs = this.el.querySelectorAll('input[name^="orderAttributes["]');
        this.debouncedSubmit = Debouncer.debounce(this._submitForm.bind(this), 300);
        this._registerEventListeners();
    }

    _registerEventListeners() {
        this.inputs.forEach(input => {
            input.addEventListener('change', this.debouncedSubmit);
            input.addEventListener('keydown', this._onKeyDown.bind(this));
        });
    }

    _onKeyDown(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            event.target.blur();
            this._submitForm();
        }
    }

    _submitForm() {
        const formData = new FormData(this.el);
        const submitButton = this.el.querySelector('[type="submit"]');

        this._showLoadingIndicator(submitButton);

        fetch(this.el.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                this._hideLoadingIndicator(submitButton);

                if (data.success) {
                    this._updateInputValues(data.data);
                } else {
                    console.error('Failed to save order attributes:', data.error);
                }
            })
            .catch(error => {
                this._hideLoadingIndicator(submitButton);
                console.error('Error submitting order attributes:', error);
            });
    }

    _updateInputValues(data) {
        Object.keys(data).forEach(key => {
            const input = this.el.querySelector(`input[name="payload[${key}]"]`);
            if (input) {
                input.value = data[key];
            }
        });
    }

    _showLoadingIndicator(submitButton) {
        this.inputs.forEach(input => {
            input.disabled = true;
            input.classList.add('is-loading');
        });

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('is-loading');
            const originalText = submitButton.innerHTML;
            submitButton.dataset.originalText = originalText;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + originalText;
        }
    }

    _hideLoadingIndicator(submitButton) {
        this.inputs.forEach(input => {
            input.disabled = false;
            input.classList.remove('is-loading');
        });

        if (submitButton) {
            submitButton.disabled = false;
            submitButton.classList.remove('is-loading');
            if (submitButton.dataset.originalText) {
                submitButton.innerHTML = submitButton.dataset.originalText;
            }
        }
    }
}
