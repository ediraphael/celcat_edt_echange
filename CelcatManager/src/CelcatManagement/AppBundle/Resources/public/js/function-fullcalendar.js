var two_selected_events = new Array();
var removed_schedule_modification = null;
var droped_event_modification = null;
var resized_event_modification = null;

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
                        calendars: [calendarFile]
                    },
                    beforeSend: function () {
                        $("#loading").show();
                    },
                    success: function () {
                        $("#loading").hide();
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

    if (removed_schedule_modification) {
        data['removed_schedule_modification'] = removed_schedule_modification;
    }

    if (droped_event_modification) {
        data['droped_event_modification'] = droped_event_modification;
    }

    if (resized_event_modification) {
        data['resized_event_modification'] = resized_event_modification;
    }

    eventSources =
            {
                url: Routing.generate('fullcalendar_refresh'),
                type: 'POST',
                // A way to add custom filters to your event listeners
                data: data,
                beforeSend: function () {
                    $("#loading").show();
                },
                success: function () {
                    $("#loading").hide();
                },
                error: function () {
                    //   alert('There was an error while fetching Google Calendar!');
                }
            }
    ;
    $('#calendar-holder').fullCalendar('addEventSource', eventSources);
    $('.popover').remove();

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
            $('#schedule_modification_container').html('');
            for (i = 0; i < response.length; i++) {
                var chaine1 = response[i].firstEvent.title +" "+(response[i].firstEvent.group?"("+response[i].firstEvent.group+")":"");
                var chaine2 =  response[i].secondEvent?response[i].secondEvent.title +" "+ (response[i].secondEvent.group?"("+response[i].secondEvent.group+")":""):"";
                var icone = "";
                if(response[i].modificationType === "swap") {
                    icone = " <i class=\"glyphicon glyphicon-resize-horizontal\"></i> ";
                }   
                else if (response[i].modificationType === "drop") {
                    icone = " <i class=\"glyphicon glyphicon-object-align-left\"></i> ";
                }
                else if(response[i].modificationType === "resize") {
                    icone = " <i class=\"glyphicon glyphicon-object-align-top\"></i> ";
                }
                $('#schedule_modification_container').append("" +
                        "<div class=\"alert alert-info alert-dismissible\" role=\"alert\">" +
                        "<button type\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\" onClick=\"removeScheduleModification('" + response[i].firstEvent.id + "')\"><span aria-hidden=\"true\">&times;</span></button>" +
                        chaine1+icone+chaine2+
                        "</div>"
                        );
            }
        },
        error: function (req, status, error) {
            console.error(error);
        }
    });
}

function removeScheduleModification(eventId) {
    removed_schedule_modification = eventId;
    refreshCalendarEvents();
    removed_schedule_modification = null;
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
            right: 'today, month, agendaWeek'
        },
        defaultView: 'agendaWeek',
        allDaySlot: false,
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
        eventClick: function (event, jsEvent, view) {
            console.log(event);
            if ((event.canClick || (two_selected_events.length > 0))) {
                var arrayEvents = new Array();
                $(this).toggleClass("selected_event");
                if (event.isEventSource === "1" || (two_selected_events.length > 0 && two_selected_events[0].id == event.id)) {

                    two_selected_events = new Array();
                }
                else {
                    two_selected_events.push(event);
                }

                if (two_selected_events.length == 2) {

                    if (event.canClick && event.isSwapable) {
                        if (!confirm("Voulez vous vraiment échanger ces deux évennements?")) {
                            two_selected_events = new Array();
                        }
                        refreshCalendarEvents();
                        two_selected_events = new Array();
                        refreshCalendarEvents();
                    }
                    else {
                        two_selected_events.splice(1, 1);
                        $(this).toggleClass("selected_event");
                    }
                }
                else {
                    refreshCalendarEvents();
                }
            }
        },
        eventDrop: function (event, delta, revertFunc) {
            console.log(event);
            droped_event_modification = JSON.stringify(event);
            refreshCalendarEvents();
            droped_event_modification = null;
        },
        eventResize: function (event, delta, revertFunc) {
            console.log(event);
            resized_event_modification = JSON.stringify(event);
            refreshCalendarEvents();
            resized_event_modification = null;
        },
        eventMouseover: function (event) {
            var dateDebut = new Date(event.start);
            var dateFin = new Date(event.end);  
            $(this).popover({
                trigger: 'hover',
                title: event.title,
                content: 'Debut: ' + dateDebut.toLocaleString() + '<br />' +
                        'Fin:' + dateFin.toLocaleString() + '<br />'+
                        'Salle:' + event.room + '<br />'+
                        'Module: ' + event.module + '<br />'+
                        'Prof:' + event.professors + '<br />'+
                        'Groupe:' + event.group + '<br />'+
                        'Note:' + event.note
                ,
                html: true,
                container: "body"
            });


        },
    });

});
