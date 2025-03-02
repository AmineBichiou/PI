import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import timeGridPlugin from "@fullcalendar/timegrid"; // Si tu utilises timeGrid

document.addEventListener("DOMContentLoaded", function () {
  var calendarEl = document.getElementById("calendar");

  if (!calendarEl) {
    console.error("L'élément avec l'ID 'calendar' n'existe pas.");
    return;
  }

  var calendar = new Calendar(calendarEl, {
    plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin],
    initialView: "dayGridMonth",
    locale: "fr",
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay",
    },
    events: function (fetchInfo, successCallback, failureCallback) {
      fetch("/api/evenements")
        .then((response) => response.json())
        .then((events) => {
          console.log(events); // Vérifie si les événements sont bien reçus
          successCallback(events);
        })
        .catch((error) => {
          console.error("Erreur lors du chargement des événements :", error);
          failureCallback(error);
        });
    },
    eventClick: function (info) {
      alert(
        "Événement : " +
          info.event.title +
          "\nDébut : " +
          info.event.start.toLocaleString()
      );
    },
    selectable: true,
    selectMirror: true,
    select: function (info) {
      let title = prompt("Entrez le titre de l'événement :");
      if (title) {
        calendar.addEvent({
          title: title,
          start: info.start,
          end: info.end,
          allDay: info.allDay,
        });
      }
    },
    editable: true,
    eventDrop: function (info) {
      alert("Événement déplacé à : " + info.event.start.toLocaleString());
    },
    eventColor: "#378006", // Couleur par défaut pour tous les événements
    eventTextColor: "#ffffff", // Couleur du texte des événements
  });

  calendar.render();
});
