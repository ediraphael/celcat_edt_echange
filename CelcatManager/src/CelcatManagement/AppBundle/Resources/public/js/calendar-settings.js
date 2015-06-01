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
                    calendar: 'g141890.xml'
                },
                error: function () {
                    console.err('There was an error while fetching Calendar!');
                }
            }
        ],
        eventClick: function(calEvent, jsEvent, view) {
            //$(this).toggleClass('selected_event');
            calEvent.color = "green";
            calEvent.editable = true;
            var arrayEvents = new Array();
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
                    $('#calendar-holder').fullCalendar( 'addEventSource', arrayEvents );
//                    $('#calendar-holder').fullCalendar( 'refetchEvents' );
                },
                error: function(req, status, error) {
                    console.err(error);
                }
            });
        }
    });
});
