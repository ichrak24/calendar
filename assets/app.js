import "./bootstrap.js";
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import "./styles/app.css";

console.log("This log comes from assets/app.js - welcome to AssetMapper! 🎉");
// Export variables
// Export variables from Twig
export const saveReservationUrl = '{{ path('save_reservation')|e('js') }}';
export const csrfToken = '{{ csrf_token('save_reservation') }}';