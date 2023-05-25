/* global FullCalendar:readonly */

/* global Swal:readonly */

jQuery(document).ready(function ($) {


  window.events = []

  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'it',
    firstDay: 1,
    events: window.baseurl + '/wp-json/agrispesa/v1/user-blocked-weeks?userId=' + window.userId,
    dateClick: function (info) {
      let date = info.date

      let curr = date; // get current date
      let first = curr.getDate() - curr.getDay() + 1; // First day is the day of the month - the day of the week
      let last = first + 7; // last day is the first day + 6

      let firstday = new Date(curr.setDate(first));
      let lastday = new Date(curr.setDate(last));

      var year = new Date(curr.getFullYear(), 0, 1);
      var days = Math.floor((curr - year) / (24 * 60 * 60 * 1000));
      let week = Math.ceil((curr.getDay() + 1 + days) / 7);

      calendar.addEvent({
        title: 'Questa settimana non ricevi la Facciamo Noi',
        start: firstday,
        end: lastday,
        allDay: true,
        classNames: ['temp-event']
      });

      window.events.push({
        week: week,
        start: firstday,
        end: lastday,
      })

      // change the day's background color just for fun
      //info.dayEl.style.backgroundColor = 'red';
    }
  });
  calendar.render();


  $(".confirm-button").click(function () {

    Swal.fire({
      title: 'Non vuoi ricevere la box?',
      text: "Se confermi la tua scelta non riceverai la box per la settimana che hai selezionato.",
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
            day: info.dateStr
          }, function (data) {
            calendar.refetchEvents()
          });
      }
    })
  })

})
