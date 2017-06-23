db.Video.find({"Keywords": {"$exists": true}}).forEach(function (v) {
    var newKeywords = [];
    var excludeKeywords = ['...', '...o', '...', '...о', '...'];
    var isChanged = false;
    for (var k in v.Keywords) {
        var keyword = v.Keywords[k].toLowerCase();
        if (excludeKeywords.indexOf(keyword) != -1) {
            isChanged = true;
            continue;
        }
        if (newKeywords.indexOf(keyword) != -1) {
            isChanged = true;
            continue;
        }
        newKeywords.push(keyword);
    }
    if (isChanged) {
        v.Keywords = newKeywords;
        db.Video.save(v);
        print(v._id);
    }
});
