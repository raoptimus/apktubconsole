db.Video.find({"Tags.Language": {"$exists": false}}).forEach(function (v) {
    v.Keywords = v.Tags;
    v.Actors = [];
    v.ChannelId = 0;
    v.Tags =
        [
            {
                "Language": "ru",
                "Tags": v.Tags
            }
        ]
    ;
    db.Video.save(v);
    print(v._id);
});
