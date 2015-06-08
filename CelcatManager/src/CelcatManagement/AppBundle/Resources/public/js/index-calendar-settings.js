
function loadCalendarEvents(objet) {
    file = $(objet).find(':selected').attr('value');
    removeCalendarEvents();
    addCalendarEventSource(file);
}

$(function () {
    loadCalendarEvents($('#groupe_select'));
});

