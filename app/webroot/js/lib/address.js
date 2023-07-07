/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!jQuery.isFunction(loader)) {

    function loader(action)
    {
        if (action == 'show') {
          jQuery('#preloader').css("display", "block");
          jQuery('#status').css("display", "block");
      } else {
          jQuery('#preloader').css("display", "none");
          jQuery('#status').css("display", "none");
        }
    }
}

function getStates(country, destId)
{
    if (typeof destId == undefined || destId == '') {
        destId = 'StateId';
    }
    loader('show');
    jQuery.ajax({
        url: BaseUrl + "countries/getStates/" + country,
        type: 'post',
        success: function (response) {
            loader('hide');
            jQuery('#' + destId).html(response);
        }
    });
}
function getCities(state, destId)
{
    if (typeof destId == undefined || destId == '') {
        destId = 'CityId';
    }
    loader('show');
    jQuery.ajax({
        url: BaseUrl + "countries/getCities/" + state,
        type: 'post',
        success: function (response) {
            loader('hide');
            jQuery('#' + destId).html(response);
        }
    });
}
