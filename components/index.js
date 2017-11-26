;(function($) {
    Vue.component('weforms-integration-permission', {
        props: ['settings'],
        components: {
            Multiselect: window.VueMultiselect.default
        },
        data: function() {
            return {
                restrict_mood: false,
                selected: [],
                wpUsers: [],
                isSearching: false
            };
        },

        created: function() {
            if (this.settings.allowed_users) {
                this.selected = this.settings.allowed_users;
            }

            if (this.settings.restrict_mood) {
                this.restrict_mood = this.settings.restrict_mood;
            }

            this.fetchWPUsers();
        },

        methods: {
            fetchWPUsers() {
                var self = this;

                wp.ajax.send('weforms_permission_fetch_users', {
                    data: {},
                    success(response) {
                        self.wpUsers = response;
                    },
                    error(error) {
                        console.log('error', error);
                    }
                });
            },

            asyncSearchUser: function(query) {

                if ( ! query ) {
                    return;
                }

                var self = this;

                this.isSearching = true;

                wp.ajax.send('weforms_permission_fetch_users', {
                    data: {
                        search: query,
                    },
                    success(response) {
                        var ids =  _.pluck(self.wpUsers, 'id');

                        response.forEach( user => {
                            if ( ! _.contains(ids, user.id) ) {
                                self.wpUsers.push(user);
                            }
                        });
                    },
                    error(error) {
                        console.log('error', error);
                    },
                    complete(){
                        self.isSearching = false
                    }
                });
            },
        },
        watch: {
            selected: function(value) {
                this.settings.allowed_users = value;
            },
            restrict_mood: function(value) {
                this.settings.restrict_mood = value;
            }
        }
    });

})(jQuery);