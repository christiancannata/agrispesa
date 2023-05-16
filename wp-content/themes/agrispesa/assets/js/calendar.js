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
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
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
