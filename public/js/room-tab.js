
document.addEventListener('DOMContentLoaded', function () {
  fetch('/check-typhoon-status')
    .then(res => res.json())
    .then(data => {
      if (data.status === 'warning') {
        Swal.fire({
          icon: 'warning',
          title: '⚠️ Typhoon Alert!',
          html: `
            <b>${data.event}</b><br>
            <small>Issued by ${data.sender}</small><br><br>
            <p>${data.description}</p>
            <p><b>From:</b> ${data.start}<br><b>Until:</b> ${data.end}</p>
          `,
          confirmButtonText: 'Acknowledge'
        });
      }
    })
    .catch(() => {
      console.error("Unable to check typhoon alert.");
    });
});

