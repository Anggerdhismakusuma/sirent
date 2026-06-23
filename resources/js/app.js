import './bootstrap';
<<<<<<< HEAD

import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap/dist/css/bootstrap.min.css';

import '../scss/app.scss'
=======
import './auth-modal';
import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import Swal from 'sweetalert2';
import * as bootstrap from 'bootstrap';

window.Alpine = Alpine;
window.flatpickr = flatpickr;
window.bootstrap = bootstrap;
window.Swal = Swal;

Alpine.start();
>>>>>>> origin/feat-peminjam
