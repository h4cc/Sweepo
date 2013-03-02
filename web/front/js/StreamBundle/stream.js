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
            this.fetchTweets();
        },

        events : {
            'click #refresh_stream' : 'loadTweets'
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
                    $('#loading_tweets').hide();
                }
            });
        },

        loadTweets : function () {
            var self = this;
            $('#loading_tweets').show();

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

        render : function() {
            var renderedContent = this.template({ tweets : this.collection.toJSON() });
            $('#loading_tweets').hide();
            $('#tweets').html(renderedContent);
            $('.hightlight').tooltip();
            return this;
        }
    });

    viewTweets = new TweetsCollectionView({ collection : new Tweets() });

});