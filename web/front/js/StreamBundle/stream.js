$(function () {

    window.Tweet = Backbone.Model.extend({
        initialize : function() {}
    });

    window.Tweets = Backbone.Collection.extend({
        model : Tweet,
        initialize : function() {}
    });

    window.TweetsCollectionView = Backbone.View.extend({

        el : $('body'),

        initialize : function() {
            this.template = _.template($('#tweet_template').html());
            this.fetchTweets();
        },

        fetchTweets : function () {
            var self = this;

            $.ajax({
                type : 'GET',
                url: Routing.generate('api_tweets') + '?token=' + core_data.user_api_key,
                success: function(data) {
                    self.collection.add(data.success);
                    self.render();
                },
                error: function(data) {

                }
            });
        },

        render : function() {
            var renderedContent = this.template({ tweets : this.collection.toJSON() });
            $('#spin_list_tweets').hide();
            $('#tweets').html(renderedContent).toggle();
            return this;
        }
    });

    view = new TweetsCollectionView({ collection : new Tweets() });

});