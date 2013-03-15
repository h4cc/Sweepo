$(function(){

    window.Subscription = Backbone.Model.extend({
        initialize : function() {}
    });

    window.Subscriptions = Backbone.Collection.extend({
        model : Subscription,
        initialize : function() {}
    });

    window.SubscriptionsCollectionView = Backbone.View.extend({

        el : $('body'),

        initialize : function() {
            var self = this;

            this.template = _.template($('#subscription_template').html());

            $.ajax({
                type : 'GET',
                url: Routing.generate('api_subscriptions') + '?token=' + core_data.user_api_key,
                success: function(data) {
                    $('#no_subscriptions').hide();
                    self.collection.add(data.success);
                    self.render();
                },
                error: function(data) {
                    $('#list_subscriptions').empty();
                    $('#no_subscriptions').show();
                }
            });

            this.listenTo(this.collection, 'add', this.render);
            this.listenTo(this.collection, 'remove', this.render);
        },

        events : {
            'click #submit_subscription'                   : 'addSubscription',
            'click #submit_form_dropdown_add_subscription' : 'addSubscription',
            'click .icon-remove-circle'                    : 'showDelete',
            'click .delete_subscription'                   : 'deleteSubscription'
        },

        addSubscription : function(event) {
            var self = this;
            var currentTarget = $(event.currentTarget);

            // Prevent the default dehavior of the submit button
            event.preventDefault();

            if ('' === currentTarget.prev().val()) {
                return;
            }

            // Spinner on the add button
            currentTarget.children('i').removeClass('icon-plus').addClass('icon-spinner icon-spin');

            $.ajax({
                type : 'POST',
                data : {'subscription': currentTarget.prev().val()},
                url: Routing.generate('api_subscriptions') + '?token=' + core_data.user_api_key,
                success: function(data) {

                    // Hide the spinner on the current add button
                    currentTarget.children('i').removeClass('icon-spinner icon-spin').addClass('icon-plus');

                    self.collection.add(data.success);
                    self.showInfo(data.success.subscription);

                    if ( $('#no_subscriptions').is(':visible') ) {
                        $('#no_subscriptions').hide();
                    }

                    // Refresh Stream with new subscription
                    viewTweets.loadTweets();
                },
                error: function(data) {
                    currentTarget.children('i').removeClass('icon-spinner icon-spin').addClass('icon-plus');
                    self.showError();
                }
            });

            this.$('input[type="text"]').val('');
        },

        deleteSubscription : function(event) {
            var self = this;
            var id = $(event.currentTarget).parent('.subscription').attr('id');
            var subscription = this.collection.get(id);

            $.ajax({
                type : 'DELETE',
                url: Routing.generate('api_subscriptions_get', {'id':id}) + '?token=' + core_data.user_api_key,
                success: function(data) {
                    $(event.currentTarget).parent('.subscription').animate({marginLeft:'1000px'}, {'duration':1000, 'queue':false, 'complete': function() {
                        self.collection.remove(subscription);

                        if (self.collection.models.length === 0) {
                            $('#no_subscriptions').show();
                        }

                        viewTweets.removeSubscription(subscription);
                    }}).fadeOut();
                },
                error: function(data) {
                    self.showError();
                }
            });
        },

        showInfo : function(subscription) {
            $('#info_subscription > span').html(subscription);
            $('#info_subscription').show().delay(4000).fadeOut();
        },

        showError : function() {
            $('#error_subscription').show().delay(4000).fadeOut();
        },

        showDelete : function(event) {
            $(event.currentTarget).next().animate({ right : '8px' }, 300).delay(4000).animate({ right : '-105px' }, 300);
        },

        render : function() {
            var renderedContent = this.template({ subscriptions : this.collection.toJSON() });
            $('#list_subscriptions').html(renderedContent);
            return this;
        }
    });

    viewSubscriptions = new SubscriptionsCollectionView({ collection : new Subscriptions() });
});