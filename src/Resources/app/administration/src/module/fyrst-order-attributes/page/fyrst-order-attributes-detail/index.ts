import template from './fyrst-order-attributes-detail.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

type OrderLineItemAttribute = Entity<'fyrst_order_line_item_attribute'>;

interface DetailComponentData {
    attribute: OrderLineItemAttribute | null;
    isLoading: boolean;
    isSaveSuccessful: boolean;
}

export default Component.wrapComponentConfig({
    template,

    inject: [
        'repositoryFactory',
    ],

    mixins: [
        'notification',
    ],

    props: {
        attributeId: {
            type: String,
            required: false,
            default: null
        }
    },

    data(): DetailComponentData {
        return {
            attribute: null,
            isLoading: false,
            isSaveSuccessful: false
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier)
        };
    },

    computed: {
        identifier(): string {
            return this.attribute ? this.attribute.key : '';
        },

        attributeRepository() {
            return this.repositoryFactory.create('fyrst_order_line_item_attribute');
        },

        isNew(): boolean {
            return !this.attributeId;
        },

        isKeyReadOnly(): boolean {
            return !this.isNew;
        }
    },

    watch: {
        attributeId(): void {
            this.createdComponent();
        }
    },

    created(): void {
        this.createdComponent();
    },

    methods: {
        createdComponent(): void {
            if (this.isNew) {
                this.attribute = this.attributeRepository.create();
                this.attribute.type = 'text';
                this.attribute.active = true;
                this.attribute.required = false;
                this.attribute.position = 0;
            } else {
                this.loadEntity();
            }
        },

        loadEntity(): void {
            this.isLoading = true;

            const criteria = new Criteria();
            criteria.addAssociation('translations');

            this.attributeRepository.get(this.attributeId).then((entity) => {
                this.attribute = entity;
                this.isLoading = false;
            }).catch(() => {
                this.isLoading = false;
            });
        },

        onSave(): Promise<void> {
            this.isSaveSuccessful = false;
            this.isLoading = true;

            return this.attributeRepository.save(this.attribute).then(() => {
                this.isSaveSuccessful = true;
                this.isLoading = false;

                if (this.isNew) {
                    this.$router.push({
                        name: 'fyrst.order.attributes.detail',
                        params: { id: this.attribute.id }
                    });
                }

                this.createNotificationSuccess({
                    message: this.isNew
                        ? this.$tc('fyrst-order-attributes.detail.successCreate')
                        : this.$tc('fyrst-order-attributes.detail.successUpdate')
                });

                this.loadEntity();
            }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    message: this.$tc('global.notification.unspecifiedSaveErrorMessage')
                });
                throw exception;
            });
        },

        onCancel(): void {
            this.$router.push({ name: 'fyrst.order.attributes.list' });
        },

        onChangeLanguage(): void {
            this.loadEntity();
        }
    }
});
