/* global FullCalendar:readonly */

/* global Swal:readonly */

jQuery(document).ready(function ($) {


  window.events = []

  let calendarEl = document.getElementById('calendar');
  window.calendar = new FullCalendar.Calendar(calendarEl, {

    eventClick: function (info) {

      let week = info.event.week

      if (info.event.id.includes('week_')) {

        window.events = window.events.filter(function (event) {
          return event.id != info.event.id;
        })

        info.event.remove()

      } else {

        $.post(window.baseurl + '/wp-json/agrispesa/v1/delete-user-blocked-weeks',
          {
            userId: window.userId,
            day: info.event.start.toISOString()
          });

        info.event.remove()

      }
    },
    dateClick: function (info) {
      let curr = info.date

      let first = curr.getDate() - curr.getDay() + 1; // First day is the day of the month - the day of the week
      let last = first + 7; // last day is the first day + 6

      let firstday = new Date(curr.setDate(first));
      let lastday = new Date(curr.setDate(last));

      var year = new Date(curr.getFullYear(), 0, 1);
      var days = Math.floor((curr - year) / (24 * 60 * 60 * 1000));
      let week = Math.ceil((curr.getDay() + 1 + days) / 7);
      week -= 1

      let hasEvent = window.events.filter(function (event) {
        return event.week == week;
      })

      if (hasEvent.length > 0) {

        window.events = window.events.filter(function (event) {
          return event.id != 'week_' + week;
        })

        let event = window.calendar.getEventById(hasEvent[0].id)
        event.remove()

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

    Swal.fire({
      title: 'Non vuoi ricevere la box?',
      text: "Se confermi la tua scelta non riceverai la box per le settimane che hai selezionato.",
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
            events: window.events
          }, function (data) {

            let events = window.calendar.getEvents()
            events.forEach(function (event) {
              if (event.id != '') {
                event.remove()
              }
            })
            window.calendar.refetchEvents()
          });
      }
    })
  })

})
