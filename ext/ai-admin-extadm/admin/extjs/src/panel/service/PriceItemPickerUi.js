/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.panel.service');

// hook price picker into the product ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.service.ItemUi', 'MShop.panel.service.PriceItemPickerUi', {
    xtype : 'MShop.panel.price.itempickerui',
    itemConfig : {
        recordName : 'Service_Lists',
        idProperty : 'service.lists.id',
        siteidProperty : 'service.lists.siteid',
        listNamePrefix : 'service.lists.',
        listTypeIdProperty : 'service.lists.type.id',
        listTypeLabelProperty : 'service.lists.type.label',
        listTypeControllerName : 'Service_Lists_Type',
        listTypeCondition : {
            '&&' : [{
                '==' : {
                    'service.lists.type.domain' : 'price'
                }
            }]
        },
        listTypeKey : 'service/lists/type/price'
    },
    listConfig : {
        domain : 'service',
        prefix : 'price.'
    }
}, 30);
