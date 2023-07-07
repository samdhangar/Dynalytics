function getErrorMessage(thisEle) {
  var action =
    jQuery(thisEle).data("action") != undefined
      ? jQuery(thisEle).data("action")
      : "analytics/getTransactionDetail/";
  loader("show");
  jQuery.ajax({
    url: BaseUrl + action + jQuery(thisEle).data("id"),
    type: "post",
    success: function (response) {
      loader("hide");
      jQuery("#smsModel .modal-content").html(response);
      jQuery("#smsModel").modal("show");
    },
    error: function () {
      jQuery("#smsModel").modal("hide");
      loader("hide");
    },
  });
}

function setBranchSession(branchId) {
  if (branchId != undefined && branchId != "") {
    loader("show");
    window.location.href = BaseUrl + "users/set_branch_session/" + branchId;
  }
}


jQuery(document).on("ready", function () {
  jQuery("td.photo, .productphoto").on("click", function () {
    content =
      '<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Image</h4></div><div class="modal-body text-center autophoto">';
    content += jQuery(this).html().replace("thumb_", "");
    content += "</div>";
    jQuery("#SimpleModel").find(".modal-content").html(content);
    jQuery("#SimpleModel").modal("show");
  });

  /**
   * change status of the ticket
   */
  jQuery(".ticketStatus").on("click", function (e) {
    e.preventDefault();
    var message = jQuery(this).data("message");
    var ticketUrl = jQuery(this).attr("href");
    if (confirm(message)) {
      loader("show");
      jQuery.ajax({
        url: ticketUrl,
        type: "get",
        success: function (response) {
          loader("hide");
          jQuery("#smsModel .modal-content").html(response);
          jQuery("#smsModel").modal("show");
        },
        error: function () {
          jQuery("#smsModel").modal("hide");
          loader("hide");
        },
      });
    }
  });

  $("input.date").datepicker({ format: "yyyy-mm-dd", autoclose: true });
  // $("input.monthDate").datepicker({
  //   changeMonth: true,
  //   changeYear: true,
  //   showButtonPanel: true,
  //   dateFormat: "MM yy",
  //   onClose: function (dateText, inst) {
  //     $(this).datepicker(
  //       "setDate",
  //       new Date(inst.selectedYear, inst.selectedMonth, 1)
  //     );
  //   },
  // });

  var dp=$("input.monthDate").datepicker( {
    format: "MM yyyy",
    startView: "months", 
    minViewMode: "months"
});



  $("input.phoneNumber").inputmask("(999)999-9999");

  jQuery("#UserPhoto").on("change", function () {
    jQuery("#photo-name").html(jQuery(this).val());
    input = this;
    isvalid = true;
    if (typeof multiple == "undefined" || multiple == null || multiple == "") {
      if (
        typeof input.files[0]["type"] == "undefined" ||
        $.inArray(input.files[0]["type"], [
          "image/jpeg",
          "image/jpg",
          "image/png",
          "image/gif",
        ]) == -1
      ) {
        isvalid = false;
        //                console.log(element.files[0]);
      }
    } else {
      for (i = 0; i < input.files.length; i++) {
        if (
          typeof input.files[i]["type"] == "undefined" ||
          $.inArray(input.files[i]["type"], [
            "image/jpeg",
            "image/jpg",
            "image/png",
            "image/gif",
          ]) == -1
        ) {
          isvalid = false;
          //error occur
        }
      }
    }

    if (isvalid && this.files.length < 2) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
          $("#UserProfileImageId").find("img").attr("src", e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
      }
    }
  });

  if (jQuery("#UserEditProfileForm tbody tr td").size() <= 1) {
    $("#deleteAll").attr("disabled", true);
  }
  $(".checkAll").click(function () {
    $(".deleteRow").prop("checked", this.checked);
    if ($("#DeleteBtn").hasClass("disabled")) {
      $("#DeleteBtn").removeClass("disabled");
    }
    if (jQuery(".disabledBtn").hasClass("disabled")) {
      jQuery(".disabledBtn").removeClass("disabled");
    }
    jQuery(".disabledBtn").removeAttr("disabled");
    if ($("input:checkbox:checked").length == 0) {
      $("#DeleteBtn").addClass("disabled");
      jQuery(".disabledBtn").addClass("disabled");
      jQuery(".disabledBtn").attr("disabled", "disabled");
    }
  });

  $(".deleteRow").click(function () {
    if ($("input:checkbox").length - 1 == $("input:checkbox:checked").length) {
      $(".checkAll").prop("checked", this.checked);
    }
    if ($("#DeleteBtn").hasClass("disabled")) {
      $("#DeleteBtn").removeClass("disabled");
    }

    if (jQuery(".disabledBtn").hasClass("disabled")) {
      jQuery(".disabledBtn").removeClass("disabled");
    }
    jQuery(".disabledBtn").removeAttr("disabled");

    if ($("input:checkbox:checked").length == 0) {
      $("#DeleteBtn").addClass("disabled");
      jQuery(".disabledBtn").addClass("disabled");
      jQuery(".disabledBtn").attr("disabled", "disabled");
    }
  });

  jQuery(".deleteAllForm").on("submit", function (e) {
    if (!confirm(jQuery(this).data("confirm"))) {
      e.preventDefault();
    }
  });

  jQuery(".transactionmessage").on("click", function () {
    var action =
      jQuery(this).data("action") != undefined
        ? jQuery(this).data("action")
        : "analytics/getTransactionDetail/";
    loader("show");
    jQuery.ajax({
      url: BaseUrl + action + jQuery(this).data("id"),
      type: "post",
      success: function (response) {
        loader("hide");
        jQuery("#smsModel .modal-content").html(response);
        jQuery("#smsModel").modal("show");
      },
      error: function () {
        jQuery("#smsModel").modal("hide");
        loader("hide");
      },
    });
  });

  jQuery(".ticketError").on("click", function () {
    loader("show");
    jQuery.ajax({
      url: BaseUrl + "tickets/getDetail/" + jQuery(this).data("id"),
      type: "post",
      success: function (response) {
        loader("hide");
        jQuery("#smsModel .modal-content").html(response);
        jQuery("#smsModel").modal("show");
      },
      error: function () {
        jQuery("#smsModel").modal("hide");
        loader("hide");
      },
    });
  });
});
jQuery(document).ready(function () {
  jQuery("#helpIcon").on("click", function () {
    var name = $("#helpIcon").attr("data-id");
    var formData = new FormData();
    formData.append("formData", name);
    console.log(formData);
    console.log(BaseUrl);
    jQuery.ajax({
      url: BaseUrl + "analytics/displayhelpPage/",
      data: formData,
      processData: false,
      contentType: false,
      type: "post",
      success: function (response) {
            jQuery("#HelpModel .modal-content").html(response);
            jQuery("#HelpModel").modal("show");
      },
      error: function () {
        // jQuery("#smsModel").modal('hide');
        // loader('hide');
      },
    });
  });
});
