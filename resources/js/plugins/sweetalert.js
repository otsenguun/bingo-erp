import Vue from 'vue'

import Swal from 'sweetalert2'

// Swal is large popup which will be shown in the center of the screen.
window.Swal = Swal
const toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000
})

// toast is a small popup which will be shown in the top right corner of the screen.
window.toast = toast

// Vue Fire custom event to reload data
window.Fire = new Vue()
