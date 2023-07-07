/**
 * Positive Number validation
 * @param {type} param1
 * @param {type} param2
 * @param {type} param3
 */
$.validator.addMethod('positiveNumber', function (value) {
    return Number(value) > 0;
}, 'Enter a positive number.');

/**
 * Length validation like 10 than you must enter 10 character
 */
$.validator.addMethod('matchNumber', function (value, element, params) {
    value = value.replace(/[-_]/g, "");
    return value.length == params;
}, 'Please enter a valid digit.');

/**
 * ssn number validation (US)
 */
$.validator.addMethod("ssnNumber", function (value, element) {
    return this.optional(element) ||
            value.match(/^\d{3}-\d{2}-\d{4}$/);
}, "Please enter a valid SSN number");

/**
 * Us Phone Number validation
 */
jQuery.validator.addMethod("phoneUS", function (phone_number, element) {
    phone_number = phone_number.replace(/\s+/g, "");
    return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^\+[0-9]{11}$/);
}, "Please specify a valid phone number");

/**
 * Phone Number validation
 */
jQuery.validator.addMethod("phoneno", function (phone_number, element) {
    phone_number = phone_number.replace(/\s+/g, "");
    return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
}, "<br />Please specify a valid phone number");

/**
 * Slug Validation rule
 */
$.validator.addMethod("slug", function (value, element) {
    //return this.optional(element) || value === "NA" ||value.match(/^[\w\-_]+$/);
    return this.optional(element) || value === "NA" || value.match(/^[a-z0-9-_]+$/);
}, "Please enter a valid ");
