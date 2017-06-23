var countryList = ['UN'];
db.Country.find().forEach(function(c) {
    countryList.push(c.Code);
});

db.PushTask.find().forEach(function (v) {
    v.CarrierType = ['wifi', 'mobile'];
    v.Countries = countryList;
    db.PushTask.save(v);
    print(v._id);
});
