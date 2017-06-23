/**
 * Created by sainomori on 16/10/15.
 */
db.Video.find().forEach(function (v) {
    var published = v.Filters.indexOf('published') !== -1;
    var approved = v.Filters.indexOf('approved') !== -1;

    var filters = [];
    for (var f in v.Filters) {
        switch (v.Filters[f]) {
            case "published":
            case "!published":
            case "approved":
            case "!approved":
                break;
            default :
                filters.push(v.Filters[f]);
        }
    }

    if (published) {
        filters.push("published");
    } else if (approved) {
        filters.push("approved");
    } else {
        filters.push("!approved");
    }
    v.Filters = filters;
    db.Video.save(v);

    print(v._id);
});
