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
                        self.collection.add(data.success);
                        self.render();
                    }
                }
            });

            _.bindAll(this, 'render');
            this.collection.bind('add', this.render);
            this.collection.bind('remove', this.render);
        },

        events : {
            'click #submit_subscription' : 'addSubscription'
        },

        addSubscription : function(e) {
            var self = this;
            e.preventDefault();

            $.ajax({
                type : 'POST',
                data : {'subscription': this.$('#input_subscription').val()},
                url: Routing.generate('api_subscriptions') + '?token=' + core_data.user_api_key,
                success: function(data) {
                    self.collection.add(data.success);
                    self.showInfo(data.success.subscription);
                }
            });

            this.$('input[type="text"]').val('');
        },

        showInfo : function(subscription) {
            $('#info_subscription > span').html(subscription);
            $('#info_subscription').show().delay(4000).fadeOut();
        },

        render : function() {
            var renderedContent = this.template({ subscriptions : this.collection.toJSON() });
            $(this.el).children('#list_subscriptions').html(renderedContent);
            return this;
        }
    });

    view = new SubscriptionsCollectionView({ collection : new Subscriptions() });
});