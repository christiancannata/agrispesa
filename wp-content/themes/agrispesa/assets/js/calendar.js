/* global FullCalendar:readonly */
/* global Swal:readonly */

jQuery(document).ready(function ($) {


  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'it',
    firstDay: 1,
    events: window.baseurl + '/wp-json/agrispesa/v1/user-blocked-weeks?userId=' + window.userId,
    dateClick: function (info) {
      let date = info.date

      if (date.getDay() == 4) {
        Swal.fire({
          title: 'Non vuoi ricevere la box?',
          text: "Se confermi la tua scelta non riceverai la box per la settimana che hai selezionato.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3c21ff',
          cancelButtonColor: '#f8f5f1',
          confirmButtonText: 'Si, non voglio ricevere la box',
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
      }
      // change the day's background color just for fun
      //info.dayEl.style.backgroundColor = 'red';
    }
  });
  calendar.render();


})
