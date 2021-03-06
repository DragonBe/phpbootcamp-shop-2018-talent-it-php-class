/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


Ext.ns('MShop.panel.media');

MShop.panel.media.ItemPickerUi = Ext.extend(MShop.panel.AbstractListItemPickerUi, {

    title : MShop.I18n.dt('admin', 'Media'),

    initComponent : function() {

        Ext.apply(this.itemConfig, {
            title : MShop.I18n.dt('admin', 'Associated media'),
            xtype : 'MShop.panel.listitemlistui',
            domain : 'media',
            getAdditionalColumns : this.getAdditionalColumns.createDelegate(this)
        });

        Ext.apply(this.listConfig, {
            title : MShop.I18n.dt('admin', 'Available media'),
            xtype : 'MShop.panel.media.listuismall'
        });

        MShop.panel.media.ItemPickerUi.superclass.initComponent.call(this);
    },

    getAdditionalColumns : function() {
        var conf = this.itemConfig;
        this.listTypeStore = MShop.GlobalStoreMgr.get(conf.listTypeControllerName, conf.domain);

        return [
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'typeid',
                header : MShop.I18n.dt('admin', 'List type'),
                id : 'listtype',
                width : 70,
                renderer : this.typeColumnRenderer.createDelegate(this, [this.listTypeStore, conf.listTypeLabelProperty], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Status'),
                id : 'refstatus',
                width : 50,
                align : 'center',
                renderer : this.refStatusColumnRenderer.createDelegate(this, ['media.status'], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Type'),
                id : 'reftype',
                width : 70,
                renderer : this.refColumnRenderer.createDelegate(this, ['media.typename'], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Mimetype'),
                id : 'refmimetype',
                width : 80,
                hidden : true,
                sortable : true,
                renderer : this.refColumnRenderer.createDelegate(this, ['media.mimetype'], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Language'),
                id : 'reflang',
                width : 50,
                renderer : this.refLangColumnRenderer.createDelegate(this, ['media.languageid'], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Label'),
                id : 'refcontent',
                renderer : this.refColumnRenderer.createDelegate(this, ['media.label'], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Preview'),
                id : 'refpreview',
                width : 100,
                renderer : this.refPreviewRenderer.createDelegate(this)
            }];
    },

    refPreviewRenderer : function(refId, metaData, record, rowIndex, colIndex, store) {
        var refItem = this.getRefStore().getById(refId);
        return (refItem ? '<img class="aimeos-admin-media-list-preview" src="' +
            MShop.urlManager.getAbsoluteUrl(refItem.get('media.preview')) + '" />' : '');
    }
});

Ext.reg('MShop.panel.media.itempickerui', MShop.panel.media.ItemPickerUi);
