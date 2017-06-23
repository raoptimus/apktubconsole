db.Video.update(
    {},
    {$addToSet : {'Filters': '*'}},
    { multi: true }
);
