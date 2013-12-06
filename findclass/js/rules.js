<script>
$(function() {
	var name = $("#name"),
		email = $("#email"),
		ssn = $("#ssn"),
		dob = $("#dob"),
		mmn = $("#mmn"),
		notes = $("#notes"),
		allFields = $([]).add(name).add(email).add(ssn).add(dob).add(notes),
		tips = $(".validateTips");

	function updateTips(t) {
		tips.text(t).addClass("ui-state-highlight");
		setTimeout(function() {
			tips.removeClass( "ui-state-highlight", 2000 );
		},500);
	}
	function checkLength(o,n,min,ma) {
		if (o.val().length > max || o.val().length < min) {
			o.addClass( "ui-state-error" );
			if (min==max) {
				updateTips( "Length of " + n + " must be " + min + " characters in length." );
			} else {
				updateTips( "Length of " + n + " must be between " + min + " and " + max + "." );
			}
			return false;
		} else {
			return true;
		}
	}
	function checkRegexp(o,regexp,n) {
		if (!(regexp.test(o.val()))) {
			o.addClass("ui-state-error");
			updateTips(n);
			return false;
		} else {
			return true;
		}
	}
	function checkDate(o,input) {
		var validformat=/^\d{2}\/\d{2}\/\d{4}$/;
		if (validformat.test(input.value)) {
			va=input.split("/");
			if (va.length==3) {
				var dayobj = new Date(va[2],va[0]-1,va[1]);
				if (!((dayobj.getMonth()+1!=va[0]) ||(dayobj.getDate()!=va[1])||(dayobj.getFullYear()!=va[2]))) {
					return true;
				}
			}
		}
		return false;
	}
	$("#dialog-form").dialog({
		autoOpen: false,
		height: 425,
		width: 600,
		modal: true,
		buttons: {
			"Save User Information": function() {
				var bValid = true;
				allFields.removeClass( "ui-state-error" );
					bValid = bValid && checkLength( name, "username", 3, 16 );
				if ($("#ssn").val().length>0) {
					bValid = bValid && checkLength(ssn,"ssn",9,9);
					bValid = bValid && checkRegexp( ssn, /^([0-9])+$/, "SSN field only allows: 0-9" );
				}
				if ($("#dob").val().length>0) bValid = bValid && checkDate(dob,$("#dob".val());
				if ( bValid ) {
// save user
// regen user list
					$( this ).dialog("close");
				}
			},
			Cancel: function() {
				$(this).dialog("close");
			}
		},
		close: function() {
			allFields.val("").removeClass("ui-state-error");
		}
	});
//
//	general initialization stuff
//
	$("#create-user").click(function() {
		$("#dialog-form").dialog("open");
	});
});
//
//	General routines
//
function addRule() {
}
function setCursor(e) {
	e.style.cursor="pointer";
}
function clearCursor(e) {
	e.style.cursor="default";
}
function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}
</script>
