import Plugin from 'src/plugin-system/plugin.class';

export default class OrderAttributesPlugin extends Plugin {
    init() {
        this.el.addEventListener('submit', this._onSubmit.bind(this));
    }

    _onSubmit(event) {
        event.preventDefault();

        const formData = new FormData(this.el);

        fetch(this.el.action, {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Order attributes saved successfully');
                } else {
                    console.error('Failed to save order attributes:', data.error);
                }
            })
            .catch(error => {
                console.error('Error submitting order attributes:', error);
            });
    }
}
