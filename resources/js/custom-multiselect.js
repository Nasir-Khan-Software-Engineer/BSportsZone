
// Toggle active class on click
$(document).on("click", ".list-item", function() {
    $(this).toggleClass("active");
});

// Update hidden input with assigned IDs
function updateAssignedIds() {
    let select = $("#assigned_ids");
    select.empty(); // clear previous
    $("#right-list .list-item").each(function() {
        let id = $(this).data("id");
        select.append(new Option(id, id, true, true));
    });
}

// Move selected to right
$("#move-right").click(function() {
    $("#left-list .list-item.active").removeClass("active").appendTo("#right-list");
    updateAssignedIds();
});

// Move all to right
$("#move-all-right").click(function() {
    $("#left-list .list-item").removeClass("active").appendTo("#right-list");
    updateAssignedIds();
});

// Move selected to left
$("#move-left").click(function() {
    $("#right-list .list-item.active").removeClass("active").appendTo("#left-list");
    updateAssignedIds();
});

// Move all to left
$("#move-all-left").click(function() {
    $("#right-list .list-item").removeClass("active").appendTo("#left-list");
    updateAssignedIds();
});

// Search filter
$(".search-box").on("keyup", function() {
    var searchText = $(this).val().toLowerCase();
    var listContainer = $(this).next(".list-container");

    listContainer.find(".list-item").each(function() {
        var text = $(this).text().toLowerCase();
        $(this).toggle(text.indexOf(searchText) > -1);
    });
});