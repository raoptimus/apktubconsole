/**
 * Created by sainomori on 13/07/15.
 */
var con = {
    fillParams: function () {
        window.paramList['u'] = $('#u').val();
        window.paramList['s'] = $('#s').val();
        window.paramList['a'] = $('#a').val();
        window.paramList['_t'] = $('#_t').val();
        window.paramList['_a'] = $('#_a').val();
        window.paramList['_o'] = $('#_o').val();
        window.paramList['_af'] = $('#_af')[0].checked ? 1 : 0;
    },
    cleanArray: function (obj) {
        var newArray = {};
        for (key in obj) {
            if (obj[key]) {
                newArray[key] = obj[key];
            }
        }
        return newArray;
    },
    generateQuery: function () {
        var returnString = "";
        var cleanArray = con.cleanArray(window.paramList);
        for (key in cleanArray) {
            returnString += key + '=' + cleanArray[key] + '&'
        }
        return '?' + returnString.substr(0,returnString.length - 1);
    },
    ready: function () {
        window.paramList = {};
        con.fillParams();
        var query = con.generateQuery();
        $('#landingURL').attr('value','http://...apk..../' + query);
        $('#apkURL').attr('value','http://...apk..../...apk.apk' + query);

        $('.userInput').change(function (e) {
            con.fillParams();
            query = con.generateQuery();
            $('#landingURL').attr('value','http://...apk..../' + query);
            $('#apkURL').attr('value','http://...apk..../...apk.apk' + query);
        });
    }
};

$(document).ready(con.ready);