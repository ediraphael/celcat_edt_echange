var two_selected_events = new Array();

function loadCalendarEvents(objet) {
    file = $(objet).find(':selected').attr('value');
    removeCalendarEvents();
    addCalendarEventSource(file);
}


function refreshCalendarEvents() {
    removeCalendarEvents();
    refreshCalendarEventSource();
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
    eventSources =
            {
                url: Routing.generate('fullcalendar_refresh'),
                type: 'POST',
                // A way to add custom filters to your event listeners
                data: {
 
                },
                error: function () {
                    //   alert('There was an error while fetching Google Calendar!');
                }
            }
    ;
    $('#calendar-holder').fullCalendar('addEventSource', eventSources);

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

$(function () {
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    $('#calendar-holder').fullCalendar({
        header: {
            left: 'prev, next',
            center: 'title',
            right: ''
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
            // for agendaWeek and agendaDay
            agenda: 'h:mm', // 5:00 - 6:30

            // for all other views
            '': 'h:mm'         // 7p
        },
//        eventSources: [
//            {
//                url: Routing.generate('fullcalendar_loader'),
//                type: 'POST',
//                // A way to add custom filters to your event listeners
//                data: {
//                    calendar: ''
//                },
//                error: function () {
//                    //   alert('There was an error while fetching Google Calendar!');
//                }
//            }
//        ],
        eventClick: function (calEvent, jsEvent, view) {
            var arrayEvents = new Array();
            $(this).toggleClass("selected_event");
            if (calEvent.color == "orange")
            {
                calEvent.color = "";
                calEvent.editable = false;
                if (two_selected_events[0] != undefined && two_selected_events[0].id == calEvent.id)
                    two_selected_events.splice(0, 1);
                if (two_selected_events[1] != undefined && two_selected_events[1].id == calEvent.id)
                    two_selected_events.splice(1, 1);
            }
            else
            {
                calEvent.color = "orange";
                calEvent.editable = true;
                if (two_selected_events.length < 2)
                    two_selected_events.push(calEvent);
                if (two_selected_events.length < 2)
                {
                    //chargement des créneaux liés a la formation du créneau selectionné
                    reloadCalendarEvents(calEvent, arrayEvents);
                }
            }
            //récupération de tous les créneaux du calendrié + vérification de la possibilité de swap
            if (two_selected_events.length == 1 && calEvent.color == "orange")
            {
                markUpAlterableEvents(calEvent);
            }
            //dans le cas ou on choisi deux créneaux pour les swapé
            else if (two_selected_events.length == 2)
            {
                var array_objects_events = new Array();
                array_objects_events.push({id: two_selected_events[1].id, day: two_selected_events[1].day, week: two_selected_events[1].week, formations: two_selected_events[1].formations});
                if (canSwapTwoEvents(two_selected_events[0], array_objects_events)[0].result)
                {
                    if (confirm("Voulez vous vraiment échanger ces deux évennements?")) {
                        swapTwoEvents(two_selected_events[0], two_selected_events[1]);
                    }
                    else {
                        calEvent.color = "";
                        two_selected_events.splice(1, 1);
                        two_selected_events[0].color = "";
                        resetEventsColor(two_selected_events[0]);
                        two_selected_events.splice(0, 1);
                    }
                }
            }
            //dans le cas ou ont déselectionne un créneau
            else
            {
                if (two_selected_events.length == 0)
                {
                    resetEventsColor(calEvent);
                }
            }
            $('#calendar-holder').fullCalendar('addEventSource', arrayEvents);
        }
    });
    loadCalendarEvents($('#groupe_select'));
});


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
                response[i].color = "red";
                arrayEvents.push(response[i]);
            }
        },
        error: function (req, status, error) {
            console.err(error);
        }
    });
}

function canSwapTwoEvents(event_source, array_events_destination)
{
    var result = false;
    $.ajax({
        type: "POST",
        async: false,
        url: Routing.generate('can_swap_two_events'),
        data: {
            event_source: {id: event_source.id, day: event_source.day, week: event_source.week, formations: event_source.formations},
            events_destination: array_events_destination
        },
        success: function (response)
        {
            result = response;
        },
        error: function (req, status, error) {
            console.err(error);
        }
    });
    return result;
}

function resetEventsColor(calEvent)
{
    var array_events_calendar = $('#calendar-holder').fullCalendar('clientEvents');
    for (var i = 0; i < array_events_calendar.length; i++)
    {
        if (array_events_calendar[i].id != calEvent.id)
        {
            array_events_calendar[i].color = "";
        }
    }
}

function markUpAlterableEvents(calEvent)
{
    var array_events_calendar = $('#calendar-holder').fullCalendar('clientEvents');
    var array_objects_events = new Array();
    for (var i = 0; i < array_events_calendar.length; i++)
    {
        if (array_events_calendar[i].id != calEvent.id)
        {
            array_objects_events.push({id: array_events_calendar[i].id, day: array_events_calendar[i].day, week: array_events_calendar[i].week, formations: array_events_calendar[i].formations});
        }

    }
    var array_return_values = canSwapTwoEvents(two_selected_events[0], array_objects_events);
    for (var i = 0; i < array_return_values.length; i++)
    {
        if (array_return_values[i].result)
        {
            for (var j = 0; j < array_events_calendar.length; j++)
            {
                if (array_events_calendar[j].id == array_return_values[i].id)
                    array_events_calendar[j].color = "green";
            }
        }
    }
}

function swapTwoEvents(event_source, event_destination)
{
    $.ajax({
        type: "POST",
        async: false,
        url: Routing.generate('swap_two_events'),
        data: {
            event_source: {id: event_source.id, day: event_source.day, week: event_source.week},
            event_destination: {id: event_destination.id, day: event_destination.day, week: event_destination.week}
        },
        success: function (response)
        {
            if (response)
            {
                console.log(two_selected_events);
                refreshCalendarEvents();
                two_selected_events = [];

                console.log(two_selected_events);
            }
            else {
                alert("Une erreur s'est produite lors du chagement des créneaux");
            }
        },
        error: function (req, status, error) {
            console.err(error);
        }
    });
}