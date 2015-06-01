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
        eventClick: function (calEvent, jsEvent, view) {
//            alert('Event: ' + calEvent.title);
//          console.log(calEvent);
            $(this).css('border-color', 'red');
            calEvent.editable = !calEvent.editable;


        }
    });
   loadCalendarEvents($('#groupe_select'));
});
