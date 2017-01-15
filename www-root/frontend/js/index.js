window.ironman = {};

window.ironman.init = function () {
	$('.datepicker').pickadate();

	$('.filter-search').click(function () {
		// pull data from filter pane input elements
		var jsonRequestData = {};
		appendIfNonNull(jsonRequestData, "date_start", getDateStart());
		appendIfNonNull(jsonRequestData, "date_end", getDateEnd());
		appendIfNonNull(jsonRequestData, "tags", getTags());
		appendIfNonNull(jsonRequestData, "names", getNames());

		// turn into request object
		var urlData = $.param(jsonRequestData);

		// call d3
		window.alert(urlData);
	});

	function appendIfNonNull (obj, key, val) {
		if(val != null){
			obj[key] = val;
		}
	}

	function getDateStart () {
		var rawVal = $('.filter-input-date-start').val();
		var formattedVal = moment(rawVal, "DD MMMM, YYYY").format('YYYY-MM-DD');
		return formattedVal;
	}

	function getDateEnd () {
		var rawVal = $('.filter-input-date-end').val();

		if(!rawVal){
			return null;
		}

		var formattedVal = moment(rawVal, "DD MMMM, YYYY").format('YYYY-MM-DD');
		return formattedVal;
	}

	function getTags () {
		var rawVal = $('.filter-input-tags').materialtags('items');
		return rawVal;
	}

	function getNames () {
		var rawVal = $('.filter-input-names').materialtags('items');
		return rawVal;
	}
};

window.ironman.init();