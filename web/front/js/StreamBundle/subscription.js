$(function(){

    window.Subscription = Backbone.Model.extend({

        initialize : function() {

        }
    });

    window.Subscriptions = Backbone.Collection.extend({
        model : Subscription,
        initialize : function() {

        }
    });

    window.SubscriptionsCollectionView = Backbone.View.extend({

        el : $('#list_subscriptions'),

        initialize : function() {
            this.template = _.template($('#subscription_template').html());

            $.ajax({
                type : 'GET',
                url: Routing.generate('api_subscriptions') + '?token=' + core_data.user_api_key,
                success: function(data) {
                    this.collection = new Subscriptions().add(data.success);
                    this.render;
                }
            });

            // _.bindAll(this, 'render');
            // this.collection.bind('change', this.render);
            // this.collection.bind('add', this.render);
            // this.collection.bind('remove', this.render);
        },

        addCollection : function(subscriptions) {
            this.collection = subscriptions;
            return this;
        },

        render : function() {
            console.log('ok');
            var renderedContent = this.template({ subscriptions : this.collection.toJSON() });
            $(this.el).html(renderedContent);
            return this;
        }

    });

    window.view = new SubscriptionsCollectionView();

    // $('#submit_subscription').on('click', function() {
    //     var val = $('#input_subscription').val();

    //     $.ajax({
    //         type : 'POST',
    //         data : {'subscription': val},
    //         url: Routing.generate('api_subscriptions') + '?token=' + core_data.user_api_key,
    //         success: function(data) {
    //             mySubscriptionCollection.add(data.success);
    //         }
    //     });

    //     return false;
    // });
});