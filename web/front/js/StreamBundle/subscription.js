$(function(){

    window.Subscription = Backbone.Model.extend({
        initialize : function() {}
    });

    window.Subscriptions = Backbone.Collection.extend({
        model : Subscription,
        initialize : function() {}
    });

    window.SubscriptionsCollectionView = Backbone.View.extend({

        el : $('#subscriptions'),

        initialize : function() {
            var self = this;

            this.template = _.template($('#subscription_template').html());

            $.ajax({
                type : 'GET',
                url: Routing.generate('api_subscriptions') + '?token=' + core_data.user_api_key,
                success: function(data) {
                    if ('success' in data) {
                        self.$('#no_subscriptions').hide();
                        self.collection.add(data.success);
                        self.render();
                    } else if ('error' in data) {
                        self.$('#list_subscriptions').empty();
                        self.$('#no_subscriptions').show();
                    }
                }
            });

            _.bindAll(this, 'render');
            this.collection.bind('add', this.render);
            this.collection.bind('remove', this.render);
        },

        events : {
            'click #submit_subscription' : 'addSubscription',
            'click .icon-remove-circle' : 'showDelete',
            'click .delete_subscription' : 'deleteSubscription'
        },

        addSubscription : function(e) {
            var self = this;
            e.preventDefault();
            this.$('#submit_subscription').children('i').removeClass('icon-plus').addClass('icon-spinner icon-spin');

            $.ajax({
                type : 'POST',
                data : {'subscription': this.$('#input_subscription').val()},
                url: Routing.generate('api_subscriptions') + '?token=' + core_data.user_api_key,
                success: function(data) {
                    self.$('#submit_subscription').children('i').removeClass('icon-spinner icon-spin').addClass('icon-plus');
                    self.collection.add(data.success);
                    self.showInfo(data.success.subscription);
                }
            });

            this.$('input[type="text"]').val('');
        },

        deleteSubscription : function(event) {
            var id = $(event.currentTarget).parent('.subscription').attr('id');
            var subscription = this.collection.get(id);
            this.collection.remove(subscription);
        },

        showInfo : function(subscription) {
            $('#info_subscription > span').html(subscription);
            $('#info_subscription').show().delay(4000).fadeOut();
        },

        showDelete : function(event) {
            $(event.currentTarget).next().animate({ right : '8px' }, 300).delay(4000).animate({ right : '-82px' }, 300);
        },

        render : function() {
            var renderedContent = this.template({ subscriptions : this.collection.toJSON() });
            $(this.el).children('#list_subscriptions').html(renderedContent);
            return this;
        }
    });

    view = new SubscriptionsCollectionView({ collection : new Subscriptions() });
});