import template from './template.html.twig'
import './style.scss'

const {Component} = Shopware
const {Criteria} = Shopware.Data;

Component.override('sw-order-line-items-grid', {
    template,

    computed: {
        orderLineItemAttributeRepository() {
            return this.repositoryFactory.create('fyrst_order_line_item_attribute');
        }
    },

    data() {
        return {
            showCustomFieldModal: false,
            orderLineItemAttribute: [],
        };
    },

    methods: {
        hasItemCustomFields(item) {
            return Object.values(item.customFields ?? {}).some((value) => {
                if (Array.isArray(value)) {
                    return value.length > 0;
                }

                return value !== null && value !== undefined && value !== '';
            });
        },

        async showCustomFields(item) {
            this.showCustomFieldModal = item.id
            this.orderLineItemAttribute = await this.orderLineItemAttributeRepository.search(new Criteria())
        },

        closeCustomFieldsModal() {
            this.showCustomFieldModal = false
        },

        getCustomFieldLabel(key) {
            return this.orderLineItemAttribute.find(item => item.key === key)?.translated?.label ?? key;
        }
    }
})