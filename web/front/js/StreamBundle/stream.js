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
            this.collection.comparator = function(tweet) {
              return -tweet.get("tweet_id");
            };
            this.template = _.template($('#tweet_template').html());
            this.template_no_tweets = _.template($('#no_tweet_template').html());
            this.template_tweets_loading = _.template($('#tweet_loading_template').html());

            this.fetchTweets();
        },

        events : {
            'click #refresh_stream' : 'refreshStream'
        },

        fetchTweets : function () {
            var self = this;

            // Loading informations
            $('#tweets').html(this.template_tweets_loading());

            $.ajax({
                type : 'GET',
                url: Routing.generate('api_tweets') + '?token=' + core_data.user_api_key,
                success: function(data) {
                    self.collection.add(data.success);
                    self.render();
                    self.loadTweets();
                },
                error: function(data) {
                    $('#loading_tweets').hide();
                    self.render();
                }
            });
        },

        refreshStream : function (event) {
            event.preventDefault();
            this.loadTweets();
        },

        loadTweets : function () {
            var self = this;

            // Loading informations
            $('#loading_tweets').show();
            if (this.collection.models.length === 0) {
                $('#tweets').html(this.template_tweets_loading());
            }

            $.ajax({
                type : 'GET',
                url: Routing.generate('api_tweets_refresh') + '?token=' + core_data.user_api_key,
                success: function(data) {
                    self.collection.add(data.success);
                    self.collection.sort();
                    self.render();
                },
                error: function(data) {
                    $('#loading_tweets').hide();
                }
            });
        },

        removeSubscription : function(subscription) {
            var models = this.collection.where({subscription_keyword : subscription.get('subscription')});
            this.collection.remove(models);
            this.render();
        },

        render : function() {
            if (this.collection.models.length !== 0) {
                var renderedContent = this.template({ tweets : this.collection.toJSON() });
                $('#loading_tweets').hide();
                $('#tweets').html(renderedContent);
                $('.hightlight').tooltip();
            } else {
                $('#tweets').html(this.template_no_tweets());
            }

            return this;
        }
    });

    viewTweets = new TweetsCollectionView({ collection : new Tweets() });

});