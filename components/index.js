;(function($) {
    Vue.component('wpuf-integration-permission', {
        template: '#tmpl-wpuf-integration-permission',
        mixins: [wpuf_mixins.integration_mixin],

        data: function() {
            return {
                lists: []
            };
        },

        computed: {

        },

        created: function() {
            // this.fetchLists();
        },

        methods: {

            fetchLists: function(target) {
                var self = this;

                wp.ajax.send('wpuf_aweber_fetch_lists', {
                    data: {
                        _wpnonce: weForms.nonce
                    },

                    success: function(response) {
                        self.lists = response;
                    },

                    error: function(error) {
                        alert(error);
                    }
                });
            },

            updateLists: function(target) {
                var self = this;

                var link = $(target).closest('a');

                link.addClass('updating');

                wp.ajax.send('wpuf_aweber_update_lists', {
                    data: {
                        _wpnonce: weForms.nonce
                    },

                    success: function(response) {
                        self.lists = response;
                    },

                    error: function(error) {
                        alert(error);
                    },

                    complete: function() {
                        link.removeClass('updating');
                    }
                });
            },

            insertValue: function(type, field, property) {
                var value = ( field !== undefined ) ? '{' + type + ':' + field + '}' : '{' + type + '}';

                this.settings.fields[property] = value;
            }
        }
    });

})(jQuery);