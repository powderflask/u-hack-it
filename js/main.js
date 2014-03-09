/*
 * Script to handle on-page form logic - just to keep things simple.
 */ 
function doSearch() {
	var term = $("form[role='search'] input[name='search']").val();
	var results = "<h4>Search results for: '" + term + "'</h4><p> ... </p>";
	$("#search-results").html(results);
}

function doUrlSearch(term) {
	term = decodeURIComponent(term);
	var results = "<h4>Search results for: '" + term + "'</h4><p> ... </p>";
	$("#url-search-results").html(results);
}

// Obtain a URL parameter with given name, or False
function urlParam(name){
    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null){
       return null;
    }
    else{
       return results[1] || 0;
    }
}
function setupEvents() {
	if (urlParam('search')) {
		doUrlSearch(urlParam('search'));
		$("#collapseOne").removeClass('in');
		$("#collapseTwo").addClass('in');
	}
	if (urlParam('session_logic') == 'userid') {
		$("#collapseOne").removeClass('in');
		$("#collapseTwo").addClass('in');
	}
	if (urlParam('session_logic') == 'session_key') {
		$("#collapseOne").removeClass('in');
		$("#collapseThree").addClass('in');
	}
	$("form[role='search'] button.search").click( doSearch );
}

$(document).ready( setupEvents );
