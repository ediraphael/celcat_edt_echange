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
        editable: true,

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
                    calendar: 'g200872.xml'
                },
                error: function () {
                    //   alert('There was an error while fetching Google Calendar!');
                }
            }
        ]
    });
});
