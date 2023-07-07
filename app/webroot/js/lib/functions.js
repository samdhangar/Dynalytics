/*
 * Made by Chirag Viradiya
 */

/*
 * ajax loader
 * @param {type} productImageId
 * @returns {undefined}
 */
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

/**
 * set primary image
 * @param {type} productImageId
 * @returns {undefined}
 */
function setPrimaryImage(productImageId) {
    if (confirm("Are you sure?you want to set this image as primary")) {
        jQuery('#primaryImage_' + productImageId).parent().find('.process').removeClass('hidden');
        jQuery.ajax({
            async: "false",
            url: BaseUrl + '/products/changePrimaryImage/' + productImageId,
            dataType: 'json',
            success: function (data) {
                if (data.status === 'success') {
                    //for othe all images
                    jQuery('.primary-image').not('#primaryImage_' + productImageId).find('i').addClass('bg-danger');
                    jQuery('.primary-image').css('background-color', 'unset');
                    //for new primary image
                    jQuery('#primaryImage_' + productImageId).find('i').removeClass('bg-danger');
                    jQuery('#primaryImage_' + productImageId).css('background-color', '#e52');
                    jQuery('#primaryImage_' + productImageId).parent().find('.process').addClass('hidden');
                }
            }
        });
    }
}

/**
 * Find percentage
 * @param {type} totalAmount
 * @param {type} amount
 * @returns {Number}
 */
function getPercentage(totalAmount, amount)
{
    totalPer = ((amount / totalAmount) * 100);
    return totalPer.toFixed(2);
}

/**
 * common js for the site
 * @param {type} param1
 * @param {type} param2
 */
jQuery(document).on('ready', function () {

    /**
     * for the modal popup load via ajax
     */
    $('body').on('show.bs.modal', '#Model', function () {
        $(this).find('.modal-content').html('<div style="text-align: center; width: 120px; margin: 0px auto; padding-top: 50px; height: 150px;"><i class="fa fa-pulse fa-spinner fa-3x text-blue" ></i> <div>Please Wait...</div></div>');
    });
    $('body').on('hidden.bs.modal', '#Model', function () {
        $(this).removeData('bs.modal');
    });

    /**
     * photo zoom on click
     */
    jQuery('td.photo, .productphoto').on('click', function () {
        content = '<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Image</h4></div><div class="modal-body text-center autophoto">';
        content += jQuery(this).html().replace("thumb_", "");
        content += '</div>';
        jQuery('#SimpleModel').find('.modal-content').html(content);
        jQuery('#SimpleModel').modal('show');
    });

    /**
     * multiple checkbox for check all
     */
    jQuery('.MultiCheckAll').click(function (e) {
        if (this.checked) {
            jQuery('.Chk').each(function (e) {
                this.checked = true;
            });
        }
        else {
            jQuery('.Chk').each(function (e) {
                this.checked = false;
            });
        }
    });
    jQuery('.Chk').on('change', function () {
        if ($(".Chk:checked").length === $(".Chk").length) {
            jQuery('.MultiCheckAll').attr('checked', 'checked');
        } else {
            jQuery('.MultiCheckAll').removeAttr('checked');
        }
    }).trigger('change');
});

/**
 * For the Check the any input is available in search panel
 * @param {type} formName
 * @param {type} fields
 * @param {type} button
 * @returns {undefined}
 */
function validateSearch(formName, fields, button) {
    checkValidation(formName, fields, button);
    jQuery('#' + formName + ' input').on('keyup', function () {
        checkValidation(formName, fields, button);
    });
    jQuery('#' + formName + ' input:checkbox').on('change', function () {
        checkBoxValue = false;
        if (jQuery(this).is(':checked')) {
            checkBoxValue = true;
        }
        checkValidation(formName, fields, button, checkBoxValue);
    });
    jQuery('#' + formName + ' input').on('focusin', function () {
        checkValidation(formName, fields, button);
    });
    jQuery('#' + formName + ' input').on('keypress', function () {
        checkValidation(formName, fields, button);
    });

    jQuery('#' + formName + ' select').on('change', function () {
        checkValidation(formName, fields, button);
    });

}

function checkValidation(formName, fields, button, checkBoxValue) {
    var noOfBlankFields = 0;
    var fieldLen = fields.length;
    for (var i = 0; i < fieldLen; i = i + 1) {
        if (fields[i] != '') {
            if (jQuery("#" + fields[i]).length > 0) {
                if (jQuery("#" + fields[i]).val() == '') {
                    noOfBlankFields = noOfBlankFields + 1;
                }
            } else {
                fields = removeElementArr(fields, fields[i]);
            }
        }

    }
    fieldLen = fields.length;
    if (noOfBlankFields === fieldLen) {
        jQuery("#" + formName + " button[type='submit']").addClass('disabled');
        jQuery("." + button).addClass('disabled');
    } else {
        jQuery("#" + formName + " button[type='submit']").removeClass('disabled');
        jQuery("." + button).removeClass('disabled');
    }
    if (jQuery('#' + formName).valid()) {
    } else {
        jQuery("#" + formName + " button[type='submit']").addClass('disabled');
        jQuery("." + button).addClass('disabled');
    }
}

function removeElementArr(Arr, value)
{
    return jQuery.grep(Arr, function (elem, index) {
        return elem !== value;
    });
}
