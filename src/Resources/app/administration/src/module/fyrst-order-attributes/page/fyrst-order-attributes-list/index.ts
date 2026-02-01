import template from './fyrst-order-attributes-list.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

type OrderLineItemAttribute = Entity<'fyrst_order_line_item_attribute'>;

interface ListComponentData {
    attributes: OrderLineItemAttribute[] | null;
    sortBy: string;
    sortDirection: 'ASC' | 'DESC';
    isLoading: boolean;
    filterLoading: boolean;
    filterSelect: unknown | null;
}

interface GridColumn {
    property: string;
    dataIndex: string;
    label: string;
    routerLink?: string;
    inlineEdit?: boolean | string;
    allowResize: boolean;
    align?: 'left' | 'right' | 'center';
}

Component.register('fyrst-order-attributes-list', {
    template,

    inject: [
        'repositoryFactory',
        'filterFactory'
    ],

    mixins: [
        'notification',
        'listing'
    ],

    data(): ListComponentData {
        return {
            attributes: null,
            sortBy: 'createdAt',
            sortDirection: 'DESC',
            isLoading: false,
            filterLoading: false,
            filterSelect: null
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        attributeRepository() {
            return this.repositoryFactory.create('fyrst_order_line_item_attribute');
        },

        columns(): GridColumn[] {
            return [
                {
                    property: 'key',
                    dataIndex: 'key',
                    label: this.$tc('fyrst-order-attributes.list.columnKey'),
                    routerLink: 'fyrst.order.attributes.detail',
                    inlineEdit: false,
                    allowResize: true
                },
                {
                    property: 'label',
                    dataIndex: 'label',
                    label: this.$tc('fyrst-order-attributes.list.columnLabel'),
                    inlineEdit: false,
                    allowResize: true
                },
                {
                    property: 'type',
                    dataIndex: 'type',
                    label: this.$tc('fyrst-order-attributes.list.columnType'),
                    inlineEdit: false,
                    allowResize: true
                },
                {
                    property: 'position',
                    dataIndex: 'position',
                    label: this.$tc('fyrst-order-attributes.list.columnPosition'),
                    inlineEdit: 'number',
                    allowResize: true,
                    align: 'right'
                },
                {
                    property: 'active',
                    dataIndex: 'active',
                    label: this.$tc('fyrst-order-attributes.list.columnActive'),
                    inlineEdit: 'boolean',
                    allowResize: true,
                    align: 'center'
                },
                {
                    property: 'required',
                    dataIndex: 'required',
                    label: this.$tc('fyrst-order-attributes.list.columnRequired'),
                    inlineEdit: 'boolean',
                    allowResize: true,
                    align: 'center'
                }
            ];
        }
    },

    created(): void {
        this.createdComponent();
    },

    methods: {
        createdComponent(): void {
            this.getList();
        },

        getList(): void {
            this.isLoading = true;

            const criteria = new Criteria(this.page, this.limit);
            criteria.setTerm(this.term);
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection));
            criteria.addAssociation('translations');

            this.attributeRepository.search(criteria).then((searchResult) => {
                this.attributes = Array.from(searchResult);
                this.isLoading = false;
            }).catch(() => {
                this.isLoading = false;
            });
        },

        onInlineEditSave(item: OrderLineItemAttribute): void {
            this.attributeRepository.save(item).then(() => {
                this.createNotificationSuccess({
                    message: this.$tc('fyrst-order-attributes.detail.successUpdate')
                });
            }).catch(() => {
                this.createNotificationError({
                    message: this.$tc('global.notification.unspecifiedSaveErrorMessage')
                });
            });
        },

        onDelete(id: string): void {
            this.$refs.entityListing.deleteId = null;

            this.attributeRepository.get(id).then((attribute) => {
                this.attributeRepository.delete(id).then(() => {
                    this.createNotificationSuccess({
                        message: this.$tc('global.notification.deleteSuccessMessage', 0, {
                            name: attribute.key
                        })
                    });
                    this.getList();
                }).catch(() => {
                    this.createNotificationError({
                        message: this.$tc('global.notification.deleteErrorMessage', 0, {
                            name: attribute.key
                        })
                    });
                });
            });
        },

        onChangeLanguage(): void {
            this.getList();
        }
    }
});
