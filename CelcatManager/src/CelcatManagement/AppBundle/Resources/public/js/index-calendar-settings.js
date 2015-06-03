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
            if(two_selected_events.length < 2 && calEvent.color == "green")
            {
                //chargement des créneaux liés a la formation du créneau selectionné
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
            if(two_selected_events.length == 1)
            {
                //récupération de tous les créneaux du calendrié + vérification de la possibilité de swap
                var array_events_calendar = $('#calendar-holder').fullCalendar('clientEvents');
                for(var i =0; i<array_events_calendar.length; i++)
                {
                    if(array_events_calendar[i].id != calEvent.id)
                    {
                        canSwapTwoEvents(two_selected_events[0], array_events_calendar[i]);
                    }
                }
            }
            //dans le cas ou on choisi deux créneaux pour les swapé
            if(two_selected_events.length == 2)
            {
                canSwapTwoEvents(two_selected_events[0], two_selected_events[1]);
            }
            $('#calendar-holder').fullCalendar( 'addEventSource', arrayEvents );
        }
    });
   loadCalendarEvents($('#groupe_select'));
});


function canSwapTwoEvents(event_source, event_destination)
{
    $.ajax({
        type: "POST",
        async: false,
        url: Routing.generate('swap_two_events'),
        data: {
            event_source: {id:event_source.id, day: event_source.day, week:event_source.week, formation:event_source.formation},
            event_destination: {id:event_destination.id, day: event_destination.day, week:event_destination.week, formation:event_destination.formation}
        },
        success: function(response) 
        {
            if(response)
                event_destination.color = "orange";
        },
        error: function(req, status, error) {
            console.err(error);
        }
    });
}