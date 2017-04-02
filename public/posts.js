var posts = {
    url: '/posts',
    bitly: 'https://api-ssl.bitly.com/v3/shorten',
    token: '697f6014c60f48331d91556a03a8db8606f977e8',
    posts: [],
    get: function() {
        var self = this;
        $.ajax({
            type: 'GET',
            url: self.url,
            dataType: 'json',
            success: function (data) {
                self.posts = data;
                self.processPosts();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    },
    processPosts: function() {
        var index = Math.floor(Math.random() * this.posts.length);

        if(typeof this.posts[index] !== 'undefined') {
            post = this.posts[index];
            this.insertDOM(post);
        }
    },
    insertDOM: function(post) {
        var title = $("#title");
        title.fadeOut(1000, function() {
            title.html(post.title);
        }).fadeIn(1000);
        title.attr('href', post.url);

        $("img").each(function(index) {
            var img = this;
            $(this).fadeOut(100, function() {
                $(img).attr('src', post.image);
            }).fadeIn(1000);
        });

        this.twitter(post);
    },
    twitter: function(post) {
        var url = this.bitly + '?access_token=' + this.token + '&longUrl=' + encodeURIComponent(post.url);

        $.getJSON(url, {}, function(response) {
          var shortUrl = (response.status_code == 200) ? response.data.url : post.url;
          $('#twitter').attr('href', 'https://twitter.com/intent/tweet?hashtags=DrupalDev&text=' + encodeURIComponent('"' + post.title + '" ' + shortUrl));
        });
    },
};
