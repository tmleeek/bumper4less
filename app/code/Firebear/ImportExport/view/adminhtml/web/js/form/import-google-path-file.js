/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

define(
    [
        'Firebear_ImportExport/js/form/import-dep-file',
        'Magento_Ui/js/lib/spinner',
        'uiRegistry'
    ],
    function (Element, loader, reg) {
        'use strict';

        return Element.extend(
            {
                defaults: {
                      listens: {
                        "value": "onChangeValue",
                        "${$.ns}.${$.ns}.source.type_file:value": "onFormatValue"
                    }
                },
                onChangeValue: function (value) {
                  
                    var form = reg.get(this.ns+'.'+this.ns);
                    if (form.name) {
                   loader.get(form.name).show();
                    }

                    var data = reg.get(this.provider).data;
                    var d = null;
                    if (this.isShown) {
                        var a = document.createElement("a");
                        if (value.length > 0) {
                        a.href = value;
                        var array = a.pathname.split('/');

                        if (_.indexOf(array, 'spreadsheets') && _.indexOf(array, 'Export') == -1) {
                            var number = _.indexOf(array, 'd');
                            if (number != -1) {
                                d = array[number + 1];
                                var val = this.getCreateUrl(a, d);
                                this.value('');
                                this.value(val);
                                this.source.set('data.' + this.name, val);
                            }
                        }
                        var map = reg.get(this.ns + '.' + this.ns + '.source_data_map_container.source_data_map');
                        var mapCategory = reg.get(this.ns + '.' + this.ns + '.source_data_map_container_category.source_data_categories_map');
                        map.deleteRecords();
                        map._updateCollection();
                        mapCategory.deleteRecords();
                        mapCategory._updateCollection();
                        reg.get(this.ns + '.' + this.ns + '.source.check_button').showMap(0);
                    }
                    }
                    if (form.name) {
                    loader.get(form.name).hide();
                    }
                    
                },
                getCreateUrl: function (localUri, d) {
                    var url = 'http://docs.google.com/feeds/download/spreadsheets/Export?key=' + d + '&exportFormat=csv';
                    var str = localUri.hash.split("?")[0].split("&")[0].slice(1);
                    if (localUri.hash != '') {
                        url = url + '&' + str;
                    }

                    return url;
                },
                onFormatValue: function (value) {
                    if (this.isShown) {
                        this.value('');
                    }
                }
            }
        );
    }
);
