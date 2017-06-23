//search by 64-bit integer Number type is 18 https://docs.mongodb.org/manual/reference/operator/query/type/
db.Journal.find({AddedDate : {$type : 18}}).forEach(function (j) {
    j.AddedDate = new Date(j.AddedDate * 1000);
    print(j.AddedDate);
    db.Journal.update({_id: j._id}, {$set : {AddedDate: j.AddedDate}})
});