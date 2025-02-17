import Vue from 'vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

// import { } from '@fortawesome/free-regular-svg-icons'
import {
  faAddressCard,
  faAsterisk,
  faBriefcase,
  faCog,
  faEnvelope,
  faHourglassStart,
  faLock,
  faMask,
  faMoneyBill,
  faNewspaper,
  faQuestion,
  faSignOutAlt,
  faUser,
  faBolt,
  faSpaceShuttle,
  faEye,
  faStar, faCode
} from '@fortawesome/free-solid-svg-icons'

import { faGithub } from '@fortawesome/free-brands-svg-icons'

library.add(
  faUser,
  faLock,
  faSignOutAlt,
  faCog,
  faGithub,
  faMask,
  faAddressCard,
  faQuestion,
  faBriefcase,
  faAsterisk,
  faHourglassStart,
  faMoneyBill,
  faNewspaper,
  faEnvelope,
  faBolt,
  faSpaceShuttle,
  faEye,
  faStar,
  faCode
)

Vue.component('Fa', FontAwesomeIcon)
