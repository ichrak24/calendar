import { startStimulusApp } from "@symfony/stimulus-bundle";
// Importer les variables
import { saveReservationUrl, csrfToken } from "./app";
const app = startStimulusApp();
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
