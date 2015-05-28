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
        firstHour: 8,
        slotMinutes: 30,
        axisFormat: 'HH:mm',
        dragOpacity: {
            agenda: .5
        },

        timeFormat: {
            // for agendaWeek and agendaDay
            agenda: 'h:mmt', // 5:00 - 6:30

            // for all other views
            '': 'h:mmt'         // 7p
        },
        eventSources: [
            {
                url: Routing.generate('fullcalendar_loader'),
                type: 'POST',
                // A way to add custom filters to your event listeners
                data: {
                },
                error: function () {
                    //   alert('There was an error while fetching Google Calendar!');
                }
            }
        ]
    });
});
