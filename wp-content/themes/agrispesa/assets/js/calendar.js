/* global FullCalendar:readonly */

/* global Swal:readonly */

jQuery(document).ready(function ($) {


  function isAnOverlapEvent(eventStartDay, eventEndDay) {
    // Events
    var events = window.calendar.getEvents()

    for (let i = 0; i < events.length; i++) {
      const eventA = events[i];

      // start-time in between any of the events
      if (eventStartDay > eventA.start && eventStartDay < eventA.end) {
        return true;
      }
      //end-time in between any of the events
      if (eventEndDay > eventA.start && eventEndDay < eventA.end) {
        return true;
      }
      //any of the events in between/on the start-time and end-time
      if (eventStartDay <= eventA.start && eventEndDay >= eventA.end) {
        return true;
      }
    }
    return false;
  }


  function getMonday(d) {
    d = new Date(d);
    var day = d.getDay(),
      diff = d.getDate() - day + (day == 0 ? -6 : 1); // adjust when day is sunday
    return new Date(d.setDate(diff));
  }

  function addDays(date, days) {
    const dateCopy = new Date(date);
    dateCopy.setDate(date.getDate() + days);
    return dateCopy;
  }

  window.events = []

  let today = new Date();
  /*
    var year = new Date(today.getFullYear(), 0, 1);
    var days = Math.floor((today - year) / (24 * 60 * 60 * 1000));
    let currentWeek = Math.ceil((today.getDay() + 1 + days) / 7);*/
  //currentWeek -= 1

  //const dateCopy = new Date(today.getTime());

  /*const nextAvailableMonday = new Date(
    dateCopy.setDate(
      dateCopy.getDate() + ((7 - dateCopy.getDay() + 1) % 7 || 7),
    ),
  );*/

  //if (today.getDay() > 2) {
  today = addDays(today, 7)
  //}

  let nextAvailableMonday = getMonday(today)


  //let deliveryDay = 4

  let calendarEl = document.getElementById('calendar');
  window.calendar = new FullCalendar.Calendar(calendarEl, {
    validRange: {
      start: nextAvailableMonday
    },
    eventClick: function (info) {

      //let week = info.event.week
      if (info.event.id.includes('week_')) {

        window.events = window.events.filter(function (event) {
          return event.id != info.event.id;
        })

        info.event.remove()

        if (window.events.length == 0) {
          $(".confirm-calendar").hide()
        }

      } else {

        $.post(window.baseurl + '/wp-json/agrispesa/v1/delete-user-blocked-weeks',
          {
            userId: window.userId,
            day: info.event.start.toISOString()
          });

        info.event.remove()

      }
    },
    /*select: function (start, end, allDay) {
      var check = $.fullCalendar.formatDate(start, 'yyyy-MM-dd');
      var today = $.fullCalendar.formatDate(new Date(), 'yyyy-MM-dd');
      if (check < today) {
        // Previous Day. show message if you want otherwise do nothing.
        // So it will be unselectable
      } else {
        // Its a right date
        // Do something
      }
    },*/
    weekNumbers: true,
    weekText: 'Settimana ',
    dateClick: function (info) {


      let curr = info.date


      const hasAlreadyServerEvent = isAnOverlapEvent(curr, curr)
      if (hasAlreadyServerEvent) {
        return false
      }
      //let first = curr.getDate() - curr.getDay() + 1; // First day is the day of the month - the day of the week
      //let last = first + 7; // last day is the first day + 6

      let firstday = getMonday(curr);
      let lastday = addDays(firstday, 7)

      var year = new Date(firstday.getFullYear(), 0, 1);
      var days = Math.floor((firstday - year) / (24 * 60 * 60 * 1000));
      let week = Math.ceil((firstday.getDay() + 1 + days) / 7);
      //week -= 1


      let hasEvent = window.events.filter(function (event) {
        return event.week == week;
      })

      if (hasEvent.length > 0) {

        window.events = window.events.filter(function (event) {
          return event.id != 'week_' + week;
        })

        let event = window.calendar.getEventById(hasEvent[0].id)
        if (event) {
          event.remove()
        }

      } else {
        window.calendar.addEvent({
          title: 'Questa settimana non ricevi la Facciamo Noi',
          start: firstday,
          end: lastday,
          allDay: true,
          classNames: ['temp-event'],
          id: 'week_' + week,
          week: week
        });

        window.events.push({
          week: week,
          start: firstday.toISOString(),
          end: lastday.toISOString(),
          id: 'week_' + week
        })

        $(".confirm-calendar").show()
      }
    },
    eventSources: [
      {
        id: 'api',
        url: window.baseurl + '/wp-json/agrispesa/v1/user-blocked-weeks?userId=' + window.userId
      }
    ],
    firstDay: 1,
    initialView: 'dayGridMonth',
    locale: 'it'
  });
  window.calendar.render();


  $(".confirm-calendar").click(function () {

    let weeks = []
    window.events.forEach(function (event) {
      weeks.push(event.week)
    })


    Swal.fire({
      title: 'Vuoi sospendere la consegna della settimana ' + weeks.join(', ') + '?',
      text: "",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3c21ff',
      cancelButtonColor: '#f8f5f1',
      confirmButtonText: 'Si',
      cancelButtonText: 'Torna indietro',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(window.baseurl + '/wp-json/agrispesa/v1/add-user-blocked-weeks',
          {
            userId: window.userId,
            events: window.events
          }, function (data) {

            let events = window.calendar.getEvents()
            events.forEach(function (event) {
              if (event && event.id != '') {
                event.remove()
              }
            })
            window.calendar.refetchEvents()
          });
      }
    })
  })

})
