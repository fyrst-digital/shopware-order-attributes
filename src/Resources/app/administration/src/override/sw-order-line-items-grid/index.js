import template from './template.html.twig'
import './style.scss'

const {Component} = Shopware

Component.override('sw-order-line-items-grid', {
    template
})