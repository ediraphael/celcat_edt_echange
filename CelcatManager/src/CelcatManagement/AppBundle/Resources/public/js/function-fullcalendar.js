var two_selected_events = new Array();

function refreshCalendarEvents() {
    removeCalendarEvents();
    refreshCalendarEventSource();
    loadCalendarModifications();
}


function addCalendarEventSource(calendarFile) {
    if (typeof calendarFile !== 'undefined') {
        eventSources =
                {
                    url: Routing.generate('fullcalendar_loader'),
                    type: 'POST',
                    // A way to add custom filters to your event listeners
                    data: {
                        calendars: calendarFile
                    },
                    error: function () {
                        //   alert('There was an error while fetching Google Calendar!');
                    }
                }
        ;
        $('#calendar-holder').fullCalendar('addEventSource', eventSources);
    }
}


function refreshCalendarEventSource() {
    var data = new Array();

    if (two_selected_events[0]) {
        data['event_source'] = {
            id: two_selected_events[0].id,
            day: two_selected_events[0].day,
            week: two_selected_events[0].week,
            formations: two_selected_events[0].formations
        };

    }
    if (two_selected_events[1]) {
        data['event_destination'] = {
            id: two_selected_events[1].id,
            day: two_selected_events[1].day,
            week: two_selected_events[1].week,
            formations: two_selected_events[1].formations
        };

    }

    eventSources =
            {
                url: Routing.generate('fullcalendar_refresh'),
                type: 'POST',
                // A way to add custom filters to your event listeners
                data: data,
                error: function () {
                    //   alert('There was an error while fetching Google Calendar!');
                }
            }
    ;
    $('#calendar-holder').fullCalendar('addEventSource', eventSources);

}

function reloadCalendarEvents(calEvent, arrayEvents)
{
    $.ajax({
        type: "POST",
        async: false,
        url: Routing.generate('event_calendar_loader'),
        data: {
            calendars: calEvent.formations
        },
        success: function (response)
        {
            for (var i = 0; i < response.length; i++)
            {
                response[i].backgroundColor = "red";
                response[i].editable = false;
                arrayEvents.push(response[i]);
            }
            $('#calendar-holder').fullCalendar('addEventSource', arrayEvents);
        },
        error: function (req, status, error) {
            console.error(error);
        }
    });
}


function removeCalendarEvents() {
    $('#calendar-holder').fullCalendar('removeEvents');
    $('#calendar-holder').fullCalendar('removeEventSource',
            {
                url: Routing.generate('fullcalendar_loader')
            }
    );
    $('#calendar-holder').fullCalendar('removeEventSource',
            {
                url: Routing.generate('fullcalendar_refresh')
            }
    );
}


function loadCalendarModifications()
{
    $.ajax({
        type: "POST",
        async: false,
        url: Routing.generate('calendar_modifications'),
        data: {
        },
        success: function (response)
        {
            console.log(response);
            $('#schedule_modification_container').html('');
            for (i = 0; i < response.length; i++) {
                $('#schedule_modification_container').append("" +
                        "<div class=\"alert alert-info alert-dismissible\" role=\"alert\">" +
                        "<button type\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                        "<strong>" + response[i].modificationType + "</strong>  " + response[i].firstEvent.title + " <-> " + response[i].secondEvent.title +
                        "</div>"
                        );
            }
        },
        error: function (req, status, error) {
            console.error(error);
        }
    });
}

$(function () {
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    $('#calendar-holder').fullCalendar({
        header: {
            left: 'prev, next',
            center: 'title',
            right: 'today'
        },
        defaultView: 'agendaWeek',
        allDaySlot: true,
        slotDuration: '00:30:00',
        axisFormat: 'HH:mm',
        maxTime: '21:00',
        minTime: '7:00',
        dragOpacity: {
            agenda: .5
        },
        editable: false,
        timeFormat: {
            agenda: 'HH:mm', 

            '': 'HH:mm'       
        },
        eventClick: function (calEvent, jsEvent, view) {
            if ((calEvent.canClick || (two_selected_events.length > 0)) && calEvent.backgroundColor != "purple") {
                var arrayEvents = new Array();
                $(this).toggleClass("selected_event");
                if (calEvent.color == "orange" || (two_selected_events.length > 0 && two_selected_events[0].id == calEvent.id)) {

                    two_selected_events = [];
                }
                else {
                    two_selected_events.push(calEvent);
                }
                
                if (two_selected_events.length == 2) {
                    if (calEvent.color = "green") {
                        if (!confirm("Voulez vous vraiment échanger ces deux évennements?")) {
                            two_selected_events = [];
                        }
                        refreshCalendarEvents();
                        two_selected_events = [];
                    }
                }
                refreshCalendarEvents();
            }
        }
    });

});
