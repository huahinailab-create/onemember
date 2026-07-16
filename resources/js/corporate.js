// WEBSITE-002A polish — slim JS entry for the public marketing site.
// Corporate pages use only Bootstrap behaviours (navbar collapse, dropdowns,
// FAQ accordions); they have no Alpine components and no image-upload UI, so
// loading the merchant app bundle (Alpine + Cropper.js chunk preload) was
// ~90KB of dead JavaScript on every marketing page view.
import 'bootstrap';
