import './override/sw-order-line-items-grid'
import deDE from './snippet/de-DE.json'
import enGB from './snippet/en-GB.json'

const { Locale } = Shopware

Locale.extend('de-DE', deDE)
Locale.extend('en-GB', enGB)