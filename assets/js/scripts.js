// 3rd party packages from NPM



// Our modules/ classes
import BackTop from './modules/BackTop';
import Pagination from './modules/Pagination';

import FaqAccordion from './modules/FaqAccordion';
import ScrollSpy from './modules/ScrollSpy';
import MobileNav from './modules/MobileNav';
import ContactHighlight from './modules/ContactHighlight';
import Dropdown from './modules/Dropdown';
import initPrintSection from './modules/PrintSection';
import initSidebarGlass from './modules/SidebarGlass';



// Instantiate a new object using our modules/classes
new BackTop({ btnSel: '.back-top', headerSel: '.header' });
new Pagination(); // uses your existing selectors
new FaqAccordion();
// If you set --sticky-offset via CSS/JS earlier, the module will use it automatically.
// Otherwise, pass a number (in px) to override, e.g., { offset: 120 }.
new ScrollSpy({ navSel: '.side-nav' });
new MobileNav();
new ContactHighlight({ wrapSel: '.contact' });
// If you don’t set --sticky-offset elsewhere, you can pass offset: 140 here.
new Dropdown(); // wires up any .nav__li that contains a .nav-dropdown
initPrintSection();
initSidebarGlass();
