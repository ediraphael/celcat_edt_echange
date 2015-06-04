var two_selected_events = new Array();

function loadCalendarEvents(objet) {
    file = $(objet).find(':selected').attr('value');
    removeCalendarEvents();
    addCalendarEventSource(file);
}

function addCalendarEventSource(calendarFile) {
    if (typeof calendarFile !== 'undefined') {
        eventSources =
                {
                    url: Routing.generate('fullcalendar_loader'),
                    type: 'POST',
                    // A way to add custom filters to your event listeners
                    data: {
                        calendar: calendarFile
                    },
                    error: function () {
                        //   alert('There was an error while fetching Google Calendar!');
                    }
                }
        ;
        $('#calendar-holder').fullCalendar('addEventSource', eventSources);
    }
}

function removeCalendarEvents() {
    $('#calendar-holder').fullCalendar('removeEvents');
    $('#calendar-holder').fullCalendar('removeEventSource',
        {
            url: Routing.generate('fullcalendar_loader')
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
        eventSources: [
            {
                url: Routing.generate('fullcalendar_loader'),
                type: 'POST',
                // A way to add custom filters to your event listeners
                data: {
                    calendar: $('#calendar_file_source').val()
                },
                error: function () {
                    //   alert('There was an error while fetching Google Calendar!');
                }
            }
        ],
        eventClick: function(calEvent, jsEvent, view) {
            $(this).toggleClass("selected_event");
            if(calEvent.color == "green")
            {
                calEvent.color = "";
                calEvent.editable = false;
                if(two_selected_events[0] != undefined && two_selected_events[0].id == calEvent.id)
                    two_selected_events.splice(0,1);
                if(two_selected_events[1] != undefined && two_selected_events[1].id == calEvent.id)
                    two_selected_events.splice(1,1);
            }
            else
            {
                calEvent.color = "green";
                calEvent.editable = true;
                if(two_selected_events.length < 2)
                    two_selected_events.push(calEvent);
            }
            var arrayEvents = new Array();
            if(two_selected_events.length < 2)
            {
                $.ajax({
                    type: "POST",
                    async: false,
                    url: Routing.generate('event_calendar_loader'),
                    data: {
                        calendar: calEvent.formation + '.xml'
                    },
                    success: function(response) 
                    {
                        for(var i=0; i<response.length; i++)
                        {
                            response[i].color = "red";
                            arrayEvents.push(response[i]);
                        }

                    },
                    error: function(req, status, error) {
                        console.err(error);
                    }
                });
            }
            $('#calendar-holder').fullCalendar( 'addEventSource', arrayEvents );
            if(two_selected_events.length == 2)
            {
                swapTwoEvents();
            }
        }
    });
   
});


function swapTwoEvents()
{
    $.ajax({
        type: "POST",
        async: false,
        url: Routing.generate('swap_two_events'),
        data: {
            event_source: {id:two_selected_events[0].id, day: two_selected_events[0].day, week:two_selected_events[0].week, formation:two_selected_events[0].formation},
            event_destination: {id:two_selected_events[1].id, day: two_selected_events[1].day, week:two_selected_events[1].week, formation:two_selected_events[1].formation}
        },
        success: function(response) 
        {
            console.log(response);
        },
        error: function(req, status, error) {
            console.err(error);
        }
    });
}